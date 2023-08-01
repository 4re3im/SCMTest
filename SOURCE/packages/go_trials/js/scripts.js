$(document).ready(function () {
  var autoCompleteSource = $('#search-box').attr('search-url')
  $('#search-box').autocomplete({
    delay: 800,
    minLength: 3,
    source: autoCompleteSource,
    select: function (event, ui) {
      $('#trials-table').show()
      if (ui.item.value !== 0) {
        var html = '<tr>';
        html += '<td class=\'subs-name\'>' + ui.item.label
        html += '<input type=\'hidden\' name=\'name[]\' id=\'name[]\' value=\'' + ui.item.name + '\' class=\'subs-ids\' />'
        html += '<input type=\'hidden\' name=\'entitlementIds[]\' id=\'entitlementIds[]\' value=\'' + ui.item.id + '\' class=\'subs-ids\' />'
        html += '</td>'
        html += '<td><a href=\'#\' class=\'btn btn-small btn-default remove-trial\' title=\'Remove Trial\'>'
        html += '<i class=\'icon-trash\'></i></a></td>'
        html += '</tr>'

        $('#trials-table > tbody').append(html);
      }
      $(this).val('')
      return false
    }
  });

  $(document).on('click', '.remove-trial', function () {
    $(this).closest('tr').remove()
    if ($('#trials-table').find('tbody').children('tr').length < 1) {
      $('#trials-table').hide()
    }
  });

  $('#trials-form').submit(function (event) {
    if (checkInputs()) {
      $("#exportBtn").prop('disabled', false);
      fetchActivations()
      $("#generateReport").prop('disabled', true)
      event.preventDefault();
    }
    return false
  });

  $('#exportBtn').click(function (event) {
    $("#exportBtn").prop('disabled', true);
    window.location = window.location.origin + '/dashboard/trials/report/exportToCSV'
    setTimeout(function () {
      $('#generating-progress').fadeOut('slow')
      $('#exportBtn').addClass('invisible')
      resetForm()
    }, 4000);
  })

  function showErrorInfo (info, element) {
    var alert = $('.alert');
      $(alert).html(info);
      (alert).addClass('alert-danger');
      (alert).show()

      $(element).focus()
      setInterval(function () {
        $('.alert').hide()
      }, 5000)
  }

  var activationsTime = 0;
  var gigyaQueryTime = 0;

  function fetchActivations() {
    var form = $('#trials-form');
    var postData = $(form).serialize();

    $.ajax({
      type: 'POST',
      url: 'fetchActivations',
      data: postData,
      dataType: 'json',
      start_time: new Date().getTime(),
      beforeSend: function() {
        $(document.body).css({'cursor' : 'wait'})
        updateProgressBar(33, 'Fetching Activations from PEAS')
      },
      success: function(data) {
        if (data.success) {
          activationsTime = (new Date().getTime() - this.start_time) / 1000
          console.log('Activations fetched in ' + activationsTime + 's')
          updateProgressBar(33, 'Activations fetched in ' + activationsTime + 's')
          queryGigya(data.activations)
        } else {
          updateProgressBar(100, data.message)
          $(document.body).css({'cursor' : 'default'})
          setTimeout(function () {
            $('#generating-progress').fadeOut('slow')
          }, 4000);
          resetForm()
        }
      },
      error: function(data) {
        console.log(data)
        updateProgressBar(100, 'There was an error in fetching activations in PEAS')
      }
    })
  }

  function queryGigya(data) {
    var formData = {
      activations: data,
      country: $('#country').val()
    }

    $.ajax({
      type: 'POST',
      url: 'fetchGigyaDetails',
      data: formData,
      dataType: 'json',
      start_time: new Date().getTime(),
      success: function(data) {
        if (data.success) {
          gigyaQueryTime = (new Date().getTime() - this.start_time) / 1000
          console.log('Gigya request took '+ gigyaQueryTime +'s')
          updateProgressBar(66, 'Gigya request took '+ gigyaQueryTime +'s')
          showCSVButton(data.count)
        } else {
          updateProgressBar(100, data.message)
          $(document.body).css({'cursor' : 'default'})
          setTimeout(function () {
            $('#generating-progress').fadeOut('slow')
          }, 4000);
          resetForm()
        }
      },
      error: function(data) {
        console.log(data)
        updateProgressBar(100, 'There was an error in querying gigya')
      }
    })
  }

  function showCSVButton(activationsCount) {
    $('#exportBtn').removeClass('invisible')
    $(document.body).css({'cursor' : 'default'})
    updateProgressBar(
      100,
      "CSV ready to download. ("+ activationsCount +" activations) Total process time is " +
        (activationsTime + gigyaQueryTime).toFixed(3) +
        " seconds"
    )
    console.log(
      "CSV ready to download. ("+ activationsCount +" activations) Total process time is " +
        (activationsTime + gigyaQueryTime).toFixed(3) +
        " seconds"
    );
  }

  function updateProgressBar(step, message) {
    $('#generating-progress').removeClass('invisible')
    $('#generating-progress').fadeIn('slow')
    var progressPercentage = step + '%'
    var progressBar = $('#generating-progress')
    $(progressBar).find('.bar').animate({ width: progressPercentage }).text(message)
  }

  function resetForm()  {
    $("#generateReport").prop('disabled', false)
    var trialsTable = $("#trials-table")
    $("#resetForm").click()
    $(trialsTable).find("tbody").children("tr").remove()
    $(trialsTable).hide()
  }

  function checkInputs() {
    if ($("input[id='entitlementIds[]']").length === 0) {
      showErrorInfo('Please select atleast 1 trial subscription', '#search-box')
      return false
    }

    if ($('#start').val() == '' && $('#end').val() !== '') {
      showErrorInfo('Please select FROM date', '#start')
      return false
    }

    if ($('#end').val() == '' && $('#start').val() !== '') {
      showErrorInfo('Please select TO date', '#end')
      return false
    }

    if (
      $("#end").val() !== "" &&
      $("#start").val() !== "" &&
      Date.parse($("#start").val()) > Date.parse($("#end").val())
    ) {
      showErrorInfo("Please make sure From and To dates are correct", "#start")
      return false
    }

    if ($('#daysRemaining').val() !== '' && $('#daysRemaining').val() < 0) {
      showErrorInfo("Please make sure days remaining is valid", "#daysRemaining")
      return false
    }

    return true
  }

})