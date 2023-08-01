<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

define('TITLE_SAMPLE_PAGE_DIR', DIR_BASE.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'cup_content'.DIRECTORY_SEPARATOR.'title_sample_page');

class CupContentTitleSamplePage extends Object {
	protected $id = FALSE;
	protected $titleID = FALSE;
	protected $filename = FALSE;
	protected $filemeta = FALSE;
	protected $filesize = FALSE;
	protected $description = FALSE;
	protected $is_page_proof = FALSE;
	protected $modifiedAt = FALSE;
	protected $createdAt = FALSE;

	protected $errors = array();
	
	protected $isFileUpload = FALSE;

	function __construct($id = false) {
		if($id){
			$db = Loader::db();
			$q = "select * from CupContentTitleSamplePages where id = ?";	
			$result = $db->getRow($q, array($id));
			
			if($result){
				
				$this->id 				= $result['id'];
				$this->titleID			= $result['titleID'];
				$this->filename			= $result['filename'];
				$this->filemeta 		= $result['filemeta'];
				$this->filesize			= $result['filesize'];
				$this->description 		= $result['description'];
				$this->is_page_proof 	= $result['is_page_proof'];
				
				$this->modifiedAt		= $result['modifiedAt'];
				$this->createdAt 		= $result['createdAt'];
			}
		}
	}
	
	public static function fetchByID($id){
		$object = new CupContentTitleSamplePage($id);
		if($object->id === FALSE){
			return FALSE;
		}else{
			return $object;
		}
	}
	
	public function loadByID($requestID){
		$this->id 				= FALSE;
		$this->titleID			= FALSE;
		$this->filename			= FALSE;
		$this->filemeta 		= FALSE;
		$this->filesize			= FALSE;
		$this->description		= FALSE;
		$this->is_page_proof 	= FALSE;
		
		$this->modifiedAt		= FALSE;
		$this->createdAt		= FALSE;
		
		$this->isFileUpload		= FALSE;
		
		$db = Loader::db();
		$q = "select * from CupContentTitleSamplePages where id = ?";	
		$result = $db->getRow($q, array($requestID));
		
		if($result){
			$this->id 				= $result['id'];
			$this->titleID			= $result['titleID'];
			$this->filename			= $result['filename'];
			$this->filemeta 		= $result['filemeta'];
			$this->filesize			= $result['filesize'];
			$this->description 		= $result['description'];
			$this->is_page_proof 	= $result['is_page_proof'];
			$this->modifiedAt		= $result['modifiedAt'];
			$this->createdAt 		= $result['createdAt'];
		}
	}
	
	public function getAssoc(){
		$temp = array(
					'id'			=> $this->id,
					'titleID'		=> $this->titleID,
					'filename'		=> $this->filename,
					'filemeta'		=> $this->filemeta,
					'filesize'		=> $this->filesize,
					'description'	=> $this->description,
					'is_page_proof' => $this->is_page_proof,
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
				$q = "update CupContentTitleSamplePages set titleID = ?, filename = ?, 
								filemeta = ?, filesize = ?, description = ?, is_page_proof = ?, modifiedAt = ? WHERE id = ?";
				$v = array($this->titleID, $this->filename, $this->filemeta, $this->filesize,
						$this->description, $this->is_page_proof, $this->modifiedAt, $this->id);
				$r = $db->prepare($q);
				$res = $db->Execute($r, $v);
				if ($res) {
					
					if($this->isFileUpload){
						$write_to_dir = TITLE_SAMPLE_PAGE_DIR.DIRECTORY_SEPARATOR.$this->titleID;
						if(!is_dir($write_to_dir)){
							mkdir($write_to_dir, 0777, true);
						}
						
						$dest_filename = $write_to_dir.DIRECTORY_SEPARATOR.$this->id;
						move_uploaded_file($_FILES['file']['tmp_name'], $dest_filename);
						chmod($dest_filename , 0777);
					}
					
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
				
		$db = Loader::db();
		$q = "INSERT INTO CupContentTitleSamplePages 
							(
								titleID, filename, filemeta, filesize, 
								description, is_page_proof, modifiedAt, createdAt
							)
						VALUES (?, ?, ?, ?, 
								?, ?, ?, ?)";
		$v = array($this->titleID, $this->filename, $this->filemeta, $this->filesize,
					$this->description, $this->is_page_proof, $this->modifiedAt, $this->createdAt);
		$r = $db->prepare($q);
		$res = $db->Execute($r, $v);
		
		if ($res) {
			$new_id = $db->Insert_ID();
			
				$write_to_dir = TITLE_SAMPLE_PAGE_DIR.DIRECTORY_SEPARATOR.$this->titleID;
				if(!is_dir($write_to_dir)){
					mkdir($write_to_dir, 0777, true);
				}
				
				$dest_filename = $write_to_dir.DIRECTORY_SEPARATOR.$new_id;
				move_uploaded_file($_FILES['file']['tmp_name'], $dest_filename);
				chmod($dest_filename , 0777);
				
			$this->loadByID($new_id);
			return true;
		}else{
			return false;
		}
	}
	
	public function getFileDir(){
		return TITLE_SAMPLE_PAGE_DIR.DIRECTORY_SEPARATOR.$this->titleID;
	}
	
	public function getFilePath(){
		$filepath = TITLE_SAMPLE_PAGE_DIR.DIRECTORY_SEPARATOR.$this->titleID.DIRECTORY_SEPARATOR.$this->id;
		
		if(file_exists($filepath)){
			return $filepath;
		}else{
			return false;
		}
	}
	
	public function delete(){
		if($this->id > 0){
			$db = Loader::db();
			$q = "DELETE FROM CupContentTitleSamplePages WHERE id = ?";
				
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
	
	public function validation(){
		$this->errors = array();
	
		$this->titleID = trim($this->titleID);
		$this->filename = trim($this->filename);
		
		$this->description = trim($this->description);
			
		if(intval($this->id) < 1){
			if(!isset($_FILES["file"])){
				$this->errors[] = "Image is required.";
			}elseif($_FILES["file"]["error"] != UPLOAD_ERR_OK){
				$this->errors[] = "file upload error, Please try again";
			}else{
				$this->filename = $_FILES['file']['name'];
				$this->filemeta = $_FILES['file']['type'];
				$this->filesize = $_FILES['file']['size'];
			}
		}else{
			if(isset($_FILES["file"]) && strlen($_FILES['file']['name']) > 1){
				if($_FILES["file"]["error"] != UPLOAD_ERR_OK){
					$this->errors[] = "file upload error, Please try again";
				}else{
					$this->filename = $_FILES['file']['name'];
					$this->filemeta = $_FILES['file']['type'];
					$this->filesize = $_FILES['file']['size'];
					$this->isFileUpload = true;
				}
				
			}
		}
		
		if(strlen($this->description) < 1){
			$this->errors[] = "File description is required";
		}
		
		
		
		if(count($this->errors) > 0){
			return false;
		}
		
		return true;
	}
	
}