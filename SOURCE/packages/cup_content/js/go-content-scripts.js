/* 
 * Handles all js functionality under the Edit Go Content page.
 */
var editMode = false;
$(document).ready(function () {
    /**
     * Handles all heading submission. Applies to folders, subfolders, and content details
     */
    $('.add-heading-form').submit(function (e) {
        e.preventDefault();
        var id = $(this).attr('id');
        var target = null;
        var data = null;
        var inputVal = $(this).find('input[type="text"]').val();
        
        if (id === 'parentFolders-form') {
            target = $('#parentFolders-list');
            data = $(this).serialize();
            $('#title-tab-content').hide();
            // $('.tab-select').removeClass('selected');
        } else if(id === 'subfolders-form') {
            target = $('#subfolders-list');
            data = {
                ContentHeading : inputVal,
                titleID : $('#titleID').val(),
                folderID : $('#parentFolders-list > tbody > tr > td.selected').attr('id')
            };
        } else if(id === 'tabs-form') {
            target = $('#tabs-list');
            data = $(this).serialize();
        } else {
            target = $('#content-details-list');
            data = {
                PublicName : inputVal,
                TypeID : 1005,
                ContentID : $('#contentID-hidden').val()
            };
        }
        addHeadingForm($(this),target,data);
        
        if (id === 'parentFolders-form') {
            refreshTabFolders();
        }
    });
    
    /**
     * Handles submission of forms within panels. This excludes the submission of headings.
     */
    $(document).on('submit','.detail-save-form',function(e){
        e.preventDefault();
        saveDetailForm($(this));
    });
    
    $(document).on('click','.parentFolders-select',function(e){
        e.stopPropagation();
        if(editMode) {
            return false;
        }
        
        var spanText = $(this).children('span').text();
        
        $('.parentFolders-select').removeClass('selected');
        $(this).addClass('selected');
        
        //st value for content detail form
        $('#FolderID').val($(this).attr("id"));
        var data = {
            folderID : $(this).attr("id")
        };
        $.ajax({
            url : $(this).attr('url'),
            data : data,
            dataType : 'html',
            type : 'POST',
            beforeSend: function () {
                $('#contents-container').show();
                $('#content-form-container').hide();
                $('#content-details-container').hide();
                $('#title-tab-content').hide();
            },
            success: function (resp) {
                $('#subfolders-list tbody').html(resp);
            },

            error: function (xhr, status, error) {
                $("#error").html(xhr.responseText);
            },
            complete: function () {
            }
        });
    });
    
    $(document).on('click','.subFolders-select',function(e){
        e.stopPropagation();
        $('.subFolders-select').removeClass('selected');
        $(this).addClass('selected');
        var data = {
            contentID : $(this).attr('id')
        };
        // set value for the multiple file upload forms
        $('.content-id-upload').val($(this).attr('id'));
        
        $.ajax({
            url : $(this).attr('url'),
            data : data,
            dataType : 'json',
            type : 'POST',
            beforeSend : function() {
                resetSubfolderFields();
                $('#subfolders-details').show();
                $('#content-form-container').show();
                $('#content-detail-container').hide();
                tinyMCE.execCommand('mceRemoveControl', false, 'ContentData');
            },
            success: function (resp) {
                fillSubfolderFields(resp.details);
                $('#content-details-list tbody').html(resp.content_details);
                $('#content-details-container').show();
            },
            error: function (xhr, status, error) {
                $("#error").html(xhr.responseText);
            },
            complete : function() {
                tinyMCE.execCommand('mceAddControl', false, 'ContentData');
            }
        });
    });
    
    $(document).on('click','.content-heading-select',function(e){
        e.stopPropagation();
        $('.content-heading-select').removeClass('selected');
        $(this).addClass('selected');
        
        // set value of this entry in the content detail form
        $('#content-detail-id').val($(this).attr('id'));
        $.ajax({
            url : $(this).attr('url'),
            data : {id : $(this).attr('id')},
            dataType : 'json',
            type : 'POST',
            beforeSend : function(){
                $('#content-detail-container').show();
            },
            success : function(data){
                fillFormFields(data);
                $('#TypeID').trigger('change');
            },
            error : function(xhr,status,error){
                $('#error').html(xhr.responseText);
            },
            complete : function(){
                $('#action-status').hide();
            }
        });
    });
    
    $(document).on('click','.tab-select',function(e){
        e.stopPropagation();
        $('.tab-select').removeClass('selected');
        $(this).addClass('selected');

        var tabID = $(this).attr("id");
        
        $.ajax({
            url : $(this).attr('url'),
            data : {
                titleID : $('#titleID').val(),
                tabID : tabID
            },
            dataType : 'json',
            type : 'POST',
            beforeSend : function(){
                resetGeneralTabPanels();
                resetLocalContentTabPanels();
                resetGlobalContentTabPanels();
                resetLinkedTabContents();
                
                tinyMCE.execCommand('mceRemoveControl', false, 'Public_TabText');
                tinyMCE.execCommand('mceRemoveControl', false, 'Private_TabText');
            },
            success : function(resp){
                var genDetail = resp.GenDetails;
                var tabContents = resp.TabContents;
                $('#tab-info-panel').html(genDetail.Information);
                $('#tab-options-panel').html(genDetail.Options);
                $('#tab-access-panel').html(genDetail.AccessRights);
                $('#tab-text-panel').html(genDetail.Text);
                
                $("#localContent-files-body").html(tabContents.folders);
                $("#localContent-contentAdded-table tbody").html(tabContents.linked_global_content);
                $("#content-added-table tbody").html(tabContents.linked_global_content);
            },
            error : function(xhr,status,err){
                $('#error').html(xhr.responseText);
            },
            complete: function(){
                $('#title-tab-content').show();
                $("#title-tab-details").children("input['submit']").show();

                var subtab = $("#tab-content-container").find("ul.nav").find("li.active").children("a");
                if($(subtab).attr("href") == "#global-content") {
                    $(subtab).trigger("click");
                }

                tinyMCE.execCommand('mceAddControl', false, 'Public_TabText');
                tinyMCE.execCommand('mceAddControl', false, 'Private_TabText');
            }
        });
    });
    
    // Retrieve form depending on the selected heading type content.
    $(document).on('change','#TypeID',function(){
        var type = $(this).val();
        var id = $('#content-detail-id').val();
        var url = $(this).attr('url');
        var form = $('#content-type-info').children('div.panel-body');
        var header = $('#content-type-info').children('div.panel-heading').children('.panel-title');
        
        if($('.selected').length > 0) {
            if(type !== '0') {
                $.ajax({
                    url: url,
                    data: {
                        TypeID: type,
                        ID: id
                    },
                    dataType: 'json',
                    type: 'POST',
                    beforeSend: function () {
                        $('#content-type-info').show();
                        $('#content-type-info').children('input[type="hidden"]').val(type);
                        $('#content-type-info').prop('content-type',type);
                        $(form).html("Please wait...");
                        $(header).html("");
                        $('#file-upload-panel').hide();
                    },
                    success: function (data) {
                        $(header).html(data.header);
                        $(form).html(data.body);
                        if (data.uploadForm) {
                            $('#file-upload-panel').children('.panel-body').html(data.uploadForm);
                            $('#file-upload-panel').show();
                        } else {
                            $('#file-upload-panel').hide();
                        }
                    },
                    error: function (xhr, status, error) {
                        $('#error').html(xhr.responseText);
                    },
                    complete: function () {
                        tinyMCE.execCommand('mceRemoveControl', false, 'HTML_Content');
                        tinyMCE.execCommand('mceAddControl', false, 'HTML_Content');
                    }
                });
            } else {
                alert('Select a content type.');
                $(this).val($('#content-type-info').children('input[type="hidden"]').val());
            }
        } else {
            $('#TypeID').val(0);
            alert('Please select a heading.');
        }
    });
    
    /**
    * ANZGO-1847
    * Bypasses normal form submission flow and binds to Malsup Jquery submission
     * @param {Event} e
     */
    $(document).on('submit','.fileuploadmulti',function(e){
        e.preventDefault();
        var form = $(this);
        var formType = $(form).attr("id");
        var submitButton = $(form).children("div.form-group").children("input[type='submit']");
        var options = {
            dataType: 'html',
            clearForm : true,
            beforeSend : function() {
                $(submitButton).prop("disabled",true);
            },
            uploadProgress : function (event, position, total, percentComplete) {
                $(submitButton).val("Uploading... " + percentComplete + "%");
            },
            success : function(data) {
                $("#content-details-list tbody").html(data);
                if (formType !== "multi-file-upload") {
                    $('.content-heading-select').each(function () {
                        if ($(this).hasClass('selected')) {
                            $(this).trigger('click');
                            return false;
                        }
                    });
                }
            },
            error : function(xhr,status,err) {
                alert(xhr.responseText);
                $("#error").html(xhr.responseText);
            },
            complete : function() {
                $(submitButton).prop("disabled",false);
                $(submitButton).val("Upload");
                $(form).clearForm();
            }  
        };
        
        if(!$(form).find('input[type="file"]').val()) {
            alert("No files selected.");
            return false;
        }
        
        if(formType === 'single-file-upload') {
            var d = confirm("Above details not saved will be lost. Continue?");
            if(d === true) {
                $(this).ajaxSubmit(options);
            }
        } else {
            $(this).ajaxSubmit(options);
        }
    });

    $(document).on('mouseover','.sortable',function(){
        $(this).sortable({
            start : function(event, ui){
                $(ui.item).children('td').children('a.go-archiver').hide();
            },
            update : function(event,ui){
                var index = 1;
                var tableType = $(this).parents('table').attr('id');
                var hiddenInputs = null;
                var notif = null;
                
                if(tableType === 'content-details-list') {
                    hiddenInputs = $('input.content-detail-select-hidden');
                    notif = $('#content-details-notif');
                } else if(tableType === 'localContent-contentAdded-table') {
                    hiddenInputs = $('input.tab-content-hidden-l');
                } else if(tableType === 'content-added-table') {
                    hiddenInputs = $('input.tab-content-hidden-g');
                } else {
                    hiddenInputs = $('input.tab-select-hidden');
                    notif = $('#tabs-notif');
                }
                
                var titles = [];
                $(hiddenInputs).each(function(){
                    if($.inArray($(this).parent("td").text(),titles) <= -1) {
                        titles.push($(this).parent("td").text());
                        $(this).attr('value',index);
                    } 
                    index++;
                });

                var url = $(this).parents('table').attr('url');
                var sort = $(hiddenInputs).serialize();

                console.log(sort);
                $.ajax({
                    url : url,
                    data : sort,
                    dataType : 'html',
                    type : 'POST',
                    beforeSend : function() {
                        $(notif).show();
                    },
                    success : function(data) {
                        console.log(data);
                        $(notif).text('Sorted.');
                    },
                    error : function(xhr,status,error) {
                        $('#error').html(xhr.responseText);
                    },
                    complete : function() {
                        setTimeout(function () {
                            $(notif).hide();
                        }, 3000);
                    }
                });
            }
        });
        // ANZUAT-12
        // $(this).disableSelection();
    });
    
    var prevHTML;
    $(document).on('dblclick','.parentFolders-select',function(e){
        e.stopPropagation();
        prevHTML = $(this).html();
        $(this).children('a').text('');
        $(this).remove('a');
        editMode = true;
        var value = $(this).text();
        
        $('.parentFolders-select').each(function(){
            if($(this).children('input').length > 0) {
                var tempVal = $(this).children('input').val();
                $(this).remove('input');
                $(this).text(tempVal);
            }
        });
        
        $(this).text('');
        $(this).append('<input type="text" value="' + value + '" class="edit-header" />');
        $(this).children('input').focus();
        $(this).children('input').val($(this).children('input').val()); // This sets the text cursor of the input at the end of the value.
    });
    
    $(document).on('keypress','input.edit-header',function(e){
        if(e.which == 13) {
            $(this).trigger('blur');
        }
    });
    
    $(document).on('blur','.edit-header',function(){
        var value = $(this).val();
        var cell = $(this).parents('td');
        var url = $(this).parents('td').parents('tr').parents('table').siblings('input[type="hidden"]').val();
        var type = $(cell).attr('class');
        var typeArr = type.split(' ');
        var data = null;
        var notif = null;
        
        editMode = false;
        
        if(typeArr[0] === 'parentFolders-select') {
            data = {FolderName : value,ID : $(this).parents('td').attr('id')};
            notif = $('#folder-heading-notif');
        }
        
        $.ajax({
            url : url,
            data : data,
            dataType : 'html',
            type : 'POST',
            beforeSend : function() {
                notif.show();
            },
            success : function(data) {
                notif.text('Updated.');
            },
            error : function(xhr,status,err) {
                $('#error').html(xhr.responseText);
            },
            complete: function () {
                var prevSpanText = $(prevHTML)[0]; 
                var newHTML = prevHTML.replace($(prevSpanText).text(),value);
                newHTML.replace('style=""','style="display:none;"');
                $(cell).remove('input');
                $(cell).html(newHTML);
                setTimeout(function () {
                    notif.hide();
                }, 3000);
            }
        });
        refreshTabFolders();
    });
    
    $(document).on('mouseover','.go-contents-table > tbody > tr > td',function(){
        $(this).children('a').show();
    });
    
    $(document).on('mouseleave','.go-contents-table > tbody > tr > td',function(){
        $(this).children('a').hide();
    });
    
    $(document).on('click','.go-archiver',function(e){
        e.stopPropagation();
        
        var d = confirm("Continue to archive this heading?");
        if(!d) {
            return false;
        }
        
        var type = $(this).attr('id');
        var url = $(this).attr('url');

        var folderID = null;
        var contentID = null;
        var table = null;
        var notif = null;
        var data = null;
        var hideContainer = null;
        var hideContainer2 = null;
        
        $('.parentFolders-select').each(function(){
            if($(this).hasClass('selected')) {
                folderID = $(this).attr('id');
            }
            table = $("#subfolders-list");
            resetSubfolderFields();
        });
        
        $('.subFolders-select').each(function(){
            if($(this).hasClass('selected')) {
                contentID = $(this).attr('id');
            }
        });
        
        if(type === 'parentFolders-archive') {            
            table = $('#parentFolders-list');
            notif = $('#folder-heading-notif');
            hideContainer = $('#contents-container');
            hideContainer2 = $('#content-details-container');
            data = {
                ID: $(this).parents('td').attr('id'),
                titleID: $('#titleID').val()
            };

        } else if(type === 'subFolders-archive') {
            table = $('#subfolders-list');
            notif = $('#subfolder-heading-notif');
            hideContainer = $('#content-form-container');
            hideContainer2 = $('#content-details-container');
            data = {
                ID : $(this).parents('td').attr('id'),
                titleID : $('#titleID').val(),
                folderID : folderID
            };
        } else if(type === 'contentDetails-archive') {
            table = $('#content-details-list');
            notif = $('#content-details-notif');
            hideContainer = $('#content-detail-container');
            data = {
                ID : $(this).parents('td').attr('id'),
                ContentID : contentID
            };
        } else {
            table = $('#tabs-list');
            notif = $('#tabs-notif');
            hideContainer = $('#title-tab-content');
            data = {
                ID : $(this).parents('td').attr('id'),
                titleID : $('#titleID').val()
            };
        }
        
        $.ajax({
            url : url,
            data : data,
            dataType : 'json',
            type : 'POST',
            beforeSend : function(){
                notif.html('Archiving...');
                notif.show();
            },
            success : function(data){
               $(table).children('tbody').html(data.body);
               notif.html('Archived');
            },
            error : function(xhr,status,err){
                $('#error').html(xhr.responseText);
            },
            complete : function(){
                if(type === 'tabs-archive') {
                    resetLocalContentTabPanels();
                    resetGlobalContentTabPanels();
                } else if(type === 'parentFolders-archive'){
                    refreshTabFolders();
                    hideContainer.hide();
                    hideContainer2.hide();
                    $(".tab-select").each(function(){
                        if($(this).hasClass("selected")) {
                            $(this).trigger("click");
                        }
                    });
                    $('#subfolders-list tbody').html('<tr><td id="empty-table">Nothing found...</td></tr>');
                } else if(type === 'subFolders-archive'){
                    hideContainer2.hide();
                    hideContainer.hide();
                } else {
                    hideContainer.hide();
                }
                resetGeneralTabPanels();
                setTimeout(function () {
                    notif.hide();
                }, 3000);
            }
        });
        
        
    });
});

/**
 * Handles the AJAX submission of forms with class '.add-heading-form'.
 * 
 * @param {DOM object} form
 * @param {DOM object} target
 * @returns {void}
 */
function addHeadingForm(form, target, data) {
    var submitBtn = $(form).children('div.form-group').children('input[type="submit"]');
    var alertBox = $(form).siblings('div.alert');
    var inputVal = $(form).find('input[type="text"]').val();
    if(!inputVal) {
        alertBox.children('span').text('Add heading text.');
        alertBox.prop('class','alert alert-box alert-danger');
        alertBox.show();
        setTimeout(function(){
            alertBox.hide();
        },3000);
        return false;
    }
    
    $.ajax({
        url: $(form).attr('action'),
        data: data,
        dataType: 'json',
        type: 'POST',
        beforeSend: function () {
            $(submitBtn).prop('disabled', true);
            $(submitBtn).val('. . .');
        },
        success: function (data) {
            $(target).children("tbody").html(data.body);
        },
        error: function (xhr, status, error) {
            $("#error").html(xhr.responseText);
        },
        complete: function () {
            $(submitBtn).prop('disabled', false);
            $(submitBtn).val('Add');
            $(form).find('input[type="text"]').val("");
            $(target).children('tbody').children('tr').children('td.selected').trigger('click');
        }
    });
}

function saveDetailForm(form) {
    var submitBtn = $(form).find('input[type="submit"]');
    var alertBox = null;
    var target = null;
    var rowClass = null;
    if($(form).attr('id') === 'subfolder-details') {
        alertBox = $('#subfolder-alert');
        target = $('#subfolders-list tbody');
    } else if($(form).attr('id') === 'title-tab-details') {
        target = $('#tabs-list tbody');
        rowClass = ".tab-select";
    } else {
        alertBox = $('#content-detail-alert');
        target = $('#content-details-list tbody');
    }

    $.ajax({
        url : $(form).attr('action'),
        data : $(form).serialize(),
        dataType : 'html',
        type : 'POST',
        beforeSend : function(){
            $(submitBtn).val('Saving...');
            $(submitBtn).prop('disabled',true);
        },
        success : function(resp) {
            target.html(resp);
        },
        error : function(xhr,status,err) {
            $("#error").html(xhr.responseText);
        },
        complete : function() {
            $(submitBtn).val('Save');
            $(submitBtn).prop('disabled',false);
            
            $(rowClass).each(function(){
                if($(this).hasClass("selected")) {
                    $(this).trigger("click");
                    return false;
                }
            });
        }
    });
}

/**
 * Function that sets the values in the static form
 * @param {array} data
 * @returns {void}
 */
function fillFormFields(data) {
    $('#ID').val(data.ID);
    $('#dispID').val(data.ID);
    $('#TypeID').val(data.TypeID);
    $('#Visibility').val(data.Visibility);
    $('#Active').val(data.Active);
    $('#CMS_Notes').val(data.CMS_Notes);
}

function fillSubfolderFields(data) {
    $('#contentID').val(data.ID);
    $('#contentID-hidden').val(data.ID);
    $('#ContentHeading').val(data.ContentHeading);
    $('#CMS_Name').val(data.CMS_Name);
    $('#ContentTypeID').val(data.ContentTypeID);
    $('#Global').val(data.Global);
    $('#CMS_Notes').val(data.CMS_Notes);
    $('#ContentData').val(data.ContentData);
}

function resetSubfolderFields() {
    $('#contentID').val("");
    $('#contentID-hidden').val("");
    $('#ContentHeading').val("");
    $('#CMS_Name').val("");
    $('#ContentTypeID').val("");
    $('#Global').val("");
    $('#CMS_Notes').val("");
    $('#ContentData').val("");
}

function refreshTabFolders() {
    var url = $("#get-tabfolder-url").val();
    var titleID = $("#titleID").val();
    $.ajax({
        url : url,
        data : {titleID : titleID},
        type : "POST",
        dataType : "html",
        beforeSend : function() {
            $("#localContent-files-body").html("Loading...");
        },
        success : function(data) {
            $("#localContent-files-body").html(data);
        },
        error : function(xhr,status,err) {
            alert(status + ": " + err);
        }
    });
}

function resetGeneralTabPanels() {
    $("#tab-info-panel").html("");
    $("#tab-options-panel").html("");
    $("#tab-access-panel").html("");
    $("#tab-text-panel").html("");
    $("#title-tab-details").find("input.btn").hide();
}

function resetLocalContentTabPanels() {
    $("#localContent-filesContent-body").html("");
    $("#localContent-contentAdded-table tbody").html("<tr><td>Nothing found...</td></tr>");
    $(".tab-folders").removeClass("tab-folders-active");
}

function resetGlobalContentTabPanels() {
    $("#content-added-table tbody").html("<tr><td>Nothing found...</td></tr>");
    $("#global-content-body").html("");
}

function resetLinkedTabContents() {
    $("#localContent-contentAdded-table tbody").html("<tr><td>Nothing found...</td></tr>");
}