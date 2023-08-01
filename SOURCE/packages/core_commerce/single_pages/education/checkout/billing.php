<?php 
$o = CoreCommerceCurrentOrder::get();
$form_attribute->setAttributeObject($o);

$html = Loader::helper('html');
$this->addHeaderItem($html->javascript('bootstrap.js'));
$this->addHeaderItem($html->javascript('jquery.ui.js'));
$this->addHeaderItem($html->javascript('ccm.dialog.js'));
$this->addHeaderItem($html->css('jquery.ui.css'));
$this->addHeaderItem($html->css('ccm.dialog.css'));
?>
<style>
.ui-dialog .ui-dialog-content{
	color: #333333;
}

.ui-dialog .ui-dialog-content a, 
.ui-dialog .ui-dialog-content a:link, 
.ui-dialog .ui-dialog-content a:visited {
	color: #333333;
    font-weight: bold;
    text-decoration: none;
}

.ui-dialog .ui-dialog-content a:hover {
	background: #F9D55B;
}
</style>

<?php
	$html = Loader::helper('html');
	$this->addHeaderItem($html->css('core_commerce.css', 'cup_content'));
?>
<?php echo Loader::packageElement('core_commerce/cart/header', 'cup_content', array('step' => 2))?>

<div id="ccm-core-commerce-checkout-cart">
	<?php echo Loader::packageElement('cart_item_list', 'core_commerce', array('edit' => false))?>
</div>
<?php  
if (isset($error) && $error->has()) {
	$error->output();
}
?>
<div id="ccm-core-commerce-checkout-form-billing" class="ccm-core-commerce-checkout-form">
	<h1><?php echo t('Billing Information')?></h1>
	<?php  Loader::packageElement('/checkout/billing','core_commerce', array('action' => $action, 'o'=>$o,'form'=>$form,'form_attribute'=>$form_attribute, 'akHandles'=>$akHandles)); ?>
</div>

<div class="cup_page_bottom_spacer"></div>

<script type="text/javascript">
	jQuery(".product_popup_content" ).dialog({
		title: 'Product Note',
		resizable: false,
		width: 500,
		height: 400,
		modal: true,
		buttons: {
			"OK": function() {
				jQuery( this ).dialog( "close" );
				}
		}
	});
</script>