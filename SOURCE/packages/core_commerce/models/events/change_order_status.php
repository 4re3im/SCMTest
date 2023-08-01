<?php   defined('C5_EXECUTE') or die("Access Denied."); 
class ChangeOrderStatus {
   /**
    * This gets fired when an order status changes.
    * This file is currently a stub for any developers interested in extending it.
         * @param CoreCommerceOrder $order
         * @param integer           $status
    */
   public function onChange($order, $status, $oldStatus) {
      if($status == $oldStatus){
         //return; //or whatever
      }
      switch ($status) {
         case CoreCommerceOrder::STATUS_NEW :
            //order should never "change" to new
            break;
         case CoreCommerceOrder::STATUS_PENDING :
            break;
         case CoreCommerceOrder::STATUS_AUTHORIZED:
            break;
         case CoreCommerceOrder::STATUS_SHIPPED:
            break;
         case CoreCommerceOrder::STATUS_COMPLETE:
            break;
         case CoreCommerceOrder::STATUS_CANCELLED:
            break;
         default:
            //unrecognized status
            break;
      }
   }
}
