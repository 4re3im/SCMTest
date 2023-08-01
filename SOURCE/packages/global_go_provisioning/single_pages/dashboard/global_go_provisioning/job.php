<?php
/**
 * SB-674 Added by mtanada 20200902
 */
defined('C5_EXECUTE') or die("Access Denied.");

$th = Loader::helper('concrete/urls');
$pkg = Package::getByHandle('go_dashboard');
$url = $th->getToolsURL('autocomplete', 'global_go_provisioning');
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
            <h3>Provisioning Job Queue</h3>
        </div>
        <div class="ccm-pane-body">
            <div class="alert" style="display:none;"></div>
            <div class="row">
                <div class="span11">
                    <!-- START -->
                    <div>
                        <button class="addJob btn btn-primary">
                            Add Job Queue
                        </button>
                    </div>
                    <!-- END -->
                    <br />

                    <form class="hideForm" id="subscriptions-form" method="POST"
                          action="<?php echo $this->url('/dashboard/global_go_provisioning/job/saveProvisionJob'); ?>"
                          enctype="multipart/form-data">
                        <!-- Divider -->
                        <hr/>
                        <div class="row-fluid">
                            <div class="span5">
                                <label>Search subscriptions</label>
                                <input type="text" class="form-control" id="search-box" search-url="<?php echo $url ?>"
                                       placeholder="Just type in and click selection.">
                            </div>
                            <div class="span4">
                                <label>Activation required date</label>
                                <input type="date" name="endDate" value="" class="form-control" id="endDate">
                            </div>
                            <div class="span3">
                                <label>&nbsp;</label>
                                <input type="file" name="excel" value="" class="form-control" id="provisioning-file">
                            </div>
                        </div>

                        <!-- Subscriptions table -->
                        <br>
                        <?php // SB-577 added by mabrigos 20200603 ?>
                        <div class="row-fluid">
                        <div class="span5">
                            <label title="Paste multiple IDs">Paste multiple entitlement IDs</label>
                            <input type="text" class="form-control" id="multipleIds" name="subscriptionIds"
                                placeholder="paste multiple entitlement IDs here" 
                                style="margin-bottom: 10px; width: 347px">
                        </div>
                        </div>
                        <!--GCAP-541 CAMPION added by mtanada 20191015-->
                        <div>
                            <label title="Choose a Third Party Provider (i.e. Campion, Box of Books etc.)">
                                <input type="checkbox" name="provider" style="display:inline-block;">
                                Provisioning users from a Third Party Provider?
                            </label>
                            <select name="providerId" class="form-control" id="providerId" 
                            style="margin-bottom: 10px; display: none">
                                <option></option>
                                <option value="saml-Campion-Education">Campion</option>
                            </select>
                        </div>
                        <table class="table table-bordered" style="display:none;" id="subscriptions-table">
                            <thead>
                            <tr>
                                <th>Subscription name</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <input type="hidden" id="file-record-id" value="" name="fileRecordId">

                        <!-- Divider -->
                        <hr/>
                    </form>
                </div>
            </div>

            <!-- JOB -->
            <div class="row" id="job-box">
                <div class="span11">
                    <div class="row-fluid">
                        <div class="span5">
                            <button class="refreshJob btn btn-primary" style="text-align: right">
                                Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END -->

            <!-- Messages -->
            <div class="row" id="alert-box" style="display:none;">
                <div class="span11">
                    <div class="alert alert-info">
                    </div>
                </div>
            </div>
            <br/>

            <!-- Pagination -->
            <div class="row">
                <div class="span11" id="job-pagination"></div>
            </div>
            <br/>

            <!-- Provisioning Jobs -->
            <div class="row">
                <div class="span11">
                    <table class="table table-bordered" style="display:none" id="jobs-table">
                        <thead>
                        <tr>
                            <th>Title</th>
                            <th>Queue Number</th>
                            <th>Entitlement IDs</th>
                            <th>Errors</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody id="jobs-table-body">
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ANZGO-3528 Added by John Renzo S. Sunico, October 13, 2017
                 Add export result button -->
            <div class="row">
                <div class="span11">
                    <form action="/dashboard/global_go_provisioning/setup/exportToSheet/" method="POST">
                        <input type="hidden" id="downloadFileId" name="fileId" val=""/>
                        <button id="exportBtn" class="btn btn-success pull-right invisible" type="submit">Export to
                            Spreadsheet
                        </button>
                    </form>
                </div>
            </div>

        </div>
        <div class="ccm-pane-footer"></div>
    </div>
</div>

<script>
    // GCAP-541 Campion added by machua/mtanada 20191004
    $('input[name="provider"]').change(function() {
        if (this.checked === true) {
            $('#providerId').show()
        } else {
            $('#providerId').hide()
        }
    });
</script>