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

 <div class="panel-default">    
    <p class='header'><strong>Activation Errors</strong></p>    

    <table id="ccm-product-list" class="ccm-results-list" cellspacing="0" cellpadding="0" border="0" >
        <thead>
            <tr>
                <th>CreatedDate</th>
                <th>Info</th>                                       
            </tr>
        </thead>
        <tbody>
            <?php                  
                if (isset($userActivationErrors) && !empty($userActivationErrors) ) {
                    foreach ($userActivationErrors as $userActivationError) {                               
            ?>
                     <!--   <tr class="ccm-list-record" onclick="location.href='<?php// echo $this->action("search", $userActivationError['uID'] ); ?>'">                           //-->
                        <tr class="ccm-list-record">                            
                            <td><?php echo $userActivationError['CreatedDate']; ?></td>
                            <td><?php echo $userActivationError['Info']; ?></td>       
                        </tr>
                        

            <?php   }
                                       
                } ?>           
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">
                    <?php  //if (isset($userSubscriptionPagination)) { echo $userSubscriptionPagination; }  ?>                          
                </td>
            </tr>
            <tr>
                
            </tr>
        </tfoot>
    </table>

</div>
