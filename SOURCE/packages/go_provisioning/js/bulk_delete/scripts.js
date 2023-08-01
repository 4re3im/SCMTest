BASE_URL = '/dashboard/provisioning/bulk_delete';
TOTAL_JOBS = 5;
SCHOOL_SEARCH_URL = BASE_URL + '/getInstitutions';

var RESULT_FILE_NAME;

$(document).on('ready', function () {
  // Initially disable file upload
  $('#bulk-actions-file').addClass('disabled');

  // FILE INPUT
  $('#bulk-actions-file').change(function (e) {
    if ($(this).hasClass('disabled')) {
      return false;
    }

    hideProgressBar();
    hideNotification();
    hideTable();
    hideDownloadModalTrigger();
    disableInstitutionField();
    hideRemoveSchoolBtn();

    var fileName = e.target.files[0].name;
    $('#bulk-delete-file-name').html('<strong>' + fileName + '</strong>');
    jQuery.fn.dialog.showLoader();
    modalConfig.height = '15%';
    modalConfig.element = '#confirmation-modal-content';
    modalConfig.title = 'File upload';
    showModal();
  });

  // MODAL BUTTONS
  $('#proceed-btn').click(function (e) {
    disableFileUpload();
    formOptions['success'] = showResponseAfterUpload;

    $('#bulk-actions-form').ajaxSubmit(formOptions);
    hideModal();
  });

  $('#download-btn, #understood-btn').click(function (e) {
    hideModal();
  });

  // NOTIFICATION
  $('#alert-box-close-btn').click(function () {
    hideNotification();
  });

  // DOWNLOAD MODAL TRIGGER
  $('#download-modal-trigger').click(function (e) {
    showDownloadModal();
  });

  // SCHOOL SEARCH
  $('#bulk-actions-institutions').autocomplete({
    source: SCHOOL_SEARCH_URL,
    minLength: 3,
    delay: 1000,
    open: function (event, ui) {
      $('.ui-autocomplete').width($('#bulk-actions-institutions').width());
    },
    select: function (event, ui) {
      if (ui.item.oid === false) {
        modalConfig.height = '15%';
        modalConfig.element = '#no-school-matches-modal-content';
        modalConfig.title = 'No institution found';
        showModal();
        return false;
      }

      SCHOOL_DATA = ui.item;
      var schoolName = ui.item.data.name;
      $('#bulk-actions-institutions').val(schoolName);
      displaySelectedSchool(schoolName);
      enableFileUpload();
      showRemoveSchoolBtn();
      return false;
    },
    search: function (event, ui) {
      // Reset SCHOOL_DATA and disable
      SCHOOL_DATA = null;
      disableFileUpload();
      hideRemoveSchoolBtn();
      hideSelectedSchool();
    }
  })
    .data('autocomplete')._renderItem = function (ul, item) {
    var resultsHtml =
      '<a><strong>' +
      item.data.name +
      '</strong><br/><span>' +
      item.data.formattedAddress +
      '</span></a>';
    return $('<li></li>')
      .data('item.autocomplete', item)
      .append(resultsHtml)
      .appendTo(ul);
  };

  // REMOVE SCHOOL BTN
  $('#school-select-remove').click(function (e) {
    $('#bulk-actions-institutions').val('');
    hideSelectedSchool();
    hideRemoveSchoolBtn();
    disableFileUpload();
    SCHOOL_DATA = null;
  });
});

function showResponseAfterUpload (response) {
  if (response.Status === 5) { // Failed upload
    showNotification('warning', response.Message);
    hideProgressBar();
    enableInstitutionField();
    $('#bulk-actions-institutions').val('');
    SCHOOL_DATA = null;
  } else {
    FILE_RECORD_ID = response.FileRecordID;
    setProgressText(response.Message);
    validateFileContents();

  }
}

function validateFileContents () {
  var currentUrl = BASE_URL + '/validateFileContents/' + FILE_RECORD_ID;

  $.ajax({
    url: currentUrl,
    type: 'GET',
    dataType: 'json',
    beforeSend: function () {
      setProgressText('Validating file contents...');
      setProgressWidth(1);
    },
    success: function (data) {
      if (data.Status === 10) { // Different domain in email
        showNotification('warning', data.Message);
        hideProgressBar();
        return false;
      }
      getUsers();
      runBulkDelete();
    },
    error: function (xhr, status, err) {
      console.log('error 1');
    }
  });
}

function runBulkDelete () {
  $.ajax({
    url: BASE_URL + '/runBulkDelete/' + FILE_RECORD_ID,
    dataType: 'json',
    beforeSend: function () {
      setProgressText('Running bulk delete...');
      setProgressWidth(2);
    },
    success: function (data) {
      // Status 7 = Gigya error
      // Status 11 = No valid records for reset
      if (data.Status === 7 || data.Status === 11) {
        hideProgressBar();
        showNotification('warning', data.Message);
        return false;
      }

      SCHEDULE_ID = JSON.parse(data.Data).scheduleID;
      getUsers();
      getJobStatus();
    },
    error: function (xhr, status, err) {
      console.log('error 2');
    }
  });
}

function getJobStatus () {
  $.ajax({
    url: BASE_URL + '/getJobDetails/' + SCHEDULE_ID,
    dataType: 'json',
    beforeSend: function () {
      setProgressWidth(3);
    },
    success: function (data) {
      var responseData = JSON.parse(data.Data);
      if (responseData.status === 'running' || responseData.status === 'pending') {
        getJobStatus();
      } else if (responseData.status === 'failed') {
        showNotification('warning', 'Bulk delete failed.');
        hideProgressBar();
      } else {
        JOB_ID = responseData.jobID;
        setProgressText('Gigya Bulk Delete finished...');
        getUserStatusFromS3();
      }
    },
    error: function (xhr, status, err) {
      console.log('error 3');
    }
  });
}

function getUserStatusFromS3 () {
  $.ajax({
    url: BASE_URL + '/getUserStatusFromS3/' + JOB_ID + '/' + FILE_RECORD_ID,
    dataType: 'json',
    beforeSend: function () {
      setProgressText('Getting user status from Gigya...');
      setProgressWidth(4);
    },
    success: function (data) {
      if (data.Status === 6) { // Gigya error
        showNotification('warning', data.Message);
        hideProgressBar();
      } else if (data.Status === 12) { //DONE
        getUsers();
        showNotification('success', 'Users\' status updated');
        saveResultsToS3();
      }
    },
    error: function (xhr, status, err) {
      console.log('error 4');
    }
  });
}

function saveResultsToS3 () {
  var instData = {
    name: SCHOOL_DATA.data.name,
    address: SCHOOL_DATA.data.formattedAddress,
    oid: SCHOOL_DATA.oid
  };

  $.ajax({
    url: BASE_URL + '/saveResultsToS3/' + FILE_RECORD_ID,
    data: {
      institution: instData
    },
    dataType: 'json',
    beforeSend: function () {
      setProgressText('Creating result file...');
      setProgressWidth(5);
    },
    success: function (data) {
      if (data.Status === 13 || data.Status === 15) { // Failed upload
        showNotification('warning', data.Message);
        hideProgressBar();
      } else {
        showNotification('success', data.Message);
        hideProgressBar();
        RESULT_FILE_NAME = JSON.parse(data.Data).resultsFilename;
        getUsers();
        showDownloadModalTrigger();
        $('#bulk-actions-institutions').val('');
        enableInstitutionField();
        disableFileUpload();
      }
    },
    error: function (xhr, status, err) {
      console.log('error 5');
    }
  });
}

function showDownloadModal () {
  jQuery.fn.dialog.showLoader();
  modalConfig.height = '15%';
  modalConfig.element = '#download-modal-content';
  modalConfig.title = 'One-time Download';

  $('#bulk-delete-result-file-name').html('<strong>' + RESULT_FILE_NAME + '</strong>');
  var downloadHref = BASE_URL + '/downloadResults/' + RESULT_FILE_NAME;
  $('#download-btn').attr('href', downloadHref);
  showModal();
}

function showDownloadModalTrigger () {
  $('#download-modal-btn-div').show();
}

function hideDownloadModalTrigger () {
  $('#download-modal-btn-div').hide();
}

function getUsers (page) {
  var currentPage = page || 1;
  $.ajax({
    url: BASE_URL + '/getUsers/' + FILE_RECORD_ID + '/' + currentPage,
    dataType: 'json',
    beforeSend: function () {
      showTable();
    },
    success: function (data) {
      $('#bulk-actions-users-table > tbody').html(data.users);
      $('#bulk-actions-footer').show();
      $('#table-pager').html(data.pager);
    },
    error: function (xhr, status) {
      console.log('error in getting users');
      $('#reset-pw-users-table > tbody').html('<tr><td>There was a problem getting user records.</td></tr>');
    }
  });
}

function displaySelectedSchool (text) {
  $('#school-select-display').html(text);
  $('#school-select-display').show();
}

function hideSelectedSchool () {
  $('#school-select-display').html('');
  $('#school-select-display').hide();
}

function showRemoveSchoolBtn () {
  $('#school-select-remove').show();
}

function hideRemoveSchoolBtn () {
  $('#school-select-remove').hide();
}
