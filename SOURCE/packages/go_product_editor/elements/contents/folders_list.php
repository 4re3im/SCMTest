<?php
defined("C5_EXECUTE") or die(_("Acess Denied"));

function format_name($content) {
    if($content['heading'] || $content['cms_name']) {
        return ($content['heading']) ? $content['heading'] : "(" . $content['cms_name'] . ")";
    } else {
        return 'No contents yet...';
    }
}
$v = View::getInstance();
?>

<li class="header">CONTENT</li>
<div><a href="<?php echo $v->url('/go_product_editor/get_add_folder_form/' . $title_id); ?>" type="button" class="btn btn-primary btn-xs" id="add-folder">Add Folder</a></div>
<?php foreach ($folders as $id => $contents) { ?>
    <?php if(isset($new_folder_id)) { ?>
        <li class="stack treeview <?php echo ($id == $new_folder_id) ? "active" : "" ?>">
    <?php } else { ?>
        <li class="stack treeview ">
    <?php } ?>
        <a class="name edit-folder" href="<?php echo $v->url('/go_product_editor/get_folder/' . $id) ?>">
            <i class="fa fa-folder-o"></i>
            <span><?php echo key($contents) ?></span>
        </a>
            <a href="<?php echo $v->url('/go_product_editor/get_add_subfolder_form/' . $id . '/' . key($contents)) ?>" class="add fade add-subfolder" data-toggle="tooltip" data-placement="left" title="Add block of content">+</a>
            <?php if($contents[key($contents)][0]) { ?>

            <a class="expand" href="#EXPAND"></a>

            <ul class="stack treeview-menu content-block <?php echo ($id == $new_folder_id) ? "menu-open" : "" ?>">

            <?php foreach ($contents as $content_key => $content_value) { ?>
                <?php foreach ($content_value as $content) { ?>
                <li class="stack treeview <?php echo ($content['content_id'] == $subfolder_id) ? "active" : "" ?>">
                    <a href="<?php echo $v->url('/go_product_editor/get_subfolder_details/' . $content['content_id'] . '/' . $id); ?>" class="subfolder-name">
                        <i class="fa fa-square-o"></i>
                        <span class="subfolder-heading"><?php echo format_name($content); ?></span>
                    </a>
                    <a href="<?php echo $v->url('/go_product_editor/get_add_content_detail_form/' . $content['content_id'] . '/' . $id); ?>" class="add fade add-content-detail" data-toggle="tooltip" data-placement="left" title="Add content detail">+</a>
                    <a class="expand" href="#"></a>
                    <?php if(!empty($content['contents'])) { ?>
                    <ul class="content-details-sortable stack treeview-menu content-detail <?php echo ($content['content_id'] == $subfolder_id) ? "menu-open" : "" ?>">
                        <?php $sortCounter = 1; ?>
                        <!-- Modified by Shane Camus 2017-03-21
                             ANZGO-3167 Sorting Issue on Content-Details-->
                        <input class="urlcontent-details" type="hidden" value="<?php echo $v->url('/go_product_editor/updateSorting/content_details/'); ?>">
                        <?php foreach ($content['contents'] as $c) { ?>
                        <li title="<?php echo $c['Public_Name']; ?>">
                            <a class="move content-detail-sort-handle" href="#"><i class="fa fa-sort fade"></i></a>
                            <a class="subfolder-content-name treedetail" href="<?php echo $v->url('/go_product_editor/get_subfolder_content_details/' . $c['ID'] . '/' . $content['content_id'] . '/' . $id); ?>">
                                <i class="fa fa-circle-thin subfolder-content-name"></i><?php echo $c['Public_Name']; ?>
                            </a>
                            <input class="content-detail-sort" type="hidden" name="sorting[<?php echo $c['ID']; ?>]" value="<?php echo $sortCounter; ?>">
                        </li>
                        <?php $sortCounter++; ?>
                        <?php } ?>
                    </ul>
                    <?php } ?>
                </li>
                <?php } ?>
            <?php } ?>
        </ul>
        <?php } ?>
    </li>
<?php } ?>
