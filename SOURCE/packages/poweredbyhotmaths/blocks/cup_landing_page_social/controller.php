<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::block('library_file');

class CupLandingPageSocialBlockController extends BlockController {
	public function getBlockTypeDescription() {
		return t("CUP Landing Page Social");
	}

	public function getBlockTypeName() {
		return t("CUP Landing Page Social");
	}

	protected $btTable = 'btCupLandingPageSocial';
	protected $btInterfaceWidth = "500";
	protected $btInterfaceHeight = "400";
	
	
	public function __construct($obj = null) {
		parent::__construct($obj);
	}
	
	public function add(){
		//parent::add();
		$this->set('config', @json_decode($this->config, true));
	}
	
	public function edit(){
		//parent::edit();
		$this->set('config', @json_decode($this->config, true));
	}
	
	public function view(){
		$this->set('config', @json_decode($this->config, true));
	}
	
	public function save($args){
		//print_r($args);
		$args['config'] = json_encode($args['config']);
		parent::save($args);
	}
	
}
