<?php  defined('C5_EXECUTE') or die(_("Access Denied."));

if ($this->controller->getTask() == 'edit') { 

$ih = Loader::helper('concrete/interface');
$valt = Loader::helper('validation/token');
?>

<?php  } else if ($this->controller->getTask() == 'detail') { ?>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Order Details'))?>

		<div style="float:right;"><a href="<?php  echo $concrete_urls->getToolsUrl('order_print','core_commerce')."?orderID=".$order->getOrderID().'&view_mode=dashboard';?>" target="_blank" class="btn"><?php  echo t('Print Order')?></a></div>
		<div class="ccm-spacer"></div>	
	
	<?php  Loader::packageElement('orders/detail','core_commerce',array('order'=>$order));?>
	
	<hr />
	
	<h4><span><?php echo t('Order Status')?></span></h4>
	
<?php  	$statusHistory = $order->getOrderStatusHistory();  ?>
	<?php  if (count($statusHistory) > 0) { ?>
		<?php echo t('Currently: ')?><strong><?php echo $order->getOrderStatusText()?></strong>
		<?php  if($order->getOrderStatus() == CoreCommerceOrder::STATUS_CANCELLED) { ?>
			<a href="<?php echo $this->action('delete',$order->getOrderID())."?ccm_token=".$validation_token->generate('delete');?>" onclick="return confirm('<?php echo t('Are you sure?')?>')"><?php echo  t('Delete Order')?></a>
		<?php  } ?>
		<br/><br/>
	<?php  } ?>
	<table border="0" cellspacing="1" cellpadding="0" class="grid-list">
	<tr>
		<td class="header"><?php echo t('Status')?></td>
		<td class="header"><?php echo t('Date Set')?></td>
		<td class="header"><?php echo t('Set By')?></td>
	</tr>
	<?php  
	if (count($statusHistory) > 0) { ?>
	<?php  foreach($statusHistory as $st) { ?>
		<tr>
			<td><?php echo $st->getOrderStatusHistoryStatusText()?></td>
			<td><?php echo $st->getOrderStatusHistoryDateTime()?></td>
			<td><?php 
				if ($st->getOrderStatusHistoryUserID() > 0) { 
					$ui = UserInfo::getByID($st->getOrderStatusHistoryUserID());
					if (is_object($ui)) {
						print '<A href="' . $this->url('/dashboard/users/search?uID=' . $st->getOrderStatusHistoryUserID()) . '">' . $ui->getUserName() . '</a>';
					}
				}
			?></td>
		</tr>
		<?php  } ?>
	<?php  } else { ?>
	<tr>
		<td><?php echo $order->getOrderStatusText()?></td>
		<td><?php echo $order->getOrderDateAdded()?></td>
		<td>&nbsp;</td>
	</tr>
	<?php  } ?>	
	</table>
	<Br/>
	
	<form method="post" action="<?php echo $this->action('update_order_status')?>">
	<h4><?php echo t('Update Order Status')?></h4>
	<?php  $statuses = $order->getOrderAvailableStatuses(); ?>
	
	<?php echo $form->hidden("orderID", $order->getOrderID())?>
	<?php echo $form->select('oStatus', $statuses)?>
	<?php echo $form->submit('submit', t('Set Status'))?>
	
	</form>
	</table>
	
	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>

<?php  } else { ?>
	

	<?php 
	$searchFields = array(
		'' => '** ' . t('Fields'),
		'date_added' => t('Created Between'),
		'product_set' => t('Product Set')
	);
	
	$uh = Loader::helper('urls', 'core_commerce');
	Loader::model('attribute/categories/core_commerce_order', 'core_commerce');
	$searchFieldAttributes = CoreCommerceOrderAttributeKey::getSearchableList();
	foreach($searchFieldAttributes as $ak) {
		$searchFields[$ak->getAttributeKeyID()] = $ak->getAttributeKeyDisplayHandle();
	}
	?>	

		<div id="ccm-core-commerce-order-search-field-base-elements" style="display: none">
	
		<span class="ccm-search-option ccm-search-option-type-date_time"  search-field="date_added">
			<?php echo $form->text('date_from', array('style' => 'width: 86px'))?>
			<?php echo t('to')?>
			<?php echo $form->text('date_to', array('style' => 'width: 86px'))?>
			</span>

			<?php  
		Loader::model('product/set', 'core_commerce');
		$productSets = CoreCommerceProductSet::getList();
		?>
			<span class="ccm-search-option"  search-field="product_set">
			<div class="clearfix">
			<ul class="inputs-list">
			<?php  if (count($productSets) > 0) { ?>
			<?php  foreach($productSets as $prs) { ?>
				<li class="ccm-product-search-set"><label><?php echo $form->checkbox('prsID[' . $prs->getProductSetID() . ']', $prs->getProductSetID() )?> <span><?php echo $prs->getProductSetName()?></span></label></li>
			<?php  } ?>
			<?php  } else { ?>
				<?php echo t("None found.")?>
			<?php  } ?>
			</ul>
			</div>
			</span>
			
			<?php  foreach($searchFieldAttributes as $sfa) { 
				$sfa->render('search'); ?>
			<?php  } ?>
			
		</div>	
		
	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Order Search'), false, false, false)?>

	<div class="ccm-pane-options" id="ccm-core-commerce-order-pane-options">
	<div class="ccm-core-commerce-order-search-form">
	<?php  $form = Loader::helper('form'); ?>
		<form method="get" id="ccm-core-commerce-order-advanced-search" action="<?php echo $this->url('/dashboard/core_commerce/orders/search')?>">

		<?php echo $form->hidden('mode', $mode); ?>
	
		<div class="ccm-pane-options-permanent-search">
		
			<input type="hidden" name="search" value="1" />
	
			<div class="span4">
			<?php echo $form->label('keywords', t('Keywords'))?>
			<div class="input">
				<?php echo $form->text('keywords', array('style'=> 'width: 130px')); ?>
			</div>
			</div>

			<div class="span4">
			<?php echo $form->label('oStatus', t('Status'))?>
			<div class="input">
				<?php  $statuses = array_merge(array('' => t('** All')));
				foreach(CoreCommerceOrder::getOrderAvailableStatuses() as $key => $value) {
					$statuses[$key] = $value;
				}
				?>
				<?php echo $form->select('oStatus', $statuses, array('class' => 'span3'))?>
			</div>
			</div>
			
			
			<div class="span5">
			<?php echo $form->label('numResults', t('# Per Page'))?>
			<div class="input">
				<?php echo $form->select('numResults', array(
					'10' => '10',
					'25' => '25',
					'50' => '50',
					'100' => '100',
					'500' => '500'
				), $_REQUEST['numResults'], array('style' => 'width:65px'))?>
	
			</div>
			<?php echo $form->submit('ccm-search-files', t('Search'), array('style' => 'margin-left: 10px'))?>
	
			</div>
	
		</div>
		<a href="javascript:void(0)" onclick="ccm_paneToggleOptions(this)" class="ccm-icon-option-<?php  if (is_array($_REQUEST['selectedSearchField']) && count($_REQUEST['selectedSearchField']) > 1) { ?>open<?php  } else { ?>closed<?php  } ?>"><?php echo t('Advanced Search')?></a>
		<div class="clearfix ccm-pane-options-content" <?php  if (is_array($_REQUEST['selectedSearchField']) && count($_REQUEST['selectedSearchField']) > 1) { ?>style="display: block" <?php  } ?>>

			<br/>
			<table class="zebra-striped ccm-search-advanced-fields" id="ccm-core-commerce-order-search-advanced-fields">
			<tr>
				<th colspan="2" width="100%"><?php echo t('Additional Filters')?></th>
				<th style="text-align: right; white-space: nowrap"><a href="javascript:void(0)" id="ccm-core-commerce-order-search-add-option" class="ccm-advanced-search-add-field"><span class="ccm-menu-icon ccm-icon-view"></span><?php echo t('Add')?></a></th>
			</tr>
			<tr id="ccm-search-field-base">
				<td><?php echo $form->select('searchField', $searchFields);?></td>
				<td width="100%">
				<input type="hidden" value="" class="ccm-core-commerce-order-selected-field" name="selectedSearchField[]" />
				<div class="ccm-selected-field-content">
					<?php echo t('Select Search Field.')?>				
				</div></td>
				<td><a href="javascript:void(0)" class="ccm-search-remove-option"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a></td>
			</tr>
			</table>
	
			<div id="ccm-search-fields-submit">
				<a href="<?php echo $uh->getToolsURL('customize_order_search_columns')?>" id="ccm-list-view-customize"><span class="ccm-menu-icon ccm-icon-properties"></span><?php echo t('Customize Results')?></a>
			</div>
		
		</div>
		
	</div>
	
	</form>

	</div>
	
	<div class="ccm-pane-body">
		<?php 
		$txt = Loader::helper('text');
		$keywords = $_REQUEST['keywords'];
		$uh = Loader::helper('urls', 'core_commerce');
		
		if (count($orders) > 0) { ?>	
			<table border="0" cellspacing="0" cellpadding="0" id="ccm-core-commerce-order-list" class="zebra-striped ccm-results-list">
			<tr>
				<th class="<?php echo $orderList->getSearchResultsClass('invoiceNumber')?>"><a href="<?php echo $orderList->getSortByURL('invoiceNumber', 'asc')?>"><?php echo t('#')?></a></th>
				<th class="<?php echo $orderList->getSearchResultsClass('oDateAdded')?>"><a href="<?php echo $orderList->getSortByURL('oDateAdded', 'asc')?>"><?php echo t('Date Added')?></a></th>
				<th><?php echo t('Total')?></th>
				<th class="<?php echo $orderList->getSearchResultsClass('oStatus')?>"><a href="<?php echo $orderList->getSortByURL('oStatus', 'asc')?>"><?php echo t('Status')?></a></th>
				<?php  
				$slist = CoreCommerceOrderAttributeKey::getColumnHeaderList();
				foreach($slist as $ak) { ?>
					<th class="<?php echo $orderList->getSearchResultsClass($ak)?>"><a href="<?php echo $orderList->getSortByURL($ak, 'asc')?>"><?php echo $ak->getAttributeKeyDisplayHandle()?></a></th>
				<?php  } ?>			
			</tr>
		<?php 
			foreach($orders as $order) { 
				
				if (!isset($striped) || $striped == 'ccm-list-record-alt') {
					$striped = '';
				} else if ($striped == '') { 
					$striped = 'ccm-list-record-alt';
				}
	
				?>
			
				<tr class="ccm-list-record <?php echo $striped?>">
				<td><a href="<?php echo $this->controller->getBaseUrl().$this->action('detail', $order->getOrderID())?>"><?php echo $order->getInvoiceNumber()?></a></td>
				<td><?php echo date(t("m/d/Y - g:i A"), strtotime($order->getOrderDateAdded()))?></td>
				<td><?php echo $order->getOrderDisplayTotal()?></td>
				<td><?php echo $order->getOrderStatusText()?></td>
				<?php  
				$slist = CoreCommerceOrderAttributeKey::getColumnHeaderList();
				foreach($slist as $ak) { ?>
					<td><?php 
					$vo = $order->getAttributeValueObject($ak);
					if (is_object($vo)) {
						print $vo->getValue('display');
					}
					?></td>
				<?php  } ?>
				</tr>
				<?php 
			}
	
		?>
		
		</table>
		

		<div id="ccm-export-results-wrapper">
			<a id="ccm-export-results" href="<?php echo $this->action('export')?>"><span></span><?php echo t('Export')?></a>
		</div>
		
		<?php echo $orderList->displaySummary();?>
		
	
		<?php  } else { ?>
			
			<div id="ccm-list-none"><?php echo t('No orders found.')?></div>
		
		<?php  } ?>

	</div>
	
	<div class="ccm-pane-footer">
		<?php  $orderList->displayPaging(); ?>				
	</div>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>

	
	
<?php  } ?>

<script type="text/javascript">
$(function() {
	ccm_coreCommerceSetupOrderSearch();
	<?php  if ($_REQUEST['selectedSearchField'] && is_array($_REQUEST['selectedSearchField'])) { ?>
		<?php  $i = 1; ?>
		<?php  foreach($_REQUEST['selectedSearchField'] as $sfa) { ?>
			<?php  if ($sfa != '') { ?>
				$("#ccm-core-commerce-order-search-add-option").click();
				$("#ccm-core-commerce-order-search-field-set<?php echo $i?> select[name=searchField]").val('<?php echo $sfa?>').change();
				ccm_totalAdvancedSearchFields++;

				<?php  $i++; ?>
			<?php  } ?>
		<?php  } ?>
	<?php  } ?>
});
</script>
