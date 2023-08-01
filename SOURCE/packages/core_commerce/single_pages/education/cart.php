<?php
	$html = Loader::helper('html');
	$this->addHeaderItem($html->css('core_commerce.css', 'cup_content'));
?>
<?php echo Loader::packageElement('core_commerce/cart/header', 'cup_content', array('step' => 1))?>


<?php  if (isset($error) && $error->has()) {
	$error->output();
} ?>

<div id="ccm-core-commerce-checkout-cart">

<?php echo Loader::packageElement('cart_item_list', 'core_commerce', array('edit' => true, 'ajax' => false, 'cart'=>$cart))?>

</div>

<div class="cup_page_bottom_spacer"></div>

