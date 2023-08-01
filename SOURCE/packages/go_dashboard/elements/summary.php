<table id="ccm-product-list" class="ccm-results-list" cellspacing="0" cellpadding="0" border="0">
    <thead>
        <tr>
            <th><?php echo t('User') ?></a></th>		        
            <th><?php echo t('Email') ?></th>
            <th><?php echo t('Creation Date') ?></th>		                		        
            <th><?php echo t('Info') ?></th>
        </tr>
    </thead>
    <tbody>
    	<?php				        		
        	if (isset($codefaillist) && !empty($codefaillist) ) {
    			foreach ($codefaillist as $codefail) { 		        				
    	?>
					<tr class="ccm-list-record"> 
	                    <td><?php echo $codefail->uID . " - " . $codefail->uName; ?></td>				                    		                   				            		                   
	                    <td><?php echo $codefail->uEmail; ?></td>				                    
	                    <td><?php echo $codefail->CreatedDate; ?></td>				                   
	                    <td><?php echo $codefail->Info; ?></td>				                   				                 
                	</tr>

    	<?php	}
    	 				           
        	} ?>           
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3">
            	<?php  //if (isset($titlesLists)) { echo $titlesListsPagination; } else { echo $titlesPagination; } ?>		                    
            </td>
        </tr>
        <tr>
            
        </tr>
    </tfoot>
</table>