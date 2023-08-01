<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

$pkgHandle = 'institution_management';
$form = Loader::helper('form');
$valt = Loader::helper('validation/token');
$title = 'Pending Institutions';
$helpText = t('Review pending Gigya institution');
echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(
    $title,
    $helpText,
    '',
    false,
    [],
    true
);

$pkgHandle = 'institution_management';
echo Loader::packageElement('search/search_bar', $pkgHandle);
echo Loader::packageElement('review_pending/modal_content_confirmation', $pkgHandle);
?>
    <div class="ccm-pane-body">
        <?php
        echo Loader::packageElement(
            'review_pending/table',
            $pkgHandle,
            ['data' => $data]
        );
        ?>
        <button type="submit" id="review-pending-proceed-btn" class="ccm-button-right btn primary accept">
            Proceed
        </button>
        <div class="clearfix"></div>
    </div>

    <div class="ccm-pane-footer">
        <div id="app-review-pending-pager" class="pagination ccm-pagination">
            <?php
            echo Loader::packageElement(
                    'review_pending/table_pager',
                    $pkgHandle,
                    ['pager' => $pager]
            )
            ?>
        </div>
    </div>
<?php
echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(true);
?>