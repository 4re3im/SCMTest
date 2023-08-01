<?php

defined('C5_EXECUTE') || die("Access Denied.");

/**
 * HTML formatting
 * @author Ariel Tabag <atabag@cambridge.org>
 * May 5, 2015
 */
class MyResourcesHelper
{
    const HOTMATHS = 'HOTMATHS';

    //ANZGO-3601 Modified by Shane Camus 01/17/18
    public function formatDisplay($subscriptions)
    {
        $uh = new View();
        $html = '';

        //temp URL - need to change AWS File bucket
        $educationTitleURLTemp = "https://cambridge.edu.au/education/titles/";
        $educationFileURL = BASE_URL . "/files/cup_content/images/titles/";

        // Modified by Paul Balila, 2016-07-08, ANZGO-2686
        $userSubs = new CupGoUserSubscription();

        // ANZGO-3383 Modified by John Renzo Sunico, May 12, 2017
        // Removed fetching of subscriptions here

        if (!$subscriptions) {
            return "<div class='container-fluid container-bg-1 resources-container'>
                <br/><br/>
                <div class='row text-center'>
                <div class='col-lg-12 col-nd-12 col-sm-12 col-xs-12'><h4 id='noresource'>Nothing to display.</h4>
                </div></div></div>";
        }

        foreach ($subscriptions as $id => $subscription) {

            if (!$id) {
                continue;
            }

            $expirationHTML = '';
            $source = '';
            $daysLeft = 0;
            $finalDaysLeft = 0;

            // ANZGO-3043
            $authToken = '';
            $brandCodes = '';

            // Moved user object call out of the loop to call it only once
            $u = new user();
            foreach ($subscription as $userSubscription) {
                // ANZGO-3600 added by jbernardez 20180212
                $userSubs->tabAccessUpdateBySID(
                    $userSubscription['SubscriptionID'],
                    $u->uID,
                    $userSubscription['UserSubscriptionID']
                );
                // ANZGO-3600 added by jbernardez 20180219
                $activeTabs = $userSubs->checkActiveUserTabs(
                    $userSubscription['SubscriptionID'],
                    $u->uID,
                    $userSubscription['UserSubscriptionID']
                );

                //product title details
                $isbn13 = $userSubscription['isbn13'];
                $displayName = $userSubscription['displayName'];
                $prettyURL = $userSubscription['prettyUrl'];
                $image = $educationFileURL . $isbn13 . '_180.jpg';
                $educationTitleURL = $educationTitleURLTemp . $prettyURL;

                //subscription details
                $type = $userSubscription['Type'];
                $description = $userSubscription['Description'];
                $source = $userSubscription['Source'];
                $tabID = $userSubscription['TabID'];
                $saID = $userSubscription['SA_ID'];
                $usID = $userSubscription['UserSubscriptionID'];
                $resourceTitleID = $userSubscription['titleID'];

                // ANZGO-3043
                if ($source == static::HOTMATHS) {
                    $authToken = $userSubscription['authToken'];
                    $brandCodes = $userSubscription['brandCodes'];
                    // ANZGO-3158
                    $finalDaysLeft = $userSubscription['tokenExpiryDate'] > 0 ?
                        $userSubscription['tokenExpiryDate'] : 0;
                }

                // ANZGO-3045
                $titleContents = $source == 'Go' ?
                    CupContentTitle::fetchContentByPrettyUrl($prettyURL, $tabID,
                        $usID) :
                    CupGoExternalUser::fetchByID($usID);

                if (!empty($titleContents)) {
                    foreach ($titleContents as $titleContent) {
                        // ANZGO-3600 added by jbernardez 20180219
                        if (isset($titleContent['TabAccess'])
                            && ($titleContent['TabAccess'] == 'Y' || $activeTabs == true)
                            && $userSubscription['Active'] == 'Y') {
                            // ANZGO-3315, modified by James Bernardez, 2017/06/27
                            $tabNameLower = strtolower($titleContent['TabName']);
                            $subscriptionByTabAccess = $userSubs->subscriptionsByTabAccess(
                                $titleContent['tabID'],
                                $usID
                            );
                            $tabDuration = $subscriptionByTabAccess[0]['Duration'];
                            $tabCreationDate = $subscriptionByTabAccess[0]['USubCreationDate'];
                            $tabEndDate = $subscriptionByTabAccess[0]['USubEndDate'];
                            // ANZGO-3055
                            $accessCode = $subscriptionByTabAccess[0]['AccessCode'];
                            $tabDaysRemaining = $subscriptionByTabAccess[0]['DaysRemaining'];

                            // ANZGO-3315, modified by James Bernardez, 2017/06/27
                            $formatExpiration = $this->formatExpiration(
                                $type,
                                $tabCreationDate,
                                $tabEndDate,
                                $tabDaysRemaining,
                                $tabDuration,
                                $tabNameLower
                            );

                            $expirationMessage = $formatExpiration['message'];
                            $daysLeft = $formatExpiration['days_left'];

                            if ($daysLeft > $finalDaysLeft) {
                                $finalDaysLeft = $daysLeft;
                            }

                            $expirationHTML .= '<p class="subscription-p"><label>' .
                                $titleContent['TabName'] . '</label>';

                            if ($description) {
                                $expirationHTML .= '<div>' . $description . '</div>';
                            }

                            $accessCodeDisplay = $accessCode ? '(' . $accessCode . ')' : '';

                            if ($expirationMessage && $source == 'Go') {
                                $expirationHTML .= '<div>' . $expirationMessage . '&nbsp;' .
                                    $accessCodeDisplay . '</div>';
                            }
                        }
                    }
                }
            }

            $expiredClass = $finalDaysLeft <= 0 && $source == 'Go' ? 'expired' : '';

            // Modified by Paul Balila for ticket ANZGO-2661, 2016-08-25
            // Do not display inactive subscriptions.
            $html .= "<div id='$resourceTitleID' ";
            $html .= "class='container-fluid container-bg-1 resources-container $expiredClass'><br /><br />";
            $html .= "<div class='row'>";
            $html .= "<div class='col-lg-12 col-nd-12 col-sm-12 col-xs-12'>";
            $html .= "<div class='container'>";
            $html .= "<div class='row'>";
            $html .= "<div class='col-lg-2 col-md-3 col-sm-3 col-xs-6'>";
            $html .= "<div class='book-wrap'>";

            if ($source == 'Go') {
                $html .= "<div class='load cover'><a href='" . $uh->url('/go/titles/' . $prettyURL) . "'>";
            } else {
                $html .= "<div class='load cover'><a href='#'>";
            }

            $html .= "<img src='$image' class='book-cover'/></a></div>";
            $html .= "<div class='undercover' style='border-color: #5c5959;'></div> ";
            $html .= '</div>';

            if ($source == 'Go') {
                if ($finalDaysLeft <= 0) {
                    $html .= "<a class='btn btn-danger btn-block go-btn' href='$educationTitleURL'> PURCHASE? </a>";
                } elseif ($finalDaysLeft <= 30) {
                    $html .= "<a class='btn btn-danger btn-block go-btn' href='$educationTitleURL'>RENEW?</a>";
                } else {
                    $html .= '';
                }
            }

            $html .= "</div><div class='col-lg-7 col-md-6 col-sm-5 col-xs-12 rsrc-display'>";

            // ANZGO-3045
            $titleContents = $source == 'Go' ?
                CupContentTitle::fetchContentByPrettyUrl($prettyURL) :
                CupGoExternalUser::fetchByID($usID);

            // ANZGO-3043
            $apiPath = null;
            if ($source == 'Go') {
                $html .= "<a href='" . $uh->url('/go/titles/' . $prettyURL) . "'><h1>" .
                    $this->formatProductDisplayName($displayName);
            } else {
                // ANZGO-3043
                $hmApiLink = HOTMATHS_CONNECT;
                // ANZGO-3158
                $apiPath = "https://$hmApiLink/cambridgeLogin?externalId=$titleContents->user_id";
                $apiPath .= "&access_token=$authToken&brandCode=$brandCodes";
                $html .= "<a href='$apiPath' target='_blank'><h1>$this->formatProductDisplayName($displayName)";
            }

            if ($daysLeft <= 30 && $daysLeft > 0) {
                $html .= "<span class='go-resource-status'>EXPIRES SOON!</span>";
            }

            $html .= '</h1></a>';

            $html .= $expirationHTML . '</p>';

            if ($titleContents && $finalDaysLeft > 0 || $source == static::HOTMATHS) {

                // ANZGO-3158
                // added source for HOTMATHS Connect
                $resourcesLinks = $this->sortResources($titleContents, $saID,
                    $apiPath, $source);
                if ($source == 'Go') {
                    $html .= "<a href='#' class='toggle-rsrc-panel'>";
                    $html .= "<svg><use xlink:href='#icon-view_all'></use></svg>";
                    $html .= "<span class='svg-text'>View All Resources</span>";
                    $html .= '</a>';
                }

                $html .= "<div class='panel panel-default resources-panel' style='display:none;'>";
                $html .= "<div class='panel-body'>";
                $html .= "<div class='row'>";
                $html .= "<div class='col-lg-6'>";
                $html .= "<ul style='list-style-type:none'>";

                /** ANZGO-3721 modified by Maryjes Tanada 2018-05-23
                 *  remove online resource link in View all resources if Teacher product exist
                 */
                $tabToCheck = array(
                    'online teacher resource',
                    'online teacher edition'
                );
                $tabHolder = array();
                foreach ($resourcesLinks['first'] as $key => $value) {
                    $tabHolder = array_merge($tabHolder, $value);
                }
                $tabHolder = array_map('strtolower', $tabHolder);
                $teacherExist = array_intersect($tabHolder, $tabToCheck);
                foreach ($tabHolder as $index => $value) {
                    if ($value === 'online teacher edition' || $value === 'online teacher resource') {
                        $isActiveTeacherTab = $tabHolder[$index + 2];
                    }
                }

                foreach ($resourcesLinks['first'] as $rlf) {
                    $smallRlf = strtolower($rlf[0]);
                    $studentFlag = explode(' ', $smallRlf);
                    if ($u->uGroups[5] === 'Teacher' && !empty($teacherExist) && $isActiveTeacherTab === 'y'
                        && ($smallRlf === 'online resource' || $studentFlag === 'student')) {
                        continue;
                    } else {
                        $html .= "<li><a href='" . $uh->url("/go/titles/$prettyURL/" . $smallRlf) .
                            "' class='resource-tabs'>$rlf[1]</a></li>";
                    }
                }

                $html .= '</ul></div>';
                $html .= "<div class='col-lg-6'>";
                $html .= "<ul style='list-style-type:none;'>";

                foreach ($resourcesLinks['second'] as $rls) {
                    $html .= "<li><a href='" . $uh->url("/go/titles/$prettyURL/" . strtolower($rls[0])) .
                        "' class='resource-tabs'>$rls[1]</a></li>";
                }

                $html .= '</ul>';
                $html .= '</div>'; // col-lg-6
                $html .= '</div>'; // row
                $html .= '</div>'; // panel-body
                $html .= '</div>'; // panel
            }

            $html .= "</div><div class='col-lg-3 col-md-3 col-sm-4 col-xs-12 text-right'>";

            if ($finalDaysLeft <= 0 && $source == 'Go') {
                $html .= "<a class='resource-action' href='#'>";
                $html .= "<span class='svg-text delete_resource' style='margin-right:13px' name='$usID'>";
                $html .= 'Delete Resource';
                $html .= '</span>';
                $html .= "<svg><use xlink:href='#icon-delete'></use></svg>";
                $html .= '</a>';
            } else {
                $html .= '';
                if ($resourcesLinks['button_resources']) {
                    foreach ($resourcesLinks['button_resources'] as $link) {
                        $html .= $link;
                    }
                }
            }

            $html .= '</div></div></div></div></div><br/><br/></div>';
        }

        return $html;
    }

    public function formatPDF($tabID)
    {

        $html = '<div clas="row">';

        $htmlColumn1 = '';
        $htmlColumn2 = '';

        foreach (CupGoTabContent::fetchAllByTabID($tabID) as $tab_content) {

            $columnNumber = $tab_content['ColumnNumber'];

            $contentData = $tab_content['ContentData'] ? $tab_content['ContentData'] : '';

            $contentHeading = $tab_content['ContentHeading'] ? $tab_content['ContentHeading'] : '';

            $contentID = $tab_content['ID'];

            $tabName = $tab_content['TabName'];

            $titleName = $tab_content['name'];

            // Added by Paul Balila, 2016-04-26
            // For ticket ANZUAT-137
            $htmlCopy = '<p><strong>' . $contentHeading . '</strong></p>';
            $htmlCopy .= '<p>' . $contentData . '</p>';

            //column position
            $htmlCopy .= $this->formatList($contentID);

            //identify the column number
            if ($columnNumber == 1) {
                $htmlColumn1 .= $htmlCopy;
            } else {
                $htmlColumn2 .= $htmlCopy;
            }
        }

        if ($htmlColumn1) {
            $html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">' . $htmlColumn1 . '</div>';
        }
        if ($htmlColumn2) {
            $html .= '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">' . $htmlColumn2 . '</div>';
        }
        if (!$htmlColumn1 && !$htmlColumn2) {
            $html = 'Coming soon';
        }

        $html .= '</div>';

        CupGoLogs::trackUser("Resources", "View Tab",
            $titleName . ' > ' . $tabName);

        return array('tab_name' => $tabName, 'html' => $html);
    }


    // ANZGO-3946 added by machua 20181210 get the contents of the tile (i.e. heading, content details)
    public function getTileAssets($tabID)
    {
        // SB-293 modified by machua 20190808 to add tab text in modal
        $assets = array();
        // SB-305 modified by machua 20190823 to show public tab text when there are no contents
        $formattedTabContents = array();

        $tabDetail = (array)CupGoTabs::fetchTabByID($tabID);
        $tabDetail = $tabDetail['existing_result'];
        $isAlwaysUsePublicTab = $tabDetail['AlwaysUsePublicText'] === 'Y';
        if ($isAlwaysUsePublicTab) {
            $assets['tabText'] = $tabDetail['Public_TabText'];
        } else {
            $assets['tabText'] = $tabDetail['Private_TabText'];
        }

        $tabContents = CupGoTabContent::fetchAllByTabID($tabID);
        foreach ($tabContents as $tabContent) {            
            $tabArray = array();

            $contentID = $tabContent['ID'];
            $tabArray['contents'] = $this->getTileContents($contentID);
            // SB-75 added by jbernardez 20190227
            $tabArray['contentHeading'] = $tabContent['ContentHeading'];
            $formattedTabContents[] = $tabArray;
        }
        $assets['tabContents'] = $formattedTabContents;
        return $assets;
    }

    // ANZGO-3946 added by machua 20181210 get the details of the content (e.g. url, file name, file size)
    private function getTileContents($contentID)
    {
        $contents = array();
        foreach (CupGoContentDetail::fetchAllByContentID($contentID) as $contentDetail) {
            $contentArray = array();
            $typeID = $contentDetail['TypeID'];
            $contentArray['contentID'] = $contentDetail['ID'];

            $contentArray['contentName'] = $contentDetail['Public_Name'];
            if($typeID === "1001"){
                $contentArray['url'] = $contentDetail['URL'];
            } else {
                $contentArray['fileName'] = $contentDetail['FileName'];
                $contentArray['fileType'] = strtoupper(array_pop(explode('.', $contentDetail['FileName'])));
                // SB-230 added by machua 20190626 to reference the correct file size
                $contentArray['fileInfo'] = $contentDetail['FileInfo'];
            }

            $contents[] = $contentArray;
        }
        return $contents;
    }

    //ANZGO-3979 added by machua 20181220 to fetch the correct data for weblinks
    public function getWeblinks($productID, $tabID)
    {
        // SB-305 modifed by machua 20190823 to show tab text in weblinks modal
        $assets = array();
        $tabDetail = (array)CupGoTabs::fetchTabByID($tabID);
        $tabDetail = $tabDetail['existing_result'];
        $isAlwaysUsePublicTab = $tabDetail['AlwaysUsePublicText'] === 'Y';
        if ($isAlwaysUsePublicTab) {
            $assets['tabText'] = $tabDetail['Public_TabText'];
        } else {
            $assets['tabText'] = $tabDetail['Private_TabText'];
        }

        $redirect = new CupGoRedirect();
        $redirectsData = $redirect->getRedirectByEpubName($productID);

        $assets['redirects'] = $redirectsData;
        return $assets;
    }


    private function formatList($contentID)
    {
        $titlesHelper = Loader::helper('titles', 'go_product');
        $html = '<ul class="content-detail content-detail-' . $contentID . '">';

        foreach (CupGoContentDetail::fetchAllByContentID($contentID) as $content_detail) {
            $url = $content_detail['URL'];

            $name = $this->removeSpecialCharacters($content_detail['Public_Name']);

            $name = $titlesHelper->removeSpecialCharacters($content_detail['Public_Name']);
            $description = $content_detail['Public_Description'];
            $html .= '<li>';
            if ($content_detail['FileName']) {
                $html .= "<p id='$contentID'><a class='content-file-download' href='" .
                    $content_detail['ID'] .
                    "'>$name</a></p>";
            } else {
                $html .= '<p id="' . $contentID . '">' . $name;
                $html .= '<br><a href="' . $url . '">' . $url . '</a></p>';
            }

            if ($description) {
                $html .= '<div>' . $description . '</div><br>';
            }

            $html .= '</li>';
        }

        $html .= '</ul>';

        return $html;
    }

    /* ANZGO-3315 modified by James Bernardez 2017/06/27
     * added Tab Name for specificexpiration message
     * ANZGO-3748 modified by mtanada 2018/07/03 added reactivation type
    */
    public function formatExpiration(
        $type,
        $creationDate,
        $endDate,
        $daysRemaining,
        $duration,
        $tabNameLower = null
    ) {
        $displayEndDate = date('jS \of F Y', strtotime($endDate));
        $displayFrontDate = date('jS \of F Y', strtotime($creationDate));
        switch ($type) {
            case 'start-end':
            case 'end-of-year':
            case 'reactivation':
                $expirationMessage = "Valid until <strong>$displayEndDate</strong>";
                break;
            case 'duration':
                $dayLabel = ($duration > 1) ? "days" : "day";
                if ($duration > 0) {
                    // ANZGO-3315 modified by James Bernardez 2017/06/27
                    // added Tab Name for specifice expiration message
                    // text guide and quizmemore
                    if (($tabNameLower == "text guide") || ($tabNameLower == "quizmemore")) {
                        $expirationMessage = "Access within <strong>$duration</strong> $dayLabel from ";
                        $expirationMessage .= "<strong>$displayFrontDate</strong>";
                    } else {
                        $expirationMessage = "Download within <strong>$duration</strong> $dayLabel from ";
                        $expirationMessage .= "<strong>$displayFrontDate</strong>";
                    }
                } else {
                    $expirationMessage = "This resource will not expire.";
                }
                break;
            default:
                // Set days remaining to a high numebr so it will not appear as expired.
                $daysRemaining = 100;
                $expirationMessage = "No information exists for your subscription";
                break;
        }

        return array(
            'message' => $expirationMessage,
            'days_left' => $daysRemaining
        );
    }

    /* ANZGO-3468 Modified by Maryjes Tañada 02/26/2018
     * As per request, 'print and interactive textbook' and `print and interactive textbook powered by HOTmaths`
     * is removed from display
     */
    public static function formatProductDisplayName($name)
    {
        $searchArray = array(
            '(print and digital)',
            '(Print and Digital)',
            '(Print & Digital)',
            '(print & digital)',
            'print and digital',
            'Print and Digital',
            'Print & Digital',
            '(print and digital package)',
            '(Print and Digital Package)',
            '(Print & Digital Package)',
            '(print & digital Package)',
            'print and digital package',
            'Print and Digital Package',
            'Print & Digital Package',
            '(digital)',
            '(Digital)',
            '(print and interactive textbook)',
            '(Print and interactive textbook)',
            '(print & interactive textbook)',
            '(print and interactive textbook powered by HOTmaths)',
            '(print and interactive textbook powered by HOTmat',
            '(print and interactive textbook powered by Cambridge HOTmaths)',
            'print and interactive textbook',
            'Print and interactive textbook',
            'print & interactive textbook',
            'print and interactive textbook powered by HOTmaths',
            'print and interactive textbook powered by HOTmat',
            'print and interactive textbook powered by Cambridge HOTmaths',
            '(print)',
            '(Print)',
            '- print',
            '- Print',
            '- digital',
            '- Digital',
            'print',
            'Print',
            'digital',
            'Digital'
        );

        $replaceArray = array(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        );

        return str_replace($searchArray, $replaceArray, $name);
    }

    /**
     * ANZGO-3947 added by jbernardez/mtanada 20181210
     * ANZGO-3990 Modified by Shane Camus 01/04/19
     * @param $tabID
     * @return bool
     */
    public function isModal($tabID)
    {
        return sizeof(CupGoTabContent::fetchByTabID($tabID)) > 1;
    }

    /**
     * ANZGO-3492 Modified by John Renzo S. Sunico September 13, 2017
     * Overhauled the function following SonarQube
     * @param $titleContents
     * @param $source
     * @param null $apiPath
     * @param $saID
     * @param null $source
     * @return array
     */

    public function sortResources(
        $titleContents,
        $saID,
        $apiPath = null,
        $source = null
    ) {
        // ANZGO-3659 added by jbernardez 20180312
        // verify the token even before anything starts
        // this will not create a new user with HM, this happens in getHotmathsResourceLink()
        //$this->verifyAuthorizationTokenWithHM($u->uID);

        $firstPriority = array();
        $secondPriority = array();
        $buttonResources = array();
        $activeStatus = null;
        $isAdmin = in_array('Administrators', $_SESSION['uGroups']); // ANZG0-3891 modified by jdchavez 10/24/2018

        if ($source == static::HOTMATHS) {
            $tabName = "Interactive Textbook</span><span>powered by HOTmaths</span></a>";
            $buttonResources[0] = static::createRHSButton($tabName, array(
                "href" => $apiPath,
                "target" => "_blank",
                "class" => "btn btn-default btn-block go-resource-btn-wrap edu-green-wrap edu-green-chm",
                ""
            ));
        } else {
            foreach ($titleContents as $tc) {

                $tabID = $tc['tabID'];
                $tabName = $tc['TabName'];
                $tabNameLower = strtolower($tc['TabName']);
                $myResourcesLink = $tc['MyResourcesLink'];

                $resourceUrl = $tc['ResourceURL'];
                // ANZGO-3721 added by Maryjes Tanada 20180516 pass teacher user type
                $resourceUrl = $tc['HMProduct'] === 'Y' ? $this->getHotmathsResourceLink(
                    $_SESSION['uID'],
                    $saID,
                    $_SESSION['uGroups']
                ) : $resourceUrl;

                $userTypeRestriction = $tc['UserTypeIDRestriction'];
                $userRestrictions = $this->checkUserRestrictions($userTypeRestriction);

                $tabAccess = ($tc['TabAccess']) ? 'Y' : 'N';
                $tabAccess = $isAdmin ? 'Y' : $tabAccess;

                $active = $tabAccess === 'Y';
                // ANZGO-3947 modified by jbernardez 20181205
                // rewriting this to boolean as needed just the name of the active tab to be sent
                $active = $active && !$userRestrictions ? true : false;
                $activeStatus = $active ? true : false;

                $tabNameSpan = '<span class="' . $active . '">' . $tc['TabName'] . '</span>';

                $defaultClass = "btn btn-default btn-block go-resource-btn edu-green";
                $popupClass = CupGoTabs::hasContent($tc['tabID']) ? " edu-downloadables" : "";

                $icon = DIR_REL . '/files/cup_content/images/formats/' . $tc['TabIcon'];
                $defaultStyle = "background-image: url('$icon'); background-size: 30px 25px;
                    background-position: left 5px center; padding-left: 15%;";


                $defaultAttributes = array(
                    "id" => $tabID,
                    "style" => $defaultStyle,
                    "class" => $defaultClass,
                    "title" => $tabName,
                    "href" => $resourceUrl,
                    "target" => "_blank",
                );
                $downloadableAttributes = $defaultAttributes;
                $downloadableAttributes['class'] .= " edu-green-pdf edu-downloadables";

                switch ($tabNameLower) {
                    case 'about':
                    case 'errata':
                    case 'ask the authors':
                    case 'teacher support':
                        $secondPriority[] = array($tc['TabName'], $tabNameSpan);
                        break;
                    default:
                        $firstPriority[] = array(
                            $tc['TabName'],
                            $tabNameSpan,
                            $activeStatus,
                            $tc['tabID']
                        );
                        if ($myResourcesLink == 'Y' && !$userRestrictions && $tabAccess == 'Y') {
                            if ($tabNameLower == 'pdf textbook') {
                                $buttonResources[0] = static::createRHSButton($tabName,
                                    $downloadableAttributes);
                            } elseif (in_array(
                                $tabNameLower,
                                array(
                                    'pdf textbook and toolkit',
                                    'pdf textbook & toolkit'
                                ))) {
                                $buttonResources[1] = static::createRHSButton($tabName,
                                    $downloadableAttributes);
                            } elseif ($tabNameLower == 'interactive textbook') {
                                if (strpos($resourceUrl, 'interactive_book')) {
                                    $resourceUrl .= '&sk=' . session_id();
                                }

                                $attributes = $defaultAttributes;
                                $attributes['href'] = $resourceUrl;

                                $buttonResources[2] = static::createRHSButton($tabName,
                                    $attributes);
                            } elseif ($tabNameLower == 'digital toolkit') {
                                $buttonResources[3] = static::createRHSButton($tabName,
                                    $downloadableAttributes);
                            } elseif ($tabNameLower == 'electronic workbook') {
                                $buttonResources[4] = static::createRHSButton($tabName,
                                    $downloadableAttributes);
                            } elseif ($tabNameLower == 'electronic version') {
                                $buttonResources[5] = static::createRHSButton($tabName,
                                    $downloadableAttributes);
                            } elseif ($tabNameLower == 'teacher edition resource') {
                                $buttonResources[6] = static::createRHSButton($tabName,
                                    $downloadableAttributes);
                            } elseif(in_array($tabNameLower, array(
                                "teacher resource package",
                                "interactive textbook - teacher edition",
                                "online teaching suite"
                            ))) {
                                $buttonResources[7] = static::createRHSButton($tabName,
                                    $downloadableAttributes);
                            } elseif ($tabNameLower == 'online text guide') {
                                $attributes = $defaultAttributes;
                                $attributes['class'] .= " edu-green-it";
                                $buttonResources[8] = static::createRHSButton($tabName,
                                    $attributes);
                            } elseif ($tabNameLower == 'cambridge hotmaths') {
                                $attributes = $downloadableAttributes;
                                $attributes['class'] .= " edu-green-chm";
                                $buttonResources[9] = static::createRHSButton($tabName,
                                    $attributes);
                            } elseif ($tabNameLower == 'app') {
                                $attributes = $defaultAttributes;
                                $attributes['class'] .= " edu-green-app";
                                $attributes['href'] .= "https://itunes.apple.com/au/app/cambridge-australia-app/
                                id780344852?mt=8";
                                $buttonResources[10] = static::createRHSButton($tabName,
                                    $attributes);
                            } elseif ($tabNameLower == 'interactive textbook powered by hotmaths') {
                                $attributes = $defaultAttributes;
                                $attributes['class'] .= " edu-green-chm";
                                $attributes['href'] = "http://www.cambridge.edu.au/hotmaths";
                                $name = "<span>Interactive Textbook</span><span>powered by HOTmaths</span>";
                                $buttonResources[11] = static::createRHSButton($name,
                                    $attributes);
                            } elseif ($tabNameLower == 'interactive online resource') {
                                $attributes = $defaultAttributes;
                                $attributes['class'] .= " edu-green-ir";
                                $attributes['href'] = "http://www.cambridge.edu.au/dynamicscience";
                                $buttonResources[12] = static::createRHSButton($tabName,
                                    $attributes);
                            } elseif (in_array($tabNameLower, array(
                                "interactive online resource - teacher edition",
                                "online resource",
                                "online teacher edition"
                            ))) {
                                $attributes = $defaultAttributes;
                                $attributes['class'] .= " edu-green-ol-resource";

                                if ($attributes['href']) {
                                    $buttonResources[13] = static::createRHSButton($tabName,
                                        $attributes);
                                }
                            } elseif ($tabNameLower == 'interactive teacher edition') {
                                $attributes = $defaultAttributes;
                                $attributes['class'] .= " edu-interactive-teacher";
                                $buttonResources[14] = static::createRHSButton($tabName,
                                    $attributes);
                            } else {
                                $attributes = $defaultAttributes;
                                $attributes['class'] .= " $popupClass";
                                $buttonResources[15 + count($buttonResources) + 1] = static::createRHSButton(
                                    $tabName,
                                    $attributes
                                );
                            }
                        }

                        break;
                }
            }
        }

        ksort($buttonResources);

        return array(
            'first' => $firstPriority,
            'second' => $secondPriority,
            'button_resources' => $buttonResources
        );
    }

    /**
     * ANZGO-3492 Added by John Renzo S. Sunico, September 13, 2017
     * Create a dynamic Right hand side launcher
     * @param $name
     * @param $attributes array of anchor tag attributes
     * @return string formatted html
     */

    public static function createRHSButton($name, $attributes)
    {
        $attributeString = "";
        foreach ($attributes as $key => $value) {
            $attributeString .= " $key=\"$value\"";
        }

        return "<a $attributeString>$name</a>";
    }

    private function checkUserRestrictions($userTypeRestriction)
    {
        $u = $this->u;

        // NOTE:: this is checking if the usertype is restricted to access the Resource
        // so since you have the user_type_restriction ID or NUMBER then just check if this is
        // in the array of the user group - if it is then the user is not allowed

        return in_array($userTypeRestriction, $_SESSION['uGroups']);
    }

    private function removeSpecialCharacters($text)
    {

        $text = html_entity_decode($text);

        // left side double smart quote
        $find[] = 'â€œ';
        // right side double smart quote
        $find[] = 'â€';
        // left side single smart quote
        $find[] = 'â€˜';
        // right side single smart quote
        $find[] = 'â€™';
        // ellipsis
        $find[] = 'â€¦';
        // em dash
        $find[] = 'â€”';
        // en dash
        $find[] = 'â€“';

        $replace[] = '"';
        $replace[] = '"';
        $replace[] = "'";
        $replace[] = "'";
        $replace[] = "...";
        $replace[] = "-";
        $replace[] = "-";
        $text = str_replace($find, $replace, $text);

        $text = filter_var($text, FILTER_SANITIZE_STRING,
            FILTER_FLAG_STRIP_HIGH);

        return str_replace($find, $replace, $text);
    }

    // ANZGO-3721 modified by Maryjes Tanada 20180516
    // passing of group type to check if teacher has a student subscription in GO but a HM product
    public function getHotmathsResourceLink($uId, $saID, $groupType = null)
    {
        Loader::library('HotMaths/api');
        $params = array(
            'userId' => $uId,
            'saId' => $saID,
            'response' => 'STRING'
        );
        $api = new HotMathsApi($params);
        if ($api !== false) {
            // ANZGO-3389, modified by James Bernardez, 05/31/2017
            // blocked old method, created new one in api
            // ANZGO-3721, modified by Maryjes Tanada 05/16/2018
            // Modify to correct product id in url link to HM when teacher has student product in TNG
            $hmProduct = $api->getHmProduct();
            if ($groupType === 'Teacher' && $hmProduct->subscriberType === 'STUDENT') {
                return $api->getHmAccessLinkModified($hmProduct->teacherProductId);
            }
            return $api->getHmAccessLinkModified();
        }
    }

    // ANZGO-3659 added by jbernardez 20180312
    // check one time, if recent authorization token in Hotmaths table is equal to
    // authorization token in HM side.
    // pass 0 for saId so it will no release an error
    private function verifyAuthorizationTokenWithHM($uId)
    {
        Loader::library('HotMaths/api');
        $params = array('userId' => $uId, 'saId' => 0, 'response' => 'STRING');
        $api = new HotMathsApi($params);
        if ($api !== false) {
            $resHMUser = $api->isHmUser();

            if (isset($resHMUser->success) && $resHMUser->success === false) {
                // There is no user with that ID, just skip this
                // he hasnt registered yet with HM, no need to register here
                return;
            } else {
                // aaahhhh he's a registered user with HM, we can use this one
                $resTokenValid = $api->isAccessTokenValid();

                if ($resHMUser->accessToken == $resTokenValid['authorizationToken']) {
                    // if the same, then token is still valid, just continue
                    return;
                } else {
                    // else, renew the token
                    $api->forceRenewHmUser();
                }
            }
        }
    }
}
