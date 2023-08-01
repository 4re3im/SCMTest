<?php 
	if (isset( $_POST['searchstring']) ) {
		$title = 'should be the name of ' . $_POST['searchstring'];
	} else {
		$title = 'User Subscriptions';		
	}
	echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(
	t($title), false, false, false) 
?>

	<div style="clear:both;"></div>
	
	<div class="ccm-pane-body">
	
		<div id="searchform" style="" align="center">	 
		 	<form method="post" action="" method="post">
		 		<table width="100%" cellpadding="5"; cellspacing="5" border="1">
		 		<tr>
		 			<?php if ($_POST['searchstring'] != '') { ?>
			 			<td align="left">	
			 			<a href="">	
			 			<input type="button" class="btn primary" value="<?php echo t(' << Show Summary')?>" /> 	            	
			 			</a>	
			 			</td>	
		 			<?php } ?>
	                <td align="right"><input type="text" name="searchstring" />               
		            
		            	<input type="submit" class="btn primary" value="<?php echo t('Search User Subscriptions')?>" /> 	            	
		            </td>
		        </tr>
	            </table>
            </form>
         </div> 
         
         <div style="clear:both"></div>
		
         <?php			
         	if ($_POST['searchstring'] != '') {                 		
         		Loader::packageElement('go_usersubscriptions/usersubscription_search', 'go_dashboard', array('records' => @$records,
         																			'tabs' => @$tabs		
         															) 
         								); 
         	} else {
				Loader::packageElement('go_usersubscriptions/usersubscription_summary', 'go_dashboard', array('subscriptionlist' => @$subscriptionlist)); 
    		}

    		?>

		    <div style="clear:both;height:5px;width:100%"></div>
	</div>
	    
	<div style="clear:both"></div>
	
		<div class="ccm-pane-footer">
		<?php  if ( !isset($_POST['searchstring']) ) { echo $subscriptionlistPagination;	} ?>
	</div>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>

	