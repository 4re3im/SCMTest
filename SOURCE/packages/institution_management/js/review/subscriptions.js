$(function() {
  var autoCompleteSource = '/dashboard/institution_management/review/search'
  $('#subscription').autocomplete({
    source: autoCompleteSource,
    open: function (event, ui) {
      $('.ui-autocomplete').width($('#subscription').width());
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
    add()
  });

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
          update(getUsIds(), 'activate');
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
          update(getUsIds(), 'deactivate');
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
          update(getUsIds(), 'archive');
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
            update(getUsIds(), 'endDate', endDate);
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

function update(usid, param, endDate = false) {
  var data = {
    usIDs: usid,
    oid,
    param,
    endDate
  };

  $.ajax({
    type: 'POST',
    data: data,
    url: '/dashboard/institution_management/review/update',
    dataType: 'json',
    success: function (data) {
      if (!data.success) {
        showNotif('alert-danger', data.message);
      } else {
        $('div.usersubscription').html(data.parameters.body)
      }
    },
    error: function (xhr, ajaxOptions, thrownError) {
      $('.usersubscription').html(xhr.status + '<br/>' + thrownError)
    }
  })
}

function add() {
  var data = {
    oid,
    sa_id: $('#sa_id').val(),
    s_id: $('#s_id').val()
  }

  $.ajax({
    type: 'POST',
    data: data,
    url: '/dashboard/institution_management/review/add',
    dataType: 'json',
    success: function (data) {
      if (data.success === false) {
        showNotif('alert-danger', data.message);
      } else {
        $('div.usersubscription').html(data.message)
      }
      $('#subscription').val('')
    },
    error: function (xhr, ajaxOptions, thrownError) {
      $('.usersubscription').html(xhr.status + '<br/>' + thrownError)
    }
  })
}