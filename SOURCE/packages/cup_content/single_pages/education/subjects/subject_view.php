<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php 
	$ch = Loader::helper('cup_content_html', 'cup_content');
	$uh = Loader::helper('url');
	Loader::helper('tools', 'cup_content');
	
	$this->addHeaderItem($html->javascript('colorbox/jquery.colorbox-min.js', 'cup_content'));
	$this->addHeaderItem($html->css('../js/colorbox/colorbox.css', 'cup_content'));
	
?>

<?php Loader::element('frontend/simple_heading', array(), 'cup_content');?>
<div class="cup-content-master-frame">
	<div class="left-sidebar-content-frame">
		<?php Loader::element('frontend/search_sidebar', array('criteria'=>$criteria), 'cup_content');?>
	</div>
	
	<div class="main-content-frame with-left-sidebar">
		<div class="h50_spacer bg_light_blue2"></div>
		<div class="content-page-title bg_light_blue2"><h1><?php echo $subject->name;?></h1></div>
		<div class="spacer_bar bg_light_blue2"></div>
		<div class="spacer_bar blue"></div>
		<div style="width:100%;height:5px;"></div>
		<div class="cup-result-frame">
			<div class="cup-result-tool-frame">
				<?php $url_asc = $uh->setVariable(array('cc_size'=>$page_size, 'cc_sort'=>'asc'));
					$url_desc = $uh->setVariable(array('cc_size'=>$page_size, 'cc_sort'=>'desc'));
					$class_asc = "";
					$class_desc = "";
					if(isset($_GET['cc_sort']) && $_GET['cc_sort']=='asc'){
						$class_asc = 'active';
					}
					if(isset($_GET['cc_sort']) && $_GET['cc_sort']=='desc'){
						$class_desc = 'active';
					}
				?>
				Sort <a class="<?php echo $class_asc;?>" href="<?php echo $url_asc;?>">A-Z</a> | <a class="<?php echo $class_desc;?>" href="<?php echo $url_desc;?>">Z-A</a>
			</div>
			
			<div class="cup-result-content">
				<?php foreach($search->getResults() as $each_object):?>
					<?php if(strcmp(get_class($each_object),'CupContentSeries') == 0):?>
						<div class="subject-result-item series_result">
							<div class="spacer-padding"></div>
							<div class="switcher-frame" title-data="<?php echo $each_object->getID() ?>-<?php echo $c->cID ?>"></div>
							<div class="simple-series-frame">
								<div class="image-frame">
									<div class="relative_frame">
										<img src="<?php echo $each_object->getImageURL(90);?>"/>
										<?php if($each_object->isContainNewProduct()):?>
										<div class="cup-new-product-90"></div>
										<?php endif;?>
									</div>
								</div>
								<div class="info-frame">
                                    <div class="title-info"><?php echo $each_object->name;?></div>
									<div class="description-info">
										<?php //echo $each_object->shortDescription;?>
										<?php 
										$words_length = 200;
										if(strlen($each_object->tagline) > $words_length):?>
											<?php echo substr($each_object->tagline, 0, strpos(wordwrap($each_object->tagline, $words_length), "\n")); ?> ...+
										<?php else:?>
											<?php echo $each_object->tagline;?>
										<?php endif;?>
									</div>
									<div class="view_series">
										<a href="#" class="expand_series"/>View Series</a>
									</div>
								</div>
								<div class="action-info">
									<div style="width:1px;height:40px"></div>
									<div class="format-info">
										INCLUDED COMPONENTS
										<div class="formats_frame">
										<?php $ch->renderFormats($each_object->formats);?>
										</div>
									</div>
								</div>
								<div style="clear:both;width:1px;height:0px;"></div>
							</div>
							<div class="comprehensive-series-frame">
								<div class="image-frame">
									<div class="relative_frame">
										<img src="<?php echo $each_object->getImageURL(180);?>"/>
										<?php if($each_object->isContainNewProduct()):?>
										<div class="cup-new-product-180"></div>
										<?php endif;?>
										<div class="spacer-padding"></div>
										<div class="action-info">
											<div>
												<a href="javascript:void(0)" class="cup_request_more_info_btn btn" ref_title="<?php echo str_replace('"', '/"', $each_object->name);?>" ref_isbn="[series]">Request more information</a>
											</div>
											<div style="width:1px;height:15px"></div>
											<div class="format-info">
												INCLUDED COMPONENTS
												<div class="formats_frame">
												<?php $ch->renderFormats($each_object->formats);?>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="info-frame">
									<div class="title-info"><?php echo $each_object->name;?></div>
									<div class="description-info">
										<?php echo $each_object->shortDescription;?>
									</div>
									<div class="view_series active">
										<a href="#" class="hide_series"/>Hide Series</a>
									</div>
								</div>
							</div>
							
							<div style="clear:both;width:1px;height:0px;"></div>
							<div class="spacer-padding">&nbsp;</div>
							
							<div class="series-titles-frame" id="series-id-<?php echo $each_object->getID() ?>">
								<div class="title-loader" style="padding: 50px 50%;background-color:white">
									<img src="<?php echo ASSETS_URL_IMAGES?>/throbber_white_32.gif" width="32" height="32" class="" />
								</div>
								<?php
									// Nick Ingarsia, 27/10/14
									// Toggle if series titles should be loaded via AJAX or all at once.
									if(isset($loadTitleViaAJAX) && $loadTitleViaAJAX === false):
								?>
								<?php
									$titles = $each_object->getTitleObjects();
									$size = count($titles);
									foreach($titles as $idx => $each_title):
										$className = "title-item";
										if($idx + 1 == $size){
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
														<?php echo $each_title->shortDescription;?>
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
							<?php endif;?>

							</div>
						</div>
					<?php elseif(strcmp(get_class($each_object),'CupContentTitle') == 0):?>
						<div class="subject-result-item title_result">
							<div class="spacer-padding"></div>
							<div>
								<div class="image-frame">
									<div class="relative_frame">
										<img src="<?php echo $each_object->getImageURL(90);?>"/>
										<?php if($each_object->new_product_flag):?>
										<div class="cup-new-product-90"></div>
										<?php endif;?>
									</div>
								</div>
								<div class="info-frame">
									<div class="title-info"><a href="<?php echo $each_object->getUrl();?>"><?php echo $each_object->name;?></a></div>
									<div class="author-info">
										<?php $ch->printAuthors($each_object, ' / ');?>
									</div>
									<div class="description-info">
										<?php echo $each_object->shortDescription;?>
									</div>
								</div>
								<div class="action-info">
									<div class="price-info">
										<?php Loader::packageElement('page_component/title_price', 'cup_content', array('titleObject' => $each_object)); ?>
									</div>
									<div class="isbn-info">
										ISBN <?php echo $each_object->isbn13;?>
									</div>
									<div class="format-info">
										INCLUDED COMPONENTS
										<div class="formats_frame">
										<?php $ch->renderFormats($each_object->formats);?>
										</div>
									</div>
								</div>
								<div style="clear:both;width:1px;height:0px;"></div>
							</div>
							<div style="clear:both;width:1px;height:0px;"></div>
							<div class="spacer-padding">&nbsp;</div>
						</div>
					<?php endif;?>
				<?php endforeach;?>
				
				<?php if(count($search->getResults()) < 1):?>
					<div class="no_result_message_frame">
						There are no results available, please try another search.
					</div>
				<?php endif;?>
				<div style="clear:both;width:1px;height:0px;"></div>
				
				
			</div>
			<div class="pagination">
				<?php echo $ch->renderPagination($search->getPages(), $subject->getUrl(), $page_size, $criteria);?>
			</div>
		</div>
	</div>
	<div style="clear:both;width:1px;height:0px;"></div>
	<div>
		<?php
			$block_news_ticker = BlockType::getByHandle('cup_news_ticker');
		?>
		<?php if($block_news_ticker):?>
			<?php Loader::element('frontend/news_ticker', array('filter' => array('tag'=>$subject->name)), 'cup_news');?>
		<?php endif;?>
	</div>
</div>

<script>
	
	// AJAX in the titles for the series
	// Nick Ingarsia, 27/10/14
	function getTitles(seriesID, cID)
	{
		if ($('#series-id-' + seriesID + ' .title-item').length == 0) {
			var titleTool = '<?php echo Loader::helper('concrete/urls')->getToolsURL('title/get_by_series', 'cup_content') ?>';
			$.get(titleTool+'?cID=' + cID + '&seriesID=' + seriesID)
				.done(function (data) {
					$('#series-id-' + seriesID).html(data);
				});
		}
	}

	jQuery('.cup-result-content .switcher-frame').each(function(){
		jQuery(this).click(function(){
			var simple_frame = jQuery(this).parent().find('.simple-series-frame');
			var comprehensive_frame = jQuery(this).parent().find('.comprehensive-series-frame');;
			var series_titles_frame = jQuery(this).parent().find('.series-titles-frame');
			if(jQuery(this).hasClass('active')){
				simple_frame.show();
				comprehensive_frame.hide();
				series_titles_frame.hide();
				jQuery(this).removeClass('active')
			}else{
				simple_frame.hide();
				comprehensive_frame.show();
				series_titles_frame.show();
				jQuery(this).addClass('active')
				// AJAX in the titles if this hasn't been done already
				// Nick Ingarsia, 27/10/14
				var titleData = $(this).attr('title-data').split('-');
				var seriesID = titleData[0];
				var cID = titleData[1];
				getTitles(seriesID, cID);
				// End
			}
		})
	});
	
	
	jQuery('.cup-result-content  a.expand_series').click(function(){
		var item = jQuery(this).parent().parent().parent().parent();
		item.find('.switcher-frame').trigger('click');
		return false;
	});
	
	jQuery('.cup-result-content  a.hide_series').click(function(){
		var item = jQuery(this).parent().parent().parent().parent();
		item.find('.switcher-frame').trigger('click');
		return false;
	});
	
	<?php 
		$uh = Loader::helper('concrete/urls');
		$contact_form_link = $uh->getToolsURL('request_more_contact_form', 'cup_content');
	?>
	var contact_form_link = "<?php echo $contact_form_link;?>";
	
	jQuery('a.cup_request_more_info_btn').click(function(){
			var titleName = jQuery(this).attr('ref_title');
			var titleISBN = jQuery(this).attr('ref_isbn');
			
			jQuery.colorbox({
					html:'<div style="width:630px;height:500px" id="popup-window-content"></div>',
					width: 650,
					height: 540,
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
</script>