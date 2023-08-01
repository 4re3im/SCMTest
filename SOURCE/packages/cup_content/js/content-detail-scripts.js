/**
 * Handles js functionality for content detail modal.
 * So rather than placing js functions within the modal itself, it will be placed here
 * to avoid misfiring of js and slow loading of the modal.
 * 
 */
$(document).ready(function(){
    
    
    
    
    
    
    
    
    /**
     * Handles content detail updates.
     */
    $(document).on('submit','#content-detail-form',function(e){
        e.preventDefault();
        if ($('.selected').length > 0) {
            $.ajax({
                url: $(this).attr("action"),
                data: $(this).serialize(),
                dataType: "html",
                type: "POST",
                beforeSend: function () {
                    $('#save-content-detail-trigger').prop("disabled", true);
                    $('#save-content-detail-trigger').val("Saving...");
                },
                success: function (data) {
                    alert("Details saved.");
                    $('#sortable').html(data);
                },
                error: function (xhr, status, err) {
                    $("#error").html(xhr.responseText);
                },
                complete: function () {
                    $('#save-content-detail-trigger').prop("disabled", false);
                    $('#save-content-detail-trigger').val("Save");
                    $('.content-detail-heading').each(function () {
                        if ($(this).hasClass('selected')) {
                            $(this).trigger('click');
                            return false;
                        }
                    });
                }
            });
        } else {
            alert('Please select a heading.');
        }
        
    });
    
});
