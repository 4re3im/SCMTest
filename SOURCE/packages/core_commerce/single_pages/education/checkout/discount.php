<div id="ccm-core-commerce-checkout-cart">

<?php echo Loader::packageElement('cart_item_list', 'core_commerce', array('edit' => false))?>

</div>

<?php  if (isset($error) && $error->has()) {
	$error->output();
} ?>

<div id="ccm-core-commerce-checkout-form-discount" class="ccm-core-commerce-checkout-form">
	<h1><?php echo t('Special Offer Code')?></h1>
	<?php  Loader::packageElement('/checkout/discount','core_commerce', array('o'=>$o,'form'=>$form,'form_attribute'=>$form_attribute, 'akHandles'=>$akHandles,'action'=>$action)); ?>
</div>