<?php  defined('C5_EXECUTE') or die(_("Access Denied."));

   Loader::model('single_page');
   $pkg = Package::getByHandle('core_commerce');
   $cart = SinglePage::add('/cart', $pkg);
   $chk = SinglePage::add('/checkout', $pkg);
   if($chk instanceof Page) {
      $chk->setAttribute('exclude_nav', 1);
      $singlePages = array(
         '/checkout/discount',
         '/checkout/billing',
         '/checkout/shipping',
         '/checkout/shipping/address',
         '/checkout/shipping/method',
         '/checkout/payment',
         '/checkout/payment/method',
         '/checkout/payment/form',
         '/checkout/finish',
         '/checkout/finish_error',
         '/checkout/complete_order'
      );


      foreach($singlePages as $path) {
         if(Page::getByPath($path)->getCollectionID() <= 0) {
            SinglePage::add($path, $pkg);
         }
      }
   }

   if($cart instanceof Page) {
      $cart->setAttribute('exclude_nav', 1);
   }

   if($checkout instanceof Page) {
      $checkout->setAttribute('exclude_nav', 1);
   }



   echo "Created pages.";
