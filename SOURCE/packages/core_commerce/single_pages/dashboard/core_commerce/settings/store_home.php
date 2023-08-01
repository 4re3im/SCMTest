<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<?php  $settings = Page::getByPath('/dashboard/core_commerce/settings', 'ACTIVE'); ?>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Store Home Settings'), false, false, false, array(), $settings);?>

<form method="post" action="<?php echo $this->action('save_homepage')?>" class="form-stacked" id="ccm-core-commerce-inventory-settings-form">
	<div class="ccm-pane-body">

		<div class="clearfix" id="ccm-core-commerce-manage-inventory-trigger-wrapper">
			<label for='STORE_ROOT'><?php echo t('Select your store\'s Homepage')?></label>
			<span class='help-block'><?php echo t('This will be used for "Return to Shopping" links.')?></span>
			<div class="input">
				<?php  $page_selector = Loader::helper('form/page_selector'); ?>
				<?php echo $page_selector->selectPage('STORE_ROOT', $pkg->config('STORE_ROOT'));?>
			</div>
		</div>

	</div>

	<div class="ccm-pane-footer">
		<?php  echo $concrete_interface->submit(t('Save'),'Save','right', 'primary')?>
	</div>
</form>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>
