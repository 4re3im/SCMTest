<?php  defined('C5_EXECUTE') or die(_("Access Denied."));
loader::library('html2text', 'cup_competition');

//$subject = SITE." - ".t("Order# %s Access Code",$order->getInvoiceNumber());
$subject = SITE." - Access Code Low Stock Notification - ".$titleObj->name;

$bodyHTML = <<<EOF
<html>
<body>
	{$titleObj->name}<br/>
	ISBN: {$titleObj->isbn13}<br/>
	<br/>
	Available Access Code Qty: {$stockQty}<br/>
	
</body>
</html>
EOF;

$h2t =& new html2text($bodyHTML);
$body = $h2t->get_text();