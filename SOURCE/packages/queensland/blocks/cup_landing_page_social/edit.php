<?php
	defined('C5_EXECUTE') or die(_("Access Denied."));
	$form = Loader::helper('form');
	$al = Loader::helper('concrete/asset_library');
	
	$opts = array(
		"0" => "disable",
		"1" => "enable"
	);
?>

<div style="margin: 0px 20px">
	<div class="row">
		<?php echo $form->label('config[facebook]', 'Facebook');?>
		<?php echo $form->select('config[facebook]', $opts, $config["facebook"]);?>
	</div>
	<div class="row">
		<?php echo $form->label('config[facebook_url]', 'Facebook URL');?>
		<?php echo $form->text('config[facebook_url]', $config["facebook_url"]);?>
	</div>
	
	<div class="row">
		<?php echo $form->label('config[linkedin]', 'LinkedIn');?>
		<?php echo $form->select('config[linkedin]', $opts, $config["linkedin"]);?>
	</div>
	<div class="row">
		<?php echo $form->label('facebook_url', 'LinkedIn URL');?>
		<?php echo $form->text('config[linkedin_url]', $config["linkedin_url"]);?>
	</div>
	
	<div class="row">
		<?php echo $form->label('config[erc]', 'Education Resource Consultant');?>
		<?php echo $form->select('config[erc]', $opts, $config["erc"]);?>
	</div>
	
	<div class="row">
		<?php echo $form->label('config[addthis]', 'AddThis');?>
		<?php echo $form->select('config[addthis]', $opts, $config["addthis"]);?>
	</div>
</div>