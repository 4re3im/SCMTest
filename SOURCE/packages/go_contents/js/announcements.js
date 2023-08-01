$(document).ready(function() {
  if($('input[id="enableDefault"]').is(":checked")) {
    $(".default").toggle();
  }
  
  $('input[id="enableDefault"]').click(function() {
      $(".default").toggle();
  });

  $('#ccm-submit-save').click(function(e) {
    e.preventDefault();
    var allCountries = []
    var properCountry = []
    var wrongCountry = []
    var formData = $('#announce-mode-form').serialize()
    var currentCountry = $('#countryArray').val().trim().split(', ')

    $("#country option").each(function() {
      allCountries.push($(this).val())
    })
    
    currentCountry.forEach(function(country) {
      if (allCountries.includes(country) && country !== "") {
        properCountry.push(country)
      } else if (country === '') {
        properCountry.push('ALL')
      } else {
        wrongCountry.push(country)
      }
    })

    if (wrongCountry.length >= 1) {
      showError('Country ' + wrongCountry.toString() + ' is not a proper ISO code')
      return
    }

    $.ajax({
      type : 'POST',
      url : $('#announce-mode-form').attr('action'),
      data : formData,
      success: function () {
        location.reload()
      }
    })
  })

  $('#addCountry').click(function() {
    var existingValue = $('#countryArray').val();
    var selectedCountryToAdd = $('#country').val();

    if (selectedCountryToAdd === 'ALL') {
      $('#countryArray').val('ALL');
    } else {
      if (existingValue === 'ALL') {
        $('#countryArray').val(selectedCountryToAdd)
      } else {
        if (!existingValue.includes(selectedCountryToAdd)) {
          existingValue = existingValue + ', ' + selectedCountryToAdd
          $('#countryArray').val(existingValue)
        } else {
          showError('Country ' + selectedCountryToAdd + ' is already added')
          return
        }
      }
    }
  })

  function showError(message) {
    $('#error-message').html(message)
    $('#error').fadeIn('fast')
    setTimeout(function() {
      $('#error').fadeOut('fast')
    }, 5000)
  }

});
