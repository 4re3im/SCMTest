<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

$u = new User();

Loader::model('format/list', 'cup_content');
Loader::model('format/model', 'cup_content');

$form = Loader::helper('form');

$title_id = $_POST['title_ID'];

$list = new CupContentFormatList();
$list->setItemsPerPage(99999);
$list->sortBy('name', 'asc');

if ($_REQUEST['keywords'] != '') {
	$list->filterByKeywords($_GET['keywords']);
}

$selected_values = array();
if(isset($_REQUEST['selected_values'])) {
	$selected_values = $_REQUEST['selected_values'];
}


$results = $list->getPage();

if(count($selected_values) > 0){
	$tmp_results = array();
	foreach($results as $each){
		if(!in_array($each->name, $selected_values)){
			$tmp_results[] = $each;
		}
	}
	$results = $tmp_results;
}

if(isset($_REQUEST['list-only']) && $_REQUEST['list-only'] == 'yes'):?>
	<?php foreach($results as $each):?>
		<div class="popup-selection-item" onclick="popup_selection_item_click(this)"><?php echo $each->name;?></div>
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

#popup-selection-area {
	width: 230px;
	height: 340px;
	margin: 0 10px 0 5px;
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
		$dashboard_select_format_link = $uh->getToolsURL('format/dashboard_selection', 'cup_content');
?>
<div style="width:600px;height:470px;">
	<div id="popup-header">
		Add/Delete Folders
	</div>

	<div class="float_left">
		<div style="margin: 0px 10px">Selected Formats</div>
		<div id="popup-selected-area">
			<?php foreach($selected_values as $each):?>
				<div class="popup-selection-item" onclick="popup_selection_item_click(this)"><?php echo $each;?></div>
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
		<div id="popup-selection-area">
			
			<div class="clearfix">
				<?php echo $form->label('FolderName', t('Folder Name') . '	<span class="ccm-required">*</span>')?>
				<div class="input">
					<?php echo $form->text('FolderName', @$entry['FolderName'], array('class' => "span6"))?>
				</div>
			</div>

		</div>
	</div>
	<div style="clear:both;height:0px;0px;"></div>
	<div>
		<?php echo $wform->button('Confirm', 'javascript:applyFormatValue();', array('style'=>'font-size:9px;'), 'blue');?>
	</div>
</div>


<script>
	
	var popup_addSelectedValues = function(){

		var dashboard_select_tabs_link =  "/dashboard/cup_content/titles/saveContentFolder/";

		var submit_data = {
					FolderName : jQuery('#FolderName').val(),
					TitleID : <?php echo $title_id; ?>
				};
		
		jQuery.ajax({
			type: 'post',
			url: dashboard_select_tabs_link, 
			data: submit_data, 
			success: function(data) {
				var html = '<div class="popup-selection-item" onclick="popup_selection_item_click(this)">'+jQuery('#FolderName').val()+'</div>';
				jQuery('#popup-selected-area').append(html);

				jQuery('#FolderName').val('');
			}
		});
		
	}
	
	var popup_removeSelectedValues = function(){
		jQuery('#popup-selected-area .popup-selection-item.selected').each(function(){
			jQuery(this).removeClass('selected');
			// ANZGO-1911
			jQuery(this).remove();
		});
	}
	
	var popup_selection_item_click = function(dom){
		if(jQuery(dom).hasClass('selected')){
			jQuery(dom).removeClass('selected');
		}else{
			jQuery(dom).addClass('selected');
		}
	}
	
	var applyFormatValue = function() {

		var html_code = "";
		var temp_value = "";
		var temp_hidden_field = "";
		var temp_value_item = "";
		var fieldname = 'contentFolders[]';
		var is_empty = true;

		jQuery('#popup-selected-area .popup-selection-item').each(function(){
			temp_value = jQuery(this).html();
			temp_hidden_field = '<input type="hidden" name="'+fieldname+'" value="'+temp_value+'"/>';
			temp_value_item = '<span class="value_item">'+temp_value+temp_hidden_field+'</span>';
			
			html_code += temp_value_item;
			
			is_empty = false;
		});
		
		if(is_empty){
			html_code = '<i style="empty_value_message">empty value</i>';
		}
		
		jQuery('.multiple-items-group[ref="contentFolders"]').empty();
		jQuery('.multiple-items-group[ref="contentFolders"]').append(jQuery(html_code));

		refreshContentFolders();
		
		jQuery.colorbox.close();
	}
	
</script>