<?php
$pkgHandle = 'institution_management';
?>
<div class="ccm-list-wrapper">
    <table class="ccm-results-list" id="<?php echo $role; ?>-user-list">
        <thead>
        <tr>
            <th><input type="checkbox"></th>
            <?php
                if ($role === 'student') {
                    array_pop($headers);
                }

                foreach ($headers as $header) {
                    echo '<th>' . $header . '</th>';
                }
            ?>
        </tr>
        </thead>
        <tbody>
        <tr class="ccm-list-record">
            <td colspan="<?php echo count($headers) + 1;?>">Retrieving users from Gigya...</td>
        </tr>
        </tbody>
    </table>
</div>

<div class="ccm-pane-footer">
    <div class="pagination ccm-pagination <?php echo $role; ?>-pagination">
        <?php Loader::packageElement('commons/table_pager', $pkgHandle); ?>
    </div>
</div>