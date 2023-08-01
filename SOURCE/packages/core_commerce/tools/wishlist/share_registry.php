<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); 
// translate this file if we're multilingualing it
if(Loader::helper('multilingual','core_commerce')->isEnabled()) {
	Loader::helper('default_language','multilingual')->setupSiteInterfaceLocalization();
}
?>
<div id="ccm-wishlist-dialog-content">
	<h2><?php  echo t('Share Gift Registry')?></h2>
<?php 
$u = new User();
$wishlist = Page::getByID($_REQUEST['cID']);
if(!$u->isLoggedIn() || !$wishlist->getCollectionID()) { 
	die(t('Access Denied'));
} else { 
	$nav = Loader::helper('navigation');
	$link = $nav->getLinkToCollection($wishlist,true);
	?>
	<div id="ccm-wishlist-share-public" <?php  echo ($wishlist->getAttribute('ecommerce_list_is_public')?'':'style="display: none;"')?>>
		<label><?php  echo t('URL:')?></label>
		<input type="text" name="wishlist-link" id="ccm-ecommerce-wishlist-url" value="<?php  echo $link?>"/>
		<?php  Loader::packageElement('share_social','core_commerce',array('page'=>$wishlist)); ?>
		<br/>
		<p><?php  echo t('This list is currently public')?></p>
		<input type="button" name="ccm-wishlist-unshare" value="<?php  echo t('Make Private')?>" onclick="javascript: makePrivate();"/>
	</div>
	<div id="ccm-wishlist-share-private" <?php  echo ($wishlist->getAttribute('ecommerce_list_is_public')?'style="display: none;"':'')?>>		
		<p><?php  echo t('This list is currently private')?></p>
		<input type="button" name="ccm-wishlist-unshare" value="<?php  echo t('Make Public')?>" onclick="javascript: makePublic();"/>
	</div>
	<div id="ccm-wishlist-empty"></div>
<?php  } ?>

</div>
<script language="javascript">
$(function() { 
	$('#ccm-ecommerce-wishlist-url').click(function() {
		$('#ccm-ecommerce-wishlist-url').select();
	});
});

makePublic = function() {
	$.getJSON('<?php  echo View::url($wishlist->getCollectionPath(),'makePublic',1)?>',function(data) {
		if(data.status =='public') {
			$('#ccm-wishlist-share-private').hide();
			$('#ccm-wishlist-share-public').show();
		}
	});	
}; 


makePrivate = function() {
	$.getJSON('<?php  echo View::url($wishlist->getCollectionPath(),'makePublic',0)?>',function(data) {
		if(data.status =='private') {
			$('#ccm-wishlist-share-public').hide();
			$('#ccm-wishlist-share-private').show();
		}
	});	
};
</script>