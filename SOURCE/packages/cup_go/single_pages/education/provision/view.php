<?php
/*
 * View Test Form
 * @author: Ariel Tabag <atabag@cambridge.org>
 */

defined('C5_EXECUTE') or die("Access Denied.");

$upload_file = $this->url('/education/provision', 'uploadFile');

$search_product = $this->url('/education/provision', 'searchProduct');

$add_user_subscription = $this->url('/education/provision', 'addUserSubscription');

?>

<script>var upload_file = '<?php echo $upload_file; ?>', search_product = '<?php echo $search_product; ?>', add_user_subscription = '<?php echo $add_user_subscription; ?>'</script>

<div class="ccm-pane">
    <div class="ccm-pane-header">
        <h3>GO Batch Account Management </h3>
    </div>
    <div class="ccm-pane-body">
        <fieldset>
            <legend>Excel File Upload</legend> 
            <div style="float: left">
                <div class="clearfix2">
                     <div class="text-label2">Download sample <a href="<?php echo $sample_excel ?>" >excel document</a> </div>
                </div>
                <div class="clearfix3">
                     <div class="text-label2" >Upload excel file with student accounts</div>
                    <div class="text-controls">
                        <input type="file" name="file" id="file">
                    </div>
                </div>
                <div class="clearfix2">
                    <a class="ccm-button-right btn primary accept" id="process_account" >Process Accounts</a>
                </div>
                <div style="margin-left: 180px;">
                    <a id="refresh" class="ccm-button-right btn primary accept">Reload</a>
                </div>
            </div>
            <div style="float: rightt" >
                <div class="clearfix2">
                    <div class="text-label2">Add user only<input type="checkbox" name="user_only" id="user_only" style="height: 20px; width: 30px;"></div>
                </div>
                <div class="text-label1">Do not verify users & use only in special Circumstances.  </div>
            </div>
        </fieldset>
    </div>
    <div class="ccm-loading ccm-div" style="display: none;">&nbsp;</div>
    <div class="ccm-warning ccm-div alert" style="display: none;">&nbsp;</div>
    <div class="ccm-result ccm-div" style="display: none;">
        <div class="ccm-pane-body">
            <fieldset>
                <legend>
                    Results
                    <?php if(in_array('Customer service', $groups) || in_array('Administrators', $groups) || $u->uID==1){ ?>
                    <div style="float: right">
                        <div id="search_wrapper">
                            <div id="search" class="search-icon"></div>
                            <input type="text" class="search legend-span4" id="search_product" placeholder="Add product (Enter Name or ISBN)" />
                            <div id="result" style="display: none;" ></div>
                        </div>
                        <div style="float: right;"><a id="assign" class="ccm-button-right btn primary accept">Assign</a><input type="hidden" id="search_id"/></div>
                    </div>
                    <?php } ?>
                </legend>       
                <table class="table table-hover table-bordered table-striped" id="provision">
                    <thead>
                        <tr>
                            <th><input type="checkbox" name="check_all" id="check_all" /> </th>
                            <th>Login</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th width="300px">
                                <span style="float:left;">Access Code</span> 
                                <span style="margin-left: 115px;">Product</span>
                            </th>
                            <th>State</th>
                            <th>Post Code</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <div class="text-label2"><span class="account-created" >Green </span>name means new account created</div><br />
                <div class="text-label2"><span class="account-exists" >Yellow </span>name means account already exists</div><br />
                <div class="text-label2"><span class="access-code-used" >Red </span>Access Code means code has already been used</div><br />

            </fieldset>
        </div>

        <div class="ccm-pane-body">
            <fieldset>
                <legend>Summary </legend>
                <div class="summary"></div>
            </fieldset>
        </div>
    </div>
    <div class="ccm-pane-footer">

    </div>
</div>
<pre id="error">
    
</pre>
