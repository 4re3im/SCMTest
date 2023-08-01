<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php 
	Loader::model('title_downloadable_file/model', 'cup_content');
	
	$ch = Loader::helper('cup_content_html', 'cup_content');
	$uh = Loader::helper('url');
	Loader::helper('tools', 'cup_content');
	
	$this->addHeaderItem($html->javascript('colorbox/jquery.colorbox-min.js', 'cup_content'));
	$this->addHeaderItem($html->css('../js/colorbox/colorbox.css', 'cup_content'));
	
	$pageTitle = $titleObj->getDisplayName();
?>

<?php Loader::element('frontend/simple_heading', array(), 'cup_content');?>
<div class="h50_spacer bg_light_blue2"></div>
<div class="breadcrumb bg_light_blue2"></div>
<div class="content-page-title bg_light_blue2"><h1><?php echo $titleObj->getDisplayName();?></h1></div>
<div class="spacer_bar blue"></div>
<div class="cup-content-master-frame">
	<div class="right-sidebar-content-frame">
		<?php $supporting_titles = $titleObj->getSupportingObjects();
		if(count($supporting_titles) > 0):?>
		<div class="related-title-frame">
			<div class="title">SUPPORTING PRODUCTS</div>
			<?php foreach($supporting_titles as $each_title_obj):?>
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
		
		
		
		<?php $related_titles = $titleObj->getRelatedObjects();
		if(count($related_titles) > 0):?>
		<div class="related-title-frame">
			<div class="title">You may also be interested in</div>
			<?php foreach($related_titles as $each_title_obj):?>
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
					<img src="<?php echo $titleObj->getImageUrl(180);?>" alt="<?php echo $titleObj->name;?>"/>
					<?php if($titleObj->new_product_flag):?>
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
			<div class="info-frame">
				<?php if(strcmp($titleObj->type, 'part of series') == 0):?>
					<?php $seriesObj = $titleObj->getSeriesObject();?>
					<div class="info-section series-info">
                        <?php $series_url = rtrim($this->url('/series', $seriesObj->prettyUrl), '/');?>
						<div class="title series"><a href="<?php echo $series_url;?>"><?php echo $seriesObj->name;?></a></div>
						<!-- <div class="content"><?php echo $ch->html2text($seriesObj->shortDescription);?></div> -->
						<!-- <div class="series_link">View all in series</div> -->
					</div>
				<?php endif;?>
				
				<div class="info-section authors-info">
					<div class="title">AUTHOR(S):</div>
					<div class="content">
						<?php $ch->printAuthors($titleObj, ' / ');?>
					</div>
				</div>
				
				<div class="info-section region-info">
					<div class="title">REGION:</div>
					<div class="content"><?php $ch->printRegions($titleObj, ', ');?></div>
				</div>
				
				<div class="info-section yearlevel-info">
					<div class="title">LEVELS:</div>
					<div class="content"><?php $ch->printLevels($titleObj, ' / ');?></div>
				</div>
				
				
				<?php
					$showSubject = true;
					if($titleObj->getSeriesObject()){
						$sr = $titleObj->getSeriesObject();
						if(in_array($sr->seriesID, array('312', '314', '292', '290', '288'))){
							$showSubject = false;
						}
					}
				?>
				<?php if($showSubject):?>
				<div class="info-section subject-info">
					<div class="title">SUBJECT AREA:</div>
					<div class="content"><?php $ch->printSubjects($titleObj, ' / ');?></div>
				</div>
				<?php endif;?>
				
				<div class="info-section edition-info">
					<div class="title">EDITION:</div>
					<div class="content"><?php echo $titleObj->edition;?></div>
				</div>
				
				<div class="info-section isbn-info">
					<div class="title">ISBN:</div>
					<div class="content"><?php echo $titleObj->isbn13;?></div>
				</div>
				
				<?php $pub_timestamp = strtotime($titleObj->publishDate);?>
				<?php if($pub_timestamp !== false && $pub_timestamp > strtotime('1970-01-01 00:00:00') && $pub_timestamp < strtotime('2100-01-01 00:00:00')):?>
				<div class="info-section publication-info">
					<div class="title">PUBLICATION DATE:</div>
					<div class="content"><?php echo date('d/m/Y', $pub_timestamp)?></div>
				</div>
				<?php endif;?>
				<!--
				<div class="info-section availability-info">
					<div class="title">AVAILABILITY:</div>
					<div class="content"><?php echo $titleObj->availability;?></div>
				</div>
				-->
			</div>
			<div class="action-frame">
				<?php Loader::packageElement('page_component/title_price', 'cup_content', array('titleObject' => $titleObj, 'display_quantity' => true)); ?>
				
				<a href="javascript:void(0);" class="cup_request_more_info_btn" ref_title="<?php echo str_replace('"', '\"', $titleObj->getDisplayName());?>" ref_isbn="<?php echo $titleObj->isbn13;?>">
					<div class="require-more-info">
						Request more information
					</div>
				</a>
				
				<div class="spacer"></div>
				<div class="formats-frame">
					<div class="title">This title includes the following components</div>
					<div class="formats"><?php $ch->printFormats($titleObj);?></div>
				</div>
				
				<?php if(strlen(trim($titleObj->goUrl)) > 0):?>
					<a href="<?php echo trim($titleObj->goUrl);?>">
						<div class="go-preview-frame">
							<div class="title">Preview</div>
							<div class="content">Interactive Textbook</div>
						</div>
					</a>
				<?php endif;?>
				
				<?php if($titleObj->hasInspectionCopy):?>
					<a href="<?php echo $ch->url('/education/inspection_copy/place_order/'.$titleObj->id);?>">
						<div class="inspection-copy-frame">
							<div class="title">Order an inspection copy</div>
						</div>
					</a>
				<?php endif;?>
			</div>
			<div class="clr"></div>
			<div class="h15_spacer"></div>  
		</div>
		<?php
			$sample_pages = $titleObj->getSamplePages();
		?>
		<table class="detail-info-frame">
			<tr>
				<td class="detail-menu">
					<div class="spacer"></div>
					<div class="item" ref="title-information">Title information</div>
					<div class="spacer"></div>
					<div class="item" ref="included-components">Included components</div>
					<div class="spacer"></div>
					
					<?php if(strlen($titleObj->content) > 0):?>
					<div class="item" ref="contents">Contents</div>
					<div class="spacer"></div>
					<?php endif;?>
					
					<?php if(strlen(trim($titleObj->previewUrl)) > 0 || count($sample_pages) > 0):?>
					<div class="item" ref="sample-pages">Sample pages</div>
					<div class="spacer"></div>
					<?php endif;?>
					
					<?php //if($titleObj->getSeriesObject()):?>
					<?php if(strcmp($titleObj->type, 'part of series') == 0):?>
					<div class="item" ref="the-complete-series">The complete series</div>
					<div class="spacer"></div>
					<?php endif;?>
					
					<div class="item" ref="about-the-authors">About the authors</div>
					<div class="spacer"></div>
					
					<?php if(strlen(trim($titleObj->review))>0):?>
					<div class="item" ref="reviews">Reviews</div>
					<?php endif;?>

                    <?php if(strcmp($titleObj->type, 'part of series') == 0):?>
                        <?php if(strlen(trim($titleObj->getSeriesObject()->reviews))>0):?>
                            <div class="item" ref="reviews">Reviews</div>
                        <?php endif;?>
                    <?php else:?>
                        <?php if(strlen(trim($titleObj->review))>0):?>
                            <div class="item" ref="reviews">Reviews</div>
                        <?php endif;?>
                    <?php endif;?>
					
					<div class="spacer"></div>
					<div class="spacer"></div>
					<div class="spacer"></div>
					<div class="spacer"></div>
					<div class="spacer"></div>
					<div class="spacer"></div>
				</td>
				<td class="detail-content">
					<div class="spacer"></div>
					<div class="item" ref="title-information">
						<?php echo $titleObj->getSelectedDescription();?>
						<?php //echo $title->shortDescription;?>
						<div><?php echo $titleObj->feature;?></div>
					</div>
					<div class="item" ref="included-components">
						<?php $ch->printFormats($titleObj, true);?>
					</div>
					
					<?php if(strlen($titleObj->content) > 0):?>
						<div class="item" ref="contents"><?php echo $titleObj->content;?></div>				
					<?php endif;?>
					
					
					<?php if(strlen(trim($titleObj->previewUrl)) > 0 || count($sample_pages) > 0):?>
					<div class="item" ref="sample-pages">
						<p>We want to give you the opportunity to view sample pages of our titles so it's easier for you to make decisions.</p>
						<?php if(count($sample_pages) > 0):?>
							<?php Loader::packageElement('title_sample_page/display', 'cup_content', array('sample_pages' => $sample_pages));?>
						<?php endif;?>
						
						<?php if(strlen(trim($titleObj->previewUrl)) > 0):?>
							<ul>
								<li>
									<a target="_blank" href="<?php echo trim($titleObj->previewUrl);?>">A sample chapter is available for preview on Cambridge GO</a>
								</li>
							</ul>
						<?php endif;?>
					</div>
					<?php endif;?>
					
					<?php //if($titleObj->getSeriesObject()):?>
					<?php if(strcmp($titleObj->type, 'part of series') == 0):?>
					<div class="item nomargin" ref="the-complete-series">
						<?php $ch->printSeries($titleObj);?>
					</div>
					<?php endif;?>
					
					<div class="item" ref="about-the-authors">
						<?php $ch->printAuthors($titleObj, true, true);?>
					</div>

					<div class="item" ref="reviews">
						<?php if(strcmp($titleObj->type, 'part of series') == 0):?>
                            <?php echo $titleObj->getSeriesObject()->reviews;?>
                        <?php else:?>
                            <?php echo $titleObj->review;?>
                        <?php endif;?>
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

<!--
Hello World;
-->
<?php echo $bodyCode;?>