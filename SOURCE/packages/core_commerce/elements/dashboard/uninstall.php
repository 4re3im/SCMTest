<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
?>

<h4><?php echo t('Uninstall Content')?></h4>

<?php  $form = Loader::helper('form'); ?>
<?php  print $form->checkbox('coreCommerceUninstallContent', 1, true)?>
<?php echo t('Remove any pages found at or below "/catalog" and "/love-of-duck."')?>

<br/><br/>