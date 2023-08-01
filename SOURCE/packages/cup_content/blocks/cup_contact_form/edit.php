<?php

	$form = Loader::helper('form');
?>
<p>Cup Simple Header</p>

<?php echo $form->label('title','Header Content');?><br/>
<?php echo $form->textarea('title', $title, array('style'=>'width: 350px'));?> 