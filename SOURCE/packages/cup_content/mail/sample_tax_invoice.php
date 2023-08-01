<?php  defined('C5_EXECUTE') or die(_("Access Denied."));
loader::library('html2text', 'cup_competition');

$subject = "Sample Receipt";


ob_start();
include(dirname(__FILE__).'/sample_invoice.html');
$body_html_tpl = ob_get_clean();

ob_start();
include(dirname(__FILE__).'/sample_invoice_item.html');
$item_html_tpl = ob_get_clean();

ob_start();
include(dirname(__FILE__).'/invoice_inclusive_item.html');
$inclusive_item_html_tpl = ob_get_clean();






$billing_detail = array();
$shipping_detail = array();
if($email_data['billing']){
	$address_detail = $email_data['billing'];
	
	$billing_detail[] = $address_detail['first_name'].' '.$address_detail['last_name'];
	$billing_detail[] = $address_detail['address1'];
	if(strlen($address_detail['address2']) > 0){
		$billing_detail[] = $address_detail['address2'];
	}
	
	$country = 'Australia';
	if(strcasecmp($address_detail['country'], 'AU') == 0){
		$country = 'Australia';
		$currency = 'AUD';
	}elseif(strcasecmp($address_detail['country'], 'NZ') == 0){
		$country = 'New Zealand';
		$currency = 'NZD';
	}
	
	$billing_detail[] = $address_detail['city'].", ".$address_detail['state'].", {$country} ".$address_detail['zip'];
}

if($email_data['shipping'] && is_array($email_data['shipping']) && count($email_data['shipping']) > 0){
	$address_detail = $email_data['shipping'];
	
	$shipping_detail[] = $address_detail['first_name'].' '.$address_detail['last_name'];
	$shipping_detail[] = $address_detail['address1'];
	if(strlen($address_detail['address2']) > 0){
		$shipping_detail[] = $address_detail['address2'];
	}
	
	$country = 'Australia';
	if(strcasecmp($address_detail['country'], 'AU') == 0){
		$country = 'Australia';
	}elseif(strcasecmp($address_detail['country'], 'NZ') == 0){
		$country = 'New Zealand';
	}
	
	$shipping_detail[] = $address_detail['city'].", ".$address_detail['state'].", {$country} ".$address_detail['zip'];
}

$billing_detail = implode('<br/>', $billing_detail);
$shipping_detail = implode('<br/>', $shipping_detail);





$currency = 'AUD';
$item_html = array();
$adjustment_html = array();
$inclusive_html = array();

foreach($email_data['products'] as $item){
	$repace_values = array(
							'%%INVOICE_NUMBER%%' => $email_data['invoiceNumber'],
							'%%ITEM_ISBN_13%%'	=> $item['isbn13'],
							'%%ITEM_QTY%%'	=> $item['quantity'],
							'%%ITEM_TITLE%%' => $item['name'],
							'%%ITEM_EDITION%%' => $item['edition'],
							'%%ITEM_UNIT_PRICE%%' => number_format($item['unit_price'], 2),
							'%%ITEM_UNIT_GST%%' => "",
							'%%ITEM_TOTAL_PRICE%%' => $item['price']
						);
						
	$item_html[] = strtr($item_html_tpl, $repace_values);
}


foreach($email_data['adjustments'] as $item){
	if($item['type'] == '+'){
		$repace_values = array(
								'%%INVOICE_NUMBER%%' => "",
								'%%ITEM_ISBN_13%%'	=> "",
								'%%ITEM_QTY%%'	=> "",
								'%%ITEM_TITLE%%' => $item['name'],
								'%%ITEM_EDITION%%' => "",
								'%%ITEM_UNIT_PRICE%%' => "",
								'%%ITEM_UNIT_GST%%' => "",
								'%%ITEM_TOTAL_PRICE%%' => '$'.trim($item['total'], '$')
							);
							
		$adjustment_html[] = strtr($item_html_tpl, $repace_values);
		$item_html[] = strtr($item_html_tpl, $repace_values);
	}elseif($item['type'] == '='){
		$repace_values = array(
								'%%INCLUSIVE_ITEM_TITLE%%' => $item['name'],
								'%%INCLUSIVE_ITEM_TOTAL_PRICE%%' => $currency.' '.trim($item['total'], '$')
							);
		$inclusive_html[] = strtr($inclusive_item_html_tpl, $repace_values);
	}
}


$item_html = implode("\n", $item_html);
$adjustment_html = implode("\n", $adjustment_html);
$inclusive_html = implode("\n", $inclusive_html);







$repace_values = array(
							'%%INVOICE_NUMBER%%' => $email_data['invoiceNumber'],
							'%%ORDER_DATE%%' => date('d/M/d', strtotime($email_data['orderDate'])),
							'%%BILLING_DETAIL%%'	=> $billing_detail,
							'%%SHIPPING_DETAIL%%'	=> $shipping_detail,
							'%%ITEMS%%' => $item_html,
							'%%INCLUSIVE_ITEM%%' => $inclusive_html,
							'%%GST_AMOUNT%%' => "",
							'%%TOTAL_AMOUNT%%' => $currency.' '.trim($email_data['totalAmount'], '$')
						);


$bodyHTML = strtr($body_html_tpl, $repace_values);


$h2t =& new html2text($bodyHTML);
$body = $h2t->get_text();
?>