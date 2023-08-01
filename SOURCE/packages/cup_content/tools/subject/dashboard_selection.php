<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

$u = new User();

Loader::model('subject/list', 'cup_content');
Loader::model('subject/model', 'cup_content');

$list = new CupContentSubjectList();
$list->setItemsPerPage(99999);
$list->sortBy('name', 'asc');

if ($_REQUEST['keywords'] != '') {
    // SB-401 / SB-399 - modified by jbernardez 20191119
    $list->filterByKeywords($_GET['keywords'], true);
}

$selected_values = array();
if(isset($_REQUEST['selected_values'])) {
    $selected_values = $_REQUEST['selected_values'];
}

$results = $list->getPage();

if (count($selected_values) > 0) {
    $tmp_results = array();
    foreach($results as $each){
        if (!in_array($each->name, $selected_values)) {
            $tmp_results[] = $each;
        }
    }
    $results = $tmp_results;
}

if (isset($_REQUEST['list-only']) && $_REQUEST['list-only'] == 'yes'):?>
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
        $dashboard_selection_link = $uh->getToolsURL('subject/dashboard_selection', 'cup_content');
?>
<div style="width:600px;height:470px;">
    <div id="popup-header">
        Choose Subjects
        <form action="<?php echo $dashboard_selection_link;?>" method="get" id="popup-search-form">
            <div style="text-align:right;margin:0 10px;">
                Keywords: <input type="name" name="keywords" id="search-keywords"/>
            </div>
        </form>
    </div>
    <div class="float_left">
        <div style="margin: 0px 10px">Selected Subjects</div>
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
        <div style="margin: 0px 10px">Available Subjects</div>
        <div id="popup-selection-area">
            <?php 
            foreach($results as $each):?>
                <?php
                // SB-399 added by jbernardez 20191113
                if ($each->isEnabled) {
                ?>
                <div class="popup-selection-item" onclick="popup_selection_item_click(this)"><?php echo $each->name;?></div>
                <?php } ?>
            <?php endforeach;?>
        </div>
    </div>
    <div style="clear:both;height:0px;0px;"></div>
    <div>
        <?php echo $wform->button('Confirm', 'javascript:applySubjectValues();', array('style'=>'font-size:9px;'), 'blue');?>
    </div>
</div>

<script>
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
    
    var applySubjectValues = function(){
        var html_code = "";
        
        var temp_value = "";
        var temp_hidden_field = "";
        var temp_value_item = "";
        var fieldname = 'subjects[]';
        //var selected_data = new Array();
        var is_empty = true;
        jQuery('#popup-selected-area .popup-selection-item').each(function(){
            //selected_data.push(jQuery(this).html());
            temp_value = jQuery(this).html();
            temp_hidden_field = '<input type="hidden" name="'+fieldname+'" value="'+temp_value+'"/>';
            temp_value_item = '<span class="value_item">'+temp_value+temp_hidden_field+'</span>';
            
            html_code += temp_value_item;
            
            is_empty = false;
        });
        
        if(is_empty){
            html_code = '<i style="empty_value_message">empty value</i>';
        }
        
        jQuery('.multiple-items-group[ref="subjects"]').empty();
        jQuery('.multiple-items-group[ref="subjects"]').append(jQuery(html_code));
        
        jQuery.colorbox.close();
    }
    
    jQuery('#popup-search-form').submit(function() {
        var form_data = jQuery(this).serialize();
        
        var selected_data = new Array();
        jQuery('#popup-selected-area .popup-selection-item').each(function(){
            selected_data.push(jQuery(this).html());
        });
        
        var submit_data = {
            'keywords': jQuery(this).find('input[name="keywords"]').val(),
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