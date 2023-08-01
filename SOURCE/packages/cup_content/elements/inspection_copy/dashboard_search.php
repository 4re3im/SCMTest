<?php
	$orders = $list->getPage();
	$pagination = $list->getPagination();
?>

<?php if(count($orders) > 0):?>
	
	<table border="0" cellspacing="0" cellpadding="0" id="ccm-product-list" class="ccm-results-list">
		<thead>
			<tr>
				<th class="<?php echo $list->getSearchResultsClass('id')?>"><a href="<?php echo $list->getSortByURL('id', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('ID')?></a></th>
				<th class="<?php echo $list->getSearchResultsClass('email')?>"><a href="<?php echo $list->getSortByURL('email', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Email')?></a></th>
				<th class="<?php echo $list->getSearchResultsClass('first_name')?>"><a href="<?php echo $list->getSortByURL('first_name', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('First Name')?></a></th>
				<th class="<?php echo $list->getSearchResultsClass('last_name')?>"><a href="<?php echo $list->getSortByURL('last_name', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Last Name')?></a></th>
				<th class="<?php echo $list->getSearchResultsClass('school_campus')?>"><a href="<?php echo $list->getSortByURL('school_campus', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('School / Campus')?></a></th>
				<th class="<?php echo $list->getSearchResultsClass('status')?>"><a href="<?php echo $list->getSortByURL('status', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Status')?></a></th>
				<th class="<?php echo $list->getSearchResultsClass('createdAt')?>"><a href="<?php echo $list->getSortByURL('createdAt', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Created At')?></a></th>
				
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($orders as $idx=>$each_order):?>
				<tr ref="<?php echo $each_order->id;?>">
					<td><?php echo $each_order->id;?></td>
					<td><?php echo $each_order->email;?></td>
					<td><?php echo $each_order->first_name;?></td>
					<td><?php echo $each_order->last_name;?></td>
					<td><?php echo $each_order->school_campus;?></td>
					<td><?php echo $each_order->status;?></td>
					<td><?php echo date('Y-m-d H:i:s', strtotime($each_order->createdAt));?></td>
					<td>
						<a href="<?php echo View::url('/dashboard/cup_content/inspection_copy_orders/order_detail/', $each_order->id);?>">VIEW</a>
						&nbsp;&nbsp;
						<a href="javascript:deleteItem(<?php echo $each_order->id;?>);">DELETE</a>
					</td>
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>
	
	<style>
		.custom_pagination .pager span{
			margin:0px 5px;
		}
	</style>
	<div class="custom_pagination">
		<?php if($pagination->getTotalPages() > 1):?>
			<?php
				$pagination->jsFunctionCall = 'gotoPage';
			?>
			<div style="float:right" class="pager">
				<?php echo $pagination->getPrevious('Previous');?> &nbsp;|&nbsp; <?php echo $pagination->getPages();?> &nbsp;|&nbsp; <?php echo $pagination->getNext('Next');?>
			</div>
		<?php endif;?>
		Page <?php echo $pagination->getRequestedPage(); ?> of <?php echo $pagination->getTotalPages(); ?> | Total Results: <?php echo $pagination->result_count; ?>
	</div>

<?php else:?>
	<div id="ccm-list-none"><?php echo t('No orders found.')?></div>
<?php endif;?>