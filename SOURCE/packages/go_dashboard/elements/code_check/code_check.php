<?php defined('C5_EXECUTE') || die(_("Access Denied.")); ?>
<style>
    hr {
        margin: 3px 0px;
        padding: 0px;
        background: none;
        color: none;
        height: 0px;
        border: none;
        border-bottom: 1px solid #999;
    }

    .panel {
        border: 1px solid #ccc;
        padding: 0px 10px 10px 10px;
        margin: 0px 5px 5px 0px;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        border-radius: 5px 5px 5px 5px;
    }

    .panel p.header {
        padding: 5px 0px;
        font-size: 14px;
        margin: 8px 5px;
        font-weight: bold;
        border-bottom: 1px solid #333;
    }

    .code-message {
        color: white;
        padding: 20px 30px;
        font-size: 18px;
        width: 200px;
        text-align: center;
        float: right;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        border-radius: 5px 5px 5px 5px;
    }

    .code-message-red {
        background-color: red;
    }

    .code-message-green {
        background-color: #2da33f;
    }

    .code-status {
        background-color: #e3e3e3;
        font-size: 16px;
        padding: 10px;
        text-align: center;
        -moz-border-radius: 10px;
        -webkit-border-radius: 10px;
        border-radius: 10px 10px 10px 10px;
    }

    .code-status p {
        padding: 5px 0px;
        margin: 0px;
    }

    .cls-code-error {
        background-color: red;
        color: white;
    }

    .cellRight {
        text-align: right;
    }

    .releaseCode {
        color: red;
    }

    table#tbl {
        margin: 11px;
        width: 98%;
    }

    table#tbl tr {
        padding: 3px 0;
    }

    table#tbl td {
        padding: 3px 0;
    }

    table#tbl td#hdr {
        font-weight: bold;
        font-size: 12px;
    }

    table#tbl td#dtl {
        padding-left: 5px;
        font-size: 12px;
    }

    #container {
        width: 100%;
        margin: 5px;
    }

    #container .panel {
        display: inline-block;
        height: 220px;
        width: 49%;
    }

    #container2 {
        width: 100%;
        margin: 5px;
    }

    #container2 .panel {
        display: inline-block;
        min-height: 180px;
        /*width: 32.4%;*/
    }

    @media print {
        body * {
            visibility: hidden;
        }

        .ccm-pane * {
            visibility: visible;
        }

        .ccm-pane {
            position: absolute;
            top: 5px;
            left: 5px;
        }

        #searchform * {
            display: none;
        }

        #container .panel {
            display: inline-block;
            min-height: 250px;
            max-width: 48%;
            vertical-align: top;

        }

        #container2 .panel {
            display: inline-block;
            min-height: 180px;
            max-width: 35%;
            vertical-align: top;
        }

        .releaseCode, .releaseCode *, .code-action, .code-action * {
            display: none;
        }
    }
</style>
<?php

$accessCode = $accessCodeDetails;

$releaseCodeLink = '';
$redeemCodeLink = '';

if ($accessCode->IsReleasable) {
    $releaseCodeLink =
        "<span style='padding-left:10px'>
                <a
                    href='#'
                    data-toggle='modal'
                    data-target='#releaseModal'
                    class='code-action'
                    id='release'>
                    [Release code]
                </a>
        </span>";
}

if ($accessCode->is_active && $accessCode->IsUsable) {
    $redeemCodeLink =
        "<span style='padding-left:10px;'>
                <a
                    href='#'
                    data-toggle='modal'
                    data-target='#redeemModal'
                    class='code-action'
                    id='redeem'>
                    [Redeem Code]
                </a>
        </span>";
}

if ($accessCode->is_active) {
    $toggleCodeLinkText = "Deactivate";
} else {
    $toggleCodeLinkText = "Activate";
}
?>
<hr/>
<?php if ($accessCode->entitlement->Active === 'Y'): ?>
    <?php $product = $accessCode->entitlement->product()->fetch(); ?>
    <table id="tbl">
        <tr>
            <td>
                <div style="font-size:20px;font-weight:bold;margin-bottom:10px">
                    <?php echo $accessCode->proof ?>
                </div>
                <div style="font-size:16px;margin-bottom:10px">
                    <?php echo $product->CMS_Name ?>
                </div>
                <div style="font-size:16px;margin-bottom:10px">
                    <?php
                    if ($product->ISBN_13 || $product->Name) {
                        echo $product->ISBN_13 . "/" . $product->Name;
                    }
                    ?>
                </div>
            </td>
            <td>
                <?php if ($accessCode->IsUsable && $accessCode->is_active) { ?>
                    <div class="code-message code-message-green">Can be used
                    </div>
                <?php } else { ?>
                    <div class="code-message code-message-red">Cannot be used
                    </div>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td colspan='2'>
                <?php $message; ?>
            </td>
        </tr>
    </table>
<?php endif; ?>
<div style="clear:both"></div>
<?php if ($status) { ?>
    <div class='code-status'>
        <?php echo $status; ?>
    </div>
<?php } ?>
<?php if ($CLSAlerts !== null) { ?>
    <br>
    <div class='code-status cls-code-error'>
        <?php echo $CLSAlerts['error']; ?>
    </div>
<?php } ?>
<div style="clear:both"></div>
<?php if ($accessCode->entitlement->Active === 'Y'): ?>
    <div id="container">
        <div class='panel'>
            <p class='header'><strong>Currently activated by</strong></p>
            <?php
            $activatedBy = $accessCode->getLastActivation();
            $lastUserId = '';
            $lastFullName = '';
            $lastEmail = '';
            $lastDateActivated = '';
            $lastEndDate = '';
            $lastPurchaseType = '';
            $lastDaysRemaining = '';
            $lastIsActive = '';
            //CGO-201 Added by lmartinito 20230303
            $lastOID = '';
            $isSiteLicense = FALSE;
            $institution = '';

            //CGO-201 Added by lmartinito 20230303
            if ($accessCode->entitlement->entitlement_type_id === 2) {
                $isSiteLicense = TRUE;
            }

            if ($activatedBy && !$accessCode->released_at) {
                //CGO-201 Added/modified by lmartinito 20230306;
                $lastOID = $activatedBy->institution_id;
                Loader::library('gigya/datastore/GigyaInstitution');
                $gi = new GigyaInstitution();
                $institution = $gi->getByOID($lastOID);
                $institution = !$institution ? 'N/A' : $institution['data']['results'][0]['data']['name'];

                $userProfile = array();

                if ($isSiteLicense) {
                    if (array_key_exists('group_admin_id', $activatedBy->metadata)) {
                        $lastUserId = $activatedBy->metadata['group_admin_id'];
                        //GCAP-844 Added by machua 20200430
                        $userProfile = DashboardCodeCheckController::getUserProfile($lastUserId);
                    } else {
                        $lastUserId = $activatedBy->metadata['CreatedBy'];
                        $userProfile = DashboardCodeCheckController::getC5UserProfile($lastUserId);
                    }
                } else {
                    $lastUserId = $activatedBy->user_id;
                    //GCAP-844 Added by machua 20200430
                    $userProfile = DashboardCodeCheckController::getUserProfile($lastUserId);
                }
                
                if (empty($userProfile)) {
                    $lastFullName = 'N/A';
                    $lastEmail = 'N/A';
                } else {
                    $lastFullName = $userProfile['fullName'];
                    $lastEmail = $userProfile['email'];
                }

                $lastDateActivated = $activatedBy->activated_at;
                $lastDateActivated = $lastDateActivated->format('Y-m-d H:i:s');

                $lastEndDate = $activatedBy->ended_at->format('Y-m-d H:i:s');

                $lastPurchaseType = $activatedBy->PurchaseType;

                $lastDaysRemaining = $activatedBy->DaysRemaining;

                $lastIsActive = $activatedBy->isActive ? 'Y' : 'N';

            }
            ?>
            <table id="tbl">
                <tr>
                    <?php 
                    //CGO-201 Modified by lmartinito 20230303
                    if ($isSiteLicense): 
                    ?>
                    <td id="hdr">OID</td>
                    <td id="dtl"><?php echo $lastOID ?></td>
                    <?php else: ?>
                    <td id="hdr">UserID</td>
                    <td id="dtl"><?php echo $lastUserId ?></td>
                    <?php endif; ?>
                </tr>
                <?php if ($isSiteLicense): ?>
                <tr>
                    <td id="hdr">School Name</td>
                    <td id="dtl"><?php echo $institution ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td id="hdr">Full Name</td>
                    <td id="dtl">
                        <?php echo $lastFullName ?>
                    </td>
                </tr>
                <tr>
                    <td id="hdr">Email</td>
                    <td id="dtl"><?php echo $lastEmail ?></td>
                </tr>
                <tr>
                    <td id="hdr">Date Activated</td>
                    <td id="dtl"><?php echo $lastDateActivated ?></td>
                </tr>
                <tr>
                    <td id="hdr">End Date</td>
                    <td id="dtl"><?php echo $lastEndDate ?></td>
                </tr>
                <tr>
                    <td id="hdr">Purchase Type</td>
                    <td id="dtl"><?php echo $lastPurchaseType ?></td>
                </tr>
                <tr>
                    <td id="hdr">Days Remaining</td>
                    <td id="dtl"><?php echo $lastDaysRemaining ?></td>
                </tr>
                <tr>
                    <td id="hdr">Active</td>
                    <td id="dtl"><?php echo $lastIsActive ?></td>
                </tr>
            </table>
        </div>
        <div class='panel'>
            <p class='header'><strong>Code information</strong><span
                        style='padding-left:10px'>
            <a href='<?php
            echo $this->url(
                '/dashboard/code_check/toggleCode/' .
                $accessCode->id .
                '/' .
                trim($accessCode->proof))
            ?>'
               class='releaseCode'><?php echo "[$toggleCodeLinkText code]" ?></a></span>
                <?php echo $releaseCodeLink ?>
                <?php echo $redeemCodeLink ?>
            </p>
            <table id="tbl">
                <tr>
                    <td id="hdr">Date created</td>
                    <td id="dtl"><?php echo $accessCode->created_at->format('Y-m-d H:i:s') ?></td>
                </tr>
                <tr>
                    <td id="hdr">Active</td>
                    <td id="dtl">
                        <?php echo $accessCode->is_active ? 'Y' : 'N' ?>
                    </td>
                </tr>
                <tr>
                    <td id="hdr">Subscription period</td>
                    <td id="dtl"><?php echo
                            $accessCode
                                ->entitlement
                                ->entitlementType
                                ->name . " / " .
                            $accessCode
                                ->entitlement
                                ->Description
                        ?>
                    </td>
                </tr>
                <tr>
                    <td id="hdr">Number of activations available</td>
                    <td id="dtl"><?php echo $accessCode->limit ?></td>
                </tr>
                <tr>
                    <td id="hdr">Number times activated</td>
                    <td id="dtl"><?php echo count($accessCode->activations) ?></td>
                </tr>
                <tr>
                    <td id="hdr">Usable</td>
                    <td id="dtl"><?php echo $accessCode->IsUsable ? 'Y' : 'N' ?></td>
                </tr>
            </table>
        </div>
    </div>
    <div style="clear:both"></div>
    <div id="container2">
        <div class='panel' style="width:33%;">
            <p class='header'><strong>Previously activated by</strong></p>
            <?php foreach ($accessCode->activations as $activation):
                //CGO-201 Added/modified by lmartinito 20230306
                Loader::library('gigya/datastore/GigyaInstitution');
                $gi = new GigyaInstitution();
                $institution = $gi->getByOID($activation->institution_id);
                $institution = !$institution ? 'N/A' : $institution['data']['results'][0]['data']['name'];

                $userProfile = array();

                if ($isSiteLicense) {
                    if (array_key_exists('group_admin_id', $activation->metadata)) {
                        $userProfile = DashboardCodeCheckController::getUserProfile($activation->metadata['group_admin_id']);
                    } else {
                        $userProfile = DashboardCodeCheckController::getC5UserProfile($activation->metadata['CreatedBy']);
                    }
                } else {
                    //GCAP-844 Added by machua 20200430
                    //CGO-322 Modified by lmartinito 20230324
                    $userProfile = DashboardCodeCheckController::getUserProfile($activation->user_id);
                } 

                if (empty($userProfile)) {
                    $fullName = 'N/A';
                    $userEmail = 'N/A';
                } else {
                    $fullName = $userProfile['fullName'];
                    $userEmail = $userProfile['email'];
                }
            ?>
                <table id="tbl">
                    <tr>
                        <td id="hdr">Subscription Active</td>
                        <td id="dtl"><?php echo $activation->IsActive ? 'Y' : 'N' ?></td>
                    </tr>
                    <tr>
                        <?php
                        //CGO-201 Modified by lmartinito 20230303
                        if ($isSiteLicense):
                        ?>
                        <td id="hdr">OID</td>
                        <td id="dtl"><?php echo $activation->institution_id ?></td>
                        <?php else: ?>
                        <td id="hdr">UserID</td>
                        <td id="dtl"><?php echo $activation->user_id ?></td>
                        <?php endif; ?>
                    </tr>
                    <?php if ($isSiteLicense): ?>
                    <tr>
                        <td id="hdr">School Name</td>
                        <td id="dtl"><?php echo $institution ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td id="hdr">Name</td>
                        <td id="dtl">
                            <?php echo $fullName ?>
                        </td>
                    </tr>
                    <tr>
                        <td id="hdr">Email</td>
                        <td id="dtl"><?php echo $userEmail ?></td>
                    </tr>
                    <tr>
                        <td id="hdr">Date Activated</td>
                        <td id="dtl">
                            <?php
                            echo $activation->activated_at->format('Y-m-d H:i:s')
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td id="hdr">Purchase Type</td>
                        <td id="dtl"><?php echo $activation->PurchaseType ?></td>
                    </tr>
                    <tr>
                        <td id="hdr">Days Remaining</td>
                        <td id="dtl"><?php echo $activation->DaysRemaining ?></td>
                    </tr>
                </table>
            <?php endforeach; ?>
        </div>
        <div class='panel' style="width:39%;">
            <p class='header'><strong>Code activation errors</strong></p>
            <table id="tbl">
                <?php
                foreach ($codeErrors as $codeError) {
                    //GCAP-844 Added by machua 20200430
                    $userProfile = DashboardCodeCheckController::getUserProfile($codeError['UserID']); 
                    if (empty($userProfile)) {
                        $userProfile['firstName']  = 'N/A';
                        $userProfile['lastName']  = 'N/A';
                        $userProfile['email']  = 'N/A';
                    }
                    ?>
                    <tr>
                        <td width="80"><strong>First name</strong></td>
                        <td><?php echo $userProfile['firstName'] ?> </td>
                    </tr>
                    <tr>
                        <td width="80"><strong>Last name</strong></td>
                        <td><?php echo $userProfile['lastName'] ?></td>
                    </tr>
                    <tr>
                        <td width="80"><strong>Email</strong></td>
                        <td><?php echo $userProfile['email'] ?></td>
                    </tr>
                    <tr>
                        <td width="80"><strong>Error date</strong></td>
                        <td><?php echo $codeError['ErrorDate'] ?></td>
                    </tr>
                    <tr>
                        <td width="80">
                            <strong>Info</strong>
                        </td>
                        <td><?php echo $codeError['Info'] ?></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <hr style='margin: 5px 0'>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
        <div class='panel' style="width:25%;">
            <p class='header'><strong>Dates the code was released</strong></p>
            <?php
            // GCAP-875 Modified by machua 20200514 revert to old codes as admin info are in C5
            foreach ($previousReleaseDates as $data) { ?>
                <table cellpadding='0' cellspacing='0' border='0'>
                    <tr>
                        <td>Date released :</td>
                        <td><?php echo $data['ReleaseDate'] ?></td>
                    </tr>
                    <tr>
                        <td>Released by :</td>
                        <td><?php echo $data['ReleasedBy'] ?></td>
                    </tr>
        
                </table>
            <?php } ?>
        </div>
    </div>
<?php endif; ?>
