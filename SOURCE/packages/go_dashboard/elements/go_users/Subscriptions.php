<?php

defined('C5_EXECUTE') || die(_("Access Denied."));

$th = Loader::helper('concrete/urls');
$url = $th->getToolsURL('autocomplete', 'go_dashboard');
?>
<div class="panel">
    <table width="100%" cellpadding="5" cellspacing="5" border="1">
        <tr>
            <td align="left">
                <form id="add-subscription"
                      action="<?php echo $this->action('addUserSubscription') ?>">
                    <input type="text" name="subscription" id="subscription"
                           placeholder="Search Product"
                           search-url="<?php echo $url ?>"/>
                    <input type="hidden" name="sa_id" id="sa_id"/>
                    <input type="hidden" name="s_id" id="s_id"/>
                    <input type="hidden" name="product_id" id="product_id"/>
                    <input type="submit" class="btn primary"
                           value="<?php echo t('Add') ?>"/>
                </form>
            </td>
        </tr>
    </table>
    <div class="alert" id="subscription-alert" role="alert"
         style="display:none;"></div>
</div>
<div style="clear:both"></div>
<div class="panel-default">
    <p class='header' style="padding-bottom: 17px;"><strong>User Subscriptions</strong>
    <span style="float: right;">
        <input type="submit" class="btn primary submit" id="endDate" value="End Date" disabled="disabled"/>
    </span>
    <span style="float: right; padding-right: 3px;">
        <input type="submit" class="btn primary submit" id="archiveSub" value="Archive" disabled="disabled"/>
    </span>
    <span style="float: right; padding-right: 3px;">
        <input type="submit" class="btn primary submit" id="deactivate" value="Deactivate" disabled="disabled"/>
    </span>
    <span style="float: right; padding-right: 3px;">
        <input type="submit" class="btn primary submit activate" id="activate" value="Activate" disabled="disabled"/>
    </span>
    </p>
    <div class="usersubscription" id="subs">
        <table id="ccm-product-list" class="ccm-results-list" cellspacing="0"
               cellpadding="0" border="0">
            <thead>
            <tr>
                <th><input type="checkbox" id="checkAll"></th>
                <th>Subscription</th>
                <th>SubType</th>
                <th>Creation Date</th>
                <th>EndDate</th>
                <th>Duration</th>
                <th>Active</th>
                <th>AccessCode</th>
                <th>Days Remaining</th>
                <th>Purchase Type</th>
                <th>Added By</th>
            </tr>
            </thead>
            <tbody id="ccm-product-list-body">
            <?php if (isset($userSubscriptions) && !empty($userSubscriptions)): ?>
                <?php foreach ($userSubscriptions as $userSubscription): ?>
                    <?php
                    // SB-272 added by mabrigos to filter Archived subscriptions 
                    if ($userSubscription->Archive === "Y") { 
                        continue; 
                    } 
                    $creatorId = $userSubscription->CreatedBy;
                    $creatorInfo = UserInfo::getByID($creatorId);
                    $creatorName = '-';
                    if ($creatorInfo) {
                        $creatorName = $creatorInfo->getAttribute('uFirstName');
                        $creatorName .= ' ';
                        $creatorName .= $creatorInfo->getAttribute('uLastName');
                    }

                    $entitlement = $userSubscription
                        ->permission
                        ->entitlement;

                    $subscription = $userSubscription
                        ->permission
                        ->entitlement
                        ->product;
                    $subscriptionData = $subscription->metadata;

                    $entitlementType = $userSubscription
                        ->permission
                        ->entitlement
                        ->entitlementType;

                    $duration = $entitlement->Duration;

                    $accessCode = $userSubscription->permission->proof;
                    $accessCode = $accessCode ? $accessCode : '';

                    $daysRemaining = $userSubscription->daysRemaining;

                    $active = 'Y';
                    $isActivated = 'Deactivate';
                    if (!$daysRemaining || $userSubscription->DateDeactivated) {
                        $active = 'N';
                        $isActivated = 'Activate';
                    }
                    
                    ?>
                    <tr class="ccm-list-record">
                        <td>
                            <input type="checkbox" name="usID" value="<?php echo $userSubscription->id ?>">
                        </td>
                        <td>
                            <?php
                            echo $subscriptionData['CMS_Name'] . ' (' . $userSubscription->id . ')';
                            ?>
                        </td>
                        <td><?php echo $entitlement->Type ?></td>
                        <td>
                            <?php echo
                            $userSubscription->created_at->format('Y-m-d H:i:s')
                            ?>
                        </td>
                        <td>
                            <?php echo
                            $userSubscription->ended_at->format('Y-m-d H:i:s')
                            ?>
                        </td>
                        <td>
                            <?php echo $duration ?>
                        </td>
                        <td>
                            <?php echo $active ?>
                        </td>
                        <td>
                            <a href='/dashboard/code_check/<?php echo $accessCode ?>'>
                                <?php echo $accessCode ?>
                            </a>
                        </td>
                        <td><?php echo $daysRemaining ?></td>
                        <td><?php echo $userSubscription->PurchaseType ?></td>
                        <td style="white-space: nowrap;
                            <?php echo !$creatorInfo ? 'text-align: center' : '' ?>"
                        >
                            <?php echo $creatorName ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="3">
                    <?php
                    if (isset($userSubscriptionPagination)) {
                        echo $userSubscriptionPagination;
                    }
                    ?>
                </td>
            </tr>
            <tr></tr>
            </tfoot>
        </table>
    </div>
</div>
<div id="dialog-confirm" title="">
  <p id="confirmText"><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span></p>
</div>
<div id="dialog-endDate">
  <p id="confirmEndDateText"><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span></p>
  <p id="endDateText">Updated End Date: <input type="text" id="datepicker"></p>
</div>
<script type="text/javascript">
// added by jbernardez/mabrigos 20190828
$(function() {
  $("#dialog-confirm").dialog({
    resizable: false,
    autoOpen: false,
    height: "auto",
    width: 400,
    modal: true
  });

  $("#dialog-endDate").dialog({
    resizable: false,
    autoOpen: false,
    height: "auto",
    width: 400,
    modal: true
  });

  $("#datepicker").datepicker();

  $("#subs").on("click", "#checkAll", function() {
    $("input[name='usID']").prop("checked", $("#checkAll").prop("checked"));
  });

  $("#subs").on("click", "input[type=checkbox]", function() {
    disableSubmitButtons();
  });

  $("#activate").on("click", function() {
    $("#ui-dialog-title-dialog-confirm").text(
      "Activate " + getCheckboxCount() + " subscriptions?"
    );
    $("#confirmText").text(
      "These will activate " +
        getCheckboxCount() +
        " subscriptions. Are you sure?"
    );
    $("#dialog-confirm").dialog({
      buttons: {
        "Activate resources": function() {
          toggleUserSubscription(getUsIds(), true);
          disableSubmitButtons(true);
          $(this).dialog("close");
        },
        Cancel: function() {
          $(this).dialog("close");
        }
      }
    });
    $("#dialog-confirm").dialog("open");
  });

  $("#deactivate").on("click", function() {
    $("#ui-dialog-title-dialog-confirm").text(
      "Deactivate " + getCheckboxCount() + " subscriptions?"
    );
    $("#confirmText").text(
      "These will deactivate " +
        getCheckboxCount() +
        " subscriptions. Are you sure?"
    );
    $("#dialog-confirm").dialog({
      buttons: {
        "Deactivate resources": function() {
          toggleUserSubscription(getUsIds(), false);
          disableSubmitButtons(true);
          $(this).dialog("close");
        },
        Cancel: function() {
          $(this).dialog("close");
        }
      }
    });
    $("#dialog-confirm").dialog("open");
  });

  $("#archiveSub").on("click", function() {
    $("#ui-dialog-title-dialog-confirm").text(
      "Archive " + getCheckboxCount() + " subscriptions?"
    );
    $("#confirmText").text(
      "These will archive " +
        getCheckboxCount() +
        " subscriptions. Are you sure?"
    );
    $("#dialog-confirm").dialog({
      buttons: {
        "Archive resources": function() {
          archiveSubscription(getUsIds());
          disableSubmitButtons(true);
          $(this).dialog("close");
        },
        Cancel: function() {
          $(this).dialog("close");
        }
      }
    });
    $("#dialog-confirm").dialog("open");
  });

  $("#endDate").on("click", function() {
    $("#ui-dialog-title-dialog-endDate").text(
      "Update End date of " + getCheckboxCount() + " subscriptions?"
    );
    $("#confirmEndDateText").text(
      "These will update the end date of " +
        getCheckboxCount() +
        " subscriptions. Are you sure?"
    );
    $("#dialog-endDate").dialog({
      buttons: {
        "Update End Date": function() {
          var endDate = $("#datepicker").val();
          if (!endDate) {
            alert("no date"); // change this one to handle no end date
          } else {
            updateEndDateSubscription(getUsIds(), endDate);
            disableSubmitButtons(true);
            $(this).dialog("close");
          }
        },
        Cancel: function() {
          $(this).dialog("close");
        }
      }
    });
    $("#dialog-endDate").dialog("open");
  });
});

function getCheckboxCount() {
  var count = $("input[type=checkbox]:checked").length;
  if ($("#checkAll").is(":checked")) {
    count = count - 1;
  }
  return count;
}

function getUsIds() {
  var usIds = [];
  $.each($("input[name='usID']:checked"), function() {
    usIds.push($(this).val());
  });
  return usIds;
}

function disableSubmitButtons(disable = false) {
  if (getCheckboxCount() > 0 && !disable) {
    $(".submit").prop("disabled", false);
  } else {
    $(".submit").prop("disabled", true);
  }
}

// SB-303 SB-295 added by jbernardez/mabrigos 20190823
function toggleUserSubscription (usid, switchToActive) {
  var data = {
    usIDs: usid,
    switchToActive: switchToActive,
    userID: <?php echo $user[0]->uID ?>,
    is_ajax: 'yes',
    func: 'togglesubscriptionstatus'
  };

  $.ajax({
    type: 'POST',
    data: data,
    url: '<?php print str_replace("&", "&", $this->action('userSubscriptionActivations')); ?>',
    success: function (data) {
      if (data.status === false) {
        alert(data.message)
        $('.subscription').val('')
      } else {
        $('div.usersubscription').html(data)
      }
    },
    error: function (xhr, ajaxOptions, thrownError) {
      $('.usersubscription').html(xhr.status + '<br/>' + thrownError)
    }
  })
}

// SB-303 SB-295 added by jbernardez/mabrigos 20190823
function archiveSubscription (usid) {
  var data = {
    usIDs: usid,
    userID: <?php echo $user[0]->uID ?>,
    is_ajax: 'yes',
    func: 'togglesubscriptionstatus'
  };

  $.ajax({
    type: 'POST',
    data: data,
    url: '<?php print str_replace("&", "&", $this->action('userSubscriptionArchives')); ?>',
    success: function (data) {
      if (data.status === false) {
        alert(data.message)
        $('.subscription').val('')
      } else {
        $('div.usersubscription').html(data)
        }
    },
    error: function (xhr, ajaxOptions, thrownError) {
      $('.usersubscription').html(xhr.status + '<br/>' + thrownError)
    }
  })
}

// SB-303 SB-295 added by jbernardez/mabrigos 20190823
function updateEndDateSubscription (usid, endDate) {
  var data = {
    usIDs: usid,
    endDate: endDate,
    userID: <?php echo $user[0]->uID ?>,
    is_ajax: 'yes',
    func: 'togglesubscriptionstatus'
  };

  $.ajax({
    type: 'POST',
    data: data,
    url: '<?php print str_replace("&", "&", $this->action('userSubscriptionEndDates')); ?>',
    success: function (data) {
      if (data.status === false) {
        alert(data.message)
        $('.subscription').val('')
      } else {
        $('div.usersubscription').html(data)
      }
    },
    error: function (xhr, ajaxOptions, thrownError) {
      $('.usersubscription').html(xhr.status + '<br/>' + thrownError)
    }
  })
}
</script>
