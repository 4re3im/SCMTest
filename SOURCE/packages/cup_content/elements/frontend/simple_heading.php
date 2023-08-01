<?php
	$uh = Loader::helper('url');
	$ch = Loader::helper('cup_content_html', 'cup_content');
	Loader::model('subject/list', 'cup_content');

    $exclusions = CupContentSubjectList::getExclusionList();

	$singlePage = Loader::model('single_page');
	$subjects = array('primary'=>array(), 'secondary'=>array());
	
	$subjectList = new CupContentSubjectList();
	$subjectList->setItemsPerPage(25);
	$subjectList->filterByDepartment('primary');
	
	if(strcmp($_SESSION['DEFAULT_LOCALE'], 'en_NZ') == 0){
		$subjectList->filterByRegion('NZ');
	}else{
		$subjectList->filterByRegion('AU');
	}
	
	$subjects['primary'] = array();
	$subjectList->sortBy('name', 'ASC');
    $searchResult = $subjectList->getPage();
    foreach($searchResult as $each){
        if(!in_array($each->name, $exclusions["primary"])){
            array_push($subjects['primary'], $each);
        }
    }
	
	$subjectList = new CupContentSubjectList();
	$subjectList->setItemsPerPage(25);
	$subjectList->filterByDepartment('secondary');
	
	if(strcmp($_SESSION['DEFAULT_LOCALE'], 'en_NZ') == 0){
		$subjectList->filterByRegion('NZ');
	}else{
		$subjectList->filterByRegion('AU');
	}

    $subjects['secondary'] = array();
	$subjectList->sortBy('name', 'ASC');
    $searchResult = $subjectList->getPage();
    foreach($searchResult as $each){
        if(!in_array($each->name, $exclusions["secondary"])){
            array_push($subjects['secondary'], $each);
        }
    }
	
?>
		
<div class="cup-content-simple-heading">
	<div class="guide_line">
		<div class="simple-search-frame">
			<button class="search"></button>
			<div class="input_frame">
				<input type="text" name="simple_search_query"/>
			</div>
		</div>
		<div class="gap_spacer"></div>
	</div>

	<div class="btn_primary">
		<div class="value">PRIMARY</div>
		<div class="bottom_pad"><div class="inner"></div></div>
	</div>
	<div class="btn_secondary">
		<div class="value">SECONDARY</div>
		<div class="bottom_pad"><div class="inner"></div></div>
	</div>
	
	
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
				$a_href = $ch->buildQuery('/education/search', array('q_department'=>'Primary'));
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
				$a_href = $ch->buildQuery('/education/search', array('q_department'=>'Secondary'));
			?>
			<div class="foot"><div class="btn_browse_all"><a href="<?php echo $a_href;?>">Browse all</a></div></div>
		</div>
	</div>
</div>

<script>
	jQuery(document).ready(function() {
		jQuery('.cup-content-simple-heading').each(function(){
			var master_frame = jQuery(this);
			var btn_parimary = jQuery(this).find('.btn_primary');
			var btn_secondary = jQuery(this).find('.btn_secondary');
			var list_frame = master_frame.find('.subject_list_frame');
			list_frame.css('zIndex', 100);
			
			var subjects = master_frame.find('.subject_list_frame .subjects');
			var primary_subjects = master_frame.find('.subjects#primary');
			var secondary_subjects = master_frame.find('.subjects#secondary');
			
			btn_parimary.css('cursor', 'pointer');
			btn_secondary.css('cursor', 'pointer');
			
			btn_parimary.click(function(){
				if(jQuery(this).hasClass('active')){
					jQuery(this).removeClass('active');
					list_frame.hide();
				}else{
					btn_secondary.removeClass('active');
					jQuery(this).addClass('active');
					subjects.hide();
					primary_subjects.show();
					list_frame.show();
				}
			});
			
			btn_secondary.click(function(){
				if(jQuery(this).hasClass('active')){
					jQuery(this).removeClass('active');
					list_frame.hide();
				}else{
					btn_parimary.removeClass('active');
					jQuery(this).addClass('active');
					subjects.hide();
					secondary_subjects.show();
					list_frame.show();
				}
			});
			
			master_frame.mouseleave(function(){
			if(btn_parimary.hasClass('active')){
				btn_parimary.trigger('click');
			}else if(btn_secondary.hasClass('active')){
				btn_secondary.trigger('click');
			}
		});
		});
	
		function cup_global_simple_field_focusout(){
			var simple_input_dom = jQuery('.cup-content-simple-heading input[name="simple_search_query"]');
			if(jQuery.trim(simple_input_dom.val()).length < 1){
				simple_input_dom.val('Search');
				simple_input_dom.addClass('empty');
			}else{
				simple_input_dom.removeClass('empty');
			}
		}

		function cup_global_simple_field_focusin(){
			var simple_input_dom = jQuery('.cup-content-simple-heading input[name="simple_search_query"]');
			if(simple_input_dom.hasClass('empty')){
				simple_input_dom.val('');
				simple_input_dom.removeClass('empty')
			}
		}
	
		cup_global_simple_field_focusout();
		jQuery('.cup-content-simple-heading input[name="simple_search_query"]').focusout(cup_global_simple_field_focusout);
		jQuery('.cup-content-simple-heading input[name="simple_search_query"]').focusin(cup_global_simple_field_focusin);
		
		jQuery('.simple-search-frame button.search').click(function(){
			var simple_input_dom = jQuery('.cup-content-simple-heading input[name="simple_search_query"]');
			if(simple_input_dom.hasClass('empty')){
				alert("Please input search keyword");
			}else{
				var val = jQuery('.cup-content-simple-heading input[name="simple_search_query"]').val();
				var url = "<?php echo $ch->buildQuery('/education/search', array('q_keywords'=>''));?>";
				url += encodeURIComponent(val);
				//alert(url);
				location.href = url;
			}
		});
		
		jQuery('.cup-content-simple-heading input[name="simple_search_query"]').keypress(function(e){
			code= (e.keyCode ? e.keyCode : e.which);
			if (code == 13) jQuery('.simple-search-frame button.search').trigger('click');
		});
	});
</script>