<?php
/*
 * View Test Form
 * @author: Ariel Tabag <atabag@cambridge.org>
 */

defined('C5_EXECUTE') or die("Access Denied.");

$verify_account = $this->url('provision', 'verifyAccount');

?>
<script>var verify_account = '<?php echo $verify_account; ?>', user_id = '<?php echo $user_id; ?>'</script>
<div id='newuser'>
    <?php if($verification_error){ 
        echo $verification_error;
    ?>
    <?php }else{ ?>
    <form id="password-form" name="password-form" enctype="multipart/form-data" action="" method="post">
    <table cellpadding="0" cellspacing="0" border="0">
      <tr>
        <td>&nbsp;</td>
        <td style="font-style:italic"><strong>Important: </strong><span style="color:#8ac007;"><?php echo $name; ?></span>, you will need to update password to activate your account.</td>
      </tr>
      <tr>
        <td class="field_heading">New Password</td>
        <td><input type="password" name="password" id="password" /></td>
        <td class="field_notes" id="password_message_div"></td>
      </tr>
      
      <tr>
        <td class="field_heading">Verify Password</td>
        <td><input type="password" name="verifypassword" id="verifypassword" /></td>
        <td class="field_notes" id="verifypassword_message_div">&nbsp;</td>
      </tr>

      <tr>
        <td>&nbsp;</td>
        <td colspan="2"><span style="font-size:13px;">Cambridge University Press and its affiliate, Cambridge HOTmaths, may occasionally send you additional product information.  Cambridge University Press and Cambridge HOTmaths respect your privacy and will not pass your details on to any third party, in accordance with our privacy policy.  This policy also contains information about how to access and seek correction to your personal data, or to complain about a breach of Australian Privacy Principles.</span></td>
      </tr>

      <tr>
        <td>&nbsp;</td>
        <td colspan="2">
          <div>
            <input type="checkbox" style="padding-right:0px;margin:0px;width:15px;border:0px" name="AllowMarketingContact" id="AllowMarketingContact" value="Y" />
            I do not wish to receive promotional material by email
          </div>
        </td>         
      </tr>
 
      <tr>
        <td>&nbsp;</td>
        <td colspan="2">
          <div id='acceptterms_message_div'>
            <input type="checkbox" style="padding-right:0px;margin:0px;width:15px;border:0px" name="acceptterms" id="acceptterms" value="Y" />
            You must accept the <a href="http://cambridge.edu.au/go/terms/?s=1" target = "_blank">Terms Of Use</a> to register.
          </div>
        </td>         
      </tr>

      <tr>
        <td colspan="2" >&nbsp;</td>
      </tr>

      <tr>
        <td>&nbsp;</td>
        <td colspan="2" style="text-align:right;padding-top:5px;">	
            <a id="continue" >Continue</a>
        </td>
      </tr>
      
    </table>
    </form>
    <?php } ?>
</div>
    
