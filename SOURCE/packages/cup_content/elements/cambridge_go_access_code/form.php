<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<?php  
$form = Loader::helper('form');
$wform = Loader::helper('wform', 'cup_content');

$v = View::getInstance();
$html = Loader::helper('html');
$th = Loader::helper('concrete/urls'); 
?>

<div class="span16">

	<div class="clearfix">
		<?php echo $form->label('title', t('Title') . '	<span class="ccm-required">*</span>')?>
		<div class="input">
			<?php //echo $form->text('title', @$entry['title'], array('class' => "span6"))?>
			<?php if(isset($entry['titleID'])):?>
				<?php $titleObj = CupContentTitle::fetchById($entry['titleID']);
						$entry['name'] = $titleObj->displayName ;?>
				<?php echo $wform->singleItem('titleID', array($entry['titleID'] => $entry['name']));?>
			<?php else:?>
				<?php echo $wform->singleItem('titleID', array());?>
			<?php endif;?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('access_code', t('Access Code') . '')?>
		<div class="input">
			<?php  //Loader::element('editor_init'); ?>
			<?php  //Loader::element('editor_config'); ?>
			<?php  //Loader::element('editor_controls', array('mode'=>'full')); ?>
			<div>
				<i style="font-size:12px;margin-top:5px;">Please enter access codes, line-separated and/or comma-separated</i>
			</div>
			<?php echo $form->textarea('access_code', @$entry['access_code'], array('class' => "span6"))?>

		</div>
	</div>
	
</div>

<div class="clearfix"></div>

<?php
	$uh = Loader::helper('concrete/urls');
	$dashboard_select_title_link = $uh->getToolsURL('title/dashboard_selection_hasAccessCode', 'cup_content');
?>

<script>
	
	jQuery('.wform-button[ref="titleID"]').click(function(){
		var dashboard_select_subject_link =  "<?php echo $dashboard_select_title_link;?>";

		jQuery.colorbox({html:'<div style="width:630px;height:500px" id="popup-window-content"></div>',
					width: "630px",
					height: "500px"});
		
		var selected_values = new Array();
		jQuery('.multiple-items-group[ref="titleID"] span.value_item input').each(function(){
			selected_values.push(jQuery(this).val());
		});
		
		var submit_data = {
						'selected_values': selected_values
					};
		
		jQuery.ajax({
			type: 'post',
			url: dashboard_select_subject_link, 
			data: submit_data, //jQuery(this).serialize(),
			success: function(html_data){
				var p = jQuery('#popup-window-content').parent();
				p.empty();
				p.html(html_data);
			}
		});
		
	});
</script>