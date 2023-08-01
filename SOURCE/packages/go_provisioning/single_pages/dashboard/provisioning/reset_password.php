<?php
defined('C5_EXECUTE') or die("Access Denied.");
$title = t('Reset Gigya Password');
$helpText = t('Reset Gigya password of users through a Gigya dataflow');
echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(
    $title,
    $helpText,
    '',
    true,
    [],
    true
);

$formUrl = $this->url('/dashboard/provisioning/reset_password/uploadFile');

echo Loader::packageElement('reset_password/modal_content_confirmation', 'go_provisioning');
echo Loader::packageElement('reset_password/modal_content_download', 'go_provisioning');
echo Loader::packageElement('reset_password/modal_content_duplicate_password', 'go_provisioning');
?>

<?php
if ($isAllowedAdmin) { ?>
    <?php echo Loader::packageElement(
            'bulk_actions/file_uploader',
            'go_provisioning',
            ['formUrl' => $formUrl]
        ); ?>

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
    <p>You have no permission accessing this feature. If you think there has been a mistake, inform your Senior Director or an Administrator.</p>
<?php } ?>


<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(); ?>