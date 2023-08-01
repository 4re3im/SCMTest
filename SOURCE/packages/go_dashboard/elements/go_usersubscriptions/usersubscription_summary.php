<table id="ccm-product-list" class="ccm-results-list" cellspacing="0" cellpadding="0" border="0" >
    <thead>
        <tr>
            <th><?php echo t('Creation Date') ?></th>
            <th><?php echo t('ID') . '-' .  t('Full Name') ?></th>  
            
             		        
            <th><?php echo t('Title') ?></th>

<!--             <th><?php //echo t('Position') ?></a></th>               -->
            <th><?php echo t('Email') ?></th>
            <th><?php echo t('Phone Number') ?></th>                                       
            <!-- <th><?php //echo t('State') ?></th> -->

            <th><?php echo t('School') ?></a></th>              
            <!-- <th><?php //echo t('Subjects Taught') ?></th> -->
<!--             <th><?php //echo t('Promotion by Email') ?></th>                                       
            <th><?php //echo t('Promotion by Regular Post') ?></th>
 -->
            <th><?php echo t('SalesForce Flag') ?></th>

            <th></th>

        </tr>
    </thead>
    <tbody>
    	<?php	
            //var_dump($teacherlist);			        		
        	if (isset($teacherlist) && !empty($teacherlist) ) {
    			foreach ($teacherlist as $list) { 		        				
    	?>
					<tr class="ccm-list-record"> 
                        <td><?php echo $list->uDateAdded; ?></td>
	                    <td><?php echo $list->uID . '-' . $list->uName; ?></td>				                    		                   				            		                   
                        
                        
	                                 
                        <td><?php echo ''; ?></td>     
                        <td><?php echo $list->uEmail; ?></td>                                                                                                            
                        <td><?php echo ''; ?></td>
                        <td><?php echo ''; ?></td>
                                                      
                        <td> <input type="checkbox" /></td>     

                        <td> <input type="button" name="view" class="btn primary" value="VIEW" /></td>                                                           
                        
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