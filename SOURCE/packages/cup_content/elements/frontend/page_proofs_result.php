<?php
	$ch = Loader::helper('cup_content_html', 'cup_content');

	$uh = Loader::helper('url');
	$uh2 = Loader::helper('concrete/urls');
	$titles = $list->getPage();
	
	$url_change_region =  $uh2->getToolsURL('frontend/inspection_change_region', 'cup_content');
	$url_add_item =  $uh2->getToolsURL('frontend/inspection_add_item', 'cup_content');
	$url_remove_item =  $uh2->getToolsURL('frontend/inspection_remove_item', 'cup_content');

	if(!isset($_SESSION['inspection_copy']['order_list'])){
		$_SESSION['inspection_copy']['order_list'] = array();
	}
?>

<div class="cup-result-tool-frame">
	<?php if(strcmp($_SESSION['DEFAULT_LOCALE'], 'en_AU') == 0):?>
		<?php 
			$region_options = array('ALL'=>'All Australia',
									'ACT'=>'Australian Capital Territory',
									'NSW'=>'New South Wales', 
									'NT'=>'Northern Territory', 
									'QLD'=>'Queensland', 
									'SA'=>'South Australia', 
									'TAS'=>'Tasmania', 
									'VIC'=>'Victoria', 
									'WA'=>'Western Australia');
		?>
		<div class="region_select_frame">
			Choose region <select id="australia_region_selection">
				<?php foreach($region_options as $key => $value):?>
					<?php if(strcmp($value, $_SESSION['inspection_copy']['filter_region']) == 0):?>
						<option value="<?php echo $value;?>" selected="selected"><?php echo $key;?></option>
					<?php else:?>
						<option value="<?php echo $value;?>"><?php echo $key;?></option>
					<?php endif;?>
				<?php endforeach;?>
			</select>
		</div>
		
		<script>
			jQuery('#australia_region_selection').change(function(){
				var region_change_url = "<?php echo $url_change_region;?>";
				var parent = jQuery(this).parent();
				parent.addClass('loading');
				jQuery.get(
					region_change_url,
					{region: jQuery(this).val()},
					function(){
						//window.location.href = window.location.href;
						window.location.reload();
					}
				);
				
				return false;
			});
		</script>
	<?php endif;?>

	<?php 
		//$page_size = $_GET['cc_size'];
		$args = array();
		if(isset($_GET['cc_size'])){
			$args['cc_size'] = $_GET['cc_size'];
		}
		if(isset($_GET['q_department'])){
			$args['q_department'] = $_GET['q_department'];
		}
		$args['cc_sort'] = 'asc';
		$url_asc = $uh->setVariable($args);
		$args['cc_sort'] = 'desc';
		$url_desc = $uh->setVariable($args);
		$class_asc = "";
		$class_desc = "";
		if(isset($_GET['cc_sort']) && $_GET['cc_sort']=='asc'){
			$class_asc = 'active';
		}
		if(isset($_GET['cc_sort']) && $_GET['cc_sort']=='desc'){
			$class_desc = 'active';
		}
	?>
	Sort <a class="<?php echo $class_asc;?>" onclick="return sortColumn(this);" href="<?php echo $url_asc;?>">A-Z</a> | <a class="<?php echo $class_desc;?>" onclick="return sortColumn(this);" href="<?php echo $url_desc;?>">Z-A</a>

	&nbsp;
	
	<?php
		$args = array();
		if(isset($_GET['cc_size'])){
			$args['cc_size'] = $_GET['cc_size'];
		}
		if(isset($_GET['cc_sort'])){
			$args['cc_sort'] = $_GET['cc_sort'];
		}
		$args['q_department'] = 'Primary';
		$url_primary = $uh->setVariable($args);
		$args['q_department'] = 'Secondary';
		$url_secondary = $uh->setVariable($args);
		
		$class_asc = "";
		$class_desc = "";
		if(isset($_GET['q_department']) && $_GET['q_department']=='Primary'){
			$class_primary = 'active';
		}
		if(isset($_GET['q_department']) && $_GET['q_department']=='Secondary'){
			$class_secondary = 'active';
		}
	?>
	
	<a class="<?php echo $class_primary;?>" onclick="return sortColumn(this);" href="<?php echo $url_primary;?>">Primary</a> | <a class="<?php echo $class_secondary;?>" onclick="return sortColumn(this);" href="<?php echo $url_secondary;?>">Secondary</a>
	
</div>

<?php if(count($titles) < 1):?>
<div class="cup-content-empty-result-message">
	No results found
</div>
<?php else:?>
	<div class="cup-result-content">
		<?php  foreach($titles as $title):?>
			<div class="page_proofs_result_item">
				<div class="spacer"></div>
				<div class="image_frame">
					<img src="<?php echo $title->getImageURL(90);?>"/>
				</div>
				<div class="info_frame">
					<div class="title_info"><?php echo $title->name;?></div>
					<div class="isbn_info">ISBN: <?php echo $title->isbn13;?></div>
					<div class="view_chapters">View chapters</div>
					<div class="hide_chapters">Hide chapters</div>
				</div>
				<div class="action_frame">
					<div class="item_expand_btn"></div>
				</div>
				<div style="clear:both; width:0px; height:0px;"></div>
				<div class="spacer"></div>
				<div class="details-frame">
					<div class="inner">
						<div class="action-frame">
							<div class="spacer"></div>
							<a href="<?php echo $ch->url('/education/inspection_copy/place_order/'.$title->id);?>" gae-category="PP Inspect Copy" gae-value="<?php echo htmlentities($title->name);?>"><div class="inspection_copy_btn">Order an inspection copy</div>
							<a href="<?php echo $title->getUrl();?>"><div class="goto_product_page_btn">Go to product page</div></a>
							<a href="<?php echo $ch->url('/About/Contact-Us');?>" gae-category="PP Contact" gae-value="<?php echo htmlentities($title->name);?>"><div class="contact_btn">Contact your<br/><span>Education Resource Consultant</span></div></a>
							<div class="spacer"></div>
						</div>
						<div class="chapters-frame">
							<div class="spacer"></div>
							<?php Loader::packageElement('title_sample_page/display', 'cup_content', array('sample_pages' => $title->getSamplePages(true)));?>
							<div class="spacer"></div>
						</div>
						<div style="clear:both;width:0px;height:0px;"></div>
					</div>
				</div>
				
			</div>
		<?php endforeach;?>
	</div>
	<div class="pagination">
		<?php Loader::packageElement('frontend/inspection_copy/pagination_bottom', 'cup_content', array('list'=>$list));?>
	</div>

	<script>
		jQuery('.page_proofs_result_item .action_frame .item_expand_btn').click(function(){
			var btn = jQuery(this);
			var item = btn.parent().parent();
			var extended_block = item.find('.details-frame');
			var view_chapters = item.find('.view_chapters');
			var hide_chapters = item.find('.hide_chapters');
			if(btn.hasClass('active')){
				extended_block.slideUp(300, function(){
					btn.removeClass('active');
					view_chapters.show();
					hide_chapters.hide();
				});
			}else{
				extended_block.slideDown(300, function(){
					btn.addClass('active');
					view_chapters.hide();
					hide_chapters.show();
				});
			}
		});
		
		jQuery('.page_proofs_result_item .view_chapters').click(function(){
			var view_chapters = jQuery(this);
			var item = jQuery(this).parent().parent();
			var expand_btn = item.find('.action_frame .item_expand_btn');
			var extended_block = item.find('.details-frame');
			var hide_chapters = item.find('.hide_chapters');
			
			if(expand_btn.hasClass('active')){
				extended_block.slideUp(300, function(){
					expand_btn.removeClass('active');
					view_chapters.show();
					hide_chapters.hide();
				});
			}else{
				extended_block.slideDown(300, function(){
					expand_btn.addClass('active');
					view_chapters.hide();
					hide_chapters.show();
				});
			}
		});
		
		jQuery('.page_proofs_result_item .hide_chapters').click(function(){
			var hide_chapters = jQuery(this);
			var item = jQuery(this).parent().parent();
			var expand_btn = item.find('.action_frame .item_expand_btn');
			var extended_block = item.find('.details-frame');
			var view_chapters = item.find('.view_chapters');
			
			if(expand_btn.hasClass('active')){
				extended_block.slideUp(300, function(){
					expand_btn.removeClass('active');
					view_chapters.show();
					hide_chapters.hide();
				});
			}else{
				extended_block.slideDown(300, function(){
					expand_btn.addClass('active');
					view_chapters.hide();
					hide_chapters.show();
				});
			}
		});
	</script>
<?php endif;?>