<?php  defined('C5_EXECUTE') or die(_("Access Denied."));
$cart_path = $c->getCollectionPath();
$items = $cart->getProducts();
$pkg = Package::getByHandle('core_commerce');
$return_to_shopping = BASE_URL.View::url(Page::getByID($pkg->config('STORE_ROOT'))->getCollectionPath());
if (isset($error) && $error->has()) {
	$error->output();
} ?>
<div id="ccm-core-commerce-registry">
	<div id="ccm-core-commerce-registry-sidebar">
		<?php  if($this->controller->canManage()) { ?>
			<div id="ccm-core-commerce-registry-otherlists">
			<?php 
			/* Hard coded page list that pulls pages at the current level excluding the current page */
			Loader::model('page_list');
			$pl = new PageList();
			$pl->filterByParentID($c->getCollectionParentID());
			$pl->filter('p1.cID',$c->getCollectionID(),'!=');
			$otherWishlists = $pl->get();
			if(is_array($otherWishlists) && count($otherWishlists)) {
				echo("<ul class=\"nav\">");
				foreach($otherWishlists as $wl) {
					echo '<li><a href="'.$navigation->getLinkToCollection($wl).'">' . $wl->getCollectionName()."</a></li>";	
				}
				echo "</ul>";
			}
			?>
			</div>
			<form>
			<?php  echo $form->button('add-user-registry',t('Create a new Gift Registry'),array('onclick'=>"ccm_coreCommerceCreateWishlist('".$concrete_urls->getToolsURL('wishlist/create_registry','core_commerce')."')"));?>
			</form>
		<?php  } ?>
	</div>
	<div id="ccm-core-commerce-checkout-cart" class="ccm-core-commerce-registry-contents">
		<?php  if($this->controller->canManage()) { ?>
			<div id="ccm-core-commerce-registry-menu">
				<a id="ccm-registry-public-private" onclick="ccm_coreCommerceShareWishlist('<?php  echo $concrete_urls->getToolsURL('wishlist/share_registry','core_commerce')."?cID=".$c->getCollectionID()?>');" href="javascript:void(0);"><?php  echo t('Share')?></a>
				<a id="ccm-registry-rename" onclick="ccm_coreCommerceCreateWishlist('<?php  echo $concrete_urls->getToolsURL('wishlist/create_registry','core_commerce')."?cID=".$c->getCollectionID()?>')" href="javascript:void(0);"><?php  echo t('Rename')?></a>
				<a id="ccm-registry-remove" onclick="return confirm('<?php  echo t('Are you sure?')?>');" href="<?php  echo $this->action('remove')?>"><?php  echo t('Delete')?></a>
			</div>
		<?php  } ?>
		<h2><?php  echo $c->getCollectionName() ?></h2>
		
		<form method="post" action="<?php echo View::url($cart_path, 'update')?>" <?php  if ($dialog) { ?>onsubmit="return ccm_coreCommerceUpdateCart('<?php echo $concrete_urls->getToolsURL('cart_dialog')?>')"<?php  } ?> name="ccm-core-commerce-cart-form<?php  if ($dialog) { ?>-dialog<?php  } ?>" id="ccm-core-commerce-cart-form<?php  if ($dialog) { ?>-dialog<?php  } ?>">
			<?php  Loader::packageElement('registry_item_list', 'core_commerce', array('edit' =>($this->controller->canManage()?true:false), 'ajax' => false, 'cart'=>$cart, 'cart_path'=>$cart_path)); ?>
			<div class="ccm-core-commerce-cart-buttons">
				<?php  
				if (isset($_REQUEST['rcID']) && $_REQUEST['rcID'] > 0) { 
					$rc = Page::getByID($_REQUEST['rcID']);
				} elseif (isset($_SESSION['coreCommerceLastProdutPagecID']) && $_SESSION['coreCommerceLastProdutPagecID'] > 0) {
					$rc = Page::getByID($_SESSION['coreCommerceLastProdutPagecID']);
				}
				?>

				<input type="button" style="float: left" onclick="window.location.href='<?php echo $return_to_shopping?>';" value="<?php   echo t('&lt; Return to Shopping')?>" />

				<?php  if (count($items) > 0) { ?>
					<input type="submit" name="purchase_wishlist" value="<?php echo t('Purchase Gift Registry')?>" class="ccm-core-commerce-cart-buttons-checkout" <?php echo $checkoutDisabled?> />
				<?php  } ?>
				<?php  if($this->controller->canManage()) { ?>
				<input type="submit" class="ccm-core-commerce-cart-buttons-checkout" value="<?php echo t('Update Gift Registry')?>"  />
				<?php  } ?>
				<img src="<?php echo ASSETS_URL_IMAGES?>/throbber_white_16.gif" width="16" height="16" id="ccm-core-commerce-cart-update-loader" />
			</div>
		
		</form>
	</div>
</div>