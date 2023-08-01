<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

class CupContentGoAccessCodeEmailTemplate extends Object {
	protected $fields = array(
						'titleID', 'isbn13', 'title',
						'content_html', 'content_text',
						'modifiedAt', 'createdAt' 
					);
					
	protected $record = array();
	
	protected $errors = array();
	
	function __construct($titleID = false) {
		if($titleID){
			//$this->record['titleID']	= $titleID;
			
			$db = Loader::db();
			$q = "select * from CupContentGoAccessCodeEmailTemplate where titleID = ?";	
			$result = $db->getRow($q, array($titleID));
			
			if($result){
				$this->record	= $result;
			}
		}
	}
	
	public static function fetchByTitleID($id){
		$object = new CupContentGoAccessCodeEmailTemplate($id);
		if($object->titleID === FALSE){
			return FALSE;
		}else{
			return $object;
		}
	}
	
	public function loadByTitleID($id){
		if($id){
			$db = Loader::db();
			$q = "select * from CupContentGoAccessCodeEmailTemplate where titleID = ?";	
			$result = $db->getRow($q, array($id));
			
			if($result){
				$this->record = $result;
			}
		}
	}
	
	public function __get($property) {
		if (isset($this->record[$property])) {
			return $this->record[$property];
		}else{
			return false;
		}
	}

	public function __set($property, $value) {
		if (in_array($property, $this->fields)) {
			$this->record[$property] = $value;
		}
		
		return $this;
	}
	
	
	public function setPost($post){
		/*
		foreach($post as $key => $value){
			if(in_array($key, $this->fields)){
				$this->record[$key] = $value;
			}
		}
		*/
		foreach($this->fields as $key){
			
			if(isset($post[$key])){
				$this->record[$key] = $post[$key];
			}elseif(!isset($this->record[$key])){
				$this->record[$key] = "";
			}
		}
		
	}
	
	public function getAssoc(){
		return $this->record;
	}
	
	
	
	
	
	public function save(){
		if($this->validation()){
		
			$obj = CupContentGoAccessCodeEmailTemplate::fetchByTitleID($this->record['titleID']);
		
			if($obj){	//update
				$this->record['modifiedAt'] = date('Y-m-d H:i:s');
				
				$db = Loader::db();
				$q = "update CupContentGoAccessCodeEmailTemplate 
						set 
							isbn13 = ?, title = ?, 
							content_html = ?, content_text = ?, 
							createdAt = ?, modifiedAt = ?
						WHERE titleID = ?";
				$v = array($this->record['isbn13'], $this->record['title'], 
						$this->record['content_html'], $this->record['content_text'], 
						$this->record['createdAt'], $this->record['modifiedAt'], 
						$this->record['titleID']);
				$r = $db->prepare($q);
				$res = $db->Execute($r, $v);
				if ($res) {
					$this->loadByTitleID($this->record['titleID']);
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
		$this->record['createdAt'] = date('Y-m-d H:i:s');
		$this->record['modifiedAt'] = $this->record['createdAt'];
				
		$db = Loader::db();
		$q = "INSERT INTO CupContentGoAccessCodeEmailTemplate 
							(
								titleID, isbn13, title,
								content_html, content_text, 
								modifiedAt, createdAt
							) 
						VALUES (?, ?, ?,
								?, ?, 
								?, ?)";
		$v = array($this->record['titleID'], $this->record['isbn13'], $this->record['title'], 
					$this->record['content_html'], $this->record['content_text'], 
					$this->record['modifiedAt'], $this->record['createdAt']);
		$r = $db->prepare($q);
		$res = $db->Execute($r, $v);
		
		if ($res) {
			//$this->loadByID($db->Insert_ID());
			$this->loadByTitleID($this->record['titleID']);
			return true;
		}else{
			return false;
		}
	}
	
	public function delete(){
		if($this->record['titleID'] > 0){
			$db = Loader::db();
			$q = "DELETE FROM CupContentGoAccessCodeEmailTemplate WHERE titleID = ?";
				
			$result = $db->Execute($q, array($this->record['titleID']));
			if($result){
				return true;
			}else{
				$this->errors[] = "Error occurs when deleting this Email Template";
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
	
	public function validation(){
		$this->record['title'] = trim($this->record['title']);
		$this->record['content_html'] = trim($this->record['content_html']);
		$this->record['content_text'] = trim($this->record['content_text']);
	
		$this->errors = array();
		
		$titleObj = CupContentTitle::fetchByID($this->record['titleID']);
		if(!$titleObj){
			$this->errors[] = "titleID is invalid";
		}else{
			$this->record['isbn13'] = $titleObj->isbn13;
		}
		
		if(strlen($this->record['content_html']) < 1){
			$this->errors[] = "Content HTML is required";
		}
		
		if(count($this->errors) > 0){
			return false;
		}
		
		return true;
	}
}

?>