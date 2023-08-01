<?php

foreach ($users as $user) {
    $rowClass = $user['Status'] === 'Password changed' ? 'success' : '';
    ?>
<tr class="<?php echo $rowClass?>">
    <td><?php echo $user['Email']; ?></td>
    <td><?php echo $user['FirstName']; ?></td>
    <td><?php echo $user['LastName']; ?></td>
    <td><?php echo $user['Status']; ?></td>
    <td><?php echo $user['DateUploaded']; ?></td>
    <td><?php echo $user['DateModified']; ?></td>
<?php } ?>
