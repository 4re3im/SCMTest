<?php 
$form = Loader::helper('form');

$fixed_desc = "(e.g. 12.95)";
$percent_desc = "(e.g. 25 for 25%, not .25)";
?>

<fieldset>
<legend><?php echo t('Basic Discount Fields')?></legend>

<div class="clearfix">
	<?php echo $form->label('amount', t('Amount'))?>
	<div class="input">
		<?php echo $form->text('amount', $amount, array('class' => 'span4'))?>
		<span class="help-inline ccm-discount-description"><?php echo $mode=='percent'?$percent_desc:$fixed_desc?></span>
	</div>
</div>

<div class="clearfix">
	<?php echo $form->label('mode', t('Type'))?>
	<div class="input">
		<?php echo $form->select('mode', array(
		'fixed' => t('Fixed Amount Off Order'),
		'percent' => t('Percent Off Order')
	), $mode,
	array('onchange' => "var txt='".$percent_desc."';if($('#mode').val() != 'percent') txt='".$fixed_desc."';$('.ccm-discount-description').text(txt)"))?>
	</div>
</div>
</fieldset>