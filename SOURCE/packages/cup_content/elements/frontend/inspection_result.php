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
			<div class="inspection_copy_result_item">
				<div class="spacer"></div>
				<div class="image_frame">
					<img src="<?php echo $title->getImageURL(90);?>"/>
				</div>
				<div class="info_frame">
					<div class="title_info"><a href="<?php echo $title->getUrl('education');?>"><?php echo $title->name;?></a></div>
					<div class="isbn_info">ISBN: <?php echo $title->isbn13;?></div>
				</div>
				<div class="action_frame">
					<?php if(in_array($title->id, $_SESSION['inspection_copy']['order_list'])):?>
						<div class="inspection_copy_check_btn active" ref="<?php echo $title->id;?>"></div>
					<?php else:?>
						<div class="inspection_copy_check_btn" ref="<?php echo $title->id;?>"></div>
					<?php endif;?>
				</div>
				<div style="clear:both; width:0px; height:0px;"></div>
				<div class="spacer"></div>
			</div>
		<?php endforeach;?>
	</div>
	<div class="pagination">
		<?php Loader::packageElement('frontend/inspection_copy/pagination_bottom', 'cup_content', array('list'=>$list));?>
	</div>

	<script>
		jQuery('div.inspection_copy_check_btn').click(function(){
			var item_id = jQuery(this).attr('ref');
			var _this = jQuery(this);
			if(_this.hasClass('active')){
				//remove from list
				var action_url = "<?php echo $url_remove_item;?>";
				_this.addClass('loading');
				jQuery.get(
					action_url,
					{title_id: item_id},
					function(content){
						_this.removeClass('loading');
						var json = jQuery.parseJSON(content)
						if(json.result == 'success'){
							_this.removeClass('active');
							
							jQuery('.selected_inspection_title_number_frame span#selected_inspection_title_number').text(json.total);
							if(json.total > 0){
								jQuery('.inspection_order_btn_frame').show();
							}else{
								jQuery('.inspection_order_btn_frame').hide();
							}
						}
					}
				);
			}else if(_this.hasClass('loading')){
				//do nothing 
			}else{
				//append to list
				var action_url = "<?php echo $url_add_item;?>";
				_this.addClass('loading');
				jQuery.get(
					action_url,
					{title_id: item_id},
					function(content){
						_this.removeClass('loading');
						var json = jQuery.parseJSON(content)
						if(json.result == 'success'){
							_this.addClass('active');
							
							jQuery('.selected_inspection_title_number_frame span#selected_inspection_title_number').text(json.total);
							if(json.total > 0){
								jQuery('.inspection_order_btn_frame').show();
							}else{
								jQuery('.inspection_order_btn_frame').hide();
							}
						}
					}
				);
			}
		});
	</script>
<?php endif;?>