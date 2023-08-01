<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

$title = t('Edit Institution');
$helpText = t('Edit institutions from Gigya DS');
echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(
    $title,
    $helpText,
    '',
    false,
    [],
    true
);

$pkgHandle = 'institution_management';
$editFormURL = $this->url('/dashboard/institution_management/edit/setDetails/' . $oid);

?>
    <input type="hidden" id="app-institution-id" value="<?php echo $oid; ?>">
    <form method="post" id="app-edit-form" action="<?php echo $editFormURL; ?>">
        <div class="ccm-pane-body">
            <?php
            Loader::packageElement('commons/js_notification', $pkgHandle);
            ?>
            <div id="app-institute-form-div">
                <?php
                Loader::packageElement('commons/institute_form', $pkgHandle, [
                    'entry' => $entry
                ]);
                ?>
            </div>
        </div>

        <div class="ccm-pane-footer">
            <a href="/dashboard/institution_management/review/<?php echo $oid; ?>" class="btn pull-left">Back</a>
            <button type="submit" class="btn btn-primary pull-right">Save</button>
        </div>
    </form>
<?php
echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(true);
?>