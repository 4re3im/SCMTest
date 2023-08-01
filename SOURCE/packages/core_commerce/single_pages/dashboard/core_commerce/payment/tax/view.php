<?php  
if ($this->controller->getTask() == 'edit') { ?>


<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Edit Tax Rate'), false, 'span11 offset2', false)?>
<form method="post" action="<?php echo $this->action('edit')?>" id="ccm-core-commerce-sales-tax-form">
<div class="ccm-pane-body">

<?php 
$valt = Loader::helper('validation/token');
$ih = Loader::helper('concrete/interface');
$delConfirmJS = t('Are you sure you want to remove this rate?');
?>
<script type="text/javascript">
deleteRate = function() {
	if (confirm('<?php echo $delConfirmJS?>')) { 
		location.href = "<?php echo $this->url('/dashboard/core_commerce/payment/tax', 'delete', $rate->getSalesTaxRateID(), $valt->generate('delete_rate'))?>";				
	}
}
</script>
<?php  print $ih->button_js(t('Delete Rate'), "deleteRate()", 'right', 'error');?>

<?php  Loader::packageElement("sales/tax_form_required", 'core_commerce', array('rate' => $rate)); ?>

</div>
<div class="ccm-pane-footer">
	<a href="<?php echo $this->url('/dashboard/core_commerce/payment/tax')?>" class="btn"><?php echo t('Back to Tax Rates')?></a>
	<input type="submit" class="primary ccm-button-right btn" value="<?php echo t('Save')?>" />
</div>
</form>	
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>

<?php  
} else if ($this->controller->getTask() == 'add_rate') { ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Sales Tax Rates'), false, 'span11 offset2', false)?>

<form method="post" action="<?php echo $this->action('add_rate')?>" id="ccm-core-commerce-sales-tax-form">
<div class="ccm-pane-body">
<?php  Loader::packageElement("sales/tax_form_required", 'core_commerce'); ?>
</div>
<div class="ccm-pane-footer">
	<a href="<?php echo $this->url('/dashboard/core_commerce/payment/tax')?>" class="btn"><?php echo t('Back to Tax Rates')?></a>
	<input type="submit" class="primary ccm-button-right btn" value="<?php echo t('Add')?>" />
</div>
</form>	
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>

<?php  } else { ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Sales Tax Rates'), false, 'span10 offset3', false)?>

<div class="ccm-pane-body">

<?php  $rates = CoreCommerceSalesTaxRate::getList(); ?>
<?php  if (count($rates) == 0) { ?>
	<?php echo t('There are no available sales tax rates.')?>
<?php  } else { ?>
	<table class="grid-list zebra-striped">
	<tr>
		<th width="100%"><?php echo t('Name')?></th>
		<th><?php echo t('Enabled')?></th>
	</tr>
	<?php  foreach($rates as $rate) { ?>
		<tr>
			<td><a href="<?php echo $this->action('edit', $rate->getSalesTaxRateID())?>"><?php  if ($rate->getSalesTaxRateName()) { ?><?php echo $rate->getSalesTaxRateName()?><?php  } else { ?><?php echo t('(None)')?><?php  } ?></a></td>
			<td><?php echo $rate->isSalesTaxRateEnabled() ? t('Yes') : t('No')?></td>
		</tr>
	<?php  } ?>
	</table>
<?php  } ?>

</div>
<div class="ccm-pane-footer">

<?php 
	print $ih->button(t('Add Tax Rate'), $this->url('/dashboard/core_commerce/payment/tax', 'add_rate'), 'right', 'primary');		
?>

</div>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>

<?php  } ?>