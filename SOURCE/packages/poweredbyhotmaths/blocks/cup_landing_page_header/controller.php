<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::block('library_file');

class CupLandingPageHeaderBlockController extends BlockController {
	public function getBlockTypeDescription() {
		return t("CUP Landing Page Header");
	}

	public function getBlockTypeName() {
		return t("CUP Landing Page Header");
	}

	protected $btTable = 'btCupLandingPageHeader';
	protected $btInterfaceWidth = "500";
	protected $btInterfaceHeight = "400";
	
	
	public function __construct($obj = null) {
		parent::__construct($obj);
	}
	
	public function view(){
		//$this->set('title', $this->getTitle());
	}
	
	/*
	public function add(){
	
	}
	*/
	
	/*
	public function edit(){
		$this->set('title', $this->getTitle());
	}
	*/
	
}
