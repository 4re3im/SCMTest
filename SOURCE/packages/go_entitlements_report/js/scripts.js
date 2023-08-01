var filename;
var inProgress = false;

$(document).ready(function () {

  var formOptions = {
    beforeSubmit: preSubmitForm,
    success: postSubmitForm,
    uploadProgress: onSubmitForm,
    error: formError,
    type: 'post',
    dataType: 'json',
    forceSync: true
  };

  $('#emails-file').change(function (e) {
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
});

  $(document).on('click', '.numbers > a', function (e) {
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
      $('#emails-file').focus();
      setInterval(function () {
        $('.alert').hide();
      }, 4000);
      return false;
  }
  inProgress = true;
  return true;
}

function onSubmitForm (event, position, total, percentComplete) {
  $('#alert-box').show();
  $('.alert-info').html('Uploading emails...');
}

function startGenerate (data, index) {
  progressGenerate(data.total, index);
  addAjax({
    url: '/dashboard/entitlements_report/setup/exportToSheet/' + data.fileRecordId + '/' + index,
    type: 'POST',
    dataType: 'json',
    data: data,
    success: function (data) {
      var index = data.index;
      if (data.IsFinished === 0) {
        startGenerate(data, index);
      } else if (data.IsFinished === 1) {
        var hiddenElement = document.createElement('a');
        hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURI(data.csvData);
        hiddenElement.target = '_blank';
        hiddenElement.download = data.filename;
        hiddenElement.click();
        $('#generating-progress').fadeOut("slow");
        $('#exportBtn').addClass('invisible');
      }
    },
    error: error
  });
}

function progressGenerate (total, index) {
  $('#generating-progress').removeClass('invisible');
  if (index === 0 && total < 20) {
    index = total / 2;
  }
  var progress = Math.round((index / total) * 100);
  var progressPercentage = progress + '%';
  var progressBar = $('#generating-progress');
  $(progressBar).find('.bar').animate({ width: progressPercentage }).text(progressPercentage);
}

var error = function (xhr, status, err) {
  console.log(xhr.status);
};


function postSubmitForm (data) {
  console.log('Time started: ' + new Date());

  fileRecordId = data.fileRecordId;
  total = data.total;

  $('#downloadFileId').val(fileRecordId);
  $("#emails-file").prop('disabled', true);
  hideInfoShowControls();

  $(document).on('click', '#exportBtn', function () {
    $("#exportBtn").prop('disabled', true);
    startGenerate(data, 0);
  });
}

function formError (xhr, status, err) {
  var alert = $('.alert');
  $(alert).html(xhr.responseText);
  $(alert).addClass('alert-danger');
  $(alert).show();
}

function finishAjaxRequest() {
  console.log('Time completed: ' + new Date());
}

function hideInfoShowControls () {
  setTimeout(function () {
    inProgress = false;
    $('.alert-info').fadeOut();
    $('#exportBtn').removeClass('invisible');
  }, 8000);
}

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
    finishAjaxRequest();
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

function showStatusInfo (info) {
  $('.alert-info').html(info);
}
