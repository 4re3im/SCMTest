/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
// SB-18 modified by machua 20190125 removed dependency for the chatbot
$(document).ready (function () {
  // Paste and Propage to 4 Input Boxes
  $(".accesscodetext").on('paste', function (e) {
    var $this = $(this);
    // ANZGO-3084
    // make sure to make all fields blank before the pasting of new values
    $('#accesscode_box1').val('');
    $('#accesscode_box2').val('');
    $('#accesscode_box3').val('');
    $('#accesscode_box4').val('');

    $this.on("input", function () {
      $currentInputBox = $(this);
      var pastedValue = $currentInputBox.val();
      // SB-391 updated by mabrigos 20191120
      if (pastedValue.length == 19) {
        pasteValues(pastedValue, 'accesscodetext');
      }

      $this.attr("maxlength", 4);
    });

    $this.attr("maxlength", 19);
  });

  $('#activate').off().on('click', function (e) {
    if ($('#accesscode_box1').val() == '' || $('#accesscode_box2').val() == '' ||
       $('#accesscode_box3').val() == '' || $('#accesscode_box4').val() == '') {
      formatMessaging('Please enter an access code.', true);
    } else if ($('#activate_checkbox').is(':checked')) {
      $('#activate').attr('disabled', 'disabled');

      // SB-391 added by mabrigos 20191120
      var $rc1 = $('#reactivationCode1').val();
      var $rc2 = $('#reactivationCode2').val();
      var $rc3 = $('#reactivationCode3').val();
      var $rc4 = $('#reactivationCode4').val();

      var $pac1 = $('#printAccessCode1').val();
      var $pac2 = $('#printAccessCode2').val();
      var $pac3 = $('#printAccessCode3').val();
      var $pac4 = $('#printAccessCode4').val();

      var reactivationCode = false;
      var printAccessCode = false;

      var combinedAccessCode = $('#accesscode_box1').val() + '-' + $('#accesscode_box2').val() + '-' +
          $('#accesscode_box3').val() + '-' + $('#accesscode_box4').val();

      if (typeof $rc1 !== 'undefined' && typeof $rc2 !== 'undefined' && typeof $rc3 !== 'undefined' &&
        typeof $rc4 !== 'undefined') {
        var reactivationCode = $.trim($rc1) + '-' + $.trim($rc2) + '-' + $.trim($rc3) + '-' + $.trim($rc4);
      }

      if (typeof $pac1 !== 'undefined' && typeof $pac2 !== 'undefined' && typeof $pac3 !== 'undefined' &&
        typeof $pac4 !== 'undefined') {
        var printAccessCode = $.trim($pac1) + '-' + $.trim($pac2) + '-' + $.trim($pac3) + '-' + $.trim($pac4);
      }
      /* ANZGO-3326 Modified by John Renzo S. Sunico, 04.21.2017
       * Adjusting this to follow JSON Response in backend.
       *
       * ANZGO-3497 Modified by John Renzo S. Sunico, September 07, 2017
       * Updating error messages
       *
       * ANZGO-3527 Modified by John Renzo S. Sunico, October 06, 2017
       * Added Option A Your education bookseller in invalid Edjin products
       */

      // ANZGO-3853 added by mtanada 20180906
      var urlAccessCode = '';
      if (reactivationCode) {
        urlAccessCode = $('#activate_code_url').val() + combinedAccessCode + '/' + reactivationCode; 
      } else {
        urlAccessCode = $('#activate_code_url').val() + combinedAccessCode;
      }

      // ANZGO-3854 added by jbernardez 20180913
      if (printAccessCode) {
        urlAccessCode = $('#activate_code_url').val() + combinedAccessCode + '/printAccessCode/' + printAccessCode;
      }
      
      $.ajax({
        url: urlAccessCode,
        success: function (data) {
          if (!data.success){

            // ANZGO-3854 Modified by jbernardez 20180913
            // SB-391 updated by mabrigos 20191120
            if (data.alertInfo) {
              formatMessaging(data.message, true, data.alertInfo, data.isPrintAccess);
            } else {
              formatMessaging(data.message, true, null, data.isPrintAccess);
            }
            $('#activate').removeAttr('disabled');

            e.preventDefault();
            return false;
          } else {
            // ANZGO-3617 added by jbernardez 20180123
            // used in case that this is not a TNG Product
            // just display message, like EM/SM/HM codes
            if ((data.action === 'can-activate-SM') ||
                (data.action === 'can-activate-EM') ||
                (data.action === 'can-activate-HM')) {

              $('#activate').removeAttr('disabled');
              $('#activate span').removeClass('glyphicon glyphicon-refresh spinning');

              formatMessaging(data.message);
              e.preventDefault();
              return false;
            }

            var currentLoc = window.location.pathname;
            var myResourcesLink = $('#myresources_url').val();

            if (currentLoc == myResourcesLink) {
              // ANZGO-3869 modified by mtanada 20181010
              // Remove toggle close with the new header and replace with hideOverly
              hideOverlay(mainContent);

              if ($('#user-resources').find('#noresource').length > 0) {
                $('#user-resources').empty();
              }

              // ANZGO-3383 Modified by John Renzo Sunico, May 12, 2017
              // Remove previous title that exist
              $.each(data.subscriptions, function (index, value) {
                $('#' + index).remove();
              });

              $('#user-resources').prepend(data.title);
              mainContent = $("#main-content .container-fluid").html();
            } else {
              location.href = $('#myresources_url').val();
            }
            $('#activate').removeAttr('disabled');
          }
          // ANZGO-3943 modified by mtanada 20181203 Success activation
          var myresourcesURL = $('#myresources_url').val();
          window.location.replace(myresourcesURL);
        }
      });
    } else {
      formatMessaging('You need to accept Terms of use.', true);
    }
    return false;
  });
});

// Parses the accesscode and split it into the individual boxes.
// SB-391 updated by mabrigos 20191120
function pasteValues(element, className) {
  var values = element.split("-");

  $(values).each(function (index) {
    if (className === 'accesscodetext') {
      var $inputBox = $('.accesscodetext[name="accesscode_box[' + (index + 1) + ']"]');
    } else {
      var $inputBox = $('.' + className +'[name="'+ className +'[' + (index + 1) + ']"]');
    }
    $inputBox.val(values[index]);
  });
};

/* ANZGO-3534 Modified by Shane Camus 9/27/2017
* Modified since Safari v9 does not accommodate optional parameters
* ANZGO-3854 modified by jbernardez 20180913
* ANZGO-3943 modified by mtanada 20181203
*/
function formatMessaging(str, danger, info, printCode) {
  // SB-18 modified by machua 20190125 IE does not support default parameter value
  if (info === undefined) {
    info = null;
  }

  var className = (danger) ? 'informational-message' : 'success-message';
  // SB-391 modified by mabrigos to format printcode block
  if (info || printCode) {
    className = 'alert-info';
  }

  var message = '<div id="message-wrapper" class="' + className + ' go-message-wrapper">\n\
        <span class="message-icon"></span><p>' + str + '</p></div>';

  $("#messaging").fadeOut(500, function () {
    $(this).html(message);
    $(this).fadeIn(500);
  });

  return;
}

// ANZGO-3759 added by mtanada 20180712
function refresh()
{
  $('.accesscodetext').each(function (i, element) {
    $(element).val('');
  });
  resetMessages();
}

// SB-18 added by machua 20190125 cannot find the reference because custom.js is not loaded
function resetMessages () {
  $('#displayAlert').removeClass('in');
  $('#activated').removeClass('in');
  $('#activated-icon').removeClass('in');
  $('.field-remove').addClass('in');

  $('#generalError').removeClass('in');
  $('#generalError').attr('hidden', true);

  $('#recaptchaError').attr('hidden', true);
  $('#recaptchaError').removeClass('in');

  $('.access-code-input').removeClass('field-error');
}

// SB-391 added by mabrigos for additional input boxes
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