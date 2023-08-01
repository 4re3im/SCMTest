<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));

$category = $eventObj->category;
Loader::element('slim_header', array('category'=>$category), 'cup_competition'); ?>

<div class="cup-competition-content-menu">
	<div class="gap"></div>
	<a href="<?php echo $this->url('/education/competition/'.$category);?>"><div class="item home">COMPETITION</div></a>
	<a href="<?php echo $this->url('/education/competition/entry_form/'.$category);?>"><div class="item active">ENTRY FORM</div></a>
	<a href="<?php echo $this->url('/education/competition/terms_and_conditions/'.$category);?>"><div class="item">TERMS & CONDITIONS</div></a>
	<?php if($eventObj && strcmp($eventObj->type, 'Photo') == 0):?>
	<a href="<?php echo $this->url('/education/competition/gallery/'.$category);?>"><div class="item">PHOTO GALLERY</div></a>
	<?php endif;?>
	<div style="clear:both;height:0px;height:0px;"></div>
</div>
<div style="clear:both;height:0px;height:0px;"></div>

<div style="width:100%;height: 20px;"></div>

<div class="cup_competition_page_title">
	ENTRY FORM
</div>

<div style="width:100%;height: 40px;"></div>

<div class="cup-competition-success">
	<strong>Thank you!</strong> - Your entry has been received
</div>

<div style="width:100%;height: 100px;"></div>

