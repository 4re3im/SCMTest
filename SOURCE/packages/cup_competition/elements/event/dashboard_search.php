<?php
	if(isset($eventList)){
		$events = $eventList->getPage();
		$pagination = $eventList->getPagination();
	}
?>
<?php if(count($events) > 0):?>

	<table border="0" cellspacing="0" cellpadding="0" id="ccm-product-list" class="ccm-results-list">
		<thead>
			<tr>
				<th width="200" class="<?php echo $eventList->getSearchResultsClass('name')?>"><a href="<?php echo $eventList->getSortByURL('name', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Name')?></a></th>
				<th class="<?php echo $eventList->getSearchResultsClass('category')?>"><a href="<?php echo $eventList->getSortByURL('category', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Category')?></a></th>
				<th class="<?php echo $eventList->getSearchResultsClass('type')?>"><a href="<?php echo $eventList->getSortByURL('type', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Type')?></a></th>
				<th class="<?php echo $eventList->getSearchResultsClass('createdAt')?>"><a href="<?php echo $eventList->getSortByURL('createdAt', 'desc')?>" onclick="return sortColumn(this);"><?php echo t('createdAt')?></a></th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($events as $idx=>$event):?>
				<tr ref="<?php echo $event->id;?>">
					<td><?php echo $event->name;?></td>
					<td><?php echo $event->category;?></td>
					<td><?php echo $event->type;?></td>
					<td><?php echo date('Y-m-d H:i:s', strtotime($event->createdAt));?></td>
					<td>
						<a href="<?php echo View::url('/dashboard/cup_competition/event/edit', $event->id);?>">EDIT</a>
						&nbsp;&nbsp;
						<a href="javascript:deleteItem(<?php echo $event->id;?>);">DELETE</a>
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
