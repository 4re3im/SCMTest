<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php 
	$ch = Loader::helper('cup_content_html', 'cup_content');
	$uh = Loader::helper('url');
	Loader::helper('tools', 'cup_content');
	
	$this->addHeaderItem($html->javascript('colorbox/jquery.colorbox-min.js', 'cup_content'));
	$this->addHeaderItem($html->css('../js/colorbox/colorbox.css', 'cup_content'));
	
?>

<?php //echo $ch->renderSimpleHeading();?>
<?php Loader::element('frontend/simple_heading', array(), 'cup_content');?>
<div class="cup-content-master-frame">

	<div class="left-sidebar-content-frame">
		<?php Loader::element('frontend/inspection_copy/order_summary_sidebar', false, 'cup_content');?>
	</div>
	
	<div class="main-content-frame with-left-sidebar">
		<div class="h30_spacer"></div>
		<div class="content-page-title">INSPECTION COPY REQUEST</div>
		<div style="width:100%;height:5px;"></div>
		<div class="spacer_bar yellow"></div>
		<div style="width:100%;height:5px;"></div>
		<?php Loader::element('alert_message_header', false, 'cup_content');?>
		<div style="width:1px; height: 30px"></div>
		<div class="inspection_copy_order_form_frame">
			<?php Loader::element('frontend/inspection_copy/order_form', array('order'=>$order), 'cup_content');?>
		</div>
		<div class="inspection_copy_empty_item_message_frame" style="display:none">
			Your order list is empty.
		</div>
	</div>
	<div style="clear:both;width:1px;height:0px;"></div>
</div>

<script>
	jQuery('.alert button.close').click(function(){
		jQuery(this).parent().remove();
	});
</script>
