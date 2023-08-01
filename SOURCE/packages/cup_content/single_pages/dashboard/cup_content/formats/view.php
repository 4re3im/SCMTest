<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Formats'), false);?>
<?php Loader::packageElement('alert_message_header', 'cup_content'); ?>

<div>
	<a href="<?php  echo View::url('/dashboard/cup_content/formats/add')?>" style="float: right; position:relative;top:-5px"  class="btn primary"><?php  echo t("New Format")?></a>
</div>


<?php if(count($formats) > 0):?>
	<p><i>#This list is sorted by name in alphabetical order</i></p>
	
	<table border="0" cellspacing="0" cellpadding="0" id="ccm-product-list" class="ccm-results-list">
		<thead>
			<tr>
				<th>Icon</th>
				<th width="200"><?php echo t('Name')?></th>
				<th>Pretty Url</th>
				<th>Short Description</th>
				<th>Long Description</th>
				<th>Modified At</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($formats as $idx=>$format):?>
				<tr ref="<?php echo $format->id;?>">
					<td><img src="<?php echo $format->getImageURL();?>"/></td>
					<td><?php echo $format->name;?></td>
					<td><?php echo $format->prettyUrl;?></td>
					<td><?php echo $format->shortDescription;?></td>
					<td><?php echo $format->longDescription;?></td>
					<td><?php echo date('Y-m-d H:i:s', strtotime($format->modifiedAt));?></td>
					<td>
						<a href="<?php echo $this->url('/dashboard/cup_content/formats/edit', $format->id);?>">EDIT</a>
						&nbsp;&nbsp;
						<a href="javascript:deleteItem(<?php echo $format->id;?>);">DELETE</a>
					</td>
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>
	
	<script>
		function deleteItem(ft_id){
			var action_url = "<?php echo rtrim($this->url('/dashboard/cup_content/formats/delete'), "/");?>";
			
			var ft = jQuery('tr[ref="' + ft_id + '"]');
			var ft_name = ft.find('td').eq(0).html();
			var r = confirm("Are you sure to delete '"+ ft_name +"'?\nThis action cannot be undone.");
			if(r == true){
				action_url = action_url+"/"+ft_id;
				jQuery.getJSON(action_url, function(json){
					if(json.result == 'success'){
						ft.remove();
					}else{
						alert(json.error);
					}
				});
			}
		}
	</script>
<?php else:?>
	<div id="ccm-list-none"><?php echo t('No Formats found.')?></div>
<?php endif;?>


<div style="clear:both"></div>
<?php  echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>