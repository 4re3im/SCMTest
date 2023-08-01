<?php 
$settings = Page::getByPath('/dashboard/core_commerce/settings', 'ACTIVE');
?>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('User Settings'), false, false, false, array(), $settings);?>
<?php 
$this->addHeaderItem(Loader::helper('html')->css('ccm.core.commerce.dashboard.css', 'core_commerce'));
$ih = Loader::helper('concrete/interface');
$valt = Loader::helper('validation/token');
$pkg = Package::getByHandle('core_commerce');
$form = Loader::helper('form');

?>
<form method="post" action="<?php echo $this->action('save_user')?>" class="form-stacked" id="ccm-core-commerce-user-settings-form">
<div class="ccm-pane-body">
<div class="clearfix">
<label><?php echo t('Enable Order History in User Profiles')?></label>
<div class="input">
	<ul class="inputs-list">
	<li><label><?php echo $form->radio('PROFILE_MY_ORDERS_ENABLED', '1', $pkg->config('PROFILE_MY_ORDERS_ENABLED'))?> <span><?php echo t('Yes')?></span></label></li>
	<li><label><?php echo $form->radio('PROFILE_MY_ORDERS_ENABLED', '0', $pkg->config('PROFILE_MY_ORDERS_ENABLED'))?> <span><?php echo t('No')?></span></label></li>
	</ul>
</div>
</div>

<div class="clearfix">
<label><?php echo t('Enable Wishlists')?></label>
<div class="input">
	<ul class="inputs-list">
	<li><label><?php echo $form->radio('WISHLISTS_ENABLED', '1', $pkg->config('WISHLISTS_ENABLED'))?> <span><?php echo t('Yes')?></span></label></li>
	<li><label><?php echo $form->radio('WISHLISTS_ENABLED', '0', $pkg->config('WISHLISTS_ENABLED'))?> <span><?php echo t('No')?></span></label></li>
	</ul>
</div>
</div>

<div class="clearfix">
<label><?php echo t('Enable Gift Registries')?></label>
<div class="input">
	<ul class="inputs-list">
	<li><label><?php echo $form->radio('GIFT_REGISTRIES_ENABLED', '1', $pkg->config('GIFT_REGISTRIES_ENABLED'))?> <span><?php echo t('Yes')?></span></label></li>
	<li><label><?php echo $form->radio('GIFT_REGISTRIES_ENABLED', '0', $pkg->config('GIFT_REGISTRIES_ENABLED'))?> <span><?php echo t('No')?></span></label></li>
	</ul>
</div>
</div>

</div>

<div class="ccm-pane-footer">
	<?php  echo $concrete_interface->submit(t('Save'),'Save','right', 'primary')?>
</div>
</form>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>