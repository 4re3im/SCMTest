<?php

/**
 * Provisioned Users Table
 * ANZGO-3595 Added by Shane Camus 01/25/2018
 */
?>

<style>
    div.ccm-pagination span.numbers {
        padding: 3px 8px;
    }
    .container, .navbar-fixed-top .container, .navbar-fixed-bottom .container {
        width: 1260px;
    }
</style>
<div class='advanced-filter'></div>
<table id='ccm-product-list' class='ccm-results-list' cellspacing='0' cellpadding='0' border='0'>
    <thead>
    <tr>
        <th><?php echo t('Creation Date') ?></th>
        <th><?php echo t('First Name') ?></th>
        <th><?php echo t('Last Name') ?></th>
        <th><?php echo t('Email') ?></th>
        <th><?php echo t('User Type') ?></th>
        <th><?php echo t('Status') ?></th>
        <th><?php echo t('Remarks') ?></th>
        <th><?php echo t('File Uploaded') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php if (isset($provisionedUsersList) && !empty($provisionedUsersList)): ?>
        <?php foreach ($provisionedUsersList as $provisionedUser): ?>
            <tr class='ccm-list-record'
                onclick="location.href='<?php echo $this->action('search', $provisionedUser->uID); ?>'">
                <td><?php echo date_format(date_create($provisionedUser->DateModified), 'jS M g:i'); ?></td>
                <td><?php echo $provisionedUser->FirstName ?></td>
                <td><?php echo $provisionedUser->LastName ?></td>
                <td><?php echo $provisionedUser->Email ?></td>
                <td><?php echo $provisionedUser->Type ?></td>
                <td><?php echo $provisionedUser->Status ?></td>
                <td><?php echo $provisionedUser->Remarks ?></td>
                <td><?php echo $provisionedUser->FileName ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
<div class='ccm-pane-footer'>
    <?php echo $pagination; ?>
</div>
