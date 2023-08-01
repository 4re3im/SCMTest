<?php

defined('C5_EXECUTE') || die(_("Access Denied."));
?>
<div class="panel">
    <form id="add-subscription"
            action="<?php echo $this->url('/dashboard/institution_management/review/add'); ?>"
            class="form-horizontal">
        <div class="ccm-pane-options-permanent-search" style="margin-top: 18px !important;">
            <div class="controls">
                <input type="text" name="subscription" id="subscription"
                        placeholder="Search Product" style="width:800px; !important"/>
                <input type="hidden" name="sa_id" id="sa_id"/>
                <input type="hidden" name="s_id" id="s_id"/>
                <input type="hidden" name="product_id" id="product_id"/>
                <input type="submit" class="btn primary"
                        value="<?php echo t('Add') ?>"/>
            </div>
        </div>
    </form>

    <div class="alert" id="subscription-alert" role="alert"
         style="display:none;"></div>
</div>

<div style="clear:both"></div>
<div class="panel-default">
    <p class='header' style="padding-bottom: 17px;"><strong>Institution Subscriptions</strong>
    <span style="float: right;">
        <input type="submit" class="btn primary submit" id="endDate" value="End Date" disabled="disabled"/>
    </span>
    <span style="float: right; padding-right: 3px;">
        <input type="submit" class="btn primary submit" id="archiveSub" value="Archive" disabled="disabled"/>
    </span>
    <span style="float: right; padding-right: 3px;">
        <input type="submit" class="btn primary submit" id="deactivate" value="Deactivate" disabled="disabled"/>
    </span>
    <span style="float: right; padding-right: 3px;">
        <input type="submit" class="btn primary submit activate" id="activate" value="Activate" disabled="disabled"/>
    </span>
    </p>
    <div class="usersubscription" id="subs">
        <table id="ccm-product-list" class="ccm-results-list" cellspacing="0"
               cellpadding="0" border="0">
            <thead>
            <tr>
                <th><input type="checkbox" id="checkAll"></th>
                <th>Subscription</th>
                <th>SubType</th>
                <th>Creation Date</th>
                <th>EndDate</th>
                <th>Duration</th>
                <th>Active</th>
                <th>AccessCode</th>
                <th>Days Remaining</th>
                <th>Purchase Type</th>
                <th>Added By</th>
            </tr>
            </thead>
            <tbody id="ccm-product-list-body">
            <?php if (isset($userSubscriptions) && !empty($userSubscriptions)): ?>
                <?php foreach ($userSubscriptions as $userSubscription): ?>
                    <?php
                    // SB-272 added by mabrigos to filter Archived subscriptions 
                    if ($userSubscription->Archive === "Y") { 
                        continue; 
                    } 
                    $creatorId = $userSubscription->CreatedBy;
                    $creatorInfo = UserInfo::getByID($creatorId);
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
                            <input type="checkbox" name="usID" value="<?php echo $userSubscription->id ?>">
                        </td>
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
                        <td style="white-space: nowrap;
                            <?php echo !$creatorInfo ? 'text-align: center' : '' ?>"
                        >
                            <?php echo $creatorName ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<div id="dialog-confirm" title="">
  <p id="confirmText"><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span></p>
</div>
<div id="dialog-endDate">
  <p id="confirmEndDateText"><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span></p>
  <p id="endDateText">Updated End Date: <input type="text" id="datepicker"></p>
</div>
