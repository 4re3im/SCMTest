// function toggleSubscription(elm,usid) {

// 	var user_id = $('#user_id').val();


// 	console.log(this);

// alert(user_id);



// 	return false;

//     e.preventDefault();
//     e.stopPropagation();

// $.ajax({
// 	type: 'POST',
// 	data: "&usid="+ usid + "&user_id="+ user_id + "&is_ajax=yes&func=toggleusersubscription",
// 	url: "<?php print str_replace("&","&",$this->action('toggleUserSubscription')); ?>",
// 	success: function(data){

//      		$('div.usersubscription').html(data);

//    	},

//    	error:function (xhr, ajaxOptions, thrownError){
//      		$('.usersubscription').html(xhr.status+"<br/>"+thrownError);
//    	}

// });



// }



function addNote() {

    var note = $('#note').val();
    var user_id = $('#user_id').val();

    $.ajax({
        type: 'POST',
        data: "&note=" + note + "&user_id=" + user_id + "&is_ajax=yes&func=note",
        url: "<?php print str_replace(" & "," & ",$this->action('addNote')); ?>",
        success: function (data) {
            $('#note').val('');
            $('div.usernotes').html(data);
            //console.log(data);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $('.usernotes').html(xhr.status + "<br/>" + thrownError);
        }

    });

}

/**
 * Function to save general user data
 * @author: jmarasigan
 * @date: July 8, 2015
 * @ticket: ANZGO-1664
 **/
function saveUser() {

    var data = $("#usergeneral").serializeArray();

    $.ajax({
        type: 'POST',
        data: data,
        url: $("#usergeneral").attr("action"),
        success: function (data) {
            //update fields
            var user_info = jQuery.parseJSON(data)
            if (user_info['ak_uActivatedDate']) {
                $('#activateddate_span').html(user_info['ak_uActivatedDate']);
                $('#activateddate').val(user_info['ak_uActivatedDate']);
                $('#manuallyactivated_span').html(user_info['ak_uManuallyActivated'] == 1 ? 'Y' : 'N');
                $('#manuallyactivated').val(user_info['ak_uManuallyActivated']);
                $('#manuallyactivatedby_span').html(user_info['uName']);
                $('#manuallyactivatedby').val(user_info['ak_uMAStaffID']);
            }
            $("#alertMessage").html("General user info saved.<br/>");
            $("#alertMessageDiv").addClass("alert-success").show();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // $("#alertMessage").html("An error was encountered while saving. Please try again.<br/>");
            $("#alertMessage").html(xhr.responseText);
            $("#alertMessageDiv").addClass("alert-error").show();
        }

    });
}

/**
 * Function to archive a user
 * @author: jmarasigan
 * @date: July 8, 2015
 * @ticket: ANZGO-1664
 **/
function archiveUser() {

    var data = $("#usergeneral").serializeArray()

    $.ajax({
        type: 'POST',
        data: data,
        url: "/dashboard/go_users/archiveUser/" + data[0].value,
        success: function (data) {
            $("#alertMessage").html("User successfully archived.<br/>");
            $("#alertMessageDiv").addClass("alert-success").show();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            $("#alertMessage").html("An error was encountered while archiving. Please try again.<br/>");
            $("#alertMessageDiv").addClass("alert-error").show();
        }

    });
}




$(function () {

    // Go User - General - User Notes
    $('input#addnote').click(function (e) {
        $("#note").slideToggle();
        e.preventDefault();
        e.stopPropagation();
    });

    // Go User - General - User Notes
    $('input#note').change(function () {
        addNote();
    });

    // Go User - General - Save
    $('input#save').click(function () {
        saveUser();
    });

    // Go User - General - Archive
    $('input#archive').click(function () {
        archiveUser();
    });

    $('.close').click(function () {
        $("#alertMessage").html("");
        $("#alertMessageDiv").hide();
    });


    // Go User - Subscriptions - Deactivate Subscriptions
    /**$('input.deactivate').click(function(e) {
     
     console.log("TEST");
     var user_id = $('#user_id').val();
     var user_subscription_id = $(this).attr("data");
     console.log($(this));
     var elm = $(this);
     
     e.preventDefault();
     e.stopPropagation();
     
     $.ajax({
     type: 'POST',
     data: "&usid="+ user_subscription_id + "&user_id="+ user_id + "&is_ajax=yes&func=toggleusersubscription",
     url: "<?php print str_replace("&","&",$this->action('toggleUserSubscription')); ?>",
     success: function(data){
     
     $('div.usersubscription').html(data);
     
     },
     
     error:function (xhr, ajaxOptions, thrownError){
     $('.usersubscription').html(xhr.status+"<br/>"+thrownError);
     }
     
     });
     
     });**/



}); //function()
