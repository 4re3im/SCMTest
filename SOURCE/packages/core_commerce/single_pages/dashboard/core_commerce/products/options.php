	<?php  if (is_object($product)) { ?>
		<?php  $title = t('Product Customer Choices'); ?>
	<?php  } else { ?>
		<?php  $title = t('Global Customer Choices'); ?>
	<?php  } ?>


<?php  if (isset($key)) { ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Edit Attribute'), false, false, false)?>
<form method="post" action="<?php echo $this->action('edit')?>" id="ccm-attribute-key-form">

<?php  Loader::element("attribute/type_form_required", array('category' => $category, 'type' => $type, 'key' => $key)); ?>

</form>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>

<?php  } else if ($this->controller->getTask() == 'select_type' || $this->controller->getTask() == 'add' || $this->controller->getTask() == 'edit') { ?>



	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper($title, false, false, false)?>
	
	<?php  if (isset($type)) { ?>
		<form method="post" action="<?php echo $this->action('add')?>" id="ccm-attribute-key-form">
		<?php  if (is_object($product)) { ?>
			<?php echo $form->hidden('productID', $product->getProductID())?>
		<?php  } ?>
		<?php  Loader::element("attribute/type_form_required", array('category' => $category, 'type' => $type)); ?>
		</form>	
	<?php  } ?>
	
	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>



<?php  } else { ?>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper($title, false, false, false)?>

	<?php 
	$attribs = CoreCommerceProductOptionAttributeKey::getList($product);
//	Loader::element('dashboard/attributes_table', array('category' => $category, 'attribs'=> $attribs, 'editURL' => '/dashboard/core_commerce/products/options')); ?>
	
	
	<div class="ccm-pane-body">
	<?php  if (is_object($product)) { ?>
		<div class="alert-message clearfix block-message info">
		<h3><?php echo t('Product')?></h3>
		<p><?php echo $product->getProductName()?></p>
		</div>
	<?php  } ?>
	
		<div class="ccm-attributes-list">
		
		<?php  
		if (count($attribs) > 0) { 
		foreach($attribs as $ak) { ?>
		<div class="ccm-attribute" id="akID_<?php  echo $ak->getAttributeKeyID()?>">
			<img class="ccm-attribute-icon" src="<?php  echo $ak->getAttributeKeyIconSRC()?>" width="16" height="16" />
			<?php  if (is_object($product) && $ak->getProductID() == 0) { ?>
			<a href="<?php  echo $this->action('deassociate_global_product_option', $product->getProductID(), $ak->getAttributeKeyID())?>"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" style="float: right" /></a>
			<?php  echo $ak->getAttributeKeyName()?>
			<?php  } else { ?>
			<a href="<?php  echo $this->url('/dashboard/core_commerce/products/options', 'edit', $ak->getAttributeKeyID())?>"><?php  echo $ak->getAttributeKeyName()?></a>
			<?php  } ?>
		</div>
		
		<?php  } ?>
		
		<?php  } else { ?>
			<p><?php echo t('No customer choices found.')?></p>
		<?php  } ?>
		
		
		</div>


</div>
	
	<?php  if (is_object($product)) { ?>
	
	<script type="text/javascript">
	$(function() {
		$("div.ccm-attributes-list").sortable({
			handle: 'img.ccm-attribute-icon',
			cursor: 'move',
			opacity: 0.5,
			stop: function() {
				var ualist = $(this).sortable('serialize');
				$.post('<?php echo $this->action("update_choice_order", $product->getProductID())?>', ualist, function(r) {
	
				});
			}
		});
	});
	
	</script>
	
	<style type="text/css">
	div.ccm-attributes-list img.ccm-attribute-icon:hover {cursor: move}
	</style>
	
	<?php  } ?>

	<div class="ccm-pane-body ccm-pane-body-footer" style="margin-top: -25px">
	
	<?php  if (count($globalOptions) > 0) { ?>
		
		<form method="post" class="form-stacked inline-form-fix" action="<?php echo $this->action('associate_global_product_option')?>">
		<div class="clearfix">
		<?php echo $form->label('akID', t('Add a Shared Product Option'))?>
		<div class="input">
			<?php echo $form->hidden('productID', $product->getProductID())?>
			<?php echo $form->select('akID', $globalOptions)?>
			<?php echo $form->submit('submit', t('Add'))?>
		</div>
		</div>
		
		</form>
	<?php  } ?>

	<form method="get" class="form-stacked inline-form-fix" action="<?php echo $this->action('select_type')?>" id="ccm-attribute-type-form">

	<div class="clearfix">
	<?php echo $form->label('atID', t('Add New Option'))?>
	<div class="input">
	
	<?php echo $form->select('atID', $types)?>
	<?php echo $form->submit('submit', t('Add'))?>
	<?php  if (is_object($product)) { ?>
		<?php echo $form->hidden('productID', $product->getProductID())?>
	<?php  } ?>
	
	</div>
	</div>
	
	</form>

	</div>
	
	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>

<?php  } ?>