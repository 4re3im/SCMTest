<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<div id="ccm-core-commerce-checkout-cart">
<?php echo Loader::packageElement('cart_item_list', 'core_commerce', array('edit' => false))?>
</div>

<?php  if (isset($error) && $error->has()) {
	$error->output();
} ?>

<div id="ccm-core-commerce-checkout-form-shipping-method" class="ccm-core-commerce-checkout-form">
	<h1><?php echo t('Shipping Method')?></h1>
	<?php  Loader::packageElement('checkout/shipping/method', 'core_commerce', array('action' => $action, 'o'=>$order,'form'=>$form,'form_attribute'=>$form_attribute))?>
</div>