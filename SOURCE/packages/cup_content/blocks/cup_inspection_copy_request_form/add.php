<?php

	$form = Loader::helper('form');
?>
<p>Cup Simple Header</p>


<?php echo $form->label('title','Title');?><br/>
<?php echo $form->textarea('title', '', array('style'=>'width: 350px'));?> 