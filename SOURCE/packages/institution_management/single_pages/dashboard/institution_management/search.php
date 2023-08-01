<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

$valt = Loader::helper('validation/token');
$title = t('Search Institution');
$helpText = t('Search institutions from Gigya DS');
echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(
    $title,
    $helpText,
    '',
    false,
    [],
    true
);

$pkgHandle = 'institution_management';
?>
<?php
echo Loader::packageElement('search/search_bar', $pkgHandle);
echo Loader::packageElement('alert_message_header', 'cup_content');
?>

    <div class="ccm-pane-body" style="display: none;">
    </div>

    <div class="ccm-pane-footer" style="display: none;">
        <div id="app-institution-pager" class="pagination ccm-pagination">
            <?php
            echo Loader::packageElement('search/table_pager', $pkgHandle)
            ?>
        </div>
    </div>
<?php
echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(true);
?>
