<?php 
$discount = $this->controller->getDiscount();

$form = Loader::helper('form');
$fixed_desc = "(e.g. 12.95)";
$percent_desc = "(e.g. 25 for 25%, not .25)";
?>

<script type="text/javascript">
$(function() {
	ccm_coreCommerceSetupDiscountFilters = function() {
		$("#ccm-core-commerce-discount-product-sets").hide();
		$("#ccm-core-commerce-discount-product").hide();
		var dpf = $("select[name=discountProductFilter]").val();
		switch(dpf) {
			case 'sets':
				$("#ccm-core-commerce-discount-product-sets").show();
				break;
			case 'products':
				$("#ccm-core-commerce-discount-product").show();
				break;
			
		}
	}
	
	$("select[name=discountProductFilter]").change(function() {
		ccm_coreCommerceSetupDiscountFilters();
	});
	
	ccm_coreCommerceSetupDiscountFilters();
});
</script>

<fieldset>
<legend><?php echo t('Product Discount Details')?></legend>

<div class="clearfix">
	<?php echo $form->label('discountProductFilter', t('Applies To'))?>
	<div class="input">
		<?php  print $form->select('discountProductFilter', array('' => t('All Products'), 'sets' => t('Products in specific set(s)'), 'products' => t('Specific Products')), $discountProductFilter); ?>
	</div>
</div>

<?php  
Loader::model('product/set', 'core_commerce');
$productSets = CoreCommerceProductSet::getList();
?>
<div id="ccm-core-commerce-discount-product-sets" style="display: none" class="clearfix">
	<?php  if (count($productSets) > 0) { ?>
	<label><?php echo t('Sets')?></label>
	<div class="input">
	<ul class="inputs-list">
	<?php  foreach($productSets as $prs) { ?>
		<li><label><?php echo $form->checkbox('prsID[' . $prs->getProductSetID() . ']', $prs->getProductSetID(), in_array($prs->getProductSetID(), $discountProductFilterProductSetIDs) )?> <span><?php echo $prs->getProductSetName()?></span></label></li>
	<?php  } ?>
	</ul>
	</div>
	<?php  } else { ?>
		<label></label>
		<div class="input">
		<p><?php echo t("No product sets created.")?></p>
		</div>
	<?php  } ?>
</div>
<div id="ccm-core-commerce-discount-product" style="display: none" class="clearfix">
<label><?php echo t('Choose Product(s)')?></label>
<div class="input">
<?php 
	$prh = Loader::helper('form/product', 'core_commerce'); 
	print $prh->selectMultiple('productID', $discountProductFilterProductIDs);
?>
</div>
</div>

<div class="clearfix">
	<?php echo $form->label('amount', t('Amount'))?>
	<div class="input">
		<?php echo $form->text('amount', $amount, array('class' => 'span4'))?>
		<span class="help-inline ccm-discount-description explanation"><?php echo $mode=='percent'?$percent_desc:$fixed_desc?></span>
	</div>
</div>

<div class="clearfix">
	<?php echo $form->label('mode', t('Type'))?>
	<div class="input">
		<?php echo $form->select('mode', array(
		'fixed' => t('Fixed Amount Off Any Matched Products'),
		'percent' => t('Percent Off Matched Products')
	), $mode,
	array('class' => 'span6', 'onchange' => "var txt='".$percent_desc."';if($('#mode').val() != 'percent') txt='".$fixed_desc."';$('.ccm-discount-description').text(txt)"))?>
	</div>
</div>


</fieldset>