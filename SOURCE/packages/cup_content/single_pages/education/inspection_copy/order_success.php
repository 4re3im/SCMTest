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
		<div style="width: 256px; height: 20px;"></div>
	</div>
	
	<div class="main-content-frame with-left-sidebar">
		<div class="h30_spacer"></div>
		<div class="content-page-title">INSPECTION COPY REQUEST</div>
		<div style="width:100%;height:5px;"></div>
		<div class="spacer_bar yellow"></div>
		<div style="width:100%;height:5px;"></div>
		<div class="sucess_message" style="text-align: left;">
			Thank you for your inspection copy order.<br/>
You will receive a confirmation email (be sure to check your junk mail if you can't find it).<br/>
<br/>
If you have any further questions please contact us on 03 8671 1400.
		</div>
		<div style="width:100%;height:100px;"></div>
	</div>
	<div style="clear:both;width:1px;height:0px;"></div>
</div>