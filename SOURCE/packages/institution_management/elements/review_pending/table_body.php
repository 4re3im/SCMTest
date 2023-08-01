<?php
$form = Loader::helper('form');
$selectOpts = [
    '' => 'Select one...',
    'invalid' => 'Invalid',
    'cup_staff_member' => 'CUP Staff Member',
    'distributor' => 'Distributor',
    'institution_already_exists' => 'Institution Already Exists',
    'home_school' => 'Home School',
    'not_using_school_email' => 'Not Using School Email',
    'unable_to_verify_school' => 'Unable to Verify School',
];
?>
<?php if (count($data) > 0) { ?>
    <?php foreach ($data as $oid => $datum) {
        $requester = $datum['requester'];
        ?>
        <tr class="ccm-list-record" id="<?php echo $oid; ?>">
            <td>
                <?php if ($datum['enableReject']) { ?>
                    <input type="checkbox" class="review-pending-checkboxes" value="<?php echo $oid; ?>" name="checkboxes"/>
                <?php }?>
            </td>
            <td>
                <p>
                    <?php echo $datum['name']; ?>
                    <br/>
                    <small><?php echo $oid; ?></small>
                    <!-- hidden fields -->
                    <input type="hidden"
                           name="institution[<?php echo $oid;?>][uid]"
                           value="<?php echo $requester['UID']?>" />
                    <input type="hidden"
                           name="institution[<?php echo $oid;?>][schoolName]"
                           value="<?php echo $datum['name']; ?>" />
                    <input type="hidden"
                           name="institution[<?php echo $oid;?>][email]"
                           value="<?php echo $requester['email']?>" />
                    <input type="hidden"
                           name="institution[<?php echo $oid;?>][username]"
                           value="<?php echo $requester['name']?>" />
                    <input type="hidden"
                           name="institution[<?php echo $oid;?>][schoolCode]"
                           value="<?php echo $datum['schoolCode']?>" />
                </p>
            </td>
            <td><?php echo $datum['address']; ?></td>
            <?php
            if (!$datum['enableReject']) { ?>
                <td colspan="2">
                    <em><?php echo $datum['remarks'] ?></em>
                </td>
            <?php } else { ?>
                <td>
                    <p>
                        <?php echo $requester['name']; ?>
                        <br />
                        <small><?php echo $requester['email']; ?></small>
                    </p>
                </td>
                <td>
                    <?php
                    echo $form->select(
                        'remarks',
                        $selectOpts,
                        null,
                        ['class' => 'review-pending-results-select']
                    );
                    ?>
                </td>
            <?php } ?>
        </tr>
    <?php } ?>
<?php } else if (count($data) === 0) { ?>
    <tr class="ccm-list-record">
        <td colspan="5">There are no institutions found related to your search.</td>
    </tr>
<?php } else { ?>
    <tr class="ccm-list-record">
        <td colspan="5">Retrieving institutions from Gigya...</td>
    </tr>
<?php } ?>