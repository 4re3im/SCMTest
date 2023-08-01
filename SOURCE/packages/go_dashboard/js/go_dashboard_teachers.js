
function sfUpdate(id) {

    var url = "/dashboard/go_users/salesforceUpdate/";
    var params = {'id': id};

    $.ajax({
        url : url,
        data : params,
        type : "POST",
        success : function(data) {
            $("#update"+id).prop('disabled', true);
        },
        error : function(xhr,status,err) {
            html = xhr.responseText;
        },
        complete : function() {

        }
    });
}

function sfHide(id) {
    var url = "/dashboard/go_users/salesforceHide/";
    var params = {'id': id};

    $.ajax({
        url : url,
        data : params,
        type : "POST",
        success : function(data) {
            $("#update"+id).prop('disabled', true);
            $("#hide"+id).prop('disabled', true);
        },
        error : function(xhr,status,err) {
            // html = xhr.responseText;
        },
        complete : function() {

        }
    });
}
