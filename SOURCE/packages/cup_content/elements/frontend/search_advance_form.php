<?php
	$search_criteria = $criteria;
	unset($search_criteria['cc_size']);
	unset($search_criteria['cc_page']);
	unset($search_criteria['cc_sort']);
?>
<div class="cup-content-advance-search-frame">
	<form action="<?php echo $this->url('/education/search');?>" method="get">
		<?php if(is_array($search_criteria) && count($search_criteria) > 0):?>
		<div class="switch-frame active"></div>
		<?php else:?>
		<div class="switch-frame"></div>
		<?php endif;?>
		<div class="header-section">
			Advanced Search
		</div>
		<?php if(is_array($search_criteria) && count($search_criteria) > 0):?>
		<div class="form-section" style="display:block;">
		<?php else:?>
		<div class="form-section">
		<?php endif;?>
			<div style="width:100%;height:10px;"></div>
			<div class="form-column">
				<div class="field-item">
					<div class="field-attr">Keyword Search</div>
					<div class="field-value">
						<?php
							$val = '';
							if(isset($criteria['q_keywords'])){
								$val = $criteria['q_keywords'];
							}
						?>
						<input type="text" name="q_keywords" value="<?php echo str_replace('"', '\"', $val);?>"/>
					</div>
				</div>
				
				<div class="field-item">
					<div class="field-attr">ISBN</div>
					<div class="field-value">
						<?php
							$val = '';
							if(isset($criteria['q_isbn'])){
								$val = $criteria['q_isbn'];
							}
						?>
						<input type="text" name="q_isbn" value="<?php echo str_replace('"', '\"', $val);?>"/>
					</div>
				</div>
				
				<div class="field-item">
					<div class="field-attr">Author</div>
					<div class="field-value">
						<?php
							$val = '';
							if(isset($criteria['q_author'])){
								$val = $criteria['q_author'];
							}
						?>
						<input type="text" name="q_author" value="<?php echo str_replace('"', '\"', $val);?>"/>
					</div>
				</div>
			</div>
			
			<div class="form-column">
				<div class="field-item">
					<div class="field-attr">Subject</div>
					<div class="field-value">
						<?php
							$val = '';
							if(isset($criteria['q_subject'])){
								$val = $criteria['q_subject'];
							}
							
							Loader::model('subject/list', 'cup_content');
                            $exclusions = CupContentSubjectList::getExclusionList();
                            $exclusions = array_merge($exclusions["primary"], $exclusions["secondary"]);

							$subject_list = new CupContentSubjectList();
							if(isset($_GET['q_department'])){
								$subject_list->filterByDepartment($_GET['q_department']);
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

						?>
						<select name="q_subject">
							<option value=""></option>
							<?php foreach($subjects as $each_subject):?>
								<?php if(strcmp($val, $each_subject->name) == 0):?>
									<option value="<?php echo $each_subject->name;?>" selected="selected"><?php echo $each_subject->name;?></option>
								<?php else:?>
									<option value="<?php echo $each_subject->name;?>"><?php echo $each_subject->name;?></option>
								<?php endif;?>
							<?php endforeach;?>
						</select>
					</div>
				</div>
				
				<div class="field-item">
					<div class="field-attr">Year Level</div>
					<div class="field-value">
						<?php
							$val = '';
							if(isset($criteria['q_year_level'])){
								$val = $criteria['q_year_level'];
							}
							
							$opts = array(
										'Preparatory Year' => 'p',
										'Year 1' => '1',
										'Year 2' => '2',
										'Year 3' => '3',
										'Year 4' => '4',
										'Year 5' => '5',
										'Year 6' => '6',
										'Year 7' => '7',
										'Year 8' => '8',
										'Year 9' => '9',
										'Year 10' => '10',
										'Year 11' => '11',
										'Year 12' => '12'
									);
						?>
						<select name="q_year_level">
							<option value=""></option>
							<?php foreach($opts as $key => $value):?>
								<?php if($value == $val):?>
									<option value="<?php echo $value;?>" selected="selected"><?php echo $key;?></option>
								<?php else:?>
									<option value="<?php echo $value;?>"><?php echo $key;?></option>
								<?php endif;?>
							<?php endforeach;?>
						</select>
					</div>
				</div>
				
				<div class="field-item">
					<div class="field-attr">Division</div>
					<div class="field-value">
						<?php
							$val = '';
							if(isset($criteria['q_department'])){
								$val = $criteria['q_department'];
							}
							
							$opts = array(
										'All' => '',
										'Primary' => 'Primary',
										'Secondary' => 'Secondary',
									);
						?>
						<select name="q_department">
							<option value=""></option>
							<?php foreach($opts as $key => $value):?>
								<?php if($value == $val):?>
									<option value="<?php echo $value;?>" selected="selected"><?php echo $key;?></option>
								<?php else:?>
									<option value="<?php echo $value;?>"><?php echo $key;?></option>
								<?php endif;?>
							<?php endforeach;?>
						</select>
					</div>
				</div>
			</div>
			
			<div class="form-column">
				<div class="field-item">
					<div class="field-attr">Component</div>
					<div class="field-value">
						<?php
							$val = '';
							if(isset($criteria['q_component'])){
								$val = $criteria['q_component'];
							}
							
							Loader::model('format/list', 'cup_content');
							$format_list = new CupContentFormatList();
							$format_list->sortBy('name', 'asc');
							$formats = $format_list->get(999, 0);
							
							$format_digital = new Object;
							$format_digital->name = "[Digital Resource]";
							$formats[] = $format_digital;
							
						?>
						<select name="q_component">
							<option value=""></option>
							<?php foreach($formats as $format):?>
								<?php if(strcmp($format->name, $val) == 0):?>
									<option value="<?php echo $format->name;?>" selected="selected"><?php echo $format->name;?></option>
								<?php else:?>
									<option value="<?php echo $format->name;?>"><?php echo $format->name;?></option>
								<?php endif;?>
							<?php endforeach;?>
						</select>
					</div>
				</div>
				
				
				<?php if(strcmp($_SESSION['DEFAULT_LOCALE'],'en_AU') == 0):?>
				<div class="field-item">
					<div class="field-attr">State/Region</div>
					<div class="field-value">
						<?php
							$val = '';
							if(isset($criteria['q_region'])){
								$val = $criteria['q_region'];
							}
							
							$opts = array(
										'New South Wales' => 'New South Wales',
										'Northern Territory' => 'Northern Territory',
										'Queensland' => 'Queensland',
										'South Australia' => 'South Australia',
										'Tasmania' => 'Tasmania',
										'Victoria' => 'Victoria',
										'Western Australia' => 'Western Australia',
										'New Zealand' => 'New Zealand',
									);
						?>
						<select name="q_region">
							<option value=""></option>
							<?php foreach($opts as $key => $value):?>
								<?php if($value == $val):?>
									<option value="<?php echo $value;?>" selected="selected"><?php echo $key;?></option>
								<?php else:?>
									<option value="<?php echo $value;?>"><?php echo $key;?></option>
								<?php endif;?>
							<?php endforeach;?>
						</select>
					</div>
				</div>
				<?php endif;?>
				
				<div class="form-submit-frame">
					<a href="javascript:clearSearchForm()"><span class="btn primary">Clear Form</span></a>
					&nbsp;&nbsp;
					<input type="submit" value="Search again"/>
				</div>
			</div>
			
			
			<div style="clear:both; width:0px;height:0px;"></div>
			<div style="width:100%;height:10px;"></div>
		</div>
	</form>
	
	<script>
		function clearSearchForm(){
			var form = jQuery('.cup-content-advance-search-frame form');
			form.find('input[type="text"]').each(function(){
				jQuery(this).val("");
			});
			form.find('select').each(function(){
				jQuery(this).val("");
			});
		}
	
		jQuery('.cup-content-advance-search-frame .switch-frame').click(function(){
			var btn = jQuery(this);
			var parent = jQuery(this).parent();
			var form_section = parent.find('.form-section');
			if(btn.hasClass('active')){
				form_section.slideUp('slow', function(){
					btn.removeClass('active')
				});
			}else{
				form_section.slideDown('slow', function(){
					btn.addClass('active')
				});
			}
			
		});
		
		jQuery('.cup-content-advance-search-frame .header-section').click(function(){
			var btn = jQuery('.cup-content-advance-search-frame .switch-frame');
			var parent = jQuery(this).parent();
			var form_section = parent.find('.form-section');
			if(btn.hasClass('active')){
				form_section.slideUp('slow', function(){
					btn.removeClass('active')
				});
			}else{
				form_section.slideDown('slow', function(){
					btn.addClass('active')
				});
			}
			
		});
	</script>
</div>