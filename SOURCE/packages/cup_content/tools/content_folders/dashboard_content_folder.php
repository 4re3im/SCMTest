<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

$u = new User();

$form = Loader::helper('form');

$title_id = $_POST['title_ID'];
$folderName = trim($_POST['folderName']);

Loader::model('title/model', 'cup_content');

$folders = CupContentTitle::getFolders($title_id, $folderName);
?>
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
<br />
<div class="container">
    <div class="row">
        <div class="col-lg-9">
            <h4><?php echo $folderName ?></h4>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Content subfolder</h3>
                </div>
                <div class="panel-body">
                    <div class="container-fluid">
                        <div class="alert" role="alert" style="display:none;" id="content-folder-alert">
                            <div id="alert-text"></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-5">
                                <form class="form-inline" action="<?php echo $v->url('/dashboard/cup_content/titles/saveFolder/'); ?>" id="new-folder">
                                    <div class="form-group">
                                        <label class="sr-only" for="Heading">Heading</label>
                                        <input type="text" class="form-control" name="ContentHeading" id="Heading" placeholder="Subfolder heading" />
                                        <input type="hidden" name="titleID" value="<?php echo $title_id; ?>" />
                                        <input type="hidden" name="folderName" value="<?php echo $folderName; ?>" />
                                        <input type="submit" class="btn btn-default" value="Add" />
                                    </div>
                                </form>
                                <br />
                                <table class="table table-bordered table-hover" id="popup-selected-area">
                                    <tbody>
                                        <?php if ($folders) { ?>
                                            <?php foreach ($folders as $folder): ?>
                                                <tr>
                                                    <td class="popup-selection-item" onclick="popup_selection_item_click(this)">
                                                        <?php echo $folder['ContentHeading']; ?>
                                                        <input type="hidden" name="<?php echo str_replace(" ", "_", $folder['ContentHeading']) ?>" id="<?php echo str_replace(" ", "_", $folder['ContentHeading']) ?>" value="<?php echo $folder['ID'] ?>" />
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php } else { ?>
                                            <tr id="empty-subfolder">
                                                <td>Nothing found...</td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                <a href="javascript:openContentDetail();" class="btn btn-primary">Content Details</a>
                                <a href="javascript:closeModal();" class="btn btn-primary">Close</a>
                            </div>
                            <div class="col-lg-7">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <p style="color:red;">*<small> - Required</small></p>
                                        <form>
                                            <div class="form-group row">
                                                <div class="col-lg-3">
                                                    <?php echo $form->label('ID', t('ID *'))?>
                                                    <?php echo $form->text('ID', @$entry['ID'], array('class' => "form-control",'disabled'=>TRUE))?>
                                                </div>
                                                <div class="col-lg-9">
                                                    <?php echo $form->label('ContentHeading', t('Heading *'))?>
                                                    <?php echo $form->text('ContentHeading', @$entry['ContentHeading'], array('class' => "form-control"))?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <?php echo $form->label('CMS_Name', t('CMS Name'))?>
                                                <?php echo $form->text('CMS_Name', @$entry['CMS_Name'], array('class' => "form-control"))?>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-lg-6">
                                                    <?php echo $form->label('ContentTypeID', t('Type'))?>
                                                    <?php echo $form->select('ContentTypeID', array('1001'=>'HTML', '1002'=>'List', '1004'=>'Subheading'), @$entry['ContentTypeID'], array('class' => "form-control"))?>
                                                </div>
                                                <div class="col-lg-6">
                                                    <?php echo $form->label('Global', t('Global'))?>
                                                    <?php echo $form->select('Global', array('Y'=>'Yes', 'N'=>'No'), @$entry['Global'], array('class' => "form-control"))?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <?php echo $form->label('CMS_Notes', t('Notes'))?>
                                                <?php echo $form->textarea('CMS_Notes', @$entry['CMS_Notes'], array('class' => "form-control"))?>
                                            </div>
                                            <div class="form-group">
                                                <?php echo $form->label('ContentDescription', t('Content Text'))?>
                                                <?php echo $form->textarea('ContentDescription', @$entry['ContentDescription'], array('class' => "ccm-advanced-editor"))?>
                                            </div>
                                            <a href="javascript:applyContentValue();" class="btn btn-success">Save</a>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
        tinyMCE.init({
                "mode":"textareas",
                "inlinepopups_skin":"concreteMCE",
                "theme_concrete_buttons2_add":"spellchecker",
                "browser_spellcheck":true,
                "gecko_spellcheck":true,
                "relative_urls":false,
                "document_base_url":"http:\/\/tng.au\/",
                "convert_urls":false,
                "entity_encoding":"raw",
                "editor_selector":"ccm-advanced-editor",
                "width":"100%",
                "height":380,
                "theme":"concrete",
                "plugins":"paste,inlinepopups,spellchecker,safari,advlink,advimage,advhr",
                "spellchecker_languages":"+English=en",
                "content_css":"\/packages\/education_theme\/themes\/education_theme\/typography.css"
        });
</script>

<script>
	
    
	
    var popup_selection_item_click = function(dom) {

        var dashboard_content_folder_link =  "<?php echo $dashboard_content_folder_link; ?>";

        // remove all selected
        jQuery('.popup-selection-item').each(function(){

                jQuery(this).removeClass('selected');

        });

        // select one
        jQuery(dom).addClass('selected');

        var cid = jQuery(dom).children("input").val();

        var dashboard_select_tabs_link =  "/dashboard/cup_content/titles/getContentInfo/";

        var selected_values = new Array();

        selected_values = { 
                                ID : cid
                        }; 

        var submit_data = {
                                'data': selected_values
                        };

        jQuery.ajax({
                type: 'post',
                url: dashboard_select_tabs_link, 
                dataType: 'json',
                data: submit_data, 
                success: function(data) {
                        $("#ID").val(data.ID);
                        $("#ContentHeading").val("");
                        $("#CMS_Name").val("");
                        $("#CMS_Notes").val("");
                        $("#ContentDescription").val("");

                        $("#ID").val(data.ID);
                        $("#ContentHeading").val(data.ContentHeading);
                        $("#CMS_Name").val(data.CMS_Name);
                        $("#CMS_Notes").val(data.CMS_Notes);
                        if (data.Global == null) {
                                data.Global = 'N';
                        }
                        $("#Global").val(data.Global);
                        $("#ContentTypeID").val(data.ContentTypeID);
                        $("#ContentDescription").val(data.ContentDescription);
                        if (tinyMCE.getInstanceById('ContentDescription')) {
                                tinyMCE.execCommand('mceFocus', false, 'ContentDescription');
                                tinyMCE.execCommand('mceRemoveControl', false, 'ContentDescription');
                        }
                        tinyMCE.execCommand('mceAddControl', false, 'ContentDescription');
                        if (data.ContentDescription == null) {
                                data.ContentDescription = "";
                        }
                        tinyMCE.get('ContentDescription').setContent(data.ContentDescription);
                }
        });

    }
	
    var applyContentValue = function() {

        var dashboard_select_tabs_link =  "/dashboard/cup_content/titles/saveContent/";

        var cDescription = tinyMCE.get('ContentDescription').getContent();

        var selected_values = { 
            ID : $('#ID').val(),
            ContentHeading : $('#ContentHeading').val(),
            CMS_Name : $('#CMS_Name').val(),
            ContentTypeID : $('#ContentTypeID').val(),
            Global : $('#Global').val(),
            CMS_Notes : $('#CMS_Notes').val(),
            ContentDescription : cDescription,
            TitleID : <?php echo $title_id; ?>,
            FolderName : '<?php echo $folderName; ?>'
        }; 

        var submit_data = {'data': selected_values};
        if($('#ID').val() && $('#ContentHeading').val()) {
            jQuery.ajax({
                type: 'post',
                url: dashboard_select_tabs_link, 
                data: submit_data, 
                success: function(html_data){
                        $('#popup-selected-area tbody').html(html_data);
                        $('.popup-selection-item').each(function(){
                            if($(this).hasClass('selected')) {
                                $(this).trigger('click');
                            }
                        });
                        alert('Saved.'); 
                        return false; // to avoid the double display of alert
                },
                error : function(xhr,status,err) {
                    $('#error').html(xhr.responseText);
                }
            });
        } else {
            if(jQuery(".popup-selection-item.selected").length <= 0) {
                alert("Please select a heading.");
            } else {
                alert("Please fill up all Required fields.");
            }
        }
    }

    var closeModal = function() {

            jQuery.colorbox.close();

    }

    var openContentDetail = function() {

        var dashboard_content_folder_link =  "/tools/packages/cup_content/content_folders/dashboard_content_detail";

        var contentID = $('#ID').val();

        var fName = "<?php echo $folderName; ?>";

        var submit_data = new Array();

        var submit_data = {
                        content_ID : contentID,
                        title_ID : <?php echo $title_id; ?>,
                        folderName: fName
        };

        if (contentID !== "") {

            jQuery.colorbox({html: '<div style="width:630px;height:500px" id="popup-window-content"></div>',
                width: "80%",
                height: "830px",
                wopen: function() {
                    $("body").addClass("modal-open");
                },
                onClosed: function() {
                    $("body").removeClass("modal-open");
                }
            });

            jQuery.ajax({
                type: 'post',
                url: dashboard_content_folder_link,
                data: submit_data,
                success: function (html_data) {
                    // console.log(html_data);
                    var p = jQuery('#popup-window-content').parent();
                    p.empty();
                    p.html(html_data);
                }
            });

        } else {
            alert("Please select a heading.");
        }

    }

</script>