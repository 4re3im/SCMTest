<?php
$package = 'go_dashboard';
$th = Loader::helper('concrete/urls');
$url = $th->getToolsURL('accesscode-autocomplete', $package);
$user_url = $th->getToolsURL('user-autocomplete', $package);
echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Code Check'), false, false, false);
$searchString = $_POST['search_string'];
$isHotMaths = $_POST['hmCheckbox'] == 'on';
?>
<style type="text/css">
    div.ccm-pagination span.numbers {
        padding: 3px 8px;
    }

    .ui-autocomplete li {
        font-size: 12px;
        font-weight: bold;
    }

    .ui-autocomplete li span {
        font-weight: 500;
    }

    .ui-autocomplete {
        border: 1px solid #ccc;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        border-radius: 5px 5px 5px 5px;
    }

    input#searchuser {
        width: 300px;
    }
</style>
<div style="clear:both;"></div>
<div class="ccm-pane-body">
    <div id="searchform" style="" align="center">
        <form method="post" action="/dashboard/code_check/">
            <table width="100%" cellpadding="5" cellspacing="5" border="1">
                <tr>
                    <?php if ($searchString != '' || isset($parameter)) { ?>
                        <td align="left">
                            <a href="<?php echo $this->url("/dashboard/code_check"); ?>">
                                <input
                                        type="button"
                                        class="btn primary"
                                        value="<?php echo t(' << Show Summary') ?>"
                                />
                            </a>
                        </td>
                    <?php } ?>
                    <td align="right">
                        <div class="checkbox btn btn-primary"
                             style="padding-left:5px;margin-top:1px">
                            <label style="color:white;'">
                                <input style="float:left;"
                                       id="hmCheckbox"
                                       type="checkbox"
                                       name="hmCheckbox"
                                       autocomplete="off">HOTmaths
                            </label>
                        </div>
                        <input type="text" id="search-accesscode"
                               name="search_string" style="height: 28px;"/>
                        <input type="hidden" name="accesscode_id"
                               id="accesscode_id"/>
                        <input type="submit" class="btn primary"
                               value="<?php echo t('Search AccessCode') ?>"/>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <div style="clear:both"></div>
    <?php
    if ($searchString != '' || isset($parameter)) {
        if (!$isHotMaths) {
            $element = 'code_check/code_check';

            Loader::packageElement(
                $element,
                $package,
                array(
                    'accessCodeDetails' => @$accessCodeDetails,
                    'status' => @$status,
                    'activatedBy' => @$activatedBy,
                    'previouslyActivatedBy' => @$previouslyActivatedBy,
                    'previousReleaseDates' => @$previousReleaseDates,
                    'codeErrors' => @$codeErrors,
                    'codeFlag' => @$codeFlag,
                    'CLSAlerts' => @$CLSAlerts
                )
            );
        } else {
            Loader::packageElement(
                'code_check/code_check_hm',
                $package,
                array(
                    'accessCodeDetails' => @$accessCodeDetails,
                    'status' => @$status
                )
            );
        }
    } else {
        Loader::packageElement(
            'codefail_summary',
            $package,
            array(
                'codeFailList' => @$codeFailList
            )
        );
    }
    ?>
    <div style="clear:both;height:5px;width:100%"></div>
</div>
<div style="clear:both"></div>
<div class="ccm-pane-footer">
    <?php
    if (!isset($searchString) && !isset($parameter)) {
        echo $codeFailListPagination;
    }
    ?>
</div>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false) ?>


<?php
  $accessCodeId = $accessCodeDetails->id;
  $accessCode = $accessCodeDetails->proof;
?>

<?php if (!$isHotMaths): ?>
    <div class="modal fade" id="redeemModal" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">
                        Assign
                        [<?php echo trim($accessCode) ?>]
                        to User
                    </h4>
                </div>
                <form
                        action="<?php echo $this->url("/dashboard/code_check", "codeAction") ?>"
                        method="POST"
                        id="redeem-code"
                >
                    <div class="modal-body">
                        <input type="text" id="searchuser"
                               placeholder="Search user" class="form-control"
                               name="email"/>
                        <input type="hidden" id="uID" name="id"/>
                        <input type="hidden" name="code"
                               value="<?php echo trim($accessCode) ?>"/>
                        <input type="hidden" id="code-action-value"
                               name="action" value="redeem"/>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger"
                                data-dismiss="modal">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="releaseModal" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">
                        Release <?php echo trim($accessCode) ?>
                    </h4>
                </div>
                <form
                        action="<?php echo $this->url("/dashboard/code_check", "codeAction"); ?>"
                        method="POST"
                        id="release-code"
                >
                    <div class="modal-body">
                        <input type="hidden" id="uID" name="id"/>
                        <input type="hidden" name="code"
                               value="<?php echo trim($accessCode) ?>"/>
                        <input type="hidden" id="code-action-value"
                               name="action" value="release"/>
                        <input type="hidden" id="code-id" name="code_id"
                               value="<?php echo $accessCodeId ?>"/>
                        <div class="panel panel-info">
                            <div class="panel-body">
                                <p id="code-status">This access code will be
                                    released. Proceed?</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger"
                                data-dismiss="modal">Cancel
                        </button>
                        <input type="submit" class="btn btn-success"
                               value="Proceed"/>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>
