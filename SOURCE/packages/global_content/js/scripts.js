
$(document).ready(function(){
    $(".global-content-select").click(function() {
        var contentName = $(this).text();
        var contentID = $(this).find("input").val();
        var gcd = new GlobalContentDisplay();
        var html = null;
        
        $.ajax({
            url : 'getContentDetails',
            data : {id : contentID},
            type : "POST",
            dataType : "html",
            beforeSend : function() {
                gcd.showBuffering();
                gcd.setLegend(contentName);
                gcd.hideDisplayContent();
                gcd.hideAlertStatus();
                gcd.displayContent();
                
                // remove all set tinyMCE controls to prepare next <textarea> for next instance
                tinyMCE.execCommand('mceRemoveControl',false,'global-content-tetxtarea'); 
            },
            success: function (data) {
                html = data;
            },
            error : function(xhr,status,err) {
                html = xhr.responseText;
            },
            complete : function() {
                gcd.hideBuffering();
                gcd.setContent(html);
                gcd.showDisplayContent();
                
                // bind retrieved <textarea> into tinyMCE
                tinyMCE.execCommand('mceAddControl',false,'global-content-tetxtarea'); 
            }
        });
    });
    
    $(document).on("click","#submit-global-content",function(e) {
        e.preventDefault();
        tinyMCE.triggerSave();
        var gcd = new GlobalContentDisplay();
        var form = $(".global-content-form");
        var html = null;
        $.ajax({
            url : form.attr("action"),
            data : form.serialize(),
            type : "POST",
            dataType : "html",
            beforeSend : function() {
                gcd.showBuffering();  
            },
            success : function(data) {
                if($.trim(data) == 'true') {
                    gcd.showSuccess("Global content updated.");
                } else {
                    gcd.showError("Problem encountered during saving.");
                }
            },
            error : function(xhr,status,error) {
                html = xhr.responseText;
            },
            complete : function() {
                gcd.hideBuffering();
            }
        });
    });
    
    $(document).on("click","#global-content-display",function(){
        var gcd = new GlobalContentDisplay();
        gcd.hideAlertStatus();
    });

    $("#add-new-gc").click(function(){
        var gcd = new GlobalContentDisplay();
        var html = "";
        $.ajax({
            url : $(this).attr("href"),
            dataType : "html",
            beforeSend : function() {
                gcd.showBuffering()

            },
            success : function(data) {
                html = data;
            },
            error : function() {
                html = xhr.responseText;
            },
            complete : function() {
                gcd.hideBuffering();
                gcd.setContent(html);
                gcd.setLegend("Add new");
                gcd.displayContent();

            }
        });
    });
});

/** OOP Global Content */
var GlobalContentDisplay = function() {
    this.display = $("#global-content-display");
    this.legend = $(this.display).find("legend");
    this.displayBody = $(this.display).find("#gc-display");
    this.loadIcon = $("#loading");
    this.alertBox = $(this.display).find(".alert");
};

GlobalContentDisplay.prototype = {
    setLegend : function(text) {
        $(this.legend).text(text);
    },
    setContent : function(html) {
        $(this.displayBody).html(html);
    },
    showBuffering : function() {
        $(this.loadIcon).show();
    },
    hideBuffering : function() {
        $(this.loadIcon).hide();
    },
    displayContent : function() {
        $(this.display).show();
    },
    hideDisplayContent : function() {
        $(this.displayBody).hide();
    },
    showDisplayContent : function() {
        $(this.displayBody).show();
    },
    hideAlertStatus : function() {
        $(this.alertBox).hide();
    },
    showSuccess : function(text) {
        $(this.alertBox).addClass("alert-success");
        $(this.alertBox).text(text);
        $(this.alertBox).show();
    },
    showError : function(text) {
        $(this.alertBox).addClass("alert-danger");
        $(this.alertBox).text(text);
        $(this.alertBox).show();
    }
    
}