<?php
defined('C5_EXECUTE') or die("Access Denied.");

$title = t('Bulk Delete Users');
$helpText = t('Delete Gigya users through a dataflow');
echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(
    $title,
    $helpText,
    '',
    true,
    [],
    true
);

$formUrl = $this->url('/dashboard/provisioning/bulk_delete/uploadFile');

echo Loader::packageElement('bulk_delete/modal_content_confirmation', 'go_provisioning');
echo Loader::packageElement('bulk_delete/modal_content_download', 'go_provisioning');
echo Loader::packageElement('bulk_delete/modal_content_no_school_matches', 'go_provisioning');
?>

<?php
if ($isAllowedAdmin) { ?>
    <div class="ccm-pane-options-permanent-search">
        <div class="row">
            <div class="span5">
                <?php echo Loader::packageElement(
                    'bulk_actions/school_select',
                    'go_provisioning'
                ); ?>
                <h3 id="school-select-display" style="display: none"></h3>
            </div>
            <div class="span2">
                <label for="">&nbsp;</label>
                <button class="btn default" id="school-select-remove" style="display: none">Remove</button>
            </div>
            <div class="span3">
                <?php echo Loader::packageElement(
                    'bulk_actions/file_uploader',
                    'go_provisioning',
                    ['formUrl' => $formUrl]
                ); ?>
            </div>

        </div>

    </div>


    <?php
    echo Loader::packageElement('bulk_actions/alert_box', 'go_provisioning');

    echo Loader::packageElement('bulk_actions/progress_bar', 'go_provisioning');

    echo Loader::packageElement('bulk_actions/users_display', 'go_provisioning');
    ?>


        <div class="ccm-ui" id="download-modal-btn-div" style="display: none;">
            <button class="btn primary ccm-button-right" id="download-modal-trigger">Get result file</button>
        </div>


    <?php
    echo Loader::packageElement('bulk_actions/footer', 'go_provisioning');
    ?>
<?php } else { ?>
    <p>You have no permission accessing this feature. If you think there has been a mistake, inform your Senior Director
        or an Administrator.</p>
<?php } ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(); ?>