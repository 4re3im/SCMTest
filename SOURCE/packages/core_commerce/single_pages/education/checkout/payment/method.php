<?php
	$html = Loader::helper('html');
	$this->addHeaderItem($html->css('core_commerce.css', 'cup_content'));
?>
<?php echo Loader::packageElement('core_commerce/cart/header', 'cup_content', array('step' => '3a'))?>


<div id="ccm-core-commerce-checkout-cart">

<?php echo Loader::packageElement('cart_item_list', 'core_commerce', array('edit' => false))?>

<?php 
$fo = Loader::helper('form'); 
if (isset($error) && $error->has()) {
	$error->output();
} ?>


<div id="ccm-core-commerce-checkout-form-payment-method" class="ccm-core-commerce-checkout-form">

<h1><?php echo t('Payment Method')?></h1>

<?php  Loader::packageElement('checkout/payment/method','core_commerce', array('action' => $action, 'o'=>$order,'form'=>$form,'form_attribute'=>$form_attribute, 'akHandles'=>$akHandles)); ?>

</div>
</div>

<div class="cup_page_bottom_spacer"></div>