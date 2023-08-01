<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

class CupContentAuthor extends Object {
	protected $id = FALSE;
	protected $name = FALSE;
	protected $prettyUrl = FALSE;
	protected $biography = FALSE;
	protected $createdAt = FALSE;
	protected $modifiedAt = FALSE;
	
	protected $submit_data = false;
	protected $system_errors = array();
	protected $errors = array();
	
	protected $exisiting_result = array();
	
	function __construct($id = false) {
		if($id){
			$db = Loader::db();
			$q = "select * from CupContentAuthor where id = ?";	
			$result = $db->getRow($q, array($id));
			
			if($result){
				
				$this->id 				= $result['id'];
				$this->name				= $result['name'];
				$this->prettyUrl		= $result['prettyUrl'];
				$this->biography		= $result['biography'];
				$this->createdAt		= $result['createdAt'];
				$this->modifiedAt		= $result['modifiedAt'];
				
				$this->exisiting_result = $result;
			}
		}
	}
	
	public static function fetchByID($id){
		$object = new CupContentAuthor($id);
		if($object->id === FALSE){
			return FALSE;
		}else{
			return $object;
		}
	}
	
	public static function fetchByPrettyUrl($prettyUrl){
		$object = new CupContentAuthor();
		$object->loadByPrettyUrl($prettyUrl);
		
		if($object->id === FALSE){
			return FALSE;
		}else{
			return $object;
		}
	}
	
	public static function fetchByName($tmp_name){
		$object = new CupContentAuthor();
		$object->loadByName($tmp_name);
		
		if($object->id === FALSE){
			return FALSE;
		}else{
			return $object;
		}
	}
	
	public function loadByID($requestID){
		$this->id = FALSE;
		$this->name = FALSE;
		$this->biography = FALSE;
		$this->createdAt = FALSE;
		$this->modifiedAt = FALSE;
	
		$db = Loader::db();
		$q = "select * from CupContentAuthor where id = ?";	
		$result = $db->getRow($q, array($requestID));
		
		if($result){
			
			$this->id 				= $result['id'];
			$this->name				= $result['name'];
			$this->prettyUrl		= $result['prettyUrl'];
			$this->biography		= $result['biography'];
			$this->createdAt		= $result['createdAt'];
			$this->modifiedAt		= $result['modifiedAt'];
			
			$this->exisiting_result = $result;
			
		}else{
			return false;
		}
	}
	
	public function loadByPrettyUrl($prettyUrl){
		$this->id = FALSE;
		$this->name = FALSE;
		$this->prettyUrl = FALSE;
		$this->biography = FALSE;
		$this->createdAt = FALSE;
		$this->modifiedAt = FALSE;
	
		$db = Loader::db();
		$q = "select * from CupContentAuthor where prettyUrl = ?";	
		$result = $db->getRow($q, array($prettyUrl));
		
		if($result){
			
			$this->id 				= $result['id'];
			$this->name				= $result['name'];
			$this->prettyUrl		= $result['prettyUrl'];
			$this->biography		= $result['biography'];
			$this->createdAt		= $result['createdAt'];
			$this->modifiedAt		= $result['modifiedAt'];
			
			$this->exisiting_result = $result;
			
		}else{
			return false;
		}
	}
	
	public function loadByName($tmp_name){
		$this->id = FALSE;
		$this->name = FALSE;
		$this->prettyUrl = FALSE;
		$this->biography = FALSE;
		$this->createdAt = FALSE;
		$this->modifiedAt = FALSE;
	
		$db = Loader::db();
		$q = "select * from CupContentAuthor where name = ?";	
		$result = $db->getRow($q, array($tmp_name));
		
		if($result){
			
			$this->id 				= $result['id'];
			$this->name				= $result['name'];
			$this->prettyUrl		= $result['prettyUrl'];
			$this->biography		= $result['biography'];
			$this->createdAt		= $result['createdAt'];
			$this->modifiedAt		= $result['modifiedAt'];
			
			$this->exisiting_result = $result;
			
		}else{
			return false;
		}
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
	
	public function getUrl(){
		return '/authors/'.$this->prettyUrl;
	}
	
	public function getAssoc(){
		$temp = array(
					'id' => $this->id,
					'name' => $this->name,
					'prettyUrl' => $result['prettyUrl'],
					'biography' => $this->biography,
					'createdAt' => $this->createdAt,
					'modifiedAt' => $this->modifiedAt
				);
				
		if($temp['id'] === FALSE){
			$temp['id'] = '';
		}
		
		return $temp;
	}
	
	public function setSubmitData($post){
		$this->submit_data = $post;
	}
	
	public function save(){
		if($this->validataion()){
			
			Loader::helper('tools', 'cup_content');
			$this->prettyUrl = CupContentToolsHelper::string2prettyURL($this->name);
				
			if($this->id > 0){	//update
				
				$this->modifiedAt = date('Y-m-d H:i:s');
				
				$db = Loader::db();
				$q = "update CupContentAuthor set name = ?, prettyUrl = ?, biography = ?, createdAt = ?, modifiedAt = ? WHERE id = ?";
				$v = array($this->name, $this->prettyUrl, $this->biography, $this->createdAt, $this->modifiedAt, $this->id);
				$r = $db->prepare($q);
				$res = $db->Execute($r, $v);
				if ($res) {
					$this->afterUpdate();
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
	
	protected function afterUpdate(){
		if(strcmp($this->name, $this->exisiting_result['name']) != 0){
			
			$db = Loader::db();
			$q = "UPDATE CupContentTitleAuthors SET author = ? WHERE author = ?";
			$v = array($this->name, $this->exisiting_result['name']);
			$r = $db->prepare($q);
			$res = $db->Execute($r, $v);
		}
	}
	
	
	
	public function saveNew(){
		$this->createdAt = date('Y-m-d H:i:s');
		$this->modifiedAt = $this->createdAt;
				
		$db = Loader::db();
		$q = "INSERT INTO CupContentAuthor (name, prettyUrl, biography, createdAt, modifiedAt) VALUES (?, ?, ?, ?, ?)";
		$v = array($this->name, $this->prettyUrl, $this->biography, $this->createdAt, $this->modifiedAt);
		$r = $db->prepare($q);
		$res = $db->Execute($r, $v);
		
		if ($res) {
			$this->loadByID($db->Insert_ID());
			return true;
		}else{
			return false;
		}
	}
	
	public function delete(){
		if($this->id > 0){
			$db = Loader::db();
			$q = "DELETE FROM CupContentAuthor WHERE id = ?";
				
			$result = $db->Execute($q, array($this->id));
			if($result){
				return true;
			}else{
				$this->errors[] = "Error occurs when deleting this format";
				return false;
			}
		}else{
			$this->errors[] = "id is missing";
			return false;
		}
	}
	
	public function validataion(){
		$this->name = trim($this->name);
	
		$this->errors = array();
		
		if(strlen($this->name) < 1){
			$this->errors[] = "Name is required";
		}else{
			$db = Loader::db();
			$params = array($this->name);
			$q = "select count(id) as count from CupContentAuthor WHERE name LIKE ?";
			if($this->id > 0){
				$q .= ' AND id <> ?';
				$params[] = $this->id;
			}
			$db_result = $db->getRow($q, $params);
		
			if($db_result['count'] > 0){
				$this->errors[] = "Name has been used";
			}
		}
		
		if(count($this->errors) > 0){
			return false;
		}
		
		return true;
	}
}