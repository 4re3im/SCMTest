<?php 
	defined('C5_EXECUTE') or die(_("Access Denied."));
	$ih = Loader::helper('concrete/interface');
 
	$form = Loader::helper('form');
?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Email Settings'), false);?>
<?php Loader::packageElement('alert_message_header', 'cup_competition'); ?>
<form method="post" action="<?php echo $this->url('/dashboard/cup_competition/config/email_settings')?>">

<div>
	<table>
		<tr>
			<td style="padding: 5px">HSC notification Email</td>
			<td style="padding: 5px"><?php echo $form->text('hsc_email', $hsc_email);?></td>
		</tr>
		<tr>
			<td style="padding: 5px">VCE notification Email</td>
			<td style="padding: 5px"><?php echo $form->text('vce_email', $vce_email);?></td>
		</tr>
	</table>
</div>

<a href="<?php echo $this->url('/dashboard/cup_competition/config')?>" class="btn"><?php echo t('Back')?></a>
<input type="submit" class="ccm-button-right btn primary accept" value="<?php echo t('Save')?>"/>
</form>

<div style="clear:both"></div>
<?php  echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(true);?>
