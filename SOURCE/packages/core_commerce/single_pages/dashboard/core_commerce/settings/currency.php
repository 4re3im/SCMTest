<?php 
$settings = Page::getByPath('/dashboard/core_commerce/settings', 'ACTIVE');
?>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Currency Settings'), false, false, false, array(), $settings);?>
<?php 
$this->addHeaderItem(Loader::helper('html')->css('ccm.core.commerce.dashboard.css', 'core_commerce'));
$ih = Loader::helper('concrete/interface');
$valt = Loader::helper('validation/token');
$pkg = Package::getByHandle('core_commerce');
$form = Loader::helper('form');

?>
<form method="post" action="<?php echo $this->action('save_currency')?>" class="form-stacked" id="ccm-core-commerce-user-settings-form">
<div class="ccm-pane-body">
	<?php  
	$pkg = Package::getByHandle('core_commerce'); ?>

<div class="clearfix" id="ccm-core-commerce-currency-selector">
<label><?php echo t('Currency')?></label>
<div class="input">
	<ul class="inputs-list">
	<li><label><?php echo $form->radio('USE_ZEND_CURRENCY', 1, $pkg->config('USE_ZEND_CURRENCY'))?> <span><?php echo t('Automatically Format Currency')?></span></label></li>
	<li><label><?php echo $form->radio('USE_ZEND_CURRENCY', 0, $pkg->config('USE_ZEND_CURRENCY'))?> <span><?php echo t('Manually Format Currency')?></span></label></li>
	</ul>
</div>
</div>

	
<div id="ccm-core-commerce-auto-currency" style="<?php  echo ($pkg->config('USE_ZEND_CURRENCY')?"":"display: none;");?>">
<?php 
	$sampleAmount = 5422.55;
	Loader::library('3rdparty/Zend/Currency');
	?>
	<div><label><?php echo t('Example')?>:</label> <?php  $cur = new Zend_Currency(ACTIVE_LOCALE); echo $cur->toCurrency($sampleAmount); ?></div>
	<?php  
	if(Loader::helper('multilingual','core_commerce')->isEnabled()) {
		
		$fh = Loader::helper('interface/flag','multilingual');
		Loader::model('section','multilingual');
		$mlist = new MultilingualSection();
		$languages = $mlist->getList();
		?><ul style="list-style: none;">
		<?php  foreach($languages as $lang) { ?>
			<li>
				<?php echo $fh->getFlagIcon($lang->getIcon())?> <?php  $cur = new Zend_Currency($lang->getLocale()); echo $cur->toCurrency($sampleAmount); ?>
				<div class="ccm-note"><?php echo $lang->getLanguageText($lang->getLocale())?> (<?php  echo $lang->getLocale()?>)</div><br/>
			</li>	
		<?php  } ?>
		</ul>
		
<?php  }  ?>
</div>
	
<div id="ccm-core-commerce-manual-currency" style="<?php  echo ($pkg->config('USE_ZEND_CURRENCY')?"display: none;":"");?>">
<?php 
$currency = $pkg->config('CURRENCY_SYMBOL'); if (empty($currency)) { $currency = '$'; } ?>
<?php  $leftPlacement = $pkg->config('CURRENCY_SYMBOL_LEFT_PLACEMENT');
	if ($leftPlacement != '0') {$leftPlacement = 1;}
?>
<?php  Loader::library('price', 'core_commerce'); ?>

<div class="clearfix">
<?php echo $form->label('CURRENCY_SYMBOL', t('Currency Symbol'))?>
<div class="input">
	<?php echo $form->text('CURRENCY_SYMBOL', $currency, array('style'=> 'width: 20px'))?>
</div>
</div>

<div class="clearfix">
<?php echo $form->label('CURRENCY_THOUSANDS_SEPARATOR', t('Thousands Separator'))?>
<div class="input">
	<?php echo $form->text('CURRENCY_THOUSANDS_SEPARATOR', CoreCommercePrice::getThousandsSeparator(), array('style'=> 'width: 20px'))?>
</div>
</div>

<div class="clearfix">
<?php echo $form->label('CURRENCY_DECIMAL_POINT', t('Decimal Symbol/Point'))?>
<div class="input">
	<?php echo $form->text('CURRENCY_DECIMAL_POINT', CoreCommercePrice::getDecimalPoint(), array('style'=> 'width: 20px'))?>
</div>
</div>

<div class="clearfix">
<label><?php echo t('Currency Symbol Location')?></label>
<div class="input">
	<ul class="inputs-list">
	<li><label><?php echo $form->radio('CURRENCY_SYMBOL_LEFT_PLACEMENT', 1, $leftPlacement)?> <span><?php echo t('Left')?></span></label></li>
	<li><label><?php echo $form->radio('CURRENCY_SYMBOL_LEFT_PLACEMENT', 0, $leftPlacement)?> <span><?php echo t('Right')?></span></label></li>
	</ul>
</div>
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

	$('#ccm-core-commerce-currency-selector input').change(function() {
		if($(this).val() == 1) {
			$('#ccm-core-commerce-manual-currency').hide();
			$('#ccm-core-commerce-auto-currency').show();
		} else {
			$('#ccm-core-commerce-manual-currency').show();
			$('#ccm-core-commerce-auto-currency').hide();
		}
	});

	
});

</script>
