<?php
/**
 * CupGoTabOrders Model Class
 * SB-174 added by machua 20190520
 */

defined('C5_EXECUTE') or die(_("Access Denied."));

class CupGoTabOrders extends Object
{
    protected $id = false;
    protected $existing_result = array();

    function __construct($id = false)
    {
        if ($id) {
            $db = Loader::db();
            $sql = "SELECT * FROM CupGoTabOrders WHERE id = ?";
            $result = $db->getRow($sql, array($id));

            if ($result) {
                $this->id 			    = $result['ID'];
                $this->entitlementId    = $result['EntitlementId'];
                $this->tabId		    = $result['TabId'];
                $this->orderNumber      = $result['OrderNumber'];
                $this->existing_result = $result;
            }
        }
    }

    public static function getOrderByProductIdAndTabId($productId, $tabId)
    {
        $db = Loader::db();

        $sql = "SELECT OrderNumber FROM CupGoTabOrders WHERE ProductId = ? AND TabId = ?";
        $result = $db->getRow($sql, array($productId, $tabId));

        if (!$result) {
            return 0;
        } else {
            return $result['OrderNumber'];
        }
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }

        return $this;
    }
}
