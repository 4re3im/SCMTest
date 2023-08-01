<?php 
	defined('C5_EXECUTE') or die(_("Access Denied."));
	$ih = Loader::helper('concrete/interface');
 
	$form = Loader::helper('form');
?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('VISTA config'), false);?>
<?php Loader::packageElement('alert_message_header', 'cup_content'); ?>
<form method="post" action="<?php echo $this->url('/dashboard/cup_content/config/vista_config')?>">

<div>
	<div style="width:1px;height:30px;"></div>
	<table>
		<tbody>
			<tr>
				<td style="font-size: 14px;padding-bottom: 10px;padding-right: 5px;">Enabled</td>
				<td style="padding-bottom: 10px;">
					<?php echo $form->select('vista[enabled]', array('1'=>'Yes', '0'=>'No'), 
											(isset($config['enabled']) ? $config['enabled'] : ""));?>
				</td>
			</tr>
			<tr>
				<td style="font-size: 14px;padding-bottom: 10px;padding-right: 5px;">API Url:</td>
				<td style="padding-bottom: 10px;">
					<?php echo $form->text('vista[api_url]', 
						(isset($config['api_url']) ? $config['api_url'] : ""));?>
				</td>
			</tr>
		</tbody>
	</table>

	<div style="margin:0px 0px;padding:30px 0px;">
		
		<h3>URL Testing</h3>
		<div style="width: 300px; float:left;">
			<label>Request XML</label>
			<textarea name="raw_xml" style="width:99%;height: 150px;"></textarea>
			<br/>
			<br/>
			<span id="btn_test" class="btn-primary" style="padding: 7px 8px;">Test</span>
		</div>
		<div style="width: 350px; float: left; margin-left: 20px;">
			<label>Result</label>
			<textarea name="api_result" style="width:99%;height: 150px;"></textarea>
		</div>
		<div style="clear:both"></div>
	</div>
</div>

<a href="<?php echo $this->url('/dashboard/cup_content/config')?>" class="btn"><?php echo t('Back')?></a>
<input type="submit" class="ccm-button-right btn primary accept" value="<?php echo t('Save')?>"/>
</form>

<div style="clear:both"></div>
<?php  echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(true);?>

<script>
	jQuery(document).ready(function(){
			jQuery('#btn_test').css('cursor','pointer');
			jQuery('#btn_test').click(function(){
				//alert('hello world');
				var api_url = jQuery('input#vista\\[api_url\\]').val();
				var xml = jQuery('textarea[name="raw_xml"]').val();
				
				jQuery.post(
					"<?php echo $this->url('/dashboard/cup_content/config/test_vista_api');?>",
					{api_url: api_url, content: xml},
					function(data){
						jQuery('textarea[name="api_result"]').val(data);
					}
				);
				return false;
			});
	});
</script>
