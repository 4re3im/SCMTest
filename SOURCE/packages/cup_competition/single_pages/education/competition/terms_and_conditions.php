<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));

//$category = 'vce';
$category = strtolower($eventObj->category);
Loader::element('slim_header', array('category'=>$category), 'cup_competition'); ?>

<div class="cup-competition-content-menu">
	<div class="gap"></div>
	<a href="<?php echo $this->url('/competition/'.$category);?>"><div class="item home">COMPETITION</div></a>
	<a href="<?php echo $this->url('/competition/entry_form/'.$category);?>"><div class="item">ENTRY FORM</div></a>
	<a href="<?php echo $this->url('/competition/terms_and_conditions/'.$category);?>"><div class="item active">TERMS & CONDITIONS</div></a>
	<?php if($eventObj && strcmp($eventObj->type, 'Photo') == 0):?>
	<a href="<?php echo $this->url('/competition/gallery/'.$category);?>"><div class="item">PHOTO GALLERY</div></a>
	<?php endif;?>
	<div style="clear:both;height:0px;height:0px;"></div>
</div>

<?php if($eventObj && strcmp($eventObj->type, 'Photo') == 0):?>
	<div style="float:right">
		<a href="<?php echo $this->url('/competition/entry_form/'.$category);?>"><div id="btn_competition_enter_now"></div></a>
	</div>
<?php endif;?>
<div style="clear:both;height:0px;height:0px;"></div>

<div style="width:100%;height: 20px;"></div>

<div class="cup_competition_page_title terms_and_conditions">
	TERMS AND CONDITIONS
</div>

<div style="width:100%;height: 20px;"></div>

<div class="cup_competition_page_content">
<?php echo $eventObj->terms_and_conditions_content;?>
</div>

<div style="width:100%;height: 20px;"></div>
