<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<?php  
$form = Loader::helper('form');

extract($controller->block_args);

//data sources
$data_sources = array();
$data_sources['directly_under_this_page'] = t('Directly under this page');
$data_sources['under_parent_page'] = t('Directly under parent page');
//$data_sources['anywhere_under_this_page'] = t('Anywhere under this page');
$data_sources['all_products'] = t("Entire inventory");
$data_sources['stored_search_query'] = t('A stored search query');
$data_sources['top_purchased_products'] = t('Top purchased products');
$data_sources['top_visited_products'] = t('Top visited products');
$data_sources['products_on_sale'] = t('Products on Sale');
$data_sources['user_visited_products'] = t('Breadcrumb of products seen');


//anywhere under this page options
$levels = range(1,10);
$levels = array_combine($levels,$levels);

//search options
$search = $controller->block_args;
if (!is_array($search['selectedSearchField'])) $search['selectedSearchField'] = array();
$searchFields = array(
	//'' => '** ' . t('Fields'),
	//'date_added' => t('Created Between')
	'' => t('Select Search Field.'),
);
$uh = Loader::helper('concrete/urls');
Loader::model('attribute/categories/core_commerce_product', 'core_commerce');
$searchFieldAttributes = CoreCommerceProductAttributeKey::getSearchableList();
$validAttributes = array();
//filter unsopported attributes untill we fix them
foreach($searchFieldAttributes as $ak) {
	$type = $ak->getAttributeKeyType()->getAttributeTypeHandle();
	if (!in_array($type,array('text','textarea','boolean','number','select'))) continue;
	$validAttributes[] = $ak;
	$searchFields[$ak->getAttributeKeyID()] = $ak->getAttributeKeyName();
}
$_POST = $search;
$_REQUEST  = $search;

?>
<div class="ccm-block-field-group">
<h4><?php echo  t('Data Source') ?></h4>
<?php echo  t('Get products from'); ?>: <?php echo  $form->select('data_source',$data_sources,$data_source); ?>
<div class="product-list-data-source-pane" id="product-list-data-source-directly_under_this_page">
	<?php  //Directly under this page ?>
</div>
<?php  
$mls = Loader::helper('multilingual', 'core_commerce');
if($mls->isEnabled()) {?>
<br/>
<div class="ccm-block-field-group">
	<?php echo  t('In language'); ?>: <?php 
		$languages = $mls->getSectionSelectArray(t('Default Language'));
		echo $form->select('language',$languages,$language);
	?>
</div>
<?php  } ?>
<div class="product-list-data-source-pane" id="product-list-data-source-anywhere_under_this_page">
	<?php echo  t('How many levels down') ?>: <?php echo  $form->select('levels_under_this_page',$levels,$levels_under_this_page); ?>
</div>

<div class="product-list-data-source-pane" id="product-list-data-source-stored_search_query">
	<h3><?php echo  t('Only display products matching the following search criteria') ?></h3>
		<?php // echo $form->hidden('mode', $mode); ?>
		<div id="ccm-core-commerce-product-search-advanced-fields">		
			<input type="hidden" name="search" value="1" />
			<div id="ccm-search-advanced-fields-inner">
				<div class="ccm-search-field">
						<?php echo $form->label('keywords', t('Keywords'))?>
					<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="100%">
						<?php echo $form->text('keywords', $search['keywords'], array('style' => 'width:390px')); ?>
						</td>
						<td>
						<a href="javascript:void(0)" id="ccm-core-commerce-product-search-add-option"><img  src="<?php echo ASSETS_URL_IMAGES?>/icons/add.png" style="margin-left: 4px; vertical-align: middle" width="16" height="16" /></a>
						</td>
					</tr>
					</table>
					<Br/>
				</div>
				<div class="ccm-search-field">
						<?php echo $form->label('price', t('Price'))?>
					<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td><?php echo t('From')?></td>
						<td>&nbsp;&nbsp;</td>
						<td>
						<?php echo $form->text('price_from', $search['price_from'], array('style' => 'width:140px')); ?>
						</td>
						<td>&nbsp;&nbsp;</td>
						<td><?php echo t('To')?></td>
						<td>&nbsp;&nbsp;</td>
						<td>
						<?php echo $form->text('price_to', $search['price_to'], array('style' => 'width:140px')); ?>
						</td>
					</tr>
					</table>
					<Br/>
				</div>
				<?php  
				Loader::model('product/set', 'core_commerce');
				$productSets = CoreCommerceProductSet::getList();
				?>
				<div class="ccm-search-field">
					<label><?php echo t('Filter by Set')?></label>
					<?php  if (count($productSets) > 0) { ?>

					<?php  foreach($productSets as $prs) { ?>
						<div class="ccm-product-search-set"><?php echo $form->checkbox('prsID[' . $prs->getProductSetID() . ']', $prs->getProductSetID() )?> <?php echo $form->label('prsID[' . $prs->getProductSetID()  . ']', $prs->getProductSetName() )?></div>
					<?php  } ?>

					<?php  } else { ?>
						<?php echo t("No product sets created.")?>
					<?php  } ?>
				</div>
				<br/>
				<div id="ccm-search-field-base" class="ccm-search-field">				
					<table border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td valign="top" width="100%" style="padding-right: 4px">
							<?php echo $form->select('searchField', $searchFields, array('style' => 'width: 130px'));
							?>
							<input type="hidden" value="" class="ccm-core-commerce-product-selected-field" name="selectedSearchField[]" />
							</td>
							<td valign="top">
							<a href="javascript:void(0)" class="ccm-search-remove-option"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a>
							</td>
						</tr>
					</table>
					<br/>
				</div>
				<div id="ccm-search-fields-wrapper">
					<?php   
						foreach ($searchFieldAttributes as $ak) { 
							$i = $ak->getAttributeKeyID();
							if (in_array($i,$search['selectedSearchField'])) {
								$display = "block";
								$selected = $i;
							}
							else {
								$selected = "";
								$display = "none";
							}
						?>
						<div id="ccm-core-commerce-product-search-field-set<?php echo  $i ?>" class="ccm-search-field" style="display:<?php echo  $display ?>">
							<table border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td valign="top" style="padding-right: 4px; white-space: nowrap">
									<?php echo  $ak->getAttributeKeyName(); ?>
									<input type="hidden" value="<?php echo  $selected ?>" class="ccm-core-commerce-product-selected-field" name="selectedSearchField[]" />
									</td>
									<td width="100%" valign="top" class="ccm-selected-field-content">
									<?php 
										$ak->render('form');
									?>
									</td>
									<td valign="top">
									<a href="javascript:void(0)" class="ccm-search-remove-option"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a>
									</td>
								</tr>
							</table>
							<br/>
						</div>
					<?php  } ?>
				</div>
			</div>
		<script type="text/javascript">
			$(function() {
				$('#ccm-core-commerce-product-search-add-option').click(
					function () {
						$('#ccm-search-field-base').show();
					}
				);
				$('#ccm-search-field-base select').change(
					function () {
						var val = $(this).val();
						$('#ccm-core-commerce-product-search-field-set'+val).show();
						$('#ccm-core-commerce-product-search-field-set'+val+' .ccm-core-commerce-product-selected-field').val(val);
						$(this).val('');
						$('#ccm-search-field-base').hide();
					}
				);
				$('.ccm-search-remove-option').click(
					function () {
						$(this).closest('.ccm-search-field').find('.ccm-core-commerce-product-selected-field').val('');
						$(this).closest('.ccm-search-field').hide();
					}
				);
			});
		</script> 
	</div>
</div>
<div class="product-list-data-source-pane" id="product-list-data-source-top_purchased_products">
	<?php  //Top purchased ?>
</div>
<div class="product-list-data-source-pane" id="product-list-data-source-top_visited_products">
	<?php  //Top visited ?>
</div>
<div class="product-list-data-source-pane" id="product-list-data-source-user_visited_products">
	<?php  //User visited ?>
</div>
</div>
<?php 
 $bt->inc('elements/form_setup_search.php', array( 'c'=>$c, 'b'=>$b, 'controller'=>$controller,'block_args'=>$block_args   ) );
?>
