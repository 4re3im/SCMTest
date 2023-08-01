var modalConfig = {
  width: '50%',
  height: '50%',
  appendButtons: true,
  modal: true,
  title: 'Modal Title'
};

var formOptions = {
  resetForm: true,
  dataType: 'json',
  beforeSubmit: validateFile
};

var FILE_RECORD_ID,
  SCHEDULE_ID,
  JOB_ID,
  BASE_URL,
  TOTAL_JOBS,
  SCHOOL_SEARCH_URL,
  SCHOOL_DATA;

$(document).on('ready', function () {
  $('#cancel-btn').click(function (e) {
    e.preventDefault();
    hideModal();
    clearFileInput();
    hideProgressBar();
  });

  // PAGER BUTTONS
  $(document).on('click', '.numbers > a', function (e) {
    e.preventDefault();
    if ($(this).parent('li').hasClass('disabled')) {
      return false;
    }
    var page = $(this).attr('data-page');
    getUsers(page);
  });

  $(document).on('click', '.prev > a', function (e) {
    e.preventDefault();
    if ($(this).hasClass('disabled')) {
      return false;
    }

    $('li.active').prev('li').children('a').click();
  });

  $(document).on('click', '.next > a', function (e) {
    e.preventDefault();
    if ($(this).hasClass('disabled')) {
      return false;
    }

    $('li.active').next('li').children('a').click();
  });
  // END OF PAGER BUTTONS
});

// MODAL FUNCTIONS
function showModal () {
  $.fn.dialog.open(modalConfig);
}

function hideModal () {
  jQuery.fn.dialog.closeTop();
  resetModalConfig();
}

function resetModalConfig () {
  modalConfig = {
    width: '50%',
    height: '50%',
    appendButtons: true,
    modal: true,
    title: 'Modal Title',
    element: ''
  };
}

// FORM FUNCTIONS
function clearFileInput () {
  $('#bulk-actions-file').val('');
}

function validateFile (formData) {
  showProgressBar();
  var acceptedFiles = ['xlsx', 'xls'];
  var file = formData[0];
  var fileName = file.value.name;
  var type = fileName.split('.')[1];
  if (!acceptedFiles.includes(type)) {
    showNotification('warning', 'Invalid file type. Please upload ~.xlsx or ~.xls files.');
    hideModal();
    hideProgressBar();
  }
}

// NOTIFICATIONS FUNCTIONS
function showNotification (type, msg, duration) {
  var alert = $('#alert-box');
  $('#alert-msg').html(msg);
  alert.addClass('alert-' + type);
  alert.show();
}

function hideNotification () {
  var alert = $('#alert-box');
  $('#alert-msg').html('');
  alert.hide();
}

// PROGRESS BAR FUNCTIONS
function setProgressText (text) {
  $('#progress-label').html(text);
}

function showProgressBar () {
  $('#bulk-actions-progress-bar').show();
}

function hideProgressBar () {
  $('#bulk-actions-progress-bar').hide();
}

function setProgressWidth (number) {
  if (number > TOTAL_JOBS) {
    number = TOTAL_JOBS;
  }

  var percent = (number / TOTAL_JOBS) * 100;
  $('.progress > .bar').width(percent + '%');
}

// USER TABLE FUNCTIONS
function showTable () {
  $('#bulk-actions-users-div').show();
}

function hideTable () {
  $('#bulk-actions-users-div').hide();
}

// SCHOOL SEARCH
function enableInstitutionField () {
  $('#bulk-actions-institutions').removeClass('disabled');
}

function disableInstitutionField () {
  $('#bulk-actions-institutions').addClass('disabled');
}

// FILE INPUT
function enableFileUpload () {
  $('#bulk-actions-file').removeClass('disabled');
}

function disableFileUpload () {
  $('#bulk-actions-file').addClass('disabled');
}