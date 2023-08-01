<?php
	$uh = Loader::helper('url');
?>
<div class="h30_spacer"></div>
<div class="cup-basic-search-subject">
	<div class="heading-section">
		CHOOSE SUBJECT
	</div>
	<div class="body-section">
		
			<?php
				$base_url = 'inspection_copy';
				if(isset($criteria['base_url'])){
					$base_url = $criteria['base_url'];
				}
				
				$selected_subject_pretty_url = false;
				if(isset($criteria['subject_prettyUrl'])){
					$selected_subject_pretty_url = $criteria['subject_prettyUrl'];
				}
					
				Loader::model('subject/list', 'cup_content');
				$subject_list = new CupContentSubjectList();
				
				
				$selected_region = 'New Zealand';
				if(strcmp($_SESSION['DEFAULT_LOCALE'], 'en_AU') == 0){
					$selected_region = 'All Australia';
					if(isset($_SESSION['inspection_copy']['filter_region'])){
						$selected_region = $_SESSION['inspection_copy']['filter_region'];
					}
				}

				$subject_list->filterWithAvailableTitleSamplePage($selected_region);

				$subject_list->sortBy('name', 'asc');
				$subjects = $subject_list->get(999, 0);
				
			?>
			<?php if(count($subjects) > 0):?>
				<ul>
					<?php foreach($subjects as $each_subject):?>
						<?php 
							$href = $this->url("/{$base_url}/", $each_subject->prettyUrl);
							if(isset($_GET['cc_size'])){
								$href .= '?'.http_build_query(array('cc_size'=>$_GET['cc_size']));
							}
						?>
						<?php if(strcmp($each_subject->prettyUrl, $selected_subject_pretty_url) == 0):?>
							<?php $mhref = $this->url("/{$base_url}/");?>
							<li class="active"><a href="<?php echo $mhref;?>"><?php echo $each_subject->name;?></a></li>
						<?php else:?>
							<li><a href="<?php echo $href;?>"><?php echo $each_subject->name;?></a></li>
						<?php endif;?>
					<?php endforeach;?>
				</ul>
			<?php else:?>
				<div class="na_message">Not Available</div>
			<?php endif;?>
	</div>
</div>

<script>
	jQuery('.search_category_item .title').click(function(){
		var list = jQuery(this).parent().parent().find('.category_filter_list');
		if(list.hasClass('hidden')){
			list.slideDown(100, function(){ list.removeClass('hidden') });
		}else{
			list.slideUp(100, function(){ list.addClass('hidden') });
		}
	});
</script>