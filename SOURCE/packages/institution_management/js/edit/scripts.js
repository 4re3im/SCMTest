(function ($) {
  var editApp = {
    init: function () {
      editApp.FORM = $('#app-edit-form');
      editApp.FORM_BOX = $('#app-institute-form-div');
      editApp.NOTIF = $('#app-notification');
      editApp.NOTIF_BOX = $('#app-notification-box');
      editApp.PROGRESS_BAR = $('#app-notification-progress-bar');
      editApp.PROGRESS_BAR_LABEL = $('#progress-label');
      editApp.OID = $('#app-institution-id').val();
    },

    processForm: function () {
      $.ajax({
        url: $(editApp.FORM).attr('action'),
        type: 'POST',
        data: $(editApp.FORM).serialize(),
        dataType: 'json',
        beforeSend: function () {
          editApp.disableForm();
          jQuery.fn.dialog.showLoader();
        },
        success: function (d) {
          if (!d.success) {
            editApp.showNotification('error', d.message);
            return;
          }
          editApp.showNotification(
            'info',
            'Your request has been submitted. Dataflows for this feature are scheduled to run every 10 minutes. Please check back in 10-15 minutes',
            10000
          );
        },
        error: function () {
          editApp.showNotification('error', 'There was an error submitting the form.');
          editApp.enableForm();
        },
        complete: function () {
          jQuery.fn.dialog.hideLoader();
          editApp.enableForm();
        }
      });
    },

    disableForm: function () {
      $('#app-edit-form :input').prop('disabled', true);
    },

    enableForm: function () {
      $('#app-edit-form :input').prop('disabled', false);
    },

    showNotification: function (type, message, timeout) {
      if (!timeout) {
        timeout = 5000;
      }
      $(editApp.NOTIF_BOX).html(message);
      $(editApp.NOTIF_BOX).addClass('alert-' + type);
      $(editApp.NOTIF).show();
      $(editApp.NOTIF_BOX).slideDown();
      setTimeout(function () {
        editApp.hideNotification();
      }, timeout);
    },

    hideNotification: function () {
      $(editApp.NOTIF_BOX).slideUp(400, function () {
        $(editApp.NOTIF_BOX).html('');
        $(editApp.NOTIF_BOX).removeClass();
        $(editApp.NOTIF_BOX).addClass('alert');
      });
    },

    showProgressBar: function (message) {
      $(editApp.PROGRESS_BAR_LABEL).html(message);
      $(editApp.PROGRESS_BAR).show();
    },

    hideProgressBar: function () {
      $(editApp.PROGRESS_BAR).hide();
      $(editApp.PROGRESS_BAR_LABEL).html('');
    }
  };

  $(document).ready(function () {
    editApp.init();

    $('#app-edit-form').submit(function (e) {
      e.preventDefault();
      editApp.processForm();
    });
  });
})(jQuery);