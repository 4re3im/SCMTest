<!-- TODO: add advanced filter for the no of days and user type -->
<style>
    .container, .navbar-fixed-top .container, .navbar-fixed-bottom .container {
        width: 1260px;
    }
</style>
<div class="advanced-filter"></div>

<!-- ANZGO-3708 Modified by M.Tanada 05/03/2018 for teacher list -->
<?php if (count($teacherList) > 0): ?>
<table id="ccm-product-list" class="ccm-results-list" cellspacing="0" cellpadding="0" border="0">
    <thead>
    <tr>
        <th><?php echo t('Creation Date') ?></th>
        <th><?php echo t('Full Name') ?></th>
        <th><?php echo t('Title') ?></th>
        <th><?php echo t('Position') ?></a></th>
        <th><?php echo t('Email') ?></th>
        <th><?php echo t('Phone Number') ?></th>
        <th><?php echo t('State') ?></th>
        <th><?php echo t('School') ?></a></th>
        <th><?php echo t('Subjects Taught') ?></th>
        <!--
        <th><?php echo t('Promotion by Email') ?></th>
        <th><?php echo t('Promotion by Regular Post') ?></th>
        -->
        <!-- ANZGO-3678 added by jbernardez 20180404 -->
        <th><?php echo t('SalesForce') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php $userAttributeKey = new UserAttributeKey(); ?>
    <?php if (isset($teacherList) && !empty($teacherList)): ?>
        <?php foreach ($teacherList as $teacher): ?>
            <?php $userAttributes = $userAttributeKey->getAttributes($teacher->uID); ?>
            <?php 
            // ANZGO-3678 added by jbernardez 20180406
            // go through the row if no flag for hiding
            if (($userAttributes->getAttribute('uHideSalesForce') == '') ||
                ($userAttributes->getAttribute('uHideSalesForce') == 0)) { ?>
            <tr class="ccm-list-record">
                <td onclick="location.href='<?php echo $this->action("search", $teacher->uID); ?>'">
                    <?php echo date_format(date_create($teacher->uDateAdded), 'jS M Y H:i:s'); ?></td>
                <td onclick="location.href='<?php echo $this->action("search", $teacher->uID); ?>'">
                    <?php echo $userAttributes->getAttribute('uFirstName') . ' ' .
                        $userAttributes->getAttribute('uLastName'); ?></td>
                <td onclick="location.href='<?php echo $this->action("search", $teacher->uID); ?>'">
                    <?php echo $userAttributes->getAttribute('uPositionTitle'); ?></td>
                <td onclick="location.href='<?php echo $this->action("search", $teacher->uID); ?>'">
                    <?php echo $userAttributes->getAttribute('uPositionType'); ?></td>
                <td onclick="location.href='<?php echo $this->action("search", $teacher->uID); ?>'">
                    <?php echo $teacher->getEmail(); ?></td>
                <td onclick="location.href='<?php echo $this->action("search", $teacher->uID); ?>'">
                    <?php echo $userAttributes->getAttribute('uSchoolPhoneNumber'); ?></td>
                <td onclick="location.href='<?php echo $this->action("search", $teacher->uID); ?>'">
                    <?php
                    switch (trim($userAttributes->getAttribute('uCountry'))) {
                        case
                        "New Zealand":
                            echo trim($userAttributes->getAttribute('uStateNZ'));
                            break;
                        case "Australia":
                            echo trim($userAttributes->getAttribute('uStateAU'));
                            break;
                        case "Canada":
                            echo trim($userAttributes->getAttribute('uStateCA'));
                            break;
                        case "United States":
                            echo trim($userAttributes->getAttribute('uStateUS'));
                            break;
                        default:
                            echo trim($userAttributes->getAttribute('uState'));
                            break;
                    }
                    ?>
                </td>
                <td onclick="location.href='<?php echo $this->action("search", $teacher->uID); ?>'">
                    <?php echo $userAttributes->getAttribute('uSchoolName'); ?></td>
                <td onclick="location.href='<?php echo $this->action("search", $teacher->uID); ?>'">
                    <?php echo $userAttributes->getAttribute('uSubjectsTaught'); ?></td>
                <td style="text-align:center">
                    <?php $checked = !empty($teacher->hasSalesForceID()) ? "checked='checked'" : ''; ?>
                    <!-- input type="checkbox" class="checkbox" disabled="disabled" <?php echo $checked ?> /-->
                    <!-- ANZGO-3678 added by jbernardez 20180404 -->
                    <?php
                    $uDisable = '';
                    if ($userAttributes->getAttribute('uSalesForceID') != '') {
                        $uDisable = 'disabled="disabled"';
                    }
                    $hDisable = '';
                    if (($userAttributes->getAttribute('uHideSalesForce') != '') &&
                        (($userAttributes->getAttribute('uHideSalesForce') == 1))) {
                        $hDisable = 'disabled="disabled"';
                    }
                    ?>
                    <div style="width: 120px;">
                        <input id="update<?php echo $teacher->uID; ?>" type="button" name="view"
                               class="btn primary sfupdate" value="Update" <?php echo $uDisable; ?>
                               onclick="sfUpdate(<?php echo $teacher->uID; ?>)"/>

                        <input id="hide<?php echo $teacher->uID; ?>" type="button" name="view"
                               class="btn primary sfhide" value="Hide" <?php echo $hDisable; ?>
                               onclick="sfHide(<?php echo $teacher->uID; ?>)"/>
                    </div>
                </td>
            </tr>
            <?php } ?>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="3">
        </td>
    </tr>
    <tr>

    </tr>
    </tfoot>
</table>
<?php else: ?>
    <div id="ccm-list-none"><?php echo t('No Teachers found.') ?></div>
<?php endif; ?>