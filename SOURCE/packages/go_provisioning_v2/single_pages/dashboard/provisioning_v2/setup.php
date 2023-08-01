<?php
defined('C5_EXECUTE') or die("Access Denied.");

$th = Loader::helper('concrete/urls');
$pkg = Package::getByHandle('go_provisioning_v2');
$url = $th->getToolsURL('autocomplete', 'go_provisioning_v2');
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
            <h3>Go Provisioning</h3>
        </div>
        <div class="ccm-pane-body">
            <div class="alert" style="display:none;"></div>
            <div class="row">
                <div class="span11">
                    <!-- // ANZGO-3642 Modified by John Renzo Sunico, 02/22/2018 -->
                    <!-- // SB-2 Modified by Michael Abrigos, 01/16/2019 -->
                    <form id="subscriptions-form" method="POST"
                          action="<?php echo $this->url('/dashboard/provisioning_v2/setup/startProvisioning'); ?>"
                          enctype="multipart/form-data">
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
                        <!--GCAP-541 CAMPION added by mtanada 20191015-->
                        <div>
                            <label title="Choose a Third Party Provider (i.e. Campion, Box of Books etc.)">
                                <input type="checkbox" name="provider" style="display:inline-block;">
                                Provisioning users from a Third Party Provider?
                            </label>
                            <select name="providerId" class="form-control" id="providerId" style="display: none">
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
            <div id="provisioning-progress" class="progress-bar invisible">
                <div class="progress progress-striped active">
                    <div class="bar" style="width:0%">0%</div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="row">
                <div class="span11" id="pagination"></div>
            </div>
            <br/>

            <!-- // ANZGO-3642 Added by John Renzo Sunico, 02/22/2018 -->
            <div class="row">
                <div class="span11">
                    <a id="legend-toggle" href="#" class="btn btn-default btn-sm invisible"
                       style="float: right; margin-bottom: 5px;"
                       onclick="$(this).siblings('#legend').slideToggle();">Show/Hide Legend</a>
                    <div id="legend" class="legend-box invisible">
                        <h4>Legend</h4>
                        <ul class="legend">
                            <li><span class="Processing"></span> On-going</li>
                            <li><span class="Provisioned"></span> Provisioned successfully.</li>
                            <li><span class="Existing"></span> Provisioned existing user.</li>
                            <li><span class="InvalidUser"></span> User details are not valid.</li>
                            <li>
                                <span class="TNGProvisionError"></span>
                                Unable to add Go subscription. You may retry.
                            </li>
                            <li>
                                <span class="HMProvisioned"></span>
                                Valid Hotmaths product and/or class. 
                            <li>
                                <span class="HMProvisionCompatibility"></span>
                                Incompatible Hotmaths product.
                            </li>
                            <li>
                                <span class="HMProductProblem"></span>
                                Invalid Hotmaths product. Check subscription setup.
                            </li>
                            <li><span class="AddClassError"></span> Unable to add user to Hotmaths class.</li>
                            <li>
                                <span class="HMProvisionError"></span>
                                Unable to add Hotmaths product. You may retry.
                            </li>
                        </ul>
                        <br />
                        <h5 style="text-align:center"> (*) This user is provisioned in Go but will be provisioned in HotMaths either via day end job or when the user visits the My Resources page.</h5>
                    </div>
                </div>
            </div>

            <!-- Provisioning results -->
            <div class="row">
                <div class="span11">
                    <table class="table table-bordered" style="display:none" id="users-table">
                        <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Email</th>
                            <th>User</th>
                            <th>User type</th>
                            <th>Status</th>
                            <th>In Gigya</th>
                        </tr>
                        </thead>
                        <tbody id="users-table-body">
                        </tbody>
                    </table>

                    <table class="table table-bordered" style="width: 35%; display: none" id="stats-table">
                        <tbody>
                            <tr>
                                <th style="width: 60%">Provisioned users</th>
                                <td><span id="provisioned-users"></td>
                            </tr>
                            <tr>
                                <th>Migrated users</th>
                                <td><span id="migrated-users"></span></td>
                            </tr>
                            <tr>
                                <th>Failed migrated users</th>
                                <td><span id="failed-users"></span></td>
                            </tr>
                            <tr>
                                <th>For Re-provisioning</th>
                                <td><span id="failed-subscription"></span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ANZGO-3528 Added by John Renzo S. Sunico, October 13, 2017
                 Add export result button -->
            <div class="row">
                <div class="span11">
                    <form action="/dashboard/provisioning_v2/setup/exportToSheet/" method="POST">
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
    $('input[type=checkbox]').change(function() {
        if (this.checked === true) {
            $('#providerId').show()
        } else {
            $('#providerId').hide()
        }
    });
</script>