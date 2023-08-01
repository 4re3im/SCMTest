<?php 
	defined('C5_EXECUTE') or die(_("Access Denied."));
	$ih = Loader::helper('concrete/interface');
 
	$form = Loader::helper('form');
?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Main Sidebar Config'), false);?>
<?php Loader::packageElement('alert_message_header', 'cup_content'); ?>
<form method="post" action="<?php echo $this->url('/dashboard/cup_content/config/block_type_main_sidebar')?>">

<div>
	<h3>Side Bar Quick Link</h3>
	<div style="width:1px;height:30px;"></div>
	
	<div>
		Title:	<input type="text" name="title" value="<?php echo str_replace('"', '/"', $config['title']);?>"/>
	</div>
	
	<div style="width:1px;height:30px;"></div>
	<table>
		<thead>
			<tr>
				<th><strong>#</strong></th>
				<th><strong>Content</strong></th>
				<th><strong>Url</strong></th>
				<th><strong>Region</strong></th>
			</tr>
		</thead>
		<tbody>
			<?php for($i=0; $i<10; $i++):?>
			<tr>
				<?php
					$content="";
					$url="";
					$region='ALL';
					if($config && isset($config['items'][$i])){
						$content = $config['items'][$i]['content'];
						$url = $config['items'][$i]['url'];
						$region = $config['items'][$i]['region'];
					}
				?>
				<td style="vertical-align:top; font-size: 14px;">#<?php echo $i+1;?></td>
				<td style="vertical-align:top; padding-bottom: 10px;padding:0px 5px;">
					<input type="text" name="content[]" value="<?php echo str_replace('"', '/"', $content);?>"/>
				</td>
				<td style="vertical-align:top; padding-bottom: 10px;">
					<input type="text" name="url[]" value="<?php echo str_replace('"', '/"', $url);?>" style="width:300px"/>
				</td>
				<td style="vertical-align:top; padding-bottom: 10px;padding:0px 5px;">
					<?php
						$region_options = array(
							'ALL' => 'All',
							'AU' => 'Australia',
							'NZ' => 'New Zealand'
						);
					?>
					<?php echo $form->select('region['.$i.']', $region_options, $region);?>
				</td>
			</tr>
			<?php endfor;?>
		</tbody>
	</table>

</div>

<a href="<?php echo $this->url('/dashboard/cup_content/config')?>" class="btn"><?php echo t('Back')?></a>
<input type="submit" class="ccm-button-right btn primary accept" value="<?php echo t('Save')?>"/>
</form>

<div style="clear:both"></div>
<?php  echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(true);?>
