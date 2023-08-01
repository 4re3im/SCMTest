<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
// $c1 = Page::getByPath('/dashboard/cup_content');
// $cp1 = new Permissions($c1);
// if (!$cp1->canRead()) { 
	// die(_("Access Denied."));
// }

$u = new User();

Loader::model('title/list', 'cup_content');
Loader::model('title/model', 'cup_content');

$list = new CupContentTitleList();
$list->filterByHasAccessCode();
$list->setItemsPerPage(99999);
$list->sortBy('name', 'asc');

if(isset($_GET['keywords'])){
	$list->filterByNameKeywords($_GET['keywords']);
}

if(isset($_GET['isbn'])){
	$list->filterByISBN($_GET['isbn']);
}



$selected_values = array();
if(isset($_REQUEST['selected_values'])) {
	$selected_values = $_REQUEST['selected_values'];
}


$results = $list->getPage();

if(count($selected_values) > 0){
	/*
	$tmp_results = array();
	foreach($results as $each){
		if(!in_array($each->titleID, $selected_values)){
			$tmp_results[] = $each;
		}
	}
	$results = $tmp_results;
	*/
	$tmp_results = array();
	foreach($selected_values as $each_title_id){
		$tmp_results[] = CupContentTitle::fetchByID($each_title_id);
	}
	
	$selected_values = $tmp_results;
}

if(isset($_REQUEST['list-only']) && $_REQUEST['list-only'] == 'yes'):?>
	<?php foreach($results as $each):?>
		<?php $series_string = "";
			if(strlen($each->series) > 0){
				$series_string = " (Series: ".$each->series.")";
			}
		?>
		<div class="popup-selection-item" onclick="popup_selection_single_item_click(this)" ref="<?php echo $each->id;?>"><?php echo $each->generateProductName().$series_string;?></div>
	<?php endforeach;?>
<?php 
	exit();
endif;?>

<style>
#popup-header{
	margin:0 10px;
	height: 60px;
}

#popup-header input#search-keywords{
	border: 0px;
	border-bottom: 1px solid #444444;
	pardding:0px 5px;
}

#popup-header input#search-isbn13{
	border: 0px;
	border-bottom: 1px solid #444444;
	pardding:0px 5px;
}

.float_left{
	float: left;
}

#popup-single-selected-area{
	height: 50px;
	margin: 0 5px 0 10px;
	border: 1px solid #BBB;
}

#popup-tool{
	float: left;
	width: 100px;
	text-align: center;
	overflow: hidden;
}

#popup-single-selection-area{
	height: 250px;
	margin: 0 5px 0 10px;
	border: 1px solid #BBB;
	overflow-x: hidden;
	overflow-y: scroll;
}

.popup-selection-item{
	padding: 0 5px;
	cursor: pointer;
	line-height: 24px;
	border-bottom: 1px solid #CCCCCC;
}

#popup-single-selected-area .popup-selection-item{
	border: 0px;
}


.popup-selection-item.selected{
	background: #44BBFF;
}
</style>

<?php $wform = Loader::helper('wform', 'cup_content');
		$uh = Loader::helper('concrete/urls');
		$dashboard_selection_link = $uh->getToolsURL('title/dashboard_selection_hasAccessCode', 'cup_content');
?>
<div style="width:600px;height:470px;">
	<div id="popup-header">
		Choose Title <?php echo "[".count($results)."]";?>
		<form action="<?php echo $dashboard_selection_link;?>" method="get" id="popup-search-form">
			<div style="text-align:right;margin:0 10px;float:right;">
				<input type="submit" name="submit" id="search-submit" value="Search"/>
			</div>
			<div style="text-align:right;margin:0 10px;float:right;">
				Keywords: <input type="name" name="keywords" id="search-keywords"/>
			</div>
			<div style="text-align:right;margin:0 10px;float:right;">
				ISBN: <input type="name" name="isbn" id="search-isbn13"/>
			</div>
			
			<div style="clear:both"></div>
		</form>
	</div>
	
	<div class="">
		<div style="margin: 0px 10px">Available Titles</div>
		<div id="popup-single-selection-area">
			<?php foreach($results as $each):?>
				<?php $series_string = "";
					if(strlen($each->series) > 0){
						$series_string = " (Series: ".$each->series.")";
					}
				?>
				<div class="popup-selection-item" onclick="popup_selection_single_item_click(this)" ref="<?php echo $each->id;?>"><?php echo $each->generateProductName().$series_string;?></div>
			<?php endforeach;?>
		</div>
	</div>
	<div style="width:100%;height:15px"></div>
	<div class="">
		<div style="margin: 0px 10px">Selected Title</div>
		<div id="popup-single-selected-area">
			<?php if(count($selected_values) > 0):?>
				<?php foreach($selected_values as $each_title):?>
					<?php $series_string = "";
						if(strlen($each->series) > 0){
							$series_string = " (Series: ".$each->series.")";
						}
					?>
					<div class="popup-selection-item" ref="<?php echo $each_title->id;?>"><?php echo $each_title->generateProductName().$series_string;?></div>
				<?php endforeach;?>
			<?php endif;?>
		</div>
	</div>
	<div>
		<?php echo $wform->button('Confirm', 'javascript:applyTitleValues();', array('style'=>'font-size:9px;'), 'blue');?>
	</div>
</div>


<script>
	var popup_selection_single_item_click = function(dom){
		jQuery('#popup-single-selection-area .popup-selection-item').removeClass('selected');
		
		jQuery(dom).addClass('selected');
		var series_name = jQuery(dom).attr('ref');
		
		var cloneObj = jQuery(dom).clone();
		cloneObj.removeAttr('onclick');
		cloneObj.removeClass('selected');
		//cloneObj.css({border: '0px'});
		jQuery('#popup-single-selected-area').empty();
		jQuery('#popup-single-selected-area').append(cloneObj);
	}
	
	var applyTitleValues = function(){
		var html_code = "";
		
		var temp_value = "";
		var temp_display_value = "";
		var temp_hidden_field = "";
		var temp_value_item = "";
		var fieldname = 'titleID';
		//var selected_data = new Array();
		var is_empty = true;
		jQuery('#popup-single-selected-area .popup-selection-item').each(function(){
			//selected_data.push(jQuery(this).html());
			temp_value = jQuery(this).attr('ref');
			temp_display_value = jQuery(this).html();
			temp_hidden_field = '<input type="hidden" name="'+fieldname+'" value="'+temp_value+'"/>';
			temp_value_item = '<span class="value_item">'+temp_display_value+temp_hidden_field+'</span>';
			
			html_code += temp_value_item;
			
			is_empty = false;
		});
		
		if(is_empty){
			html_code = '<i style="empty_value_message">empty value</i>';
		}
		
		jQuery('.multiple-items-group[ref="titleID"]').empty();
		jQuery('.multiple-items-group[ref="titleID"]').append(jQuery(html_code));
		
		jQuery.colorbox.close();
	}
	
	jQuery('#popup-search-form input#search-keywords').keypress(function(e) {
		if(e.which == 13) {
			jQuery('#popup-search-form').trigger('submit');
		}
	});
	
	jQuery('#popup-search-form').submit(function(){
		var form_data = jQuery(this).serialize();
		//alert(form_data);
		//alert(JSON.stringify(form_data));
		
		/*
		var selected_data = new Array();
		jQuery('#popup-selected-area .popup-selection-item').each(function(){
			selected_data.push(jQuery(this).html());
		});
		*/
		var submit_data = {
								'keywords': jQuery(this).find('input[name="keywords"]').val(),
								'isbn13': jQuery(this).find('input[name="isbn"]').val(),
								'list-only': 'yes',
								//'selected_values': selected_data
							};
		
		var action_url = jQuery(this).attr('action');
		var submit_type = jQuery(this).attr('method');
		if(typeof(submit_type) === 'undefined' || submit_type === false){
			submit_type = 'GET';
		}
		
		jQuery('#popup-single-selection-area').addLoadingMask();
		jQuery.ajax({
			type: submit_type,
			url: action_url, 
			data: submit_data, //jQuery(this).serialize(),
			success: function(html_data){
				jQuery('#popup-single-selection-area').html(html_data);
				jQuery('#popup-single-selection-area').removeLoadingMask();
			}
		});
		
		return false;
	});
</script>