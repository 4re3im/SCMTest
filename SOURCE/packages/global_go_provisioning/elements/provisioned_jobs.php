<?php
/**
 * SB-674 Added by mtanada 20200902
 */
defined('C5_EXECUTE') or die("Access Denied.");
?>
<?php if (!empty($jobs)) { ?>
    <?php foreach ($jobs as $job) {?>
        <tr class="">
            <td><?php echo $job['Title']; ?></td>
            <td><?php echo $job['Queue']; ?></td>
            <td><?php echo $job['EntitlementIds']; ?></td>
            <td><?php echo $job['Errors'] ?></td>
            <td><strong><?php echo $job['Status']; ?></strong></td>
            <td>
                <?php if ($job['Status'] === 'done') { ?>
                    <button class="btn btn-success">export</button>
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
<?php } else { ?>
    <tr>
        <td colspan="6">No jobs found...</td>
    </tr>
<?php } ?>