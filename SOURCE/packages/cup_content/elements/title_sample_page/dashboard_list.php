<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<?php
	$ch = Loader::helper('cup_content_html', 'cup_content');
?>

<?php if(count($samplePages) > 0):?>
	
	<table border="0" cellspacing="0" cellpadding="0" id="ccm-product-list" class="ccm-results-list">
		<thead>
			<tr>
				<th class="<?php echo $samplePageList->getSearchResultsClass('filename')?>"><a href="<?php echo $samplePageList->getSortByURL('filename', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('File Name')?></a></th>
				<th class="<?php echo $samplePageList->getSearchResultsClass('description')?>"><a href="<?php echo $samplePageList->getSortByURL('description', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Description')?></a></th>
				<th class="<?php echo $samplePageList->getSearchResultsClass('filesize')?>"><a href="<?php echo $samplePageList->getSortByURL('filesize', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('File Size')?></a></th>
				<th class="<?php echo $samplePageList->getSearchResultsClass('modifiedAt')?>"><a href="<?php echo $samplePageList->getSortByURL('modifiedAt', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Modified At')?></a></th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($samplePages as $idx=>$sample_page):?>
				<tr ref="<?php echo $sample_page->id;?>">
					<td><a href="<?php echo $this->url('/dashboard/cup_content/titles/downloadSamplePageFile', $sample_page->id);?>"/><?php echo $sample_page->filename;?></a></td>
					<td><?php echo $sample_page->description;?></td>
					<td><?php echo $ch->formatRawSize($sample_page->filesize);?> </td>
					<td><?php echo date('Y-m-d H:i:s', strtotime($sample_page->modifiedAt));?></td>
					<td>
						<a href="<?php echo View::url('/dashboard/cup_content/titles/edit_sample_page', $sample_page->id);?>">Edit</a>
						&nbsp;&nbsp;
						<a href="javascript:deleteItem(<?php echo $sample_page->id;?>);">DELETE</a>
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
		<?PHP endif;?>
		Page <?php echo $pagination->getRequestedPage(); ?> of <?php echo $pagination->getTotalPages(); ?> | Total Results: <?php echo $pagination->result_count; ?>
	</div>

<?php else:?>
	<div id="ccm-list-none"><?php echo t('No Sample Pages found.')?></div>
<?php endif;?>