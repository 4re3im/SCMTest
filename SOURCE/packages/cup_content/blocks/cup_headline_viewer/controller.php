<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::block('library_file');
Loader::model('page_list');

class CupHeadlineViewerBlockController extends BlockController {
	public function getBlockTypeDescription() {
		return t("CUP Headline Viewer");
	}

	public function getBlockTypeName() {
		return t("CUP Headline Viewer");
	}

	protected $btTable = 'btCupHeadlineViwer';
	protected $btInterfaceWidth = "760";
	protected $btInterfaceHeight = "200";
	
	protected $cparentID = false;
	protected $slide_interval = false;
	
	
	
	public function view(){
		$cParentID = $this->getCparentID();
		$slide_interval = $this->getSlideInterval();
		
		$pl = new PageList();
		$pl->setNameSpace('b' . $this->bID);
		$pl->sortByPublicDateDescending();
		$pl->filterByParentID($cParentID);
		
		$pages = $pl->getPage();
		$this->set('pages', $pages);
		$this->set('slide_interval', $slide_interval);
	}
	
	public function add(){
		
	}
	
	public function edit(){
		$this->set('cParentID', $this->getCparentID());
		$this->set('slide_interval', $this->getSlideInterval());
	}
	
	public function delete(){
	
	}
	
	protected function retrieveConfig(){
		$db = Loader::db();
		$q = "select * from btCupHeadlineViwer where bID = ?";
		$result = $db->getRow($q, array($this->bID));
		if($result){
			$this->cparentID = $result['cParentID'];
			$this->slide_interval = $result['slide_interval'];
		}
	}
	
	public function getCparentID(){
		if($this->cparentID == false){
			$this->retrieveConfig();
		}
		
		return $this->cparentID;
	}
	
	public function getSlideInterval(){
		if($this->slide_interval == false){
			$this->retrieveConfig();
		}
		
		return $this->slide_interval;
	}
	
	/*
	public function action_submit(){
			$u = new User();
			
			Loader::model('user_group_request', 'cup_toolbox');
			$request_object = new UserGroupRequest();
			$request_object->setSubmitData($this->post());
			$request_object->FormID = $this->retrieveFormID();
			$request_object->uID = $u->getUserID();
			$request_object->status = 'pending';
			if($request_object->prepareFormData()){
				if($request_object->save()){
					Loader::model('user_group_request_form', 'cup_toolbox');
					$form_object = new UserGroupRequestForm($this->retrieveFormID());
			
					$this->set("success_response", $form_object->response);
				}else{
					$this->set('form_errors', $request_object->errors);
				}
			}else{
				print_r($request_object->system_errors);
				exit();
			}
			//exit();
			
		}
	*/
}