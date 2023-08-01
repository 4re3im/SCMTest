<?php
	$ih = Loader::helper('concrete/interface');
	
	$this->addHeaderItem($html->css('jquery.wspecial.css', 'cup_competition')); 
	$this->addHeaderItem($html->javascript('jquery.wspecial.js', 'cup_competition'));
?>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Competition Event Entries'), false);?>

<?php Loader::packageElement('alert_message_header', 'cup_competition'); ?>

<div style="text-align:right;height: 60px;">
	<a href="<?php echo $this->url("/dashboard/cup_competition/entry/exportByEvent", $eventID);?>" class="btn primary">EXPORT</a>
</div>
<!--
<div>
	<a href="<?php  echo View::url('/dashboard/cup_competition/event/add')?>" style="float: right; position:relative;top:-5px"  class="btn primary"><?php  echo t("New Event")?></a>
	<div class="span4">
		<form class="ajax_form" action="<?php  echo View::url('/dashboard/cup_competition/event')?>" method="get">
			<input type="hidden" name="ajax" value="yes"/>
			Keywords: <input type="text" name="keywords"/>
		<form>
	</div>
</div>
-->
<div style="clear:both;"></div>

<div id="page_content">
	<?php Loader::packageElement('event_entry/dashboard_search', 'cup_competition', array('entryList' => $entryList));?>
	<div style="clear:both;height:5px;width:100%"></div>
</div>


<div style="clear:both"></div>
<?php  echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(true);?>



<script>
	function deleteItem(sr_id){
		var action_url = "<?php echo rtrim($this->url('/dashboard/cup_competition/entry/delete'), '/');?>";
		
		var sr = jQuery('tr[ref="' + sr_id + '"]');
		//var sr_name = sr.find('td').eq(0).html();
		var r = confirm("Are you sure to delete 'ID: "+ sr_id +"'?\nThis action cannot be undone.");
		if(r == true){
			action_url = action_url+"/"+sr_id;
			jQuery.getJSON(action_url, function(json){
				if(json.result == 'success'){
					sr.remove();
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
