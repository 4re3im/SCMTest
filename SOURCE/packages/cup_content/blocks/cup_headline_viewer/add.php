<?php

	$form = Loader::helper('form');
	$page_form = Loader::helper('form/page_selector');
?>
<p>Headline Viewer</p>

<?php echo $form->label('cParentID','Select a parent page');?>
<?php echo $page_form->selectPage('cParentID');?>

<?php echo $form->label('slide_interval','Interval Time:');?>
<?php echo $form->select('slide_interval', 
								array('4'=>'4', '5'=>'5', '6'=>'6',
									'7'=>'7', '8'=>'8', '9'=>'9',
									'10'=>'10', '15'=>'15', '20'=>'20')
							);?> seconds