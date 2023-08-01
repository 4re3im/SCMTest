<?php 
	defined('C5_EXECUTE') or die(_("Access Denied."));
	$ih = Loader::helper('concrete/interface');
 
	$form = Loader::helper('form');
?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Over Night Sync Config'), false);?>
<?php Loader::packageElement('alert_message_header', 'cup_content'); ?>
<form method="post" action="<?php echo $this->url('/dashboard/cup_content/config/over_night_sync_config')?>">

<div>
	<div style="width:1px;height:30px;"></div>
	<div style="float: left; width: 500px;">
		<table>
			<tbody>
				<tr>
					<td style="font-size: 14px;padding-bottom: 10px;padding-right: 5px;">Enabled</td>
					<td style="padding-bottom: 10px;">
						<?php echo $form->select('config[enabled]', array('1'=>'Yes', '0'=>'No'), 
												(isset($config['enabled']) ? $config['enabled'] : ""));?>
					</td>
				</tr>
				<tr>
					<td style="font-size: 14px;padding-bottom: 10px;padding-right: 5px;">Request Size</td>
					<td style="padding-bottom: 10px;">
						<?php echo $form->select('config[page_size]', 
												array('10'=>'10', '20'=>'20', '30'=>'30', '40'=>'40', '50'=>'50',
														'75'=>'75', '100'=>'100', '125'=>'125', '150'=>'150',
														'200'=>'200', '250'=>'250', '300'=>'300', '350'=>'350',
														'400'=>'400', '450'=>'450', '500'=>'500', '550'=>'550'), 
												(isset($config['page_size']) ? $config['page_size'] : ""));?>
					</td>
				</tr>
				<tr>
					<td style="font-size: 14px;padding-bottom: 10px;padding-right: 5px;">SOAP WSDL</td>
					<td style="padding-bottom: 10px;">
						<?php echo $form->text('config[wsdl]', 
							(isset($config['wsdl']) ? $config['wsdl'] : ""));?>
					</td>
				</tr>
				<tr>
					<td style="font-size: 14px;padding-bottom: 10px;padding-right: 5px;">SOAP location</td>
					<td style="padding-bottom: 10px;">
						<?php echo $form->text('config[location]', 
							(isset($config['location']) ? $config['location'] : ""));?>
					</td>
				</tr>
				<tr>
					<td style="font-size: 14px;padding-bottom: 10px;padding-right: 5px;">SOAP uri</td>
					<td style="padding-bottom: 10px;">
						<?php echo $form->text('config[uri]', 
							(isset($config['uri']) ? $config['uri'] : ""));?>
					</td>
				</tr>
			</tbody>
		</table>

		<div style="margin:0px 0px;padding:30px 0px;">
			
			<h3>URL Testing</h3>
			<div style="width: 500px;">
				<label>Request IBSN13(s) (comma separated, no space) </label>
				<input type="text" name="isbns" style="width:99%;"/>
				<br/>
				<label>Result</label>
				<textarea name="api_result" style="width:99%;height: 150px;"></textarea>
				<br/><br/>
				<span id="btn_test" class="btn-primary" style="padding: 7px 8px;">Test</span>
			</div>
			<div style="clear:both"></div>
		</div>
	</div>
	<div style="float:right; width:360px">
		<?php if($sync_note):?>
		<pre><?php print_r($sync_note);?></pre>
		<?php endif;?>
	</div>
	<div style="clear:both"></div>
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
				var soap_wsdl = jQuery('input#config\\[wsdl\\]').val();
				var soap_location = jQuery('input#config\\[location\\]').val();
				var soap_uri = jQuery('input#config\\[uri\\]').val();
				var soap_arg = jQuery('input[name="isbns"]').val();
				
				
				jQuery.post(
					"<?php echo $this->url('/dashboard/cup_content/config/test_sync_api');?>",
					{wsdl: soap_wsdl, location: soap_location, uri: soap_uri, arg: soap_arg},
					function(data){
						jQuery('textarea[name="api_result"]').val(data);
					}
				);
				return false;
			});
	});
</script>
