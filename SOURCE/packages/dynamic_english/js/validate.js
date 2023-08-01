
$(document).ready(function(){
	$(".form-control").blur(function(){
		validateForm("",$(this));
		errorHighlightButton($(this).parents("form"));
	});

	$("#contact-us-btn").click(function(e){
		e.preventDefault();
		var flag = validateForm($("#contact-us"));
        if (flag) {
        	$("#contact-us").prop("action",$("#contact-us-handler").val());
        	$.ajax({
        		url : $("#contact-us-handler").val(),
        		data : $("#contact-us").serialize(),
        		dataType : "html",
        		type : "POST",
        		success : function(data){
        			if(parseInt(data)) {
        				$("#contact-alert").addClass("alert-success");
        				$("#contact-alert-content").html("Thanks for your enquiry. You will be contacted shortly.");
        				$("#contact-alert").show();
        				$("#contact-us").find(".form-control").each(function(){
        					$(this).val("");
        				});
        				setTimeout(function(){
        					$("#contact-alert").hide();
        				},4000);
        			}
        		},
        		error : function(xhr,status,err){
        			$("#contact-alert").addClass("alert-danger");
    				$("#contact-alert-content").html("Something went wrong. Try again later or contact your administrator.");
    				$("#contact-alert").show();
        		},
        		complete : function(){

        		}
        	});
        } else {
        	errorHighlightButton($("#contact-us"));
        }
	});
});

function validateForm(form,element) {
    var errorFlag = true;
    var inputs = "";
    var oInputs = ""; // handler for checkboxes and radio buttons
    var ticked = 0;
    var notification = "";
    var initElement = null;
    if(element) {
        if(element.hasClass("form-control-tickable")) {
            oInputs = element;
        } else {
            inputs = element;
        }
    } else {
        inputs = form.find(".form-control");
        oInputs = form.find(".form-control-tickable");
    }

    if(inputs.length > 0) {
        inputs.each(function(){
        var value = $(this).val();
        var name = $(this).attr("name");
        var type = $(this).attr("type");
        var equalto = $(this).attr("equalto");

        var container = $(this).parents(".form-group");
        var notif = $(this).siblings(".help-block");
        var notifContent = $(this).siblings(".help-block").children("span");

        var notRequired = $(this).attr('no-required');

        if (typeof notRequired === typeof undefined || notRequired === false) {
            if (!value) {

                // small hack for the forgot password function
                /*
                if(element && $(this).attr("id") === "forgot-email") {
                    return false;
                }
                */
                container.addClass("has-error");
                notifContent.html(notification + "Please fill out this field.");
                notif.show();
                errorFlag = false;
                if(form && (!initElement)) {
                    initElement = $(this);
                }
            } else {
                if (type === "email") {
                    // email validation fix to handle all special characters, etc
                    if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(value)) {
                        container.removeClass("has-error");
                        notifContent.html("");
                        notif.hide();
                    } else {
                        container.addClass("has-error");
                        notifContent.html(notification + "Invalid Format");
                        notif.show();
                        errorFlag = false;
                        if(form && (!initElement)) {
                            initElement = $(this);
                        }
                    }

                    if (typeof equalto !== typeof undefined && equalto !== false) {
                        if (value !== $("input[name='" + equalto + "']").val()) {
                            container.addClass("has-error");
                            notifContent.html(notification + "Must be the same with Email field");
                            notif.show();
                            errorFlag = false;
                            if(form && (!initElement)) {
                                initElement = $(this);
                            }
                        } else {
                            container.removeClass("has-error");
                            notifContent.html("");
                            notif.hide();
                        }
                    }
                } else if (type === "password") {
                    if (value.length < 6) {
                        container.removeClass("has-success").addClass("has-error");
                        notifContent.html(notification + "Password must be 6 characters or longer.");
                        notif.show();
                        errorFlag = false;
                        if(form && (!initElement)) {
                            initElement = $(this);
                        }
                    } else if (value.indexOf("'") >= 0 || value.indexOf(" ") >= 0 || value.indexOf('"') >= 0 || value.indexOf(">") >= 0 || value.indexOf("<") >= 0) {
                        container.removeClass("has-success").addClass("has-error");
                        notifContent.html(notification + "Invalid characters found.");
                        notif.show();
                        errorFlag = false;
                        if(form && (!initElement)) {
                            initElement = $(this);
                        }
                    } else {
                        container.removeClass("has-error");
                        notifContent.html("");
                        notif.hide();
                    }

                    
                } else  {
                    var placeholder = $(this).attr("placeholder");

                    if(typeof placeholder === typeof undefined || placeholder === false) {
                        container.removeClass("has-error");
                        notifContent.html("");
                        notif.hide();
                    } else {

                        if((placeholder.toLowerCase() == "postcode" || placeholder.toLowerCase() == "postcode *") && isNaN(value)) {
                            container.removeClass("has-success").addClass("has-error");
                            notifContent.html(notification + "Invalid characters found.");
                            errorFlag = false;
                            if(form && (!initElement)) {
                                initElement = $(this);
                            }
                        } else {
                            container.removeClass("has-error");
                            notifContent.html("");
                            notif.hide();
                        }
                    }
                }
            }
        }
    });
    }


    if(oInputs.length > 0) {
        oInputs.each(function () {
            var name = $(this).attr("name");
            var type = $(this).attr("type");
            var equalto = $(this).attr("equalto");

            var container = $(this).parents(".form-col");
            var notif = $(this).siblings(".help-block");

            var notRequired = $(this).attr('no-required');
            if (typeof notRequired === typeof undefined || notRequired === false) {
                var c = $("input[type='checkbox']");
                $(c).each(function() {
                    if($(this).hasClass("form-control-tickable") && $(this).is(":checked")) {
                        ticked++;
                    }
                });
            }
        });
        // alert(ticked); return false;
        if (ticked <= 0) {
            oInputs.parents(".form-col").addClass("has-error");
            var tickNotif = oInputs.parents(".form-col").children(".help-block"); //.siblings(".help-block");
            tickNotif.html("Required. Please tick at least one.");
            errorFlag = (errorFlag && false);
        } else {
            oInputs.parents(".form-col").removeClass("has-error");
            var tickNotif = oInputs.parents(".form-col").children(".help-block"); //.siblings(".help-block");
            tickNotif.html("");
            errorFlag = (errorFlag && true);
        }

    }
    if(form && $(initElement).length > 0) {

        $("body").animate({
            scrollTop : ($(initElement).offset().top) - 250
        },"medium");
        $(initElement).focus();
    }
    return errorFlag;
}

function errorHighlightButton(form) {
	var flag = $(form).find("div.has-error").length;
	if(flag) {
		$(form).find("button").addClass("btn-danger");
	} else {
		$(form).find("button").removeClass("btn-danger");
	}
}
