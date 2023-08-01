<?php
$isAdmin = false;
$isTeacher = false;
if ($totalCount === 0) {
    ?>
    <tr class="ccm-list-record">
        <td colspan="<?php echo count($headers) + 1;?>">There are no users related to this institution.</td>
    </tr>
<?php } else {
    foreach ($results as $result) {
        $roleArray = [];
        $uid = $result->UID;
        $profile = $result->profile;
        $institutionArray = $result->data->eduelt->instituteRole;
        foreach ($institutionArray as $institution) {
            if ($institution->key_s === $oid) {
                if ($institution->role === 'admin' && 
                    $institution->role === 'teacher') {
                    $isAdmin = true;
                }

                if ($institution->role === 'teacher') {
                    $isTeacher = true;
                }
                array_push($roleArray, $institution->role);
            }
        }
    
  ?>
<tr class="ccm-list-record">
    <td><input type="checkbox" name="users"></td>
    <td><?php echo $uid; ?></td>
    <td><?php echo $profile->firstName; ?></td>
    <td><?php echo $profile->lastName; ?></td>
    <td><?php echo $profile->email; ?></td>
    <td><?php echo implode(",", $roleArray); ?></td>
    <?php if ($isTeacher) { ?>
        <td><?php 
            echo $isAdmin ? "<a onclick=removeAdmin(". $uid .")>[remove]</a>" : 
                            "<a onclick=addAdmin(". $uid .")>[add]</a>";
        ?>
        </td>
    <?php } ?>
</tr>
<?php }}?>