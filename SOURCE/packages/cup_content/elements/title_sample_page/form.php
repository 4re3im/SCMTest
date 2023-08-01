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
	<?php if(isset($entry['filename']) && strlen($entry['filename']) > 1):?>
		<div class="clearfix" style="padding: 5px 10px; border: 1px dotted #000000;">
			Exisitng Filename: <strong><?php echo $entry['filename'];?></strong>
		</div>
		<div style="width: 100%;height: 30px"></div>
	<?php endif;?>

	<div class="clearfix">
		<?php echo $form->label('description', t('Description'))?>
		<div class="input">
			<?php echo $form->text('description', @$entry['description'], array('class' => "span6", 'style'=>'height: 18px; line-height: 18px; font-size: 12px;'))?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('file', t('File'))?>
		<div class="input">
			<?php echo $form->file('file', '', array('class' => "span6"))?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('is_page_proof', t('Publish to Page Proof Central'))?>
		<div class="input">
			<?php echo $form->radio('is_page_proof', "1", @$entry['is_page_proof'], array())?> Yes 
			&nbsp;&nbsp;&nbsp;
			<?php echo $form->radio('is_page_proof', "0", @$entry['is_page_proof'], array())?> No
		</div>
	</div>
	
</div>

<div class="clearfix"></div>

