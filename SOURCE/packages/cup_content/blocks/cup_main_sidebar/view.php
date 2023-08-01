<?php 
	$ch = Loader::helper('cup_content_html', 'cup_content');
?>
<div class="cup-main-sidebar">
	<div class="simple-search-frame">
		<button class="search"></button>
		<div class="input_frame">
			<input type="text" name="simple_search_query"/>
		</div>
	</div>
	<div class="gap_spacer"></div>
	
	<div class="advance-search-frame">
		<div class="title">Advanced Search</div>
		<div class="form">
			<form action="<?php echo $this->url('/education/search');?>" method="get">
			<div class="fields">
				<div class="item">
					<label>Keyword Search</label>
					<div class="field"><input type="text" name="q_keywords"/></div>
				</div>
				
				<div class="item">
					<label>ISBN</label>
					<div class="field"><input type="text" name="q_isbn"/></div>
				</div>
				
				<div class="item">
					<label>Author</label>
					<div class="field"><input type="text" name="q_author"/></div>
				</div>
				
				<div class="item">
					<label>Subject</label>
					<div class="field">
						<?php
						Loader::model('subject/list', 'cup_content');
                            $exclusions = CupContentSubjectList::getExclusionList();
                            $exclusions = array_merge($exclusions["primary"],$exclusions["secondary"]);

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
                            foreach($searchResult as $eachRecord){
                                if(!in_array($eachRecord->name, $exclusions)){
                                    array_push($subjects, $eachRecord);
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
				
				<div class="item">
					<label>Year Level</label>
					<div class="field">
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
				
				<div class="item">
					<label>Component</label>
					<div class="field">
						<?php
							$val = '';
							if(isset($criteria['q_component'])){
								$val = $criteria['q_component'];
							}
							
							Loader::model('format/list', 'cup_content');
							$format_list = new CupContentFormatList();
							$format_list->sortBy('name', 'asc');
							$formats = $format_list->get(999, 0);
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
				<div class="item">
					<label>State/region</label>
					<div class="field">
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
			</div>
			<div class="footer">
				<input type="submit" value="Search"/>
			</div>
			</form>
		</div>
	</div>
	<div class="gap_spacer"></div>
	
	
	<?php
	$pkg  = Package::getByHandle('cup_content');
	$config = false;
	$store_value = $pkg->config('MAIN_SIDEBAR_CONFIG');
	$res = unserialize($store_value);
	if(is_array($res)){
		$config = $res;
	}
	
	if($config && is_array($config) && count($config) > 0):?>
	<div class="guide-title">
		<?php echo $config['title'];?>
	</div>
	<div class="gap_spacer"></div>
	<div class="section-frame">
		<ul>
			<?php foreach($config['items'] as $each):?>
				<?php if(strcmp($_SESSION['DEFAULT_LOCALE'], 'en_NZ') == 0 && strcmp($each['region'],'NZ') == 0):?>
					<?php if(strlen($each['url']) > 0):?>
						<li><a href="<?php echo $each['url'];?>" gae-category="Home Sidebar"><?php echo $each['content'];?></a></li>
					<?php else:?>
						<li><?php echo $each['content'];?></li>
					<?php endif;?>
				<?php elseif(strcmp($_SESSION['DEFAULT_LOCALE'], 'en_AU') == 0 && strcmp($each['region'],'AU') == 0):?>
					<?php if(strlen($each['url']) > 0):?>
						<li><a href="<?php echo $each['url'];?>" gae-category="Home Sidebar"><?php echo $each['content'];?></a></li>
					<?php else:?>
						<li><?php echo $each['content'];?></li>
					<?php endif;?>
				<?php elseif(strcmp($each['region'],'ALL') == 0):?>
					<?php if(strlen($each['url']) > 0):?>
						<li><a href="<?php echo $each['url'];?>" gae-category="Home Sidebar"><?php echo $each['content'];?></a></li>
					<?php else:?>
						<li><?php echo $each['content'];?></li>
					<?php endif;?>
				<?php endif;?>
			<?php endforeach;?>
		</ul>
	</div>
	<div class="gap_spacer"></div>
	<?php endif;?>
	
	<div class="gap_spacer_yellow"></div>
	

	
	<a href="http://www.cambridge.edu.au/go" gae-category="Home Outbound links" gae-value="Cambridge GO">
		<div class="link_section">
			<div class="block_cambridge_go">
				<div class="content">
					<h5>Cambridge GO</h5>
					<p>Digital resources and material</p>
				</div>
			</div>
		</div>
	</a>
	
	<div class="gap_spacer_yellow"></div>
	<a href="http://www.hotmaths.com.au/" gae-category="Home Outbound links" gae-value="HOTmaths">
		<div class="link_section">
			<div class="block_cambridge_hotmaths">
				<div class="content">
					<h5>Cambridge HOTmaths</h5>
					<p>interactive maths online</p>
				</div>
			</div>
		</div>
	</a>


	<div class="gap_spacer_yellow"></div>
	<a href="http://dynamicscience.cambridge.edu.au/">
		<div class="link_section">
			<div class="block_dynamic_science">
				<div class="content">
					<h5>Dynamic Science</h5>
					<p>Dynamic Science</p>
				</div>
			</div>
		</div>
	</a>


	<div class="gap_spacer_yellow"></div>
	<a href="http://www.cambridge.edu.au/checkpoints">
		<div class="link_section">
			<div class="block_cambridge_checkpoints">
				<div class="content">
					<h5>Cambridge Checkpoints</h5>
					<p>VCE & HSC study guides</p>
				</div>
			</div>
		</div>
	</a>
	
	<div class="gap_spacer_yellow"></div>
	<a href="http://www.cambridge.org/au/elt/?site_locale=en_AU">
		<div class="link_section">
			<div class="block_elt">
				<div class="content">
					<h5>ELT</h5>
					<p>English Language Teaching</p>
				</div>
			</div>
		</div>
	</a>
	
	


	
	<!--
	<a href="http://l.cambridge.edu.au/go">
		<div class="link_section">
			<div class="block_cambridge_go"></div>
			<div class="content">
				<h5>Cambridge GO</h5>
				<p>Digital resources and material</p>
			</div>
		</div>
	</a>
	
	<div class="gap_spacer_yellow"></div>
	<a href="http://www.hotmaths.com.au/">
		<div class="link_section">
			<div class="block_cambridge_hotmaths"></div>
			<div class="content">
				<h5>Cambridge HOTmaths</h5>
				<p>interactive maths online</p>
			</div>
		</div>
	</a>
	
	<div class="gap_spacer_yellow"></div>
	<a href="http://l.cambridge.edu.au/education/companion/checkpoints/index.html">
		<div class="link_section">
			<div class="block_cambridge_checkpoints"></div>
			<div class="content">
				<h5>Cambridge Checkpoints</h5>
				<p>VCE & HSC study guides</p>
			</div>
		</div>
	</a>
	
	<div class="gap_spacer_yellow"></div>
	<a href="http://www.cambridge.org/au/elt/?site_locale=en_AU">
		<div class="link_section">
			<div class="block_elt"></div>
			<div class="content">
				<h5>ELT</h5>
				<p>English Language Teaching</p>
			</div>
		</div>
	</a>
	-->
	<div class="gap_spacer"></div>
</div>

<script>
	jQuery('.advance-search-frame .title').click(function(){
			var form = jQuery('.advance-search-frame .form');
			if(form.hasClass('active')){
				form.slideUp('slow', function(){
					form.removeClass('active')
				});
			}else{
				form.slideDown('slow', function(){
					form.addClass('active')
				});
			}
			
		});
	
	jQuery('.simple-search-frame button.search').click(function(){
			var simple_input_dom = jQuery('.simple-search-frame input[name="simple_search_query"]');
			if(simple_input_dom.hasClass('empty')){
				alert("Please input search keyword");
			}else{
				var val = jQuery('.simple-search-frame input[name="simple_search_query"]').val();
				var url = "<?php echo $ch->buildQuery('education/search', array('q_keywords'=>''));?>";
				url += encodeURIComponent(val);
				//alert(url);
				location.href = url;
			}
		});

	jQuery('.simple-search-frame input[name="simple_search_query"]').keypress(function(e){
		code= (e.keyCode ? e.keyCode : e.which);
        if (code == 13) jQuery('.simple-search-frame button.search').trigger('click');
	});
</script>