var filename;
var inProgress = false;

$(document).ready(function () {
  var searchBox = $('#search-box');
  var autoCompleteSource = $(searchBox).attr('search-url');
  $(searchBox).autocomplete({
    delay: 800,
    source: autoCompleteSource,
    select: function (event, ui) {
      $('#subscriptions-table').show();
      if (ui.item.value !== 0) {
        var html = '<tr>';
        html += '<td class=\'subs-name\'>' + ui.item.label;
        html += '<input type=\'hidden\' name=\'subs[]\' value=\'' + ui.item.s_id + '\' class=\'subs-ids\' />';
        html += '<input type=\'hidden\' name=\'subsAvail[]\' value=\'' + ui.item.id + '\' class=\'subs-ids\' />';
        html += '</td>';
        html += '<td><a href=\'#\' class=\'btn btn-small btn-default remove-subs\' title=\'Remove subscription\'>';
        html += '<i class=\'icon-trash\'></i></a></td>';
        html += '</tr>';

        $('#subscriptions-table > tbody').append(html);
      }
      $(this).val('');
      return false;
    }
  });

  $(document).on('click', '.remove-subs', function () {
    $(this).closest('tr').remove();

    var subscriptionTable = $('#subscriptions-table');
    if ($(subscriptionTable).find('tbody').children('tr').length < 1) {
      $(subscriptionTable).hide();
    }
  });

  var formOptions = {
    beforeSubmit: preSubmitForm,
    success: postSubmitForm,
    uploadProgress: onSubmitForm,
    error: formError,
    type: 'post',
    dataType: 'json',
    forceSync: true
  };

  $('#archiving-file').change(function (e) {
    var fullPath = $(this).val();
    if (fullPath) {
      var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
      filename = fullPath.substring(startIndex);
      if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
        filename = filename.substring(1);
      }
    }

    var conf = confirm('Upload file: ' + filename + '?');
    if (conf) {
      $('#subscriptions-form').submit();
      return true;
    } else {
      e.preventDefault();
      $(this).val('');
      return false;
    }
  });

  $('#subscriptions-form').submit(function (e) {
    e.preventDefault();
    $(this).ajaxSubmit(formOptions);
    return false;
  });

  $(window).on('beforeunload', function () {
    if (inProgress) {
      return false;
    }
  });

});

function preSubmitForm (formData, jqForm, options) {
  var temp = filename.split('.');
  switch (temp[1]) {
    case 'xls':
    case 'xlsx':
      break;
    default:
      var alert = $('.alert');
      $(alert).html('Invalid file types. Please upload .xls or .xlsx files only.');
      (alert).addClass('alert-danger');
      (alert).show();
      $('#archiving-file').focus();
      setInterval(function () {
        $('.alert').hide();
      }, 4000);
      return false;
  }
  inProgress = true;
  return true;
}

// ANZGO-3642 Modified by John Renzo Sunico, 02/22/2018
function onSubmitForm () {
  $('#alert-box').show();
  $('.alert-info').html('Removing subscriptions...');
}

var error = function (xhr) {
  console.log(xhr.responseText);
};

function postSubmitForm (data) {
    console.log('Time started: ' + new Date());

    fileRecordId = data.fileRecordId;
    total = data.total;

    $('#file-record-id').val(fileRecordId);
    $('#downloadFileId').val(fileRecordId);

    var form = $('#subscriptions-form');
    var postData = $(form).serialize();

    for (var i = 0; i < data.pages; i++) {
        addAjax({
            url: '/dashboard/provisioning/archive/getUsersFromGigya/' + data.fileRecordId + '/' + i,
            type: 'POST',
            dataType: 'json',
            data: postData,
            error: error
        });
    }
}

function formError (xhr) {
  var alert = $('.alert');
  $(alert).html(xhr.responseText);
  $(alert).addClass('alert-danger');
  $(alert).show();
}

function finishArchiving () {
  console.log('Time completed: ' + new Date());
  $('.alert-info').removeClass('alert-info').addClass('alert-success').html('Removing subscriptions completed')
}

// ANZGO-3899 Added by Shane Camus 10/19/18
// ANZGO-3895 POC by John Renzo Sunico 10/19/18
// Ajax Queue Handler

var ajaxReqs = 0;
var ajaxQueue = [];
var ajaxActive = 0;
var ajaxMaxConc = 6;
var callback = function () {
  ajaxReqs--;

  var nextRequest = ajaxQueue.shift();

  if (ajaxActive === ajaxMaxConc && nextRequest !== undefined) {
    $.ajax(nextRequest);
  } else {
    ajaxActive--;
  }

  if (ajaxReqs === 0) {
    finishArchiving();
  }
};

function addAjax (obj) {
  ajaxReqs++;
  var oldSuccess = obj.success;
  var oldError = obj.error;

  obj.success = function (resp, xhr, status) {
    callback();
    if (oldSuccess) oldSuccess(resp, xhr, status);
  };
  obj.error = function (xhr, status, error) {
    callback();
    if (oldError) oldError(xhr, status, error);
  };

  if (ajaxActive === ajaxMaxConc) {
    ajaxQueue.push(obj);
  } else {
    ajaxActive++;
    $.ajax(obj);
  }
}
