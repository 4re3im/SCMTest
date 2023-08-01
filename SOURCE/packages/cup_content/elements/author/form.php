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
		<?php echo $form->label('biography', t('Biography') . '	<span class="ccm-required">*</span>')?>
		<div class="input">
			<?php  Loader::element('editor_init'); ?>
			<?php  Loader::element('editor_config'); ?>
			<?php  Loader::element('editor_controls', array('mode'=>'full')); ?>
			<?php echo $form->textarea('biography', @$entry['biography'], array('class' => "ccm-advanced-editor"))?>
		</div>
	</div>
</div>

<div class="clearfix"></div>