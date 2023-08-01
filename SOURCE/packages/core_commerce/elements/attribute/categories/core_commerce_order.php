<?php 
if (is_object($key)) {
	$orakIsRequired = $key->isOrderAttributeKeyRequired();
} else {
	$orakIsRequired = 1;
}
?>
<?php  $form = Loader::helper('form'); ?>

<fieldset>
<legend><?php echo t('Order Attribute Options')?></legend>
<div class="clearfix">
<label></label>
<div class="input">
<ul class="inputs-list">
	<li><label><?php echo $form->checkbox('orakIsRequired', 1, $orakIsRequired)?> <span><?php echo t('Required when checking out.');?></span></label></li>
</ul>
</div>
</div>

</fieldset>