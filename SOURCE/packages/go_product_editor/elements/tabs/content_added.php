<?php
defined('C5_EXECUTE') or die("Access Denied.");
$v = View::getInstance();
?>
<?php if(!isset($to_append)) { ?>
<input type="hidden" name="edit_content_added[tab_id]" value="<?php echo $tab_id; ?>" />
<?php } ?>

<?php if(!empty($content)) { ?>
    <?php foreach ($content as $c) {
        $content_name = ($c['ContentHeading']) ? $c['ContentHeading'] : '(' . $c['CMS_Name'] . ')';
        $col1 = ($c['ColumnNumber'] == 1) ? "checked" : "";
        $col2 = ($c['ColumnNumber'] == 2) ? "checked" : "";
        $is_active = ($c['Active'] == 'Y') ? "checked" : "";
        $public_visibility = ($c['Visibility'] == "Public") ? "checked" : "";
        $private_visibility = ($c['Visibility'] == "Private") ? "checked" : "";
        $is_demo = ($c['DemoOnly'] == 'Y') ? "checked" : "";

        $col1_radio_class = ($c['ColumnNumber'] == 1) ? "active" : "";
        $col2_radio_class = ($c['ColumnNumber'] == 2) ? "active" : "";
        $public_radio_class = ($c['Visibility'] == "Public") ? "active" : "";
        $private_radio_class = ($c['Visibility'] == "Private") ? "active" : "";
        ?>
        <tr>
            <td class="acsort">
                <i class="fa fa-sort"></i>
            </td>
            <td class="content-heading"><?php echo $content_name; ?>
                <?php if(isset($content_id)) { ?>
                <input type="hidden" name="edit_content_added[<?php echo $c['ID']; ?>][ContentID]" value="<?php echo $content_id; ?>"/>
                <?php } ?>
                <input type="hidden" class="content-heading-delete" name="edit_content_added[<?php echo $c['ID']; ?>][ToDelete]" value="N" /> 
                <input type="hidden" class="content-heading-sort" name="edit_content_added[<?php echo $c['ID']; ?>][SortOrder]" value="<?php echo $c['SortOrder'] ?>"  />
            </td>
            <td>
                <div class="pill">
                    <div class="btn-group" data-toggle="buttons">
                        <a href="#" class="btn btn-default btn-sm <?php echo $col1_radio_class; ?>" data-toggle="tab">
                            <input type="radio" class="go-product-editor-radio" name="edit_content_added[<?php echo $c['ID'] ?>][ColumnNumber]" value="1" <?php echo $col1; ?> /> 1</a>
                        <a href="#" class="btn btn-default btn-sm <?php echo $col2_radio_class; ?>" data-toggle="tab">
                            <input type="radio" class="go-product-editor-radio" name="edit_content_added[<?php echo $c['ID'] ?>][ColumnNumber]" value="2" <?php echo $col2; ?> /> 2</a>
                    </div>
                </div>
            </td>
            <td>
                <div class="onoffswitch">
                    <input type="checkbox" class="onoffswitch-checkbox" name="edit_content_added[<?php echo $c['ID'] ?>][Active]" value="Y" id="content-active-<?php echo $c['ID'] ?>" <?php echo $is_active; ?>>
                    <label class="onoffswitch-label" for="content-active-<?php echo $c['ID'] ?>">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                    </label>
                </div>
            </td>
            <td>
                <div class="pill">
                    <div class="btn-group" data-toggle="buttons">
                        <a href="#" class="btn btn-default btn-xs <?php echo $public_radio_class; ?>" data-toggle="tab">
                            <input type="radio" class="go-product-editor-radio" name="edit_content_added[<?php echo $c['ID'] ?>][Visibility]" value="Public" /> Public</a>
                        <a href="#" class="btn btn-default btn-xs <?php echo $private_radio_class; ?>" data-toggle="tab">
                            <input type="radio" class="go-product-editor-radio" name="edit_content_added[<?php echo $c['ID'] ?>][Visibility]" value="Private" /> Private</a>
                    </div>
                </div>
            </td>

            <td>
                <div class="onoffswitch">
                    <input type="checkbox" class="onoffswitch-checkbox" name="edit_content_added[<?php echo $c['ID'] ?>][DemoOnly]" value="Y" id="content-demo-<?php echo $c['ID'] ?>" <?php echo $is_demo; ?>>
                    <label class="onoffswitch-label" for="content-demo-<?php echo $c['ID'] ?>">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                    </label>
                </div>
            </td>
            <td>
                <a class="content-trash" href="#" content-id="<?php echo $c['ContentID'] ?>"><i class="fa fa-half-2x fa-minus-circle" aria-hidden="true"></i></a>
            </td>
        </tr>
    <?php } ?>
<?php } ?>
