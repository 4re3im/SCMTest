<div class="ccm-pane-body">
    <div class="ccm-list-wrapper">
        <table class="ccm-results-list" id="gigya-users-list">
            <thead>
            <tr>
                <?php foreach ($headers as $header) { ?>
                    <th><?php echo $header; ?></th>
                <?php }?>
            </tr>
            </thead>
            <tbody>
            <tr class="ccm-list-record">
                <td colspan="<?php echo count($headers);?>">Retrieving users from Gigya...</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="ccm-pane-footer">
    <div class="pagination ccm-pagination gigya-pagination">
        <?php Loader::packageElement('gigya/users_table/pager', 'go_gigya'); ?>
    </div>
</div>
