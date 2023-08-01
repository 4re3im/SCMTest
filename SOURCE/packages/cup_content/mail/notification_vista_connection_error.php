<?php  defined('C5_EXECUTE') or die(_("Access Denied."));
loader::library('html2text', 'cup_competition');

$subject = SITE." [System Notification] - Unsuccessful Purchase";

$site = SITE;

$billingHtml = "n/a";
if($billTo){
	$billingHtml = "";
	foreach($billTo as $key => $val){
		//$billingHtml .= $key.": ".$val."<br/>";
		if(strlen(trim($val)) > 0){
			$billingHtml .= $val."<br/>";
		}
	}
}

$shippingHtml = "n/a";
if($shipTo){
	$shippingHtml = "";
	foreach($shipTo as $key => $val){
		//$shippingHtml .= $key.": ".$val."<br/>";
		if(strlen(trim($val)) > 0){
			$shippingHtml .= $val."<br/>";
		}
	}
}

$cart_detail = "";
if($cart){
	foreach($cart->getProducts() as $product){
		$product_name = $product->getProductName();
		$requested_qty = $product->getQuantity();
		
		$productObj = $product->getProductObject();
		$titleObject = CupContentTitle::fetchByProductId($productObj->getProductID());
		$title_isbn10 = $titleObject->isbn10;
		
		$cart_detail .= $product_name."<br/>";
		$cart_detail .= "ISBN10: {$title_isbn10} <br/>";
		$cart_detail .= "QTY: {$requested_qty} <br/>";
		$cart_detail .= "<br/><br/>";
	}
}


$bodyHTML = <<<EOF
<html>
<body>
	The website is having a problem with communication to VISTA.<br/>
	
	<table>
		<tr>
			<td valign="top">Billing details</td>
			<td valign="top">{$billingHtml}</td>
		</tr>
		<tr>
			<td valign="top">Shipping details</td>
			<td valign="top">{$shippingHtml}</td>
		</tr>
		<tr>
			<td valign="top">Order details</td>
			<td valign="top">{$cart_detail}</td>
		</tr>
	</table>
<body>
</html>
EOF;


$h2t =& new html2text($billingHtml);
$billingText = $h2t->get_text();

$h2t =& new html2text($shippingHtml);
$shippingText = $h2t->get_text();

$h2t =& new html2text($cart_detail);
$cart_detail_text = $h2t->get_text();
$body = <<<EOF
	The website is having a problem with communication to VISTA.
	
Billing details:
{$billingText}
	
	
Shipping details:
{$shippingText}
	
	
Order details:
{$cart_detail_text}
EOF;
?>