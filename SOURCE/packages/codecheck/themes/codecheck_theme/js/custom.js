$('.close').click(function () {
  $(this).parent().removeClass('in'); // hides alert with Bootstrap CSS3 implem
});

$('.modal-transparent').on('show.bs.modal', function () {
  setTimeout(function () {
    $('.modal-backdrop').addClass('modal-backdrop-transparent');
  }, 0);
});

$('.modal-transparent').on('hidden.bs.modal', function () {
  $('.modal-backdrop').addClass('modal-backdrop-transparent');
});

$('.modal-fullscreen').on('show.bs.modal', function () {
  setTimeout(function () {
    $('.modal-backdrop').addClass('modal-backdrop-fullscreen');
  }, 0);
});

$('.modal-fullscreen').on('hidden.bs.modal', function () {
  $('.modal-backdrop').addClass('modal-backdrop-fullscreen');
});

function delay (URL) {
  setTimeout(function () { window.location = URL; }, 1500);
}

$('#accessCodeForm').on('submit', function (e) {
  e.preventDefault();

  resetMessages();

  if (grecaptcha.getResponse() === '') {
    $('.g-recaptcha div div').css('height', '100%');
    $('.g-recaptcha div div').css('border', '1px solid #f30000');
    $('#recaptchaError').removeAttr('hidden');
    $('#recaptchaError').addClass('in');
    return false;
  }

  $.ajax({
    url: '/codecheck/verifyAccessCode/',
    type: 'POST',
    dataType: 'json',
    data: $(this).serialize(),
    async: false,
    error: function (xhr, status, error) {
    },
    success: function (data) {
      data.message = data.message.replace('h6', 'small');
      if (data.success) {
        showMessage(data.message);
      } else {
        showGeneralError(data.message);
      }
    }
  });
});

$('.access-code-input').on('paste', function (e) {
  $('.access-code-input').each(function (i, element) {
    $(element).val('');
  });

  var pastedValue = e.originalEvent.clipboardData.getData('text').split('-');
  $('.access-code-input').each(function (i, element) {
    $(element).val(pastedValue[i]);
  });

  resetMessages();
});

$('.access-code-input').on('input', function () {
  resetMessages();
});

function recaptchaCallback (data) {
  $('.g-recaptcha div div').css('height', '100%');
  $('.g-recaptcha div div').css('border', '1px solid #D3D8D3');
  $('#recaptchaError').attr('hidden', true);
  $('#recaptchaError').removeClass('in');
  resetMessages();
}

function showMessage (message) {
  $('#activated').html(message);
  $('#displayAlert').addClass('in');
  $('#activated').addClass('in');
  $('#activated-icon').addClass('in');
  $('.field-remove').removeClass('in');
  $('.field-remove').css('display', 'none');
}

function showGeneralError (message) {
  $('#generalError').html($('<p></p>').html(message));
  $('#generalError').addClass('in');
  $('#generalError').attr('hidden', false);
  $('.access-code-input').addClass('field-error');
}

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

// ANZGO-3556 added by jbernardez 20171026
$('#accesscode-refresh').on('click', function (e) {
  $('.access-code-input').each(function (i, element) {
    $(element).val('');
  });

  resetMessages();
});

// SB-391 Added by mabrigos for dynamic paste values
// Parses the accesscode and split it into the individual boxes.
function pasteValues(element, className) {
  var values = element.split("-");
  $(values).each(function(index) {
    var $inputBox = $('.' + className +'[name="'+ className +'[' + (index + 1) + ']"]');
    $inputBox.val(values[index])
  });
};

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

// SB-391 added by mabrigos for additional input boxes
$(document).on('click', '#reactivation-refresh', function (e) {
  $('.reactivationcode').each(function (i, element) {
      $(element).val('');
  });
});

$(document).on('input', '.reactivationcode', function() {
  if ($(this).val().length == $(this).attr('maxlength')) {
      $(this).closest('div').next().find(':input').first().focus();
  }
});