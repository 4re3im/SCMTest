BASE_URL = '/dashboard/provisioning/reset_password';
TOTAL_JOBS = 5;

var RESULT_FILE_NAME;

$(document).on('ready', function () {
  // FILE INPUT
  $('#bulk-actions-file').change(function (e) {
    var fileName = e.target.files[0].name
    $('#bulk-delete-file-name').html(fileName)
    jQuery.fn.dialog.showLoader()
    modalConfig.height = '15%'
    modalConfig.element = '#confirmation-modal-content'
    modalConfig.title = 'File upload'
    showModal();
  });

  // MODAL BUTTONS
  $('#cancel-btn, #duplicate-pw-cancel-btn').click(function (e) {
    e.preventDefault();
    hideModal();
    clearFileInput();
    hideProgressBar();
  });

  $('#proceed-btn').click(function (e) {
    formOptions['success'] = showResponseAfterUpload

  $('#download-btn').click(function (e) {
    hideModal();
  });

    $('#bulk-actions-form').ajaxSubmit(formOptions);
    hideModal();
  });

  $('#duplicate-pw-proceed-btn').click(function (e) {
    e.preventDefault();
    validateFileContents(true);
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
});

function showResponseAfterUpload (response) {
  if (response.Status === 5) { // Failed upload
    showNotification('warning', response.Message);
    hideProgressBar();
  } else {
    FILE_RECORD_ID = response.FileRecordID;
    setProgressText(response.Message);
    validateFileContents();
  }
}

function validateFileContents (userHasBeenReminded, page) {
  var currentUrl = BASE_URL + '/validateFileContents/' + FILE_RECORD_ID;

  if (userHasBeenReminded) {
    currentUrl += '/1'
  }

  $.ajax({
    url: currentUrl,
    type: 'GET',
    dataType: 'json',
    beforeSend: function () {
      setProgressText('Validating file contents...');
      setProgressWidth(1);
    },
    success: function (data) {
      if (data.Status === 9) { // Duplicate password used
        modalConfig.height = '10%';
        modalConfig.element = '#duplicate-password-modal-content';
        modalConfig.title = 'Common password detected';
        showModal();
        return false;
      } else if (data.Status === 10) { // Different domain in email
        showNotification('warning', data.Message);
        hideProgressBar();
        return false;
      }
      getUsers();
      runBulkPasswordReset();
    },
    error: function (xhr, status, err) {
      console.log('error 1');
    }
  });
}

function runBulkPasswordReset () {
  $.ajax({
    url: BASE_URL + '/runBulkPasswordReset/' + FILE_RECORD_ID,
    dataType: 'json',
    beforeSend: function () {
      setProgressText('Running bulk password reset...');
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
      console.log('error 3');
    }
  });

  // SB-613 added by jbernardez 20200629
  runBulkPasswordResetC5(0);
}

function runBulkPasswordResetC5 (index) {
  $.ajax({
    url: BASE_URL + '/runBulkPasswordResetC5/' + FILE_RECORD_ID + '/' + index,
    dataType: 'json',
    success: function (data) {
      var index = JSON.parse(data.Data).index;
      if (data.IsFinished === 0) {
        runBulkPasswordResetC5(index);
      } else if (data.IsFinished === 1) {
        console.log('update password done')
      }
    },
    error: function (xhr, status, err) {
      console.log('error bulk');
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
      var responseData = JSON.parse(data.Data)
      if (responseData.status === 'running' || responseData.status === 'pending') {
        getJobStatus();
      } else if (responseData.status === 'failed') {
        showNotification('warning', 'Bulk reset password failed.');
        hideProgressBar();
      } else  {
        JOB_ID = responseData.jobID
        setProgressText('Gigya Bulk Reset password finished...');
        removeTempPasswords();
      }
    },
    error: function (xhr, status, err) {
      console.log('error 4');
    }
  });
}

function removeTempPasswords () {
  $.ajax({
    url: BASE_URL + '/removeTempPasswords/' + FILE_RECORD_ID,
    beforeSend: function () {
      setProgressText('Finishing up...');
      setProgressWidth(4);
    },
    success: function () {
      setProgressText('Bulk password reset done...');
      getUserStatusFromS3();
    },
    error: function (xhr, status, err) {
      console.log('error 5');
    }
  });
}

function getUserStatusFromS3 () {
  $.ajax({
    url: BASE_URL + '/getUserStatusFromS3/' + JOB_ID + '/' + FILE_RECORD_ID,
    dataType: 'json',
    beforeSend: function () {
      setProgressText('Getting user status from Gigya...');
      setProgressWidth(5);
    },
    success: function (data) {
      if (data.Status === 6) { // Gigya error
        showNotification('warning', data.Message);
        hideProgressBar();
      } else if (data.Status === 12) { //DONE
        getUsers();
        hideProgressBar();
        showNotification('success', 'Users\' status updated');
        saveResultsToS3();
      }
    },
    error: function (xhr, status, err) {
      console.log('error 6');
    }
  });
}

function saveResultsToS3 () {
  $.ajax({
    url: BASE_URL + '/saveResultsToS3/' + FILE_RECORD_ID,
    data: {
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
        showDownloadModalTrigger();
      } else {
        showNotification('success', data.Message);
        hideProgressBar();
        RESULT_FILE_NAME = JSON.parse(data.Data).resultsFilename;
        getUsers();
        showDownloadModalTrigger();
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

  $('#reset-passwrd-result-file-name').html('<strong>' + RESULT_FILE_NAME + '</strong>');
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
      $('#bulk-actions-users-div').show();
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
