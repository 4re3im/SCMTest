<?php

defined('C5_EXECUTE') || die("Access Denied.");

Loader::library('hub-sdk/autoload');

use HubEntitlement\Repositories\ProductRepository;

/**
 * HTML formatting of title/product page
 * @author Ariel Tabag <atabag@cambridge.org>
 * @author Paul Balila <gbalila@cambridge.org>
 * April 10, 2015
 */
class TitlesHelper
{
    const TEACHER                   = 'teacher';
    const STUDENT                   = 'student';
    const LOGIN_ONLY                = 'Login only';
    const TAB_ID                    = 'tabID';
    const TAB_NAME                  = 'TabName';
    const VISIBILITY                = 'Visibility';
    const USER_TYPE_ID_RESTRICTION  = 'UserTypeIDRestriction';
    const CONTENT_ACCESS            = 'ContentAccess';
    const TAB_ACCESS                = 'TabAccess';
    const SUBSCRIPTION              = 'Subscription';
    const TAB_GO_CONTENT_VISIBILITY = 'TabGoContentVisibility';
    const CONTENT_DEMO              = 'ContentDemo';
    const PUBLIC_DESCRIPTION        = 'Public_Description';
    // SB-101 added by mabrigos 20190327
    const FREE                      = 'Free';

    protected $studentID;
    protected $teacherID;
    protected $isAdmin;
    protected $redirectors      = false;
    protected $currentTabId     = 0;
    protected $tabsSubscription = [];
    // SB-239 added by machua 20190628
    protected $userSubscription = [];

    private $hmId       = false;
    private $hmError    = false;

    public function __construct()
    {
        $u = new User();
        // Gets all user groups in the system then...
        Loader::model('misc', 'go_product');
        $this->groups = Misc::getGroups();

        // assign teacher and student ID to respective variables
        foreach ($this->groups as $group) {
            if (strtolower($group['gName']) === static::TEACHER) {
                $this->teacherID = $group['gID'];
            }

            if (strtolower($group['gName']) === static::STUDENT) {
                $this->studentID = $group['gID'];
            }
        }

        $this->isAdmin = (
            in_array("Administrators", $u->uGroups) || in_array("CUP Staff", $u->uGroups)
        );
    }

    public function setUserTabsSubscription(array $tabs)
    {
        $this->tabsSubscription = $tabs;
    }

    public function isSubscribedToTabId($tabId)
    {
        return in_array($tabId, $this->tabsSubscription);
    }

    /* SB-239 
     * Added by machua 20190628
     * Set the user subscription for the title
     * @param subscription
     */
    public function setUserTitleSubscription($subscription)
    {
        $this->userSubscription = $subscription;
    }

    /* SB-239
     * Added by machua 20190628
     * Get the entitlementID to use in retrieving the correct HMID
     * @param tabId
     */
    public function getEntitlementIdByTabId($tabId)
    {
        foreach ($this->userSubscription as $subTabID => $subTabDetails) {
            if ((int)$tabId === (int)$subTabID) {
                return $subTabDetails['entitlementID'];
            }
        }

        return null;
    }

    // ANZGO-3665 Modified by Shane Camus
    public function formatProduct($titleContents, $tab = false, $hmAdminLink = null)
    {
        if (!$titleContents) {
            return null;
        }

        $u = new User();

        $userGroup = 0;
        foreach ($u->uGroups as $key => $value) {
            $group = strtolower($value);
            if (in_array($group, [static::STUDENT, static::TEACHER])) {
                $userGroup = $key;
                break;
            }
        }

        $hasSubscriptionOnTitle = false;
        $html = '';

        foreach ($titleContents as $content) {
            $contentTabID = $content['ID'];
            $this->currentTabId = $contentTabID;
            $tabName = strtolower(trim($content[static::TAB_NAME]));
            $isTabPrivate = $content[static::VISIBILITY] === 'Private';
            $userLoggedIn = $u->isLoggedIn();

            $content[static::TAB_ID] = $contentTabID;

            if ($isTabPrivate && !$userLoggedIn && !$this->isAdmin) {
                continue;
            }

            $html .= <<<html
                <div class="row row-accordion">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="go-accordion" id="accordion_$contentTabID">
                            <div class="panel panel-default go-panel">
                                <div class="panel-heading go-first-panel" role="tab" id="headingOne_$contentTabID">
html;

            $isStudent = $userGroup == $this->studentID;
            $forTeacherOnly = $content[static::USER_TYPE_ID_RESTRICTION] == $this->teacherID;
            $isContentPrivate = $content[static::VISIBILITY] != 'Public';
            $originalTabName = $content[static::TAB_NAME];
            $formattedTabName = str_replace(array(' ', '&'), array('-', 'and'), $tabName);
            $contentTabImage = $this->getTabImage($content['TabIcon']);

            if (($isStudent && $forTeacherOnly) && $isContentPrivate && !$this->isAdmin) {
                $html .= <<<html
                    <div>
                        <h3 class="panel-title $formattedTabName">
                            $contentTabImage&nbsp;
                            <div class="tab-name">$formattedTabName</div>
                        </h3>
                    </div>
html;
            } else {
                $html .= <<<html
                    <a class="go-panel-header"
                        data-toggle="collapse"
                        href="#$contentTabID"
                        data-parent="#accordion_$contentTabID"
                        aria-expanded="false"
                        aria-controls="$contentTabID">
html;

                $accordionIconClass = strtolower($originalTabName) == $tab ? 'top' : 'bottom';
                $html .= <<<html
                    <span class="glyphicon glyphicon-triangle-$accordionIconClass pull-right"></span>
html;

                $html .= <<<html
                    <h3 class="panel-title $formattedTabName">
                        $contentTabImage&nbsp;
                        <div class="tab-name">$originalTabName</div>
                    </h3>
                    </a>
html;
            }

            // End of panel-heading
            $html .= '</div>';

            $collapseTabClass = strtolower($originalTabName) == $tab ? 'in' : '';
            $html .= <<<html
                    <div id="$contentTabID"
                        class="panel-collapse collapse $collapseTabClass"
                        role="tabpanel"
                        aria-labelledby="headingOne_$contentTabID">
html;

            $isContentAccessSubscription = $content[static::CONTENT_ACCESS] === static::SUBSCRIPTION;
            $isContentLoginOnly = $content[static::CONTENT_ACCESS] === static::LOGIN_ONLY;

            // SB-239 added by machua 20190628 to check if user has access only if he has a subscription to the tab            
            $tabEntitlementId = $this->getEntitlementIdByTabId($contentTabID);
            $userHasAccess = !is_null($tabEntitlementId);
            $isAlwaysUsePublicTab = $content['AlwaysUsePublicText'] === 'Y';
            // SB-101 added by mabrigos 20190327
            $isContentFree = $content[static::CONTENT_ACCESS] === static::FREE;

            // SB-239 modified by machua 20190628
            $content[static::TAB_ACCESS] = !is_null($tabEntitlementId) ? 'Y' : 'N';
            if ($content['HmID']) {
                $tempHmId = $content['HmID'];
            } else {
                $tempHmId = $hmId;
            }

            if ($content['HMProduct'] === 'Y') {
                $hmId = CupGoTabHmIds::getHmIdByEntitlementIdAndTabId(
                    $tabEntitlementId, $contentTabID
                );
                $content['HMProductId'] = $hmId;
            }

            $hasAccess = $this->checkDetailAccessRight(
                $userGroup,
                $content[static::CONTENT_ACCESS],
                $content[static::USER_TYPE_ID_RESTRICTION],
                $content[static::TAB_ACCESS],
                $content[static::TAB_ID],
                $tempHmId
            );
            
            // SB-122 added by jbernardez 20190408
            // override $hasAccess to true to view all list for content
            $hasAccess = true;

            // SB-111 modified by mabrigos 20190327
            $hasSubscription = ($isContentAccessSubscription || $isContentLoginOnly) && $userHasAccess;
            // SB-309 added by machua 20190829 to cover the private tab text for student
            $isContentFreeAndStudentHasAccess = $userHasAccess && $isStudent && $isContentFree;
            $hasLoginOnlyAccess = $this->hasLoginOnlyAccess($userGroup, $content[static::USER_TYPE_ID_RESTRICTION]);
            // SB-310 modified by machua 20190829 to restrict access to public visibility only
            $isContentLoginOnlyAndHasAccess = $isContentLoginOnly && $hasLoginOnlyAccess && !$isContentPrivate;

            // SB-15 added by machua 20190124
            $isSafari12orHigher = false;
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $userAgentInfoArr = explode('/', $userAgent);
            $uAgentSize = count($userAgentInfoArr);
            $browserInfo = $userAgentInfoArr[$uAgentSize - 2];
            $browserInfoArr = explode('.', $browserInfo);
            if (strpos($browserInfo, 'Safari') !== false
                && strpos($userAgent, 'Macintosh') !== false
                && strpos($userAgent, 'Chrome') === false
                && (int)$browserInfoArr[0] >= 12) {
                $isSafari12orHigher = true;

            }

            // SB-249 mabrigos 20190710 added condition if coming soon skip adding public/private text. 
            if ($content['ComingSoon'] === 'Y') {
                $html .= '';
            // SB-307 modified by machua 20190828 to get the private tab text for a subscribed tab
            // SB-309 added by machua 20190829 to cover the private tab text for student and public tab text for teacher
            } elseif (($hasSubscription || $this->isAdmin
                || $isContentLoginOnlyAndHasAccess || $isContentFreeAndStudentHasAccess) 
                && !$isAlwaysUsePublicTab) {
                if ($isSafari12orHigher
                    && strpos($content['Private_TabText'], 'go/epub/preview_content') !== false) {
                    $newContent = str_replace('target="_blank"', '', $content['Private_TabText']);
                    $html .= $newContent;
                } else {
                    $html .= $content['Private_TabText'];
                }
            } else {
                if ($isSafari12orHigher
                    && strpos($content['Public_TabText'], 'go/epub/preview_content') !== false) {
                    $newContent = str_replace('target="_blank"', '', $content['Public_TabText']);
                    $html .= $newContent;
                } else {
                    $html .= $content['Public_TabText'];
                }
            }
            // SB-101 modified by mabrigos 20190327
            if ($isContentFree || $hasAccess || $this->isAdmin) {
                $html .= $this->getFormattedTabContents($content, $tempHmId, $hasSubscription, $hmAdminLink, $isContentLoginOnlyAndHasAccess);
            }

            $html .= '</div>'; // panel-collapse
            $html .= '</div>'; // panel
            $html .= '</div>'; // go-accordion
            $html .= '</div>'; // col-lg-12
            $html .= '</div>'; // row

            $hasSubscriptionOnTitle = $hasSubscriptionOnTitle || $hasSubscription;
        }

        return array('html' => $html, 'hasSubscription' => $hasSubscriptionOnTitle || $this->isAdmin);
    }

    public function getHMIdByTabId($tabId)
    {
        $product = ProductRepository::findProductWithTabId((int)$tabId);

        if (!$product) {
            return null;
        }

        $entitlement = array_pop($product->entitlements()->fetch());

        if (!$entitlement) {
            return null;
        }

        // SB-225 added by machua 20190625 changed the table where HMID is fetched
        return CupGoTabHmIds::getHmIdByEntitlementIdAndTabId(
                    $entitlement->id, $tabId
                );
    }

    // ANZGO-3628 Modified by Shane Camus 02/12/2018
    // weblinks tab should be following a separate logic
    // SB-101 modified by mabrigos 20190327
    protected function getFormattedTabContents($content, $tempHmId, $hasSubscription, $hmAdminLink = null, $isContentLoginOnlyAndHasAccess)
    {
        $html = '';
        $tabName = strtolower(trim($content[static::TAB_NAME]));
        $tabContents = CupGoTabContent::fetchAllByTabID($content[static::TAB_ID]);
        
        // SB-249 added condition if coming soon skip all other formatting and displaying of data. 
        if ($content['ComingSoon'] === 'Y') {
            $html .= <<<html
                    <p><i>Coming soon</i></p>
html;
        return $html;
        }
        
        if ($content['Columns'] >= 2 && ($tabName != 'weblinks' || $tempHmId <= 0)) {
            $col1 = '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">';
            $col2 = '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">';

            foreach ($tabContents as $tabContent) {

                if ($tabContent['IsTabContentActive'] == 'N') {
                    continue;
                }

                $isTabContentDemo = $this->checkDemoOnly(
                    $tabContent['DemoOnly'],
                    $content[static::CONTENT_ACCESS],
                    $content[static::TAB_ACCESS]
                );

                $isTabContentPublic = $tabContent[static::TAB_GO_CONTENT_VISIBILITY] == 'Public';
                // SB-129 added by jbernardez 20190415
                // this is also an override to always show the content header
                $isTabContentPublic = true;
                // SB-101 added by mabrigos 20190327
                $isContentFree = $tabContent[static::CONTENT_ACCESS] == static::FREE;

                if ($tabContent['ColumnNumber'] == 1) {
                    // ANZGO-3424 Modified by Maryjes Tanada 03/08/2018
                    // SB-101 modified by mabrigos 20190327
                    // SB-8 mabrigos 20190711 - added condition for content demo
                    if ($hasSubscription && $tabContent['DemoOnly'] === 'Y') {
                        $col2 .= '';
                    } else {
                        if ($isContentFree || $isTabContentPublic || $isContentLoginOnlyAndHasAccess || 
                            $this->isAdmin && $isTabContentDemo || $hasSubscription) {
                            // SB-316 added by jbernardez 20190909
                            if ($tabName !== 'weblinks') {
                                $col1 .= '<p style="font-weight:bold">' . $tabContent['ContentHeading'] . '</p>';
                            }
                        }

                        if ($isContentFree || $isTabContentPublic || $isContentLoginOnlyAndHasAccess || $this->isAdmin) {
                            $col1 .= '<div>' . $tabContent['ContentDescription'] . '</div>';
                        }

                        if ($isTabContentDemo) {
                            $col1 .= $tabContent['ContentData'];

                            // SB-316 added by jbernardez 20190909
                            if ($tabName !== 'weblinks') {
                                $col1 .= $this->formatList(
                                    $tabContent['ID'],
                                    $tabContent[static::TAB_GO_CONTENT_VISIBILITY],
                                    $content[static::CONTENT_ACCESS],
                                    $content[static::TAB_ACCESS],
                                    $tabContent[static::USER_TYPE_ID_RESTRICTION]
                                );
                            }
                        }
                    }
                    
                } else {
                    // ANZGO-3424 Modified by Maryjes Tanada 03/08/2018
                    // SB-101 modified by mabrigos 20190327
                    // SB-8 mabrigos 20190711 - added condition for content demo 
                    if ($hasSubscription && $tabContent['DemoOnly'] === 'Y') {
                        $col2 .= '';
                    } else {
                        if ($isContentFree || $isTabContentPublic || $isContentLoginOnlyAndHasAccess || 
                        $this->isAdmin && $hasSubscription) {
                            // SB-316 added by jbernardez 20190909
                            if ($tabName !== 'weblinks') {
                                $col2 .= '<p style="font-weight:bold">' . $tabContent['ContentHeading'] . '</p>';
                            }
                        }

                        if ($isContentFree || $isTabContentPublic || $isContentLoginOnlyAndHasAccess || $this->isAdmin) {
                            $col2 .= '<div>' . $tabContent['ContentDescription'] . '</div>';
                        }

                        if ($isTabContentDemo) {
                            $col2 .= $tabContent['ContentData'];
                            // SB-316 added by jbernardez 20190909
                            if ($tabName !== 'weblinks') {
                                $col2 .= $this->formatList(
                                    $tabContent['ID'],
                                    $tabContent[static::TAB_GO_CONTENT_VISIBILITY],
                                    $content[static::CONTENT_ACCESS],
                                    $content[static::TAB_ACCESS],
                                    $tabContent[static::USER_TYPE_ID_RESTRICTION]
                                );
                            }
                        }
                    }
                }
            }

            // SB-316 added by jbernardez 20190909
            if ($tabName == 'weblinks') {
                foreach ($tabContents as $tabContent) {
                    if ($tabContent['IsTabContentActive'] == 'N') {
                        continue;
                    }

                    if ($tabContent['Global'] == 'Y') {
                        if ($this->redirectors === false) {
                            $this->redirectors = $this->showNewContent($content['id'], 1);
                        }

                        if ($this->redirectors !== false) {
                            $col2 .= $this->redirectors;
                        }
                    }
                }
            }

            $col1 .= '</div>';
            $col2 .= '</div>';

            $html .= $col1 . $col2;

        } else {
            if ($content['HMProduct'] === 'Y') {
                $text = '';
                // ANZGO-3687 added by Maryjes Tanada 04/06/2018
                // Check HM links for Admins/CupStaff (Student and Teacher)
                if ($content['HMProductId'] || isset($hmAdminLink)) {
                    $this->hmId = $content['HMProductId'];
                    // SB-317 modified by jbernardez 20190905
                    $text = '<img src="/go/interactive_book/images/button-full.png">';
                } else {
                    // SB-225 modified by machua 20190701
                    if (!$this->hmId || (int)$tempHmId > 0) {
                        $this->hmId = $content['HmID'];
                        // SB-317 modified by jbernardez 20190905
                        $text = '<img src="/go/interactive_book/images/button-preview.png">';
                    }
                }
                // Create separate links for Student and Teacher resource when user is an Admin/Cup Staff
                if (isset($hmAdminLink)) {
                    $studentLink = $hmAdminLink[0];
                    $teacherLink = $hmAdminLink[1];
                } else {
                    // ANZGO-3915 modified by jbernardez 20181031
                    if ((int)$tempHmId > 0) {
                        $link = $this->checkBuildHMAccess($tempHmId);
                    // SB-239 modified by machua 20190628 get link only if there is an HMID value
                    } elseif ($this->hmId) {
                        $link = $this->checkBuildHMAccess($this->hmId);
                    }
                }

                if ($studentLink && strpos($tabName, 'teacher') === false) {
                    // SB-317 modified by jbernardez 20190905
                    $html .= <<<html
                        <p><a href='$studentLink' target='_blank'>$text</a></p>
html;
                } elseif ($teacherLink && strpos($tabName, 'teacher')) {
                    // SB-317 modified by jbernardez 20190905
                    $html .= <<<html
                        <p><a href='$teacherLink' target='_blank'>$text</a></p>
html;
                } elseif ($link) {
                    // SB-317 modified by jbernardez 20190905
                    $html .= <<<html
                        <p><a href='$link' target='_blank'>$text</a></p>
html;
                } else {
                    // SB-250 modified by machua 20190710 removed the 'There was a problem...' error message
                    $errorMessage = $this->hmError['message'];
                    $html .= <<<html
                        <p>$errorMessage</p>
html;
                }

            } elseif ($tabName == 'weblinks') {
                foreach ($tabContents as $tabContent) {
                    if ($tabContent['IsTabContentActive'] == 'N') {
                        continue;
                    }

                    if ($tabContent['Global'] == 'Y') {
                        $html .= $tabContent['ContentData'];

                        if ($this->redirectors === false) {
                            $this->redirectors = $this->showNewContent($content['id'], 1);
                        }

                        if ($this->redirectors !== false) {
                            $html .= $this->redirectors;
                        }
                    }
                }
            } else {
                foreach ($tabContents as $tabContent) {

                    if ($tabContent['IsTabContentActive'] == 'N') {
                        continue;
                    }

                    $isTabContentPublic = $tabContent[static::TAB_GO_CONTENT_VISIBILITY] == 'Public';
                    // SB-129 added by jbernardez 20190411
                    // this is also an override to always show the content header
                    $isTabContentPublic = true;
                    $isTabContentDemo = $this->checkDemoOnly(
                        $tabContent['DemoOnly'],
                        $content[static::CONTENT_ACCESS],
                        $content[static::TAB_ACCESS]
                    );
                    // ANZGO-3424 Modified by Maryjes Tanada 03/08/2018
                    // SB-101 added by mabrigos 20190327
                    // SB-8 mabrigos 20190711 - added condition for content demo 
                    if ($hasSubscription && $tabContent['DemoOnly'] === 'Y') {
                        $col2 .= '';
                    } else {
                        if ($isContentFree || $isTabContentPublic || $isContentLoginOnlyAndHasAccess || 
                            $this->isAdmin && $hasSubscription) {
                            $html .= '<p style="font-weight:bold">' . $tabContent['ContentHeading'] . '</p>';
                        }

                        if ($isContentFree || $isTabContentPublic || $hasSubscription || $isContentLoginOnlyAndHasAccess) {
                            $html .= '<div>' . $tabContent['ContentDescription'] . '</div>';
                        }

                        if ($isTabContentDemo) {
                            $html .= $tabContent['ContentData'];
                            $html .= $this->formatDiv(
                                $tabContent['ID'],
                                $tabContent[static::TAB_GO_CONTENT_VISIBILITY],
                                $content[static::CONTENT_ACCESS],
                                $content[static::TAB_ACCESS],
                                $tabContent[static::USER_TYPE_ID_RESTRICTION]
                            );
                        }
                    }
                }
            }
        }

        return $html;
    }

    private function formatDiv($contentID, $contentHeadingVisibility, $contentAccess, $tabSubscription, $userType)
    {
        $u = new User();
        $userGroup = 0;
        foreach ($u->uGroups as $key => $value) {
            if (strtolower($value) === static::STUDENT || strtolower($value) === static::TEACHER) {
                $userGroup = $key;
                break;
            }
        }
        $html = '<div class="content-detail">';

        foreach (CupGoContentDetail::fetchAllByContentID($contentID, $this->currentTabId) as $contentDetail) {
            $name = $this->removeSpecialCharacters($contentDetail['Public_Name']);
            $fileInfo = $contentDetail['FileInfo'] != '' ? ' [' . $contentDetail['FileInfo'] . ']' : '';
            if ($this->isAdmin) {
                if ($contentDetail[static::CONTENT_DEMO] === 'N') {
                    $html .= $this->createListItem($contentID, $contentDetail);
                }
                continue;
            }

            if ($contentAccess == 'Free') {
                // Check visibility of content heading
                // SB-101 modified by mabrigos removed checking for public content block
                if ($contentDetail[static::CONTENT_DEMO] === 'N') {
                    $html .= $this->createListItem($contentID, $contentDetail);
                }
                continue;
            }

            // SB-132 added by jbernardez 20190412
            if ($u->isLoggedIn() && $tabSubscription === 'Y') {
                if ($contentDetail[static::CONTENT_DEMO] === 'N') {
                    $html .= $this->createListItem($contentID, $contentDetail);
                }
                $html .= '</li>';
                continue;
            }

            if ($u->isLoggedIn() && $contentAccess == static::SUBSCRIPTION) {
                if ($tabSubscription == 'Y' && $contentDetail[static::CONTENT_DEMO] == 'N') {
                    $html .= $this->createListItem($contentID, $contentDetail);
                } else {
                    //Check content heading visibility
                    if ($contentHeadingVisibility == 'Public') {
                        if ($contentDetail[static::CONTENT_DEMO] == 'Y') {
                            $html .= $this->createListItem($contentID, $contentDetail);
                        } else {
                            if ($contentDetail[static::VISIBILITY] == 'Public') {
                                $html .= $this->createListItem($contentID, $contentDetail);
                            } else {
                                // Added by Paul Balila for special characters.
                                $html .= '<p id="' . $contentID . '">' . html_entity_decode($name) . $fileInfo . '</p>';
                                if ($contentDetail[static::PUBLIC_DESCRIPTION]) {
                                    $html .= '<div>' . $contentDetail[static::PUBLIC_DESCRIPTION] . '</div><br>';
                                }
                            }
                        }
                    // SB-135 added by jbernardez 20190412
                    // satisfies the block content=private, access tab=teacher, content access=teacher
                    } else {
                        $html .= '<p id="' . $contentID . '">' . html_entity_decode($name) . $fileInfo . '</p>';
                    }
                }
                continue;
            }

            if ($u->isLoggedIn() && $contentAccess === static::LOGIN_ONLY && $userGroup >= $userType &&
                $contentDetail[static::CONTENT_DEMO] == 'N') {
                // SB-130 modified by jbernardez 20190411
                // Additional fix to formatList function
                if ($contentDetail['Visibility'] == 'Public' || $contentDetail[static::VISIBILITY] == 'Private') {
                    $html .= $this->createListItem($contentID, $contentDetail);
                }
                continue;
            } else {
                //Check content heading visibility
                if ($contentHeadingVisibility == 'Public') {
                    if ($contentDetail[static::CONTENT_DEMO] == 'Y') {
                        $html .= $this->createListItem($contentID, $contentDetail);
                    } else {
                        if ($contentDetail[static::VISIBILITY] == 'Public') {
                            $html .= $this->createListItem($contentID, $contentDetail);
                        } else {
                            // Added by Paul Balila for special characters.
                            $html .= '<p id="' . $contentID . '">' . html_entity_decode($name) . $fileInfo . '</p>';
                            if ($contentDetail[static::PUBLIC_DESCRIPTION]) {
                                $html .= '<div>' . $contentDetail[static::PUBLIC_DESCRIPTION] . '</div><br>';
                            }
                        }
                    }

                }
                continue;
            }

            if (!$u->isLoggedIn() && ($contentAccess === static::LOGIN_ONLY ||
                    $contentAccess == static::SUBSCRIPTION ||
                    $contentDetail[static::CONTENT_DEMO] == 'Y')) {
                // Check content heading visibility
                if ($contentHeadingVisibility == 'Public') {
                    if ($contentDetail[static::VISIBILITY] == 'Public' || $contentDetail[static::CONTENT_DEMO] == 'Y') {
                        $html .= $this->createListItem($contentID, $contentDetail);
                    } else {
                        // Added by Paul Balila for special characters.
                        $html .= '<p id="' . $contentID . '">' . html_entity_decode($name) . $fileInfo . '</p>';
                        if ($contentDetail[static::PUBLIC_DESCRIPTION]) {
                            $html .= '<div>' . $contentDetail[static::PUBLIC_DESCRIPTION] . '</div><br>';
                        }
                    }
                }
                continue;
            }
        }
        $html .= '</div>';

        return $html;
    }


    //NOTE: There must be a better way! :(
    //for this entire listing of tabs
    //specially on calling $this->createListItem
    //because ... sigh ... :(
    private function formatList($contentID, $contentHeadingVisibility, $contentAccess, $tabSubscription, $userType)
    {
        $u = new User();

        $userGroup = 0;
        foreach ($u->uGroups as $key => $value) {
            if (strtolower($value) == 'student' || strtolower($value) == 'teacher') {
                $userGroup = $key;
            }
        }

        $html = '<ul class="content-detail content-detail-' . $contentID . '">';

        foreach (CupGoContentDetail::fetchAllByContentID($contentID, $this->currentTabId) as $contentDetail) {
            $name = $this->removeSpecialCharacters($contentDetail['Public_Name']);
            $fileInfo = $contentDetail['FileInfo'] != '' ? ' [' . $contentDetail['FileInfo'] . ']' : '';

            $html .= '<li>';
            if ($this->isAdmin) {
                if ($contentDetail[static::CONTENT_DEMO] == 'N') {
                    $html .= $this->createListItem($contentID, $contentDetail);
                    $html .= '</li>';
                }
                continue;
            }

            if ($contentAccess === 'Free') {
                // Check visibility of content heading
                // SB-101 modified by mabrigos 20190327
                if ($contentDetail[static::CONTENT_DEMO] === 'N') {
                    $html .= $this->createListItem($contentID, $contentDetail);
                }
                $html .= '</li>';
                continue;
            }

            if ($u->isLoggedIn() && $tabSubscription === 'Y') {
                if ($contentDetail[static::CONTENT_DEMO] === 'N') {
                    $html .= $this->createListItem($contentID, $contentDetail);
                }
                $html .= '</li>';
                continue;
            }

            if ($u->isLoggedIn() && $contentAccess === static::SUBSCRIPTION) {

                if ($tabSubscription === 'Y' && $contentDetail[static::CONTENT_DEMO] === 'N') {
                    $html .= $this->createListItem($contentID, $contentDetail);
                } else {
                    //Check content heading visibility
                    if ($contentHeadingVisibility === 'Public') {
                        if ($contentDetail[static::CONTENT_DEMO] === 'Y') {
                            $html .= $this->createListItem($contentID, $contentDetail);
                        } else {
                            if ($contentDetail[static::VISIBILITY] === 'Public') {
                                $html .= $this->createListItem($contentID, $contentDetail);
                            } else {
                                // Added by Paul Balila for special characters.
                                $html .= '<p id="' . $contentID . '">' . html_entity_decode($name) . $fileInfo . '</p>';
                                if ($contentDetail[static::PUBLIC_DESCRIPTION]) {
                                    $html .= '<div>' . $contentDetail[static::PUBLIC_DESCRIPTION] . '</div><br>';
                                }
                            }
                        }
                    // SB-135 added by jbernardez 20190412
                    // satisfies the block content=private, access tab=teacher, content access=teacher
                    } else {
                        $html .= '<p id="' . $contentID . '">' . html_entity_decode($name) . $fileInfo . '</p>';
                    }
                }
                $html .= '</li>';
                continue;
            }
            if ($u->isLoggedIn() && $contentAccess === static::LOGIN_ONLY && $userGroup >= $userType &&
                $contentDetail[static::CONTENT_DEMO] === 'N') {
                if ($contentDetail[static::VISIBILITY] === 'Public' ||
                        $contentDetail[static::VISIBILITY] === 'Private') {
                    $html .= $this->createListItem($contentID, $contentDetail);
                }
                $html .= '</li>';
                continue;
            } else {
                if ($contentHeadingVisibility === 'Public') {
                    if ($contentDetail[static::CONTENT_DEMO] === 'Y') {
                        $html .= $this->createListItem($contentID, $contentDetail);
                    } else {
                        if ($contentDetail[static::VISIBILITY] === 'Public') {
                            $html .= $this->createListItem($contentID, $contentDetail);
                        } else {
                            // Added by Paul Balila for special characters.
                            $html .= '<p id="' . $contentID . '">' . html_entity_decode($name) . $fileInfo . '</p>';
                            if ($contentDetail[static::PUBLIC_DESCRIPTION]) {
                                $html .= '<div>' . $contentDetail[static::PUBLIC_DESCRIPTION] . '</div><br>';
                            }
                        }
                    }

                }
                $html .= '</li>';
                continue;
            }

            if (!$u->isLoggedIn() && ($contentAccess === static::LOGIN_ONLY ||
                    $contentAccess === static::SUBSCRIPTION || $contentDetail[static::CONTENT_DEMO] === 'Y')) {
                // Check content heading visibility
                if ($contentHeadingVisibility === 'Public' || $contentDetail[static::CONTENT_DEMO] === 'Y') {
                    // Check content detail visibility
                    if ($contentDetail[static::VISIBILITY] === 'Public') {
                        $html .= $this->createListItem($contentID, $contentDetail);
                    } else {
                        // Added by Paul Balila for special characters.
                        $html .= '<p id="' . $contentID . '">' . html_entity_decode($name) . $fileInfo . '</p>';
                        if ($contentDetail[static::PUBLIC_DESCRIPTION]) {
                            $html .= '<div>' . $contentDetail[static::PUBLIC_DESCRIPTION] . '</div><br>';
                        }
                    }
                }
                $html .= '</li>';
                continue;
            }
        }

        $html .= '</ul>';

        return $html;
    }

    /**
     * ANZGO-3467 Added by John Renzo S. Sunico
     * Login only access type
     * Student can access student tab
     * Teacher can access student and teacher tab
     * @param $userGroup
     * @param $userTypeRestriction
     * @return bool
     */
    protected function hasLoginOnlyAccess($userGroup, $userTypeRestriction)
    {
        $u = new User();

        return $u->isLoggedIn() && ($userGroup >= $userTypeRestriction);
    }

    /**
     * @param $userGroup
     * @param $contentAccess
     * @param $userTypeRestriction
     * @param $tabAccess
     * @param $tabID
     * @param $hmID
     * @return bool
     */
    protected function checkDetailAccessRight(
        $userGroup,
        $contentAccess,
        $userTypeRestriction,
        $tabAccess,
        $tabID,
        $hmID
    )
    {

        $u = new User();

        switch ($contentAccess) {
            case 'Free':
                $access = true;
                break;
            case 'Login only':
                $access = $u->isLoggedIn();
                $access = $access && ($userGroup >= $userTypeRestriction);
                break;
            case 'Subscription':
                $access = ($u->isLoggedIn() && $tabAccess == 'Y');
                $access = $access && ($userGroup >= $userTypeRestriction);
                break;
            default:
                $access = false;
        }

        if ($hmID > 0) {
            return $access;
        }

        // Check for content detail permissions.
        $overrideAccess = CupGoTabContent::checkContentDetailAccess($tabID);

        if (!$access && $contentAccess != 'Free') {
            $access = $overrideAccess;
        }

        // user is admin so give all access
        if ($this->isAdmin) {
            $access = true;
        }

        return $access;
    }

    public function getTabImage($tabIconID)
    {
        if ($tabIconID) {
            $relativePath = DIR_REL;

            return <<<tabImage
                <img
                    class="tab-icon-class"
                    src="$relativePath/files/cup_content/images/formats/$tabIconID" />
tabImage;
        }

        return '';
    }

    // Modified by Paul Balila
    private function checkDemoOnly($demoOnly, $contentAccess, $tabSubscription)
    {
        global $u;
        $showContent = true;
        if ($demoOnly === 'Y') {
            if ($this->isAdmin || ($contentAccess == static::SUBSCRIPTION && $tabSubscription == 'Y')) {
                return false;
            }
            if (($u->isLoggedIn() && ($contentAccess === static::LOGIN_ONLY || $contentAccess == 'Free'))) {
                return false;
            }
        }
        return $showContent;
    }

    private function loginHtml()
    {
        $v = View::getInstance();

        $goLoginUrl = $v->url('/go/login');

        $html = '
        <div class="row-login" >
        <div class="row text-center">
            <div class="col-lg-8">';
        if ($this->tab_go_custom_access_message) {
            $html .= $this->tab_go_custom_access_message;
        } else {
            $html .= '<div>
                    To access your resources Log in to or create your Cambridge GO account.
                </div><br />
                <div>
                    Activate your resources by entering the 16-character access code found in the sealed pocket card,
                    supplied by email or in selected print textbooks.
                </div>';
        }
        $html .= '</div>
            <div class="col-lg-4">
                <div class="col-lg-10 col-lg-offset-1 col-md-4 col-md-offset-4 col-sm-4 col-sm-offset-4 col-xs-12">';
        $html .= '<div style="text-align:left;font-size: 16px;" ';
        $html .= 'class="btn btn-success btn-block btn-black-text front-ajax-btn" href="' . $goLoginUrl . '">
                        <svg>
                        <use xlink:href="#icon-login"/>
                        </svg>
                        <span class="svg-text">Login</span>
                    </div>
                </div>
            </div>
        </div>
    </div>';

        return $html;
    }

    private function activateHtml()
    {
        $v = View::getInstance();

        $activateUrl = $v->url('activate');

        $html = '
        <div class="row-login" >
        <div class="row text-center">
            <div class="col-lg-12">';
        $html .= '<div>
                    Activate your resources by entering the 16-character access code found in the sealed pocket card,
                    supplied by email or in selected print textbooks.
                </div><br />
                <div>
                    <a class="btn btn-lg btn-info btn-block front-ajax-btn" href="' . $activateUrl . '" >ACTIVATE</a>
                </div>
            </div>
        </div>
    </div>';

        return $html;
    }

    private function createModal($contentID, $title, $content, $width, $height)
    {

        $style = "style='width:$width;height:$height;'";

        $html = '<div id="product_modal_' . $contentID . '" class="modal fade" role="dialog" style="' . $style . '">
        <div class="modal-dialog">
          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
                <h4 id="generalModalLabel" class="modal-title">' . $title . '</h4>
            </div>
            <div class="modal-body">' . urldecode($content) . '</div>
            </div>
        </div>
    </div>';
        return $html;
    }

    private function createListItem($contentID, $contentDetail)
    {
        $html = '';

        $url = $contentDetail['URL'];

        $name = $this->removeSpecialCharacters(html_entity_decode($contentDetail['Public_Name']));

        $description = $contentDetail[static::PUBLIC_DESCRIPTION];

        // ANZUAT-120
        $fileInfo = $contentDetail['FileInfo'] != '' ? ' [' . $contentDetail['FileInfo'] . ']' : '';

        $typeId = $contentDetail['TypeID'];

        $contentDetailId = $contentDetail['ID'];

        $windowBehaviour = $contentDetail['WindowBehaviour'] == 'New' ? 'target="_blank"' : '';

        //file content type
        if ($typeId == 1005) {

            $html .= '<p><a class="content-file-download" href="' . $contentDetailId . '">';
            $html .= $name . '</a>';
            // ANZUAT-120
            $html .= $fileInfo;
            if ($description) {
                $html .= '<div>' . $description . '</div><br>';
            }
            $html .= '</p>';
        } elseif ($typeId == 1001) {
            //link content type
            $html .= $typeId . '<p id="' . $contentDetailId . '">';

            $html .= '<b>' . $name . '</b></br>';
            // Modified by Paul Balila
            // We add the urldecode just in case the URL has been encoded.
            // If the URL is in its human form, it will return the same "humanized" URL.\]
            // ANZUAT-120
            $html .= '<a href="' . urldecode($url) . '" ' . $windowBehaviour . ' >' . urldecode($url) . '</a>';
            $html .= $fileInfo . '</br>';
            if ($description) {
                $html .= '<span>' . $description . '</span>';
            }
            $html .= '</p>';
        } else {
            //html content type
            $html .= '<p id="' . $contentDetailId . '">';
            // ANZUAT-120
            $html .= '<a href="#" data-toggle="modal" data-target="#product_modal_' . $contentDetailId . '">';
            $html .= $name . '</a>' . $fileInfo . '</p>';
            $html .= $this->createModal(
                $contentDetailId,
                $name,
                $contentDetail['HTML_Content'],
                $contentDetail['WindowWidth'],
                $contentDetail['WindowHeight']
            );
            if ($description) {
                $html .= '<div>' . $description . '</div><br>';
            }
        }
        return $html;
    }

    public function removeSpecialCharacters($text)
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
        // elipsis
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

        $text = filter_var($text, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);

        return str_replace($find, $replace, $text);
    }

    // Modified by Paul Balila, ticket ANZGO-2846, 2016-09-07
    private function showNewContent($productId, $fullAccess, $column = 2)
    {
        $fullAccess = 1;
        $redirect = new CupGoRedirect();

        $redirectsData = $redirect->getRedirectByEpubName($productId);

        $html = '';
        if ($fullAccess > 0) {
            $html = '';
            foreach ($redirectsData as $chapter => $contents) {
                $html .= '<p style="font-weight:bold">' . $chapter . '</p>';

                foreach ($contents as $content) {
                    $url = filter_var($content['url'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
                    $title = filter_var($content['title'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
                    $notes = filter_var($content['notes'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);

                    $html .= '<div style="margin-bottom:10px;margin-top:10px;padding-left:30px;word-break:break-all;">';
                    $html .= '<strong>' . $title . '</strong>';
                    $html .= '<br>';
                    $html .= '<a target="_blank" href="' . $url . '" class="redirector-links">' . $url . '</a>';

                    if ($data_row['notes'] != '') {
                        $html .= '<br>';
                        $html .= $notes;
                    }

                    $html .= '</div>';
                }
            }
        }

        return $html;
    }

    /**
     * Handles the display of expiration of tabs.
     * @author: Ariel Tabag
     * @editedBy: Paul Balila
     * @param: string
     * @return: array
     */
    private function formatExpiration($type, $startDate, $endDate, $duration, $usubCreationDate, $usubEndDate)
    {
        $expirationMessage = '';

        $today = strtotime(date("Y/m/d"));

        if ($type == 'start-end') {

            $daysLeft = strtotime($endDate) - $today;

            $date = date_create_from_format('Y-m-d H:i:s', $startDate);

            $startDate = date_format($date, "jS \of F Y");

            $date = date_create_from_format('Y-m-d H:i:s', $endDate);

            $endDate = date_format($date, "jS \of F Y");

            $expirationMessage = "Valid until <strong>$endDate</strong>";
        } else {
            if ($type == 'duration') {

                $date = date_create_from_format('Y-m-d H:i:s', $usubCreationDate);
                $durationstart = date_format($date, "jS \of F Y");

                if ($duration > 0) {

                    if ($duration > 1) {
                        $daydisp = 'days';
                    } else {
                        $daydisp = 'day';
                    }

                    $expirationMessage = "Download within <strong>$duration</strong> $daydisp ";
                    $expirationMessage .= "from <strong>$durationstart</strong>";

                    // ANZUAT-143
                    $daysLeft = strtotime($usubCreationDate) - (strtotime('-' . $duration . 'days', $today));
                } else {

                    $expirationMessage = "This resource will not expire.";

                    // Added by Paul Balila for ANZGO-2692, 2016-09-19
                    // If duration 0 or Perpetual type, subtract Tab end date to now.
                    $daysLeft = (strtotime($usubEndDate) - strtotime($today));
                }
            } else {
                // ANZGO-3748 modified by mtanada 2018/07/03 added reactivation type
                if ($type == 'end-of-year' || $type == 'reactivation') {

                    if ($usubEndDate) {
                        $date = date_create_from_format('Y-m-d H:i:s', $usubEndDate);

                        $endOfYearEnd = date_format($date, "jS \of F Y");

                        $expirationMessage = "Valid until <strong>$endOfYearEnd</strong>";
                    }

                    $daysLeft = strtotime($usubEndDate) - $today;
                } else {
                    $expirationMessage = "No information exists for your subscription";
                }
            }
        }

        // ANZUAT-143
        return array(
            'message' => $expirationMessage,
            'days_left' => round((($daysLeft / 24) / 60) / 60)
        );
    }

    // ANZGO-2867
    // This function should be called when a user is
    // Logged in, With access to this product
    public function checkBuildHMAccess($hmId, $endDate = false, $userType = null)
    {
        Loader::library('HotMaths/api');
        global $u;
        if (!$u->isLoggedIn()) {
            return false;
        }
        $params = array('userId' => $u->uID, 'hmProductId' => $hmId, 'response' => 'STRING');
        $api = new HotMathsApi($params);

        // ANZGO-3721 Maryjes Tanada 2018-05-24
        // If teacher account passes a student HMid to API, get & use teacher product version & modify the params
        $hmProduct = $api->getHmProduct();
        if ($u->uGroups[5] === 'Teacher' && $hmProduct->subscriberType === 'STUDENT') {
            $params = array('userId' => $u->uID, 'hmProductId' => $hmProduct->teacherProductId, 'response' => 'STRING');
            $api = new HotMathsApi($params);
        }

        // ANZGO-3720 modified by jbernardez mtanada 20180524
        $productName = strtolower($hmProduct->name);
        $addRunToggle = strpos($productName, 'sample') ? true : false;

        if ($api->getError() !== false) {
            $accessLink = $api->getError();
        } else {
            // ANZGO-3688 modified by jbernardez 20180412
            // added true parameter, as getHmAccessLink was modified
            // as to not always load HM API connection of products as this was added
            // on activation, and provisioning, but not on samples
            // ANZGO-3720 modified by jbernardez mtanada 20180524
            $accessLink = $api->getHmAccessLink($addRunToggle, $userType);
        }
        $this->hmError = $api->getError();

        return ($accessLink) ? $accessLink : false;
    }

    // ANZGO-2902
    private function createSampleLink($row)
    {
        global $u;
        // PREVIEW SAMPLE PRODUCT ONLY
        // this means that the HM product ID was attached
        // in the Products -> Tab
        if (!empty($row['HmID'])) {

            $hm_productid = $row['HmID'];
            $hm_test_url = (!empty($row['hm_test_url']) ? $row['hm_test_url'] : 'https://testportal.edjin.com');
            $hm_prod_url = (!empty($row['hm_prod_url']) ? $row['hm_prod_url'] : 'https://hotmaths.cambridge.edu.au');

            // NOTE: THIS IS FOR PREVIEW HM PRODUCT ONLY
            // check if this is a Preview Product
            // Preview Product is identified in CMS_Tabs Table
            if (isset($hm_productid) && !empty($hm_productid) && !empty($hm_test_url) || !empty($hm_prod_url)) {

                //1 check if user is in HotMaths Table
                $hm_user_exist = $hmHelper->checkEdjinUser();

                if (isset($hm_user_exist->userId) && $hm_user_exist->userId !== "") {
                    // add the user to the product to gain access
                    $hmAdded = $hmHelper->addUserToHMProduct($hm_user_exist->userId, $hm_productid);
                } else {
                    $hmUserCreated = $hmHelper->createEdjinUser($hm_productid, $userID);
                    if (isset($hmUserCreated->userId) && $hmUserCreated->userId !== "") {
                        // add the user to the product to gain access
                        $hmAdded = $hmHelper->addUserToHMProduct($hmUserCreated->userId, $hm_productid);
                    }
                }

                // user has been give access to HM product
                // finally we get his access token
                if ($hmAdded) {
                    $userInHotMath = checkUserInHotMath($userID);
                    $authorizationToken = $userInHotMath['authorizationToken'];
                    $brandCode = $userInHotMath['brandCodes'];
                    $previewUrl = $row['hm_prod_url'];
                    $previewUrl .= "/cambridgeLogin?brandCode=$brandCode&access_token=$authorizationToken";
                    $previewLink = "<span id='hm_url' style='display:none'><a href='$previewUrl' target='_blank'>";
                    $previewLink .= "Preview a Sample</a></span>";
                    return $previewLink;
                }
            }
        }
    }

    public function convertSlashes($string)
    {
        return str_replace('\\', '/', $string);
    }
}
