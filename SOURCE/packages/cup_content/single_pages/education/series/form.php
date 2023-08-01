<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<?php  
$form = Loader::helper('form');
$wform = Loader::helper('wform', 'cup_content');

$v = View::getInstance();
$html = Loader::helper('html');
$th = Loader::helper('concrete/urls'); 

$this->addHeaderItem($html->javascript('tiny_mce/tiny_mce.js'));
?>

<?php echo $form->hidden('id', @$entry['id']); ?>

<div class="span16">
	<div class="clearfix">
		<?php echo $form->label('search_priority', t('Search Priority'))?>
		<div class="input">
			<?php
				$tmp_opt;
				for($i = 0; $i<=50; $i++){
					$tmp = $i;
					if($i == 0){
						$tmp = $i.' [Default / No priority]';
					}elseif($i == 50){
						$tmp = $i.' [Highest]';
					}
					$tmp_opt[$i] = $tmp;
				}
			?>
			<?php echo $form->select('search_priority', $tmp_opt, @$entry['search_priority'], array('class' => "span6"))?>
		</div>
	</div>

	<div class="clearfix">
		<?php echo $form->label('image', t('IMAGE'))?>
		<div class="input">
			<?php echo $form->file('image', '', array('class' => "span6"))?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('isEnabled', t('Enable'))?>
		<div class="input">
			<?php echo $form->select('isEnabled', array('1'=>'Yes', '0'=>'No'), @$entry['isEnabled'], array('class' => "span6"))?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('seriesID', t('Series ID') . '	<span class="ccm-required">*</span>')?>
		<div class="input">
			<?php echo $form->text('seriesID', @$entry['seriesID'], array('class' => "span6"))?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('name', t('Name') . '	<span class="ccm-required">*</span>')?>
		<div class="input">
			<?php echo $form->text('name', @$entry['name'], array('class' => "span6"))?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('shortDescription', t('Short Description') . '	<span class="ccm-required">*</span>')?>
		<div class="input">
			<?php  Loader::element('editor_init'); ?>
			<?php  Loader::element('editor_config'); ?>
			<?php  Loader::element('editor_controls', array('mode'=>'full')); ?>
			<?php echo $form->textarea('shortDescription', @$entry['shortDescription'], array('class' => "ccm-advanced-editor"))?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('longDescription', t('Long Description') . '	<span class="ccm-required">*</span>')?>
		<div class="input">
			<?php echo $form->textarea('longDescription', @$entry['longDescription'], array('class' => "ccm-advanced-editor"))?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('divisions', t('Division') . '	<span class="ccm-required">*</span>')?>
		<div class="input">
			<?php echo $wform->divionsSelection(@$entry['divisions']);?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('regions', t('Region') . '	<span class="ccm-required">*</span>')?>
		<div class="input">
			<?php //echo $form->textarea('regions', @$entry['regions'], array('class' => "span6"))?>
			<?php echo $wform->regionsSelection(@$entry['regions']);?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('yearLevels', t('Year Levels') . '	<span class="ccm-required">*</span>')?>
		<div class="input">
			<?php //echo $form->text('yearLevels', @$entry['yearLevels'], array('class' => "span6"))?>
			<?php echo $wform->yearlevelSelection(@$entry['yearLevels'], 'yearLevels');?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('formats', t('Formats'))?>
		<div class="input">
			<?php //echo $form->text('formats', @$entry['formats'], array('class' => "span6"))?>
			<?php echo $wform->multipleItems('formats', @$entry['formats']);?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('subjects', t('Subjects'))?>
		<div class="input">
			<?php //echo $form->text('subjects', @$entry['subjects'], array('class' => "span6"))?>
			<?php echo $wform->multipleItems('subjects', @$entry['subjects']);?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('compGoUrl', t('Companion Go URL'))?>
		<div class="input">
			<?php echo $form->text('compGoUrl', @$entry['compGoUrl'], array('class' => "span6"))?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('compHotUrl', t('Companion Hot URL'))?>
		<div class="input">
			<?php echo $form->text('compHotUrl', @$entry['compHotUrl'], array('class' => "span6"))?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('compSiteUrl', t('Companion Site URL'))?>
		<div class="input">
			<?php echo $form->text('compSiteUrl', @$entry['compSiteUrl'], array('class' => "span6"))?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('partnerSiteName', t('Partner Site Name'))?>
		<div class="input">
			<?php echo $form->text('partnerSiteName', @$entry['partnerSiteName'], array('class' => "span6"))?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('partnerSiteUrl', t('Partner Site Url'))?>
		<div class="input">
			<?php echo $form->text('partnerSiteUrl', @$entry['partnerSiteUrl'], array('class' => "span6"))?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('tagline', t('Tagline'))?>
		<div class="input">
			<?php echo $form->textarea('tagline', @$entry['tagline'], array('class' => "span6"))?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('reviews', t('Reviews'))?>
		<div class="input">
			<?php echo $form->textarea('reviews', @$entry['reviews'], array('class' => "ccm-advanced-editor"))?>
		</div>
	</div>
</div>

<div class="clearfix"></div>