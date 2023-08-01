<?php

/**
 * Search and display product subjects
 *
 * @author Ariel Tabag <atabag@cambridge.org>
 * March 25, 2015
 */

Loader::library('hub-sdk/autoload');
// SB-239 added by machua 20190628
Loader::model('hub_activation_list', 'go_contents');

use HubEntitlement\Repositories\ProductRepository;
use HubEntitlement\Models\Entitlement;
use HubEntitlement\Models\Product;

class GoTitlesController extends Controller
{
    const PAGE_BROWSE = 'Browse';
    const PAGE_RESOURCES = ' Resources';
    const ACTION_VIEW_TITLE = 'View Title';
    const ACTION_DOWNLOAD_FILE = 'Download File';
    const MESSAGE_ILLEGAL_DOWNLOAD = 'Failed download. Possible illegal download.';
    const TEACHER = 'TEACHER';
    const STUDENT = 'STUDENT';
    // ANZGO-3691 added by jbernardez 20180418
    const TITLE_PAGE = 'Title Page';
    const TITLE_DESC = 'No title by prettyUrl';


    protected $pkgHandle = 'go_product';
    private $titleHelper;

    public function __construct()
    {
        $this->titleHelper = Loader::helper('titles', $this->pkgHandle);
    }

    // ANZGO-3665 Modified by Shane Camus
    public function view($prettyUrl, $tab = false)
    {
        Loader::library('HotMaths/api');

        $html = Loader::helper('html');
        $breadcrumbHelper = Loader::helper('breadcrumb', $this->pkgHandle);

        $u = new User();
        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle("go_theme"));
        // ANZG0-3944 modified by mtanada 20181207
        $this->addHeaderItem('<link rel="stylesheet" type="text/css" href="' . (string)$html->css(
                'style.css',
                $this->pkgHandle
            )->href . '?v=1.1"></link>');

        $titleDetails = CupContentTitle::fetchDetailByPrettyUrl($prettyUrl);

        // SB-239 added and modified by machua 20190628 to properly get the correct subscription
        if (!is_null($u->getUserID())) {
            $activationList = new HubActivationList();
            $activationList->fetchMyResourcesList($u->getUserID());
            $userSubscription = $activationList->getSubscriptionByTitleID($titleDetails['id']);
            $this->titleHelper->setUserTitleSubscription($userSubscription);
        }

        $titleContents = CupContentTitle::getActiveContentsById($titleDetails['id']);

        // ANZGO-3691 added by jbernardez 20180417
        // check if there if an isbn13 that was retrieved from fetchDetailByPrettyUrl() method
        $noISBN = false;
        if (!$titleDetails['isbn13']) {
            $noISBN = true;
            $this->set('noISBN', $noISBN);

            CupGoLogs::trackUser(
                static::PAGE_BROWSE,
                static::ACTION_VIEW_TITLE,
                static::TITLE_PAGE,
                $u->getUserID(),
                static::TITLE_DESC
            );

            header('Location: ' . GO_BASE_URL . '/subjects/');
            exit;
        } else {
            $eduMarPrettyUrl = CupContentTitle::getEduPrettyUrlByISBN($titleDetails['isbn13']);
            $title = CupContentTitle::fetchByPrettyUrl($prettyUrl);
            $series = CupContentSeries::fetchByName($title->series);
            $subjects = CupContentTitle::getTitleSubjects($title->id);

            $subjectPrettyUrl = $subjects ? $subjects[0]['prettyUrl'] : 'Unknown';
            $seriesPrettyUrl = $series ? $series->prettyUrl : null;

            if (!$seriesPrettyUrl) {
                header('Location: ' . GO_BASE_URL . '/subjects/');
                exit;
            }

            header('Location: ' . GO_BASE_URL . '/subjects/' . $subjectPrettyUrl . '/' . $seriesPrettyUrl . '/' . $title->prettyUrl);
            exit;

            /* ANZGO-3687 added by Maryjes Tanada 04/04/2018
            * Adding of HM access for admin and cupstaff
            */
            $goUserType = array_values($u->uGroups)[1];
            if ($goUserType === 'Administrators' || $goUserType === 'CUP Staff') {
                $adminHMLink = $this->addHmAccessAdminCupStaff($title->id, $u, $titleDetails['isbn13']);
            }
            // Check if HM product for Admin link is set, otherwise render the usual format
            if (isset($adminHMLink)) {
                $contents = $this->titleHelper->formatProduct($titleContents, $tab, $adminHMLink);
            } else {
                $contents = $this->titleHelper->formatProduct($titleContents, $tab);
            }
            $breadcrumb = $breadcrumbHelper->buildFromTitle($title);

            $this->set('contents', $contents['html']);
            $this->set('user_id', $u->getUserID());
            $this->set('buy_now_link', 'https://cambridge.edu.au/education/titles/' . $eduMarPrettyUrl);
            $this->set('hasSubscription', $contents['hasSubscription']);
            $this->set('title_details', $titleDetails);
            $this->set('triggerActivate', $_SESSION['redirectError'] ? true : false);
            $this->set('breadcrumb', $breadcrumb);
            // ANZGO-3691 added by jbernardez 20180417
            $this->set('noISBN', $noISBN);

            CupGoLogs::trackUser(
                static::PAGE_BROWSE,
                static::ACTION_VIEW_TITLE,
                $titleDetails['name'],
                $u->getUserID(),
                $titleDetails['id']
            );
        }
    }

    private function addHmAccessAdminCupStaff($titleId, $u, $isbn13)
    {
        // SB-233 modified by machua 20190701 to get data from CupGoTabHmIds table
        $tabIds = CupContentTitle::getStudentHMTabsByTitleId($titleId);
        $tabIds = array_column($tabIds, 'ID');

        $tabHmID = false;
        foreach ($tabIds as $tabId) {
                $tabHmIdDetails = CupGoTabHmIds::getFormattedDetailsByTabId($tabId);
                if (!$tabHmIdDetails) {
                    continue;
                } else {
                    foreach ($tabHmIdDetails as $entitlementId => $hmId) {
                        if ((int)$hmId !== 0
                            && $this->entitlementHasISBN13($entitlementId, $isbn13)) {
                            $tabHmID = $hmId;
                            break;
                        }
                    }
                }

                if ($tabHmID && (int)$tabHmID > 0) {
                    break;
                }
        }

        if (!$tabHmID) {
            return null;
        }

        $studentLink = $this->hmApi($u, $tabHmID, null, static::STUDENT);

        // Get Teacher Product using HmID
        $params = array(
            'userId' => $u->uID,
            'hmProductId' => $tabHmID,
            'response' => 'STRING',
            'saId' => null
        );
        $api = new HotMathsApi($params);
        $hmIdTeacher = $api->getHmTeacherProduct();

        // Check HM Teacher product if exists
        if ($hmIdTeacher->success === false) {
            $teacherLink = $hmIdTeacher->message;
        } else {
            $teacherLink = $this->hmApi($u, $hmIdTeacher->productId, null, static::TEACHER);
        }

        return array($studentLink, $teacherLink);
    }

    /* SB-233
     * Added by machua 20190704
     * Check if the subscription is correct using the ISBN13 value
     * @param $entitlementId
     * @param $isbn13
     * @return boolean
     */
    private function entitlementHasISBN13($entitlementId, $isbn13)
    {
        $entitlement = Entitlement::find($entitlementId);

        if (!$entitlement) return false;

        $product = $entitlement->product()->fetch();
        if (!$product) return false;

        $cmsName = strtolower($product->CMS_Name);
        if (strpos($cmsName, 'trial') !== false || strpos($cmsName, 'demo') !== false) {
            return false;
        }

        if ($product->ISBN_13 !== $isbn13) {
            return false;
        }

        return true;
    }

    /* ANZGO-3687 added by Maryjes Tanada 04/06/2018
    * Process of adding HM products and users
    */
    public function hmApi($u, $hmId, $saId, $userType = null)
    {
        $titleHelper = new TitlesHelper();
        $params = array('userId' => $u->uID, 'hmProductId' => $hmId, 'response' => 'STRING', 'saId' => $saId);
        $api = new HotMathsApi($params);
        $hmProduct = $api->getHmProduct();
        $hmUser = $api->getHmUserByType($userType);

        if (isset($hmId)) {
            if ($hmUser->success === false || $hmProduct->subscriberType !== $hmUser->subscriberType) {
                $api->createHmUser($userType);
                $hmNewUser = $api->getHmUserByType($userType);
                if ($hmProduct->subscriberType === $hmNewUser->subscriberType) {
                    $api->addHmSubscription(false, $userType);
                }
            } elseif ($hmProduct->subscriberType === $hmUser->subscriberType) {
                $api->addHmSubscription(false, $userType);
            }
            return $titleHelper->checkBuildHMAccess($hmId, false, $userType);
        } else {
            return null;
        }
    }

    // @param CupGoContentDetail ID
    public function downloadPdf($id)
    {
        $html = Loader::helper('html');
        $this->addHeaderItem($html->javascript('download.js', $this->pkgHandle));

        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle("go_plain_theme"));

        $contentDetails = CupGoContentDetail::fetchByID($id);

        $this->set('id', $id);
        $this->set('file_name', $contentDetails->file_name);
        $this->set('file_size', $contentDetails->file_size);

        $this->render("/go/download_pdf");
    }

    public function downloadFile($id)
    {
        $u = new User();

        // Rechecking user log in and subscription in case this method has been accessed directly.

        /* ANZGO-3439 modified by Maryjes Tanada 04/03/2018
         * Check free access via CupGoTabs' ContentAccess === Free
         * modify var name: Visibility(tab/content) === public is considered as Free as well
         */
        $isContentAccessFree = CupGoTabs::isContentAccessFree($id);
        $isTabBlockAndContentPublic = CupGoContentDetail::isTabBlockAndContentPublic($id);

        // Check login only access here.
        $tabAccess = CupGoTabs::isLoginOnlyAccess($id);
        $permFlag = false;
        if (strcmp(strtolower($tabAccess['ContentAccess']), "login only") === 0 || $isContentAccessFree === true) {
            $permFlag = true;
        }

        // For logged out users
        if (!$u->isLoggedIn() && !$isTabBlockAndContentPublic && !$isContentAccessFree) {
            CupGoLogs::trackUser(
                static::PAGE_RESOURCES,
                static::ACTION_DOWNLOAD_FILE,
                static::MESSAGE_ILLEGAL_DOWNLOAD
            );
            $this->redirect('/go');
        }

        // check for user types here
        $groups = $u->uGroups;
        $isAdmin = (in_array("Administrators", $groups) || in_array("CUP Staff", $groups));
        if (!$isAdmin) {
            Loader::library('Activation/user_activation');
            $userActivationLibrary = new UserActivation();
            $userActivationLibrary->setUserId($u->getUserID());
            $tabIdsSubscribedTo = $userActivationLibrary->getSubscribedTabIds();
            $userSubs = CupGoContentDetail::getActiveSubscriptionContentsByTabIds(
                $tabIdsSubscribedTo
            );

            // User has no subscriptions.
            if (empty($userSubs) && !$isTabBlockAndContentPublic && !$permFlag) {
                CupGoLogs::trackUser(
                    static::PAGE_RESOURCES,
                    static::ACTION_DOWNLOAD_FILE,
                    static::MESSAGE_ILLEGAL_DOWNLOAD
                );
                $this->redirect('/go');
            }

            if (!$isTabBlockAndContentPublic && !in_array($id, $userSubs) && !$permFlag) {
                CupGoLogs::trackUser(
                    static::PAGE_RESOURCES,
                    static::ACTION_DOWNLOAD_FILE,
                    static::MESSAGE_ILLEGAL_DOWNLOAD
                );
                $this->redirect('/go');
            }
        }

        // Commence download here
        $contentDetails = CupGoContentDetail::fetchByID($id);

        $fullPath = DIR_BASE . '/';
        $fileName = $contentDetails->file_name;

        $fileExtension = strtoupper(
            substr(
                $fileName,
                strrpos($fileName, '.') + 1
            )
        );

        $filePath = $this->titleHelper->convertSlashes($contentDetails->file_path);

        // Check for initial slahes
        // and remove them if there are any
        if (strpos($filePath, '/') === 0) {
            $filePath = ltrim($filePath, '/');
        }

        // Some file paths have no appended file names.
        // We do the checking here.
        if (strpos($filePath, $fileName) === false) {
            $filePath .= $fileName;
        }

        // At this point, the relative path is now clean
        // Complete the full path of the file needed.
        $fullPath .= $filePath;

        if (!file_exists($fullPath)) {
            echo 'File not found';
            exit;
        }

        //https://jira.cambridge.org/browse/ANZUAT-111
        $ctype = $this->getContentType($fileExtension);

        // ANZGO-2904
        $stripFileName = str_replace(' ', '_', $contentDetails->file_name);

        // Set the headers, echo the file content.
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$stripFileName");
        header("Content-Type: $ctype");
        header("Content-Transfer-Encoding: binary");
        ob_clean();
        flush();
        readfile($filePath);

        /* ANZGO-3529 Added by Jeszy Tanada, 10/20/2017
         * To log users when downloading resources
         */
        $contentID = $contentDetails->exisiting_result['ContentID'];
        $tabName = CupGoTabs::getTabName($id);
        $smallTabName = strtolower($tabName['TabName']);

        // Get the last 4 string in file name (ex. .zip)
        $fileExtension = substr($contentDetails->file_name, -4);

        if ($smallTabName === 'pdf textbook') {
            CupGoLogs::trackUser(
                'Resources/Title',
                $tabName['TabName'],
                $contentID
            );
        }
        if ($smallTabName === 'word activities') {
            CupGoLogs::trackUser(
                'Resources/Title',
                $tabName['TabName'],
                $contentID
            );
        }
        if ($smallTabName === 'teacher resource package' && $fileExtension === '.zip') {
            CupGoLogs::trackUser(
                'Resources/Title',
                $tabName['TabName'],
                $contentID
            );
        }

        CupGoLogs::trackUser(
            static::PAGE_RESOURCES,
            static::ACTION_DOWNLOAD_FILE,
            $contentDetails->file_name
        );
        exit;

    }

    public function downloadFile2($id)
    {
        $contentDetails = CupGoContentDetail::fetchByID($id);
        $fileExtension = strtoupper(
            substr(
                $contentDetails->file_name,
                strrpos($contentDetails->file_name, '.') + 1
            )
        );
        $fullPathLocal = LOCAL_ASSETS_FOLDER .
            str_replace(
                '\\',
                '/',
                $contentDetails->file_path . $contentDetails->file_name
            );
        $fullPath = file_exists($fullPathLocal) ? $fullPathLocal : ASSETS_FOLDER .
            str_replace(
                '\\',
                '/',
                $contentDetails->file_path . $contentDetails->file_name
            );

        //https://jira.cambridge.org/browse/ANZUAT-111
        $ctype = $this->getContentType($fileExtension);

        // Set the headers, echo the file content.
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$contentDetails->file_name");
        header("Content-Type: $ctype");
        header("Content-Transfer-Encoding: binary");
        ob_clean();
        flush();
        readfile($fullPath);

        CupGoLogs::trackUser(
            static::PAGE_RESOURCES,
            static::ACTION_DOWNLOAD_FILE,
            $contentDetails->file_name
        );
        exit;

    }

    //https://jira.cambridge.org/browse/ANZUAT-111
    private function getContentType($fileExtension)
    {
        switch ($fileExtension) {
            case "pdf":
                $cType = "application/pdf";
                break;
            case "exe":
                $cType = "application/octet-stream";
                break;
            case "zip":
                $cType = "application/zip";
                break;
            case "docx":
            case "doc":
                $cType = "application/force-download";
                break;
            case "csv":
            case "xls":
            case "xlsx":
                $cType = "application/vnd.ms-excel";
                break;
            case "ppt":
                $cType = "application/vnd.ms-powerpoint";
                break;
            case "gif":
                $cType = "image/gif";
                break;
            case "png":
                $cType = "image/png";
                break;
            case "jpeg":
            case "jpg":
                $cType = "image/jpg";
                break;
            case "tif":
            case "tiff":
                $cType = "image/tiff";
                break;
            case "psd":
                $cType = "image/psd";
                break;
            case "bmp":
                $cType = "image/bmp";
                break;
            case "ico":
                $cType = "image/vnd.microsoft.icon";
                break;
            default:
                $cType = "application/force-download";
        }

        return $cType;
    }

}
