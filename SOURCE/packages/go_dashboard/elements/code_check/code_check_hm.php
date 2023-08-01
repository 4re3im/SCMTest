<?php

/**
 * ANZGO-3500 Added by Shane Camus 10/02/2017
 * Separate code check element for HM Products
 */

defined('C5_EXECUTE') || die(_("Access Denied."));

?>

<style>

    hr {
        margin: 3px 0px;
        padding: 0px;
        background: none;
        color: none;
        height: 0px;
        border:  none;
        border-bottom: 1px solid #999;
    }

    .panel {
        border:  1px solid #ccc;
        padding: 0px 10px 10px 10px;
        margin:  0px 5px 5px 0px;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        border-radius: 5px 5px 5px 5px;
    }

    .panel p.header {
        padding: 5px 0px;
        font-size: 14px;
        margin: 8px 5px;
        font-weight: bold;
        border-bottom:  1px solid #333;
    }

    .code-message {
        color:white;
        padding:20px 30px;
        font-size:  18px;
        width: 200px;
        text-align: center;
        float:  right;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        border-radius: 5px 5px 5px 5px;
    }

    .code-message-red {
        background-color: red;
    }

    .code-message-green{
        background-color: #2da33f;
    }

    .code-status {
        background-color:#e3e3e3;
        font-size:16px;
        padding:10px;
        text-align: center;
        -moz-border-radius: 10px;
        -webkit-border-radius: 10px;
        border-radius: 10px 10px 10px 10px;
    }

    .code-status p {
        padding:  5px 0px;
        margin: 0px;
    }

    table#tbl {
        margin: 11px;
        width: 98%;
    }

    table#tbl tr {
        padding:3px 0;
    }

    table#tbl td {
        padding:3px 0;
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
        height: 200px;
        width: 49%;
    }

    #container2 .panel {
        display: inline-block;
        min-height: 180px;
        /*width: 32.4%;*/
    }

    @media print{
        body * { visibility: hidden; }
        .ccm-pane * { visibility: visible; }
        .ccm-pane { position: absolute; top: 5px; left: 5px; }
        #searchform * { display: none; }
        #container .panel {
            display: inline-block;
            min-height: 250px;
            max-width: 48%;
            vertical-align:top;

        }
        #container2 .panel {
            display: inline-block;
            min-height: 180px;
            max-width: 35%;
            vertical-align:top;
        }
        .releaseCode, .releaseCode *, .code-action, .code-action * { display: none; }
    }

</style>

<?php

    $today = date('Y-m-d H:i:s');
    $serverName = $_SERVER['HTTP_HOST'];

    if (strpos($serverName, 'localhost') > 0 ||
        strpos($serverName, 'dev') > 0 ||
        strpos($serverName, 'uat') > 0 ||
        strpos($serverName, 'testdeploy') > 0 ||
        strpos($serverName, 'local') > 0) {
        $hmURL = "https://testportal.edjin.com/";
    } else {
        $hmURL = "https://www.hotmaths.com.au";
    }

    $accessCode = $accessCodeDetails->code;
    $title  = $accessCodeDetails->productName;
    $isbn = $accessCodeDetails->isbn;
    $usageMax = $accessCodeDetails->maxActivations + $accessCodeDetails->maxReactivations;
    $usageCount = $usageMax - ($accessCodeDetails->activationsRemaining + $accessCodeDetails->reactivationsRemaining);
    $isFound = !isset($accessCodeDetails->success);
    $isCancelled = $accessCodeDetails->cancelled;
    $subscriptionPeriod = $accessCodeDetails->subscriptionMonths . ' months';
    $canBeActivated = true;

    if ($usageCount != 0) {
        $index = $usageCount - 1;
        $userID = $accessCodeDetails->activationCodeUses[$index]->userId;
        $firstName = $accessCodeDetails->activationCodeUses[$index]->firstName;
        $lastName = $accessCodeDetails->activationCodeUses[$index]->lastName;
        $fullName = $firstName . ' ' . $lastName;
        $email = $accessCodeDetails->activationCodeUses[$index]->email;
        $startDate = $accessCodeDetails->activationCodeUses[$index]->startDate . ' 00:00:00';
        $endDate = $accessCodeDetails->activationCodeUses[$index]->endDate . ' 24:00:00';
        $canBeActivated = $endDate < $today;
    }

?>

<hr />

<?php if ($isFound) { ?>
<form action="<?php echo $hmURL ?>">
<table id="tbl">
    <tr>
        <td>
            <div style="font-size:20px;font-weight:bold;margin-bottom:10px"><?php echo $accessCode ?></div>
            <div style="font-size:16px;margin-bottom:10px"><?php echo $title ?></div>
            <div style="font-size:16px;margin-bottom:10px"><?php echo $isbn ?></div>
        </td>
        <td>
            <div style="float: right" class="inline">
                <input
                        style="margin: 10px"
                        type="submit"
                        class="btn btn-large primary"
                        value="<?php echo t('Visit HOTmaths Admin')?>"
                />
            <?php if ($usageMax != $usageCount && !$isCancelled && $canBeActivated) { ?>
                <div class="code-message code-message-green">Can be used</div>
            <?php } else { ?>
                <div class="code-message code-message-red">Cannot be used</div>
            <?php } ?>
            </div>
        </td>
    </tr>
</table>
</form>
<?php } ?>

<div style="clear:both"></div>

<div class='code-status'>
    <?php echo $status ?>
</div>

<div style="clear:both"></div>

<?php if ($isFound) { ?>
<div id="container">
    <div class='panel'>
        <p class='header'><strong>Currently activated by </strong></p>
        <table id="tbl">
            <tr><td id="hdr">UserID</td><td id="dtl"><?php echo !$canBeActivated ? $userID : '' ?></td></tr>
            <tr><td id="hdr">Full Name</td><td id="dtl"><?php echo !$canBeActivated ? $fullName : '' ?></td></tr>
            <tr><td id="hdr">Email</td><td id="dtl"><?php echo !$canBeActivated ? $email : '' ?></td></tr>
            <tr><td id="hdr">Date Activated</td><td id="dtl"><?php echo !$canBeActivated ? $startDate : '' ?></td></tr>
            <tr><td id="hdr">End Date</td><td id="dtl"><?php echo !$canBeActivated ? $endDate : '' ?></td></tr>
            <tr><td id="hdr">Active</td><td id="dtl">
                    <?php echo !$canBeActivated ? ($isCancelled ? 'N' : 'Y') : '' ?>
                </td>
            </tr>
        </table>
    </div>

    <div class='panel'>
        <p class='header'><strong>Code information</strong><span style='padding-left:10px'>
        </p>
        <table id="tbl">
            <tr>
                <td id="hdr">Active</td>
                <td id="dtl"><?php echo $isCancelled ? 'N' : 'Y' ?></td>
            </tr>
            <tr>
                <td id="hdr">Subscription period</td>
                <td id="dtl"><?php echo $subscriptionPeriod ?></td>
            </tr>
            <tr>
                <td id="hdr">Number of activations available</td>
                <td id="dtl"><?php echo $usageMax; ?></td>
            </tr>
            <tr>
                <td id="hdr">Number times activated</td>
                <td id="dtl"><?php echo $usageCount; ?></td>
            </tr>
            <tr>
                <td id="hdr">Usable</td>
                <td id="dtl">
                    <?php echo ($canBeActivated && !$isCancelled && ($usageCount!=$usageMax)) ? 'Y' : 'N' ?>
                </td>
            </tr>
        </table>
    </div>
</div>
<?php } ?>
