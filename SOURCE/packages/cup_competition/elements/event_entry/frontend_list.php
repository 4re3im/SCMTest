<?php
	$entries = $entryList->getPage();
	$pagination = $entryList->getPagination();
?>
<?php if(count($entries) > 0):?>
	<style>
		.custom_pagination .pager span{
			margin:0px 5px;
		}
	</style>
	
	<div class="cup-competition-gallery-pagination-frame">
		<div class="custom_pagination" style="text-align:right;">
			<?php if($pagination->getTotalPages() > 1):?>
				<?php
					$pagination->jsFunctionCall = 'gotoPage';
				?>
				<div style="float:left" class="pager">
					<?php echo $pagination->getPrevious('Previous');?> &nbsp;|&nbsp; <?php echo $pagination->getPages();?> &nbsp;|&nbsp; <?php echo $pagination->getNext('Next');?>
				</div>
			<?php endif;?>
			<!--
			Page <?php echo $pagination->getRequestedPage(); ?> of <?php echo $pagination->getTotalPages(); ?> | Total Results: <?php echo $pagination->result_count; ?>
			-->
			
			<?php $page_size = 12;
				if(isset($_GET['page_size'])){
					$page_size = intval($_GET['page_size']);
				}
			?>
			Items per page 	<select id="items_per_page">
								<?php foreach(array(1,4,8,12,16,20,24) as $each_val):?>
									<?php if($each_val == $page_size):?>
										<option value="<?php echo $each_val;?>" selected="selected"><?php echo $each_val;?></option>
									<?php else:?>
										<option value="<?php echo $each_val;?>"><?php echo $each_val;?></option>
									<?php endif;?>
								<?php endforeach;?>
							</select>
		</div>
	</div>
	<div style="width:100%;height:25px"></div>

	
	<?php foreach($entries as $idx => $entry):?>
		<div class="gallery_item">
			<a href="<?php echo $entry->getImageUrl(800);?>" rel="gallery[photo]" title="<?php echo str_replace('"', '\"', $entry->image_description);?>">
				<img src="<?php echo $entry->getImageUrl(235, 235);?>" alt="<?php echo str_replace('"', '\"', $entry->image_caption);?>"/>
				<div class="hover_info">
					<div style="width:100%;height:15px"></div>
					<div class="content">
						<?php echo $entry->image_caption;?>
					</div>
				</div>
			</a>
		</div>
	<?php endforeach;?>
	
	<div style="clear:both; width:0px; height:0px;"></div>
		
	
	<div style="width:100%;height:25px"></div>
	<div class="cup-competition-gallery-pagination-frame">
		<div class="custom_pagination" style="text-align:right;">
			<?php if($pagination->getTotalPages() > 1):?>
				<?php
					$pagination->jsFunctionCall = 'gotoPage';
				?>
				<div style="float:left" class="pager">
					<?php echo $pagination->getPrevious('Previous');?> &nbsp;|&nbsp; <?php echo $pagination->getPages();?> &nbsp;|&nbsp; <?php echo $pagination->getNext('Next');?>
				</div>
			<?php endif;?>
			
			<!--
			Page <?php echo $pagination->getRequestedPage(); ?> of <?php echo $pagination->getTotalPages(); ?> | Total Results: <?php echo $pagination->result_count; ?>
			-->
			
			Items per page 	<select id="items_per_page_alt">
								<?php foreach(array(1,4,8,12,16,20,24) as $each_val):?>
									<?php if($each_val == $page_size):?>
										<option value="<?php echo $each_val;?>" selected="selected"><?php echo $each_val;?></option>
									<?php else:?>
										<option value="<?php echo $each_val;?>"><?php echo $each_val;?></option>
									<?php endif;?>
								<?php endforeach;?>
							</select>
		</div>
	</div>

	<script>
		$('a[rel^="gallery"]').prettyPhoto({
				theme: 'custom_light_square',
				overlay_gallery: false,
				markup: '<div class="pp_pic_holder"> \
						<div class="pp_top"> \
							<div class="pp_left"></div> \
							<div class="pp_middle"></div> \
							<div class="pp_right"></div> \
						</div> \
						<div class="pp_content_container"> \
							<div class="pp_left"> \
							<div class="pp_right"> \
								<div class="pp_content"> \
									<div class="pp_loaderIcon"></div> \
									<div class="pp_fade"> \
										<a class="pp_close" href="#">Close</a> \
										<div class="pp_hoverContainer"> \
											<span style="margin-right: 30px"><a class="pp_next" href="#">next</a></span> \
											<a class="pp_previous" href="#">previous</a> \
										</div> \
										<div id="pp_full_res"></div> \
										<div class="pp_details"> \
											<div class="pp_social" style="float:right">{pp_social}</div> \
											<div class="ppt">&nbsp;</div> \
											<div class="description" style="margin:0px 10px"><p class="pp_description"></p></div> \
											<div style="clear:both; width:100%; height: 3px"></div> \
										</div> \
									</div> \
								</div> \
							</div> \
							</div> \
						</div> \
						<div class="pp_bottom"> \
							<div class="pp_left"></div> \
							<div class="pp_middle"></div> \
							<div class="pp_right"></div> \
						</div> \
					</div> \
					<div class="pp_overlay"></div>',
				social_tools: '<div class="pp_social"><div class="facebook"><iframe src="http://www.facebook.com/plugins/like.php?locale=en_US&href={location_href}&amp;layout=button_count&amp;show_faces=true&amp;width=500&amp;action=like&amp;font&amp;colorscheme=light&amp;height=23" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:500px; height:23px;" allowTransparency="true"></iframe></div></div>'
				//slideshow:5000, 
				//autoplay_slideshow:true
			});
			
			jQuery('#items_per_page').change(function(){
				var page_size = jQuery(this).val();
				jQuery('.cup_competition_gallery_content').addLoadingMask();
				jQuery.get(window.location, 
					{page_size:page_size, ajax:'yes'},
					function(html_data){
						jQuery('.cup_competition_gallery_content').html(html_data);
						jQuery('.cup_competition_gallery_content').removeLoadingMask();
					}
				);
				return false;
			});
			
			jQuery('#items_per_page_alt').change(function(){
				var page_size = jQuery(this).val();
				jQuery('.cup_competition_gallery_content').addLoadingMask();
				jQuery.get(window.location, 
					{page_size:page_size, ajax:'yes'},
					function(html_data){
						jQuery('.cup_competition_gallery_content').html(html_data);
						jQuery('.cup_competition_gallery_content').removeLoadingMask();
					}
				);
				return false;
			});

	</script>
<?php else:?>
	<div style="margin: 0px 15px; font-size: 16px; color: #333; font-family: PT Sans;"><?php echo t('No entries have been published yet, please come back later.')?></div>
	<div style="width:100%;height:40px"></div>
<?php endif;?>


