<?php  defined('C5_EXECUTE') or die(_("Access Denied."));
loader::library('html2text', 'cup_competition');

//$subject = SITE." - ".t("Order# %s Access Code",$order->getInvoiceNumber());
$subject = SITE." - ".t("Order# %s Access Code",$order->getOrderID());



$codes_html = "";
foreach($title_access_codes as $each) {
	$title = $each['title'];
	$access_code = $each['access_code'];
	$codes_html.= $title->displayName."<br/>";
	$codes_html.= '<span class="access_code">'.$access_code.'</span><br/>';

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

You have purchased the following digital product(s) from Cambridge University Press:<br/>
<br/>
{$codes_html}
<br/>

<p>
Digital products can be accessed now through <a href="http://www.cambridge.edu.au/GO">http://www.cambridge.edu.au/GO</a>.
</p>

<p>To access follow the steps below:</p>
<ol>
	<li>
		Login to your existing Cambridge GO user account *<br/>
		OR<br/>
		Create a new user account by visiting: <a href="http://www.cambridge.edu.au/GO/newuser">http://www.cambridge.edu.au/GO/newuser</a>
	</li>
	<li>
		To activate each product enter the unique access code provided.  The code can only be activated once, and will provide access to the one product on Cambridge GO.
	</li>
	<li>
		Your product will now be available to access via the MY RESOURCES page. Once activated, this code is no longer required. Access you’re my RESOURCES page using your Cambridge GO username and password.
	</li>
</ol>

<p>
	You are entitled to download and store any downloadable products for individual use only.  It may not be transferred to another party, including other students, as an electronic or print version.
</p>

<p>
	If you have purchased a subscription product your subscription term is defined as such: If activation occurs between January to July of this year, subscription concludes on the 31st December this year.  If activation occurs between August to December of this year subscription concludes on the 31st of December the following year. 
</p>

<p>
	If you have purchased a Teacher Resource Package this code may be activated once only in one unique Cambridge GO account.  Downloaded materials may be placed on your school network for use by other teachers at your school.
</p>

<p>
	For a full list of terms and conditions visit www.cambridge.edu.au/GO
</p>

<p>This product is non-returnable.</p>

<p>If you have any questions or issues, please contact us on Australia 1800 005 210 NZ 0800 023 520 or <a href="mailto:enquiries@cambridge.edu.au">enquiries@cambridge.edu.au</a></p>
<body>
</html>
EOF;


$h2t =& new html2text($bodyHTML);
$body = $h2t->get_text();
?>