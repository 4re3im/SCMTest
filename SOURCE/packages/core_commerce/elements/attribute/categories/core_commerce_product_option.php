<?php 
if (is_object($key)) {
	$poakIsRequired = $key->isProductOptionAttributeKeyRequired();
} else {
	$poakIsRequired = 1;
}
?>
<?php  $form = Loader::helper('form'); ?>

<fieldset>
<legend><?php echo t('Customer Choice Options')?></legend>
<div class="clearfix">
<label for="poakIsRequired"><?php echo t('Required to Purchase')?></label>
<div class="input">
<ul class="inputs-list">
	<li><label><?php echo $form->checkbox('poakIsRequired', 1, $poakIsRequired)?> <span><?php echo t('Required when adding product to cart.');?></span></label></li>
</ul>
</div>
</div>

</fieldset>