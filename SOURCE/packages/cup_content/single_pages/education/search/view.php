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
		<?php Loader::element('frontend/search_sidebar', array('criteria'=>$criteria), 'cup_content');?>
	</div>
	
	<div class="main-content-frame with-left-sidebar">
		<div class="h50_spacer bg_light_blue2"></div>
		<div class="content-page-title bg_light_blue2"><h1>SEARCH RESULTS</h1></div>
		<?php Loader::element('frontend/search_advance_form', array('criteria'=>$criteria), 'cup_content');?>
		
		<div class="cup-result-frame">
			<?php Loader::element('frontend/search_result', array('search'=>$search, 'criteria'=>$criteria), 'cup_content');?>
		</div>
	</div>
	<div style="clear:both;width:1px;height:0px;"></div>
</div>