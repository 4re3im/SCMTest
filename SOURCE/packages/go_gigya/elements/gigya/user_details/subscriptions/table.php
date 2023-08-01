<?php
defined('C5_EXECUTE') or die("Access Denied.");

if (count($subscriptions) > 0) { ?>
<table id="ccm-product-list" class="ccm-results-list table">
    <thead>
    <tr>
        <th>
            <input type="checkbox" id="checkAll">
        </th>
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
        <th></th>
    </tr>
    </thead>
    <tbody id="ccm-product-list-body">
    <?php foreach ($subscriptions as $subscription) {
        $isActivated = $subscription['Active'] === 'Y' ? 'Deactivate' : 'Activate';
        $creatorInfo = UserInfo::getByID($subscription['CreatedBy']);

        $creatorName = '-';
        if ($creatorInfo) {
            $creatorName = $creatorInfo->getAttribute('uFirstName') . " " . $creatorInfo->getAttribute('uLastName');
        }
        ?>

        <tr class='ccm-list-record'>
            <td>
                <input type="checkbox" name="userSubsId" value="<?php echo $subscription['ID'] ?>">
            </td>
            <td>
                <?php echo $subscription['Subscription'] . " (" . $subscription['ID'] . ")"; ?>
            </td>
            <td>
                <?php echo $subscription['SubType']; ?>
            </td>
            <td>
                <?php echo $subscription['CreationDate']; ?>
            </td>
            <td>
                <?php echo $subscription['EndDate']; ?>
            </td>
            <td>
                <?php echo $subscription['Duration']; ?>
            </td>
            <td>
                <?php echo $subscription['Active']; ?>
            </td>
            <td>
                <a href="/dashboard/code_check/<?php echo $subscription['AccessCode']; ?>">
                    <?php echo $subscription['AccessCode']; ?>
                </a>
            </td>
            <td>
                <?php echo $subscription['DaysRemaining']; ?>
            </td>
            <td>
                <?php echo $subscription['PurchaseType']; ?>
            </td>

            <?php if (isset($showCreator) && $showCreator): ?>
                <td><?php echo $creatorName; ?></td>
            <?php endif; ?>
        </tr>
    <?php } // end of foreach ?>

    <?php } else { ?>
        <p>
            No subscriptions found...
        </p>
    <?php } ?>
    </tbody>
</table>
