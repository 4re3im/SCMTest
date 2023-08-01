<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php 
	Loader::model('title_downloadable_file/model', 'cup_content');
	
	$ch = Loader::helper('cup_content_html', 'cup_content');
	$uh = Loader::helper('url');
	Loader::helper('tools', 'cup_content');
	
	$this->addHeaderItem($html->javascript('colorbox/jquery.colorbox-min.js', 'cup_content'));
	$this->addHeaderItem($html->css('../js/colorbox/colorbox.css', 'cup_content'));
?>

<?php Loader::element('frontend/simple_heading', array(), 'cup_content');?>
<div class="h50_spacer bg_light_blue2"></div>
<div class="breadcrumb bg_light_blue2"></div>
<div class="content-page-title bg_light_blue2"><h1><?php echo $seriesObj->name;?></h1></div>
<div class="spacer_bar blue"></div>
<div class="cup-content-master-frame">
	<div class="right-sidebar-content-frame">
		<?php $series_titles = $seriesObj->getTitleObjects();
		if(count($series_titles) > 0):?>
		<div class="related-title-frame">
			<div class="title">The complete series</div>
			<?php foreach($series_titles as $each_title_obj):?>
				<div class="item">
					<div class="spacer_pad"></div>
						<div class="image-frame">
							<div class="relative_frame">
								<img src="<?php echo $each_title_obj->getImageUrl(60);?>" alt="<?php echo $each_title_obj->name;?>"/>
								<?php if($each_title_obj->new_product_flag):?>
								<div class="cup-new-product-60"></div>
								<?php endif;?>
							</div>
						</div>
						<div class="content-frame">
								<?php 
									$tmp_desc = $each_title_obj->getSelectedDescription(true);
									$tmp_desc = wordwrap($tmp_desc, 80, '|||');
									$tmp_desc = explode("|||", $tmp_desc);
									if(count($tmp_desc) > 1){
										$tmp_desc = $tmp_desc[0].'...';
									}else{
										$tmp_desc = $tmp_desc[0];
									}
								?>
							<div class="item-title"><a href="<?php echo $each_title_obj->getUrl();?>"><?php echo $each_title_obj->name;?></a></div>
							<div class="item-description"><?php echo $tmp_desc;?></div>
							<div class="item-link"><a href="<?php echo $each_title_obj->getUrl();?>">Read more</a></div>
						</div>
						<div class="clr"></div>
						<div class="spacer_pad"></div>
					</div>
			<?php endforeach;?>
		</div>
		<?php endif;?>
		
	</div>
	
	
	<div class="main-content-frame with-right-sidebar">
		<div class="title-content-frame">
			<div class="h20_spacer"></div>
			<div class="image-frame">
				<div class="relative_frame">
					<img src="<?php echo $seriesObj->getImageUrl(180);?>" alt="<?php echo $seriesObj->name;?>"/>
					<?php if($seriesObj->isContainNewProduct()):?>
					<div class="cup-new-product-180"></div>
					<?php endif;?>
					
					<div style="text-align:center;">
						<!-- AddThis Button BEGIN -->
						<div class="h20_spacer"></div>
						<div class="addthis_toolbox addthis_default_style " style="width: 180px; margin:0 auto;">
							<a class="addthis_button_preferred_1"></a>
							<a class="addthis_button_preferred_2"></a>
							<a class="addthis_button_preferred_3"></a>
							<a class="addthis_button_preferred_4"></a>
							<a class="addthis_button_compact"></a>
							<a class="addthis_counter addthis_bubble_style"></a>
						</div>
						<script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
						<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5119c82f3938cb62"></script>
						<!-- AddThis Button END -->
					</div>
				</div>
			</div>
			<div class="info-frame series">
				<div class="info-section series-info">
					<!--<div class="title series"><?php echo $seriesObj->name;?></div>-->
					<div class="content" style="font-size: 14px;"><?php echo $ch->html2text($seriesObj->tagline);?></div>
					<!-- <div class="series_link">View all in series</div> -->
				</div>
				<div class="h20"></div>
			</div>
			<div class="info-frame">
				<!--
				<div class="info-section authors-info">
					<div class="title">AUTHOR(S):</div>
					<div class="content">
						<?php //$ch->printAuthors($titleObj, ' / ');?>
					</div>
				</div>
				-->
				
				<div class="info-section region-info">
					<div class="title">REGION:</div>
					<div class="content"><?php $ch->printRegions($seriesObj, ', ');?></div>
				</div>
				
				<div class="info-section yearlevel-info">
					<div class="title">LEVELS:</div>
					<div class="content"><?php $ch->printLevels($seriesObj, ' / ');?></div>
				</div>
				
			</div>
			<div class="action-frame">
				<a href="javascript:void(0);" class="cup_request_more_info_btn" ref_title="(SERIES) <?php echo str_replace('"', '\"', $seriesObj->name);?>" ref_isbn="<?php echo "na"?>">
					<div class="require-more-info">
						Request more information
					</div>
				</a>
				
				<div class="formats-frame">
					<div class="title">This series includes the following components</div>
					<div class="formats"><?php $ch->printFormats($seriesObj);?></div>
				</div>
			</div>
			<div class="clr"></div>
			<div class="h15_spacer"></div>
		</div>

		<table class="detail-info-frame">
			<tr>
				<td class="detail-menu">
					<div class="spacer"></div>
					<div class="item" ref="series-information">Series information</div>
					<div class="spacer"></div>
					<div class="item" ref="included-components">Included components</div>
					<div class="spacer"></div>
					<div class="item" ref="complete-series">Complete series</div>
					<div class="spacer"></div>
					
					
					<div class="spacer"></div>
					<div class="spacer"></div>
					<div class="spacer"></div>
					<div class="spacer"></div>
					<div class="spacer"></div>
					<div class="spacer"></div>
				</td>
				<td class="detail-content">
					<div class="spacer"></div>
					<div class="item" ref="series-information">
						<?php echo $seriesObj->longDescription;?>
					</div>
					<div class="item" ref="included-components">
						<?php $ch->printFormats($seriesObj, true);?>
					</div>
					<div class="item" ref="complete-series" style="margin:0px">
						<?php
							$title_count = count($series_titles);
						?>
						<?php foreach($series_titles as $idx => $each_title):?>
							<?php
								$className = "title-item";
								if($idx + 1 == $title_count){
									$className = "title-item end";
								}
							?>
							<div class="<?php echo $className;?>">
								<div class="spacer-padding"></div>
									<div class="image-frame">
										<div class="relative_frame">
											<img src="<?php echo $each_title->getImageURL(90);?>"/>
											<?php if($each_title->new_product_flag):?>
											<div class="cup-new-product-90"></div>
											<?php endif;?>
										</div>
									</div>
									<div class="info-frame">
										<div class="title-info"><a href="<?php echo $each_title->getUrl();?>"><?php echo $each_title->name;?></a></div>
										<div class="author-info">
											<?php $ch->printAuthors($each_title, ' / ');?>
										</div>
										<div class="description-info">
											<?php 
											$words_length = 200;
											if(strlen($each_title->shortDescription) > $words_length):?>
												<?php echo substr($each_title->shortDescription, 0, strpos(wordwrap($each_title->shortDescription, $words_length), "\n")); ?> ...
											<?php else:?>
												<?php echo $each_title->shortDescription;?>
											<?php endif;?>
										</div>
									</div>
									
									<div class="action-info">
										<div class="price-info">
											<?php Loader::packageElement('page_component/title_price', 'cup_content', array('titleObject' => $each_title)); ?>
										</div>
										<div class="isbn-info">
											ISBN <?php echo $each_title->isbn13;?>
										</div>
										<div class="format-info">
											INCLUDED COMPONENTS
											<div class="formats_frame">
											<?php $ch->renderFormats($each_title->formats);?>
											</div>
										</div>
									</div>
									<div style="clear:both;width:1px;height:0px;"></div>
									
								<div class="spacer-padding"></div>
							</div>
						<?php endforeach;?>
					</div>
					
					
					<div class="spacer"></div>
				</td>
			</tr>
		</table>
	</div>
	<div style="clear:both; width:1px; height:0px;"></div>
</div>
<div style="clear:both; width:1px; height:0px;"></div>
<div style="clear:both; width:1px; height:30px;"></div>


<script>
	<?php 
		$uh = Loader::helper('concrete/urls');
		$contact_form_link = $uh->getToolsURL('request_more_contact_form', 'cup_content');
	?>
	var contact_form_link = "<?php echo $contact_form_link;?>";

	jQuery(document).ready(function(){
		jQuery('.detail-info-frame .detail-content .item').hide();
		jQuery('.detail-info-frame .detail-menu .item').first().addClass('active');
		jQuery('.detail-info-frame .detail-content .item').first().show();
		
		jQuery('.detail-info-frame .detail-menu .item').hover(
					function(){	jQuery(this).addClass('hover');}, 
					function(){ jQuery(this).removeClass('hover');}
			);
		
		jQuery('.detail-info-frame .detail-menu .item').click(function(){
			var ref = jQuery(this).attr('ref');
			var old_ref = jQuery('.detail-info-frame .detail-menu .item.active').attr('ref');
			jQuery('.detail-info-frame .detail-menu .item.active').removeClass('active');
			jQuery('.detail-info-frame .detail-content .item[ref="'+old_ref+'"]').fadeOut(200, function(){
				jQuery('.detail-info-frame .detail-content').find('.item[ref="'+ref+'"]').fadeIn(200);
			});
			jQuery(this).addClass('active');
		});
		
		
		jQuery('a.cup_request_more_info_btn').click(function(){
			var titleName = jQuery(this).attr('ref_title');
			var titleISBN = jQuery(this).attr('ref_isbn');
			
			jQuery.colorbox({
					html:'<div style="width:630px;height:500px" id="popup-window-content"></div>',
					width: 650,
					height: 555,
					});
					
			var submit_data = {
						'title': titleName,
						'isbn': titleISBN
					};
		
			jQuery.ajax({
				type: 'post',
				url: contact_form_link, 
				data: submit_data, //jQuery(this).serialize(),
				success: function(html_data){
					var p = jQuery('#popup-window-content').parent();
					p.empty();
					p.html(html_data);
					p.css('background', '#ebf4f6');
				}
			});
		});
	});
	
	
</script>

<?php echo $bodyCode;?>