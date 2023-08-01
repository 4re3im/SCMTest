<?php 
	defined('C5_EXECUTE') or die(_("Access Denied."));
	$ih = Loader::helper('concrete/interface');
 
	$form = Loader::helper('form');
?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('HSC Slider Config'), false);?>
<?php Loader::packageElement('alert_message_header', 'cup_competition'); ?>
<form method="post" action="<?php echo $this->url('/dashboard/cup_competition/config/hsc_slider')?>">

<div>
	<table>
		<tr>
			<td style="padding-right: 10px;">Carousel Slide Interval: </td>
			<td><?php echo $form->select('interval', 
									array('4'=>'4', '5'=>'5', '6'=>'6', 
										'7'=>'7', '8'=>'8', '9'=>'9', 
										'10'=>'10', '15'=>'15', '20'=>'20',
										'25'=>'25', '30'=>'30' ),
									$slider_config['interval'])?> sec</td>
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
			<?php foreach($slider_config['carousel_data'] as $idx => $each_entry):?>
			<tr>
				<td style="vertical-align:top; font-size: 14px;">#<?php echo $idx;?></td>
				<td style="vertical-align:top; padding-bottom: 10px;">
					<table>
						<tr>
							<td style="text-align:right; padding: 5px;">Is Enabled:</td>
							<td><?php echo $form->select('carousel_data['.$idx.'][enable]', 
									array('1'=>'YES', '0'=>'NO' ),
									$each_entry['enable'], array('style'=>'width:80px'))?></td>
						</tr>
						<tr>
							<td style="text-align:right; padding: 5px;">Title:</td>
							<td><?php echo $form->text('carousel_data['.$idx.'][title]', 
										$each_entry['title'], 
										array('style'=>'width:350px'))?></td>
						</tr>
						<tr>
							<td style="text-align:right; padding: 5px;">Description</td>
							<td><?php echo $form->text('carousel_data['.$idx.'][description]', 
										$each_entry['description'], 
										array('style'=>'width:350px'))?></td>
						</tr>
						<tr>
							<td style="text-align:right; padding: 5px;">Image URL</td>
							<td><?php echo $form->text('carousel_data['.$idx.'][image]', 
										$each_entry['image'], 
										array('style'=>'width:550px'))?></td>
						</tr>
						<tr>
							<td style="text-align:right; padding: 5px;">Link URL</td>
							<td><?php echo $form->text('carousel_data['.$idx.'][link]', 
										$each_entry['link'], 
										array('style'=>'width:550px'))?></td>
						</tr>
					</table>
				</td>
			</tr>
			<?php endforeach;?>
		</tbody>
	</table>

</div>

<a href="<?php echo $this->url('/dashboard/cup_competition/config')?>" class="btn"><?php echo t('Back')?></a>
<input type="submit" class="ccm-button-right btn primary accept" value="<?php echo t('Save')?>"/>
</form>

<div style="clear:both"></div>
<?php  echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(true);?>
