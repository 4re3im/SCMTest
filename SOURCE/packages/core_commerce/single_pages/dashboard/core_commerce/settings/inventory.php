<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<?php 
$settings = Page::getByPath('/dashboard/core_commerce/settings', 'ACTIVE');
?>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Inventory Settings'), false, false, false, array(), $settings);?>

<form method="post" action="<?php echo $this->action('save_inventory')?>" class="form-stacked" id="ccm-core-commerce-inventory-settings-form">
<div class="ccm-pane-body">

<div class="clearfix">
<label><?php echo t('Allow Negative Inventory By Default')?></label>
<div class="input">
	<ul class="inputs-list">
	<li><label><?php echo $form->radio('NEGATIVE_QUANTITY', 1, $pkg->config('NEGATIVE_QUANTITY'))?> <span><?php echo t('Yes')?></span></label></li>
	<li><label><?php echo $form->radio('NEGATIVE_QUANTITY', 0, $pkg->config('NEGATIVE_QUANTITY'))?> <span><?php echo t('No')?></span></label></li>
	</ul>
</div>
</div>

<div class="clearfix">
<label><?php echo t('Automatic Inventory Management')?></label>
<div class="input">
	<ul class="inputs-list">
	<li><label><?php echo $form->radio('MANAGE_INVENTORY', 1, $pkg->config('MANAGE_INVENTORY'))?> <span><?php echo t('Yes')?></span></label></li>
	<li><label><?php echo $form->radio('MANAGE_INVENTORY', 0, $pkg->config('MANAGE_INVENTORY'))?> <span><?php echo t('No')?></span></label></li>
	</ul>
</div>
</div>

<div class="clearfix" id="ccm-core-commerce-manage-inventory-trigger-wrapper"> 
<label><?php echo t('Subtract inventory when the following occurs:')?></label>
<div class="input">
	<?php echo $form->select('MANAGE_INVENTORY_TRIGGER', array(
				'FINISHED' => t('Order clears the payment gateway'),
				'SHIPPED' => t('Order status changed to "shipped"'),
				'COMPLETED' => t('Order status changed to "completed"'),
			), $pkg->config('MANAGE_INVENTORY_TRIGGER'), array('class' => 'span6'))?>
</div>
</div>

</div>

<div class="ccm-pane-footer">
	<?php  echo $concrete_interface->submit(t('Save'),'Save','right', 'primary')?>
</div>
</form>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>
<script type="text/javascript">
$(function() {
	$("input[name=MANAGE_INVENTORY]").click(function() {
		ccm_updateManageInventorySettings($(this));
	});
	
	ccm_updateManageInventorySettings();
});

ccm_updateManageInventorySettings = function(obj) {
	if (!obj) {
		var obj = $("input[name=MANAGE_INVENTORY][checked=checked]");
	}
	if (obj.attr('value') == 1) {
		$("#MANAGE_INVENTORY_TRIGGER").attr('disabled' , false);
		$("#ccm-core-commerce-manage-inventory-trigger-wrapper").show();
	} else {
		$("#MANAGE_INVENTORY_TRIGGER").attr('disabled' , true);
		$("#ccm-core-commerce-manage-inventory-trigger-wrapper").hide();
	}
}
</script>
