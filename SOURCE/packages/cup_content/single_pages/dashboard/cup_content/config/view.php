<?php 
	defined('C5_EXECUTE') or die(_("Access Denied."));
	$ih = Loader::helper('concrete/interface');
?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Cup Content Config'), false);?>

<?php
	$url_prefix = '/dashboard/cup_content/config/';
	foreach($config_links as $name => $link){
		$config_links[$name] = $this->url($url_prefix.$link);
	}
?>

<div>	
	<div style="width:1px;height:30px;"></div>
	<table>
		<tbody>
			<?php foreach($config_links as $name => $url):?>
				<tr>
					<td style="padding-bottom: 5px;"><a href="<?php echo $url;?>"><?php echo $name;?></a></td>
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>
</div>

<div style="clear:both"></div>
<?php  echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(true);?>
