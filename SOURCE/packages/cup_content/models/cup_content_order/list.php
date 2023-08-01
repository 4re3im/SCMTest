<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model('cup_content_order/model', 'cup_content');
class CupContentOrderList extends DatabaseItemList { 

	protected $queryCreated = 0;
	protected $attributeFilters = array();
	protected $autoSortColumns = array('orderID', 'invoiceID', 'status', 'vistaOrderID', 'vistaOrderID_digital', 'email', 'modifiedAt', 'createdAt');
	protected $itemsPerPage = 10;
	protected $attributeClass = '';	//'CoreCommerceProductAttributeKey';
	
	/*
	// magic method for filtering by attributes. //
	public function __call($nm, $a) {
		if (substr($nm, 0, 8) == 'filterBy') {
			$txt = Loader::helper('text');
			$attrib = $txt->uncamelcase(substr($nm, 8));
			if (count($a) == 2) {
				$this->filterByAttribute($attrib, $a[0], $a[1]);
			} else {
				$this->filterByAttribute($attrib, $a[0]);
			}
		}			
	}
	*/
	
	public function get($itemsToGet = 0, $offset = 0) {
		$list = array();
		$this->createQuery();
		$r = parent::get($itemsToGet, $offset);

		foreach($r as $row) {
			$obj = new CupContentOrder($row['id']);
			//$obj->prspDisplayOrder = $row['prspDisplayOrder'];
			$obj->email = $row['email'];
			$list[] = $obj;
		}
		return $list;
	}
	
	public function getTotal(){
		$this->createQuery();
		return parent::getTotal();
	}
	
	protected function setBaseQuery() {
		$this->setQuery('SELECT cpo.orderID, cpo.id, cco.oEmail as email from CupContentOrder cpo, CoreCommerceOrders cco');
		$this->filter(false, 'cpo.orderID = cco.orderID');
	}
	
	protected function createQuery(){
		if(!$this->queryCreated){
			$this->setBaseQuery();
			$this->queryCreated = 1;
		}
	}
	
	public function filterByOrderID($orderID) {
		$this->filter('cpo.orderID','%'.$orderID,'like');
	}
	
	public function filterByInvoiceID($invoiceID) {
		$this->filter('cpo.invoiceID','%'.$invoiceID,'like');
	}
	
	public function filterByEmail($email) {
        $this->filter('cco.oEmail','%'.$email.'%','like');
	}
	
	public function filterByVistaOrderID($vistaID) {
		//$this->filter('cpo.vistaOrderID','%'.$vistaID,'like');
		//$this->filter('cpo.vistaOrderID','%'.$vistaID,'like');
		$this->filter(false, "(cpo.vistaOrderID like '%{$vistaID}%' OR cpo.vistaOrderID_nz like '%{$vistaID}%' OR cpo.vistaOrderID_digital like '%{$vistaID}%')");
	}
	
	public function filterByStatus($status) {
		$this->filter('cpo.status', $status, 'like');
	}
	
	public function filterByModifiedAt($date, $comparison = '<') {
		$this->filter('cpo.modifiedAt', $date, $comparison);
	}

    public function filterByModifiedAtStart($datetime_string){
        $this->filter('cpo.modifiedAt', $datetime_string, '>=');
    }

    public function filterByModifiedAtEnd($datetime_string){
        $this->filter('cpo.modifiedAt', $datetime_string, '<=');
    }
}