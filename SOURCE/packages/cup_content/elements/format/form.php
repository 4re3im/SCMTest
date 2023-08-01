<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<?php  
$form = Loader::helper('form');

$v = View::getInstance();
$html = Loader::helper('html');
$th = Loader::helper('concrete/urls'); 

$this->addHeaderItem($html->javascript('tiny_mce/tiny_mce.js'));


if (is_object($format)) { 
	
}

?>

<?php echo $form->hidden('id', @$entry['id']); ?>

<div class="span16">
	<div class="clearfix">
		<?php echo $form->label('name', t('Name') . '	<span class="ccm-required">*</span>')?>
		<div class="input">
			<?php echo $form->text('name', @$entry['name'], array('class' => "span6"))?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('shortDescription', t('Short Description') . '	<span class="ccm-required">*</span>')?>
		<div class="input">
			<?php echo $form->textarea('shortDescription', @$entry['shortDescription'], array('class' => "span6"))?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('longDescription', t('Long Description') . '	<span class="ccm-required">*</span>')?>
		<div class="input">
			<?php echo $form->textarea('longDescription', @$entry['longDescription'], array('class' => "span6"))?>
		</div>
	</div>
	
	
	
	<div class="clearfix">
		<?php echo $form->label('isDigital', t('Is Digital') . '	<span class="ccm-required">*</span>')?>
		<div class="input">
			<?php echo $form->select('isDigital', array('0'=>'NO', '1'=>'YES'), @$entry['isDigital'], array('class' => "span6"));?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('image', t('ICON') . '	<span class="ccm-required">*</span>')?>
		<div class="input">
			<?php echo $form->file('image', '', array('class' => "span6"));?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('image_small', t('ICON Small (20px x 20px)') . '	<span class="ccm-required">*</span>')?>
		<div class="input">
			<?php echo $form->file('image_small', '', array('class' => "span6"));?>
		</div>
	</div>
</div>

<div class="clearfix"></div>