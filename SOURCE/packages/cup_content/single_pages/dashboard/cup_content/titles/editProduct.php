<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$html = Loader::helper('html');
$this->addHeaderItem($html->css("jquery.ui.css"));
$this->addHeaderItem($html->javascript("jquery.ui.js"));

$this->addHeaderItem($html->css('wform.css', 'cup_content')); 
$this->addHeaderItem($html->javascript('colorbox/jquery.colorbox-min.js', 'cup_content'));
$this->addHeaderItem($html->css('../js/colorbox/colorbox.css', 'cup_content'));
$this->addHeaderItem($html->javascript('jquery.wspecial.js', 'cup_content')); 

$valt = Loader::helper('validation/token');

?>


<?php
	$page_title = "Edit Title Product";
	if($region == 'au'){
		$page_title .= ' - Australia Region';
	}else{
		$page_title .= ' - New Zealands Region';
	}

?>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t($page_title), false, false, false)?>


<form method="post" id="ccm-core-commerce-product-add-form" action="<?php echo $this->url('/dashboard/cup_content/titles/editProduct', $title->id, $region)?>">
<div class="ccm-pane-body">
	<?php Loader::packageElement('alert_message_header', 'cup_content'); ?>
	
	<div style="font-size: 16px;">
		<table>
			<tr>
				<td style="padding: 3px 10px;">ISBN:</td>
				<td><?php echo $title->isbn13;?></td>
			</tr>
			<tr>
				<td style="padding: 3px 10px;">Title:</td>
				<td><?php echo $title->displayName;?></td>
			</tr>
			<tr>
				<td style="padding: 3px 10px;">Subtitle:</td>
				<td><?php echo $title->displaySubtitle;?></td>
			</tr>
		</table>
		<div style="width:100%;height:5px;border-bottom:1px solid #aaaaaa;"></div>
	</div>
	
	<?php echo $valt->output('edit_title_product_'.$region)?>
	<?php Loader::packageElement('title/productForm', 'cup_content', array('productID'=>@$product_id)); ?>
</div>
<div class="ccm-pane-footer">
	<input type="hidden" name="create" value="1" />
	<input id="cc-parent-page" type="hidden" name="parentCID" value="0" />
	<a href="<?php echo $this->url('/dashboard/cup_content/titles')?>" class="btn"><?php echo t('Back to Titles')?></a>
	<input type="submit" class="ccm-button-right btn primary accept" value="<?php echo t('Save')?>"/>
</div>	
</form>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>
