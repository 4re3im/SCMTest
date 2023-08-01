<?php   
$form = Loader::helper('form'); 
$ih = Loader::helper("concrete/interface");
$valt = Loader::helper('validation/token');

$salesTaxRateName = '';
$salesTaxRateIsEnabled = 0;
$salesTaxRateIncludeShipping = 0;

if (is_object($rate)) {
	$salesTaxRateName = $rate->getSalesTaxRateName();
	$salesTaxRateIsEnabled = $rate->isSalesTaxRateEnabled();
	$salesTaxRateAmount = $rate->getSalesTaxRateAmount();
	$salesTaxRateCountry = $rate->getSalesTaxRateCountry();
	$salesTaxRateStateProvince = $rate->getSalesTaxRateStateProvince();
	$salesTaxRatePostalCode = $rate->getSalesTaxRatePostalCode();
	$salesTaxRateIncludedInProduct = $rate->isSalesTaxIncludedInProduct();
	$salesTaxRateIncludeShipping = $rate->includeShippingInSalesTaxRate();
   $salesTaxRateSpecialSet = $rate->getSalesTaxRateSpecialSet();
	print $form->hidden('rateID', $rate->getSalesTaxRateID());
} else if ($this->controller->isPost()) {

} else {
	$salesTaxRateCountry = 'US';
}

$spreq = $form->getRequestValue('salesTaxRateStateProvince');
if ($spreq != false) {
	$salesTaxRateStateProvince = $spreq;
}
$creq = $form->getRequestValue('salesTaxRateCountry');
if ($creq != false) {
	$salesTaxRateCountry = $creq;
}

$co = Loader::helper('lists/countries');
$countries = array_merge(array('' => t('Choose Country')), $co->getCountries());

?>

<div class="ccm-core-commerce-sales-tax-location">

<fieldset>
<legend><?php echo t('Basics')?></legend>
<div class="clearfix">
<?php echo $form->label('salesTaxRateName', t('Name'))?>
<div class="input">
	<?php  echo $form->text('salesTaxRateName', $salesTaxRateName, array('class' => 'span4'))?>
	<span class="help-inline"><?php echo t('Required')?></span>
</div>
</div>

<div class="clearfix">
<?php echo $form->label('salesTaxRateIsEnabled', t('Enabled?'))?>
<div class="input">
	<?php  echo $form->select('salesTaxRateIsEnabled', array(
			'1' => t('Enabled'), 
			'0' => t('Disabled')
		), $salesTaxRateIsEnabled, array('class' => 'span4'));?>
	<span class="help-inline"><?php echo t('Required')?></span>
</div>
</div>

<div class="clearfix">
<?php echo $form->label('salesTaxRateAmount', t('Rate'))?>
<div class="input">
	<?php  echo $form->text('salesTaxRateAmount', $salesTaxRateAmount, array('style' => 'width: 50px'))?>%
	<span class="help-inline"><?php echo t('Required')?></span>
</div>
</div>

</fieldset>

<fieldset>
<legend><?php echo t('Location')?></legend>

<div class="clearfix">
<?php echo $form->label('salesTaxRateCountry', t('Country'))?>
<div class="input ccm-attribute-address-line ccm-attribute-address-country">
	<?php  echo $form->select('salesTaxRateCountry', $countries, $salesTaxRateCountry, array('class' => 'span4')); ?>
</div>
</div>

<div class="clearfix">
<?php echo $form->label('salesTaxRateStateProvinceSelect', t('State/Province'))?>
	<div class="ccm-attribute-address-line ccm-attribute-address-state-province input">
	<?php  echo $form->select('salesTaxRateStateProvinceSelect', array('' => t('Choose State/Province')), $salesTaxRateStateProvince, array('class' => 'span4', 'ccm-attribute-address-field-name' => 'salesTaxRateStateProvince'))?>
	<?php  echo $form->text('salesTaxRateStateProvinceText', $salesTaxRateStateProvince, array('style' => 'display: none', 'class' => 'span4', 'ccm-attribute-address-field-name' => 'salesTaxRateStateProvince'))?>
	</div>
</div>

<div class="clearfix">
<?php echo $form->label('salesTaxRatePostalCode', t('Postal Code'))?>
<div class="input">
<?php  echo $form->text('salesTaxRatePostalCode', $salesTaxRatePostalCode, array('style' => 'width: 90px'))?>
<span class="help-inline"><?php echo t('Optional')?></span>
</div>
</div>

</fieldset>

<fieldset>
<legend><?php echo t('Details')?></legend>

<div class="clearfix">
<?php echo $form->label('salesTaxRateIncludedInProduct', t('Tax Included in Price'))?>
<div class="input">
<?php   echo $form->select('salesTaxRateIncludedInProduct', array(
			'0' => t('No, bill sales tax during checkout'), 
			'1' => t('Yes, display sales tax but do not add it to order')
		), $salesTaxRateIncludedInProduct, array('class' => 'span6'));?>
</div>
</div>

<div class="clearfix">
<?php echo $form->label('salesTaxRateSpecialSet', t('Limit by Product Set?'))?>
<div class="input">
   <?php  
   Loader::model('product/set', 'core_commerce');
   $productSets = CoreCommerceProductSet::getList();

   $nameIDPairs = array('0'=> t('** No. Tax All Products'));
   foreach($productSets as $set) {
      $nameIDPairs[$set->getProductSetID()] = $set->getProductSetName();
   }
   ?>
   <?php  echo $form->select('salesTaxRateSpecialSet',$nameIDPairs,$salesTaxRateSpecialSet);?>

</div>
</div>

<div class="clearfix">
<?php echo $form->label('salesTaxRateIncludeShipping', t('Include Shipping in Tax'))?>
<div class="input">
<?php   echo $form->select('salesTaxRateIncludeShipping', array(
				'1' => t('Yes'), 
				'0' => t('No')
			), $salesTaxRateIncludeShipping);?>
</div>
</div>

</fieldset>


<?php  echo $valt->output('add_or_update_sales_tax_rate')?>


</div>
