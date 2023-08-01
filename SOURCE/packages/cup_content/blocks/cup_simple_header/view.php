<?php

Loader::element('frontend/simple_heading', array(), 'cup_content');

if($title):?>
	<div class="cup-content-simple-heading-page-title">
		<h1><?php echo $title;?></h1>
	</div>
<?php endif;?>