<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>
<?php if (!empty($users)) { ?>
    <?php foreach ($users as $user) {
        switch ($user['in_gigya']) {
            case '0' :
                $status = 'Failed';
                break;
            case '1' :
                $status = 'Succeeded';
                break;
            default:
                $status = 'Pending';
        }

        ?>
        <tr class="<?php echo str_replace(" ", "", $user['Status']) . " " . str_replace(" ", "", $user['Remarks']); ?>">
            <td><?php echo $user['uID']; ?></td>
            <td><?php echo $user['Email']; ?></td>
            <td><?php echo $user['LastName'] . ", " . $user['FirstName']; ?></td>
            <td><?php echo $user['Type'] ?></td>
            <td><?php echo $user['Status'] . " - " . $user['Remarks']; ?></td>
            <td><strong><?php echo $status; ?></strong></td>
        </tr>
    <?php } ?>
<?php } else { ?>
    <tr>
        <td colspan="6">No records found...</td>
    </tr>
<?php } ?>