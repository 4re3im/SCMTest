<?php
/**
 * MyResourcesHub
 *
 * @author jsunico@cambridge.org
 */

Loader::helper('myresources', 'go_contents');

class MyResourcesHubHelper extends MyResourcesHelper
{
    public function formatDisplay($subscriptions)
    {
        $uh = new View();
        $html = '';

        $educationTitleURLTemp = "https://cambridge.edu.au/education/titles/";
        $educationFileURL = BASE_URL . "/files/cup_content/images/titles/";

        if (!$subscriptions) {
            return '';
        }

        $u = new User();

        foreach ($subscriptions as $titleId => $tabsSubscribed) {
            $singleTab = $tabsSubscribed[0];
            $expirationHTML = null;
            $authToken = $singleTab['authToken'];
            $brandCodes = $singleTab['brandCodes'];
            $daysLeft = max(array_column($tabsSubscribed, 'DaysRemaining'));
            $finalDaysLeft = $daysLeft;
            $isbn13 = $singleTab['isbn13'];
            $displayName = $singleTab['displayName'];
            $prettyURL = $singleTab['prettyUrl'];
            $image = $educationFileURL . $isbn13 . '_180.jpg';
            $educationTitleURL = $educationTitleURLTemp . $prettyURL;
            $source = $singleTab['Source'];
            $usID = $singleTab['UserSubscriptionID'];
            $hmId = $singleTab['HmID'];
            $resourceTitleID = $titleId;

            if ($source === static::HOTMATHS) {
                $authToken = $singleTab['authToken'];
                $brandCodes = $singleTab['brandCodes'];
                $finalDaysLeft = $singleTab['tokenExpiryDate'] > 0
                    ? $singleTab['tokenExpiryDate']
                    : 0;
            }

            $title = new CupContentTitle($titleId);

            $userSubscriptionIds = array_column(
                $tabsSubscribed,
                'UserSubscriptionID'
            );
            $userSubscriptionIds = array_unique($userSubscriptionIds);

            foreach ($userSubscriptionIds as $uSubscriptionId) {
                $tabSubscriptionData = array_filter(
                    $tabsSubscribed,
                    function ($tab) use ($uSubscriptionId) {
                        return $tab['UserSubscriptionID'] === $uSubscriptionId;
                    }
                );
                $tabSubscriptionData = array_pop($tabSubscriptionData);

                $isExpired = $tabSubscriptionData['DaysRemaining'];
                $isExpired = is_null($isExpired) ? 0 : $isExpired;
                $isExpired = $isExpired <= 0;
                $isHotMaths = $tabSubscriptionData['Source'] === static::HOTMATHS;
                $isDeactivated = !is_null($tabSubscriptionData['USubDateDeactivated']);

                if ($isExpired || $isHotMaths || $isDeactivated) {
                    continue;
                }

                $type = $tabSubscriptionData['Type'];
                $usCreationDate = $tabSubscriptionData['USubCreationDate'];
                $usEndDate = $tabSubscriptionData['USubEndDate'];
                $daysLeft = $tabSubscriptionData['DaysRemaining'];
                $saDuration = $tabSubscriptionData['Duration'];
                $description = $tabSubscriptionData['Description'];
                $accessCode = $tabSubscriptionData['AccessCode'];
                $tabNameLower = strtolower($tabSubscriptionData['Name']);

                $formatExpiration = $this->formatExpiration(
                    $type,
                    $usCreationDate,
                    $usEndDate,
                    $daysLeft,
                    $saDuration,
                    $tabNameLower
                );

                $expirationMessage = $formatExpiration['message'];
                $daysLeft = $formatExpiration['days_left'];

                if ($daysLeft > $finalDaysLeft) {
                    $finalDaysLeft = $daysLeft;
                }

                $expirationHTML .= '<p class="subscription-p"><label>' .
                    $tabSubscriptionData['Name'] . '</label></p>';

                if ($description) {
                    $expirationHTML .= '<div>' . $description . '</div>';
                }

                $accessCodeDisplay = $accessCode ? "($accessCode)" : '';

                if ($expirationMessage) {
                    $expirationHTML .= '<div>' . $expirationMessage . '&nbsp;' .
                        $accessCodeDisplay . '</div>';
                }
            }

            $dateDeactivatedList = array_column($tabsSubscribed,
                'USubDateDeactivated'
            );
            $dateDeactivatedList = array_filter(
                $dateDeactivatedList,
                function ($date) {
                    return $date === null;
                }
            );
            if (empty($dateDeactivatedList)) {
                $daysLeft = 0;
                $finalDaysLeft = 0;
            }

            $expiredClass = $finalDaysLeft <= 0 && $source == 'Go'
                ? 'expired'
                : '';

            $html .= "<div id='$resourceTitleID' ";
            $html .= "class='container-fluid container-bg-1 resources-container $expiredClass'><br /><br />";
            $html .= "<div class='row'>";
            $html .= "<div class='col-lg-12 col-nd-12 col-sm-12 col-xs-12'>";
            $html .= "<div class='container'>";
            $html .= "<div class='row'>";
            $html .= "<div class='col-lg-2 col-md-3 col-sm-3 col-xs-6'>";
            $html .= "<div class='book-wrap'>";

            if ($source == 'Go') {
                $url = $uh->url('/go/titles/' . $prettyURL);
                $html .= "<div class='load cover'><a href='$url'>";
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

            $titleContents = $source == 'Go'
                ? $this->addTabAccessToContent($title->getActiveTabs(),
                    $tabsSubscribed)
                : CupGoExternalUser::fetchByID($usID);

            $apiPath = null;
            $displayName = $this->formatProductDisplayName($displayName);
            if ($source == 'Go') {
                $url = $uh->url('/go/titles/' . $prettyURL);
                $html .= "<a href='$url'><h1>$displayName";
            } else {
                $hmApiLink = HOTMATHS_CONNECT;
                $apiPath = "https://$hmApiLink/cambridgeLogin?externalId=$titleContents->user_id";
                $apiPath .= "&access_token=$authToken&brandCode=$brandCodes";
                $html .= "<a href='$apiPath' target='_blank'><h1>$displayName";
            }

            if ($daysLeft <= 30 && $daysLeft > 0) {
                $html .= "<span class='go-resource-status'>EXPIRES SOON!</span>";
            }

            $html .= '</h1></a>';
            $html .= $expirationHTML . '</p>';

            if ($titleContents && $finalDaysLeft > 0 || $source == static::HOTMATHS) {
                $resourcesLinks = $this->sortResources(
                    $titleContents,
                    $hmId,
                    $apiPath,
                    $source
                );

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
                        // someone changed this, needs verification
                        $html .= "<li><a id='".$rlf[3]."' class='resource-tabs'>$rlf[1] </a></li>";
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

    private function addTabAccessToContent($titleContents, $tabsSubscribed)
    {
        $subscriptionTabs = array_map(function ($tabs) {
            return $tabs['TabID'];
        }, $tabsSubscribed);

        $contents = [];
        foreach ($titleContents as $titleContent) {
            $tabId = $titleContent['ID'];
            $tabAccess = in_array($tabId, $subscriptionTabs);
            $daysRemaining = array_filter($tabsSubscribed,
                function ($tab) use ($tabId) {
                return $tab['TabID'] == $tabId;
            });
            $daysRemaining = array_pop($daysRemaining);

            $daysRemaining = $daysRemaining['DaysRemaining'];
            $daysRemaining = !is_null($daysRemaining) ? $daysRemaining : 0;


            $titleContent['TabAccess'] = $tabAccess && $daysRemaining > 0;

            $contents[] = $titleContent;
        }

        return $contents;
    }

    // ANZGO-3897 modified by jdchavez 10/19/2018
    public function getHotmathsResourceLink($uId, $hmId, $groupType = null)
    {
        return "/go/myresources/toHotmaths/?productId=$hmId&group=$groupType";
    }

    // ANZGO-3897 added by jdchavez 10/19/2018
    public function getHotMathsRedirectUrl($uId, $hmId, $groupType = null)
    {
        Loader::library('HotMaths/api');
        $params = [
            'userId' => $uId,
            'hmProductId' => $hmId,
            'response' => 'STRING'
        ];

        $api = new HotMathsApi($params);

        if ($api !== false) {
            $hmProduct = $api->getHmProduct();
            if ($groupType === 'Teacher' && $hmProduct->subscriberType === 'STUDENT') {
                return $api->getHmAccessLinkModified($hmProduct->teacherProductId);
            }
            return $api->getHmAccessLinkModified();
        }
    }
}
