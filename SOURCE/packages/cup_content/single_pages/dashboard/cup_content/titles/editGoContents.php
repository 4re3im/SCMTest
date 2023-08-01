<?php
/*
 * Renders the Edit Go product page.
 * All HTML contents related to the Contents and Tabs related to a title is found here.
 */
defined('C5_EXECUTE') or die(_("Access Denied."));

$v = View::getInstance();
$form = Loader::helper('form');
$wform = Loader::helper('wform', 'cup_content');

echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Edit Go Contents'), false, false, false);
?>
<div class="ccm-pane-body">
    <h3><?php echo $titleName; ?></h3>
    <br />
    <div>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#tabs" aria-controls="tabs" role="tab" data-toggle="tab">Tabs</a></li>
            <li role="presentation"><a href="#content" aria-controls="content" role="tab" data-toggle="tab">Content</a></li>
        </ul>   

        <!-- Tab panes -->
        <div class="tab-content">
            
            <!-- TABS -->
            <div role="tabpanel" class="tab-pane active" id="tabs">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="panel panel-default" id="">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Tabs <small id="tabs-notif" class="pull-right" style="display: none;">Sorting...</small></h3>
                                </div>
                                <div class="panel-body">
                                    <div class="alert" role="alert" style="display:none;">
                                        <span>...</span>
                                    </div>
                                    <form class="form-inline add-heading-form" id="tabs-form" action="<?php echo $v->url('dashboard/cup_content/titles/addNewTab');?>" >
                                        <div class="form-group">
                                            <label class="sr-only" for="Heading">Heading</label>
                                            <input type="text" class="form-control" name="TabName" id="TabName" placeholder="Tab name" />
                                            <input type="hidden" name="ID" value="<?php echo $titleID; ?>" />
                                            <input type="submit" class="btn btn-default" value="Add" />
                                        </div>
                                    </form>
                                    <small><i>* Drag and drop to sort.</i></small>
                                    <br />
                                    <br />
                                    <table class="table table-bordered table-hover go-contents-table" style="cursor:pointer;" id="tabs-list" url="<?php echo $v->url("dashboard/cup_content/titles/updateSorting/tabs"); ?>">
                                        <tbody class="sortable">
                                            <?php echo $tabs; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-9 col-md-9 col-xs-9 col-sm-9">
                            <div>
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active"><a href="#general-tab" aria-controls="general-tab" role="tab" data-toggle="tab">General</a></li>
                                    <li role="presentation"><a href="#tab-contents" aria-controls="tab-contents" role="tab" data-toggle="tab" id="show-tab-contents">Tab Contents</a></li>
                                </ul>

                                <!-- Tab panes -->
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="general-tab">
                                        <div class="panel panel-default" id="title-tab-content">
                                            <div class="panel-heading">
                                                <h3 class="panel-title">Tab Content</h3>
                                            </div>
                                            <div class="panel-body">
                                                <form id="title-tab-details" class="detail-save-form" action="<?php echo $v->url('/dashboard/cup_content/titles/saveTab'); ?>" method="POST">
                                                    <div class="container-fluid">
                                                        <div class="row" >
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" id="tab-info-panel">
                                                            </div>
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" id="tab-options-panel">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="tab-access-panel"></div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="tab-text-panel"></div>
                                                        </div>
                                                    </div>
                                                    <input type="submit" class="btn btn-primary" value="Save"  style="display:none;" />
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="tab-contents">
                                        <div id="tab-content-container" style="display:block;">
                                            <ul class="nav nav-tabs" role="tablist">
                                                <li role="presentation" class="active"><a href="#local-content" aria-controls="global-content" role="tab" data-toggle="tab" content-link="<?php echo $v->url('/dashboard/cup_content/titles', 'getLocalContentTabs'); ?>" class="title-tab-contents" id="local-content-display">Local Content</a></li>
                                                <li role="presentation"><a href="#global-content" aria-controls="profile" role="tab" data-toggle="tab" content-link="<?php echo $v->url('/dashboard/cup_content/titles', 'getGlobalContentTabs'); ?>" class="title-tab-contents">Global Content</a></li>
                                            </ul>
                                            <div class="tab-content">
                                                <div role="tabpanel" class="tab-pane active" id="local-content"> <!-- LOCAL CONTENT -->
                                                    <div class="container-fluid">
                                                        <div class="row">
                                                            <div class="col-lg-12"> 
                                                                <div class="panel panel-default">
                                                                    <div class="panel-heading">
                                                                        <h3 class="panel-title">Select Files</h3>
                                                                    </div>
                                                                    <div class="panel-body" id="localContent-files-body"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                                <div class="panel panel-default">
                                                                    <div class="panel-heading">
                                                                        <h3 class="panel-title">Content Available</h3>
                                                                    </div>
                                                                    <div class="panel-body" id="localContent-filesContent-body" style="max-height: 400px; overflow-y: auto;">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                                                <div class="panel panel-default">
                                                                    <div class="panel-heading">
                                                                        <h3 class="panel-title">Content Added<span class="pull-right"><small class="addedContents-status"></small></h3>
                                                                    </div>
                                                                    <div class="panel-body" id="localContent-contentAdded-body">
                                                                        <small><i>* Drag and drop to sort</i></small>
                                                                        <table class="table table-hover go-contents-table" id="localContent-contentAdded-table" url="<?php echo $v->url("/dashboard/cup_content/titles/updateSorting/tab_content"); ?>">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th class="col-lg-3 col-md-3 col-sm-3 col-xs-3">Name</th>
                                                                                    <th class="col-lg-2 col-md-2 col-sm-2 col-xs-2">Col</th>
                                                                                    <th class="col-lg-2 col-md-2 col-sm-2 col-xs-2">Active</th>
                                                                                    <th class="col-lg-2 col-md-2 col-sm-2 col-xs-2">Visibility</th>
                                                                                    <th class="col-lg-2 col-md-2 col-sm-2 col-xs-2">Demo Only</th>
                                                                                    <th class="col-lg-1 col-md-1 col-sm-1 col-xs-1"></th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody class="sortable">
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div role="tabpanel" class="tab-pane" id="global-content"> <!-- GLOBAL CONTENT -->
                                                    <div class="container-fluid">
                                                        <div class="row">
                                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                                <div class="panel panel-default">
                                                                    <div class="panel-heading">
                                                                        <h3 class="panel-title">Content Available <span class="pull-right"><small class="contents-status"></small></span></h3>
                                                                    </div>
                                                                    <div class="panel-body" id="global-content-body" style="max-height: 400px; overflow-y: auto;">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">  
                                                                <div class="panel panel-default">
                                                                    <div class="panel-heading">
                                                                        <h3 class="panel-title">Content Added <span class="pull-right"><small class="addedContents-status"></small></span></h3>
                                                                    </div>
                                                                    <div class="panel-body">
                                                                        <small><i>* Drag and drop to sort</i></small>
                                                                        <table class="table table-hover go-contents-table" style="cursor:pointer" id="content-added-table" url="<?php echo $v->url("/dashboard/cup_content/titles/updateSorting/tab_content"); ?>">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th class="col-lg-3 col-md-3 col-sm-3 col-xs-3">Name</th>
                                                                                    <th class="col-lg-2 col-md-2 col-sm-2 col-xs-2">Col</th>
                                                                                    <th class="col-lg-2 col-md-2 col-sm-2 col-xs-2">Active</th>
                                                                                    <th class="col-lg-2 col-md-2 col-sm-2 col-xs-2">Visibility</th>
                                                                                    <th class="col-lg-2 col-md-2 col-sm-2 col-xs-2">Demo Only</th>
                                                                                    <th class="col-lg-1 col-md-1 col-sm-1 col-xs-1"></th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody class="sortable" id="content-added-tbody">
                                                                            </tbody>
                                                                        </table>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CONTENT -->
            <div role="tabpanel" class="tab-pane" id="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Folders <small class="pull-right" id="folder-heading-notif" style="display:none;">Updating...</small></h3>
                                </div>
                                <div class="panel-body" style="max-height: 700px;overflow-y: auto;">
                                    <div class="alert" role="alert" style="display:none;">
                                    <span>...</span>
                                </div>
                                <form class="form-inline add-heading-form" id="parentFolders-form" action="<?php echo $v->url("/dashboard/cup_content/titles/saveContentFolder/"); ?>">
                                    <div class="form-group">
                                        <label class="sr-only" for="Heading">Folder</label>
                                        <input type="text" class="form-control" name="FolderName" placeholder="Folder heading" />
                                        <input type="hidden" name="titleID" value="<?php echo $titleID; ?>" id="titleID" />
                                        <input type="submit" class="btn btn-default" value="Add" />
                                    </div>
                                </form>
                                    <input type="hidden" id="get-tabfolder-url" value="<?php echo $v->url("/dashboard/cup_content/titles/getTabFolders"); ?>" />
                                <small><i>* Double click to edit.</i></small>
                                <br />
                                <br />
                                <div>
                                    <input type="hidden" value="<?php echo $v->url('/dashboard/cup_content/titles/editHeading/folder');?>" />
                                    <table class="table table-bordered table-hover go-contents-table" id="parentFolders-list">
                                        <tbody style="cursor:pointer;">
                                            <?php echo $parentFolders; ?>
                                        </tbody>
                                    </table>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9" id="contents-container" style="display:none;">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Contents <small id="subfolder-heading-notif" style="display:none;">Updating...</small></h3>
                                </div>
                                <div class="panel-body" style="max-height:700px;overflow-y: auto;">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                <div class="alert" role="alert" style="display:none;">
                                                    <span>...</span>
                                                </div>
                                                <form class="form-inline add-heading-form" action="<?php echo $v->url('/dashboard/cup_content/titles/saveFolder/'); ?>" id="subfolders-form">
                                                    <div class="form-group">
                                                        <label class="sr-only" for="Heading">Heading</label>
                                                        <input type="text" class="form-control" name="ContentHeading" id="Heading" placeholder="Content heading" />
                                                        <input type="hidden" name="titleID" value="<?php echo $title_id; ?>" />
                                                        <input type="hidden" name="folderName" value="<?php echo $folderName; ?>" />
                                                        <input type="submit" class="btn btn-default" value="Add" />
                                                    </div>
                                                </form>
                                                
                                                <div style="max-height: 400px;overflow-y: auto;">
                                                    <table class="table table-bordered table-hover go-contents-table" id="subfolders-list" style="cursor:pointer;">
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" id="content-form-container"  style="display:none;">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <form class="detail-save-form" id="subfolder-details" action="<?php echo $v->url('/dashboard/cup_content/titles/saveContent'); ?>">
                                                            <div class="alert alert-dismissible" id="subfolder-alert" role="alert" style="display:none;">
                                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                <div class="alert-text"></div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                                                    <?php echo $form->label('ID', t('ID *')) ?>
                                                                    <?php echo $form->text('contentID', '', array('class' => "form-control", 'disabled' => TRUE)) ?>
                                                                    <input type="hidden" name="contentID-hidden" id="contentID-hidden" value="" />
                                                                    <input type="hidden" name="FolderID" id="FolderID" value="" />
                                                                    <input type="hidden" name="titleID" value="<?php echo $titleID; ?>" id="titleID" />
                                                                </div>
                                                                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                                                                    <?php echo $form->label('ContentHeading', t('Heading *')) ?>
                                                                    <?php echo $form->text('ContentHeading', '', array('class' => "form-control")) ?>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <?php echo $form->label('CMS_Name', t('CMS Name')) ?>
                                                                <?php echo $form->text('CMS_Name', '', array('class' => "form-control")) ?>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                                    <?php echo $form->label('ContentTypeID', t('Type')) ?>
                                                                    <?php echo $form->select('ContentTypeID', array('1006' => 'HTML', '1002' => 'List', '1004' => 'Subheading'), '', array('class' => "form-control")) ?>
                                                                </div>
                                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                                    <?php echo $form->label('Global', t('Global')) ?>
                                                                    <?php echo $form->select('Global', array('Y' => 'Yes', 'N' => 'No'), '', array('class' => "form-control")) ?>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <?php echo $form->label('CMS_Notes', t('Notes')) ?>
                                                                <?php echo $form->textarea('CMS_Notes', '', array('class' => "form-control")) ?>
                                                            </div>
                                                            <div class="form-group">
                                                                <?php echo $form->label('ContentData', t('Content Text')) ?>
                                                                <?php echo $form->textarea('ContentData', '', array('class' => "ccm-advanced-editor")) ?>
                                                            </div>
                                                            <input type="submit" class="btn btn-primary" value="Save" />
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
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="panel panel-default" id="content-details-container" style="display:none;">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Content Details <small style="display:none;" id="content-details-notif">Sorting...</small></h3>
                                </div>
                                <div class="panel-body">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                                <div class="alert" role="alert" style="display:none;">
                                                    <span>...</span>
                                                </div>
                                                <form class="form-inline add-heading-form" id="content-details-form" action="<?php echo $v->url("/dashboard/cup_content/titles/saveFolderDetail/") ?>" >
                                                    <div class="form-group">
                                                        <label class="sr-only" for="Heading">Heading</label>
                                                        <input type="text" class="form-control" name="ContentHeading" id="ContentHeading" placeholder="Content detail heading" />
                                                        <input type="submit" class="btn btn-default" value="Add" />
                                                    </div>
                                                </form>
                                                <small><i>* Drag and drop to sort.</i></small>
                                                <br />
                                                <br />
                                                <div style="max-height: 400px;overflow-y: auto;">
                                                    <table class="table table-bordered table-hover go-contents-table" id="content-details-list" style="cursor:pointer;" url="<?php echo $v->url("/dashboard/cup_content/titles/updateSorting/content_details"); ?>">
                                                        <tbody class="sortable">
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <br />
                                                <div class="panel panel-default">
                                                    <div class="panel-heading">
                                                        <h3 class="panel-title">Upload Multiple Files</h3>
                                                    </div>
                                                    <div class="panel-body">
                                                        <form class="form-inline fileuploadmulti" id="multi-file-upload" action="<?php echo $v->url('/dashboard/cup_content/titles/handleFileUploads'); ?>" method="post" enctype="multipart/form-data">
                                                            <input type="hidden" name="ID" value="<?php echo $titleID; ?>" />
                                                            <input type="hidden" name="ContentID" value="" class="content-id-upload" />
                                                            <div class="form-group">
                                                                <input id="filemulti" type="file" name="files[]" multiple class="form-control">
                                                                <input type="submit" class="btn btn-primary medium" style="margin-top:5px;" value="Upload" id="multi-upload-btn" />
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9" id="content-detail-container" style="display:none;">
                                                <div class="alert alert-dismissible" id="content-detail-alert" role="alert" style="display:none;">
                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <div class="alert-text"></div>
                                                </div>
                                                <form id="content-detail-form" action="<?php echo $v->url("dashboard/cup_content/titles/saveDetail"); ?>" class="detail-save-form">
                                                    <div class="panel panel-default">
                                                        <div class="panel-body">
                                                            <div class="form-group row">
                                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                                    <?php
                                                                    echo $form->label('dispID', t('ID'));
                                                                    echo $form->text('dispID', @$entry['ID'], array('class' => "form-control", "disabled" => "disabled"));
                                                                    ?>
                                                                    <input type="hidden" name="ID" value="" id="content-detail-id" /> <!-- ID of CupgocontentDetail -->
                                                                    <input type="hidden" name="ContentID" value="" class="content-id-upload" />
                                                                </div>
                                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                                    <?php echo $form->label('TypeID', t('Type')) ?>
                                                                    <?php
                                                                    $cdTypeForm = $v->url('/dashboard/cup_content/titles/getContentTypeForm');
                                                                    echo $form->select('TypeID', array('0' => '---', '1001' => 'Link', '1006' => 'HTML', '1005' => 'File'), @$entry['TypeID'], array('class' => "form-control", 'url' => $cdTypeForm));
                                                                    ?>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                                    <?php echo $form->label('Visibility', t('Visibility')) ?>
                                                                    <?php echo $form->select('Visibility', array('Public' => 'Public', 'Private' => 'Private'), @$entry['Visibility'], array('class' => "form-control")) ?>
                                                                </div>
                                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                                    <?php echo $form->label('Active', t('Active')) ?>
                                                                    <?php echo $form->select('Active', array('Y' => 'Yes', 'N' => 'No'), @$entry['Active'], array('class' => "form-control")) ?>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <?php echo $form->label('CMS_Notes', t('Notes')) ?>
                                                                <?php echo $form->textarea('CMS_Notes', @$entry['CMS_Notes'], array('class' => "form-control")) ?>
                                                            </div>
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
            </div>

        </div>
    </div>
    <div id="error"></div>
</div>
<script>
    tinyMCE.init({
        "mode": "textareas",
        "inlinepopups_skin": "concreteMCE",
        "theme_concrete_buttons2_add": "spellchecker",
        "browser_spellcheck": true,
        "gecko_spellcheck": true,
        "relative_urls": false,
        // "document_base_url": "http:\/\/tng.au\/",
        "convert_urls": false,
        "entity_encoding": "raw",
        "editor_selector": "ccm-advanced-editor",
        "width": "100%",
        "height": 250,
        "theme": "concrete",
        "plugins": "paste,inlinepopups,spellchecker,safari,advlink,advimage,advhr",
        "spellchecker_languages": "+English=en",
        "valid_elements": "*[*]"
        // "content_css": "\/packages\/education_theme\/themes\/education_theme\/typography.css"
    });
</script>

