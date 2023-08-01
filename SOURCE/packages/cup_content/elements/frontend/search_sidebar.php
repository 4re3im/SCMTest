<?php
	$uh = Loader::helper('url');
	$ch = Loader::helper('cup_content_html', 'cup_content');
	
	
	unset($criteria['cc_page']);
?>
<div class="h30_spacer"></div>
<div class="cup-basic-search-criteria">
	
	<div class="heading-section">
		&nbsp;
	</div>
	
	<div class="body-section">
		<div class="search_category_group">
			<div class="search_category_item">
				<div class="title">Subject</div>
			</div>
			<?php
				$filter_list_class = "category_filter_list hidden";
				$selected_filter = false;
				if(isset($criteria['q_subject'])){
					$selected_filter = $criteria['q_subject'];
					$filter_list_class = "category_filter_list";
				}
				
				
				$filter_list = array();
				/*
						'New South Wales' => $uh->buildQuery('/search', array_merge($criteria, array('q_region'=>'New South Wales'))),
						'Northern Territory' => $uh->buildQuery('/search', array_merge($criteria, array('q_region'=>'Northern Territory'))),
						'Queensland' => $uh->buildQuery('/search', array_merge($criteria, array('q_region'=>'Queensland'))),
						'South Australia' => $uh->buildQuery('/search', array_merge($criteria, array('q_region'=>'South Australia'))),
						'Tasmania' => $uh->buildQuery('/search', array_merge($criteria, array('q_region'=>'Tasmania'))),
						'Victoria' => $uh->buildQuery('/search', array_merge($criteria, array('q_region'=>'Victoria'))),
						'Western Australia' => $uh->buildQuery('/search', array_merge($criteria, array('q_region'=>'Western Australia'))),
						'New Zealand' => $uh->buildQuery('/search', array_merge($criteria, array('q_region'=>'New Zealand'))),
				*/
					
				Loader::model('subject/list', 'cup_content');
                $exclusions = CupContentSubjectList::getExclusionList();
				
				/*
				if($_SERVER['REMOTE_ADDR'] == "115.64.119.173"){
					print_r($exclusions);
					$exclusions = array_merge($exclusions["primary"], $exclusions["secondary"]);
					print_r($exclusions);
					exit();
				}
				*/
				$subject_list = new CupContentSubjectList();
				if(isset($_GET['q_department'])){
					$subject_list->filterByDepartment($_GET['q_department']);
                    $exclusions = $exclusions[strtolower($_GET['q_department'])];
				}elseif(isset($criteria['q_department'])){
					$subject_list->filterByDepartment($criteria['q_department']);
                    $exclusions = $exclusions[strtolower($criteria['q_department'])];
				}else{
                    $exclusions = array_merge($exclusions["primary"], $exclusions["secondary"]);
                }
				
				
				if(strcmp($_SESSION['DEFAULT_LOCALE'], 'en_NZ') == 0){
					$subject_list->filterByRegion('NZ');
				}else{
					$subject_list->filterByRegion('AU');
				}
				
				$subject_list->sortBy('name', 'asc');
				$subjects = array();
                $searchResult = $subject_list->get(999, 0);
                foreach($searchResult as $eachItem){
                    if(!in_array($eachItem->name, $exclusions)){
                        array_push($subjects, $eachItem);
                    }
                }


				foreach($subjects as $each_subject){
					$filter_list[$each_subject->name] = $ch->buildQuery('/education/search', array_merge($criteria, array('q_subject'=>$each_subject->name)));
				}
			?>
			<div class="<?php echo $filter_list_class;?>">
				<ul>
					<?php foreach($filter_list as $key => $url):?>
						<?php if(strcmp($key, $selected_filter) == 0):?>
							<?php 
								$tmp_c = $criteria;
								unset($tmp_c['q_subject']);
								$tmp_l = $ch->buildQuery('/education/search', $tmp_c);
							?>
							<li class="active"><a href="<?php echo $tmp_l;?>"><?php echo $key;?></a></li>
						<?php else:?>
							<li><a href="<?php echo $url;?>"><?php echo $key;?></a></li>
						<?php endif;?>
					<?php endforeach;?>
				</ul>
			</div>
		</div>
	
	
		<div class="heading-section">
			REFINE BY
		</div>
	
	<?php if(strcmp($_SESSION['DEFAULT_LOCALE'], 'en_AU') == 0):?>
		<div class="search_category_group">
			<div class="search_category_item">
				<div class="title">State/Region</div>
			</div>
			<?php
				$filter_list_class = "category_filter_list hidden";
				$selected_filter = false;
				if(isset($criteria['q_region'])){
					$selected_filter = $criteria['q_region'];
					$filter_list_class = "category_filter_list";
				}
				$filter_list = array(
						'New South Wales' => $ch->buildQuery('/education/search', array_merge($criteria, array('q_region'=>'New South Wales'))),
						'Northern Territory' => $ch->buildQuery('/education/search', array_merge($criteria, array('q_region'=>'Northern Territory'))),
						'Queensland' => $ch->buildQuery('/education/search', array_merge($criteria, array('q_region'=>'Queensland'))),
						'South Australia' => $ch->buildQuery('/education/search', array_merge($criteria, array('q_region'=>'South Australia'))),
						'Tasmania' => $ch->buildQuery('/education/search', array_merge($criteria, array('q_region'=>'Tasmania'))),
						'Victoria' => $ch->buildQuery('/education/search', array_merge($criteria, array('q_region'=>'Victoria'))),
						'Western Australia' => $ch->buildQuery('/education/search', array_merge($criteria, array('q_region'=>'Western Australia'))),
						'New Zealand' => $ch->buildQuery('/education/search', array_merge($criteria, array('q_region'=>'New Zealand'))),
					);
			?>
			<div class="<?php echo $filter_list_class;?>">
				<ul>
					<?php foreach($filter_list as $key => $url):?>
						<?php if(strcmp($key, $selected_filter) == 0):?>
							<?php 
								$tmp_c = $criteria;
								unset($tmp_c['q_region']);
								$tmp_l = $ch->buildQuery('/education/search', $tmp_c);
							?>
							<li class="active"><a href="<?php echo $tmp_l;?>"><?php echo $key;?></a></li>
						<?php else:?>
							<li><a href="<?php echo $url;?>"><?php echo $key;?></a></li>
						<?php endif;?>
					<?php endforeach;?>
				</ul>
			</div>
		</div>
	<?php endif;?>
		
		
		<div class="search_category_group">
			<div class="search_category_item">
				<div class="title">Year Level</div>
			</div>
			<?php
				$filter_list_class = "hidden";
				$selected_filter = false;
				if(isset($criteria['q_year_level'])){
					$selected_filter = $criteria['q_year_level'];
					if($selected_filter == 'p'){
						$selected_filter = 'Preparatory Year';
					}else{
						$selected_filter = 'Year '.$selected_filter;
					}
					$filter_list_class = "";
				}
				
				$filter_list = array(
						'Preparatory Year' => $ch->buildQuery('/education/search', array_merge($criteria, array('q_year_level'=>'p'))),
						'Year 1' => $ch->buildQuery('/education/search', array_merge($criteria, array('q_year_level'=>'1'))),
						'Year 2' => $ch->buildQuery('/education/search', array_merge($criteria, array('q_year_level'=>'2'))),
						'Year 3' => $ch->buildQuery('/education/search', array_merge($criteria, array('q_year_level'=>'3'))),
						'Year 4' => $ch->buildQuery('/education/search', array_merge($criteria, array('q_year_level'=>'4'))),
						'Year 5' => $ch->buildQuery('/education/search', array_merge($criteria, array('q_year_level'=>'5'))),
						'Year 6' => $ch->buildQuery('/education/search', array_merge($criteria, array('q_year_level'=>'6'))),
						'Year 7' => $ch->buildQuery('/education/search', array_merge($criteria, array('q_year_level'=>'7'))),
						'Year 8' => $ch->buildQuery('/education/search', array_merge($criteria, array('q_year_level'=>'8'))),
						'Year 9' => $ch->buildQuery('/education/search', array_merge($criteria, array('q_year_level'=>'9'))),
						'Year 10' => $ch->buildQuery('/education/search', array_merge($criteria, array('q_year_level'=>'10'))),
						'Year 11' => $ch->buildQuery('/education/search', array_merge($criteria, array('q_year_level'=>'11'))),
						'Year 12' => $ch->buildQuery('/education/search', array_merge($criteria, array('q_year_level'=>'12')))
					);
			?>
			<div class="category_filter_list <?php echo $filter_list_class;?>">
				<ul>
					<?php foreach($filter_list as $key => $url):?>
						<?php if(strcmp($key, $selected_filter) == 0):?>
							<?php 
								$tmp_c = $criteria;
								unset($tmp_c['q_year_level']);
								$tmp_l = $ch->buildQuery('/education/search', $tmp_c);
							?>
							<li class="active"><a href="<?php echo $tmp_l;?>"><?php echo $key;?></a></li>
						<?php else:?>
							<li><a href="<?php echo $url;?>"><?php echo $key;?></a></li>
						<?php endif;?>
					<?php endforeach;?>
				</ul>
			</div>
		</div>
		
		<div class="search_category_group">
			<div class="search_category_item">
				<div class="title">Component</div>
			</div>
			
			<?php
				$filter_list_class = "category_filter_list hidden";
				$selected_filter = false;
				if(isset($criteria['q_component'])){
					$selected_filter = $criteria['q_component'];
					$filter_list_class = "category_filter_list";
				}
				$filter_list = array();
					
				Loader::model('format/list', 'cup_content');
				$format_list = new CupContentFormatList();
				$format_list->sortBy('name', 'asc');
				$formats = $format_list->get(999, 0);
				
				$format_digital = new Object;
				$format_digital->name = '[Digital Resource]';
				$formats[] = $format_digital;
				
				foreach($formats as $each_format){
					$filter_list[$each_format->name] = array('url' => $ch->buildQuery('/education/search', array_merge($criteria, array('q_component'=>$each_format->name))));
					if(strcmp(get_class($each_format), 'CupContentFormat') == 0){
						$filter_list[$each_format->name]['logo'] = $each_format->getSmallImageURL();
					}
				}
			?>
			<div class="<?php echo $filter_list_class;?>">
				<ul>
					<?php foreach($filter_list as $key => $each_item):?>
						<?php
							$logo_html = "";
							$url = $each_item['url'];
							if(isset($each_item['logo'])){
								$logo_html = '<span><img src="'.$each_item['logo'].'"/></span>';
							}
						?>
						<?php if(strcmp($key, $selected_filter) == 0):?>
							<?php 
								$tmp_c = $criteria;
								unset($tmp_c['q_component']);
								$tmp_l = $ch->buildQuery('/education/search', $tmp_c);
							?>
							<li class="active"><?php echo $logo_html;?><a href="<?php echo $tmp_l;?>"><?php echo $key;?></a></li>
						<?php else:?>
							<li><?php echo $logo_html;?><a href="<?php echo $url;?>"><?php echo $key;?></a></li>
						<?php endif;?>
					<?php endforeach;?>
				</ul>
			</div>
			
		</div>
		
		<!--
		<div class="search_category_group">
			<div class="search_category_item">
				<div class="title">Author</div>
			</div>
		</div>
		
		<div class="search_category_group">
			<div class="search_category_item">
				<div class="title">Availability</div>
			</div>
		</div>
		-->
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