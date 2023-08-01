<?php
/**
 * Description of go_dps
 *
 * @author paulbalila
 */
class GoDpsAttributeKey extends AttributeKey {
    protected $searchIndexFieldDefinition = 'orderID I(11) UNSIGNED NOTNULL DEFAULT 0 PRIMARY';
    
    public function getIndexedSearchTable() {
        return 'GoDpsSearchIndexAttributes';
    }
    
    public function getAttributes($gdID, $method = 'getValue') {
        $db = Loader::db();
        $values = $db->GetAll("select avID, akID from GoDpsAttributeValues where gdID = ?", array($gdID));
        $avl = new AttributeValueList();
        foreach ($values as $val) {
            $ak = GoDpsAttributeKey::getByID($val['akID']);
            if (is_object($ak)) {
                $value = $ak->getAttributeValue($val['avID'], $method);
                $avl->addAttributeValue($ak, $value);
            }
        }
        return $avl;
    }
    
    public static function getColumnHeaderList() {
        return parent::getList('go_dps', array('akIsColumnHeader' => 1));
    }
    
    public static function getSearchableIndexedList() {
        return parent::getList('go_dps', array('akIsSearchableIndexed' => 1));
    }

    public static function getSearchableList() {
        return parent::getList('go_dps', array('akIsSearchable' => 1));
    }

    public function getAttributeValue($avID, $method = 'getValue') {
        $av = GoDpsAttributeValue::getByID($avID);
        $av->setAttributeKey($this);
        return $av->{$method}();
    }
    
    public static function getByID($akID) {
        $ak = new GoDpsAttributeKey();
        $ak->load($akID);
        if ($ak->getAttributeKeyID() > 0) {
            return $ak;
        }
    }

    public static function getByHandle($akHandle) {
        $ak = CacheLocal::getEntry('go_dps_attribute_key_by_handle', $akHandle);
        if (is_object($ak)) {
            return $ak;
        } else if ($ak == -1) {
            return false;
        }
        $ak = -1;
        $db = Loader::db();

        $q = "SELECT ak.akID 
			FROM AttributeKeys ak
			INNER JOIN AttributeKeyCategories akc ON ak.akCategoryID = akc.akCategoryID 
			WHERE ak.akHandle = ?
			AND akc.akCategoryHandle = 'go_dps'";
        $akID = $db->GetOne($q, array($akHandle));
        if ($akID > 0) {
            $ak = UserAttributeKey::getByID($akID);
        }

        CacheLocal::set('go_dps_attribute_key_by_handle', $akHandle, $ak);
        if ($ak === -1) {
            return false;
        }
        return $ak;
    }

    public static function getList() {
        $list = parent::getList('go_dps');
        usort($list, array('GoDpsAttributeKey', 'sortListByDisplayOrder'));
        return $list;
    }

    public function saveAttribute($uo, $value = false) {
        $av = $object->getAttributeValueObject($this, true);
        parent::saveAttribute($av, $value); 
    }
    
    public function add($type, $args, $pkg = false) {
        $ak = parent::add('go_dps', $type, $args, $pkg);
        
        $db = Loader::db();
        $displayOrder = $db->GetOne('select max(displayOrder) from GoDpsAttributeKeys');
        if (!$displayOrder) {
            $displayOrder = 0;
        }
        $displayOrder++;
        $v = array($ak->getAttributeKeyID(), $displayOrder);
        $db->Execute('insert into GoDpsAttributeKeys (akID, displayOrder) values (?, ?)', $v);

        $nak = new GoDpsAttributeKey();
        $nak->load($ak->getAttributeKeyID());
        return $nak;
    }
    
    public function update($args) {
        $ak = parent::update($args);
        /*

        extract($args);

        if ($uakProfileDisplay != 1) {
            $uakProfileDisplay = 0;
        }
        if ($uakMemberListDisplay != 1) {
            $uakMemberListDisplay = 0;
        }
        if ($uakProfileEdit != 1) {
            $uakProfileEdit = 0;
        }
        if ($uakProfileEditRequired != 1) {
            $uakProfileEditRequired = 0;
        }
        if ($uakRegisterEdit != 1) {
            $uakRegisterEdit = 0;
        }
        if ($uakRegisterEditRequired != 1) {
            $uakRegisterEditRequired = 0;
        }
        $db = Loader::db();
        $v = array($uakProfileDisplay, $uakMemberListDisplay, $uakProfileEdit, $uakProfileEditRequired, $uakRegisterEdit, $uakRegisterEditRequired, $ak->getAttributeKeyID());
        $db->Execute('update UserAttributeKeys set uakProfileDisplay = ?, uakMemberListDisplay = ?, uakProfileEdit= ?, uakProfileEditRequired = ?, uakRegisterEdit = ?, uakRegisterEditRequired = ? where akID = ?', $v);
         *
         */
    }
    
    public function delete() {
        parent::delete();
        $db = Loader::db();
        $db->Execute('delete from GoDpsAttributeKeys where akID = ?', array($this->getAttributeKeyID()));
        $r = $db->Execute('select avID from GoDpsAttributeValues where akID = ?', array($this->getAttributeKeyID()));
        while ($row = $r->FetchRow()) {
            $db->Execute('delete from AttributeValues where avID = ?', array($row['avID']));
        }
        $db->Execute('delete from GoDpsAttributeValues where akID = ?', array($this->getAttributeKeyID()));
    }

}

class GoDpsAttributeValue extends AttributeValue {

    public function setDps($gdo) {
        $this->go_dps = $gdo;
    }

    public static function getByID($avID) {
        $gdav = new GoDpsAttributeValue();
        $gdav->load($avID);
        if ($gdav->getAttributeValueID() == $avID) {
            return $gdav;
        }
    }
    
    public function delete() {
        $db = Loader::db();
        $db->Execute('delete from GoDpsAttributeValues where gdID = ? and akID = ? and avID = ?', array(
            $this->go_dps->getWidgetID(),
            $this->attributeKey->getAttributeKeyID(),
            $this->getAttributeValueID()
        ));

        // Before we run delete() on the parent object, we make sure that attribute value isn't being referenced in the table anywhere else
        $num = $db->GetOne('select count(avID) from GoDpsAttributeValues where avID = ?', array($this->getAttributeValueID()));
        if ($num < 1) {
            parent::delete();
        }
    }

}
