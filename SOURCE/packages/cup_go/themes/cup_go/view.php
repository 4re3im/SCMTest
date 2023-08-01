<?php defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<?php if(isset($theme_competition) && $theme_competition):?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
   <?php $this->inc('elements/html_head_competition.php');?>
</head>
<body>
	<?php $this->inc('elements/header_competition.php');?>
	
	<div class="cup-content-area-main">
		<?php 

		print $innerContent;
		
		?>
	</div>
	
	<?php $this->inc('elements/footer_competition.php'); ?>
	<?php $this->inc('elements/google_analytics_tracking.php');?>
</body>
</html>
<?php else:?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
   <?php $this->inc('elements/html_head.php');?>
   
    
</head>
<body>
	<?php $this->inc('elements/header.php');?>
        <?php $this->inc('elements/alert_message_header.php');?>
	
	<div class="cup-content-area-main">
		<?php 

		print $innerContent;
		
		?>
	</div>
	
	<?php $this->inc('elements/footer.php'); ?>
	<?php $this->inc('elements/google_analytics_tracking.php');?>
</body>
</html>
<?php endif;?>