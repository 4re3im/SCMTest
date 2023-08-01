<?php
/**
 * User: yetket
 * Date: 28/05/13
 * Time: 12:08 PM
 * To change this template use File | Settings | File Templates.
 */

defined('C5_EXECUTE') or die(_("Access Denied."));
loader::library('html2text', 'cup_competition');

$subject = 'Cambridge University Press Inspection copy order';

$entry = $order->getAssoc();

$product_list = array();
$list = CupContentInspectionCopyOrder::getOrderItemList($order->id);
if($list && is_array($list)){
    foreach($list as $item){
        $product_list[] = $item['product_name'];
    }
}

$product_html = implode('<br/>', $product_list);


$address_line = "";
if(strlen($entry['shipping_address_line_1']) > 0){
    $address_line .= $entry['shipping_address_line_1'];
}

if(strlen($entry['shipping_address_line_2']) > 0){
    $address_line .= '<br/>'.$entry['shipping_address_line_2'];
}

$bodyHTML = <<<EOF
<html>
<body>
<style>

</style>

Thank you, you have ordered an inspection copy of:<br/>
{$product_html}

<br/>
<br/>

The inspection copy will be sent to:
<table>
    <tbody>
        <tr>
            <td valign="top">School order number</td>
            <td valign="top">{$entry['school_order_number']}</td>
        </tr>
        <tr>
            <td valign="top">Title</td>
            <td>{$entry['title']}</td>
        </tr>
        <tr>
            <td valign="top">First name</td>
            <td valign="top">{$entry['first_name']}</td>
        </tr>
        <tr>
            <td valign="top">Last name</td>
            <td valign="top">{$entry['last_name']}</td>
        </tr>
        <tr>
            <td valign="top">Job Title</td>
            <td valign="top">{$entry['position']}</td>
        </tr>
        <tr>
            <td valign="top">Address</td>
            <td valign="top">
                {$address_line}<br/>
                {$entry['shipping_address_city']}, {$entry['shipping_address_state']}<br/>
                {$entry['shipping_address_country']} {$entry['shipping_address_postcode']}
            </td>
        </tr>
        <tr>
            <td valign="top">Phone</td>
            <td valign="top">{$entry['phone']}</td>
        </tr>
        <tr>
            <td valign="top">Email</td>
            <td valign="top">{$entry['email']}</td>
        </tr>
    </tbody>
</table>

<p>
	After 30 days, inspection copies may be: purchased at the published price or returned in good condition at your school's expense to Cambridge University Press.
</p>

<p>
    Inspection copies should be ordered using an official school order number. If your school does not allow school order numbers please reply to this email and we will cancel your order.
</p>

<p>
    Cambridge University Press <br/>
    477 Williamstown Road <br/>
    Port Melbourne VIC 3207 <br/>
    Ph: 03 8671 1400 <br/>
    enquiries@cambridge.edu.au <br/>
    www.cambridge.edu.au/education
</p>

<body>
</html>
EOF;


$h2t =& new html2text($bodyHTML);
$body = $h2t->get_text();
?>