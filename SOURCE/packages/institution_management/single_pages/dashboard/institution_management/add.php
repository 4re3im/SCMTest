<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

$valt = Loader::helper('validation/token');
$title = t('Add Institution');
$helpText = t('Add new institution in Gigya DS');
echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(
    $title,
    $helpText,
    '',
    false,
    [],
    true
);

$pkgHandle = 'institution_management';
$addFormURL = $this->url('/dashboard/institution_management/add/create');
?>

<div class="ccm-pane-body">
    <?php Loader::packageElement('commons/flash_alerts', $pkgHandle); ?>
    <form method="post" id=""
          action="<?php echo $addFormURL; ?>">

        <?php
        echo $valt->output('create_subject');
        Loader::packageElement('commons/institute_form', $pkgHandle, ['entry' => $entry]);
        ?>
</div>

<?php
echo Loader::packageElement('/add/footer', $pkgHandle);
echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(true);
?>
</form>