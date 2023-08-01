<?php defined('C5_EXECUTE') or die(_("Access Denied."));
	
class GoSupportBlockController extends BlockController {
	
	protected $btTable = "supports";
	protected $btInterfaceWidth = "550";
	protected $btInterfaceHeight = "400";
        
	protected $pkgHandle = "go_contents";
        
        public function on_page_view($obj = null) {
            parent::__construct($obj);

         }

	public function getBlockTypeName() {
		return t('Support');
	}

	public function getBlockTypeDescription() {
		return t('Support block');
	}

}
