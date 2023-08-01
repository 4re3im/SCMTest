<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>
<input type="hidden" value="<?php echo $subfolder_id; ?>" name="add_content_detail[ContentID]" />
<input type="hidden" value="<?php echo $folder_id; ?>" name="add_content_detail[folder_id]" />
<div class="row">
    <div class="col-sm-10 col-md-offset-1">
        <div class="row">
            <div class="col-sm-12">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">New Content detail</h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="conheading">Visibility</label>
                                    <div class="container">
                                        <div class="row">
                                            <div class="pill">

                                                <div id="tab" class="btn-group" data-toggle="buttons">
                                                    <a href="#prices" class="btn btn-default active" data-toggle="tab">
                                                        <input type="radio" name="add_content_detail[Visibility]" value="Public" checked="" /> Public</a>
                                                    <a href="#features" class="btn btn-default" data-toggle="tab">
                                                        <input type="radio" name="add_content_detail[Visibility]" value="Private" /> Private</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label>Active</label>
                                <div class="onoffswitch">
                                    <input type="checkbox" name="add_content_detail[Active]" value="Y" class="onoffswitch-checkbox" id="myonoffswitch" checked="">
                                    <label class="onoffswitch-label" for="myonoffswitch">
                                        <span class="onoffswitch-inner"></span>
                                        <span class="onoffswitch-switch"></span>
                                    </label>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea class="form-control" name="add_content_detail[CMS_Notes]" id="con-notes" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Type</label>
                                    <select id="filetype-subcontent" name="add_content_detail[TypeID]" class="form-control select2" style="width: 100%;">
                                        <option class="type-of-file" value="1005" selected="">File</option>
                                        <option class="type-of-file" value="1001">Link</option>
                                        <option class="type-of-file" value="1006">HTML</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- FILE -->
        <div class="row file fade type-panel">
            <div class="col-sm-12">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">File Information</h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="publicno">Public Name</label>
                                    <input type="text" name="add_content_detail[Public_Name]" placeholder="Enter public name" id="publicname" class="form-control edit-public-name" value="">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="finfo">File Info</label>
                                    <textarea class="form-control" name="add_content_detail[FileInfo]" id="finfo" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="publicdesc">Public Description</label>
                                    <textarea class="form-control edit-public-description" name="add_content_detail[Public_Description]" id="publicdesc" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
        <!-- /FILE -->
        <!-- LINK -->
        <div class="row link fade type-panel">
            <div class="col-sm-12">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">Link Content</h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label for="publicname2">Public Name</label>
                                    <input type="text" name="add_content_detail[Public_Name]" placeholder="Enter public name"  id="publicname" class="form-control edit-public-name" value="">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="conheading">Window Behaviour</label>

                                    <div class="container">
                                        <div class="row">
                                            <div class="pill">

                                                <div id="tab" class="btn-group" data-toggle="buttons">
                                                    <a href="#prices" class="btn btn-default" data-toggle="tab">
                                                        <input type="radio" name="add_content_detail[WindowBehaviour]" checked="" value="New"/> New</a>
                                                    <a href="#features" class="btn btn-default" data-toggle="tab">
                                                        <input type="radio" name="add_content_detail[WindowBehaviour]" value="Current"/> Current</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="url">URL</label>
                                    <input type="text" name="add_content_detail[URL]" placeholder="Enter URL" id="url" class="form-control" value="">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="publicdesc2">Public Description</label>
                                    <textarea class="form-control edit-public-description" name="add_content_detail[Public_Description]" id="publicdesc2" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
        <!-- /LINK -->
        <!-- HTML -->
        <div class="row html fade type-panel">
            <div class="col-sm-12">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">HTML Information</h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>

                    <div class="box-body">

                        <div class="row">
                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label for="publicname3">Public Name</label>
                                    <input type="text" placeholder="Enter public name" name="add_content_detail[Public_Name]" id="" class="form-control edit-public-name" value="">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Window Size</label>
                                    <select class="form-control select2" name="add_content_detail[WindowSize]" style="width: 100%;">
                                        <option selected="selected" value="320x240">Small (320 x 240)</option>
                                        <option value="640x240">Medium (640 x 480)</option>
                                        <option value="1280x960">Large (1280 x 960)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="publicdesc">Public Description</label>
                                    <textarea class="form-control edit-public-description" name="add_content_detail[Public_Description]" id="publicdesc" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="htmlcon">HTML Content</label>

                                    <textarea id="HTML_Content" name="add_content_detail[HTML_Content]" rows="10" cols="80"></textarea>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
        <!-- //HTML -->

        <!-- FILE DETAILS -->
        <div class="row file fade type-panel">
            <div class="col-sm-12">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">File Upload</h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                <!-- Modified by JSulit SB-976 -->
                                        <ul id="filelist"></ul>
                                        <div id="container">
                                            <a id="upload-file">Choose File</a><br><br>
                                        </div>
                                    <br />
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <div class="form-group">

                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <th>File Name</th>
                                                <td>
                                                    <span id="FileNameLabel"></span>
                                                    <input type="hidden" id="FileName" name="add_content_detail[FileName]" value="">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>File Path</th>
                                                <td>
                                                    <span id="FilePathLabel"></span>
                                                    <input type="hidden" id="FilePath" name="add_content_detail[FilePath]" value="">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>File Size</th>
                                                <td>
                                                    <span id="FileSizeLabel"></span>
                                                    <input type="hidden" id="FileSize" name="add_content_detail[FileSize]" value="">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>File Upload Date</th>
                                                <td>
                                                    <span id="FileUploadDateLabel"></span>
                                                    <input type="hidden" id="FileUploadDate" name="add_content_detail[FileUploadDate]" value="">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
        <!-- //FILE DETAILS -->

    </div>
</div>


