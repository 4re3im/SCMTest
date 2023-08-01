<?php

?>

<div class="cup_landing_social_section">
	<table>
		<tr>
			<td class="pad">&nbsp;</td>
			<?php if($config['facebook']):?>
			<td class="facebook">
				<a href="<?php echo $config['facebook_url'];?>">Find us on Facebook</a>
			</td>
			<?php endif;?>
			
			<?php if($config['linkedin']):?>
			<td class="linkedin">
				<a href="<?php echo $config['linkedin_url'];?>">Find us on LinkedIn</a>
			</td>
			<?php endif;?>
		
			<?php if($config['erc']):?>
			<td class="erc">
				<a href="<?php echo View::url("/about/contact-us");?>">Contact your Education resource Consultant</a>
			</td>
			<?php endif;?>
			
			<?php if($config['addthis']):?>
			<td class="addthis">
				<!-- AddThis Button BEGIN -->
				<div class="addthis_toolbox addthis_default_style " style="width: 180px; margin:0 auto;">
					<a class="addthis_button_preferred_1"></a>
					<a class="addthis_button_preferred_2"></a>
					<a class="addthis_button_preferred_3"></a>
					<a class="addthis_button_preferred_4"></a>
					<a class="addthis_button_compact"></a>
					<a class="addthis_counter addthis_bubble_style"></a>
				</div>
				<script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
				<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5119c82f3938cb62"></script>
				<!-- AddThis Button END -->
			</td>
			<?php endif;?>
			
			<td class="pad">&nbsp;</td>
		</tr>
	</table>
</div>