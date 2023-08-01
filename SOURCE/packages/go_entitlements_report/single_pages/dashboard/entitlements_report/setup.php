<?php
/**
 * Entitlement Reports Block Single Pages
 * SB-611 Added by mtanada 20200715
 */
defined('C5_EXECUTE') or die("Access Denied.");

$th = Loader::helper('concrete/urls');
$pkg = Package::getByHandle('go_dashboard');
$loaderPath = $th->getPackageURL($pkg);
?>
<style>
    .ui-autocomplete {
        font-size: 12px;
        max-height: 300px;
        max-width: 1000px;
        overflow-y: auto;
        overflow-x: auto;

    }

    .ui-autocomplete-loading {
        background: url('<?php echo $loaderPath?>/images/ajax-loader.gif') no-repeat right center
    }

    .ccm-ui {
        position: absolute;
    }
</style>
<div class="ccm-ui">
    <div class="ccm-pane">
        <div class="ccm-pane-header">
            <h3>Go Entitlements Report</h3>
        </div>
        <div class="ccm-pane-body">
            <div class="alert" style="display:none;"></div>
            <div class="row">
                <div class="span11">
                    <form id="subscriptions-form" method="POST"
                          action="<?php echo $this->url('/dashboard/entitlements_report/setup/startUpload'); ?>"
                          enctype="multipart/form-data">
                        <div class="row-fluid">
                            <div>
                                <label style="float:left;margin-right:5px;">
                                    <h4>Upload a file (.xlsx) with a list of emails.   </h4>
                                </label>
                                <input type="file" name="excel" value="" class="form-control" id="emails-file">
                            </div>
                        </div>
                        <input type="hidden" id="file-record-id" value="" name="fileRecordId">
                    </form>
                </div>
            </div>

            <!-- Divider -->
            <hr/>

            <!-- Messages -->
            <div class="row" id="alert-box" style="display:none;">
                <div class="span11">
                    <div class="alert alert-info">
                    </div>
                </div>
            </div>
            <br/>

            <!-- Progressbar -->
            <div id="generating-progress" class="progress-bar invisible">
                <div class="progress progress-striped active">
                    <div class="bar" style="width:0%">0%</div>
                </div>
            </div>

            <!-- Add export result button -->
            <div class="row">
                <div class="span11">
                    <input type="hidden" id="downloadFileId" name="fileId" val=""/>
                    <button id="exportBtn" class="btn btn-success pull-right invisible" type="submit">Export to
                        Spreadsheet
                    </button>
                </div>
            </div>

        </div>
        <div class="ccm-pane-footer"></div>
    </div>
</div>