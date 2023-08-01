<?php
	defined('C5_EXECUTE') or die(_("Access Denied."));
	$form = Loader::helper('form');
	$al = Loader::helper('concrete/asset_library');
?>

<div style="margin: 0px 20px">
<div class="row">
    <?php
        echo $form->label('title', 'Title');
        echo $form->text('title', $title);
    ?>
</div>

<div class="row">
    <?php
		$color_opts = array(
			'#FFFFFF' => 'white',
			'#FF4604' => 'orange'
		);
        echo $form->label('title_color', 'Title Color');
        echo $form->select('title_color', $color_opts, $title_color);
    ?>
</div>

<div class="row">
    <?php
        echo $form->label('caption', 'Caption');
        echo $form->text('caption', $caption);
    ?>
</div>

<div class="row">
    <?php
		$color_opts = array(
			'#FFFFFF' => 'white',
			'#FF4604' => 'orange'
		);
        echo $form->label('caption_color', 'caption Color');
        echo $form->select('caption_color', $color_opts, $caption_color);
    ?>
</div>

<div class="row">
    <?php
		$enable_opts = array(
			'1' => 'Yes',
			'0' => 'No'
		);
        echo $form->label('enable_overlay_heading', 'Enable Overlay Heading');
        echo $form->select('enable_overlay_heading', $enable_opts, $enable_overlay_heading);
    ?>
</div>

<?php
	
   // echo $al->file('optional-ID', 'fID', t('Pick a file.'), $bf, $args);
?>
<div class="row">
	<?php
		echo $form->label('fID', 'Background Image');
		$file = null;
		if($fID){
			$file =  File::getByID($fID);
		}
		echo $al->image('fID', 'fID', t('Pick an image.'), $file);
	?>
</div>
<div>