<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<?php 
$settings = Page::getByPath('/dashboard/core_commerce/settings', 'ACTIVE');
?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Order Settings'), false, false, false, array(), $settings);?>

<form method="post" action="<?php echo $this->action('save_order_settings')?>" class="form-stacked" id="ccm-core-commerce-ssl-settings-form">
<div class="ccm-pane-body">

<div class="clearfix">
<label><?php echo t('Use SSL for Checkout')?></label>
<div class="input">
	<ul class="inputs-list">
	<li><label><?php echo $form->radio('SECURITY_USE_SSL', 'true', $use_ssl == 'true')?> <span><?php echo t('Yes')?></span></label></li>
	<li><label><?php echo $form->radio('SECURITY_USE_SSL', 'false', $use_ssl != 'true')?> <span><?php echo t('No')?></span></label></li>
	</ul>
</div>
</div>

<?php  $style = ($use_ssl == 'true' ? '' : 'style="display:none"'); ?>
<?php  $base_url = preg_replace('/http:/', 'https:', $base_url_ssl) ?>

<div class="clearfix"  <?php echo $style?>>
<label for="BASE_URL_SSL"><?php echo t('Base SSL URL')?></label>
<div class="input">
	<input class="ccm-input-text cc-ssl-base" id="BASE_URL_SSL" type="text" name="BASE_URL_SSL" value="<?php echo $base_url_ssl?>"/>
</div>
</div>

<div class="clearfix">
<label><?php echo t('Force Users to Login Before Checkout')?></label>
<div class="input">
	<ul class="inputs-list">
	<li><label><?php echo $form->radio('CHECKOUT_FORCE_LOGIN', 1, $CHECKOUT_FORCE_LOGIN)?> <span><?php echo t('Yes')?></span></label></li>
	<li><label><?php echo $form->radio('CHECKOUT_FORCE_LOGIN', 0, $CHECKOUT_FORCE_LOGIN)?> <span><?php echo t('No')?></span></label></li>
	</ul>
</div>
</div>

<div class="clearfix">
<label><?php echo t('Minimum Order Amount')?></label>
<div class="input">
	<ul class="inputs-list">
	<li><label><?php echo $form->radio('ORDER_TOTAL_ENABLE_MINIMUM', '1', $order_total_enable_minimum)?> <span><?php echo t('Yes')?></span></label></li>
	<li><label><?php echo $form->radio('ORDER_TOTAL_ENABLE_MINIMUM', '0', $order_total_enable_minimum)?> <span><?php echo t('No')?></span></label></li>
	</ul>
</div>
</div>

<div class="clearfix" id="ccm-core-commerce-order-total-minimum-amount" <?php  if (!$order_total_enable_minimum) { ?>style="display: none"<?php  } ?>">
<label for="ORDER_TOTAL_MINIMUM_AMOUNT"><?php echo t('Minimum Amount')?></label>
<div class="input">
<?php echo $form->text('ORDER_TOTAL_MINIMUM_AMOUNT', $order_total_minimum_amount)?>
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

	$('#SECURITY_USE_SSL1,#SECURITY_USE_SSL2').change(function(){$('.cc-ssl-base').toggle()});
	$("input[name=ORDER_TOTAL_ENABLE_MINIMUM]").click(function() {
		ccm_updateOrderTotalMinimumSettings($(this));
	});
	

});

ccm_updateOrderTotalMinimumSettings = function(obj) {
	if (!obj) {
		var obj = $("input[name=ORDER_TOTAL_ENABLE_MINIMUM][checked=checked]");
	}
	if (obj.attr('value') == 1) {
		$("#ORDER_TOTAL_MINIMUM_AMOUNT").attr('disabled' , false);
		$("#ccm-core-commerce-order-total-minimum-amount").show();
	} else {
		$("#ORDER_TOTAL_MINIMUM_AMOUNT").attr('disabled' , true);
		$("#ccm-core-commerce-order-total-minimum-amount").hide();
	}
}

</script>