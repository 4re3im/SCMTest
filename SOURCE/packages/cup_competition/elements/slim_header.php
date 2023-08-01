<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
	//$category = 'hsc';
	
if(!isset($category)){
	$category = 'hsc';
}

$slider_config = "";
if(strcmp(strtoupper($category), 'HSC') == 0){
	Loader::model('hsc_slider', 'cup_competition');
	$carousel = new HscSlider();
	$slider_config = json_decode($carousel->toResultJSON(), true);
}else{
	Loader::model('vce_slider', 'cup_competition');
	$carousel = new VceSlider();
	$slider_config = json_decode($carousel->toResultJSON(), true);
}

$image_src = $slider_config['carousel_data'][0]['image'];
$info_title = $slider_config['carousel_data'][0]['title'];
$info_description = $slider_config['carousel_data'][0]['description'];
?>
<div class="cup-competition-slim-header">
	<div class="image-frame">
		<!--
		hello world
		-->
<img src="<?php echo $image_src;?>"/>
	</div>
	
	<div class="description_frame">
		<div class="indication_frame"></div>
		<div class="info_frame">
			<div class="content_area">
				<h5><?php echo $info_title;?></h5>
				<p><?php echo $info_description;?></p>
			</div>
		</div>
		<div style="clear: both; width: 0px; height: 0px">
		
		</div>
	</div>
	
	<div class="menu-btn-frame">
		<div class="btns">
			<?php $links = array(
							'HSC' => $this->url('/competition/hsc'),
							'VCE' => $this->url('/competition/vce'),
						);?>
			<?php if(strcmp(strtoupper($category), 'HSC') == 0):?> 
				<span class="active">HSC</span>
			<?php else:?>
				<a href="<?php echo $links['HSC'];?>">HSC</a>
			<?php endif;?>	
			/
			<?php if(strcmp(strtoupper($category), 'VCE') == 0):?> 
				<span class="active">VCE</span>
			<?php else:?>
				<a href="<?php echo $links['VCE'];?>">VCE</a>
			<?php endif;?>	
		</div>
	</div>
</div>
