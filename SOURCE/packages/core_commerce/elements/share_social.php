<?php 
/*
 * To add you're social sharing icons, override this element by copying it to your site's elements folder.
 * use the $link and $page variables to reference this page in your sharing code 
 * By default this page won't display anything 
 */
if($page instanceof Page) {
	$nav = Loader::helper('navigation');
	$link = $nav->getLinkToCollection($page,true);
	
	$page; // page object for the page we're referencing
	$link; // full url for the page
}
?>