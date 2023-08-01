<?php  defined('C5_EXECUTE') or die(_("Access Denied."));

class MultilingualHelper { 

	protected $pkgHandle = 'multilingual';
	protected $pkg;
	
	public function getHandle() {
		return $this->pkgHandle;
	}
	
	public function getPkg() {
		if(!$this->pkg instanceof Package) {
			$this->pkg = Package::getByHandle($this->pkgHandle);
		}
		return $this->pkg;
	}
	
	public function isEnabled() {
		if(defined('CORE_COMMERCE_DISABLE_MULTILINGUAL') && CORE_COMMERCE_DISABLE_MULTILINGUAL) { 
			return false;
		}
		$pkg = $this->getPkg();
		if(isset($pkg) && $pkg instanceof Package && $pkg->getPackageHandle() == $this->pkgHandle) {
			if(version_compare($pkg->getPackageVersion(), '1.1','>=')) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * returns a keyed array array('en'=>'English') suitable for use in the FormHelper::select method
	 * @return array()
	 */
	public function getSectionSelectArray($default_text = NULL) {
		Loader::model('section', $this->getHandle()); 
		$mslist = MultilingualSection::getList();
		$default_text = (isset($default_text)?$default_text:t('** Choose a Language'));
		
		$sections = array();
		if(is_array($mslist) && count($mslist)) {
			$sections = array(''=>$default_text);
			foreach($mslist as $ms) {
				$sections[$ms->getLocale()] = $ms->getLanguageText()." (".$ms->getLocale().")";						
			}
		}
		return $sections;
	}
	
}