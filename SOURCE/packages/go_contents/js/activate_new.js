$(document).ready(function() {
  // Paste and Propagate to 4 Input Boxes
  $(".accesscodetext").bind('paste', function (e) {
    var $this = $(this);
    var originalValue = $this.val();

    $this.one("input", function(){
      var $currentInputBox = $(this);

      var pastedValue = $currentInputBox.val();
      // SB-391 updated by mabrigos 20191127
      if (pastedValue.length === 19) {
          pasteValues(pastedValue, 'accesscodetext');
      } else {
          $this.val(originalValue);
      }

        $this.attr("maxlength", 4);
    });
      $this.attr("maxlength", 19);
  });
  // ANZGO-3694 Added by Maryjes Tanada 04/19/2018 Automatically tab to next input field
  $(".accesscodetext").on("input", function() {
    if ($(this).val().length == $(this).attr("maxlength")) {
      $(this).closest('div').next().find(':input').first().focus();
    }
  });
});

// Parses the accesscode and split it into the individual boxes.
function pasteValues(element, className) {
  var values = element.split("-");

  $(values).each(function(index) {
    // SB-391 Updated by mabrigos for dynamic paste values
    if (className === 'accesscodetext') {
      var $inputBox = $('.accesscodetext[name="accesscode_s1dummy[' + (index + 1) + ']"]');
    } else {
      var $inputBox = $('.' + className +'[name="'+ className +'[' + (index + 1) + ']"]');
    }
    $inputBox.val(values[index])
  });
};

// submit activation form
// ANZGO-3495 Modified by Shane Camus 9/22/2017
$('#dummyActivator').click(function(e) {
  var $ac1 = $('#accesscode_s1dummy').val();
  var $ac2 = $('#accesscode_s2dummy').val();
  var $ac3 = $('#accesscode_s3dummy').val();
  var $ac4 = $('#accesscode_s4dummy').val();

  var $rc1 = $('#reactivationCode1').val();
  var $rc2 = $('#reactivationCode2').val();
  var $rc3 = $('#reactivationCode3').val();
  var $rc4 = $('#reactivationCode4').val();

  var $pac1 = $('#printAccessCode1').val();
  var $pac2 = $('#printAccessCode2').val();
  var $pac3 = $('#printAccessCode3').val();
  var $pac4 = $('#printAccessCode4').val();

  var $reactivationCode = $('#reactivationCode').val(); // ANZGO-3853 added by mtanada 20180905
  var $printAccessCode = $('#printAccessCode').val(); // ANZGO-3854 added by jbernardez 20180912

  // First check if activate button is clicked without entering any value
  if ( $ac1 === '' &&  $ac2 === '' && $ac3 === '' && $ac4 === '') {
    var $message = "<div class='alert alert-danger animated bounce alert-dismissible' role='alert'>" +
        "You must enter an access code to continue." +
        "</div>";

    $('#error-message').css("display", "block");
    $('input[type=text]').addClass('field-error');
    $('#error-message').html($message);

    e.preventDefault();
    return false;
  }

  if (typeof $rc1 !== 'undefined' && typeof $rc2 !== 'undefined' && typeof $rc3 !== 'undefined' && 
    typeof $rc4 !== 'undefined') {
    var $reactivationCode = $.trim($rc1) + '-' + $.trim($rc2) + '-' + $.trim($rc3) + '-' + $.trim($rc4);
  }

  if (typeof $pac1 !== 'undefined' && typeof $pac2 !== 'undefined' && typeof $pac3 !== 'undefined' && 
    typeof $pac4 !== 'undefined') {
    var $printAccessCode = $.trim($pac1) + '-' + $.trim($pac2) + '-' + $.trim($pac3) + '-' + $.trim($pac4);
  }

  var $combinedAccessCode = $.trim($ac1) + '-' + $.trim($ac2 ) + '-' + $.trim($ac3) + '-' + $.trim($ac4);
  var $terms = $('#accept').is(':checked');

  $.post( "/activate/activateProduct/", {
    accessCode: $combinedAccessCode,
    terms: $terms,
    reactivationCode: $reactivationCode, // ANZGO-3853 added by mtanada 20180905
    printAccessCode: $printAccessCode // ANZGO-3854 added by jbernardez 20180912
  }, function (data) {
      if (!data.success) {
        // ANZGO-3854 modified by jbernardez 20180912
        if (data.alertInfo) {
          $message = "<div class='alert alert-info animated bounce alert-dismissible' " +
              "role='alert' id='login-error' style='margin-top:20px'>";
        } else {
          $message = "<div class='alert alert-danger animated bounce alert-dismissible' " +
              "role='alert' id='login-error'>";
        }
        $message += data.message;
        $message += "</div>";

        $('#error-message').html($message);
        $('#error-message').css("display", "block");

        if (data.action === 'login') {
          setTimeout(function() {
            // ANZGO-3913 modified by jbernardez 20181108
            // will do a redirect as we have updated the login page
            window.location.href = "/go/login/";
          }, 5000);
        }
        e.preventDefault();
        return false;
      } else {
        $message = "<div class='alert alert-success animated bounce alert-dismissible' " +
            "role='alert' id='login-error'>";
        $message += data.message;
        $message += "</div>";

        $('input[type=text]').css("display", "none");
        //<!-- ANZGO-3759 added by mtanada 20180712 -->
        $('#accesscode-refresh').css("display", "none");

        $('#activated').css("display", "block");
        $('#activated').addClass("animated bounceInDown long");

        $('#error-message').html($message);
        $('#error-message').css("display", "block");

        if (data.action === 'activate-TNGProduct') {
          setTimeout(function () {
            window.location = "/go/myresources/";
          }, 5000);
        }
      }

  }, "json")
  .fail(function(xhr,status,err){
    $('#newactivateform').html(xhr.responseText);
  });

  e.preventDefault();

});

// ANZGO-3789 added by jbernardez 20180706
$(document).on("click", "#front-deactivate", function (e) {
  e.preventDefault();
  e.stopPropagation();
  $(".announce-info").show();
});

// SB-254 added by jbernardez 20190919
function displayWindowSize(){
  var width = document.documentElement.clientWidth;

  if (width < 500) {
      $('#magicBR').show();
  } else {
      $('#magicBR').hide();
  }
}

// SB-391 added by mabrigos for additional input boxes
$(document).on('click', '#reactivation-refresh', function (e) {
  $('.reactivationcode').each(function (i, element) {
      $(element).val('');
  });
});

$(document).on('click', '#printAccessCode-refresh', function (e) {
  $('.printAccessCode').each(function (i, element) {
      $(element).val('');
  });
});

$(document).on('paste', '.reactivationcode', function (e) {
  var $this = $(this);
  var originalValue = $this.val();

  $this.one('input', function() {
      var $currentInputBox = $(this);
      var pastedValue = $currentInputBox.val();

      if (pastedValue.length === 19) {
          pasteValues(pastedValue, 'reactivationcode');
      } else {
          $this.val(originalValue);
      }

      $this.attr('maxlength', 4);
  });

  $this.attr('maxlength', 19);
});

$(document).on('paste', '.printAccessCode', function (e) {
  var $this = $(this);
  var originalValue = $this.val();

  $this.one('input', function() {
      var $currentInputBox = $(this);
      var pastedValue = $currentInputBox.val();

      if (pastedValue.length === 19) {
          pasteValues(pastedValue, 'printAccessCode');
      } else {
          $this.val(originalValue);
      }

      $this.attr('maxlength', 4);
  });

  $this.attr('maxlength', 19);
});

$(document).on('input', '.reactivationcode', function() {
  if ($(this).val().length == $(this).attr('maxlength')) {
      $(this).closest('div').next().find(':input').first().focus();
  }
});

$(document).on('input', '.printAccessCode', function() {
  if ($(this).val().length == $(this).attr('maxlength')) {
      $(this).closest('div').next().find(':input').first().focus();
  }
});

window.addEventListener("resize", displayWindowSize);
displayWindowSize();
