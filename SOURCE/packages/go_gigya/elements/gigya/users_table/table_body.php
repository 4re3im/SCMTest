<?php
$json = json_decode($results);
foreach ($json as $teacher) {
    $gigyaPlatforms = isset($platforms[$teacher->UID]) ? $platforms[$teacher->UID] : null;
    $profile = $teacher->profile;
    $instituteRole = $teacher->data->eduelt->instituteRole;
?>
    <tr class="ccm-list-record">
        <td><?php echo date("dS M Y g:i A", strtotime($teacher->created)); ?></td>
        <td><a href="/dashboard/gigya/subscriptions/<?php echo $teacher->UID?>"><?php echo "$profile->firstName $profile->lastName"; ?></a></td>
        <td><?php echo $profile->email; ?></td>
        <td><?php echo is_array($instituteRole) ? $instituteRole[0]->institute : ''; ?></td>
        <td>
            <?php echo (isset($subjectsTaught[$teacher->UID])) ? implode(",\n", $subjectsTaught[$teacher->UID]) : null;?>
        </td>
        <td style="text-align:center">
            <?php
            if (is_array($gigyaPlatforms)) {
                echo join(' & ', $gigyaPlatforms);
            } else {
                echo 'No terms agreed.';
            } ?>
        </td>
    </tr>
<?php } ?>
