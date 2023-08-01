<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::block('library_file');

class CupMainSidebarBlockController extends BlockController {

	public function on_start(){
		$header = <<<EOF
<!--[if IE 8]>
<style>
.cup-main-sidebar{

}

.cup-main-sidebar .guide-title{
	font-family: 'PT Sans',sans-serif;
	font-size: 20px;
	letter-spacing: -1px;
}
</style>
<![endif]-->	
EOF;
		$this->addHeaderItem($header);
	}
	
	public function getBlockTypeDescription() {
		return t("Sidebar");
	}

	public function getBlockTypeName() {
		return t("Cup Main Sidebar");
	}
	
	public function view(){
		if(strcmp($_SESSION['DEFAULT_LOCALE'], 'en_NZ') == 0){
			setcookie('DEFAULT_LOCALE', 'en_NZ', time()+60*60*24*365);
			$_SESSION['DEFAULT_LOCALE'] = 'en_NZ';
		}else{
			setcookie('DEFAULT_LOCALE', 'en_AU', time()+60*60*24*365);
			$_SESSION['DEFAULT_LOCALE'] = 'en_AU';
		}
	}

	protected $btTable = '';
	protected $btInterfaceWidth = "260";
	protected $btInterfaceHeight = "400";
}