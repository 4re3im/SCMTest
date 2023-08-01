$(document).ready(function() {
    // GCAP-625 added by mtanada 20200128
    var searchEntitlement = $('#search-entitlement');
    var autoCompleteSource = $(searchEntitlement).attr('search-url');
    $(searchEntitlement).autocomplete({
        delay: 800,
        source: autoCompleteSource,
        select: function (event, ui) {
            if (ui.item.value !== 0) {
                $("#demo_id").val(ui.item.id);
                $("#demo_id").change();
            }
        }
    });
    $("#demo_id").on("change", function(){
        var demoId = $("#demo_id").val();
        demoIdExists(demoId);
    });

    $(document).on("click","#tab-content-display",function(){
        $("#local-content-display").trigger("click");
    });
    
    $(document).on("click",".title-tab-contents",function(e){
        var url = $(this).attr("content-link");
        var href = $(this).attr("href");
        var titleID = $("#titleID").val();
        var tabID = '';

        var count = 0;

        $(".tab-select").each(function(){
            if($(this).hasClass('selected')) {
                tabID = $(this).attr('id');
                count++;
            }
        });

        if(count > 0) {
            if(href === "#global-content") {
                displayTabGlobalContent(url,tabID,titleID,href);
            } else {
                displayTabLocalContent(url,tabID,titleID);
            }
        } else {
            alert("Please select a tab.");
        }
    });
    
    $(document).on("click",".add-content",function() {
        var contentID = $(this).siblings("input").val(); // ID of content
        
        var titleID = $("#titleID").val();
        var tabID = '';
        $(".tab-select").each(function(){
            if($(this).hasClass('selected')) {
                tabID = $(this).attr("id");
            }
        });
        
        var text = $(this).siblings("span").text();
        var url = $(this).attr("href");
        var html = null;
        var params = {title : text, contentID : contentID, tabID : tabID, titleID : titleID, class : "global"};
        $(this).hide();
        $.ajax({
            url : url,
            data : params,
            type : "POST",
            dataType : "html",
            beforeSend : function() {
                $(".addedContents-status").html("Adding...");
            },
            success : function(data) {
                html = data;
            },
            error : function(xhr,status,err) {
                html = xhr.responseText;
            },
            complete : function() {
                if($("#content-added-table tbody").children("tr").children("td#empty-table").length > 0) {
                    $("#content-added-table tbody").html(html);
                } else {
                    $("#content-added-table tbody").append(html);
                }
                
                $(".addedContents-status").html("");
            }
        });
    });
    
    $(document).on("click",".remove-content",function() {
        var titleID = $(this).attr("title-content");
        var tabContentID = $(this).parents("tr").attr("content-id");
        var url = $(this).attr("href");
        var html = null;
        $(this).parents("td").parents("tr").remove();
        $.ajax({
            url : url,
            data : { tabContentID : tabContentID },
            dataType : "html",
            type : "POST",
            beforeSend : function() {
                $(".addedContents-status").html("Deleting...");
                $("#localContent-contentAdded-footer").html("Deleting...");
            },
            success : function(data) {
                html = data;
                $(".add-content").each(function() {
                    if ($(this).attr("id") == titleID) {
                        $(this).show();
                    }
                });
                $(".add-local-content").each(function() {
                    if ($(this).attr("id") == titleID) {
                        $(this).show();
                    }
                });
            },
            error : function(xhr,status,err) {
                html = xhr.responseText;
            },
            complete : function() {
                if(parseInt(html) === 1) {
                    $(".addedContents-status").html("Deleted...");
                    $("#localContent-contentAdded-footer").html("Deleted...");
                } else {
                    $(".addedContents-status").html("Error...");
                    $("#localContent-contentAdded-footer").html("Error...");
                }
            }
        });
    });
    
    $(document).on("change",".tab-content-updater",function() {
        var tabName = $(this).parents("td").parents("tr").attr("id");
        tabName = tabName.replace(/ /g,"_");
        tabName = tabName.replace(/,/g,"");
        var url = $(".tab-content-details#update-url").val();
        var tabContentID = $(this).parents("td").parents("tr").attr("content-id");
        var column = $(this).attr("column");
        var value = $(this).val();
        var html = null;
        
        $.ajax({
            url : url,
            data : {
                columnName : column,
                columnValue : value,
                tabContentID : tabContentID
            },
            dataType : "html",
            type : "POST",
            beforeSend : function() {
                $(".addedContents-status").html("Updating...");
            },
            success : function(data) {
                html = data;
            },
            error : function(xhr,status,err) {
                html = xhr.responseText;
            },
            complete : function() {
                if(parseInt(html) === 1) {
                    $(".addedContents-status").html("Updated...");
                } else {
                    $(".addedContents-status").html("Error...");
                }
            }
        });
    });
    
    $(document).on("click",".tab-folders",function(e) {
        e.preventDefault();
        var folderID = $(this).attr("folder-id");
        var tabID = '';
        $(".tab-select").each(function(){
            if($(this).hasClass('selected')) {
                tabID = $(this).attr("id");
            }
        });
        var url = $(this).attr("href");
        var html = null;
        $.ajax({
            url : url,
            data : {folderID : folderID, tabID : tabID},
            type : "POST",
            dataType : "html",
            beforeSend : function() {
                $("#localContent-filesContent-body").html("Please wait...");
            },
            success : function(data) {
                html = data;
            },
            error : function(xhr,status,err) {
                html = xhr.responseText;
            },
            complete : function() {
                $("#localContent-filesContent-body").html(html);
                $(".contents-status").html("");
            }
        });
        $(".tab-folders").removeClass("tab-folders-active");
        $(this).addClass("tab-folders-active");
    });
    
    $(document).on("click",".add-local-content",function() {
        var contentID = $(this).attr("id"); // ID of content
        
        var titleID = $("#titleID").val();
        var tabID = '';
        $(".tab-select").each(function(){
            if($(this).hasClass('selected')) {
                tabID = $(this).attr("id");
            }
        });
        
        var text = $(this).siblings("span").text();
        var url = $(this).attr("href");
        var html = null;
        $(this).hide();
        $.ajax({
            url : url,
            data : {title : text, contentID : contentID, tabID : tabID, titleID : titleID, class : "local"},
            type : "POST",
            dataType : "html",
            beforeSend : function() {
                $(".addedContents-status").html("Adding...");
            },
            success : function(data) {
                html = data;
            },
            error : function(xhr,status,err) {
                html = xhr.responseText;
            },
            complete : function() {
                if($("#localContent-contentAdded-table tbody").children("tr").children("td#empty-table").length > 0) {
                    $("#localContent-contentAdded-table tbody").html(html);
                } else {
                    $("#localContent-contentAdded-table tbody").append(html);
                }
                $(".addedContents-status").html("");
            }
        });
    });

    $(document).on("click","tbody.sortable tr td",function(){
        // alert("IN");
    });
});

function displayTabGlobalContent(url,tabID,titleID,href) {
    var html = null;
    $.ajax({
        url: url,
        data: {tabID: tabID, titleID: titleID},
        type: "POST",
        dataType: "json",
        beforeSend: function () {
            $(".contents-status").html("Please wait...");
            if (href === "#global-content") {
                $(".addedContents-status").html("Please wait...");
            }
            $("#content-added-table tbody").html("");
            $("#localContent-contentAdded-table tbody").html("");
            $("#localContent-filesContent-body").children("table").children("tbody").html("");
        },
        success: function (data) {
            html = data;
        },
        error: function (xhr, status, err) {
            $('#error').html(xhr.responseText);
            html = xhr.responseText;
        },
        complete: function () {
            $(href + "-body").html(html.global_contents);
            $("#content-added-table tbody").append(html.linked_global_content);
            $(".contents-status").html("");
            $(".addedContents-status").html("");
        }
    });
}

function displayTabLocalContent(url,tabID,titleID) {
    var html = null;
    $.ajax({
        url: url,
        data: {tabID: tabID, titleID: titleID},
        type: "POST",
        dataType: "json",
        beforeSend: function () {
            $("#localContent-files-footer").html("Please wait...");
            $(".addedContents-status").html("Please wait...");
            $("#localContent-contentAdded-table tbody").html("");
            $("#content-added-table tbody").html("");
            
            $("#global-content-body").html("");
            
        },
        success: function (data) {
            html = data;
        },
        error: function (xhr, status, err) {
            html = xhr.responseText;
        },
        complete: function () {
            $("#localContent-files-body").html(html.folders);
            $("#localContent-contentAdded-table tbody").append(html.linked_global_content);
            $("#localContent-files-footer").html("");
            $(".addedContents-status").html("");
        }
    });
}
// GCAP-704 Added by mtanada 20200228
function demoIdExists(demoId) {
    var html = null;
    var url = '/dashboard/cup_content/titles/getDemoId/';
    $.ajax({
        type: "POST",
        url: url,
        data: {demoId: demoId},
        dataType: "json",
        success: function (data) {
            if (data.titleName !== 'available') {
                alert("Demo ID: " + demoId + ' is already used on Title: ' + data.titleName);
                $("#demo_id").val(0);
            }
        },
        error: function (xhr) {
            html = xhr.responseText;
        }
    });
}