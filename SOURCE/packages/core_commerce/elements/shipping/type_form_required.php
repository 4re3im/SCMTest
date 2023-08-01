<?php  
$form = Loader::helper('form'); 
$ih = Loader::helper("concrete/interface");
$valt = Loader::helper('validation/token');
$co = Loader::helper('lists/countries'); 
$countries = array_merge(array('' => t('Choose Country')), $co->getCountries());

if (isset($_POST['shippingTypeHasCustomCountriesSelected'])) {
	$shippingTypeHasCustomCountriesSelected = $_POST['shippingTypeHasCustomCountriesSelected'];
} else {
	$shippingTypeHasCustomCountriesSelected = $type->getShippingTypeCustomCountries();
}

if (isset($_POST['shippingTypeHasCustomCountries'])) {
	$shippingTypeHasCustomCountries = $_POST['shippingTypeHasCustomCountries'];
}  else {
	$shippingTypeHasCustomCountries = $type->hasShippingTypeCustomCountries();
} 

?>

<fieldset>
<legend><?php echo t('Details')?></legend>
<div class="clearfix">
<label><?php echo t('Handle')?></label>
<div class="input">
	<input type="text" disabled="disabled" class="span4" value="<?php echo $type->getShippingTypeHandle()?>" />
</div>
</div>

<div class="clearfix">
<label><?php echo t('Name')?></label>
<div class="input">
	<input type="text" disabled="disabled" class="span4" value="<?php echo $type->getShippingTypeName()?>" />
</div>
</div>
</fieldset>

<fieldset>
<legend><?php echo t('Availability')?></legend>
<div class="clearfix">
<?php echo $form->label('shippingTypeIsEnabled', t('Enabled'))?>
<div class="input"><?php 
		print $form->select('shippingTypeIsEnabled', array('0' => t('No'), '1' => t('Yes')), $type->isShippingTypeEnabled());
	?></div>
</div>

<div class="clearfix">
<label><?php echo t("Shipping Available To")?></label>
<div class="input">
	<ul class="inputs-list">
		<li><label><?php echo $form->radio('shippingTypeHasCustomCountries', 0, $type->hasShippingTypeCustomCountries())?> <span><?php echo t('All Available Countries')?></label></li>
		<li><label><?php echo $form->radio('shippingTypeHasCustomCountries', 1, $type->hasShippingTypeCustomCountries())?> <span><?php echo t('Selected Countries')?></label></li>
	</ul>
	
		<select id="shippingTypeHasCustomCountriesSelected" name="shippingTypeHasCustomCountriesSelected[]" multiple size="7" style="width:100%" disabled>
			<?php  foreach ($countries as $key=>$val) { ?>
				<?php  if (empty($key) || empty($val)) continue; ?>
				<option <?php echo (in_array($key, $shippingTypeHasCustomCountriesSelected) || $shippingTypeHasCustomCountries == 0 ?'selected ':'')?>value="<?php echo $key?>"><?php echo $val?></option>
			<?php  } ?>
		</select>
</div>
</div>

<?php echo $form->hidden('shippingTypeID', $type->getShippingTypeID())?>
<?php echo $valt->output('update_shipping_type')?>

<?php  $type->render('type_form'); ?>

<script type="text/javascript">
$(function() {
	$("input[name=shippingTypeHasCustomCountries]").click(function() {
		ccm_coreCommerceShippingTypeCountries($(this));
	});
	
	ccm_coreCommerceShippingTypeCountries();
});

ccm_coreCommerceShippingTypeCountries = function(obj) {
	if (!obj) {
		var obj = $("input[name=shippingTypeHasCustomCountries][checked=checked]");
	}
	if (obj.attr('value') == 1) {
		$("#shippingTypeHasCustomCountriesSelected").attr('disabled' , false);
	} else {
		$("#shippingTypeHasCustomCountriesSelected").attr('disabled' , true);
		$("#shippingTypeHasCustomCountriesSelected option").attr('selected', true);
	}
}

</script>