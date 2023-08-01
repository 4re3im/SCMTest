<?php
/**
 * Trial Block Single Pages
 * GCAP-1286 Added by mabrigos 20210428
 */
defined('C5_EXECUTE') or die("Access Denied.");

$th = Loader::helper('concrete/urls');
$pkg = Package::getByHandle('go_dashboard');
$loaderPath = $th->getPackageURL($pkg);
$url = $th->getToolsURL('search_trials', 'go_trials');

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
            <h3>Go Trials Report</h3>
        </div>
        <div class="ccm-pane-body">

            <div class="alert" style="display:none;"></div>

            <div class="row">
                <div class="span11">
                    <form id="trials-form" method="POST"
                          action="<?php echo $this->url('/dashboard/trials/report/processReport'); ?>"
                          enctype="multipart/form-data">
                        <div class="row-fluid">
                            <div class="span5">
                                <label>Search Trial Name</label>
                                <input type="text" class="form-control" id="search-box" id="search-box" search-url="<?php echo $url ?>"
                                    placeholder="Just type in and click selection.">
                            </div>
                            <div class="span3">
                                <label>Country</label>
                                <input type="text" class="form-control" id="country" name="country" style="width:200px" 
                                    placeholder="eg. Australia">
                            </div>
                            <div class="span3">
                                <label>Days Remaining</label>
                                <input type="number" class="form-control" id="daysRemaining" name="daysRemaining" style="width:100px" 
                                    placeholder="eg. 20">
                            </div>
                        </div>
                        <br>
                        <div class="row-fluid">
                            <div class="span3">
                                <label>From</label>
                                <input type="date" class="form-control" id="start" name="start" style="width:200px">
                            </div>
                            <div class="span4">
                                <label>To</label>
                                <input type="date" class="form-control" id="end" name="end">
                            </div>
                        </div>

                        <br>

                        <table class="table table-bordered" style="display:none;" id="trials-table">
                            <thead>
                            <tr>
                                <th>Trial name</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
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
                    <button id="exportBtn" class="btn btn-success pull-right invisible">Export to
                        Spreadsheet
                    </button>
                </div>
            </div>

        </div>

        <div class="ccm-pane-footer">
            <input id= "resetForm" type="reset" value="Reset" class="invisible">
            <input type="submit" class="ccm-button-right btn primary accept" id='generateReport' value="<?php echo t('Generate Report') ?>"/>
        </form>
        </div>
    </div>
</div>