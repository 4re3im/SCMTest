<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yetket
 * Date: 6/07/13
 * Time: 3:06 PM
 * To change this template use File | Settings | File Templates.
 */
Loader::model('cup_content_order/model', 'cup_content');

$submit_url = View::url('/dashboard/cup_content/config/send_invoice');
$submit_url = rtrim($submit_url, "/")."/".$cup_content_order->orderID;

?>
<div id="resend_invoice_server_response"></div>
<table id="resend_invoice">
    <tr>
        <td>To Email</td>
        <td><input type="text" name="send_invoice_to_email"></td>
    </tr>
    <tr>
        <td></td>
        <td><input type="submit" value="Send Invoice"></td>
    </tr>
</table>

<script>
    $('table#resend_invoice input[type="submit"]').click(function(){
        var to_email = jQuery('table#resend_invoice input[name="send_invoice_to_email"]').val();
        var action_url = "<?php echo $submit_url;?>"

        jQuery('#resend_invoice_server_response').html('<span style="">... please wait ...</span></span>')
        jQuery.post(action_url, {to_email:to_email}, function(data){
            jQuery('#resend_invoice_server_response').html(data);
        });
    });
</script>