/**
 * provision js controls
 */
$(document).ready(function(){
    
    // Add events
    var files; 

    $('#file').unbind('change'); $('form').unbind('submit');

    $('#file').on('change', function(event){ files = event.target.files; });

    // Catch the form submit and upload the files
    $('#process_account').on('click', function(event){

        if($('#file').val()){
            
            $('div.ccm-result').hide();
            
            $('div.ccm-warning').hide();

            $('div.ccm-loading').show();

            event.stopPropagation(); // Stop stuff happening

            event.preventDefault(); // Totally stop stuff happening  

            // Create a formdata object and add the files
            var data = new FormData();
        
            $.each(files, function(key, value){ data.append(key, value); }); 
            
            data.append('user_only', $('#user_only').attr('checked')=='checked' ? 1 : 0);
            
            $.ajax({

                url: upload_file, type: 'POST', data: data, cache: false,

                dataType: 'json', processData: false, contentType: false,

                success: function(data){
                    
                    $('div.ccm-loading').hide();
                    
                    if(data){

                        if(data['warning']){ $('.ccm-warning').show().html(data['warning']);
                                       
                        }else{ $('div.ccm-result').show(); createDataTable(data); }

                    }else{

                        $('table.table tbody').html('<tr><td>No data found</td></tr><tr><td>'+data+'</td></tr>');

                        $('div.summary').html('<tr><td>No data found</td></tr>');

                    }
                    
                },error: function(XMLHttpRequest, textStatus, errorThrown) { 

                    $('div.ccm-loading').hide();
                    
                    $("#error").html(XMLHttpRequest.responseText);

                    // console.log("Status: " + XMLHttpRequest); 

                }  

            });
        }

    });
    
    /**
     * Searching for products in CMS product
     */
    var typingTimer, doneTypingInterval = 600;

    $('#search_product').on('keydown', function( e ){ $('#result').hide(); });

    $('#search_product').on('keyup', function( e ){

        clearTimeout(typingTimer);
        
        if($(this).val().length > 5){
            
            typingTimer = setTimeout(function(){
                
                $('#search').removeClass('search-icon').addClass('loading-icon');
                
                $.ajax({

                   url: search_product, data: {product_name : $('#search_product').val()},

                   success: function(data){

                       $('#result').html(jQuery.parseJSON(data)).show(); 
                       
                       $('#search').removeClass('loading-icon').addClass('search-icon');

                        /**
                         * Add User Subscription
                         */
                        
                        $('div#result ul').unbind('click');
                        
                        $('div#result ul').on('click', 'li', function( e ){
                            
                            $('#result').hide();
                            
                            $('#search_product').val($(this).html());
                            
                            $('#search_id').val($(this).attr('id'));
                            
                            $('#assign').unbind('click');
                            
                            $('#assign').on('click', function( e ){

                                var user_id = '';

                                $(".provision_checkbox:checked").each(function(){
                                    user_id += (user_id =='' ? this.id : '|'+this.id); 
                                });

                                var data = {search_id:$('#search_id').val(), user_id:user_id};
                                $.ajax({

                                   url: add_user_subscription, data: data,

                                   success: function(d){
                                       // $("#error").html(jQuery.parseJSON(d));
                                        createDataTable(jQuery.parseJSON(d));
                                       user_id = '';
                                   },
                                   error : function(xhr, status, data) {
                                       $("#error").html(xhr.responseText);
                                   }

                                });
                                
                            });

                        });
                   
                   }
                   
                });

            }, doneTypingInterval);
        }

    });
    
    //refresh provision state
    $('#refresh').click(function(){
        
        $("#search_product").val('');
        
        var input = $("#file");
        
        $('.ccm-div').hide();
        
        input.replaceWith(input.val('').clone(true));
        
    });
    
    //check/uncheck of heck box 
    $('#check_all').click(function(event) {  //on click 

        if(this.checked) { // check select status
           
            $("input.provision_checkbox").attr("checked",true);
            
        }else{
            
            $("input.provision_checkbox").attr("checked",false);
      
        }
        
    });
   
}).click(function (e){
   
    var container = $('#search_wrapper');

    if (!container.is(e.target) && container.has(e.target).length === 0) $('#result').hide();
    
});

var createDataTable = function(data){
    
    $('#provision').dataTable().fnDestroy();

    $('table#provision tbody').replaceWith('<tbody>' + data['provision_lists'] + '</tbody>');
    
    //$('table#provision tbody').html(data['provision_lists']);
    
    $('div.summary').html(data['summary']);
                   
    $('#provision').dataTable({
        
         'oLanguage': {

           "sSearch": "Filter table: "

        },
         
        "aoColumnDefs": [
          { 'bSortable': false, 'aTargets': [ 0 ] }
        ],
        
        "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ]

     });
                    
    $('table#provision tbody').on( 'click', 'tr', function () {

         if ( $(this).hasClass('selected') ) {

             $(this).removeClass('selected');

         } else {

             $('tr.selected').removeClass('selected');

             $(this).addClass('selected');

         }

     } );
     
    //to remove the up down arrow in checkbox
    $('#check_all').parent('th.sorting_asc').removeClass('sorting_desc').removeClass('sorting_asc');

}