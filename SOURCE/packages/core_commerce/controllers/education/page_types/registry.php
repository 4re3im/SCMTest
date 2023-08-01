<?php  defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::controller('/cart');
include_once(Loader::pageTypeControllerPath('wish_list', 'core_commerce'));
class RegistryPageTypeController extends WishlistPageTypeController { }