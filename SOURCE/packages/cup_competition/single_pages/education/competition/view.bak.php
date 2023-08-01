<?php
	defined('C5_EXECUTE') or die(_("Access Denied."));
	$theme_competition = true;
	
	$uh = Loader::helper('concrete/urls');
	$slider_config_url = $uh->getToolsURL('slider/hsc_config', 'cup_competition');
?>

<div class="cup-competition-slider">
	<div class="slider_frame">
		<div class="image_holder">
		</div>
		
		<div class="image_frame">
			<div class="transition_frame">
				<div class="position_one"></div>
				<div class="position_two"></div>
			</div>
		</div>
		
		<div class="loading_indicator"></div>
		
		<div class="description_frame">
			<div class="indication_frame">
				<div style="clear:both; width:0px; height:0px;"></div>
			</div>
			<div class="info_frame">
				<div class="content_area">
					
				</div>
			</div>
			<div style="clear:both; width:0px; height:0px;"></div>
		</div>
	</div>
</div>

<script>
	cup_slider_start('<?php echo $slider_config_url;?>');
</script>