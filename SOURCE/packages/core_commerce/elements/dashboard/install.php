<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
?>

<h4><?php echo t('Install Sample Content')?></h4>

<?php  $form = Loader::helper('form'); ?>
<p><?php echo t('Install eCommerce sample content? If this is your first time working with eCommerce, you might want to install the sample content. You can remove it later.')?></p>

<div class="clearfix">
	<label><?php echo t('Sample Content')?></label>
	<div class="input">
	<ul class="inputs-list">
		<li><label><input type="radio" name="coreCommerceInstallContent" value="yes" checked="checked" /> <span><?php echo t('Yes, install the sample content')?></span></label></li>
		<li><label><input type="radio" name="coreCommerceInstallContent" value="no" /> <span><?php echo t('No, do not install the sample content.')?></span></label></li>
	</ul>
	</div>
</div>
