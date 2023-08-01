var filename;
var inProgress = false;

$(document).ready(function () {
  var searchBox = $('#search-box');
  var autoCompleteSource = $(searchBox).attr('search-url');
  
  // SB-577 added by mabrigos 20200603
  $('#multipleIds').on('keypress', function(e) {
    var entitlementIds = $('#multipleIds').val()
    var baseUrl = window.location.href

    if (baseUrl.slice(-1) == '#') {
      var url = baseUrl.substring(0, baseUrl.length - 1) + 'getMultipleEntitlements';
    } else {
      var url = window.location.href + 'getMultipleEntitlements'
    }
    
    if (e.which == 13) {
      $.ajax({
        url: url,
        type: 'POST',
        data: {'entitlementIds': entitlementIds},
        dataType: 'json',
        success: function (data) {
          if (data.length !== 0) {
            $('#subscriptions-table').show();
            for (var i = 0; i < data.length; i++) {
              var html = '<tr>';
              html += '<td class=\'subs-name\'>' + data[i].label;
              html += '<input type=\'hidden\' name=\'subs[]\' value=\'' + data[i].s_id + '\' class=\'subs-ids\' />';
              html += '<input type=\'hidden\' name=\'subsAvail[]\' value=\'' + data[i].id + '\' class=\'subs-ids\' />';
              html += '</td>';
              html += '<td><a href=\'#\' class=\'btn btn-small btn-default remove-subs\' title=\'Remove subscription\'>';
              html += '<i class=\'icon-trash\'></i></a></td>';
              html += '</tr>';
              $('#subscriptions-table > tbody').append(html);
            }
          } else {
            alert("No entitlements found")
          }
        },
        error: function (xhr, status, err) {
          console.log(xhr.responseText);
        }
      })
    }
  });

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

  $('#provisioning-file').change(function (e) {
    var fullPath = $(this).val();
    if (fullPath) {
      var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
      filename = fullPath.substring(startIndex);
      if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
        filename = filename.substring(1);
      }
    }
    // GCAP-541 added by machua/mtanada 20191015
    if ($('#providerId').val()) {
        var conf = confirm('Are you sure to upload file with ONLY LITE Users: ' + filename + '?');
    } else {
        var conf = confirm('Upload file: ' + filename + '?');
    }
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

  $(document).on('click', '.numbers > a', function (e) {
    e.preventDefault();

    $('.numbers').removeClass('active');
    $(this).parents('span').addClass('active');

    var fileId = $('#file-record-id').val();
    var url = $(this).attr('href') + fileId;

    $.ajax({
      url: url,
      type: 'GET',
      dataType: 'json',
      beforeSend: function () {
        $('#users-table-body').addClass('table-block');
      },
      success: function (data) {
        $('#users-table-body').html(data['users']);
      },
      error: function (xhr, status, err) {
        console.log(xhr.responseText);
      },
      complete: function () {
        $('#users-table-body').removeClass('table-block');
      }
    });
  });

  $(document).on('click', '.ccm-page-right > a', function (e) {
    e.preventDefault();
    var nextLi = $('span.active').next();
    $(nextLi).children('a').trigger('click');
  });

  $(document).on('click', '.ccm-page-left > a', function (e) {
    e.preventDefault();
    var prevLi = $('span.active').prev();
    $(prevLi).children('a').trigger('click');
  });

  // ANZGO-3642 Added by John Renzo Sunico, 02/22/2018
  $(window).on('beforeunload', function () {
    if (inProgress) {
      return false;
    }
  });
});

// ANZGO-3642 Modified by John Renzo Sunico, 02/22/2018
function preSubmitForm (formData, jqForm, options) {
  var temp = filename.split('.');
  switch (temp[1]) {
    case 'xls':
    case 'xlsx':
    case 'csv':
      break;
    default:
      var alert = $('.alert');
      $(alert).html('Invalid file types. Please upload .xls, .xlsx or .csv files only.');
      (alert).addClass('alert-danger');
      (alert).show();
      $('#provisioning-file').focus();
      setInterval(function () {
        $('.alert').hide();
      }, 4000);
      return false;
  }
  inProgress = true;
  return true;
}

// ANZGO-3642 Modified by John Renzo Sunico, 02/22/2018
function onSubmitForm (event, position, total, percentComplete) {
  $('#alert-box').show();
  $('.alert-info').html('Provisioning users...');
}

/**
 * ANZGO-3528 Modified by John Renzo Sunico, October 13, 2017
 * ANZGO-3642 Modified by John Renzo Sunico, 02/22/2018
 * Incorporate export form.
 * @param data
 * return void
 */
var completed = 0;
var total = 0;
var fileRecordId = null;
var successProvision = function (data) {
  completed++;

  var progress = Math.round((completed / total) * 100);
  var progressPercentage = progress + '%';
  var progressBar = $('#provisioning-progress');

  $(progressBar).find('.bar').animate({ width: progressPercentage }).text(progressPercentage);

  $('.alert-info').html(
    'Provisioning users... ' +
    (total - completed) + ' of ' + total + ' remaining. Please do not leave this page.'
  );

  updateResultTable(data.table, true);
};

var error = function (xhr, status, err) {
  console.log(xhr.responseText);
};

// ANZGO-3899 Added by Shane Camus 10/19/18
// ANZGO-3895 POC by John Renzo Sunico 10/19/18
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
      url: '/dashboard/provisioning_hotmaths/setup/provision/' + data.fileRecordId + '/' + i,
      type: 'POST',
      dataType: 'json',
      data: postData,
      success: successProvision,
      error: error
    });
  }
}

function formError (xhr, status, err) {
  var alert = $('.alert');
  $(alert).html(xhr.responseText);
  $(alert).addClass('alert-danger');
  $(alert).show();
}

// ANZGO-3642 Added by John Renzo Sunico, 02/22/2018
function finishProvisioning () {
  console.log('Time completed: ' + new Date());

  refreshResult(fileRecordId, function (data) {
    showStatusInfo('Uploading users to Gigya. Please wait.');
    updateResultTable(data, true);

    provisionInGigya();
  });
}

function provisionInGigya () {
  console.log('Gigya time start: ' + new Date());
  // GCAP-541 added by machua/mtanada 20191015
  var providerId = $('#providerId').val();
  $.ajax({
    url: '/dashboard/provisioning_hotmaths/setup/provisionInGigya/' + fileRecordId + '/' + providerId,
    type: 'POST',
    dataType: 'json',
    beforeSend : function(){
      showStatusInfo('Users enqueue to Gigya.');
    },
    success: function (data) {
      updateResultTable(data, true);
      if(data.success) {

        checkJobStatus(data.scheduleId);
      } else {
        showStatusInfo(data.message);
      }
    },
    error: function () {
      showStatusInfo('There was a problem setting up Gigya migration.');
      hideInfoShowControls();
    }
  });
}

function checkJobStatus (scheduleId) {
  $.ajax({
    url : '/dashboard/provisioning_hotmaths/setup/getJobStatus/' + scheduleId,
    type : 'GET',
    dataType: 'json',
    beforeSend : function(){
      showStatusInfo('Gigya migration currently running.');
    },
    success : function(data){
      if(data.status === 'running' || data.status === 'pending') {
        checkJobStatus(scheduleId);
      } else {
        checkUsersInGigya();
      }
    },
    error: function(){
      showStatusInfo('Unable to retrieve Gigya migration status.');
      hideInfoShowControls();
    }
  });
}

function checkUsersInGigya () {
  // GCAP-541 added by machua/mtanada 20191015
  var providerId = $('#providerId').val();
  $.ajax({
    url: '/dashboard/provisioning_hotmaths/setup/updateGigyaStatus/' + fileRecordId + '/' + providerId,
    type: 'POST',
    dataType: 'json',
    beforeSend: function(){
      showStatusInfo('Checking the users\' Gigya migration status.');
    },
    success: function (data) {
      showStatusInfo('Users\' Gigya migration status updated.');
      updateResultTable(data, true);
      showStats(data);
    },
    error : function() {
      showStatusInfo('Unable to retrieve the users\' Gigya migration status.');
    },
    complete : function(){
      console.log('Gigya time end: ' + new Date());
      hideInfoShowControls();
    }
  });
}

function startPollingStatus () {
  setTimeout(checkGigyaProvisionStatus, 1000);
}

function checkGigyaProvisionStatus () {
  $.ajax({
    url: '/dashboard/provisioning_hotmaths/setup/getGigyaProvisioningStatus/' + fileRecordId,
    type: 'POST',
    dataType: 'json',
    success: function (data) {
      if (!data.isDone) {
        startPollingStatus();
        return;
      }

      refreshResult(fileRecordId, function (data) {
        $('.alert-info').html('Users provisioned in Gigya. Provisioning complete.');

        updateResultTable(data, true);
        hideInfoShowControls();
      });
    }
  });
}

function hideInfoShowControls () {
  setTimeout(function () {
    inProgress = false;
    $('.alert-info').fadeOut();
    $('#provisioning-progress').fadeOut();
    $('#exportBtn').removeClass('invisible');
  }, 8000);
}

function refreshResult (fileRecordId, successCallback) {
  $.ajax({
    url: '/dashboard/provisioning_hotmaths/setup/displayUsers/' + 1 + '/' + fileRecordId,
    type: 'POST',
    dataType: 'json',
    success: successCallback
  });
}

// ANZGO-3642 Added by John Renzo Sunico, 02/22/2018
// HUB-100 Modified by Carl Lewi Godoy, 08/02/2018
function updateResultTable (result, updatePagination) {
  $('#users-table').show();
  $('#users-table-body').html(result.users);
  if (updatePagination === true) {
    $('#pagination').html(result.pagination);
  }
  $('#provisioning-progress').removeClass('invisible');
  $('#legend-toggle').removeClass('invisible');
  $('#legend').removeClass('invisible');
  showOrHidePagination();
}

function showStats (data) {
  var tngUsers = data.totalTngUsers;
  var gigyaUsers = data.totalGigyaUsers;
  $('#provisioned-users').html(tngUsers);
  $('#migrated-users').html(gigyaUsers);

  $('#failed-users').html(parseInt(tngUsers) - parseInt(gigyaUsers));
  $('#stats-table').show();
}

// ANZGO-3642 Added by John Renzo Sunico, 02/22/2018
function showOrHidePagination () {
  if ($('.ccm-pagination').find('.numbers').length < 2) {
    $('#pagination').hide();
  } else {
    $('#pagination').show();
  }
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
    finishProvisioning();
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
