<?php
/**
 * Created by PhpStorm.
 * User: gxbalila
 * Date: 17/05/2019
 * Time: 10:03 AM
 */

echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Gigya Users'),
    t('List and search Gigya users'), false, false);

$helper = Loader::helper('gigya_users_table', 'go_gigya');
?>
<div class="ccm-pane-options">
    <form class="form-horizontal" id="ccm-user-advanced-search">
        <div class="ccm-pane-options-permanent-search">
            <div class="span11">
                <label for="gigya-search" class="control-label">Search</label>
                <div class="controls">
                    <input id="gigya-search" type="text" name="email" value="" placeholder="Email" class="ccm-input-text">
                </div>
                <span style="float: right"> <strong>Platform:</strong> <br> Go - CLS</span>
            </div>
        </div>
    </form>
</div>

<?php echo $helper->getInitialTable() ?>
