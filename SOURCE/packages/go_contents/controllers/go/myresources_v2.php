<?php

/**
 * Description of resources
 * @author atabag <atabag@cambridge.org>
 * @author paulbalila
 */
Loader::model('cup_go_user_subscription', 'go_contents');
Loader::model('cup_go_user_subscription_list', 'go_contents');
Loader::model('cup_go_external_user', 'go_contents');
Loader::model('notifications', 'go_contents');
Loader::model('hub_activation_list', 'go_contents');
Loader::model('format/model', 'cup_content');
Loader::model('gousermodel', 'go_contents');

// HotMaths API
Loader::library('HotMaths/api');
Loader::library('HotMaths/collections_v2/user');
Loader::library('Activation/hub_activation_v2');
Loader::library('hub-sdk/autoload');

Loader::library('FileLog/FileLogger');

use HubEntitlement\Models\Activation;

class GoMyResourcesV2Controller extends Controller
{
    const TITLES            = 'titles';
    const COMPLETED         = 'completed';
    const ACCESS_CODE       = 'accesscode';
    // ANZGO-3854 modified by jbernardez 20180913
    const PRINT_ACCESS_CODE = 'printAccessCode';
    // ANZGO-3947 added by jbernardez/mtanada 20181210
    const HOTMATHS          = 'HOTMATHS';
    const TAB_ID            = 'TabID';
    const MY_RESOURCES_HUB  = 'myresources_hub';
    const MY_RESOURCES      = 'myresources';
    const ERROR             = 'error';
    // SB-7 added by jbernardez 20190208
    const DEEPLINK_HASH     = 'hash';
    const BRAND_CODE        = 'brandCode';

    private $userModel;
    protected $pkgHandle    = 'go_contents';
    protected $offset       = 0;
    protected $pagination   = 8;

    // SB-251 added by jbernardez 20190712
    const TEACHER           = 'Teacher';
    const STUDENT           = 'Student';
    const TEACHER_SRC       = 'https://www.surveymonkey.co.uk/r/LPHH5VS';
    const STUDENT_SRC       = 'https://www.surveymonkey.co.uk/r/QB3N3CV';
    const SURVEY_MONKEY     = 'uSurveyMonkey';
    const CONTACT_DETAILS   = 'uContactDetails';

    // BTS task
    const CLASS_CODE         = 'classCode';
    const HM_ID             = 'hmID';
    const HM_IDS            = 'hmIDs';
    const FUNCTION_TYPE     = 'function';
    const PRODUCT_IDS       = 'productIds';
    const TIMESTAMP         = 'timestamp';
    const USER_ID           = 'userID';
    const USER_IDS          = 'userIDs';

    public function on_start()
    {
        $this->userModel = new GoUserModel();
        $u = new User();
        // SB-292 added by machua 20190806 to redirect user to login
        if (!$u->isLoggedIn()) {
            $this->redirect('/go/login');
        }

        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle("go_theme"));
        $html = Loader::helper('html');
        $this->addHeaderItem(Loader::helper('html')->css('style.css', $this->pkgHandle));
        $this->addFooterItem(Loader::helper('html')->javascript('bootbox.js', $this->pkgHandle));
        // ANZGO-3325 modified by jbernardez 20180222 ANZGO-3943 modified by mtanada 20181210
        $this->addFooterItem(
            '<script type="text/javascript" src="' .
            (string)$html->javascript('myresources_v2.js', $this->pkgHandle)->href . '?v=17.0"></script>'
        );
        // ANZGO-3227
        // reload product.js to so modal window can be loaded
        $this->addFooterItem(Loader::helper('html')->javascript('product.js',
            'go_product'));

        // ANZGO-2851
        // check for accesscode session
        // if there is an accesscode, proceed to activate code
        if ($_SESSION[static::ACCESS_CODE] != '') {
            $_SESSION['error_message'] = '';
            $accesscode = $_SESSION[static::ACCESS_CODE];
            // ANZGO-3854 modified by jbernardez 20180913
            $printAccessCode = $_SESSION[static::PRINT_ACCESS_CODE];

            // ANZGO-2801 SB-64 added by mtanada 20190211 print code session
            if (!$u->isLoggedIn()) {
                // ANZGO-2858
                $_SESSION[static::ACCESS_CODE] = $accesscode;
                $_SESSION[static::PRINT_ACCESS_CODE] = $printAccessCode;
            }
            // ANZGO-2800
            $userID = $u->getUserID();

            // ANZGO-3630 Added by Maryjes Tanada 02/12/2018
            // If HM access code, check account & product (user type)
            // If not same user type, suspend activation process & redirect users to activation modal
            // ANZGO-3854 modified by jbernardez 20180913
            $data = [
                'userID' => $userID,
                'accessCode' => $accesscode,
                'response' => 'STRING',
                'printAccessCode' => $printAccessCode
            ];

            // HUB-147 Modified by John Renzo S. Sunico, 08/28/2018

            $activationLibrary = new HubActivation($data);

            $result = $activationLibrary->activateProduct();
            $message = $activationLibrary->checkHmUserProductType();

            $processCodeResult = $result['message'];
            if (!$result['success']) {
                if ($message !== 'user-type-success') {
                    $_SESSION['userTypeMessage'] = $message;
                    $_SESSION[static::ACCESS_CODE] = '';
                    $_SESSION['hmID'] = '';
                    $this->redirect('go/myresources_v2/');
                } else {
                    $_SESSION['error_message'] = $processCodeResult;
                    $this->redirect('go/myresources_v2/');
                }
            }
            // if not redirect, destroy accesscode session
            $_SESSION[static::ACCESS_CODE] = '';
        }

        // SB-7 added by jbernardez 20190208
        $this->checkDeeplink();
    }

    public function view($arrange = null)
    {
        $u = new User();
        if ($u->isRegistered()) {
            // SB-46 by mtanada 20190131 Removed commented out codes, subscriptions are build on loadMoreResources
            // SB-46 by atabag/mabrigos 20190130 set the number of user's visit
            $_SESSION['myResourcesVisitCount'] += 1;
            $this->set('arrange', $arrange ? $arrange : 'Arrange');
            $this->set('user_id', $u->getUserID());

            //user name display
            $ui = UserInfo::getByID($u->getUserID());
            $displayName = $ui->getAttribute('uFirstName') . " " . $ui->getAttribute('uLastName');
           
            CupGoLogs::trackUser("Account", "View my resources");

            // ANZUAT-128
            $displayHideHelpSession = $_SESSION['hideHelpSession'];
        } else {
            $_SESSION['redirectError'] = "You must login to view your resources.";
            $this->set(
                'triggerClick',
                '<script>$( document ).ready(function() { $( "#header-login" ).trigger( "click" ); });</script>'
            );
        }
        $this->set('displayName', $displayName);
        $_SESSION['visitedMyResources'] = "Visited My resources";
        // ANZUAT-128
        $this->set('displayHideHelpSession', $displayHideHelpSession);

        // SB-251 added by jbernardez 20190712
        $this->set('source', $this->surveyMonkeySource());
        $this->set('isSurveyMonkey', $this->surveyMonkeyCheck());
        $this->set('myResourcesLogCount', CupGoLogs::myResoucesLogCount($u->getUserID()));
    }

    public function checkHMPendings() {
        $u = new User();
        $uID = $u->getUserID();

        $AllUserHMPendings = $this->userModel->fetchUserHMPendings($uID);
        // $isHMUserRecentlyCreated = false;
        $updatedExternalId = null;

        foreach($AllUserHMPendings as $userHMPendings) {
            // var_dump($userHMPendings);
            $HMPIDs = $userHMPendings['HMPIDs'];
            $classCodes = $userHMPendings['classCodes'];
            $externalID = $userHMPendings['externalID'];
            $firstName = $userHMPendings['firstName'];
            $lastName = $userHMPendings['lastName'];
            $subscriberType = $userHMPendings['subscriberType'];
            $limitedProduct = intval($userHMPendings['limitedProduct']);

            $HMUser = new HMUser();
            $isUserSuccess = true;
            $isClassKeySuccess = true;
            $isProductSuccess = true;

            $errorMsg = '';
            
            // if (is_null($externalID) && !$isHMUserRecentlyCreated) {
            if (is_null($externalID)) {
                 $userInfo = UserInfo::getByID($uID);
                 $userParams = array(
                    'email' => $userInfo->uEmail,
                    'username' => $userInfo->uEmail,
                    'userID' => $userInfo->uID,
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'countryCode' => '',
                    'subscriberType' => $subscriberType
                );

                // var_dump('***** create! userParams'); var_dump($userParams);

                $response = $HMUser->create($userParams);
                $externalID = $response->userId;
                // var_dump($response);

                // checker $response = null;
                if (is_null($response) || $response->success === false || !empty($response->errors)) {
                    if ($response->message == 'Username is already used') {
                        $fetchedHMUID = $this->userModel->getHMUIDbyGOUID($userInfo->uID);
                        if (empty($fetchedHMUID)) {
                            $getUserByGoID = $HMUser->getUserByGoID($userInfo->uID);
                            $this->userModel->updateHotMaths($getUserByGoID);
                            $externalID = $getUserByGoID->userId;
                        } else {
                            $externalID = $fetchedHMUID['externalID'];
                        }
                    } else {
                        $isUserSuccess = false;
                        $errorMsg .= "GO is experiencing problem to process your account in Hotmaths.\n";
                        FileLogger::log(
                            array(
                                static::TIMESTAMP => date('r'),
                                static::USER_ID => $userInfo->uID,
                                'info' => 'HM API Function (MyResources on load - createUser): ' . debug_backtrace()[1][static::FUNCTION_TYPE],
                                'meta' => $response
                            )
                        );
                    }
                } else {
                    $this->userModel->updateHotMaths($response);
                }

                $updatedExternalId = $response->userId;
                // $isHMUserRecentlyCreated = true;
            } 
            /*
            else {
                $externalID = $updatedExternalId;
            }*/

            

            if (!is_null($classCodes)) {
                $classCodesArray = explode(',', $classCodes);
                $arrHMUID = array($externalID);
                $classKeyError = array();

                foreach($classCodesArray as $classCode) {
                    // var_dump('********* Process Class Key');
                    // var_dump('classCode'); var_dump($classCode);
                    // var_dump('arrHMUID'); var_dump($arrHMUID);
                    $classResponse = $HMUser->addUsersToClass($classCode, $arrHMUID);
                    // var_dump($classResponse);

                    if (!$classResponse->success) {
                        $errorMsg .= "GO is experiencing problem to add you in these class (Class Codes: {$classCode}).\n";
                        array_push($classKeyError, $classCode);
                        FileLogger::log(
                            array(
                                static::TIMESTAMP => date('r'),
                                static::CLASS_CODE => $classCode,
                                static::USER_ID => $uID,
                                static::HM_ID => $arrHMUID,
                                'info' => 'HM API Function (MyResources on load - addUsersToClass): ' . debug_backtrace()[1][static::FUNCTION_TYPE],
                                'meta' => $classResponse
                            )
                        );
                    }
                }

                if (count($classKeyError) > 0) {
                    $isClassKeySuccess = false;
                }
            }

            if (!is_null($HMPIDs)) {
                // var_dump('********* Process User Products');
                $HMPIdsArray = explode(',', $HMPIDs);
                $HMPIdsIntArray = array_map('intval', $HMPIdsArray);
                $additionals = null;

                if (!is_null($limitedProduct)) {
                    $additionals['limitedProduct'] = $limitedProduct;
                }

                $productResponse = $HMUser->addProductToUser(intval($externalID), $HMPIdsIntArray, $additionals);
                // var_dump('add product hmpids'); var_dump($productResponse);

                if (is_null($productResponse || $productResponse->success === false || !empty($productResponse->errors))) {
                    $isProductSuccess = false;
                    $errorMsg .= "GO is experiencing problem to add these products (Product Ids: {$HMPIDs}) to your account.\n";
                    FileLogger::log(
                        array(
                            static::TIMESTAMP => date('r'),
                            static::PRODUCT_IDS => $HMPIdsIntArray,
                            static::USER_IDS => $uID,
                            static::HM_IDS => $HMPIdsIntArray,
                            'info' => 'HM API Function (CRON Pending HM Provisioning - addProductToUser): ' . debug_backtrace()[1][static::FUNCTION_TYPE],
                            'meta' => $productResponse
                        )
                    );
                }
            }

            // var_dump($isUserSuccess);
            // var_dump($isClassKeySuccess);
            // var_dump($isProductSuccess);

            if ($isUserSuccess && $isClassKeySuccess && $isProductSuccess) {
                // var_dump('update status!'); var_dump($uID); 
                $this->userModel->updateUserHMPendings($uID);
            } else {
                echo $errorMsg;
            }
        }
        // echo 'end';
        exit;
    }

    // Modified by Carl Lewi Godoy
    public function deleteResource($usID)
    {
        // HUB-150 Modified by John Renzo S. Sunico, 08/28/2018
        $subscription = Activation::find($usID);
        $subscription->Archive = 'Y';
        $subscription->ArchivedDate = date('Y-m-d H:i:s');
        $result = $subscription->save();

        $message = $result ? 'Delete resource' : 'Error on deleting resource ' . $usID;
        echo json_encode($result);

        CupGoLogs::trackUser("Account", $message, $usID);
        exit;
    }

    public function viewPDF($tabID)
    {
        $v = View::getInstance();
        $v->setTheme(PageTheme::getByHandle("go_plain_theme"));
        $myResourcesHelper = Loader::helper(static::MY_RESOURCES, $this->pkgHandle);
        $contents = $myResourcesHelper->formatPDF($tabID);
        $this->set('contents', $contents['html']);
        $this->set('title', $contents['tab_name']);
        $this->render('/go/viewpdf');
    }

    // ANZGO-3946 added by machua 20181210 get the necessary data of the tile and add it to modal package element
    public function fetchContents($tabID)
    {
        $myResourcesHelper = Loader::helper(static::MY_RESOURCES, $this->pkgHandle);
        $assets = $myResourcesHelper->getTileAssets($tabID);
        Loader::packageElement('modal', $this->pkgHandle, ['assets' => $assets]);
        exit;
    }

    public function fetchWeblinks($tabID)
    {
        $myResourcesHelper = Loader::helper(static::MY_RESOURCES, $this->pkgHandle);
        // SB-305 modified by machua 20190823 for getting tab text
        $idArray = explode('-', $tabID);
        $productID = $idArray[0];
        $tabID = $idArray[1];
        $assets = $myResourcesHelper->getWeblinks($productID, $tabID);
        Loader::packageElement('weblinksmodal', $this->pkgHandle, ['assets' => $assets]);
        exit;
    }

    // ANZGO-3013
    public function userTracking()
    {
        $data = $this->post();
        // ANZGO-3451 Modified by Shane Camus 7/26/2017 (To include userID on logging)
        CupGoLogs::trackUser(
            $data['pageName'],
            $data['action'],
            $data['info'],
            $data['userID']
        );
        exit;
    }

    /**
     * ANZGO-3300 Added by John Renzo Sunico, 04/21/2017
     * ANZGO-3383 Modified by John Renzo Sunico, 05/12/2017
     * HUB-60 Modified by John Renzo S. Sunico, June 28, 2018
     *
     * @param $loaded string
     */
    public function more($loaded)
    {
        header('Content-Type: application/json');
        $user = new User();
        $data = $this->post();
        $loaded = intval($loaded);
        $response = array(
            static::COMPLETED => false,
            static::TITLES => null,
            static::ERROR => array()
        );

        if (!$user->isLoggedIn()) {
            http_response_code(403);
            $response = array(static::ERROR => 'Invalid Access.');
        } else {
            $arrange = $data['sort'];

            // HUB-149 Modified by John Renzo S. Sunico, 08/28/2018
            $myResourcesHelper = Loader::helper(static::MY_RESOURCES_HUB, $this->pkgHandle);
            $activationList = new HubActivationList();
            $activationList->fetchMyResourcesList($user->getUserID());
            $activationList->sortSubscriptions($arrange);
            $subscriptions = $activationList->getPage(
                $loaded,
                $this->pagination
            );
            $titles = $myResourcesHelper->formatDisplay($subscriptions);
            $next = $activationList->getPage($loaded + $this->pagination, 1);



            if (!$next) {
                $response[static::COMPLETED] = true;
            }

            // ANZGO-3942 modified by machua 20181204 to check if there are no resources
            if ($titles === '') {
                $response[static::COMPLETED] = true;
                $response[static::TITLES] = "";
            } else {
                $response[static::TITLES] = $titles;
            }
        }

        echo json_encode($response);
        exit;
    }

    /*
     * ANZGO-3947 added by machua 20181212 for loading more resources/titles with the new UI/UX design
     */
    public function loadMoreResources($loaded)
    {
        header('Content-Type: application/json');
        $user = new User();
        $data = $this->post();
        $loaded = intval($loaded);

        $response = array(
            static::COMPLETED => false,
            static::TITLES => null,
            static::ERROR => array()
        );

        if (!$user->isLoggedIn()) {
            http_response_code(403);
            $response = array(static::ERROR => 'Invalid Access.');
        } else {
            $arrange = $data['sort'];

            // HUB-149 Modified by John Renzo S. Sunico, 08/28/2018
            $activationList = new HubActivationList();
            $activationList->fetchMyResourcesList($user->getUserID());
            $activationList->sortSubscriptions($arrange);
            $subscriptions = $activationList->getPage(
                $loaded,
                $this->pagination
            );

            // ANZGO-3947 added by jbernardez/mtanada 20181210
            $subscriptions = $this->formatSubscriptions($subscriptions);

            $next = $activationList->getPage($loaded + $this->pagination, 1);

            if (!$next) {
                $response[static::COMPLETED] = true;
            }

            // ANZGO-3942 modified by machua 20181204 to check if there are no resources
            if (!$subscriptions) {
                $response[static::COMPLETED] = true;
                $response[static::TITLES] = "";
            } else {
                ob_start();
                Loader::packageElement(
                    'resources',
                    $this->pkgHandle,
                    ['resources' => $subscriptions, 'index' => $loaded + 1]
                );
                $response[static::TITLES] = ob_get_clean();
            }
        }
        echo json_encode($response);
        exit;
    }

    // ANZGO-3897 added by jdchavez 10/18/2018
    public function toHotmaths()
    {
        $u = new User();
        $hmId = $this->get('productId');
        $group = $this->get('group');
        $isAjax = $this->get('isAjax');

        if (!$u->isRegistered() || !$hmId) {
            $this->redirect('/go');
        }

        $myResourcesHelper = Loader::helper(static::MY_RESOURCES_HUB, $this->pkgHandle);
        $redirectTo = $myResourcesHelper->getHotMathsRedirectUrl($u->getUserID(), $hmId, $group);

        if ($isAjax) {
            echo $redirectTo;
            exit;
        }

        $this->externalRedirect($redirectTo);
    }

    /**
     * ANZGO-3947 added by jbernardez/mtanada 20181210
     * This will create a new formatted subscriptions to send to frontend
     * ANZGO-3947 modified by mtanada scamus 20181212
     * ANZGO-3990 Modified by Shane Camus 01/04/19
     * @param $subscriptions
     * @return array|bool
     */
    private function formatSubscriptions($subscriptions)
    {
        Loader::helper(static::MY_RESOURCES, $this->pkgHandle);

        $educationFileURL = BASE_URL . '/files/cup_content/images/titles/';
        $educationIconURL = BASE_URL . '/files/cup_content/images/formats/';

        if (!$subscriptions) {
            return false;
        }

        $formattedSubscriptions = array();

        foreach ($subscriptions as $titleId => $tabsSubscribed) {

            $singleTab          = $tabsSubscribed[0];
            $isbn13             = $singleTab['isbn13'];
            $displayName        = $singleTab['displayName'];
            $authToken          = $singleTab['authToken'];
            $brandCodes         = $singleTab['brandCodes'];
            $externalID         = $singleTab['externalID'];
            // ANZGO-3978 modified by machua 20181221 to avoid using the cached image
            $image              = $educationFileURL . $isbn13 . '_180.jpg?=' . time();


            $tiles = array();
            foreach ($tabsSubscribed as $tabSubscriptionData) {
                $daysRemaining  = $tabSubscriptionData['DaysRemaining'];
                // Check days remaining if null and set to 0 else keep the value return boolean
                $isExpired       = is_null($daysRemaining) ? 0 : $daysRemaining;
                $isExpired       = $isExpired <= 0;
                $isDeactivated   = !is_null($tabSubscriptionData['USubDateDeactivated']);
                $tabEndDate      = $tabSubscriptionData['USubEndDate'];
                $tabID           = $tabSubscriptionData[static::TAB_ID];
                $tabInfo         = $this->getTabContentsByTabID($tabID);
                $tabName         = $tabInfo->existing_result['TabName'];
                $tabIcon         = $tabInfo->existing_result['TabIcon'];
                $goUserType      = array_values($_SESSION['uGroups'])[1];
                $source          = $tabSubscriptionData['Source'];
                // SB-174 added by machua 20190521 get order from CupGoTabs
                $titleTabOrder   = $tabInfo->existing_result['SortOrder'];

                // SB-174 added by machua 20190521 get order from CupGoTabOrders
                $cmsTabOrder        = CupGoTabOrders::getOrderByProductIdAndTabId(
                    $tabSubscriptionData['SubscriptionID'],
                    $tabSubscriptionData['TabID']
                );
                // SB-16 modified by mtanada 20190110 Call HM ID from CupGoTabHmIds table
                $hmID = CupGoTabHmIds::getHmIdByEntitlementIdAndTabId(
                    $tabSubscriptionData['SA_ID'],
                    $tabSubscriptionData['TabID']
                );
                $resourceURL     = $hmID > 0 ?
                    $this->getHotmathsResourceLink($hmID, $goUserType) :
                    $tabInfo->existing_result['ResourceURL'];
                // SB-305 added by machua 20190823 to add modal to all tabs without a resource URL
                $hasModal        = ($resourceURL === '' || is_null($resourceURL));

                // SB-2 Added by mabrigos 20190116 added enddate and limited flag
                // SB-12 Modified by mtanada 20190118 priority to display limited product
                $limited = $tabSubscriptionData['limited'];

                // Get the latest Tab from multiple Subscriptions
                if ((array_key_exists($tabID, $tiles))
                    && ($tiles[$tabID]['DaysRemaining'] > $daysRemaining)) {
                    // SB-96 added by machua 20190319 reset value of limited if there is an existing subscription with greater days remaining than the limited product provisioned
                    if ($limited === true) $limited = false;
                        continue;
                }
                // SB-12 Modified by mtanada 20190118 limited subscription end date
                // SB-249 mabrigos 20190710 - added coming soon for tiles.
                $limitedEndDate = $limited ? date('d-m-y', strtotime($tabEndDate)) : null;
                $tiles[$tabID]  = array(
                    'productID'     => $titleId,
                    'name'          => $tabName,
                    'tileIcon'      => $educationIconURL . $tabIcon,
                    'endDate'       => date('d-m-y', strtotime($tabEndDate)),
                    'isExpired'     => $isExpired,
                    'isDeactivated' => $isDeactivated,
                    'hasModal'      => $hasModal,
                    'resourceURL'   => $resourceURL,
                    'isResourceLink'=> $tabInfo->existing_result['MyResourcesLink'] === 'Y',
                    'isActive'      => $tabInfo->existing_result['Active'] === 'Y',
                    'ComingSoon'    => $tabInfo->existing_result['ComingSoon'] === 'Y',
                    'DaysRemaining' => $daysRemaining,
                    'titleOrder'    => $titleTabOrder,
                    'cmsOrder'      => $cmsTabOrder
                );

                // Added this for Hotmaths Connect, this adds a single tile with overridden values
                if ($source === 'HOTMATHS') {
                    // SB-9 added by jbernardez 20191105
                    $connDaysRemaining = floor($tabEndDate / 86400);
                    $connCreationDate = $tabSubscriptionData['USubCreationDate'];
                    $connUpdateDate = $tabSubscriptionData['UpdateDate'];

                    if ($connUpdateDate === '0000-00-00 00:00:00') {
                        $connEndDate = date('d-m-Y', strtotime($connCreationDate . ' + ' . $connDaysRemaining . ' days'));
                    } else {
                        $connEndDate = date('d-m-Y', strtotime($connUpdateDate . ' + ' . $connDaysRemaining . ' days'));
                    }

                    // SB-9 added by jbernardez 20191106
                    $isExpired = false;
                    if (strtotime($connEndDate) <= strtotime(date('d-m-Y'))) {
                        $isExpired = true;
                    }

                    // SB-9 added by jbernardez 20191105
                    $tiles[$tabID] = array(
                        'name'          => $displayName,
                        'tileIcon'      => $image,
                        'endDate'       => $connEndDate,
                        'isExpired'     => $isExpired,
                        'isDeactivated' => false,
                        'hasModal'      => false,
                        'resourceURL'   => $this->getHotmathsConnectResourceLink($externalID, $authToken, $brandCodes),
                        'deleteURL'     => $this->getHotmathsConnectDeleteLink($authToken, $brandCodes),
                        'isResourceLink'=> true,
                        'ComingSoon'    => $tabInfo->existing_result['ComingSoon'] === 'Y',
                        'isActive'      => true,
                        'DaysRemaining' => $connDaysRemaining,
                        'source'        => $source,
                        'updateDate'    => $connUpdateDate,
                        'brandCode'     => $brandCodes
                    );
                }
            }

            // SB-174 added by machua 20190520 to sort tabs for displaying
            uasort($tiles, array($this, 'sortTabsByOrder'));
    
            $formattedSubscriptions[] = array(
                'title'             => MyResourcesHelper::formatProductDisplayName($displayName),
                'limited'           => $limited,
                'limitedEndDate'    => $limitedEndDate,
                'image'             => $image,
                'tiles'             => $tiles
            );
        }

        return $formattedSubscriptions;
    }

    /**
     * ANZGO-3947 added by jbernardez/mtanada 20181210
     * Get Tab Contents by tab ID
     * @param $tabID
     * @return CupGoTabs
     */
    private function getTabContentsByTabID($tabID)
    {
        return new CupGoTabs($tabID);
    }

    /**
     * ANZGO-3947 added by jbernardez/mtanada 20181210
     * Get hotmaths generated resource link
     * @param $hmId
     * @param null $groupType
     * @return string
     */
    private function getHotmathsResourceLink($hmId, $groupType = null)
    {
        return "/go/myresources_v2/toHotmaths/?productId=$hmId&group=$groupType";
    }

    /**
     * ANZGO-3947 added by jbernardez 20190102
     * code cleanup
     * Get hotmaths connect generated resource link
     * @param $externalID
     * @param $authToken
     * @param $brandCodes
     * @return string
     */
    private function getHotmathsConnectResourceLink($externalID, $authToken, $brandCodes)
    {
        $apiPath = 'https://' . HOTMATHS_CONNECT . '/cambridgeLogin?externalId=' . $externalID;
        return $apiPath . '&access_token=' . $authToken . '&brandCode=' . $brandCodes;
    }

    // SB-9 added by jbernardez 20191106
    private function getHotmathsConnectDeleteLink($authToken, $brandCode)
    {
        return BASE_URL . '/api/removeExternalUser/' . $authToken . '/' . $brandCode;
    }

    /**
     * SB-7 added by jbernardez 20190208
     * this will check if the session variables hash and brandcode has been set
     * if it has, then it will redirect to the deeplink controller url
     * and then will destroy the session variables
     */
    private function checkDeeplink()
    {
        if ($_SESSION[static::DEEPLINK_HASH] && $_SESSION[static::BRAND_CODE]) {
            $deeplinkHash = $_SESSION[static::DEEPLINK_HASH];
            $brandCode = $_SESSION[static::BRAND_CODE];

            // destroy sessions
            $_SESSION[static::DEEPLINK_HASH] = '';
            $_SESSION[static::BRAND_CODE] = '';
            $this->redirect('deeplink/' . $deeplinkHash . '/' . $brandCode);
        }
    }

    /*
     * SB-174 added by machua 20190521
     * Order tab based from the CupGoTabOrders table
     * If there are no order value, then use the SortOrder in CupGoTabs table
     * @param $tab1 array
     * @param $tab2 array
     * @return int [-1-1]
     */
    public function sortTabsByOrder($tab1, $tab2)
    {
        if ($tab1['cmsOrder'] !== $tab2['cmsOrder'])
            return $tab1['cmsOrder'] - $tab2['cmsOrder'];
        else
            return $tab1['titleOrder'] - $tab2['titleOrder'];
    }

    /*
     * SB-251 added by jbernardez 20190712
     * function to choose which URL to use for survey monkey
     * @return str/false
     */
    private function surveyMonkeySource()
    {
        $user = new User();
        $isTeacher = $user->uGroups[5] === static::TEACHER;
        $isStudent = $user->uGroups[4] === static::STUDENT;
        if ($isTeacher) {
            $source = static::TEACHER_SRC;
        } elseif ($isStudent) {
            $source = static::STUDENT_SRC;
        } else {
            $source = false;
        }

        return $source;
    }

    /*
     * SB-251 added by jbernardez 20190712
     * check user survey monkey value return true if yes
     * @return bool true/false
     */
    private function surveyMonkeyCheck()
    {
        $u = new User();
        $ui = UserInfo::getByID($u->getUserID());
        $userAttribs = AttributeSet::getByHandle(static::CONTACT_DETAILS);

        foreach ($userAttribs->getAttributeKeys() as $ua) {
            if ($ua->getAttributeKeyHandle() == static::SURVEY_MONKEY) {
                $surveyMonkey = $ui->getAttribute(static::SURVEY_MONKEY);
            }
        }

        if ((is_null($surveyMonkey)) || ($surveyMonkey == '')) {
            $returnValue = false;
        } else {
            $returnValue = true;
        }

        return $returnValue;
    }

    /*
     * SB-251 added by jbernardez 20190710
     * Hides the user Survey Mokey popup by setting the User attribute, "uSurveyMonkey" to true
     */
    public function stopSurveyMonkey()
    {
        $user = new User();
        $userInfo = UserInfo::getByID($user->getUserID());
        $userAttributes = AttributeSet::getByHandle(static::CONTACT_DETAILS);

        foreach ($userAttributes->getAttributeKeys() as $userAttribute) {
            if ($userAttribute->getAttributeKeyHandle() == static::SURVEY_MONKEY) {
                $userInfo->setAttribute($userAttribute, 1);
            }
        }
    }
}
