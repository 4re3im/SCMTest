<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::block('library_file');

Loader::model('subject/list', 'cup_content');

class CupMainCarouselBlockController extends BlockController {
	public function getBlockTypeDescription() {
		return t("CUP Caroursel");
	}

	public function getBlockTypeName() {
		return t("CUP Caroursel");
	}

	protected $btTable = '';
	protected $btInterfaceWidth = "764";
	protected $btInterfaceHeight = "400";
	
	public function view(){
        $exclusions = CupContentSubjectList::getExclusionList();

		$subjects = array('primary'=>array(), 'secondary'=>array());
		
		$subjectList = new CupContentSubjectList();
		$subjectList->setItemsPerPage(25);
		$subjectList->filterByDepartment('primary');
		
		if(strcmp($_SESSION['DEFAULT_LOCALE'], 'en_NZ') == 0){
			$subjectList->filterByRegion('NZ');
			setcookie('DEFAULT_LOCALE', 'en_NZ', time()+60*60*24*365);
			$_SESSION['DEFAULT_LOCALE'] = 'en_NZ';
		}else{
			$subjectList->filterByRegion('AU');
			setcookie('DEFAULT_LOCALE', 'en_AU', time()+60*60*24*365);
			$_SESSION['DEFAULT_LOCALE'] = 'en_AU';
		}
		
		//$subjects['primary'] = $subjectList->getPage();
		$subjectList->sortBy('name', 'ASC');
        $page_result = $subjectList->getPage();
        $subjects['primary'] = array();
        foreach($page_result as $idx => $each){
            if(!in_array($each->name, $exclusions["primary"])){
                array_push($subjects['primary'], $each);
            }
        }
		
		$subjectList = new CupContentSubjectList();
		$subjectList->setItemsPerPage(25);
		$subjectList->filterByDepartment('secondary');
		
		if(strcmp($_SESSION['DEFAULT_LOCALE'], 'en_NZ') == 0){
			$subjectList->filterByRegion('NZ');
		}else{
			$subjectList->filterByRegion('AU');
		}
		$subjectList->sortBy('name', 'ASC');
        $page_result = $subjectList->getPage();

		$subjects["debug"] = $page_result;
		$subjects['secondary'] = array();
        foreach($page_result as $idx => $each){
            if(!in_array($each->name, $exclusions["secondary"])){
                array_push($subjects['secondary'], $each);
            }
        }
		
		
		$this->set('subjects', $subjects);
	}
}