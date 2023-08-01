<?php  
Loader::model('author/list', 'cup_content');
Loader::model('author/model', 'cup_content');

class DashboardCupContentAuthorsSearchController extends Controller {

	public function view() {
		$html = Loader::helper('html');
		$this->addHeaderItem($html->javascript('jquery.wspecial.js', 'cup_content')); 
		$this->addHeaderItem($html->css('cup_content.css', 'cup_content')); 
		
		//$this->redirect('/dashboard/cup_content/formats/search');
		$authorList = new CupContentAuthorList();
		if ($_REQUEST['numResults']) {
			$authorList->setItemsPerPage($_REQUEST['numResults']);
		}
		
		if ($_GET['keywords'] != '') {
			$authorList->filterByKeywords($_GET['keywords']);
		}	
		
		//$authorList->sortBy('name', 'asc');
		//$authorList->sortBy('prettyUrl', 'asc');
		
		if(isset($_GET['ajax'])){
			echo Loader::packageElement('author/dashboard_search', 'cup_content', 
								array('authors' => $authorList->getPage(), 
									'authorList' => $authorList, 
									'pagination' => $authorList->getPagination())
						);
			exit();
		}
		
		$this->set('authorList', $authorList);		
		$this->set('authors', $authorList->getPage());		
		$this->set('pagination', $authorList->getPagination());
	}
	

	
}