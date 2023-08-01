<?php
defined('C5_EXECUTE') or die(_("Access Denied."));


define('TITLE_DOWNLOADALE_FOLDER', DIR_BASE.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'cup_content'.DIRECTORY_SEPARATOR.
				'title_downloadable'.DIRECTORY_SEPARATOR);

class CupContentTitleDownloadableFile extends Object {
	protected $id = FALSE;
	protected $titleID = FALSE;
	protected $filename = FALSE;
	protected $filemeta = FALSE;
	protected $filesize = FALSE;
	protected $description = FALSE;
	protected $expiry_type = FALSE;
	protected $modifiedAt = FALSE;
	protected $createdAt = FALSE;
	
	protected $isFileUpload		= FALSE;
	
	
	function __construct($id = false) {
		if($id){
			$db = Loader::db();
			$q = "select * from CupContentTitleDownloadableFile where id = ?";	
			$result = $db->getRow($q, array($id));
			
			if($result){
				
				$this->id 				= $result['id'];
				$this->titleID			= $result['titleID'];
				$this->filename			= $result['filename'];
				$this->filemeta 		= $result['filemeta'];
				$this->filesize			= $result['filesize'];
				$this->description 		= $result['description'];
				$this->expiry_type		= $result['expiry_type'];
				
				$this->modifiedAt		= $result['modifiedAt'];
				$this->createdAt 		= $result['createdAt'];
			}
		}
	}
	

	public static function fetchByID($id){
		$object = new CupContentTitleDownloadableFile($id);
		if($object->id === FALSE){
			return FALSE;
		}else{
			return $object;
		}
	}
	
	public static function fetchAllByTitleID($title_id){
		$objects = array();
		
		$db = Loader::db();
		$q = "select * from CupContentTitleDownloadableFile where titleID = ?";	
		$result = $db->getAll($q, array($title_id));
		
		if($result){
			foreach($result as $each){
				$objects[] = new CupContentTitleDownloadableFile($each['id']);
			}
			
			if(count($objects) > 0){
				return $objects;
			}
		}
			
		return false;
		
	}
	
	public static function fetchOneByTitleID($title_id){

		$db = Loader::db();
		$q = "select * from CupContentTitleDownloadableFile where titleID = ?";	
		$result = $db->getAll($q, array($title_id));
		
		if($result){
			foreach($result as $each){
				return new CupContentTitleDownloadableFile($each['id']);
			}
			
		}
			
		return false;
		
	}
	
	public function loadByID($requestID){
		$this->id 				= FALSE;
		$this->titleID			= FALSE;
		$this->filename			= FALSE;
		$this->filemeta 		= FALSE;
		$this->filesize			= FALSE;
		$this->description		= FALSE;
		$this->expiry_type		= FALSE;
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
			$this->expiry_type		= $result['expiry_type'];
			
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
					'expiry_type'	=> $this->expiry_type,
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
				$q = "update CupContentTitleDownloadableFile set titleID = ?, filename = ?, 
								filemeta = ?, filesize = ?, description = ?, 
								expiry_type = ?,
								modifiedAt = ? WHERE id = ?";
				$v = array($this->titleID, $this->filename, 
						$this->filemeta, $this->filesize, $this->description,
						$this->expiry_type,
						$this->modifiedAt, $this->id);
				$r = $db->prepare($q);
				$res = $db->Execute($r, $v);
				if ($res) {
					
					if($this->isFileUpload){
						$write_to_dir = TITLE_DOWNLOADALE_FOLDER.DIRECTORY_SEPARATOR.$this->titleID;
						if(!is_dir($write_to_dir)){
							mkdir($write_to_dir, 0777, true);
						}
						
						$write_to_dir = rtrim($write_to_dir, DIRECTORY_SEPARATOR);
						
						
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
		$q = "INSERT INTO CupContentTitleDownloadableFile 
							(
								titleID, filename, filemeta, filesize, 
								description, expiry_type, modifiedAt, createdAt
							)
						VALUES (?, ?, ?, ?, 
								?, ?, ?, ?)";
		$v = array($this->titleID, $this->filename, $this->filemeta, $this->filesize,
					$this->description, $this->expiry_type, $this->modifiedAt, $this->createdAt);
		$r = $db->prepare($q);
		$res = $db->Execute($r, $v);
		
		if ($res) {
			$new_id = $db->Insert_ID();
			
				$write_to_dir = TITLE_DOWNLOADALE_FOLDER.DIRECTORY_SEPARATOR.$this->titleID;
				if(!is_dir($write_to_dir)){
					mkdir($write_to_dir, 0777, true);
				}
				
				$write_to_dir = rtrim($write_to_dir, DIRECTORY_SEPARATOR);
				
				$dest_filename = $write_to_dir.DIRECTORY_SEPARATOR.$new_id;
				move_uploaded_file($_FILES['file']['tmp_name'], $dest_filename);
				chmod($dest_filename , 0777);
				
			$this->loadByID($new_id);
			return true;
		}else{
			return false;
		}
	}
	
	public static function getFileDir(){
		return TITLE_DOWNLOADALE_FOLDER.DIRECTORY_SEPARATOR.$this->titleID;
	}
	
	public function getFilePath(){
		$filepath = rtrim(TITLE_DOWNLOADALE_FOLDER, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$this->titleID.DIRECTORY_SEPARATOR.$this->id;
		
		if(file_exists($filepath)){
			return $filepath;
		}else{
			return false;
		}
	}
	
	public function delete(){
		if($this->id > 0){
			$db = Loader::db();
			$q = "DELETE FROM CupContentTitleDownloadableFile WHERE id = ?";
				
			$result = $db->Execute($q, array($this->id));
			
			$file_path = $this->getFilePath();
			if($result){
				unlink($file_path);
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
				$this->errors[] = "File is required.";
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
	
	public function downloadFile(){
		$filePath = $this->getFilePath();
		
		if(!$filePath){
			echo "File not available";
		}else{
			$fileName = $this->filename;
			$fileMeta = $this->filemeta;
			header('Content-type: '.$fileMeta);
			header('Content-Disposition: attachment; filename="'.$fileName.'"');
			readfile($filePath);
		}
	}
}

