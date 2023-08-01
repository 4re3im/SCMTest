<?php
	$th = Loader::helper('text');
	$nh = Loader::helper('navigation');
	
	$config_interval = 6;
	if(isset($slide_interval)){
		$config_interval = $slide_interval;
	}
?>
<div class="cup-headline-viewer-frame" config_interval="<?php echo $config_interval;?>">
	<?php foreach($pages as $page):?>
	<?php
		$title = $th->entities($page->getCollectionName());
		$url = $nh->getLinkToCollection($page);
		$target = ($page->getCollectionPointerExternalLink() != '' && $page->openCollectionPointerExternalLinkInNewWindow()) ? '_blank' : $page->getAttribute('nav_target');
		$target = empty($target) ? '_self' : $target;
		$description = $page->getCollectionDescription();
		$description = $controller->truncateSummaries ? $th->shorten($description, $controller->truncateChars) : $description;
		$description = $th->entities($description);	
		$date_string = $page->cDateModified;
		$date = date('j/n/Y', strtotime($date_string));
		
		$tmp = array('title'=>$title,
					'url'=>$url,
					'target'=>$target,
					'description'=>$description,
					'cDateModified' => $date);
	?>
	<div class="cup-headline-item">
		<a class="btn_more" href="<?php echo $url;?>"><div class="btn_more">&nbsp;MORE&nbsp;</div></a>
		<h5><?php echo $date;?> | <?php echo $title;?></h5>
		<p><?php echo $description;?></p>
	</div>
	<?php endforeach;?>
</div>

<div style="clear:both; width:0px; height:0px;"></div>