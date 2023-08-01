<style>
    .panel { margin:  0px 8px; height: 100%; }

    .panel-default p.header {
        padding-bottom: 5px;
        font-size: 14px;
        font-weight: bold;
        border-bottom:  1px solid #333;
    }

    .panel-default {

        border:  1px solid #ccc;
        padding: 10px;
        margin:  8px;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        border-radius: 5px 5px 5px 5px;  
        min-height: 100px;        
        height: 100%;


    }

</style>

<div class="panel">	 
 	<!-- <form method="post" action="" method="post"> -->
 		<table width="100%" cellpadding="5"; cellspacing="5" border="1">
 		<tr> 			
            <td align="left">            	
            	<input type="button" id="reload-tracking-general" class="btn primary" value="<?php echo t('Reload Tracking General')?>" /> 	            	
            </td>
        </tr>
        </table>
    <!-- </form> -->
 </div> 
 
 <div style="clear:both"></div>

 <div class="panel-default">    
    <p class='header'><strong>Tracking - General</strong></p>

    <div id="tracking-general">
    <table id="ccm-product-list" class="ccm-results-list" cellspacing="0" cellpadding="0" border="0" >
        <thead>
            <tr>
                <th>Date</th>
                <th>Page Name</th>  
                <th>Action</th>
                <th>Info</th>                                       
                <th>Access Level</th>
            </tr>
        </thead>
        <tbody>
            <?php                  
                if (isset($userTrackingGeneral) && !empty($userTrackingGeneral) ) {
                    foreach ($userTrackingGeneral as $userTrackingGeneralList) {                               
            ?>
                        <!-- <tr class="ccm-list-record" onclick="location.href='<?php //echo $this->action("search", $userSubscription['uID'] ); ?>'">                             -->
                        <tr class="ccm-list-record">                            
                            <td><?php echo $userTrackingGeneralList['CreatedDate']; ?></td>
                            <td><?php echo $userTrackingGeneralList['PageName']; ?></td>
                            <td><?php echo $userTrackingGeneralList['Action']; ?></td>     
                            <td><?php echo $userTrackingGeneralList['Info']; ?></td>       
                            <td><?php echo $userTrackingGeneralList['Access_Level']; ?></td>                                                                                                     
                            
                        </tr>
                        

            <?php   }
                                       
                } ?>           
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">
                    <?php  if (isset($userSubscriptionPagination)) { echo $userSubscriptionPagination; }  ?>                          
                </td>
            </tr>
            <tr>
                
            </tr>
        </tfoot>
    </table>
    </div>

</div>

<script type="text/javascript">

    $(function() {   



        // Go User - Subscriptions - Add Subscriptions
        $('input#reload-tracking-general').click(function(e) {            
                   
           var user_id = $('#user_id').val();

                 

            $.ajax({
                type: 'POST',
                data: "&user_id="+ user_id + "&is_ajax=yes&func=tracking-general",                
                url: "<?php print str_replace("&","&",$this->action('search')); ?>",
                success: function(data){
                
                    if (data != '') {                        
                        $('div#tracking-general').html(data);
                    }
                },

                error:function (xhr, ajaxOptions, thrownError){
                    $('#tracking-general').html(xhr.status+"<br/>"+thrownError);
                }

            });


            e.preventDefault();
            e.stopPropagation();



        });     

    });

</script>
