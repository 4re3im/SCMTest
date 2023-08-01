
$(document).ready(function () {

    function validateForm(element, button = null) {
        var errorFlag = true;
        var notification = "* ";

        if (button) {
            element = $('input.go-input');
        }

        if (element.length > 0) {
            element.each(function () {
                var value = $(this).val();
                var container = $(this).parents(".form-group");
                var notif = $(this).siblings(".help-block");
                var equalto = $(this).attr('equalto');

                if (!value) {
                    container.removeClass("has-success").addClass("has-error");
                    notif.html(notification + "Required");
                    errorFlag = false;
                } else {
                    if (/^\s/g.test(value) === true || /\s$/g.test(value) === true) {
                        container.removeClass("has-success").addClass("has-error");
                        notif.html(notification + "Password must not have leading and trailing whitespaces.");
                        errorFlag = false;
                    } else if (value.length < 8) {
                        container.removeClass("has-success").addClass("has-error");
                        notif.html(notification + "Password must be 8 characters or longer.");
                        errorFlag = false;
                    } else if (/\d/g.test(value) === false) {
                        container.removeClass("has-success").addClass("has-error");
                        notif.html(notification + "Password must contain at least 1 number.");
                        errorFlag = false;
                    } else if (/[a-zA-Z]/g.test(value) === false) {
                        container.removeClass("has-success").addClass("has-error");
                        notif.html(notification + "Password must contain at least 1 letter.");
                        errorFlag = false;
                    } else {
                        container.removeClass("has-error");
                        notif.html("");
                    }

                    if (typeof equalto !== typeof undefined && equalto !== false) {
                        if (value !== $("input[name='" + equalto + "']").val()) {
                            container.addClass("has-error");
                            notif.html(notification + "Must be the same with password");
                            errorFlag = false;
                        } else {
                            container.removeClass("has-error");
                            notif.html("");
                        }
                    }
            }
            });
            return errorFlag;
        }
    }

    $(document).on("submit", "#forgetPasswordForm", function (e) {
        var flag = validateForm($(this), 'submit');
        return flag;
    });

   $('input.go-input').blur(function() {
       if ($(this).parents().get(1).id === 'forgetPasswordForm') {
           validateForm($(this));
       }
   });

});
