<?php  defined('C5_EXECUTE') or die(_("Access Denied."));
// translate this file if we're multilingualing it
if(Loader::helper('multilingual','core_commerce')->isEnabled()) {
	Loader::helper('default_language','multilingual')->setupSiteInterfaceLocalization();
}
?>
<div id="ccm-wishlist-dialog-content">
	<h2><?php  echo t('Add to wishlist')?></h2>
	<div class="ccm-message" style="display:none;"><?php  echo t('Item added to wishlist.')?></div>
	<form>
<?php 
$u = new User();
if($u->isLoggedIn()) {
	$wlh = Loader::helper('user_wishlists','core_commerce');
	$lists = $wlh->getUserLists(); 
	if(is_array($lists) && count($lists) == 1) { // only one wishlist
		$action = $wlh->getUpdateAction($lists[0]);
		?><input type="button" onclick="ccm_wishlist.submitToList('<?php  echo $action?>')" name="ccm-add-to-wishlist" value="<?php  echo t('Add')?>"/><?php  
	} elseif(is_array($lists) && count($lists) > 1) { // multiple wishlists - select the one ?>
		<select name="wishlist-action" id="ccm-select-wishlist-action">
		<?php  
			foreach($lists as $l) {
				echo '<option value="'.$wlh->getUpdateAction($l).'" '.($_SESSION['coreCommerceLastAccessedWishlistID'] == $l->getCollectionID()?"selected=\"selected\"":"").'>'.
					$l->getCollectionName().'</option>';	
			}
		?>		
		</select>
		<input type="button" onclick="ccm_wishlist.submitToList($('#ccm-select-wishlist-action').val());" name="ccm-add-to-wishlist" value="<?php  echo t('Add')?>"/>
	<?php  } ?>
	<br/>
	<div id="ccm-wishlist-manage-link"><a href="<?php  echo View::url('/wishlists');?>"><?php  echo t('Create and manage wishlists')?></a></div>
<?php  } else {  // not logged in user message ?>
	<div class="ccm-message">
		<?php  echo t('You must be logged in to add items to a wishlist.') ?><br/>
		<a href="<?php echo View::url('/login');?>?rcID=<?php  echo str_replace('"', '&#34;', $_REQUEST['rcID'])?>"><?php  echo t('Login or Register')?></a>
	</div>
<?php  } ?>
	</form>
	<div id="ccm-wishlist-empty"></div>
</div>
