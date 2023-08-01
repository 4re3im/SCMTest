<?php  defined('C5_EXECUTE') or die("Access Denied."); 
Loader::library('price','core_commerce');
$pkg = Package::getByHandle('core_commerce');
$dt = Loader::helper('date');
$form = Loader::helper('form');
?>
<div id="ccm-profile-wrapper">
    <?php  Loader::element('profile/sidebar', array('profile'=> $profile)); ?>    
    <div id="ccm-profile-body">	
    	<div id="ccm-profile-body-attributes">
	    	<div class="ccm-profile-body-item">
	        	<h1><?php echo t('Order History')?></h1>
	        	<div class="ccm-core-commerce-profile-order-list">
	        	<?php  
	        	if(is_array($orders) && count($orders)) {
					$i=0;
	        		foreach($orders as $order) { $i++; ?>
						<div class="ccm-core-commerce-profile-order-list-row">
							<ul>
								<li>
									<label><?php echo t('Date:')?></label>
 									<?php echo $dt->getLocalDateTime($order->getOrderDateAdded(),DATE_APP_GENERIC_MDY); ?>
 									<div class="ccm-spacer"></div>
 								</li>
								<li>
									<label><?php echo t('Order #:')?></label>
 									<?php echo $order->getInvoiceNumber()?>
 									<div class="ccm-spacer"></div>
 								</li>
 								<li>
 									<label><?php echo t('Total')?></label>
 									<?php  echo CoreCommercePrice::format($order->getOrderTotal()); ?>	
 								</li>
 								<li>
 									<label><?php echo t('Status')?></label>
 									<?php  echo $order->getOrderStatusText($order->getOrderStatus()) ?>	
 								</li>
 								<li class="ccm-core-commerce-order-print-link">
 									<a href="<?php  echo $concrete_urls->getToolsUrl('order_print','core_commerce')."?orderID=".$order->getOrderID(); ?>" target="_blank"><?php  echo t('Print Order')?></a>
 								</li>
							</ul>
							<div class="ccm-spacer"></div>
							 
							 
							<div class="ccm-core-commerce-profile-order-list-products" style="display:none;">
								<?php  
								Loader::packageElement('orders/detail','core_commerce',
										array('order'=>$order, 'exclude_sections'=>array('order_number', 'total', 'payment_method', 'user_account', 'billing', 'shipping'))
										);
								?>
								<br/>
							 <form method="post" action="<?php echo $this->action('duplicate_order')?>">
							 	<?php echo $form->hidden('orderID', $order->getOrderID())?>
							 	<?php echo $form->submit('submit', t('Place Order Again'))?>
							 </form>

							</div>
						</div>	
					<?php  }
				}
				?>	        	
	        	</div>
	        </div>
	    </div>
		
		<?php  
			$a = new Area('Main'); 
			$a->setAttribute('profile', $profile); 
			$a->setBlockWrapperStart('<div class="ccm-profile-body-item">');
			$a->setBlockWrapperEnd('</div>');
			$a->display($c); 
		?>
		
    </div>
	
	<div class="ccm-spacer"></div>
	
</div>
