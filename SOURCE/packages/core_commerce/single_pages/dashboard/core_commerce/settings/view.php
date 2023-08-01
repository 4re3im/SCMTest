<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('eCommerce Settings'))?>

<div class="row">
	
	<div class="span-pane-half">
	<h3><?php echo t('Settings')?></h3>
	
<?php 
	$show = array();
	$subcats = $c->getCollectionChildrenArray(true);
	foreach($subcats as $catID) {
		$subcat = Page::getByID($catID, 'ACTIVE');
		$catp = new Permissions($subcat);
		if ($catp->canRead() && $subcat->getAttribute('exclude_nav') != 1) { 
			$show[] = $subcat;
		}
	}
	
	if (count($show) > 0) { ?>
	
	<div class="ccm-dashboard-system-category-inner">
	
	<?php  foreach($show as $subcat) { ?>
	
	<div>
	<a href="<?php echo Loader::helper('navigation')->getLinkToCollection($subcat)?>"><?php echo t($subcat->getCollectionName())?></a>
	</div>
	
	<?php  } ?>
	
	</div>

	<?php  } ?>
	</div>
	
	<div class="span-pane-half">
	<h3><?php echo t('Advanced')?></h3>
	
	<div class="ccm-dashboard-system-category-inner">
	
	<div><a href="<?php echo $this->url('/dashboard/core_commerce/products/attributes')?>"><?php echo t('Product Attributes')?></a></div>
	<div><a href="<?php echo $this->url('/dashboard/core_commerce/orders/attributes')?>"><?php echo t('Order Attributes')?></a></div>
	<div><a href="<?php echo $this->url('/dashboard/core_commerce/products/options')?>"><?php echo t('Customer Choices')?></a></div>
	
	
	</div>


</div>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>
