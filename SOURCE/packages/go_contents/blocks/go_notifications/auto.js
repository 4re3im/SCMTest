$(document).ready(function(){
    $(document).on("click","#add-notif",function(e){
        e.preventDefault();
        var notifHtml = $("#notification-group").html();
        $("#main-notif-container").append(notifHtml);
    });
});