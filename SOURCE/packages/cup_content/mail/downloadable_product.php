<?php  defined('C5_EXECUTE') or die(_("Access Denied."));
loader::library('html2text', 'cup_competition');

$subject = SITE." - ".t("Order# %s Downloadable Product ",$order->getInvoiceNumber());


$codes_html = "";
foreach($title_downloadable_files as $each) {
	$title = $each['title'];
	$download_url = $each['download_url'];
	$codes_html.= $title->displayName."<br/>\n ";
	$codes_html.= '<span class="access_code"><a href="'.$download_url.'">'.$download_url.'</a></span><br/>';

}


$bodyHTML = <<<EOF
<html>
<body>
<style>
span.access_code{
	font-size: 16px;
	color: #FF0000;
}

span.red{
	color: #FF0000;
}
</style>

You have purchased the following digital downloadable product(s) from Cambridge University Press:<br/>
<br/>
{$codes_html}
<br/>


<p>
	You are entitled to download and store any downloadable products for individual use only.  It may not be transferred to another party, including other students, as an electronic or print version.
</p>


<p>This product is non-returnable.</p>

<p>*Teachers please note: <span class="red">To access teacher only resources, you must register for a Cambridge GO teacher account.  Verification takes 1 business day during school term (it may take longer during school holidays).<span></p>

<p>If you have any questions or issues, please contact us on Australia 1800 005 210 NZ 0800 023 520 or <a href="mailto:enquiries@cambridge.edu.au">enquiries@cambridge.edu.au</a></p>
<body>
</html>
EOF;


$h2t =& new html2text($bodyHTML);
$body = $h2t->get_text();
?>