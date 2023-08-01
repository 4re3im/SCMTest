<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<?php  

$ch = Loader::helper('cup_content_html', 'cup_content');

$form = Loader::helper('form');
$wform = Loader::helper('wform', 'cup_content');

$v = View::getInstance();
$html = Loader::helper('html');
$th = Loader::helper('concrete/urls'); 

$this->addHeaderItem($html->javascript('tiny_mce/tiny_mce.js'));
?>

<?php echo $form->hidden('id', @$entry['id']); ?>

<div class="span16">
	<?php if(isset($entry['filename']) && strlen($entry['filename']) > 1):?>
		<div id="existing_file">
			<div class="clearfix" style="padding: 5px 10px; border: 1px dotted #000000;">
				<div style="float: right">
					<a href="javascript:deleteDownloadableFile(<?php echo $entry['id'];?>)">DELETE</a>
				</div>
				<?php	
					$file_download_url = $ch->url('/dashboard/cup_content/titles/downloadable_file_download', $entry['id']);
				?>
				Exisitng Filename: <a href="<?php echo $file_download_url;?>"><strong><?php echo $entry['filename'];?></strong></a>
			</div>
			<div style="width: 100%;height: 30px"></div>
		</div>
	<?php endif;?>

	<div class="clearfix">
		<?php echo $form->label('description', t('Description'))?>
		<div class="input">
			<?php echo $form->text('description', @$entry['description'], array('class' => "span6", 'style'=>'height: 18px; line-height: 18px; font-size: 12px;'))?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('file', t('File'))?>
		<div class="input">
			<?php echo $form->file('file', '', array('class' => "span6"))?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('expiry_type', t('expiry type'))?>
		<div class="input">
			<?php
				$options = array(
								"10 years" => "never expiry",
								"3 days" => "3 days",
								"7 days" => "7 days",
								"14 days" => "14 days",
								"30 days" => "30 days",
								"90 days" => "90 days",
							);
			?>
			<?php echo $form->select('expiry_type', $options, @$entry['expiry_type'])?>
		</div>
	</div>
	
</div>

<div class="clearfix"></div>

<script>

function deleteDownloadableFile(fID){
	var action_url = "<?php echo Loader::helper('concrete/urls')->getToolsURL('title_downloadable_file/delete', 'cup_content');?>";
		
	jQuery.get(
		action_url,
		{fid: fID},
		function(data){
			try{
				var json = jQuery.parseJSON( data );
				if(json.result == 'success'){
					jQuery('#existing_file').remove();
				}else{
					alert("Action Error:"+json.error);
				}
			}catch(err){
				alert("Request Error!\n"+data);
			}
		}
	);
}

</script>
