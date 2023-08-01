<?php
	$uh = Loader::helper('url');
	$ch = Loader::helper('cup_content_html', 'cup_content');
?>
<div class="cup-main-carousel page_heading_background">
	<div class="carousel_frame">
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
			<div class="btn_frame"><a href="#"><div></div></a></div>
			<div style="clear:both; width:0px; height:0px;"></div>
		</div>
	</div>
	
	<div class="btn_department_frame">
		<div class="btn_primary">
			<div class="value">PRIMARY</div>
			<div class="bottom_pad"><div class="inner"></div></div>
		</div>
		<div class="btn_secondary">
			<div class="value">SECONDARY</div>
			<div class="bottom_pad"><div class="inner"></div></div>
		</div>
		<div style="clear:both; width:0px; height:0px;"></div>
		
		
		<div class="subject_list_frame">
			<?php $lastIdx = 0;?>
			<div class="subjects" id="primary">
				<div class="title">Browse by subject</div>
				<div class="list">
					<div class="section">
						<ul>
						<?php foreach($subjects['primary'] as $idx => $each_subject):?>
							<?php $lastIdx = $idx;
									if($idx == 10):?>
									</ul>
								</div>
								<div class="section end">
									<ul>
							<?php endif;?>
							
							<?php
								//$a_href = $uh->buildQuery('/search', array('q_subject'=>$each_subject->name));
								//$a_href = $ch->url('/search').'?'.http_build_query(array('q_subject'=>$each_subject->name));
								$a_href = $each_subject->getUrl();
								$a_href = rtrim($a_href, '/')."/Primary";
							?>
							<li><a href="<?php echo $a_href;?>"><?php echo $each_subject->name;?></a></li>
						<?php endforeach;?>
						</ul>
					</div>
					<?php if($lastIdx < 10):?>
						<div class="section end"></div>
					<?php endif;?>
					<div class="width:0px;height:0px;clear:both"></div>
				</div>
				<?php
					$a_href = $ch->buildQuery('/search', array('q_department'=>'Primary'));
				?>
				<div class="foot"><div class="btn_browse_all"><a href="<?php echo $a_href;?>">Browse all</a></div></div>
			</div>
			
			<div class="subjects" id="secondary">
				<div class="title">Browse by subject</div>
				<div class="list">
					<div class="section">
						<ul>
						<?php foreach($subjects['secondary'] as $idx => $each_subject):?>
							<?php $lastIdx = $idx;
								if($idx == 10):?>
									</ul>
								</div>
								<div class="section end">
									<ul>
							<?php endif;?>
							
							<?php
								$a_href = $each_subject->getUrl();
								$a_href = rtrim($a_href, '/')."/Secondary";
							?>
							<li><a href="<?php echo $a_href;?>"><?php echo $each_subject->name;?></a></li>
						<?php endforeach;?>
						</ul>
					</div>
					<?php if($lastIdx < 10):?>
						<div class="section end"></div>
					<?php endif;?>
					<div class="width:0px;height:0px;clear:both"></div>
				</div>
				<?php
					$a_href = $ch->buildQuery('/search', array('q_department'=>'Secondary'));
				?>
				<div class="foot"><div class="btn_browse_all"><a href="<?php echo $a_href;?>">Browse all</a></div></div>
			</div>
		</div>
	</div>
</div>
<?php
	/* http://con5.local.com/packages/cup_layout/carousel_testdata.php */
	$uh = Loader::helper('concrete/urls');
	$carousel_config_url = $uh->getToolsURL('block_main_carousel/config', 'cup_content');
?>
<script>
jQuery(document).ready(function(){
	cup_carousel_start('<?php echo $carousel_config_url;?>');
});
</script>