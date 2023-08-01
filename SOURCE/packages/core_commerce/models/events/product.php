<?php   defined('C5_EXECUTE') or die("Access Denied.");

class ProductEvents {
   /*
    * cleans up products that grant group membership so that people without accounts
    * can buy the product still and not get asked to log in, even though the group
    * no longer exists.
    *   * @param Group $group
    */
   function userGroupDeleted($group) {
      $db = Loader::db();
      $db->query('delete from CoreCommerceProductSearchPurchaseGroups where gID = ?',array($group->getGroupID()));
   }
}
