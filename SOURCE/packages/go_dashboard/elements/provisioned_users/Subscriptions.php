<?php

/**
 * Subscriptions Tab for Provisioned Users (PEAS Integrated)
 * ANZGO-3595 Added by Shane Camus 01/25/2018
 * HUB-127 Modified by Carl Lewi Godoy, 08/15/2018
 */

defined('C5_EXECUTE') or die("Access Denied.");

$th = Loader::helper('concrete/urls');
$url = $th->getToolsURL('autocomplete', 'go_dashboard');
$u = new User();
$currUser = $u->uID;
?>
<div class="panel">
    <table width="100%" cellpadding="5" cellspacing="5" border="1">
        <tr>
            <td align="left">
                <form id="add-subscription"
                      action="<?php echo $this->action('addUserSubscription') ?>">
                    <input type="text" name="subscription" id="subscription"
                           placeholder="Search Product"
                           search-url="<?php echo $url ?>"/>
                    <input type="hidden" name="sa_id" id="sa_id"/>
                    <input type="hidden" name="s_id" id="s_id"/>
                    <input type="hidden" name="product_id" id="product_id"/>
                    <input type="submit" class="btn primary"
                           value="<?php echo t('Add') ?>"/>
                </form>
            </td>
        </tr>
    </table>
    <div class="alert" id="subscription-alert" role="alert"
         style="display:none;"></div>
</div>
<div style="clear:both"></div>
<div class="panel-default">
    <p class='header'><strong>User Subscriptions</strong></p>
    <div class="usersubscription">
        <table id="ccm-product-list" class="ccm-results-list" cellspacing="0"
               cellpadding="0" border="0">
            <thead>
            <tr>
                <th>Subscription</th>
                <th>SubType</th>
                <th>Creation Date</th>
                <th>EndDate</th>
                <th>Duration</th>
                <th>Active</th>
                <th>AccessCode</th>
                <th>Days Remaining</th>
                <th>Purchase Type</th>
                <th></th>
            </tr>
            </thead>
            <tbody id="ccm-product-list-body">
            <?php if (isset($userSubscriptions) && !empty($userSubscriptions)): ?>
                <?php foreach ($userSubscriptions as $userSubscription): ?>
                    <?php
                    $creatorId = $userSubscription->CreatedBy;
                    $creatorInfo = UserInfo::getByID($creatorId);
                    if ($currUser !== $creatorId) {
                        continue;
                    }
                    $creatorName = '-';
                    if ($creatorInfo) {
                        $creatorName = $creatorInfo->getAttribute('uFirstName');
                        $creatorName .= ' ';
                        $creatorName .= $creatorInfo->getAttribute('uLastName');
                    }

                    $entitlement = $userSubscription
                        ->permission
                        ->entitlement;

                    $subscription = $userSubscription
                        ->permission
                        ->entitlement
                        ->product;
                    $subscriptionData = $subscription->metadata;

                    $entitlementType = $userSubscription
                        ->permission
                        ->entitlement
                        ->entitlementType;

                    $duration = $entitlement->Duration;

                    $accessCode = $userSubscription->permission->proof;
                    $accessCode = $accessCode ? $accessCode : '';

                    $daysRemaining = $userSubscription->daysRemaining;

                    $active = 'Y';
                    $isActivated = 'Deactivate';
                    if (!$daysRemaining || $userSubscription->DateDeactivated) {
                        $active = 'N';
                        $isActivated = 'Activate';
                    }
                    
                    ?>
                    <tr class="ccm-list-record">
                        <td>
                            <?php
                            echo $subscriptionData['CMS_Name'] . ' (' . $userSubscription->id . ')';
                            ?>
                        </td>
                        <td><?php echo $entitlement->Type ?></td>
                        <td>
                            <?php echo
                            $userSubscription->created_at->format('Y-m-d H:i:s')
                            ?>
                        </td>
                        <td>
                            <?php echo
                            $userSubscription->ended_at->format('Y-m-d H:i:s')
                            ?>
                        </td>
                        <td>
                            <?php echo $duration ?>
                        </td>
                        <td>
                            <?php echo $active ?>
                        </td>
                        <td>
                            <a href='/dashboard/code_check/<?php echo $accessCode ?>'>
                                <?php echo $accessCode ?>
                            </a>
                        </td>
                        <td><?php echo $daysRemaining ?></td>
                        <td><?php echo $userSubscription->PurchaseType ?></td>
                        <td>
                            <input type="button" name="view" class="btn primary"
                                   id="deactivate"
                                   onclick="toggleUserSubscription(<?php echo $userSubscription->id ?>)"
                                   value="<?php echo $isActivated; ?>"/>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="3">
                    <?php
                    if (isset($userSubscriptionPagination)) {
                        echo $userSubscriptionPagination;
                    }
                    ?>
                </td>
            </tr>
            <tr></tr>
            </tfoot>
        </table>
    </div>
</div>
<script type="text/javascript">

  function toggleUserSubscription(usid) {
        $.ajax({
            type: 'POST',
            data: '&usID=' + usid + '&userID=' +
            <?php echo $user[0]->uID ?> +'&is_ajax=yes&func=togglesubscriptionstatus',
            url: "<?php print str_replace('&', '&', $this->action('toggleUserSubscription')); ?>",
            success: function (data) {
                if (data.status === false) {
                    alert(data.message);
                    $('.subscription').val('');
                } else {
                    $('div.usersubscription').html(data);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $('.usersubscription').html(xhr.status + '<br/>' + thrownError);
            }
        });
    }

</script>
