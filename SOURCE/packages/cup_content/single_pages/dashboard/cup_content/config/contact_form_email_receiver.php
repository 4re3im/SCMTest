<?php 
	defined('C5_EXECUTE') or die(_("Access Denied."));
	$ih = Loader::helper('concrete/interface');
 
	$form = Loader::helper('form');
?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Contact Form Email Receiver'), false);?>
<?php Loader::packageElement('alert_message_header', 'cup_content'); ?>
<form method="post" action="<?php echo $this->url('/dashboard/cup_content/config/contact_form_email_receiver')?>">

<div>
	<div style="width:1px;height:30px;"></div>
	<table>
		<tbody>
			<tr>
				<td style="font-size: 14px;padding-bottom: 10px;padding-right: 5px;">Contact Form Receiver Email:</td>
				<td style="padding-bottom: 10px;">
					<?php echo $form->text('email_address', $store_value);?>
				</td>
			</tr>
		</tbody>
	</table>

</div>

<a href="<?php echo $this->url('/dashboard/cup_content/config')?>" class="btn"><?php echo t('Back')?></a>
<input type="submit" class="ccm-button-right btn primary accept" value="<?php echo t('Save')?>"/>
</form>

<div style="clear:both"></div>
<?php  echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(true);?>
