<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
// $c1 = Page::getByPath('/dashboard/cup_content');
// $cp1 = new Permissions($c1);
// if (!$cp1->canRead()) { 
	// die(_("Access Denied."));
// }

$u = new User();
/*
$cnt = Loader::controller('/dashboard/core_commerce/products/search');
$productList = $cnt->getRequestedSearchResults();

$products = $productList->getPage();
$pagination = $productList->getPagination();
$searchType = $_REQUEST['searchType'];


Loader::packageElement('product/search_results', 'core_commerce', array('products' => $products, 'searchType' => $searchType, 'productList' => $productList, 'pagination' => $pagination));
*/

Loader::model('title/list', 'cup_content');
Loader::model('title/model', 'cup_content');

$input_fieldname = 'titleIDs';


$list = new CupContentTitleList();
$list->setItemsPerPage(200);
$list->sortBy('name', 'asc');

if (isset($_REQUEST['keywords']) && $_REQUEST['keywords'] != '') {
	$list->filterByKeywords($_REQUEST['keywords']);
}

if (isset($_REQUEST['isbn']) && $_REQUEST['isbn'] != '') {
	$list->filterByISBN($_REQUEST['isbn']);
}

if (isset($_REQUEST['fieldname']) && $_REQUEST['fieldname'] != '') {
	$input_fieldname = $_REQUEST['fieldname'];
}

$selected_values = array();
if(isset($_REQUEST['selected_values'])) {
	$selected_values = $_REQUEST['selected_values'];
}


$results = $list->getPage();

$selected_objects = array();

if(count($selected_values) > 0){
	$tmp_results = array();
	foreach($results as $each){
		if(!in_array($each->id, $selected_values)){
			$tmp_results[] = $each;
		}
	}
	$results = $tmp_results;
	
	foreach($selected_values as $each_title_id){
		$tmp_obj = CupContentTitle::fetchByID($each_title_id);
		if($tmp_obj){
			$selected_objects[] = $tmp_obj;
		}
	}
}


if(isset($_REQUEST['list-only']) && $_REQUEST['list-only'] == 'yes'):?>
	<?php foreach($results as $each):?>
		<?php $series_string = "";
			if(strlen($each->series) > 0){
				$series_string = " (Series: ".$each->series.")";
			}
		?>
		<div class="popup-selection-item" onclick="popup_selection_item_click(this)" ref="<?php echo $each->id;?>"><?php echo $each->generateProductName().$series_string;?></div>
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

#popup-header input#search-isbn{
	border: 0px;
	border-bottom: 1px solid #444444;
	pardding:0px 5px;
}

.float_left{
	float: left;
}

#popup-selected-area{
	width: 230px;
	height: 340px;
	margin: 0 5px 0 10px;
	border: 1px solid #BBB;
	overflow-x: hidden;
	overflow-y: scroll;
}

#popup-tool{
	float: left;
	width: 100px;
	text-align: center;
	overflow: hidden;
}

#popup-selection-area{
	width: 230px;
	height: 340px;
	margin: 0 10px 0 5px;
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

.popup-selection-item.selected{
	background: #44BBFF;
}
</style>

<?php $wform = Loader::helper('wform', 'cup_content');
		$uh = Loader::helper('concrete/urls');
		$dashboard_selection_link = $uh->getToolsURL('title/dashboard_multiple_selection', 'cup_content');
?>
<div style="width:600px;height:470px;">
	<div id="popup-header">
		Choose Titles [<?php echo $input_fieldname;?>]
		<form action="<?php echo $dashboard_selection_link;?>" method="get" id="popup-search-form">
			<div style="text-align:right;float:right;margin:0 10px;">
				<input type="submit" name="submit" value="Search" id="search-submit"/>
			</div>
			<div style="text-align:right;float:right;margin:0 10px;">
				Keywords: <input type="name" name="keywords" id="search-keywords"/>
			</div>
			<div style="text-align:right;float:right;margin:0 10px;">
				ISBN: <input type="name" name="isbn" id="search-isbn"/>
			</div>
		</form>
	</div>
	<div class="float_left">
		<div style="margin: 0px 10px">Selected Titles</div>
		<div id="popup-selected-area">
			<?php foreach($selected_objects as $each):?>
				<?php $series_string = "";
					if(strlen($each->series) > 0){
						$series_string = " (Series: ".$each->series.")";
					}
				?>
				<div class="popup-selection-item" onclick="popup_selection_item_click(this)" ref="<?php echo $each->id;?>"><?php echo $each->generateProductName().$series_string;?></div>
			<?php endforeach;?>
		</div>
	</div>
	<div id="popup-tool">
		<div style="height:100px;width:100%"></div>
			<?php echo $wform->button('ADD', 'javascript:popup_addSelectedValues();', array('style'=>'font-size:9px;'), 'blue');?>
		<div style="height:80px;width:100%"></div>
			<?php echo $wform->button('REMOVE', 'javascript:popup_removeSelectedValues();', array('style'=>'font-size:9px;'), 'blue');?>
	</div>
	<div class="float_left">
		<div style="margin: 0px 10px">Available Titles</div>
		<div id="popup-selection-area">
			<?php foreach($results as $each):?>
				<?php $series_string = "";
					if(strlen($each->series) > 0){
						$series_string = " (Series: ".$each->series.")";
					}
				?>
				<div class="popup-selection-item" onclick="popup_selection_item_click(this)" ref="<?php echo $each->id;?>"><?php echo $each->generateProductName().$series_string;?></div>
			<?php endforeach;?>
		</div>
	</div>
	<div style="clear:both;height:0px;0px;"></div>
	<div>
		<div style="float:right;font-size:12px;font-style:italic;color:444444;">
			ps. maximun of displaying 200 records
		</div>
		<?php echo $wform->button('Confirm', 'javascript:applyTitlesValues();', array('style'=>'font-size:9px;'), 'blue');?>
	</div>
</div>


<script>
	/*
	jQuery('.popup-selection-item').live('click', function(){
		if(jQuery(this).hasClass('selected')){
			jQuery(this).removeClass('selected');
		}else{
			jQuery(this).addClass('selected');
		}
	});
	*/
	/*
	jQuery('.popup-selection-item').click(function(){
		if(jQuery(this).hasClass('selected')){
			jQuery(this).removeClass('selected');
		}else{
			jQuery(this).addClass('selected');
		}
	});
	*/
	
	var popup_addSelectedValues = function(){
		jQuery('.popup-selection-item.selected').each(function(){
			jQuery(this).removeClass('selected');
			jQuery('#popup-selected-area').append(jQuery(this));
		});
	}
	
	var popup_removeSelectedValues = function(){
		jQuery('#popup-selected-area .popup-selection-item.selected').each(function(){
			jQuery(this).removeClass('selected');
			jQuery('#popup-selection-area').append(jQuery(this));
		});
	}
	
	var popup_selection_item_click = function(dom){
		if(jQuery(dom).hasClass('selected')){
			jQuery(dom).removeClass('selected');
		}else{
			jQuery(dom).addClass('selected');
		}
	}
	
	var applyTitlesValues = function(){
		var html_code = "";
		
		var temp_value = "";
		var temp_content = "";
		var temp_hidden_field = "";
		var temp_value_item = "";
		var fieldname_section = '<?php echo $input_fieldname;?>';
		var fieldname = '<?php echo $input_fieldname;?>[]';
		//var selected_data = new Array();
		var is_empty = true;
		jQuery('#popup-selected-area .popup-selection-item').each(function(){
			//selected_data.push(jQuery(this).html());
			temp_content = jQuery(this).html();
			temp_value = jQuery(this).attr('ref');
			temp_hidden_field = '<input type="hidden" name="'+fieldname+'" value="'+temp_value+'"/>';
			temp_value_item = '<span class="value_item">'+temp_content+temp_hidden_field+'</span>';
			
			html_code += temp_value_item;
			
			is_empty = false;
		});
		
		if(is_empty){
			html_code = '<i style="empty_value_message">empty value</i>';
		}
		
		jQuery('.multiple-items-group[ref="'+fieldname_section+'"]').empty();
		jQuery('.multiple-items-group[ref="'+fieldname_section+'"]').append(jQuery(html_code));
		
		jQuery.colorbox.close();
	}
	
	jQuery('#popup-search-form').submit(function(){
		var form_data = jQuery(this).serialize();
		//alert(form_data);
		//alert(JSON.stringify(form_data));
		
		var selected_data = new Array();
		jQuery('#popup-selected-area .popup-selection-item').each(function(){
			selected_data.push(jQuery(this).html());
		});
		
		var submit_data = {
								'keywords': jQuery(this).find('input[name="keywords"]').val(),
								'isbn': jQuery(this).find('input[name="isbn"]').val(),
								'list-only': 'yes',
								'selected_values': selected_data
							};
		
		var action_url = jQuery(this).attr('action');
		var submit_type = jQuery(this).attr('method');
		if(typeof(submit_type) === 'undefined' || submit_type === false){
			submit_type = 'GET';
		}
		
		jQuery('#popup-selection-area').addLoadingMask();
		jQuery.ajax({
			type: submit_type,
			url: action_url, 
			data: submit_data, //jQuery(this).serialize(),
			success: function(html_data){
				jQuery('#popup-selection-area').html(html_data);
				jQuery('#popup-selection-area').removeLoadingMask();
			}
		});
		
		return false;
	});
</script>