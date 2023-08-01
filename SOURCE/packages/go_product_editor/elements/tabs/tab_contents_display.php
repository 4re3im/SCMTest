<?php
defined('C5_EXECUTE') or die("Access Denied.");

function format_global_content_name($content) {
    return ($content['ContentHeading']) ? $content['ContentHeading'] : "(" . $content['CMS_Name'] . ")";
}

function format_local_content_name($content) {
    return ($content['heading']) ? $content['heading'] : "(" . $content['cms_name'] . ")";
}
$v = View::getInstance();

$added_arr = array();
if(!empty($content_added)) {
    foreach ($content_added as $c) {
        $added_arr[] = $c['ContentID'];
    }   
}
?>
<div class="row">
    <div class="col-lg-3 col-lg-offset-1">
        <div class="row">
            <div class="col-md-12">
                <!-- Custom Tabs -->
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#local" data-toggle="tab">Local Content</a>
                        </li>
                        <li>
                            <a href="#global" data-toggle="tab">Global Content</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="local">
                            <?php foreach ($local_content_folders as $id => $contents) { ?>
                                <div class="panel-group tab-content">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a data-toggle="<?php echo (!$contents[key($contents)][0]) ? '' : 'collapse' ?>" href="#collapse-<?php echo $id; ?>" class="<?php echo (!$contents[key($contents)][0]) ? 'disable-collapse' : '' ?>"><?php echo key($contents); ?></a>
                                            </h4>
                                        </div>
                                        <div id="collapse-<?php echo $id ?>" class="panel-collapse collapse">
                                            <ul class="list-group">
                                                <?php foreach ($contents as $content) { ?>
                                                    <?php foreach ($content as $c) { ?>
                                                        <?php if($c['content_id']) { ?>
                                                            <li class="list-group-item"><?php echo format_local_content_name($c) ?>
                                                                <a class="content-plus <?php echo in_array($c['content_id'], $added_arr) ? "content-plus-hide" : ""?>" id="<?php echo $c['content_id']; ?>" href="<?php echo $v->url('/go_product_editor/add_content/' . $tab_id . '/' . $c['content_id']); ?>"><i class="fa fa-plus-circle pull-right" aria-hidden="true"></i></a>
                                                            </li>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <!-- /.tab-pane -->
                        <div class="tab-pane" id="global">
                            <ul class="list-group">
                                <?php foreach ($global_content_folders as $content) { ?>
                                <li class="list-group-item"><?php echo format_global_content_name($content);  ?>
                                    <a class="content-plus <?php echo in_array($content['ID'], $added_arr) ? "content-plus-hide" : ""?>" id="<?php echo $content['ID'] ?>" href="<?php echo $v->url('/go_product_editor/add_content/' . $tab_id . '/' . $content['ID']); ?>"><i class="fa fa-plus-circle pull-right" aria-hidden="true"></i></a>
                                </li>    
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">

        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Block of Content Added</h3>
                <h6>* Drag-and-Drop to arrange order</h6>
            </div>
            <div class="add-content box-body table-responsive no-padding">
                <table class="table table-hover" id="add-content-table">
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th>Name</th>
                            <th>Column</th>
                            <th>Active</th>
                            <th>Visibility</th>
                            <th>Demo Only</th>
                            <th>Remove?</th>
                        </tr>
                    </thead>
                    <tbody id="add-content-tbody">
                        <?php Loader::packageElement('tabs/content_added','go_product_editor',array('content'=>$content_added,'tab_id'=>$tab_id)); ?>
                    </tbody>
                </table>
            </div>
            <!-- /.box-body -->
        </div>
    </div>
</div>

