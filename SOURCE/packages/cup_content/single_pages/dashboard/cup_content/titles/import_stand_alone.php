<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

$html = Loader::helper('html');
$this->addHeaderItem($html->css('wform.css', 'cup_content')); 
$this->addHeaderItem($html->javascript('colorbox/jquery.colorbox-min.js', 'cup_content'));
$this->addHeaderItem($html->css('../js/colorbox/colorbox.css', 'cup_content'));
$this->addHeaderItem($html->javascript('jquery.wspecial.js', 'cup_content')); 

$valt = Loader::helper('validation/token');

?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Import Title Stand Alone'), false)?>

<form method="post" id="ccm-core-commerce-product-add-form" enctype="multipart/form-data">
<div class="ccm-pane-body">
	<?php Loader::packageElement('alert_message_header', 'cup_content'); ?>
	File: <input type="file" name="file" />
	<br/>
	<br/>
	Worksheet name (excel file only): <input type="text" name="worksheet_name"/>
</div>
<div class="ccm-pane-footer">
	<input type="submit" class="ccm-button-right btn primary accept" value="<?php echo t('Continue')?>"/>
</div>	
</form>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>
