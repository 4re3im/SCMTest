<?php

	Loader::model('go_access_code_email_template/model', 'cup_content');
?>
<?php if(count($records) > 0):?>
	
	<table border="0" cellspacing="0" cellpadding="0" id="ccm-product-list" class="ccm-results-list">
		<thead>
			<tr>
				<th class="<?php echo $list->getSearchResultsClass('displayName')?>"><a href="<?php echo $list->getSortByURL('displayName', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Name')?></a></th>
				<th class="<?php echo $list->getSearchResultsClass('displaySubtitle')?>"><a href="<?php echo $list->getSortByURL('displaySubtitle', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Subtitle')?></a></th>
				<th width="90px" class="<?php echo $list->getSearchResultsClass('total_count')?>"><a href="<?php echo $list->getSortByURL('total_count', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Total Count')?></a></th>
				<th width="90px" class="<?php echo $list->getSearchResultsClass('used_count')?>"><a href="<?php echo $list->getSortByURL('used_count', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Used Count')?></a></th>
				<th>Action</th>
				<th>Email Template</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($records as $idx => $record):?>
				<tr ref="<?php echo $record->titleID;?>">
					<td><?php echo $record->getTitle()->displayName;?></td>
					<td><?php echo $record->getTitle()->displaySubtitle;?></td>
					<td><?php echo $record->total_count;?></td>
					<td><?php echo $record->used_count;?></td>
					<?php
						$templateObj = false;
						$templateObj = CupContentGoAccessCodeEmailTemplate::fetchByTitleID($record->titleID);
						
					?>
					<td>
						<a href="<?php echo View::url('/dashboard/cup_content/cambridge_go_access_code/title', $record->titleID);?>">VIEW</a>
					</td>
					<td>
						<a href="<?php echo View::url('/dashboard/cup_content/cambridge_go_access_code/edit_email_template', $record->titleID);?>">
						<?php if($templateObj):?>
							Custom
						<?php else:?>
							Default
						<?php endif;?>
						</a>
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
	<div id="ccm-list-none"><?php echo t('No Title Match.')?></div>
<?php endif;?>