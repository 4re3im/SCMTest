<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));  

global $c;

$uh = Loader::helper('urls', 'core_commerce');
$im = Loader::helper('image');
if ($options['show_search_form']) {
	$this->inc('view_search_form.php', array( 'c'=>$c, 'b'=>$b, 'controller'=>$controller,'block_args'=>$block_args ) );
}
?>
<?php  if ($options['show_products'] || $_REQUEST['search'] == '1') { ?>
	<?php 
	$nh = Loader::helper('navigation');
	
	$productList = $this->controller->getRequestedSearchResults();
	$products = $productList->getPage();
	$paginator = $productList->getPagination();
	?>
	<?php  if(count($products)>0) { ?>
	
<!-- begin products -->

		<div class="ccm-core-commerce-product-list-container">
			
			
			<?php  if($paging['show_top'] || $paging['show_bottom']) {
				echo '<div class="ccm-core-commerce-summary">';
				$productList->displaySummary();
				echo '</div>';
			}
			?>				
			<?php 
				if (count($sort_columns)>0) { ?>
				<div>
					<div class="product-list-sort-header"><?php echo  t('Sort by:'); ?> <select class="product-list-sort-select">
					<?php 
					$current_col = $_REQUEST['ccm_order_by'];
					foreach ($sort_columns as $col => $name) {
						$selected = ($current_col == $col && $_REQUEST['ccm_order_dir'] == 'asc') ? "selected" : "";
						echo '<option '.$selected.' value="';
						$productList->getSortByURL($col, 'asc', $bu);
						echo '">'.$name.' ' . t('Ascending') . '</option>';
						$selected = ($current_col == $col && $_REQUEST['ccm_order_dir'] == 'desc') ? "selected" : "";
	
						echo '<option '.$selected.' value="';
						$productList->getSortByURL($col, 'desc', $bu);
						echo '">'.$name.' ' . t('Descending') . '</option>';
					}
					?></select>
	
					</div>
				</div>
					<?php 
				}  ?>
	
		<div style="clear: both"></div>
		<?php  if($paging['show_top'] && $paginator && strlen($paginator->getPages())>0){ ?>	
		<div class="pagination">	
			 <span class="pageLeft"><?php  echo $paginator->getPrevious(t('Previous'))?></span>
			 <?php  echo $paginator->getPages()?>
			 <span class="pageRight"><?php  echo $paginator->getNext(t('Next'))?></span>
		</div>

		<?php  } ?>
		
		<div class="ccm-core-commerce-product-list-results">

		<?php 
	
		for ($i = 0; $i < count($products); $i++) {	
			
			$pr = $products[$i];
			$args['product'] = $pr;	
			$args['id'] = $pr->getProductID() . '-' . $b->getBlockID();
			foreach($this->controller->getSets() as $key => $value) {
				$args[$key] = $value;
			}
			
			//print '<div>';
			//Loader::packageElement('product/display', 'core_commerce', $args);
			//print '</div>';
			
			print '<div class="ccm-core-commerce-product-list-product">';
			Loader::packageElement('../blocks/product/templates/plain/view', 'core_commerce', $args);
			print '</div>';
		}
	?>
		</div>
	</div>
	
	<?php 
		
	} else { ?>
		<?php echo  t('No products found'); ?>
	<?php  } ?>
<?php  }

if($paging['show_bottom'] && $paginator && strlen($paginator->getPages())>0){ ?>	
<div class="pagination">	
	 <span class="pageLeft"><?php  echo $paginator->getPrevious(t('Previous'))?></span>
	 <?php  echo $paginator->getPages()?>
	 <span class="pageRight"><?php  echo $paginator->getNext(t('Next'))?></span>
</div>	
<?php  } ?>

 
