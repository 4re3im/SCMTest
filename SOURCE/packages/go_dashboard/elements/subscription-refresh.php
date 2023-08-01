<?php
defined('C5_EXECUTE') or die("Access Denied.");

// ANZGO-3634 Modified by John Renzo Sunico, 02/12/2018
foreach ($subscriptions as $subscription):
    $isActivated = $subscription['Active'] == 'Y' ? 'Deactivate' : 'Activate';
    $creatorInfo = UserInfo::getByID($subscription['CreatedBy']);

    $creatorName = '-';
    if ($creatorInfo) {
        $creatorName = $creatorInfo->getAttribute('uFirstName') . " " . $creatorInfo->getAttribute('uLastName');
    }
    ?>
    <tr class='ccm-list-record'>
        <td><input type="checkbox" name="usID" value="<?php echo  $subscription['ID'] ?>"></td>
        <td><?php echo $subscription['Subscription'] . " (" . $subscription['ID'] . ")"; ?></td>
        <td><?php echo $subscription['SubType']; ?></td>
        <td><?php echo $subscription['CreationDate']; ?></td>
        <td><?php echo $subscription['EndDate']; ?></td>
        <td><?php echo $subscription['Duration']; ?></td>
        <td><?php echo $subscription['Active']; ?></td>
        <td>
            <a href="/dashboard/code_check/<?php echo $subscription['AccessCode']; ?>">
                <?php echo $subscription['AccessCode']; ?>
            </a>
        </td>
        <td><?php echo $subscription['DaysRemaining']; ?></td>
        <td><?php echo $subscription['PurchaseType']; ?></td>
        <?php if (isset($showCreator) && $showCreator): ?>
            <td><?php echo $creatorName; ?></td>
        <?php endif; ?>
    </tr>
<?php endforeach; ?>