<?php

/**
 * Subfolder Content EDIT Form Element
 */

defined('C5_EXECUTE') || die("Access Denied.");

const PUBLIC_NAME = 'Public_Name';
const PUBLIC_DESCRIPTION = 'Public_Description';
const FILE_UPLOAD_DATE = 'FileUploadDate';
const FILE_SIZE = 'FileSize';
const WINDOW_BEHAVIOUR = 'WindowBehaviour';
const TYPE_ID = 'TypeID';
const SELECTED = 'selected';
const ACTIVE = 'active';
const VISIBILITY = 'Visibility';
const CHECKED = 'checked';

$v = View::getInstance();
$publicVisibility = ($details[VISIBILITY] == 'Public') ? CHECKED : '';
$privateVisibility = ($details[VISIBILITY] == 'Private') ? CHECKED : '';
$publicRadioClass = ($details[VISIBILITY] == 'Public') ? ACTIVE : '';
$privateRadioClass = ($details[VISIBILITY] == 'Private') ? ACTIVE : '';
$isActive = ($details['Active'] == 'Y') ? CHECKED : '';

$fileSelect = ($details[TYPE_ID] == '1005') ? SELECTED : '';
$linkSelect = ($details[TYPE_ID] == '1001') ? SELECTED : '';
$HTMLSelect = ($details[TYPE_ID] == '1006') ? SELECTED : '';

$fileFormShow = ($details[TYPE_ID] == '1005') ? 'in' : '';
$linkFormShow = ($details[TYPE_ID] == '1001') ? 'in' : '';
$HTMLFormShow = ($details[TYPE_ID] == '1006') ? 'in' : '';

// LINK type settings
$showInNew = ($details[WINDOW_BEHAVIOUR] == 'New') ? CHECKED : '';
$showInCurrent = ($details[WINDOW_BEHAVIOUR] != 'New') ? CHECKED : '';
$showInNewRadioClass = ($details[WINDOW_BEHAVIOUR] == 'New') ? ACTIVE : '';
$showInCurrentRadioClass = ($details[WINDOW_BEHAVIOUR] != 'New') ? ACTIVE : '';

// FILE details
$sizeTokb = round(($details[FILE_SIZE] / 1024), 2);
$sizeTomb = round(($details[FILE_SIZE] / 1048576), 2);
$details[FILE_UPLOAD_DATE] = strtotime($details[FILE_UPLOAD_DATE]);
$dateUploaded = date('d F, Y', $details[FILE_UPLOAD_DATE]);
$windowSize = $details['WindowWidth'] . 'x' . $details['WindowHeight'];

$url = '/go_product_editor/delete_content_detail_action/' . $details['ID'] . '/' . $subfolder_id . '/' . $folder_id;
?>

<input type="hidden" value="<?php echo $v->url($url); ?>" id="general-delete-url" />
<input type="hidden" value="<?php echo $subfolder_id; ?>" name="edit_content_detail[subfolder_id]" />
<input type="hidden" value="<?php echo $folder_id; ?>" name="edit_content_detail[folder_id]" />
<div class="row">
    <div class="col-sm-10 col-md-offset-1">
        <div class="row">
            <div class="col-sm-12">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $details[PUBLIC_NAME] ?></h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">

                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="idnumber">ID Number</label>
                                    <input type="text"
                                           placeholder="Enter ID Number"
                                           id="idnumber"
                                           class="form-control"
                                           value="<?php echo $details['ID'] ?>"
                                           disabled=''/>
                                    <input type="hidden"
                                           name="edit_content_detail[ID]"
                                           value="<?php echo $details['ID'] ?>"/>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="conheading">Visibility</label>

                                    <div class="container">
                                        <div class="row">
                                            <div class="pill">

                                                <div id="tab" class="btn-group" data-toggle="buttons">
                                                    <a href="#prices"
                                                       class="btn btn-default <?php echo $publicRadioClass ?>"
                                                       data-toggle="tab">
                                                        <input type="radio"
                                                               name="edit_content_detail[Visibility]"
                                                                <?php echo $publicVisibility ?>
                                                               value="Public" /> Public</a>
                                                    <a href="#features"
                                                       class="btn btn-default
                                                       <?php echo $privateRadioClass ?>"
                                                       data-toggle="tab">
                                                        <input type="radio"
                                                               name="edit_content_detail[Visibility]"
                                                                <?php echo $privateVisibility ?>
                                                               value="Private" /> Private</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label>Active</label>
                                <div class="onoffswitch">
                                    <input type="checkbox"
                                           name="edit_content_detail[Active]"
                                           value="Y" class="onoffswitch-checkbox"
                                           id="myonoffswitch"
                                            <?php echo $isActive; ?>>
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
                                    <textarea class="form-control"
                                              name="edit_content_detail[CMS_Notes]"
                                              id="con-notes" rows="3">
<?php echo $details['CMS_Notes']; ?>
</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Type</label>
                                    <select id="filetype-subcontent"
                                            name="edit_content_detail[TypeID]"
                                            class="form-control select2"
                                            style="width: 100%;"
                                            url="<?php echo $v->url('/go_product_editor/get_content_type_form'); ?>">
                                        <option class="type-of-file" value="1005" <?php echo $fileSelect; ?>>
                                            File
                                        </option>
                                        <option class="type-of-file" value="1001" <?php echo $linkSelect; ?>>
                                            Link
                                        </option>
                                        <option class="type-of-file" value="1006" <?php echo $HTMLSelect; ?>>
                                            HTML
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- FILE -->
        <div class="row file fade <?php echo $fileFormShow; ?> type-panel">
            <div class="col-sm-12">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">File Information</h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="publicno">Public Name</label>
                                    <input type="text"
                                           placeholder="Enter name"
                                           name="edit_content_detail[PUBLIC_NAME]"
                                           class="form-control edit-public-name"
                                           id="publicname"
                                           value="<?php echo $details[PUBLIC_NAME]; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="finfo">File Info</label>
                                    <textarea class="form-control"
                                              name="edit_content_detail[FileInfo]"
                                              id="finfo" rows="3">
<?php echo $details['FileInfo']; ?>
</textarea>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="publicdesc">Public Description</label>
                                    <textarea class="form-control edit-public-description"
                                              name="edit_content_detail[PUBLIC_DESCRIPTION]"
                                              id="publicdesc" rows="3">
<?php echo $details[PUBLIC_DESCRIPTION]; ?>
</textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
        <!-- /FILE -->
        <!-- LINK -->
        <div class="row link fade <?php echo $linkFormShow; ?> type-panel">
            <div class="col-sm-12">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">Link Content</h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label for="publicname2">Public Name</label>
                                    <input type="text"
                                           placeholder="Enter public name"
                                           name="edit_content_detail[PUBLIC_NAME]"
                                           class="form-control edit-public-name"
                                           value="<?php echo $details[PUBLIC_NAME]; ?>">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="conheading">Window Behaviour</label>

                                    <div class="container">
                                        <div class="row">
                                            <div class="pill">

                                                <div id="tab" class="btn-group" data-toggle="buttons">
                                                    <a href="#prices"
                                                       class="btn btn-default <?php echo $showInNewRadioClass; ?>"
                                                       data-toggle="tab">
                                                        <input type="radio"
                                                               name="edit_content_detail[WindowBehaviour]"
                                                               value="New" <?php echo $showInNew; ?>/> New
                                                    </a>
                                                    <a href="#features"
                                                       class="btn btn-default <?php echo $showInCurrentRadioClass; ?>"
                                                       data-toggle="tab">
                                                        <input type="radio"
                                                               name="edit_content_detail[WindowBehaviour]"
                                                               value="Current" <?php echo $showInCurrent; ?>/> Current
                                                    </a>
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
                                    <input type="text"
                                           placeholder="Enter URL"
                                           id="url" class="form-control"
                                           name="edit_content_detail[URL]"
                                           value="<?php echo $details['URL']; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="publicdesc2">Public Description</label>
                                    <textarea class="form-control edit-public-description"
                                              id="publicdesc2" name="edit_content_detail[PUBLIC_DESCRIPTION]"
                                              rows="3">
<?php echo $details[PUBLIC_DESCRIPTION]; ?>
</textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
        <!-- /LINK -->
        <!-- HTML -->
        <div class="row html fade <?php echo $HTMLFormShow; ?> type-panel">
            <div class="col-sm-12">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">HTML Information</h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">

                        <div class="row">
                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label for="publicname3">Public Name</label>
                                    <input type="text"
                                           placeholder="Enter public name"
                                           name="edit_content_detail[PUBLIC_NAME]"
                                           id="publicname3" class="form-control edit-public-name"
                                           value="<?php echo $details[PUBLIC_NAME]; ?>">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Window Size</label>
                                    <select class="form-control select2"
                                            name="edit_content_detail[WindowSize]"
                                            style="width: 100%;">
                                        <option value="320x240"
                                            <?php echo ($windowSize == "320x240") ? SELECTED : ''; ?>>
                                            Small (320 x 240)</option>
                                        <option value="640x480"
                                            <?php echo ($windowSize == "640x480") ? SELECTED : ''; ?>
                                        >Medium (640 x 480)</option>
                                        <option value="1280x960"
                                            <?php echo ($windowSize == "1280x960") ? SELECTED : ''; ?>
                                        >Large (1280 x 960)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="publicdesc">Public Description</label>
                                    <textarea class="form-control edit-public-description"
                                              name="edit_content_detail[PUBLIC_DESCRIPTION]"
                                              id="publicdesc" rows="3">
<?php echo $details[PUBLIC_DESCRIPTION];?>
</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="HTML_Content">HTML Content</label>
                                    <textarea id="HTML_Content"
                                              name="edit_content_detail[HTML_Content]"
                                              rows="10" cols="80">
<?php echo $details['HTML_Content']; ?>
</textarea>
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
                            <button class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <div class="form-group">
                                     <!-- Modified by JSulit SB-976 -->
                                        <label for="exampleInputFile">Attach file</label>
                                               <ul id="filelist"></ul>
                                        <div id="container">
                                            <a id="upload-file"> Choose File</a><br><br>
                                        </div>
                                        <br />
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <div class="form-group">

                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <th>File Name</th>
                                                <td>
                                                    <span id="FileNameLabel"><?php echo $details['FileName'] ?></span>
                                                    <input id="FileName"
                                                           type="hidden"
                                                           name="edit_content_detail[FileName]"
                                                           value="<?php echo $details['FileName'] ?>" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>File Path</th>
                                                <td>
                                                    <span id="FilePathLabel"><?php echo $details['FilePath'] ?></span>
                                                    <input id="FilePath"
                                                           type="hidden"
                                                           name="edit_content_detail[FilePath]"
                                                           value="<?php echo $details['FilePath'] ?>" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>File Size</th>
                                                <td>
                                                    <span id="FileSizeLabel">
                                                        <?php
                                                            echo ($sizeTomb < 1) ? $sizeTokb . ' KB' : $sizeTomb . ' MB'
                                                        ?>
                                                    </span>
                                                    <input id="FileSize"
                                                           type="hidden"
                                                           name="edit_content_detail[FileSize]"
                                                           value="<?php echo $details[FILE_SIZE] ?>" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>File Upload Date</th>
                                                <td>
                                                    <span id="FileUploadDateLabel"><?php echo $dateUploaded; ?></span>
                                                    <input id="FileUploadDate"
                                                           type="hidden"
                                                           name="edit_content_detail[FileUploadDate]"
                                                           value="<?php echo $details[FILE_UPLOAD_DATE] ?>" />
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
