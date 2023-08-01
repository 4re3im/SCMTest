$(document).ready(function () {
  loadSubscriptions();

  /*
  * GCAP-839 Added by mtanada 20200422
  * Reference: SB-303 SB-295
  */
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
    $("input[name='userSubsId']").prop("checked", $("#checkAll").prop("checked"));
  });

  $("#subs").on("click", "input[type=checkbox]", function() {
    disableSubmitButtons();
  });

  // Activate Subscription
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

  // Deactivate Subscription
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

  // Archive Subscription
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

  // End date modify subscription
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
            // change this one to handle no end date
            alert("no date");
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
  // END GCAP-839

  $('#gigya-search')
    .autocomplete({
      source: '/dashboard/gigya/users/searchByEmail',
      minLength: 3,
      select: function (event, ui) {
        location.href = '/dashboard/gigya/subscriptions/' + ui.item.label;
      }
    })
    .data('autocomplete')._renderItem = function (ul, item) {
    var resultsHtml =
      '<a><strong>' +
      item.value +
      '</strong><br/><span>' +
      item.name +
      '</span></a>';
    return $('<li></li>')
      .data('item.autocomplete', item)
      .append(resultsHtml)
      .appendTo(ul);
  };

  $('#gigya-search-subscriptions').autocomplete({
    source: '/dashboard/gigya/subscriptions/search',
    open: function (event, ui) {
      $('.ui-autocomplete').width($('#gigya-search-subscriptions').width());
    },
    select: function (event, ui) {
      $('#subscription').val(ui.item.label);
      $('#sa_id').val(ui.item.id);
      $('#s_id').val(ui.item.s_id);
      $('#product_id').val(ui.item.p_id);
    }
  });

  $(document).on('submit', '#add-subscription', function (e) {
    e.preventDefault();

    if (!$('#sa_id').val()) {
      showNotif('alert-danger', 'Subscription is empty');
      return false;
    }

    $.when(addSubscription($(this)))
      .then(
        function () {  // done
          $('input#gigya-search-subscriptions').val('');
          loadSubscriptions();
        },
        function (message) { // fail
          showNotif('alert-danger', message);
          $('input#subscription').val('');
        }
      );
  });

  $(document).on('submit', '#ccm-user-advanced-search', function (e) {
    e.preventDefault();
  });

  $(document).on('click', '.status-toggler', function () {
    $.when(toggleSubscription($(this)))
      .then(
        function () { // done
          setTimeout(function(){
            loadSubscriptions();
          }, 1000);

        },
        function (message) { // fail
          showNotif('alert-danger', message);
        }
      );
  });
});

var deferred;

// START GCAP-839 added by mtanada 20200422 reference SB-303
function getCheckboxCount() {
  var count = $("input[type=checkbox]:checked").length;
  if ($("#checkAll").is(":checked")) {
    count = count - 1;
  }
  return count;
}

function getUsIds() {
  var usIds = [];
  $.each($("input[name='userSubsId']:checked"), function() {
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

function toggleUserSubscription (usid, switchToActive) {
  var uid = $('#gigya-uid').val();
  var data = {
    usIDs: usid,
    switchToActive: switchToActive,
    userID: uid,
    is_ajax: 'yes',
    func: 'togglesubscriptionstatus'
  };

  $.ajax({
    type: 'POST',
    data: data,
    url: '/dashboard/gigya/subscriptions/setActivateDeactivateUserSubscription',
    success: function (data) {
      if (data.status === false) {
        alert(data.message)
        $('.subscription').val('')
      } else {
        loadSubscriptions();
      }
    },
    error: function (xhr, ajaxOptions, thrownError) {
      $('.usersubscription').html(xhr.status + '<br/>' + thrownError)
    }
  })
}

// Archive Subscription Event
function archiveSubscription (usid) {
  var uid = $('#gigya-uid').val();
  var data = {
    usIDs: usid,
    userID: uid,
    is_ajax: 'yes',
    func: 'togglesubscriptionstatus'
  };

  $.ajax({
    type: 'POST',
    data: data,
    url: '/dashboard/gigya/subscriptions/archiveUserSubscription',
    success: function (data) {
      if (data.status === false) {
        alert(data.message)
        $('.subscription').val('')
      } else {
        loadSubscriptions();
      }
    },
    error: function (xhr, ajaxOptions, thrownError) {
      $('.usersubscription').html(xhr.status + '<br/>' + thrownError)
    }
  })
}

function updateEndDateSubscription (usid, endDate) {
  var uid = $('#gigya-uid').val();
  var data = {
    usIDs: usid,
    endDate: endDate,
    userID: uid,
    is_ajax: 'yes',
    func: 'togglesubscriptionstatus'
  };

  $.ajax({
    type: 'POST',
    data: data,
    url: '/dashboard/gigya/subscriptions/setUserSubscriptionEndDate',
    success: function (data) {
      if (data.status === false) {
        alert(data.message)
        $('.subscription').val('')
      } else {
        loadSubscriptions();
      }
    },
    error: function (xhr, ajaxOptions, thrownError) {
      $('.usersubscription').html(xhr.status + '<br/>' + thrownError)
    }
  })
}
// END GCAP-839

function loadSubscriptions () {
  var systemId = $('#gigya-system-id').val();
  var uid = $('#gigya-uid').val();
  var tableDiv = $('.usersubscription');

  $.ajax({
    url: '/dashboard/gigya/subscriptions/load',
    data: {
      systemID: systemId,
      uid: uid
    },
    type: 'GET',
    dataType: 'html',
    success: function (data) {
      $(tableDiv).html(data);
    },
    error: function (xhr, status, err) {
      $(tableDiv).children('table').find('tbody').html(
        '<tr><td colspan="10">Error loading subscriptions...</td></tr>'
      );
    },
    complete : function(){
      hideNotif();
    },
    cache : false
  });
}

function addSubscription (form) {
  var data = $(form).serializeArray();
  var gigya_uid = $('#gigya-uid').val();
  var gigya_system_id = $('#gigya-system-id').val();
  var submitBtn = $('#gigya-add-subscription');

  if (submitBtn.hasClass('disabled')) {
    return false;
  }

  data.push(
    { name: 'gigya_uid', value: gigya_uid },
    { name: 'gigya_system_id', value: gigya_system_id }
  );

  deferred = $.Deferred();

  $.ajax({
    type: 'POST',
    data: data,
    url: $(form).attr('action'),
    dataType: 'json',
    beforeSend: function () {
      submitBtn.val('Please wait');
      submitBtn.addClass('disabled');
    },
    success: function (d) {
      if (d.status) {
        showNotif('alert-success', 'Subscription added. Loading subscription...');
        deferred.resolve();
      } else {
        deferred.reject(d.message);
      }
    },
    error: function (xhr, ajaxOptions, thrownError) {
      deferred.reject(xhr.responseText);
    },
    complete: function () {
      $('input#gigya-search-subscriptions').val('');
      $('#sa_id').val('');
      $('#s_id').val('');
      $('#product_id').val('');
      submitBtn.val('Add');
      submitBtn.removeClass('disabled');
    },
    cache : false
  });

  return deferred;
}

function toggleSubscription (toggleBtn) {
  if ($(toggleBtn).hasClass('disabled')) {
      return false;
  }

  var subscriptionID = $(toggleBtn).attr('data-subscription-id');
  var gigya_uid = $('#gigya-uid').val();
  var gigya_system_id = $('#gigya-system-id').val();

  deferred = $.Deferred();

  $.ajax({
    url: '/dashboard/gigya/subscriptions/toggle',
    type: 'GET',
    dataType: 'json',
    data: {
      subscription_id: subscriptionID,
      gigya_uid: gigya_uid,
      gigya_system_id: gigya_system_id
    },
    beforeSend: function () {
      $(toggleBtn).addClass('disabled');
      $(toggleBtn).val('Please wait');
    },
    success: function (data) {
      if (data.status) {
        deferred.resolve();
      } else {
        deferred.reject('There was an error toggling the subscription.');
      }
    },
    error: function (xhr, status, err) {
      deferred.reject('There was an error toggling the subscription.');
    },
    cache: false
  });

  return deferred;
}

var timeout;
function showNotif (className, content, delay) {
  delay = delay || 4500;
  var notif = $('#subscription-alert');
  notif.addClass(className);
  notif.html(content);
  notif.slideDown();

  timeout = setTimeout(function () {
    hideNotif();
  }, delay);
}

function hideNotif() {
  var openNotif = $('#subscription-alert');
  $(openNotif).slideUp(400, function(){
    $(openNotif).attr('class', 'alert');
  });
  clearTimeout(timeout);
}
