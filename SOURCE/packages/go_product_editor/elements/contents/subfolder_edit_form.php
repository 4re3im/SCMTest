<?php
defined('C5_EXECUTE') or die("Access Denied.");
$v = View::getInstance();
$heading = ($details['ContentHeading']) ? $details['ContentHeading'] : "(" . $details['CMS_Name'] . ")";
$is_global = ($details['Global'] == 'Y') ? "checked" : "";

$html_select = ($details['ContentTypeID'] == 1006) ? "selected" : "";
$list_select = ($details['ContentTypeID'] == 1002) ? "selected" : "";
$subheading_select = ($details['ContentTypeID'] == 1004) ? "selected" : "";
?>
<input type="hidden" id="general-delete-url" value="<?php echo $v->url('/go_product_editor/delete_subfolder_action/' . $details['ID'] . '/' . $folder_id); ?>" />
<input type="hidden" value="<?php echo $folder_id ?>" name="edit_subfolder[FolderID]" />
<div role="tabpanel" class="tab-pane" id="content">
    <div class="row">
        <div class="col-sm-10 col-md-offset-1">
            <div class="row">
                <div class="col-sm-12">
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php echo $heading; ?></h3>
                            <div class="box-tools pull-right">
                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>

                        <div class="box-body">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="idnumber">ID Number</label>
                                        <input type="text" placeholder="Enter ID Number" id="idnumber" class="form-control" value="<?php echo $details['ID']; ?>" disabled>
                                        <input type="hidden" name="edit_subfolder[ID]" value="<?php echo $details['ID']; ?>" />
                                    </div>
                                </div>
                                <div class="col-sm-9">
                                    <div class="form-group">
                                        <label for="conheading">Content Heading</label>
                                        <input type="text" placeholder="Enter content heading" id="conheading" name="edit_subfolder[ContentHeading]" class="form-control" value="<?php echo $details['ContentHeading']; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Type</label>
                                        <select class="form-control select2" style="width: 100%;" name="edit_subfolder[ContentTypeID]">
                                            <option value="1006" <?php echo $html_select; ?>>HTML</option>
                                            <option value="1002" <?php echo $list_select; ?>>List</option>
                                            <option value="1004" <?php echo $subheading_select; ?>>Subheading</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-9">
                                    <label>Global</label>
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="edit_subfolder[Global]" value="Y" class="onoffswitch-checkbox" id="myonoffswitch" <?php echo $is_global; ?>>
                                        <label class="onoffswitch-label" for="myonoffswitch">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>CMS Name</label>
                                        <input type="text" placeholder="Enter CMS name" id="cmsname" name="edit_subfolder[CMS_Name]" class="form-control" value="<?php echo $details['CMS_Name']; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-9">
                                    <div class="form-group">
                                        <label for="con-notes">Notes</label>
                                        <textarea class="form-control" id="con-notes" name="edit_subfolder[CMS_Notes]" rows="3"><?php echo $details['CMS_Notes'] ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <label>Content Text</label>

                                        <textarea id="ContentData" name="edit_subfolder[ContentData]" rows="10" cols="80">
                                            <?php echo $details['ContentData'] ?>
                                        </textarea>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="con-notes">Attach Multiple Files</label>
                                        <div class="form-group">
                                            <label for="exampleInputFile">Choose files</label>
                                            <input type="file" name="edit_subfolder[SubcontentFiles][]" id="input-file-e" multiple="multiple">
                                            <br />
                                        </div>
                                        <div class="attach-file box-body table-responsive no-padding" style="display:none;">
                                            <table class = "table">
                                                <tr>
                                                    <th class = "namecol">File Name</th>
                                                    <th>Delete</th>
                                                </tr>
                                            </table>
                                            <div class = "tablebody">
                                                <table class = "table table-hover scroll" id="file-display">
                                                    <tbody>
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
