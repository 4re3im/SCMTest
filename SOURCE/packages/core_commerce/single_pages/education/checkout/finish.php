<?php  defined('C5_EXECUTE') or die("Access Denied.");  ?>

<?php
	//sparkmill
	$html = Loader::helper('html');
	$this->addHeaderItem($html->css('core_commerce.css', 'cup_content'));

	echo Loader::packageElement('core_commerce/cart/header', 'cup_content', array('step' => '4')); //sparkmill?>


<div id="ccm-core-commerce-checkout-cart">
	<?php 
	$a = new Area('Main'); 
	$blocks = $a->getAreaBlocksArray($c);
	if((is_array($blocks) && count($blocks)) || $c->isEditMode()) { 
		$a->display($c);
	} else { ?>
		<p>Thank you for your purchase, you will receive a Tax Invoice by email. <br/>
If you have purchased a digital product, you will also receive access instructions in a separate email (be sure to check your junk mail if you cannot find it).<br/>
If you have any queries, please email Customer Service: <a href="mailto:enquiries@cambridge.edu.au">enquiries@cambridge.edu.au</a></p> <?php 
	} ?>
	
	<div class="ccm-core-commerce-checkout-complete-order">
	<?php  
		if($previousOrder instanceof CoreCommercePreviousOrder && $previousOrder->getStatus() != CoreCommerceOrder::STATUS_NEW && $previousOrder->getStatus() != CoreCommerceOrder::STATUS_INCOMPLETE) { ?>
			
			<div class="ccm-core-commerce-order-print-link">
				<a href="<?php  echo $concrete_urls->getToolsUrl('order_print','core_commerce')."?orderID=".$previousOrder->getOrderID(); ?>" target="_blank"><?php  echo t('Print Order Details')?></a>
			</div>
			<?php 
			Loader::packageElement('orders/detail','core_commerce',array('order'=>$previousOrder, 'exclude_sections'=>array('payment_method', 'user_account')));
		}
	?>
	</div>
</div>

<div class="cup_page_bottom_spacer"></div> <!-- sparkmill -->

<!-- Ecommerce Tracking Start -->
<?php
Loader::model('cup_content_order/list', 'cup_content');

$cupOrder = CupContentOrder::fetchByOrderID($previousOrder->getOrderID());

if($cupOrder){
    echo $cupOrder->generateGoogleTrackingCode();
}
?>
<!-- Ecommerce Tracking End -->

<!-- Facebook Conversion Code for FacebookTextGuidesAd -->
<script type="text/javascript">
var fb_param = {};
fb_param.pixel_id = '6010305185593';
fb_param.value = '0.01';
fb_param.currency = 'AUD';
(function(){
var fpw = document.createElement('script');
fpw.async = true;
fpw.src = '//connect.facebook.net/en_US/fp.js';
var ref = document.getElementsByTagName('script')[0];
ref.parentNode.insertBefore(fpw, ref);
})();
</script>
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/offsite_event.php?id=6010305185593&value=0.01&currency=AUD"; [^] /></noscript>