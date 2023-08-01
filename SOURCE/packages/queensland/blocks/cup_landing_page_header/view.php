<?php

?>

<style>
.cup_landing_header_block{
	height: 280px;
	border: 2px solid #000000;
	border-top: 0px;
	background: #BBBBBB;
	overflow: hidden;
	position: relative;
}

.cup_landing_header_block .title_section{
	position: absolute;
	top: 98px;
	border-left: 24px solid #000000;
	width: 100%;
	height: 140px;
}

.cup_landing_header_block .title_section h1{
	margin: 0px 20px;
	padding: 28px 0px 18px 0px;
	font-size: 48px;
}

.cup_landing_header_block .title_section .caption{
	font-size: 28px;
	margin: 0px 20px;
	padding: 0px 0px 18px 0px;
}
</style>
<!--
<div>
	<?php echo $title;?> | <?php echo $title_color;?>
</div>
<div>
	<?php echo $caption;?> | <?php echo $caption_color;?>
</div>
<div>
	<?php echo $fID;?>
</div>
-->

<?php
$cup_content_pkg = loader::package('cup_content');
if ($cup_content_pkg) {
	Loader::element('frontend/simple_heading', array(), 'cup_content');
}
?>
<div class="cup_landing_header_block">
	<?php
	if ($fID) {
		$file =  File::getByID($fID);
		if($file):
		$fv = $file->getApprovedVersion()
		?>
		<img src="<?php echo $fv->getDownloadURL();?>">
		<?php endif;
	}
	?>
	<?php if ($enable_overlay_heading):?>
	<div class="title_section">
		<h1 style="color:<?php echo $title_color;?>"><?php echo $title;?></h1>
		<div class="caption" style="color:<?php echo $caption_color;?>"><?php echo $caption;?></div>
	</div>
	<?php endif;?>
</div>
