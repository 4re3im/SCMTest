<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::block('library_file');

class CupSimpleHeaderBlockController extends BlockController {
	public function getBlockTypeDescription() {
		return t("Cup Simple Header");
	}

	public function getBlockTypeName() {
		return t("Cup Simple Header");
	}

	protected $btTable = 'btCupSimpleHeader';
	protected $btInterfaceWidth = "980";
	protected $btInterfaceHeight = "400";
	
	protected $title = false;
	
	public function view(){
		$this->set('title', $this->getTitle());
	}
	
	public function view_on_page($ti){
		$html = Loader::helper('html');      
		$urls = Loader::helper('concrete/urls');
		$bv = new BlockView();
		//$bv->setBlockObject($this->getBlockObject());
		
		//$this->addHeaderItem($html->css($urls->getBlockTypeAssetsURL($this->block, 'view.css')));
		
		$bt = BlockType::getByHandle('cup_simple_header');
		
		$this->set('title', $ti);
		//$this->render('view');
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
	
	public function setTitle($ti){
		$this->title = $ti;
	}
	
	protected function retrieveConfig(){
		$db = Loader::db();
		$q = "select * from btCupSimpleHeader where bID = ?";
		$result = $db->getRow($q, array($this->bID));
		if($result){
			$this->title = $result['title'];
		}
	}
	
	
}