<?php  defined('C5_EXECUTE') or die("Access Denied."); 
// translate this file if we're multilingualing it
if(Loader::helper('multilingual','core_commerce')->isEnabled()) {
	Loader::helper('default_language','multilingual')->setupSiteInterfaceLocalization();
}

$orderID = $_REQUEST['orderID'];
$mode = ($_REQUEST['view_mode']=='dashboard'?'dashboard':'profile');
Loader::model('order/model','core_commerce');

$order = CoreCommerceOrder::getByID($orderID);

if($order instanceof CoreCommerceOrder) { 	

$html = Loader::helper('html');	
?>
<html>
<head>
<style>
@media print {
.hideprint {
	display:none;
}
}
</style>
<title><?php  echo t('Print Order No. '). $order->getInvoiceNumber()?></title>
<?php  
echo $html->css('ccm.core.commerce.order-print.css','core_commerce');

if (function_exists('mb_internal_encoding') && defined('APP_CHARSET')) {
	mb_internal_encoding(APP_CHARSET);
}

?>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

</head>
<body>
	<div id="ccm-order-print">
	<?php 
	if($mode == "dashboard") {
		// perms check
		$cnt = Loader::controller('/dashboard/core_commerce/orders/search');
		$p = new Permissions($cnt->getCollectionObject());
		
		if($p->canRead()) {
			Loader::packageElement('orders/detail','core_commerce',array('order'=>$order));
		} else {
			die(t("Access Denied."));	
		}
	} else { // profile mode
		$u = new User();
		$pkg = Package::getByHandle('core_commerce');
		
		$can_view = false;
		// registered user
		if($u->isLoggedIn() && $u->getUserID() == $order->getOrderUserID()) {
			$can_view = true;
		}
		
		// non-registerd, but recently completed
		if(!$can_view) {
			Loader::model('order/current', 'core_commerce');
			Loader::model('order/previous', 'core_commerce');
			$previousOrder = @CoreCommercePreviousOrder::get();
			if($previousOrder instanceof CoreCommercePreviousOrder && $previousOrder->getOrderID() == $order->getOrderID()) {
				$can_view = true;
			}
		}
		
		if($can_view) {
			Loader::packageElement('orders/detail','core_commerce',array('order'=>$order, 'exclude_sections'=>array('user_account')));
         ?> <p class="hideprint"><a href="Javascript:self.close()"><?php  echo t('Close this receipt') ?></a></p>	<?php 
		} else {
			die(t("Access Denied."));	
		}
	}
	?>
</div>
</body>
</html>
<?php  
} else {
	die(t("Access Denied."));	
}
