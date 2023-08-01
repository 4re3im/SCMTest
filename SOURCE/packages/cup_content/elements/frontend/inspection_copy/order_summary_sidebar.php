<?php
	Loader::model('title/list','cup_content');

	$uh = Loader::helper('url');
	$uh2 = Loader::helper('concrete/urls');
	$url_remove_item =  $uh2->getToolsURL('frontend/inspection_remove_item', 'cup_content');
?>
<div class="h30_spacer"></div>
<div class="cup-inspection-copy-order-summary-frame">
	<div class="heading-section">
		ORDER SUMMARY
	</div>
	<div class="body-section">
		
			<?php
				$list = new CupContentTitleList();
				$list->filterByIds($_SESSION['inspection_copy']['order_list']);
				$list->sortBy('name', 'asc');
				$list->setItemsPerPage(999);
				$titles = $list->getPage();
			?>
			<ul>
				<?php foreach($titles as $each_title):?>
					<li>
						<div class="action-block">
							<a href="#" class="remove_inspection_item" ref="<?php echo $each_title->id;?>">
								<div class="remove_btn"></div>
							</a>
						</div>
						<?php echo $each_title->generateProductName();?>
					</li>
				<?php endforeach;?>
			</ul>
			<div style="width:1px;height:30px"></div>
	</div>
</div>

<script>
	jQuery('a.remove_inspection_item').click(function(){
		var item_id = jQuery(this).attr('ref');
		var action_url = "<?php echo $url_remove_item;?>";
		var action_block = jQuery(this).parent();
		var item_element = action_block.parent();
		jQuery(this).hide();
		action_block.addClass('loading');
		
		
		jQuery.get(
					action_url,
					{title_id: item_id},
					function(content){
						var json = jQuery.parseJSON(content)
						if(json.result == 'success'){
							item_element.fadeOut(300, function(){
									item_element.remove();
								});
							if(json.total < 1){
								jQuery('.inspection_copy_order_form_frame').fadeOut(300, function(){
									jQuery('.inspection_copy_empty_item_message_frame').fadeIn(300);
								});
								
							}
						}
					}
				);
	});
</script>