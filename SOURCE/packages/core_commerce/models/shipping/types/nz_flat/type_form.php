<?php  $form = Loader::helper('form'); ?>
<div class="clearfix">
<?php echo $form->label('SHIPPING_TYPE_FLAT_BASE', t('Starting Rate'))?>
<div class="input">
	<?php echo $form->text('SHIPPING_TYPE_FLAT_BASE', $SHIPPING_TYPE_FLAT_BASE)?>
</div>
</div>

<div class="clearfix">
<?php echo $form->label('SHIPPING_TYPE_FLAT_PER_ITEM', t('Per Item'))?>
<div class="input">
	<?php echo $form->text('SHIPPING_TYPE_FLAT_PER_ITEM', $SHIPPING_TYPE_FLAT_PER_ITEM)?>
	<span class="help-inline"><?php echo t('An additional per item cost, above the base charge.')?></span>
</div>
</div>