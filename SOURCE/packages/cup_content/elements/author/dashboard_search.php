
<?php if(count($authors) > 0):?>
	
	<table border="0" cellspacing="0" cellpadding="0" id="ccm-product-list" class="ccm-results-list">
		<thead>
			<tr>
				<th width="200" class="<?php echo $authorList->getSearchResultsClass('name')?>"><a href="<?php echo $authorList->getSortByURL('name', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Name')?></a></th>
				<th class="<?php echo $authorList->getSearchResultsClass('prettyUrl')?>"><a href="<?php echo $authorList->getSortByURL('prettyUrl', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Pretty Url')?></a></th>
				<th class="<?php echo $authorList->getSearchResultsClass('modifiedAt')?>"><a href="<?php echo $authorList->getSortByURL('modifiedAt', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Modified At')?></a></th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($authors as $idx=>$author):?>
				<tr ref="<?php echo $author->id;?>">
					<td><?php echo $author->name;?></td>
					<td><?php echo $author->prettyUrl;?></td>
					<td><?php echo date('Y-m-d H:i:s', strtotime($author->modifiedAt));?></td>
					<td>
						<a href="<?php echo View::url('/dashboard/cup_content/authors/edit', $author->id);?>">EDIT</a>
						&nbsp;&nbsp;
						<a href="javascript:deleteItem(<?php echo $author->id;?>);">DELETE</a>
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