/**
 * SB-674 Added by mtanada 20200902
 */
$(document).ready(function () {

  window.onload = function() {
    if (window.location.href.indexOf('/job') > -1) {
      jobHeaders();
      loadJobs(1);
    }
  }

  if (window.location.href.indexOf('/job/displayJobs/*/') > -1) {
    jobHeaders();
    loadJobs();
  }

  $(document).on('click', '.job-page', function (e) {
    var url = $(this).attr('href');
    loadJobs(url.slice(-2, -1));
  });

  $(document).on('click', '.refreshJob', function (e) {
    loadJobs(1);
  });

});

function loadJobs (page) {
  addAjax({
    url: '/dashboard/global_go_provisioning/job/displayJobs/'+ page ,
    type: 'GET',
    dataType: 'json',
    success: function (data) {
      updateJobTable(data, true);
    },
    error: error
  });
}


// SB-674 Added by mtanada 20200902
var jobHeaders = function () {
  $('#jobs-table').show();
}

// SB-674 Added by mtanada 20200902
var updateJobTable = function (data, updatePagination) {
  $('#jobs-table-body').html(data.jobs);
  $('#job-pagination').html(data.pagination);
  if (updatePagination === true) {
    $('#job-pagination').html(data.pagination);
  }
}
