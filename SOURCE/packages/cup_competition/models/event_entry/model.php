<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model('event/model', 'cup_competition');

define('COMPETITION_FILE_DIR', DIR_BASE.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'cup_competition');
define('COMPETITION_FILE_URL', BASE_URL.DIRECTORY_SEPARATOR.'/files/cup_competition');


class CupCompetitionEventEntry extends Object {
	protected $fields = array(
						'id', 'eventID', 'email', 'first_name', 'last_name',
						'question_data', 'image_filename', 'image_meta', 'image_caption',
						'image_description', 'qa_question', 'qa_answer',
						'status', 'note', 'modifiedAt', 'createdAt'
					);
					
	protected $eventObj = false;
	
	protected $record = array();
	
	protected $errors = array();
	
	protected $hash_method = 'md5';
	
	function __construct($id = false) {
		if($id){
			$db = Loader::db();
			$q = "select * from CupCompetitionEventEntry where id = ?";	
			$result = $db->getRow($q, array($id));
			
			if($result){
				$this->record	= $result;
				
				$question_data = json_decode($this->record['question_data'], true);
				if($form_config !== FALSE){
					$this->record['question_data'] = $question_data;
				}else{
					$this->record['question_data'] = array();
				}
				
				
				$qa_answer = json_decode($this->record['qa_answer'], true);
				if($form_config !== FALSE){
					$this->record['qa_answer'] = $qa_answer;
				}else{
					$this->record['qa_answer'] = array();
				}
				
				$this->eventObj = CupCompetitionEvent::fetchByID($this->record['eventID']);
			}
		}
	}
	
	public static function fetchByID($id){
		$object = new CupCompetitionEventEntry($id);
		if($object->id === FALSE){
			return FALSE;
		}else{
			return $object;
		}
	}
	
	public function loadByID($id){
		if($id){
			$db = Loader::db();
			$q = "select * from CupCompetitionEventEntry where id = ?";	
			$result = $db->getRow($q, array($id));
			
			if($result){
				$this->record	= $result;
				
				$question_data = json_decode($this->record['question_data'], true);
				if($form_config !== FALSE){
					$this->record['question_data'] = $question_data;
				}else{
					$this->record['question_data'] = array();
				}
				
				$qa_answer = json_decode($this->record['qa_answer'], true);
				if($form_config !== FALSE){
					$this->record['qa_answer'] = $qa_answer;
				}else{
					$this->record['qa_answer'] = array();
				}
				
				$this->eventObj = CupCompetitionEvent::fetchByID($this->record['eventID']);
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
	
	public function loadEventObject(){
		if($this->record['eventID'] && !$this->eventObj){
			$this->eventObj = CupCompetitionEvent::fetchByID($this->record['eventID']);
		}
	}
	
	public function setPost($post){
		foreach($this->fields as $key){
			
			if(isset($post[$key])){
				$this->record[$key] = $post[$key];
			}elseif(!isset($this->record[$key])){
				$this->record[$key] = "";
			}
		}
	
		/*
		foreach($post as $key => $value){
			if(in_array($key, $this->fields)){
				$this->record[$key] = $value;
			}
		}
		*/
		
		if(isset($post['fields'])){
			$this->loadEventObject();
			
			$data = $post['fields'];
			$question_data = array();
			
			foreach($this->eventObj->form_config as $each_field){
				$field_name = $each_field['field_name'];
				$enc_field_name = hash($this->hash_method, $field_name);
				if(isset($data[$enc_field_name])){
					$question_data[$field_name] = $data[$enc_field_name];
				}else{
					$question_data[$field_name] = false;
				}
			}
			
			$this->record['question_data'] = $question_data;
			
			/*
			foreach($post['form_config_web']['field_name'] as $idx => $value){
				$field_name = trim($value);
				$field_type = $post['form_config_web']['field_type'][$idx];
				$field_config = trim($post['form_config_web']['field_config'][$idx]);
				
				$form_config[] = array(
									'field_name' => $field_name,
									'field_type' => $field_type,
									'field_config' => $field_config
								);
			}
			
			$this->record['form_config'] = $form_config;
			*/
		}
		
		
		
		if(isset($post['fields_qa'])){
			$this->loadEventObject();
			
			$data = $post['fields_qa'];
			$qa_answer = array();
			
			foreach($this->eventObj->qa_question as $each_field){
				$field_name = $each_field['field_name'];
				$enc_field_name = hash($this->hash_method, $field_name);
				if(isset($data[$enc_field_name])){
					$qa_answer[$field_name] = $data[$enc_field_name];
				}else{
					$qa_answer[$field_name] = false;
				}
			}
			
			$this->record['qa_answer'] = $qa_answer;
		}
	}
	
	public function getAssoc(){
		return $this->record;
	}
	
	public function getEventObject(){
		return $this->eventObj;
	}
	
	public function save(){
		if($this->validation()){
		
			if(isset($this->record['id']) && $this->record['id'] > 0){	//update
				$this->record['modifiedAt'] = date('Y-m-d H:i:s');
				
				/*
					'id', 
						'eventID', 'email', 'first_name', 'last_name',
						'question_data', 'image_filename', 'image_meta', 
						'image_caption', 'image_description'
						'qa_question', 'qa_answer',
						'status', 'modifiedAt', 'createdAt'
				*/
				$db = Loader::db();
				$q = "update CupCompetitionEventEntry set eventID = ?, email = ?, first_name = ?, last_name = ?, 
					question_data = ?, image_filename = ?, image_meta = ?,  image_caption = ?,
					image_description = ?, qa_question = ?,  qa_answer = ?,
					status = ?, note = ?, modifiedAt = ? WHERE id = ?";
				$v = array($this->record['eventID'], $this->record['email'], $this->record['first_name'], $this->record['last_name'],
						json_encode($this->record['question_data']), $this->record['image_filename'], $this->record['image_meta'], 
						$this->record['image_caption'], $this->record['image_description'], $this->record['qa_question'],
						json_encode($this->record['qa_answer']), $this->record['status'], $this->record['note'],
						$this->record['modifiedAt'], $this->record['id']);
				$r = $db->prepare($q);
				$res = $db->Execute($r, $v);
				if ($res) {
					$this->loadByID($this->record['id']);
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
		$q = "INSERT INTO CupCompetitionEventEntry 
							(
								eventID, email, first_name, last_name,
								question_data, image_filename, image_meta, 
								image_caption, image_description,
								qa_question, qa_answer,
								status, note, modifiedAt, createdAt
							)
						VALUES (?, ?, ?, ?, 
								?, ?, ?,
								?, ?, 
								?, ?,
								?, ?, ?, ?)";
		$v = array($this->record['eventID'], $this->record['email'], $this->record['first_name'], $this->record['last_name'], 
					json_encode($this->record['question_data']), $this->record['image_filename'], $this->record['image_meta'],
					$this->record['image_caption'], $this->record['image_description'], 
					$this->record['qa_question'], json_encode($this->record['qa_answer']),
					$this->record['status'], $this->record['note'], $this->record['modifiedAt'], $this->record['createdAt']);
		$r = $db->prepare($q);
		$res = $db->Execute($r, $v);
		
		if ($res) {
			$new_id = $db->Insert_ID();
			$this->loadByID($new_id);
			
			
			if(strcmp($this->eventObj->type, 'Photo') == 0){
				$write_to_dir = COMPETITION_FILE_DIR.DIRECTORY_SEPARATOR.$this->eventObj->id;
				if(!is_dir($write_to_dir)){
					mkdir($write_to_dir, 0777, true);
				}
				
				$_FILES['image']['tmp_name'];
				
				$dest_filename = $write_to_dir.DIRECTORY_SEPARATOR.$db->Insert_ID().".jpg";
				move_uploaded_file($_FILES['image']['tmp_name'], $dest_filename);
				chmod($dest_filename , 0777);
			}
			return true;
		}else{
			return false;
		}
	}
	
	public function getImageURL($width = false, $height = false){
		$uh = Loader::helper('concrete/urls');
		if($width && $height){
			$url = $uh->getToolsURL('entryImage', 'cup_competition');
			$url.= '?entry_id='.$this->record['id'].'&width='.$width.'&height='.$height;
			return BASE_URL.$url;
		}elseif($width){
			$url = $uh->getToolsURL('entryImage', 'cup_competition');
			$url.= '?entry_id='.$this->record['id'].'&width='.$width;
			return BASE_URL.$url;
		}elseif($height){
			$url = $uh->getToolsURL('entryImage', 'cup_competition');
			$url.= '?entry_id='.$this->record['id'].'&height='.$height;
			return BASE_URL.$url;
		}else{
			$url = COMPETITION_FILE_URL.'/'.$this->eventObj->id;
			return BASE_URL.$url.'/'.$this->record['id'].".jpg";
		}
	}
	
	public function delete(){
		if($this->record['id'] > 0){
			$db = Loader::db();
			$q = "DELETE FROM CupCompetitionEventEntry WHERE id = ?";
				
			$result = $db->Execute($q, array($this->record['id']));
			if($result){
				return true;
			}else{
				$this->errors[] = "Error occurs when deleting this event entry";
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
				/*
					'id', 
						'eventID', 'email', 'first_name', 'last_name',
						'question_data', 'image_filename', 'image_meta', 
						'image_caption', 'image_description'
						'qa_question', 'qa_answer',
						'status', 'modifiedAt', 'createdAt'
				*/
	
		$this->record['first_name'] = trim($this->record['first_name']);
		$this->record['last_name'] = trim($this->record['last_name']);
		
		$this->record['image_caption'] = trim(@$this->record['image_caption']);
		$this->record['image_description'] = trim(@$this->record['image_description']);
		
		$this->record['qa_question'] = trim(@$this->record['qa_question']);
		//$this->record['qa_answer'] = trim(@$this->record['qa_answer']);
		
		
		if(!in_array($this->record['status'], array('pending', 'approved', 'rejected'))){
			$this->record['status'] = 'pending';
		}
		
		if(strlen(trim($this->record['eventID'])) < 0){
			$this->errors[] = 'Event ID is missing';
		}else{
			$this->loadEventObject();
			if($this->eventObj === false){
				$this->errors[] = 'Event ID error';
			}
		}
		
		
		if(strlen($this->record['first_name']) < 1){
			$this->errors[] = "Step 1: First Name is required";
		}
		
		if(strlen($this->record['last_name']) < 1){
			$this->errors[] = "Step 1: Last Name is required";
		}
		
		if(strlen($this->record['email']) < 1){
			$this->errors[] = "Step 1: Email is required";
		}elseif(!filter_var($this->record['email'], FILTER_VALIDATE_EMAIL)){
			$this->errors[] = "Step 1: Email address is invalid";
		}elseif($this->eventObj){
			if($this->record['id'] < 1){
				$db = Loader::db();
				$params = array($this->record['email'], $this->record['eventID']);
				$q = "select count(id) as count from CupCompetitionEventEntry WHERE email LIKE ? AND eventID = ?";
				/*
				if($this->id > 0){
					$q .= ' AND id <> ?';
					$params[] = $this->id;
				}
				*/
				$db_result = $db->getRow($q, $params);
			
				if($db_result['count'] > $this->eventObj->max_submission){
					$this->errors = array();
					$this->errors[] = "You have reached the limit of submission.";
					return false;
				}
			}
		}
		
		
		if($this->eventObj){
				
			if(is_array($this->eventObj->form_config)){
				foreach($this->eventObj->form_config as $idx => $field){
					$fieldname = $field['field_name'];
					if(isset($field['field_required']) && $field['field_required']){
						if(!isset($this->record['question_data'][$fieldname])
							|| $this->record['question_data'][$fieldname] == false ){
							$this->errors[] = "Step 1: '{$fieldname}' is required.";
						}
					}
				}
			}
			
			if(strcmp($this->eventObj->type, 'Photo') == 0){
				//$_FILES['image']['type'] ;
				//$_FILES['image']['tmp_name'];
				if(intval($this->record['id']) < 1){
					if(!isset($_FILES["image"])){
						$this->errors[] = "Image is required.";
					}elseif($_FILES["image"]["error"] != UPLOAD_ERR_OK){
						$this->errors[] = "Image upload error! Please try again";
					}elseif(function_exists(exif_imagetype) && exif_imagetype($_FILES['image']['tmp_name']) != IMAGETYPE_JPEG) {
						$this->errors[] = "Images invalid. Only JPG image will be accepted";
					}elseif(!in_array(strtolower(pathinfo($_FILES['image']['name'],PATHINFO_EXTENSION)), array('jpg', 'jpeg'))) {
						$this->errors[] = "Images invalid. Only JPG image will be accepted";
					}elseif($_FILES['image']['size'] > 4198400){ //4100kb * 1024
						$htmlHelper = Loader::helper('cup_competition_html', 'cup_competition');
						$cat = $this->eventObj->category;
						$terms_url = $htmlHelper->url('competition/terms_and_conditions/'.$cat);
						
						$this->errors[] = "Sorry, your file is too large.<br/>
											<br/>
											Our competition <a href=\"{$terms_url}\">terms and conditions</a> require your file to be under 4MB.  If you require further assistance please contact <a href=\"mailto:educationmarketing@cambridge.edu.au\">educationmarketing@cambridge.edu.au</a>";
					}else{
						$this->record['image_filename'] = $_FILES['image']['name'];
						$this->record['image_meta'] = $_FILES['image']['type'];
					}
				}
				
				if(strlen($this->record['image_caption']) < 1){
					$this->errors[] = "Image Caption is required";
				}
				
				if(strlen($this->record['image_description']) < 1){
					$this->errors[] = "Image Description is required";
				}
			}else{	//Q&A
				//if(strlen($this->record['qa_answer']) < 1){
				//	$this->errors[] = "Q&A is required";
				//}else{
					if(is_array($this->eventObj->qa_question)){
						foreach($this->eventObj->qa_question as $idx => $field){
							$fieldname = $field['field_name'];
							if(isset($field['field_required']) && $field['field_required']){
								if(!isset($this->record['qa_answer'][$fieldname])
									|| $this->record['qa_answer'][$fieldname] == false ){
									$this->errors[] = "Step 2: '{$fieldname}' is required.";
								}
							}
						}
					}
				//}
			}
				
		}
		
		
		
		if(count($this->errors) > 0){
			return false;
		}
		
		return true;
	}
}
?>
