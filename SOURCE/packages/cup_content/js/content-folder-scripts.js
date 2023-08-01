/* 
 * Handles js functionality in the Content folder modal
 */
$(document).ready(function(){
    $(document).on('submit','#new-folder',function(e){
        e.preventDefault();
        var form = $(this);
        var newFolderHdg = $(form).children('div.form-group').children('input[type="text"]');
        var addBtn = $(form).children('div.form-group').children('input[type="submit"]');
        
        if(!$(newFolderHdg).val()) {
            $('#alert-text').text("Please put a heading before saving.");
            $('#content-folder-alert').addClass('alert-warning');
            $('#content-folder-alert').show();
            return false;
        }
        
        $.ajax({
            url : $(form).attr('action'),
            data : $(form).serialize(),
            dataType : 'html',
            type : 'POST',
            beforeSend : function(){
                $(addBtn).prop('disabled',true);
                $(addBtn).val('Saving');
            },
            success : function(data) {
                $('#popup-selected-area tbody').html(data);
            },
            error : function(xhr,status,err) {
                $('#error').html(xhr.responseText);
            },
            complete : function() {
                $(addBtn).prop('disabled',false);
                $(addBtn).val('Add');
                $(newFolderHdg).val('');
                $('popup-selection-item').each(function(){
                    if($(this).hasClass('selected')) {
                        $(this).trigger('click');
                        return false;
                    }
                });
                $('#alert-text').text("");
                $('#content-folder-alert').prop('class','alert');
                $('#content-folder-alert').hide();
            }
        });
    });
});

