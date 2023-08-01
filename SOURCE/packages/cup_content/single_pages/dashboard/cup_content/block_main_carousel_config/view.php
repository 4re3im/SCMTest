<?php 
	defined('C5_EXECUTE') or die(_("Access Denied."));
	$ih = Loader::helper('concrete/interface');
?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Main Carousel Config'), false);?>

<div>
	<a href="<?php  echo View::url('/dashboard/cup_content/block_main_carousel_config/edit')?>" style="float: right; position:relative;top:-5px"  class="btn primary"><?php  echo t("EDIT")?></a>
</div>


<div>
	<table>
		<tr>
			<td style="padding-right: 10px;">Carousel Slide Interval: </td>
			<td><?php echo $carousel_config['interval'];?> sec</td>
		</tr>
	</table>
	
	<div style="width:1px;height:30px;"></div>
	<table>
		<thead>
			<tr>
				<th><strong>Entry ID</strong></th>
				<th><strong>Content</strong></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($carousel_config['carousel_data'] as $idx => $each_entry):?>
			<tr>
				<td style="vertical-align:top; font-size: 14px;">#<?php echo $idx;?></td>
				<td style="vertical-align:top; padding-bottom: 10px;">
					<table>
						<tr>
							<td style="text-align:right; padding: 5px;">Is Enabled:</td>
							<td><?php if($each_entry['enable']){ echo 'YES';}else{echo 'NO';}?></td>
						</tr>
						<tr>
							<td style="text-align:right; padding: 5px;">Title:</td>
							<td><?php echo $each_entry['title'];?></td>
						</tr>
						<tr>
							<td style="text-align:right; padding: 5px;">Description</td>
							<td><?php echo $each_entry['description'];?></td>
						</tr>
						<tr>
							<td style="text-align:right; padding: 5px;">Image URL</td>
							<td><?php echo $each_entry['image'];?></td>
						</tr>
						<tr>
							<td style="text-align:right; padding: 5px;">Link URL</td>
							<td><?php echo $each_entry['link'];?></td>
						</tr>
					</table>
				</td>
			</tr>
			<?php endforeach;?>
		</tbody>
	</table>
</div>

<div style="clear:both"></div>
<?php  echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(true);?>
