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
            <h3>Go Archive</h3>
        </div>
        <div class="ccm-pane-body">
            <div class="alert" style="display:none;"></div>
            <div class="row">
                <div class="span11">
                    <form id="subscriptions-form" method="POST"
                          action="<?php echo $this->url('/dashboard/provisioning_v2/archive/startArchiving'); ?>"
                          enctype="multipart/form-data">
                        <div class="row-fluid">
                            <div class="span8">
                                <label>Search subscriptions</label>
                                <input type="text" class="form-control" id="search-box" search-url="<?php echo $url ?>"
                                       placeholder="Just type in and click selection." style="width: 100% !important;">
                            </div>
                            <div class="span3">
                                <label>&nbsp;</label>
                                <input type="file" name="excel" value="" class="form-control" id="archiving-file">
                            </div>
                        </div>

                        <!-- Subscriptions table -->
                        <br>
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
        </div>
        <div class="ccm-pane-footer"></div>
    </div>
</div>
