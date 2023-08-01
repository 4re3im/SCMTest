<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); 
// translate this file if we're multilingualing it
if(Loader::helper('multilingual','core_commerce')->isEnabled()) {
	Loader::helper('default_language','multilingual')->setupSiteInterfaceLocalization();
}

$form = Loader::helper('form');
$cID = $_REQUEST['cID'];
$u = new User();
// editing wishlist
if(is_numeric($cID) && $cID > 0) {
	$wl = Page::getByID($cID);	
	if($u->getUserID() == $wl->getCollectionUserID()) {
		$action = View::url($wl->getCollectionPath(),'rename');
		$name = $wl->getCollectionName();
	}
} else {
	$wh = Loader::helper('user_wishlists','core_commerce');
	$lists = $wh->getUserLists();
	if(is_array($lists) && count($lists) && $lists[0] instanceof Page) {
		$action = View::url($lists[0]->getCollectionPath(),'addNew');
	}
}
?>
<div id="ccm-wishlist-dialog-content">
	<h2><?php  echo t('Wishlist Name')?></h2>
	<form action="<?php  echo $action ?>" method="post">
		<?php  echo $form->text('name',$name)?>	
		<?php  echo $form->submit('save',t('Save')); ?>
	</form>
</div>