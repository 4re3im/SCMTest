<?php 
	defined('C5_EXECUTE') or die(_("Access Denied."));
	$ih = Loader::helper('concrete/interface');
 
	$form = Loader::helper('form');
?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('From Email Address'), false);?>
<?php Loader::packageElement('alert_message_header', 'cup_content'); ?>
<form method="post" action="<?php echo $this->url('/dashboard/cup_content/config/from_email_address')?>">

<div>
	<div style="width:1px;height:30px;"></div>
	<p>
		This is the email From attribute. Only verified email addresses are allowed to use Amazon SES service.
	</p>
	<table>
		<tbody>
			<tr>
				<td style="font-size: 14px;padding-bottom: 10px;padding-right: 5px;">From Email Address:</td>
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
