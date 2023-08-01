<?php  
$ih = Loader::helper('concrete/interface');

if ($this->controller->getTask() == 'view_detail') { ?>

	<style type="text/css">
	.ccm-core-commerce-product-details ul {margin-left: 30px;}
	
	table.no-border {border: 0px !important;}
	table.no-border td {border: 0px !important;}
	</style>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('View Product'), false, false, false)?>
	<div class="ccm-pane-options ccm-pane-options-permanent-search">
			<?php echo $ih->button(t('Edit Properties'), $this->url('/dashboard/core_commerce/products/search', 'edit', $product->getProductID()), 'left')?>
			<?php echo $ih->button(t('Images'), $this->url('/dashboard/core_commerce/products/images', $product->getProductID()), 'left')?>
			<?php echo $ih->button(t('Customer Choices'), $this->url('/dashboard/core_commerce/products/options', 'view', $product->getProductID()), 'left')?>
			<?php  if (method_exists('AttributeKey', 'duplicate')) { ?>
				<form id="ccm-core-commerce-product-duplicate-form" style="display: inline" method="post" action="<?php echo $this->url('/dashboard/core_commerce/products/search', 'duplicate', $product->getProductID())?>">
				<input type="hidden" id="cc-parent-page" name="cParentID" value="" />
				
				<?php echo $ih->button_js(t('Duplicate'), 'javascript:ccm_coreCommerceAskAboutProductPage()', 'left')?>
				
				</form>
			<?php 
			}
			
			$valt = Loader::helper('validation/token');
			$ih = Loader::helper('concrete/interface');
			$delConfirmJS = t('Are you sure you want to remove this product?');
			$delPageConfirmJS = t('This product has a page associated in the sitemap. Do you want to remove that page as well?');
			?>
			<script type="text/javascript">
			deleteProduct = function() {
				var url = "<?php echo $this->url('/dashboard/core_commerce/products/search', 'delete_product', $product->getProductID(), $valt->generate('delete_product'))?>";
				<?php  if ($product->getProductCollectionID()>0) { ?>
				if (confirm('<?php echo $delPageConfirmJS?>')) {
					url = "<?php echo $this->url('/dashboard/core_commerce/products/search', 'delete_product', $product->getProductID(), $valt->generate('delete_product'),'delete_pages_too')?>";
				}
				<?php  } ?>
				if (confirm('<?php echo $delConfirmJS?>')) { 
					location.href = url;				
				}
			}
			</script>
			<?php  print $ih->button_js(t('Delete'), "deleteProduct()", 'right', 'error');?>
	</div>
	<div class="ccm-pane-body ccm-pane-body-footer">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="no-border">
	<tr>
		<td valign="top" style="padding-right: 10px"><ul class="media-grid"><li><a href="#" style="cursor: default"><?php echo $product->outputThumbnail()?></a></li></ul></td>
		<td valign="top" width="100%" class="ccm-core-commerce-product-details">
			
			<h3><?php echo $product->getProductName()?></h3>
			<p><?php echo $product->getProductDescription()?></p>
			
			<h3><?php echo t('Price')?></h3>
			<p><?php echo Loader::packageElement('product/price', 'core_commerce', array('product' => $product, 'displayDiscount' => true)); ?></p>	
		
		</td>
	</tr>
	</table>	
	</div>
	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>



	<script type="text/javascript">
	<?php  $uh = Loader::helper('urls', 'core_commerce'); ?>
		function ccm_coreCommerceAskAboutProductPage() {
            $.fn.dialog.open({
                href: "<?php echo $uh->getToolsURL('create_page')?>",
                title: "<?php echo t('Create a Product Page?')?>",
                width: 550,
                modal: true,
                appendButtons: true,
                onOpen:function(){},
                onClose: function(){$('#ccm-core-commerce-product-duplicate-form').get(0).submit()},
                height: 480
            });
		}
	</script>

<?php  } else if ($this->controller->getTask() == 'edit') { 

$valt = Loader::helper('validation/token');
?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Edit Product'), false, false, false)?>
<form method="post" id="ccm-core-commerce-product-update-form" action="<?php echo $this->url('/dashboard/core_commerce/products/search', 'edit')?>">
<div class="ccm-pane-body">
	<?php echo $valt->output('update_product')?>
	<?php  Loader::packageElement('product/form', 'core_commerce', array('product' => $product)); ?>
</div>
<div class="ccm-pane-footer">
	<a href="<?php echo $this->url('/dashboard/core_commerce/products/search', 'view_detail', $product->getProductID())?>" class="btn"><?php echo t('Cancel')?></a>
	<a href="javascript:void(0)" onclick="$('#ccm-core-commerce-product-update-form').get(0).submit()" class="ccm-button-right btn primary accept"><?php echo t('Save')?></a>
</div>	
</form>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>

<?php  } else { ?>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Product Search'), false, false, false)?>

	<div class="ccm-pane-options" id="ccm-<?php echo $searchInstance?>-pane-options">
	<div class="ccm-core-commerce-product-search-form"><?php  Loader::packageElement('product/search', 'core_commerce', array('searchType' => 'DASHBOARD')); ?></div>
	</div>
	<!--
	<a href="<?php echo $this->url('/dashboard/core_commerce/products/add')?>" class="ccm-button-left"><span><?php echo t('Add Product')?></span></a>
	//-->
	<?php  Loader::packageElement('product/search_results', 'core_commerce', array('products' => $products, 'productList' => $productList, 'pagination' => $pagination, 'searchType' => 'DASHBOARD')); ?>



	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>
	
<?php  } ?>
