<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php  $ih = Loader::helper('concrete/interface'); ?>
<?php  if ($this->controller->getTask() == 'view_detail') { ?>
	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Product Set'), false, 'span12 offset2', false)?>
	<form method="post" id="file_sets_edit" action="<?php echo $this->url('/dashboard/core_commerce/products/sets', 'edit_set')?>" onsubmit="return ccm_saveProductSetDisplayOrder()">
		<?php echo $form->hidden('prsDisplayOrder', '')?>
		<?php echo $validation_token->output('product-sets-edit');?>

	<div class="ccm-pane-body">
	
	<div class="clearfix">
	<ul class="tabs">
		<li class="active"><a href="javascript:void(0)" onclick="$('.tabs').find('li.active').removeClass('active');$(this).parent().addClass('active');$('.ccm-tab').hide();$('#ccm-tab-details').show()" ><?php echo t('Details')?></a></li>
		<li><a href="javascript:void(0)" onclick="$('.tabs').find('li.active').removeClass('active');$(this).parent().addClass('active');$('.ccm-tab').hide();$('#ccm-tab-products').show()"><?php echo t("Products in Set")?></a></li>
	</ul>
	</div>

	<div id="ccm-tab-details" class="ccm-tab">

		<?php 
		$u=new User();
		$delConfirmJS = t('Are you sure you want to permanently remove this product set?');
		?>
		
		<script type="text/javascript">
		deleteFileSet = function() {
			if (confirm('<?php echo $delConfirmJS?>')) { 
				location.href = "<?php echo $this->url('/dashboard/core_commerce/products/sets', 'delete', $prs->getProductSetID(),'?ccm_token='. Loader::helper('validation/token')->generate('delete_product_set'))?>";				
			}
		}
		</script>

		<div class="clearfix">
		<?php echo $form->label('prsName', t('Name'))?>
		<div class="input">
			<?php echo $form->text('prsName',$prs->getProductSetName(), array('class' => 'span5'));?>	
		</div>
		</div>


		<?php 
			echo $form->hidden('prsID',$prs->getProductSetID());
		?>
		
		</div>

	<div style="display: none" class="ccm-tab" id="ccm-tab-products">
				<?php 
		Loader::model("product/list", 'core_commerce');
		$cpl = new CoreCommerceProductList();
		$cpl->filterBySet($prs);
		$cpl->sortByProductSetDisplayOrder();
		$cpl->setItemsPerPage(-1);
		$products = $cpl->get();
		if (count($products) > 0) { ?>
		
			<?php echo $form->hidden('prspDisplayOrder', '')?>
			<?php echo $form->hidden('prsID', $prs->getProductSetID())?>
		
		<p><?php echo t('Click and drag to reorder the products in this set. New products added to this set will automatically be appended to the end.')?></p>
		<div class="ccm-spacer">&nbsp;</div>
		
		<ul class="ccm-core-commerce-set-product-list">
		
		<?php 

		foreach($products as $pr) { ?>
			
		<li id="productID_<?php echo $pr->getProductID()?>">
			<div>
				<?php echo $pr->outputThumbnail()?>				
				<span style="word-wrap: break-word"><?php echo $pr->getProductName()?></span>
			</div>
		</li>
			
		<?php  } ?>

			
		</ul>
		<?php  } else { ?>
			<p><?php echo t('There are no products in this set.')?></p>
		<?php  } ?>
	</div>
	</div>
	<div class="ccm-pane-footer">
		<a href="<?php echo $this->url('/dashboard/core_commerce/products/sets')?>" class="btn"><?php echo t('Back to List')?></a>
		<input type="submit" value="<?php echo t('Save')?>" class="btn primary ccm-button-v2-right" />
		<?php  print $ih->button_js(t('Delete'), "deleteFileSet()", 'right','error');?>
	</div>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>

	</form>

	<script type="text/javascript">
	
	ccm_saveProductSetDisplayOrder = function() {
		var fslist = $('.ccm-core-commerce-set-product-list').sortable('serialize');
		$('input[name=prsDisplayOrder]').val(fslist);
		return true;
	}
	
	$(function() {
		$(".ccm-core-commerce-set-product-list").sortable({
			cursor: 'move',
			opacity: 0.5
		});
		
	});
	
	</script>
		
	<style type="text/css">
	.ccm-core-commerce-set-product-list:hover {cursor: move}
	</style>


<?php  } else { ?>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Product Sets'), false, 'span12 offset2')?>

		<?php  if (count($productSets) > 0) { 

			
		foreach ($productSets as $prs) { ?>
		
			<div class="ccm-group">
				<a class="ccm-group-inner" href="<?php echo $this->action('view_detail', $prs->getProductSetID())?>" style="background-image: url(<?php echo ASSETS_URL_IMAGES?>/icons/group.png)"><?php echo $prs->getProductSetName()?></a>
			</div>
		
		
		<?php  }
		
		
		} else { ?>
		
			<p><?php echo t('No product sets found.')?></p>
		
		<?php  } ?>
		
		<Br/><br/>
		<h4><?php echo t('Add Product Set')?></h4>
			<form method="post" id="product-sets-add" action="<?php echo $this->action('add')?>">
			<?php echo $validation_token->output('add_set');?>
			<div class="clearfix">
			<?php echo $form->label('prsName', t('Name'))?>
			<div class="input">
				<?php echo $form->text('prsName', array("class" => "span4"));?>
				<?php  print $concrete_interface->submit(t('Add'), 'product-sets-add', 'left')?>
			</div>
			</div>
		</form>
	


	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(); ?>
		
<?php  } ?>	
