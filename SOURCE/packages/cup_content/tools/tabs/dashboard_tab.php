<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

$u = new User();
$v = View::getInstance();

Loader::model('format/model', 'cup_content');
Loader::model('title/model', 'cup_content');

$title_id = $_POST['title_ID'];
$tabName = $_POST['tabName'];

$titles = new CupContentTitle();

$entry = $titles->getTabByIdName($title_id, $tabName);

if (isset($_REQUEST['list-only']) && $_REQUEST['list-only'] == 'yes'):
    ?>
    <?php foreach ($results as $each): ?>
        <div class="popup-selection-item" onclick="popup_selection_item_click(this)"><?php echo $each->name; ?></div>
    <?php endforeach; ?>
    <?php
    exit();
endif;
?>

<?php
$form = Loader::helper('form');
$wform = Loader::helper('wform', 'cup_content');
$uh = Loader::helper('concrete/urls');
$dashboard_select_format_link = $uh->getToolsURL('format/dashboard_selection', 'cup_content');
?>
<br />
<div class="container">
    <div class="row">
        <div class="col-lg-10">
            <div>
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#general" aria-controls="general" role="tab" data-toggle="tab">General</a></li>
                    <li role="presentation"><a href="#tab-content" aria-controls="tab-content" role="tab" data-toggle="tab" id="tab-content-display">Tab Content</a></li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="general">
                        <?php echo $form->hidden('id', $title_id); ?>
                        <?php
                        if ($entry) {
                            echo $form->hidden('newtab', 'N');
                        } else {
                            echo $form->hidden('newtab', 'Y');
                            $entry['TabName'] = $tabName;
                        }
                        ?>
                        <br />
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-9">
                                    <div class="row">
                                        <!-- INFORMATION -->
                                        <div class="col-lg-12">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title">Information</h3>
                                                </div>
                                                <div class="panel-body">
                                                    <form>
                                                        <div class="form-group">
                                                            <?php echo $form->label('TabName', t('Tab Name *')) ?>
                                                            <?php echo $form->text('TabName', @$entry['TabName'], array('class' => "form-control",'disabled'=>'')) ?>
                                                            
                                                        </div>
                                                        <div class="form-group">
                                                            <?php echo $form->label('Columns', t('Columns')) ?>
                                                            <?php echo $form->select('Columns', array('1' => '1', '2' => '2', '3' => '3'), @$entry['Columns'], array('class' => "form-control")) ?>
                                                        </div>
                                                        <!--<div class="form-group">
                                                            <?php echo $form->label('TabColour', t('Tab Colour')) ?>
                                                            <?php echo $form->select('TabColour', array('blue' => 'blue', 'blue-gold' => 'blue-gold', 'green' => 'green', 'green-gold' => 'green-gold', 'orange' => 'orange', 'red' => 'red'), @$entry['TabColour'], array('class' => "form-control")) ?>
                                                        </div>
                                                        <div class="form-group">
                                                            <?php echo $form->label('WatermarkImage', t('Watermark')) ?>
                                                            <?php echo $form->select('WatermarkImage', array('1' => '1', '2' => '2', '3' => '3'), @$entry['WatermarkImage'], array('class' => "form-control")) ?>
                                                        </div>-->
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="row">
                                        <!-- OPTIONS -->
                                        <div class="col-lg-12">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title">Options</h3>
                                                </div>
                                                <div class="panel-body">
                                                    <form>
                                                        <!--<div class="form-group">
                                                            <?php echo $form->label('DefaultTab', t('Default Tab')) ?>
                                                            <?php echo $form->select('DefaultTab', array('Y' => 'Yes', 'N' => 'No'), @$entry['DefaultTab'], array('class' => "form-control")) ?>
                                                            
                                                        </div>-->
                                                        <!--<div class="form-group">
                                                            <?php echo $form->label('AllowSearch', t('Allow Search Of Tab Text')) ?>
                                                            <?php echo $form->select('AllowSearch', array('Y' => 'Yes', 'N' => 'No'), @$entry['AllowSearch'], array('class' => "form-control")) ?>
                                                        </div>-->
                                                        <div class="form-group">
                                                            <?php echo $form->label('MyResourcesLink', t('My Resources Link')) ?>
                                                            <?php echo $form->select('MyResourcesLink', array('Y' => 'Yes', 'N' => 'No'), @$entry['MyResourcesLink'], array('class' => "form-control")) ?>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <!-- ACCESS RIGHTS -->
                                        <div class="col-lg-12">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title">Access Rights</h3>
                                                </div>
                                                <div class="panel-body">
                                                    <form>
                                                        <div class="form-group">
                                                            <?php echo $form->label('Active', t('Active')) ?>
                                                            <?php echo $form->select('Active', array('Y' => 'Yes', 'N' => 'No'), @$entry['Active'], array('class' => "form-control")) ?>
                                                        </div>
                                                        <div class="form-group">
                                                            <?php echo $form->label('Visibility', t('Visibility')) ?>
                                                            <?php echo $form->select('Visibility', array('Public' => 'Public', 'Private' => 'Private'), @$entry['Visibility'], array('class' => "form-control")) ?>
                                                        </div>
                                                        <div class="form-group">
                                                            <?php echo $form->label('ContentVisibility', t('Content Access')) ?>
                                                            <?php echo $form->select('ContentVisibility', array('open' => 'Open', 'closed' => 'Closed'), @$entry['ContentVisibility'], array('class' => "form-control")) ?>
                                                        </div>
                                                        <div class="form-group">
                                                            <?php echo $form->label('UserTypeIDRestriction', t('Content Restriction')) ?>
                                                            <?php echo $wform->userTypeRestrictionGroups(@$entry['UserTypeIDRestriction'],'',TRUE); ?>
                                                        </div>
                                                        <div class="form-group">
                                                            <?php echo $form->label('ContentType', t('Content Type')) ?>
                                                            <?php echo $wform->contentType(@$entry['ContentType'],'',TRUE); ?>
                                                        </div>
                                                        <div class="form-group">
                                                            <?php echo $form->label('CustomAccessMessage', t('Custom Message')) ?>
                                                            <?php echo $form->textarea('CustomAccessMessage', @$entry['CustomAccessMessage'], array('class' => "form-control")) ?>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title">Text</h3>
                                                </div>
                                                <div class="panel-body">
                                                    <form>
                                                        <div class="form-group">
                                                            <?php echo $form->label('AlwaysUsePublicText', t('Always Use Public Text')) ?>
                                                            <?php echo $form->select('AlwaysUsePublicText', array('Y' => 'Yes', 'N' => 'No'), @$entry['AlwaysUsePublicText'], array('class' => "form-control")) ?>
                                                        </div>
                                                        <div class="form-group">
                                                            <?php echo $form->label('Public_TabText', t('Public Text')) ?>
                                                            <?php echo $form->textarea('Public_TabText', @$entry['Public_TabText'], array('class' => "ccm-advanced-editor form-control")) ?>
                                                        </div>
                                                        <div class="form-group">
                                                            <?php echo $form->label('Private_TabText', t('Private Text')) ?>
                                                            <?php echo $form->textarea('Private_TabText', @$entry['Private_TabText'], array('class' => "ccm-advanced-editor form-control")) ?>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href="javascript:applyTabValues();" class="btn btn-primary">Confirm</a>
                            <br />
                            <br />
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab-content">
                        <br />
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#local-content" aria-controls="global-content" role="tab" data-toggle="tab" content-link="<?php echo $v->url('/dashboard/cup_content/titles','getLocalContentTabs'); ?>" class="title-tab-content" id="local-content-display">Local Content</a></li>
                            <li role="presentation"><a href="#global-content" aria-controls="profile" role="tab" data-toggle="tab" content-link="<?php echo $v->url('/dashboard/cup_content/titles','getGlobalContentTabs'); ?>" class="title-tab-content">Global Content</a></li>
                        </ul>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="local-content"> <!-- LOCAL CONTENT -->
                                <div class="container">
                                    <div class="row">
                                        <div class="col-lg-9">
                                            <br />
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title">Select Files</h3>
                                                </div>
                                                <div class="panel-body" id="localContent-files-body"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <br />
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title">Content Available <span class="pull-right"><small class="contents-status"></small></h3>
                                                </div>
                                                <div class="panel-body" id="localContent-filesContent-body">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <br />
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title">Content Added <span class="pull-right"><small class="addedContents-status"></small></h3>
                                                </div>
                                                <div class="panel-body" id="localContent-contentAdded-body">
                                                    <table class="table table-condensed" id="localContent-contentAdded-table">
                                                        <thead>
                                                            <tr>
                                                                <th class="col-lg-4">Name</th>
                                                                <th class="col-lg-1">Col</th>
                                                                <th class="col-lg-2">Active</th>
                                                                <th class="col-lg-2">Visibility</th>
                                                                <th class="col-lg-2">Demo Only</th>
                                                                <th class="col-lg-1"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="global-content"> <!-- GLOBAL CONTENT -->
                                <div class="container">
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <br />
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title">Content Available <span class="pull-right"><small class="contents-status"></small></span></h3>
                                                </div>
                                                <div class="panel-body" id="global-content-body">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <br />
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title">Content Added <span class="pull-right"><small class="addedContents-status"></small></span></h3>
                                                </div>
                                                <div class="panel-body">
                                                    <table class="table table-condensed" id="content-added-table">
                                                        <thead>
                                                            <tr>
                                                                <th class="col-lg-4">Name</th>
                                                                <th class="col-lg-1">Col</th>
                                                                <th class="col-lg-2">Active</th>
                                                                <th class="col-lg-2">Visibility</th>
                                                                <th class="col-lg-2">Demo Only</th>
                                                                <th class="col-lg-1"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="content-added-tbody">
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

    var applyTabValues = function () {

        // ANZGO-1738
        var dashboard_select_tabs_link = "/dashboard/cup_content/titles/saveTab/";

        var selected_values = new Array();

        selected_values = {
            titleID: $('#id').val(),
            TabName: $('#TabName').val(),
            Columns: $('#Columns').val(),
            TabColour: $('#TabColour').val(),
            WatermarkImage: $('#WatermarkImage').val(),
            DefaultTab: $('#DefaultTab').val(),
            AllowSearch: $('#AllowSearch').val(),
            MyResourcesLink: $('#MyResourcesLink').val(),
            Active: $('#Active').val(),
            Visibility: $('#Visibility').val(),
            ContentVisibility: $('#ContentVisibility').val(),
            UserTypeIDRestriction: $('#UserTypeIDRestriction').val(),
            ContentType: $('#ContentType').val(),
            CustomAccessMessage: $('#CustomAccessMessage').val(),
            AlwaysUsePublicText: $('#AlwaysUsePublicText').val(),
            Public_TabText: tinyMCE.get('Public_TabText').getContent(),
            Private_TabText: tinyMCE.get('Private_TabText').getContent(),
            newtab: $('#newtab').val()
        };

        var submit_data = {
            'data': selected_values
        };

        jQuery.ajax({
            type: 'post',
            url: dashboard_select_tabs_link,
            data: submit_data,
            success: function (html_data) {
                // var p = jQuery('#popup-window-content').parent();
                // p.empty();
                // p.html(html_data);
            }
        });

        jQuery.colorbox.close();
    }

</script>