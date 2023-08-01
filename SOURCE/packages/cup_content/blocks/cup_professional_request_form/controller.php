<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::block('library_file');

class CupProfessionalRequestFormBlockController extends BlockController {
	public function getBlockTypeDescription() {
		return t("Cup Professional Request Form");
	}

	public function getBlockTypeName() {
		return t("Cup Professional Request Form");
	}

	protected $btTable = 'btCupProfessionalRequestForm';
	protected $btInterfaceWidth = "500";
	protected $btInterfaceHeight = "400";
	
	protected $title = false;
	
	public function view(){
		$this->set('title', $this->getTitle());
	}
	
	public function submit(){
		echo "hello world";
		exit();
	}
	
	public function add(){
	
	}
	
	public function edit(){
		$this->set('title', $this->getTitle());
	}
	
	public function getTitle(){
		if($this->title === false){
			$this->retrieveConfig();
		}
		
		return $this->title;
	}
	
	protected function retrieveConfig(){
		$db = Loader::db();
		$q = "select * from btCupProfessionalRequestForm where bID = ?";
		$result = $db->getRow($q, array($this->bID));
		if($result){
			$this->title = $result['title'];
		}
	}
	
	
}