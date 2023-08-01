<?php
defined('C5_EXECUTE') or die(_("Access Denied."));


define('TITLE_DOWNLOADALE_FOLDER', DIR_BASE.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'cup_content'.DIRECTORY_SEPARATOR.
				'title_downloadable'.DIRECTORY_SEPARATOR);

class CupContentTitleDownloadableFileOrderRecord extends Object {
	protected $id = FALSE;
	protected $orderID = FALSE;
	protected $invoiceID = FALSE;
	protected $fID		= FALSE;
	protected $hashKey = FALSE;
	protected $download_count = FALSE;
	protected $expiredAt = FALSE;
	protected $modifiedAt = FALSE;
	protected $createdAt = FALSE;
	
	
	function __construct($id = false) {
		if($id){
			$db = Loader::db();
			$q = "select * from CupContentTitleDownloadableFileOrderRecord where id = ?";	
			$result = $db->getRow($q, array($id));
			
			if($result){
				
				$this->id 				= $result['id'];
				$this->orderID			= $result['orderID'];
				$this->invoiceID		= $result['invoiceID'];
				$this->fID				= $result['fID'];
				$this->hashKey 			= $result['hashKey'];
				$this->download_count	= $result['download_count'];
				$this->expiredAt 		= $result['expiredAt'];
				$this->modifiedAt		= $result['modifiedAt'];
				$this->createdAt 		= $result['createdAt'];
			}
		}
	}
	

	public static function fetchByID($id){
		$object = new CupContentTitleDownloadableFileOrderRecord($id);
		if($object->id === FALSE){
			return FALSE;
		}else{
			return $object;
		}
	}
	
	public static function fetchByRequestKey($key){
		$key = base64_decode($key);
		if($key === FALSE){
			return false;
		}
		
		$key = base64_decode($key);
		if($key === FALSE){
			return false;
		}
		
		$key = explode(':', $key);
		
		
		$db = Loader::db();
		$q = "select * from CupContentTitleDownloadableFileOrderRecord where orderID = ? AND fID = ? AND hashKey = ?";	
		$result = $db->getAll($q, $key);
		
		if($result){
			foreach($result as $each){
				return new CupContentTitleDownloadableFileOrderRecord($each['id']);
			}
			
		}else{
			return false;
		}
		
	}
	
	public function loadByID($requestID){
		$this->id 				= FALSE;
		$this->orderID			= FALSE;
		$this->invoiceID		= FALSE;
		$this->fID				= FALSE;
		$this->hashKey 			= FALSE;
		$this->download_count	= FALSE;
		$this->expiredAt			= FALSE;
		$this->modifiedAt		= FALSE;
		$this->createdAt		= FALSE;
		
		
		$db = Loader::db();
		$q = "select * from CupContentTitleDownloadableFileOrderRecord where id = ?";	
		$result = $db->getRow($q, array($requestID));
		
		if($result){
			$this->id 				= $result['id'];
			$this->orderID			= $result['orderID'];
			$this->invoiceID		= $result['invoiceID'];
			$this->fID				= $result['fID'];
			$this->hashKey 			= $result['hashKey'];
			$this->download_count	= $result['download_count'];
			$this->expiredAt 		= $result['expiredAt'];
			$this->modifiedAt		= $result['modifiedAt'];
			$this->createdAt 		= $result['createdAt'];
		}
	}
	
	public function getAssoc(){
		$temp = array(
					'id'			=> $this->id,
					'orderID'		=> $this->orderID,
					'invoiceID'		=> $this->invoiceID,
					'fID'			=> $this->fID,
					'hashKey'		=> $this->hashKey,
					'download_count'=> $this->download_count,
					'expiredAt'		=> $this->expiredAt,
					'modifiedAt'	=> $this->modifiedAt,
					'createdAt'		=> $this->createdAt
				);
		return $temp;
	}
	
	public function __get($property) {
		if (property_exists($this, $property)) {
			return $this->$property;
		}
	}

	public function __set($property, $value) {
		if (property_exists($this, $property)) {
			$this->$property = $value;
		}

		return $this;
	}
	
	public function save(){
		if($this->validation()){
		
			if(isset($this->id) && $this->id > 0){	//update
				$this->modifiedAt = date('Y-m-d H:i:s');
				
				$db = Loader::db();
				$q = "update CupContentTitleDownloadableFileOrderRecord set orderID = ?, invoiceID = ?, fID = ?, 
								download_count = ?, expiredAt = ?, 
								modifiedAt = ? WHERE id = ?";
				$v = array($this->orderID, $this->invoiceID, $this->fID, 
						$this->download_count, $this->expiredAt,
						$this->modifiedAt, $this->id);
				$r = $db->prepare($q);
				$res = $db->Execute($r, $v);
				if ($res) {
					$this->loadByID($this->id);
					return true;
				}else{
					return false;
				}
			}else{	//insert
				return $this->saveNew();
			}
		}else{
			return false;
		}
	}
	
	public function saveNew(){
		$this->createdAt = date('Y-m-d H:i:s');
		$this->modifiedAt = $this->createdAt;
				
		$this->hashKey = substr(md5(time()), 0, 10);
		$this->download_count = 0;
			
		$db = Loader::db();
		$q = "INSERT INTO CupContentTitleDownloadableFileOrderRecord 
							(
								orderID, invoiceID, fID, hashKey, download_count, 
								expiredAt, modifiedAt, createdAt
							)
						VALUES (?, ?, ?, ?, ?, 
								?, ?, ?)";
		$v = array($this->orderID, $this->invoiceID, $this->fID, $this->hashKey, $this->download_count,
					$this->expiredAt, $this->modifiedAt, $this->createdAt);
		$r = $db->prepare($q);
		$res = $db->Execute($r, $v);
		
		if ($res) {
			$new_id = $db->Insert_ID();
				
			$this->loadByID($new_id);
			return true;
		}else{
			return false;
		}
	}
	
	public function isExpired(){
		$now = time();
		$expired_time = strtotime($this->expiredAt);
		if($now > $expired_time){
			return true;
		}else{
			return false;
		}
	}
	
	public function delete(){
		if($this->id > 0){
			$db = Loader::db();
			$q = "DELETE FROM CupContentTitleDownloadableFileOrderRecord WHERE id = ?";
				
			$result = $db->Execute($q, array($this->id));
			if($result){
				return true;
			}else{
				$this->errors[] = "Error occurs when deleting this sample page";
				return false;
			}
		}else{
			$this->errors[] = "id is missing";
			return false;
		}
	}
	
	public function getErrors(){
		return $this->errors;
	}
	
	public function generateRequestKey(){
		$str = $this->orderID.':'.$this->fID.':'.$this->hashKey;
		$str = base64_encode(base64_encode($str));
		return $str;
	}
	
	public function generateDownloadUrl(){
		$ch = Loader::helper('cup_content_html', 'cup_content');
		$uh = Loader::helper('url');
		$key = $this->generateRequestKey();
		$arg = array(
					'request'=>$key,
				);
		return BASE_URL.$uh->buildQuery( Loader::helper('concrete/urls')->getToolsURL('title_downloadable_file/download', 'cup_content'), $arg);
	}
	
	public function validation(){
		$this->errors = array();
	
		$this->orderID = trim($this->orderID);
		$this->invoiceID = trim($this->invoiceID);
		$this->fID = trim($this->fID);
		
		if(strlen($this->orderID) < 1){
			$this->errors[] = "orderID is required";
		}
		
		if(strlen($this->invoiceID) < 1){
			$this->errors[] = "invoiceID is required";
		}
		
		if(strlen($this->fID) < 1){
			$this->errors[] = "fID is required";
		}
		
		
		
		if(count($this->errors) > 0){
			return false;
		}
		
		return true;
	}
}

