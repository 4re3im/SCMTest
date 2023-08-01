<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Sample Pages'), false);?>
<?php Loader::packageElement('alert_message_header', 'cup_content'); ?>

<div>
	<a href="<?php  echo View::url('/dashboard/cup_content/titles/new_sample_page/'.$title_id)?>" style="float: right; position:relative;top:-5px"  class="btn primary"><?php  echo t("New Sample Page")?></a>
	<a href="<?php echo $this->url('/dashboard/cup_content/titles/show', $title_id)?>" class="btn"><?php echo t('Back to Title')?></a>
</div>
<div style="clear:both;width:1px;height:20px;"></div>

<div id="page_content">
	<?php //Loader::packageElement('title/dashboard_search', 'cup_content', array('titles' => $titles, 'titleList' => $titleList, 'pagination' => $pagination));?>
	<?php Loader::packageElement('title_sample_page/dashboard_list', 'cup_content', array('samplePages' => $samplePages, 'samplePageList' => $samplePageList, 'pagination' => $pagination));?>
	<div style="clear:both;height:5px;width:100%"></div>
</div>

<div style="clear:both"></div>
<?php  echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>



<script>
	function deleteItem(at_id){
		var action_url = "<?php echo rtrim($this->url('/dashboard/cup_content/titles/delete_sample_page'), "/");?>";
		
		var at = jQuery('tr[ref="' + at_id + '"]');
		var at_name = at.find('td').eq(0).text();
		var r = confirm("Are you sure to delete '"+ at_name +"'?\nThis action cannot be undone.");
		if(r == true){
			action_url = action_url+"/"+at_id;
			jQuery.getJSON(action_url, function(json){
				if(json.result == 'success'){
					at.remove();
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