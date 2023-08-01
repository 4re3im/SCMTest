<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

// GCAP-1272 Added by Shane Camus 04/08/2021
$pkg = Package::getByHandle('cup_content');
$th = Loader::helper('concrete/urls');
$loaderPath = $th->getPackageURL($pkg);

$html = Loader::helper('html');
$this->addHeaderItem($html->css('wform.css', 'cup_content')); 
$this->addHeaderItem($html->javascript('colorbox/jquery.colorbox-min.js', 'cup_content'));
$this->addHeaderItem($html->css('../js/colorbox/colorbox.css', 'cup_content'));
$this->addHeaderItem($html->javascript('jquery.wspecial.js', 'cup_content')); 

$valt = Loader::helper('validation/token');

?>

<style>
    /* GCAP-1272 Added by Shane Camus 04/08/2021 */
    .ui-autocomplete {
        font-size: 12px;
        max-height: 300px;
        max-width: 1000px;
        overflow-y: auto;
        overflow-x: auto;
    }
    .ui-autocomplete-loading {
        background: url('<?php echo $loaderPath?>/images/ajax-loader.gif') no-repeat right center
    }
</style>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Add Series'), false)?>

<form method="post" id="ccm-core-commerce-product-add-form" enctype="multipart/form-data" action="<?php echo $this->url('/dashboard/cup_content/series/add', 'submit')?>">
<div class="ccm-pane-body">
	<?php Loader::packageElement('alert_message_header', 'cup_content'); ?>
	<?php echo $valt->output('create_series')?>
	<?php Loader::packageElement('series/form', 'cup_content', array('entry'=>@$entry)); ?>
</div>
<div class="ccm-pane-footer">
	<input type="hidden" name="create" value="1" />
	<input id="cc-parent-page" type="hidden" name="parentCID" value="0" />
	<a href="<?php echo $this->url('/dashboard/cup_content/subjects')?>" class="btn"><?php echo t('Back to Subjects')?></a>
	<input type="submit" class="ccm-button-right btn primary accept" value="<?php echo t('Add')?>"/>
</div>	
</form>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>

<?php
	$uh = Loader::helper('concrete/urls');
	$dashboard_select_format_link = $uh->getToolsURL('format/dashboard_selection', 'cup_content');
	$dashboard_select_subject_link = $uh->getToolsURL('subject/dashboard_selection', 'cup_content');
?>
<script>
	jQuery('.wform-button[ref="formats"]').click(function(){
		var dashboard_select_format_link =  "<?php echo $dashboard_select_format_link;?>";
		/*
		jQuery.colorbox({href:dashboard_select_format_link,
					width: "530px",
					height: "500px"});
		*/	
		
		jQuery.colorbox({html:'<div style="width:630px;height:500px" id="popup-window-content"></div>',
					width: "630px",
					height: "500px"});
		
		var selected_formats = new Array();
		jQuery('.multiple-items-group[ref="formats"] span.value_item input').each(function(){
			selected_formats.push(jQuery(this).val());
		});
		
		var submit_data = {
						'selected_values': selected_formats
					};
		
		jQuery.ajax({
			type: 'post',
			url: dashboard_select_format_link, 
			data: submit_data, //jQuery(this).serialize(),
			success: function(html_data){
				var p = jQuery('#popup-window-content').parent();
				p.empty();
				p.html(html_data);
			}
		});
		
	});
	
	jQuery('.wform-button[ref="subjects"]').click(function(){
		var dashboard_select_subject_link =  "<?php echo $dashboard_select_subject_link;?>";
		/*
		jQuery.colorbox({href:dashboard_select_format_link,
					width: "530px",
					height: "500px"});
		*/	
		
		//alert('subjects');
		
		jQuery.colorbox({html:'<div style="width:630px;height:500px" id="popup-window-content"></div>',
					width: "630px",
					height: "500px"});
		
		var selected_values = new Array();
		jQuery('.multiple-items-group[ref="subjects"] span.value_item input').each(function(){
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