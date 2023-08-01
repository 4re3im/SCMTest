<?php
/**
 * Description of godpsinfo
 *
 * @author paulbalila
 */
class GodpsInfo {

    public function setAttribute($ak, $value) {
        Loader::model('attribute/categories/go_dps');
        if (!is_object($ak)) {
            $ak = GoDpsAttributeKey::getByHandle($ak);
        }
        $ak->setAttribute($this, $value);
        $this->reindex();
    }

    public function reindex() {
        $attribs = GoDpsAttributeKey::getAttributes($this->getWidgetID(), 'getSearchIndexValue');
        $db = Loader::db();

        $db->Execute('delete from GoDpsSearchIndexAttributes where gdID = ?', array($this->getWidgetID()));
        $searchableAttributes = array('gdID' => $this->getProductID());
        $rs = $db->Execute('select * from GoDpsSearchIndexAttributes where gdID = -1');
        AttributeKey::reindex('GoDpsSearchIndexAttributes', $searchableAttributes, $attribs, $rs);
    }

    public function getAttribute($ak, $displayMode = false) {
        Loader::model('attribute/categories/go_dps');
        if (!is_object($ak)) {
            $ak = GoDpsAttributeKey::getByHandle($ak);
        }
        if (is_object($ak)) {
            $av = $this->getAttributeValueObject($ak);
            if (is_object($av)) {
                return $av->getValue($displayMode);
            }
        }
    }

    public function getAttributeValueObject($ak, $createIfNotFound = false) {
        $db = Loader::db();
        $av = false;
        $v = array($this->getProductID(), $ak->getAttributeKeyID());
        $avID = $db->GetOne("select avID from GoDpsAttributeValues where gdID = ? and akID = ?", $v);
        if ($avID > 0) {
            $av = GoDpsAttributeValue::getByID($avID);
            if (is_object($av)) {
                $av->setWidget($this);
                $av->setAttributeKey($ak);
            }
        }

        if ($createIfNotFound) {
            $cnt = 0;

            // Is this avID in use ?
            if (is_object($av)) {
                $cnt = $db->GetOne("select count(avID) from GoDpsAttributeValues where avID = ?", $av->getAttributeValueID());
            }

            if ((!is_object($av)) || ($cnt > 1)) {
                $av = $ak->addAttributeValue();
            }
        }

        return $av;
    }

}
