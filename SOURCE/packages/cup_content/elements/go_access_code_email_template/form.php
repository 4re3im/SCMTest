<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<?php  
$form = Loader::helper('form');
$wform = Loader::helper('wform', 'cup_content');

$v = View::getInstance();
$html = Loader::helper('html');
$th = Loader::helper('concrete/urls'); 
?>

<div class="span16">

	<div class="clearfix">
		<?php echo $form->label('title', t('Subject Title') . '')?>
		<div class="input">
			<?php echo $form->text('title', @$entry['title'], array('class' => "span6"))?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('content_html', t('Content (HTML)') . '')?>
		<div class="input">
			<div>
				Replacement Tag:<br/>
				%%FIRST_NAME%% &nbsp; &nbsp; %%LAST_NAME%%<br/>
				%%ACCESSCODE_1%% &nbsp; &nbsp; %%ACCESSCODE_2%% &nbsp; &nbsp; %%ACCESSCODE_3%%
			</div>
			<?php  Loader::element('editor_init'); ?>
			<?php  Loader::element('editor_config'); ?>
			<?php  Loader::element('editor_controls', array('mode'=>'full')); ?>
			<?php echo $form->textarea('content_html', @$entry['content_html'], array('class' => "ccm-advanced-editor"))?>

		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('content_text', t('Content (TEXT)') . '')?>
		<div class="input">
			<div>
				Replacement Tag:<br/>
				%%FIRST_NAME%% &nbsp; &nbsp; %%LAST_NAME%%<br/>
				%%ACCESSCODE_1%% &nbsp; &nbsp; %%ACCESSCODE_2%% &nbsp; &nbsp; %%ACCESSCODE_3%%
			</div>
			<?php echo $form->textarea('content_text', @$entry['content_text'], array('class' => "span6", 'style'=>'height:400px;resize:vertical;'))?>

		</div>
	</div>
	
</div>

<div class="clearfix"></div>
