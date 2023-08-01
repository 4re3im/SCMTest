<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
class CoreCommerceDiscount extends Object {
	
	public function getDiscountName() { return $this->discountName;}
	public function getDiscountHandle() { return $this->discountHandle;}
	public function getDiscountID() { return $this->discountID;}
	public function getDiscountStart() { return $this->discountStart;}
	public function getDiscountEnd() { return $this->discountEnd;}
	public function getDiscountCode() { return $this->discountCode;}
	public function isDiscountEnabled() { return $this->discountIsEnabled;}

	protected function load($discountID) {
		$db = Loader::db();
		$row = $db->GetRow('select discountID, discountHandle, discountName, discountIsEnabled, discountStart, discountEnd, discountCode, discountTypeID from CoreCommerceDiscounts where discountID = ?', array($discountID));
		$this->setPropertiesFromArray($row);
	}
	
	public static function getByID($discountID) {
		$ed = new CoreCommerceDiscount();
		$ed->load($discountID);
		return $ed;
	}

	public static function getByCode($discountCode) {
		$db = Loader::db();
		$discountID = $db->GetOne('select discountID from CoreCommerceDiscounts where discountCode = ?', array($discountCode));
		if ($discountID) {
			$ed = new CoreCommerceDiscount();
			$ed->load($discountID);
			return $ed;
		}
	}
	
	public function getDiscountType() {
		return CoreCommerceDiscountType::getByID($this->discountTypeID);
	}
	
	public function setupEnabledDiscounts($order) {
		$list = CoreCommerceDiscount::getList();
		foreach($list as $d) {
			$d->getController()->deleteDiscount();
		}
		foreach($list as $d) {
			// 1. If a discount has a code, and the code equals our order discount code, we apply
			if (!($d->validate() instanceof ValidationErrorHelper) && $d->getDiscountCode() != '' && $order->getAttribute('discount_code') == $d->getDiscountCode()) {
				$d->getController()->applyDiscount();
			}

			// 2. If a discount has no code, but is enabled, it always applies
			if (!($d->validate() instanceof ValidationErrorHelper) && $d->getDiscountCode() == '') {
				$d->getController()->applyDiscount();
			}
		}		
	}
	
	/** 
	 * Returns a list of all attributes of this category
	 */
	public static function getList($filters = array()) {
		$db = Loader::db();
		$q = 'select discountID from CoreCommerceDiscounts where 1=1';
		foreach($filters as $key => $value) {
			if (is_string($key)) {
				$q .= ' and ' . $key . ' = ' . $value . ' ';
			} else {
				$q .= ' and ' . $value . ' ';
			}
		}
		$r = $db->Execute($q);
		$list = array();
		while ($row = $r->FetchRow()) {
			$list[] = CoreCommerceDiscount::getByID($row['discountID']);
		}
		$r->Close();
		return $list;
	}
	
	public static function getTotal($filters = array()) {
		$db = Loader::db();
		$q = 'select count(discountID) from CoreCommerceDiscounts where 1=1';
		foreach($filters as $key => $value) {
			if (is_string($key)) {
				$q .= ' and ' . $key . ' = ' . $value . ' ';
			} else {
				$q .= ' and ' . $value . ' ';
			}
		}
		$r = $db->GetOne($q);
		return $r;
	}
	
	public function add($type, $args) {
		$txt = Loader::helper('text');
		$discountTypeID = $type->getDiscountTypeID();

		extract($args);
		
		$_discountStart = null;
		$_discountEnd = null;
		$_discountIsEnabled = 1;
		
		if (!$discountIsEnabled) {
			$_discountIsEnabled = 0;
		}
		if ($discountStart) {
			$_discountStart = $discountStart;
		}
		if ($discountEnd) {
			$_discountEnd = $discountEnd;
		}
		$db = Loader::db();
		$a = array($discountHandle, $discountName, $discountIsEnabled, $_discountStart, $_discountEnd, $discountCode, $discountTypeID);
		$r = $db->query("insert into CoreCommerceDiscounts (discountHandle, discountName, discountIsEnabled, discountStart, discountEnd, discountCode, discountTypeID) values (?, ?, ?, ?, ?, ?, ?)", $a);
		
		if ($r) {
			$discountID = $db->Insert_ID();
			$ed = CoreCommerceDiscount::getByID($discountID);
			$cnt = $ed->getController();
			$cnt->save($args);
			return $ed;
		}
	}
	
	public function setDiscountGroups($gArray) {
		$db = Loader::db();
		$db->Execute('delete from CoreCommerceDiscountGroups where discountID = ?', array($this->getDiscountID()));
		if (is_array($gArray)) {
			foreach($gArray as $go) {
				if (is_object($go)) {
					$gID = $go->getGroupID();
				} else {
					$gID = $go;
				}
				$cnt = $db->GetOne('select count(discountID) from CoreCommerceDiscountGroups where gID = ? and discountID = ?', array($gID, $this->getDiscountID()));
				if ($cnt < 1) { 
					$db->Execute('insert into CoreCommerceDiscountGroups (gID, discountID) values (?, ?)', array($gID, $this->getDiscountID()));
				}
			}
		}
	}

	public function setDiscountUsers($uArray) {
		$db = Loader::db();
		$db->Execute('delete from CoreCommerceDiscountUsers where discountID = ?', array($this->getDiscountID()));
		if (is_array($uArray)) {
			foreach($uArray as $uo) {
				if (is_object($uo)) {
					$uID = $uo->getUserID();
				} else {
					$uID = $uo;
				}
				$cnt = $db->GetOne('select count(discountID) from CoreCommerceDiscountUsers where uID = ? and discountID = ?', array($uID, $this->getDiscountID()));
				if ($cnt < 1) { 
					$db->Execute('insert into CoreCommerceDiscountUsers (uID, discountID) values (?, ?)', array($uID, $this->getDiscountID()));
				}
			}
		}
	}

	public function getDiscountUsers() {
		$users = array();
		$db = Loader::db();
		$r = $db->Execute('select cd.uID from CoreCommerceDiscountUsers cd inner join Users on Users.uID = cd.uID where cd.discountID = ? order by uName asc', array($this->getDiscountID()));
		while ($row = $r->FetchRow()) {
			$ui = UserInfo::getByID($row['uID']);
			if (is_object($ui)) {
				$users[] = $ui;
			}
		}
		return $users;
	}

	public function getDiscountGroups() {
		$groups = array();
		$db = Loader::db();
		$r = $db->Execute('select cd.gID from CoreCommerceDiscountGroups cd inner join Groups on Groups.gID = cd.gID where cd.discountID = ? order by gName asc', array($this->getDiscountID()));
		while ($row = $r->FetchRow()) {
			$g = Group::getByID($row['gID']);
			if (is_object($g)) {
				$groups[] = $g;
			}
		}
		return $groups;
	}

	public function update($args) {
		extract($args);
		
		$_discountIsEnabled = 1;
		$_discountStart = null;
		$_discountEnd = null;
		
		if (!$discountIsEnabled) {
			$_discountIsEnabled = 0;
		}
		if ($discountStart) {
			$_discountStart = $discountStart;
		}
		if ($discountEnd) {
			$_discountEnd = $discountEnd;
		}


		$db = Loader::db();

		$a = array($discountHandle, $discountName, $discountIsEnabled, $_discountStart, $_discountEnd, $discountCode, $this->getDiscountID());
		$r = $db->query("update CoreCommerceDiscounts set discountHandle = ?, discountName = ?, discountIsEnabled = ?, discountStart = ?, discountEnd = ?, discountCode = ? where discountID = ?", $a);
		
		if ($r) {
			$ed = CoreCommerceDiscount::getByID($this->discountID);
			$cnt = $ed->getController();
			$cnt->save($args);
			return $ed;
		}
	}
	
	public function delete() {
		$dt = $this->getDiscountType();
		$cnt = $this->getController();
		$cnt->delete();
		
		$db = Loader::db();
		$db->Execute('delete from CoreCommerceDiscounts where discountID = ?', array($this->getDiscountID()));
	}

	public function getController() {
		Loader::model('discount/type', 'core_commerce');
		$at = CoreCommerceDiscountType::getByID($this->discountTypeID);
		$cnt = $at->getController();
		$cnt->setDiscount($this);
		return $cnt;
	}
	
	public function validate() {
		$dh = Loader::helper('date');
		$error = Loader::helper('validation/error');
		
		// started
		$discountStart = $this->getDiscountStart();
		if(isset($discountStart) && strlen($discountStart)) {
			$dt = new DateTime($discountStart);
			if($dt > new DateTime()) {
				$error->add(t('Invalid coupon code.'));
				return $error;
			}
		}
		
		// ended
		$discountEnd = $this->getDiscountEnd();
		if(isset($discountEnd) && strlen($discountEnd)) {
			$dt = new DateTime($discountEnd);
			if($dt <= new DateTime()) {
				$error->add(t('Coupon code expired.'));
				return $error;
			}
		}
		
		// enabled
		if(!$this->isDiscountEnabled()) { 
			$error->add(t('Invalid coupon code.'));			
			return $error;
		}
		
		// check to see users and groups
		$discountPermIsValid = false;
		// users first
		$users = $this->getDiscountUsers();
		$groups = $this->getDiscountGroups();
		$u = new User();
		if (count($users) > 0 || count($groups) > 0) {
			foreach($users as $ui) {
				if ($u->getUserID() == $ui->getUserID()) {
					$discountPermIsValid = true;
					break;
				}
			}
			foreach($groups as $g) {
				if ($g->getGroupID() == GUEST_GROUP_ID && (!$u->isRegistered())) {
					$discountPermIsValid = true;
					break;
				}
				if ($g->getGroupID() == REGISTERED_GROUP_ID && ($u->isRegistered())) {
					$discountPermIsValid = true;
					break;
				}
				if ($u->inGroup($g)) {
					$discountPermIsValid = true;
					break;
				}
			}
		} else {
			$discountPermIsValid = true;
		}
		
		if(!$discountPermIsValid) { 
			$error->add(t('Invalid coupon code.'));			
			return $error;
		}
		
		return $this->getController()->validate();
	}
	
}