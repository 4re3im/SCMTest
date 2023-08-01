<?php  
$o = CoreCommerceCurrentOrder::get();
$form_attribute->setAttributeObject($o);
$txt = Loader::helper('text');

$steps = $this->controller->getSteps();
$page = Page::getByPath($steps[0]->getPath());
?>
<script type="text/javascript">
<!--
$(function() {
	ccmSinglePageCheckout.init('<?php  echo $txt->sanitizeFileSystem($steps[0]->getPath())?>');
});
//-->
</script>
<div id="ccm-core-commerce-checkout-cart" class="ccm-core-commerce-checkout-singlepage">
	<?php  Loader::packageElement('cart_item_list', 'core_commerce', array('edit' => false))?>
	<div style="height: 800px;">
		<?php 
			$i=0;
			foreach( $steps as $step) {
				$page = Page::getByPath($step->getPath());
				if($page instanceof Page) { ?>
					<h2 onclick="ccmSinglePageCheckout.toggleStep('<?php  echo $txt->sanitizeFileSystem($step->getPath()) ?>');"><?php  echo $page->getCollectionName(); ?></h2>
					<div id="ccm-core-commerce-checkout-form-<?php  echo $txt->sanitizeFileSystem($step->getPath()) ?>" 
						class="ccm-core-commerce-checkout-form" <?php echo  ($i==0?'':'style="display:none;"') ?>>
						<div class="ccm-error"></div>
						<?php  
						$this->controller->checkoutStepHelper->setCurrentPagePath($step->getPath());
						Loader::packageElement($step->getPath(),
								'core_commerce',
								array('o'=>$o,'form'=>$form,'form_attribute'=>$form_attribute, 'akHandles'=>$akHandles, 'action'=>$step->getSubmitURL(1))
							); ?>
						</div>
					<?php  
					$i++;
				}
			}			
		?>
	</div>
</div>