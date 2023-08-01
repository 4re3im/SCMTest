$(document).ready(function(){
    var title_list_init = $("#titleList").html();

    $(".datepicker").datepicker({
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        dateFormat : 'M d, yy'
    });
    
    $("#searchTitles").focus(function(){
        var source = $(this).attr("href");
        $(this).autocomplete({
            source : source,
            select : function(event, ui) {
                if(ui.item.value != 'No matches found... :(') {
                    if ($("#tlPlaceholder").length > 0) {
                        $("#titleList").html(getOptionTemplate(ui.item.value, ui.item.label));
                    } else {
                        $("#titleList").append(getOptionTemplate(ui.item.value, ui.item.label));
                        
                        var poolOpts = $("#titleList").children("option");
                        poolOpts.sort(function (a, b) {
                            if (a.text == 'NA') {
                                return 1;
                            }
                            else if (b.text == 'NA') {
                                return -1;
                            }
                            return (a.text > b.text) ? 1 : -1;
                        });
                        $("#titleList").html(poolOpts);
                    }
                }   
            },
            close : function(event,ui) {
                $("#searchTitles").val("");
            }
        });
    });
    
    $(".annType").change(function(){
        var type = parseInt($(this).val());
        if(type === 0) {
            $("#searchTitles").attr("disabled","");
            $("#titleList").attr("disabled","");
            $("#titleList").html(title_list_init);
        } else {
            $("#searchTitles").removeAttr("disabled");
            $("#titleList").removeAttr("disabled");
        }
    });
    
    $(document).on("click",".tick-notif",function(e){
        if($(".tick-notif:checked").length > 0) {
            $("#del-ticked").removeAttr("disabled");
        } else {
            $("#del-ticked").attr("disabled","");
        }
    });
    
    $("#del-ticked").click(function(e){
        e.preventDefault();
        var url = $(this).attr("href");
        var data = $(".tick-notif:checked").serializeArray();
        var page = $("#nPage").val();
        data.push({name : "page", value : page});  
        generalAjax(url,data,"html",$("#announcementTable tbody"));
    });
    
    /** Search table functions */    
    $("#advanced-search-trigger").click(function(e){
        e.preventDefault();
        var icon = $(this).find("i");
        $("#advanced-search").slideToggle("slow", function () {
            if (icon.attr("class") === "icon-circle-arrow-down") {
                icon.attr("class", "icon-circle-arrow-up");
            } else {
                icon.attr("class", "icon-circle-arrow-down");
            }
        });
    });
    
    $(".notif-search").focus(function(){
        if($(this).attr("id") != "nPage") {
            $(".notif-search").removeClass("active-search");
            $(this).addClass("active-search");
        }
    });
    
    $("#go-search").click(function(e){
        e.preventDefault();
        var url = $(this).attr("href") + $(".active-search").attr("id");
        var value = $(".active-search").val();
        if(typeof value !== typeof undefined || value !== false) {
            generalAjax(url,{data : value},"html",$("#announcementTable tbody"));
        } else {
            alert("No search parameters");
        }
        
    });
    
    $("#nPage").change(function(){
        var url = $(this).attr("href");
        var count = $(this).val();
        generalAjax(url,{data : count},"html",$("#announcementTable tbody"));
        
        var widgetUrl = $("#widgetUrl").val() + 1;
        generalAjax(widgetUrl,{page : count},"html",$("#tableWidget"));
    });
    /** End */
    
    /** Table navigation controls */
    // Currently set for Search Notifications table.
    $(document).on("click",".numbers",function(e){
        e.preventDefault();
        var url = $("#navigateUrl").val() + $(this).children("a").text();
        var widget_url = $("#widgetUrl").val() + $(this).children("a").text();
        var pagination = $("#nPage").val();
        var data = {pagination : pagination};
        if(!$(this).hasClass("disabled")) {
            // refresh table
            generalAjax(url,data,"html",$("#announcementTable tbody"));
            $(".numbers").removeClass("currentPage active disabled");
            $(this).addClass("currentPage active disabled");
            
            // refresh nav
            generalAjax(widget_url,data,"html",$("#tableWidget"));
        }
    });
    var currentPage;
    var pageNumber;
    $(document).on("click",".prev",function(e){
        e.preventDefault();
        currentPage = parseInt($(".currentPage").children("a").text());
        if(!$(this).hasClass("disabled")) {
            $(".numbers").each(function(){
                pageNumber = parseInt($(this).children("a").text());
                if((currentPage - 1) === pageNumber) {
                    $(this).trigger("click");
                    return false;
                }
            });
        }
    });
    
    $(document).on("click",".next",function(e){
        e.preventDefault();
        currentPage = parseInt($(".currentPage").children("a").text());
        if(!$(this).hasClass("disabled")) {
            $(".numbers").each(function(){
                pageNumber = parseInt($(this).children("a").text());
                if((currentPage + 1) === pageNumber) {
                    $(this).trigger("click");
                    return false;
                }
            });
        }
        
    });
    /** End */
    
});

function getOptionTemplate(value, text) {
    return "<option value='" + value + "' selected>" + text + "</option>";
}

function generalAjax(url,data,dataType,target) {
     $.ajax({
        url: url,
        data: data,
        type: "POST",
        dataType: dataType,
        success: function (response) {
            target.html(response);
        },
        error: function (xhr,status,error) {
            alert(error + ": " + status);
        }
    });
}