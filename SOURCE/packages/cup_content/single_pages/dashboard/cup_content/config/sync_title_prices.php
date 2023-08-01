<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

$html = Loader::helper('html');

$valt = Loader::helper('validation/token');

?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Sync Title Prices'), false)?>

<p>
	This takes a excel file<br/>
	Please make sure the file contains column names: isbn10, isbn13, aus_price, nz_price, aus_availble_stock, nz_available_stock, availability
</p>

<form method="post" enctype="multipart/form-data">
<div class="ccm-pane-body">
	<?php Loader::packageElement('alert_message_header', 'cup_content'); ?>
	File: <input type="file" name="file" />
	<br/>
	<br/>
	Worksheet name (excel file): <input type="text" name="worksheet_name"/>
</div>
<div class="ccm-pane-footer">
	<input type="submit" class="ccm-button-right btn primary accept" value="<?php echo t('Continue')?>"/>
</div>	
</form>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>

