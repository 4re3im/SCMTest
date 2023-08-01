<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>
<input type="hidden" name="add_subfolder[FolderID]" value="<?php echo $folder_id ?>" />
<div role="tabpanel" class="tab-pane" id="content">
    <div class="row">
        <div class="col-sm-10 col-md-offset-1">
            <div class="row">
                <div class="col-sm-12">
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title"><strong><?php echo $folder_name; ?></strong> New content</h3>
                            <div class="box-tools pull-right">
                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            </div>
                        </div>

                        <div class="box-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="conheading">Content Heading</label>
                                        <input type="text" placeholder="Enter content heading" id="conheading" name="add_subfolder[ContentHeading]" class="form-control" value="">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Type</label>
                                        <select class="form-control select2" style="width: 100%;" name="add_subfolder[ContentTypeID]">
                                            <option value="1006">HTML</option>
                                            <option value="1002">List</option>
                                            <option value="1004" selected="">Subheading</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-9">
                                    <label>Global</label>
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="add_subfolder[Global]" value="Y" class="onoffswitch-checkbox" id="myonoffswitch">
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
                                        <input type="text" placeholder="Enter CMS name" id="cmsname" name="add_subfolder[CMS_Name]" class="form-control" value="">
                                    </div>
                                </div>
                                <div class="col-sm-9">
                                    <div class="form-group">
                                        <label for="con-notes">Notes</label>
                                        <textarea class="form-control" id="con-notes" name="add_subfolder[CMS_Notes]" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <label>Content Text</label>
                                        <textarea id="ContentData" name="add_subfolder[ContentData]" rows="10" cols="80"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="con-notes">Attach Multiple Files</label>
                                        <div class="form-group">
                                            <label for="exampleInputFile">Choose files</label>
                                            <input type="file" name="add_subfolder[SubcontentFiles][]" id="input-file-a" multiple="multiple">
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