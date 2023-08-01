<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));

$eventAssoc = $eventObj->getAssoc();
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


<?php if(time() < strtotime($eventAssoc['start_time'])):?>
	<div style="width:100%;height: 20px;"></div>
	<div style="margin:0px 25px; font-size: 24px; font-weight:bold;">
		Competition has not started. Please check again later.
	</div>
	<div style="width:100%;height: 80px;"></div>
<?php elseif(time() > strtotime($eventAssoc['end_time'])):?>
	<div style="width:100%;height: 20px;"></div>
	<div style="margin:0px 25px; font-size: 24px;font-weight:bold;">
		Competition has been closed.
	</div>
	<div style="width:100%;height: 80px;"></div>
<?php else:?>
	<?php if(count($errors) > 0):?>
	<div style="width:100%;height: 20px;"></div>
	<div class="cup-competition-errors">
		<ul>
		<?php foreach($errors as $error):?>
		<li><?php echo $error;?></li>
		<?php endforeach;?>
		</ul>
	</div>
	<div style="width:100%;height: 20px;"></div>
	<?php endif;?>

	<form method="post" enctype="multipart/form-data" id="competition_form"><!-- -->

	<?php Loader::element('entry_form_step1', array('eventObj'=>$eventObj, 'entryObj'=>$entryObj), 'cup_competition'); ?>


	<div style="width:100%;height: 20px;"></div>

	<?php Loader::element('entry_form_step2', array('eventObj'=>$eventObj, 'entryObj'=>$entryObj), 'cup_competition'); ?>

	<div style="width:100%;height: 20px;"></div>

	<?php Loader::element('entry_form_step3', array('eventObj'=>$eventObj, 'entryObj'=>$entryObj), 'cup_competition'); ?>

	<div style="width:100%;height: 20px;"></div>

	<div class="cup_competition_page_content">
		<input id="form_submit_btn" type="submit" value="Submit entry"/>
		<div id="form_loading_indicator">Please wait ...</div>
	</div>
	<div style="width:100%;height: 90px;"></div>

	</form>


	<script>
		jQuery('form#competition_form').submit(function(){
			jQuery(this).find('#form_loading_indicator').show();
			jQuery(this).find('#form_submit_btn').hide();
		});
	</script>
<?php endif;?>