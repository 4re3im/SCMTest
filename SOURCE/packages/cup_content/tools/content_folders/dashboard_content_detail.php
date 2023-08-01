<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

$u = new User();
$v = View::getInstance();

$form = Loader::helper('form');

$title_id = $_POST['title_ID'];
$folderName = $_POST['folderName'];
$content_id = $_POST['content_ID'];

Loader::model('title/model', 'cup_content');

$contentDetails = CupContentTitle::getContentDetails($content_id);
$content = CupContentTitle::getContentByID($content_id);

$wform = Loader::helper('wform', 'cup_content');
$uh = Loader::helper('concrete/urls');
$dashboard_select_format_link = $uh->getToolsURL('format/dashboard_selection', 'cup_content');
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <br />
            <h4><?php echo $content['ContentHeading']; ?></h4>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Content Detail <small class="pull-right" id="action-status" style="display:none;">Please wait...</small></h3>
                </div>
                <div class="panel-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-4">
                                <form class="form-inline" action="<?php echo $v->url("/dashboard/cup_content/titles/saveFolderDetail/") ?>" id="new-content-detail">
                                    <div class="form-group">
                                        <label class="sr-only" for="Detail">Detail</label>
                                        <?php
                                        echo $form->hidden('newC-ContentID',$content_id);
                                        echo $form->hidden('newC-TypeID',1001);
                                        ?>
                                        <input type="text" class="form-control" id="newC-PublicName" name="newC-PublicName" placeholder="Detail" />
                                    </div>
                                    <input type="submit" class="btn btn-default" value="Add" />
                                </form>
                                <br />
                                <div id="sortable-container">
                                    <ul id="sortable" style="cursor: pointer;">
                                        <?php if ($contentDetails) { ?>
                                            <?php foreach ($contentDetails as $detail) { ?>
                                                <li class="content-detail-heading" url="<?php echo $v->url('/dashboard/cup_content/titles/getContentDetailInfo/'); ?>">
                                                    <?php echo $detail['Public_Name']; ?>
                                                    <?php echo $form->hidden(html_entity_decode(str_replace(" ", "_", $detail['Public_Name'])), @$detail['ID']); ?>
                                                </li>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <li id="no-content-flag">Nothing found...</li>
                                        <?php } ?>
                                    </ul>
                                </div>
                                
                                <div class="panel panel-default" style="margin: 20px 0;">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Upload Multiple Files</h3>
                                    </div>
                                    <div class="panel-body">
                                        <form class="form-inline fileuploadmulti" id="multi-file-upload" action="<?php echo $v->url('/dashboard/cup_content/titles/handleFileUploads'); ?>" method="post" enctype="multipart/form-data">
                                            <?php echo $form->hidden('hTitleID', @$title_id); ?>
                                            <?php echo $form->hidden('ContentID', @$content_id); ?>
                                            <div class="form-group">
                                                <input id="filemulti" type="file" name="files[]" multiple class="form-control">
                                                <input type="submit" class="btn btn-primary medium" style="margin-top:5px;" value="Upload" id="multi-upload-btn" />
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <form id="content-detail-form" action="<?php echo $v->url("dashboard/cup_content/titles/saveDetail"); ?>">
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                                <div class="form-group row">
                                                    <div class="col-lg-6">
                                                        <?php 
                                                        echo $form->label('dispID', t('ID'));
                                                        echo $form->text('dispID', @$entry['ID'], array('class' => "form-control", "disabled" => "disabled"));
                                                        echo $form->hidden('ID', @$entry['ID']);
                                                        echo $form->hidden('ContentID', $content_id);
                                                        ?>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <?php echo $form->label('TypeID', t('Type')) ?>
                                                        <?php
                                                        $cdTypeForm = $v->url('/dashboard/cup_content/titles/getContentTypeForm');
                                                        echo $form->select('TypeID', array('0' => '---', '1001' => 'Link', '1006' => 'HTML', '1005' => 'File'), @$entry['TypeID'], array('class' => "form-control",'url'=>$cdTypeForm));
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-lg-6">
                                                        <?php echo $form->label('Visibility', t('Visibility')) ?>
                                                        <?php echo $form->select('Visibility', array('Public' => 'Public', 'Private' => 'Private'), @$entry['Visibility'], array('class' => "form-control")) ?>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <?php echo $form->label('Active', t('Active')) ?>
                                                        <?php echo $form->select('Active', array('Y' => 'Yes', 'N' => 'No'), @$entry['Active'], array('class' => "form-control")) ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <?php echo $form->label('CMS_Notes', t('Notes')) ?>
                                                    <?php echo $form->textarea('CMS_Notes', @$entry['CMS_Notes'], array('class' => "form-control")) ?>
                                                </div>
                                            <!-- </form> The form should end here but to accommodate our dynamic content below, which is also part of the form, we should place the closing form tag in the helper. -->
                                        </div>
                                    </div>
                                    <!-- Type Content -->
                                    <div class="panel panel-default" id="content-type-info" style="display:none;">
                                        <input type="hidden" value="" />
                                        <div class="panel-heading">
                                            <h3 class="panel-title"></h3>
                                        </div>
                                        <div class="panel-body">
                                            <p>Loading...</p>
                                        </div>
                                    </div>
                                    <input type="submit" id="save-content-detail-trigger" class="btn btn-primary" value="Save"/>
                                </form>
                                <br />
                                <div class="panel panel-default" id="file-upload-panel" style="display:none;">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">File Upload</h3>
                                    </div>
                                    <div class="panel-body">
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
<div id="error">
    
</div>

<script>
    tinyMCE.init({
        "mode": "textareas",
        "inlinepopups_skin": "concreteMCE",
        "theme_concrete_buttons2_add": "spellchecker",
        "browser_spellcheck": true,
        "gecko_spellcheck": true,
        "relative_urls": false,
        "document_base_url": "http:\/\/tng.au\/",
        "convert_urls": false,
        "entity_encoding": "raw",
        "editor_selector": "ccm-advanced-editor",
        "width": "100%",
        "height": 380,
        "theme": "concrete",
        "plugins": "paste,inlinepopups,spellchecker,safari,advlink,advimage,advhr",
        "spellchecker_languages": "+English=en",
        "content_css": "\/packages\/education_theme\/themes\/education_theme\/typography.css"
    });
</script>

<script>

    

    

    var closeModal = function () {

        var dashboard_content_folder_link = "/index.php/tools/packages/cup_content/content_folders/dashboard_content_folder";

        jQuery.colorbox({html: '<div style="width:630px;height:500px" id="popup-window-content"></div>',
            width: "960px",
            height: "830px"
        });

        var fName = "<?php echo $folderName; ?>";

        var submit_data = {
            'selected_values': "",
            'title_ID': <?php echo $title_id; ?>,
            'folderName': fName
        };

        jQuery.ajax({
            type: 'post',
            url: dashboard_content_folder_link,
            data: submit_data, //jQuery(this).serialize(),
            success: function (html_data) {
                // console.log(html_data);
                var p = jQuery('#popup-window-content').parent();
                p.empty();
                p.html(html_data);
            }
        });

    }
        
    $(function() {
        $( "#sortable" ).sortable({
            revert: true,
            stop: function(event, ui) {

                updateSorting();

            }
        });
        $( "#draggable" ).draggable({
            connectToSortable: "#sortable",
            helper: "clone",
            revert: "invalid"
        });
        $( "ul, li" ).disableSelection();
    });

    var updateSorting = function () {

        var selected_values = new Array();

        jQuery('#sortable .popup-selection-item input').each(function () {

            selected_values.push(this.value);

        });

        var dashboard_upload_link = "/index.php/dashboard/cup_content/titles/saveContentDetailSortable/";

        var submit_data = {
            'data': selected_values
        };

        jQuery.ajax({
            type: 'post',
            url: dashboard_upload_link,
            data: submit_data,
            success: function (html_data) {
                // 
            }
        });

    }

    var updateFileContentDetail = function (file, cdid, setValue) {

        var dashboard_upload_link = "/index.php/dashboard/cup_content/titles/saveUploadDetail/";

        selected_values = {
            ID: cdid,
            FileName: file['name'],
            FilePath: file['url'],
            FileSize: file['size']
        };

        var submit_data = {
            'data': selected_values
        };

        jQuery.ajax({
            type: 'post',
            dataType: 'json',
            url: dashboard_upload_link,
            data: submit_data,
            success: function (html_data) {
                if (setValue == true) {
                    $('#FileUploadDate').val(html_data);
                }
            },
            complete : function() {
                //! fileuploaddate;
            }
        });

    }
    
</script>
