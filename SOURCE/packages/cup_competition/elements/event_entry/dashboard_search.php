<?php
	$entries = $entryList->getPage();
	$pagination = $entryList->getPagination();
?>
<?php if(count($entries) > 0):?>
	
	<table border="0" cellspacing="0" cellpadding="0" id="ccm-product-list" class="ccm-results-list">
		<thead>
			<tr>
				<th class="<?php echo $entryList->getSearchResultsClass('id')?>"><a href="<?php echo $entryList->getSortByURL('id', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('ID')?></a></th>
				<th class="<?php echo $entryList->getSearchResultsClass('first_name')?>"><a href="<?php echo $entryList->getSortByURL('first_name', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('First Name')?></a></th>
				<th class="<?php echo $entryList->getSearchResultsClass('last_name')?>"><a href="<?php echo $entryList->getSortByURL('last_name', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Last Name')?></a></th>
				<th class="<?php echo $entryList->getSearchResultsClass('email')?>"><a href="<?php echo $entryList->getSortByURL('email', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Email')?></a></th>
				<th class="<?php echo $entryList->getSearchResultsClass('status')?>"><a href="<?php echo $entryList->getSortByURL('status', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Status')?></a></th>
				<th class="<?php echo $entryList->getSearchResultsClass('createdAt')?>"><a href="<?php echo $entryList->getSortByURL('createdAt', 'desc')?>" onclick="return sortColumn(this);"><?php echo t('Created At')?></a></th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($entries as $idx => $entry):?>
				<tr ref="<?php echo $entry->id;?>">
					<td><?php echo $entry->id;?></td>
					<td><?php echo $entry->first_name;?></td>
					<td><?php echo $entry->last_name;?></td>
					<td><?php echo $entry->email;?></td>
					<td><?php echo $entry->status;?></td>
					<td><?php echo date('Y-m-d H:i:s', strtotime($entry->createdAt));?></td>
					<td>
						<a href="<?php echo View::url('/dashboard/cup_competition/entry/viewEntry/', $entry->id);?>">VIEW</a>
						&nbsp;&nbsp;
						<a href="javascript:deleteItem(<?php echo $entry->id;?>);">DELETE</a>
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
	<div id="ccm-list-none"><?php echo t('No authors found.')?></div>
<?php endif;?>


