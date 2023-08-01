<?php  defined('C5_EXECUTE') or die("Access Denied.");  ?>


<div id="ccm-core-commerce-checkout-cart">
	<?php 
	$a = new Area('Main'); 
	$blocks = $a->getAreaBlocksArray($c);
	if((is_array($blocks) && count($blocks)) || $c->isEditMode()) { 
		$a->display($c);
	} else { ?>
		<p><?php echo t('Thank you for your purchase!')?></p> <?php 
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


<div class="cup_page_bottom_spacer"></div>