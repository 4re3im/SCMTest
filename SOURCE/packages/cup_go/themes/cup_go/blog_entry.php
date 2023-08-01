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
   <?php
		$html = Loader::helper('html');
		
   ?>
   <link rel="stylesheet" type="text/css" href="<?php print $this->getStyleSheet('css/simple_header.css'); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php print $this->getStyleSheet('css/blog_entry.css'); ?>" />
</head>
<body>
	<?php $this->inc('elements/header.php');?>
	
	<div class="cup-content-area-main">
		<?php Loader::element('frontend/simple_heading', array(), 'cup_content');?>
		<div class="cup-content-simple-heading-page-title">
			<h1><?php echo $c->getCollectionName();?></h1>
		</div>
		
		<div class="blog_entry_content">
			<div class="pageSection">
				<?php  $ai = new Area('Blog Post Header'); $ai->display($c); ?>
			</div>
			<div class="pageSection">
				<!-- <h1><?php  echo $c->getCollectionName(); ?></h1> -->
				<p class="meta"><?php  echo t('Posted by')?> <?php  echo $c->getVersionObject()->getVersionAuthorUserName(); ?> on <?php  echo $c->getCollectionDatePublic('F j, Y'); ?></p>		
			</div>
			<div class="pageSection">
				<?php  $as = new Area('Main'); $as->display($c); ?>
			</div>
			<div class="pageSection">
				<?php  $a = new Area('Blog Post More'); $a->display($c); ?>
			</div>
			<div class="pageSection">
				<?php  $ai = new Area('Blog Post Footer'); $ai->display($c); ?>
			</div>
			<div class="spacer">&nbsp;</div>	
		</div>
	</div>
	
	<?php $this->inc('elements/footer.php'); ?>
	<?php $this->inc('elements/google_analytics_tracking.php');?>
</body>
</html>
<?php endif;?>
