
<?php if(count($records) > 0):?>
	
	<table border="0" cellspacing="0" cellpadding="0" id="ccm-product-list" class="ccm-results-list">
		<thead>
			<tr>
				<th class="<?php echo $list->getSearchResultsClass('accessCode')?>"><a href="<?php echo $list->getSortByURL('accessCode', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('accessCode')?></a></th>
				<th class="<?php echo $list->getSearchResultsClass('orderID')?>"><a href="<?php echo $list->getSortByURL('orderID', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('orderID')?></a></th>
				<th class="<?php echo $list->getSearchResultsClass('orderID')?>"><a href="<?php echo $list->getSortByURL('invoiceID', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('invoiceID')?></a></th>
				<th class="<?php echo $list->getSearchResultsClass('productName')?>"><a href="<?php echo $list->getSortByURL('productName', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Product Name')?></a></th>
				<th class="<?php echo $list->getSearchResultsClass('email')?>"><a href="<?php echo $list->getSortByURL('email', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Email')?></a></th>
				<th class="<?php echo $list->getSearchResultsClass('firstName')?>"><a href="<?php echo $list->getSortByURL('firstName', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('First Name')?></a></th>
				<th class="<?php echo $list->getSearchResultsClass('lastName')?>"><a href="<?php echo $list->getSortByURL('lastName', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Last Name')?></a></th>
				<th class="<?php echo $list->getSearchResultsClass('redeemAt')?>"><a href="<?php echo $list->getSortByURL('redeemAt', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Redeem At')?></a></th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($records as $idx => $record):?>
				<tr ref="<?php echo $record->id;?>">
					<td><?php echo $record->accessCode;?></td>
					<td><?php echo $record->orderID;?></td>
					<td><?php echo $record->invoiceID;?></td>
					<td><?php echo $record->productName;?></td>
					<td><?php echo $record->email;?></td>
					<td><?php echo $record->firstName;?></td>
					<td><?php echo $record->lastName;?></td>
					<td><?php if(strtotime($record->redeemAt)){ echo date('d/m/y', strtotime($record->redeemAt)); }?></td>
					<td>
						<a href="javascript:deleteItem(<?php echo $record->id;?>)">DELETE</a>
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
	<div id="ccm-list-none"><?php echo t('No Access Code')?></div>
<?php endif;?>