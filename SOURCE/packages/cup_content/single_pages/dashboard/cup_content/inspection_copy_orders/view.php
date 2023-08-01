<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Series'), false);?>
<?php Loader::packageElement('alert_message_header', 'cup_content'); ?>

<div>
	<div class="span4">
		<form class="ajax_form" action="<?php  echo View::url('/dashboard/cup_content/series/search')?>" method="get">
			<input type="hidden" name="ajax" value="yes"/>
				Keywords: <input type="text" name="keywords"/> 
				<!--
				Format: <input type="text" name="format"/>
				<input type="submit" name="submit" value="Submit"/>
				-->
		<form>
	</div>
</div>

<div style="clear:both;"></div>

<div id="page_content">
	<?php Loader::packageElement('inspection_copy/dashboard_search', 'cup_content', array('list' => $list));?>
	<div style="clear:both;height:5px;width:100%"></div>
</div>

<div style="clear:both"></div>
<?php  echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>
	
	<script>
	function deleteItem(order_id){
		var action_url = "<?php echo rtrim($this->url('/dashboard/cup_content/inspection_copy_orders/delete'), "/");?>";
		
		var row = jQuery('tr[ref="' + order_id + '"]');
		var r = confirm("Are you sure to delete order （ID: "+ order_id +")?\nThis action cannot be undone.");
		if(r == true){
			action_url = action_url+"/"+order_id;
			jQuery.getJSON(action_url, function(json){
				if(json.result == 'success'){
					row.remove();
				}else{
					alert(json.error);
				}
			});
		}
	}

	function gotoPage(dom, pageNumber){
		var ref = jQuery(dom).attr('href');
		if(ref.indexOf("ajax=yes") == -1){
			ref = ref+'&ajax=yes';
		}
		//alert(ref);
		jQuery('#page_content').addLoadingMask();
		jQuery.get(ref, 
			function(html_data){
				jQuery('#page_content').html(html_data);
				jQuery('#page_content').removeLoadingMask();
			}
		);
		return false;
	}
	
	function sortColumn(dom){
		//return true;
		var ref = jQuery(dom).attr('href');
		if(ref.indexOf("ajax=yes") == -1){
			ref = ref+'&ajax=yes';
		}
		
		//alert(ref);
		jQuery('#page_content').addLoadingMask();
		jQuery.get(ref, 
			function(html_data){
				jQuery('#page_content').html(html_data);
				jQuery('#page_content').removeLoadingMask();
			}
		);
		return false;
	}
	
	//ccm_setupInPagePaginationAndSorting();
	//ccm_setupSortableColumnSelection();
	
	jQuery('.ajax_form').submit(function(){
		var action_url = jQuery(this).attr('action');
		var submit_type = jQuery(this).attr('method');
		if(typeof(submit_type) === 'undefined' || submit_type === false){
			submit_type = 'GET';
		}
		
		jQuery('#page_content').addLoadingMask();
		jQuery.ajax({
			type: submit_type,
			url: action_url, 
			data: jQuery(this).serialize(),
			success: function(html_data){
				jQuery('#page_content').html(html_data);
				jQuery('#page_content').removeLoadingMask();
			}
		});
		
		return false;
	});
	</script>