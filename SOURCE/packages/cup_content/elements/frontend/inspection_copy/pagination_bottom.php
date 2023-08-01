<?php
	$uh = Loader::helper('url');
	$form = Loader::helper('form');
	$html = array();
	
	$pagination = $list->getPagination();
?>

<div class="cup_pagination">
	<?php if($pagination->getTotalPages() > 1):?>
			<?php
				$pagination->jsFunctionCall = 'gotoPage';
			?>
			<!--<div class="pager">-->
				<?php echo $pagination->getPrevious('Previous');?> &nbsp;|&nbsp; <?php echo $pagination->getPages();?> &nbsp;|&nbsp; <?php echo $pagination->getNext('Next');?>
			<!--</div>-->
	<?php endif;?>

	<div class="page_size">
		<?php $page_size = @$_GET['cc_size'];?>
		Items per page <?php echo $form->select('cup_page_size', array('10'=>'10', '15'=>'15', '20'=>'20'), $page_size);?>
	</div>
</div>
<script>
	jQuery('.cup_pagination .page_size select[name="cup_page_size"]').change(function(){
		var url = "<?php echo $base_url;?>";
		var page_size = jQuery(this).val();
		var query = "<?php //unset($criteria['cc_size']); echo http_build_query($criteria);?>";
		if(query.length > 1){
			url = url+'?'+query+'&cc_size='+page_size;
		}else{
			url = url+'?cc_size='+page_size;
		}
		document.location.href = url;
	});
</script>