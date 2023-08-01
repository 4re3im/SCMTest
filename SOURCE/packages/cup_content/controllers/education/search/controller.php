<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model('subject/model', 'cup_content');
Loader::model('cup_content_search', 'cup_content');
Loader::helper('tools', 'cup_content');

class EducationSearchController extends Controller {

	public function on_start(){
            $v = View::getInstance();
                $v->setTheme(PageTheme::getByHandle("education_theme"));
		CupContentToolsHelper::initialLocate();
		
		$this->error = Loader::helper('validation/error');
		$this->addHeaderItem(Loader::helper('html')->css('ccm.profile.css'));
		
		$html = Loader::helper('html');
		$this->addHeaderItem($html->css('cup_content.css', 'cup_content')); 
		
		
		$this->addHeaderItem(Loader::helper('html')->javascript('jquery.js'));
		$this->addHeaderItem(Loader::helper('html')->css('ccm.core.commerce.cart.css', 'core_commerce'));
		$this->addHeaderItem(Loader::helper('html')->javascript('ccm.core.commerce.cart.js', 'core_commerce'));
		$this->addFooterItem(Loader::helper('html')->javascript('jquery.form.js'));
		$this->addHeaderItem(Loader::helper('html')->javascript('jquery.ui.js'));
		$this->addHeaderItem(Loader::helper('html')->css('jquery.ui.css'));
		
		$pkg = Package::getByHandle('core_commerce');
		if($pkg->config('WISHLISTS_ENABLED')) {
			$this->addHeaderItem(Loader::helper('html')->javascript('ccm.core.commerce.wishlist.js', 'core_commerce'));
			$this->addHeaderItem(Loader::helper('html')->css('ccm.core.commerce.wishlist.css', 'core_commerce'));
		}
		
		if(!isset($_SESSION['DEFAULT_LOCALE'])){
			$_SESSION['DEFAULT_LOCALE'] = 'en_AU';
		}elseif(!in_array($_SESSION['DEFAULT_LOCALE'], array('en_AU', 'en_NZ'))){
			$_SESSION['DEFAULT_LOCALE'] = 'en_AU';
		}
	}

	public function view() { 
	
		$search = new CupContentSearch();
		
		$criteria = array();
		$reload_criteria = false;
		
		if(isset($_GET['q_subject'])){
			
			if(strlen(trim($_GET['q_subject'])) > 0){
				$search->filterBySubject($_GET['q_subject']);
				$criteria['q_subject'] = $_GET['q_subject'];
			}else{
				$reload_criteria = true;
			}
		}
		
		if(isset($_GET['q_isbn'])){
			
			if(strlen(trim($_GET['q_isbn'])) > 0){
				$search->filterByISBN($_GET['q_isbn']);
				$criteria['q_isbn'] = $_GET['q_isbn'];
			}else{
				$reload_criteria = true;
			}
		}
		
		if(isset($_GET['q_author'])){
			if(strlen(trim($_GET['q_author'])) > 0){
				$search->filterByAuthor($_GET['q_author']);
				$criteria['q_author'] = $_GET['q_author'];
			}else{
				$reload_criteria = true;
			}
		}
		
		if(isset($_GET['q_component'])){
			if(strlen(trim($_GET['q_component'])) > 0){
				$search->filterByFormat($_GET['q_component']);
				$criteria['q_component'] = $_GET['q_component'];
			}else{
				$reload_criteria = true;
			}
		}
		
		if(strcmp($_SESSION['DEFAULT_LOCALE'], 'en_AU') == 0){
			$search->filterByRegion('Australia');
			if(isset($_GET['q_region'])){
				if(strlen(trim($_GET['q_region'])) > 0){
					$search->filterByRegion($_GET['q_region']);
					$criteria['q_region'] = $_GET['q_region'];
				}else{
					$reload_criteria = true;
				}
			}
		}else{
			$search->filterByRegion('New Zealand');
		}
		
		if(isset($_GET['q_year_level'])){
			if(strlen(trim($_GET['q_year_level'])) > 0){
				$search->filterByYear($_GET['q_year_level']);
				$criteria['q_year_level'] = $_GET['q_year_level'];
			}else{
				$reload_criteria = true;
			}
		}
		
		if(isset($_GET['q_department'])){
			if(strlen(trim($_GET['q_department'])) > 0){
				$search->filterByDepartment($_GET['q_department']);
				$criteria['q_department'] = $_GET['q_department'];
			}else{
				$reload_criteria = true;
			}
		}
		
		if(isset($_GET['q_keywords'])){
			if(strlen(trim($_GET['q_keywords'])) > 0){
				//print_r($_GET);
				//exit();				
				if(preg_match('/^\d+$/', trim($_GET['q_keywords']))){
					$criteria['q_isbn'] = trim($_GET['q_keywords']);
					$reload_criteria = true;
				}else{
					$search->filterByKeywords($_GET['q_keywords']);
					$criteria['q_keywords'] = $_GET['q_keywords'];
				}
			}else{
				$reload_criteria = true;
			}
		}
		
		$page_size = 10;
		
		if(isset($_GET['cc_sort'])){
			if(strlen(trim($_GET['cc_sort'])) > 0){
				$search->setSortBy('name', $_GET['cc_sort']);
				$criteria['cc_sort'] = $_GET['cc_sort'];
			}else{
				$reload_criteria = true;
			}
		}
		
		if(isset($_GET['cc_page'])){
			if(strlen(trim($_GET['cc_page'])) > 0){
				$search->setPageNumber($_GET['cc_page']);
				$criteria['cc_page'] = $_GET['cc_page'];
			}else{
				$reload_criteria = true;
			}
		}
		
		if(isset($_GET['cc_size'])){
			if(strlen(trim($_GET['cc_size'])) > 0){
				$page_size = $_GET['cc_size'];
				$criteria['cc_size'] = $_GET['cc_size'];
			}else{
				$reload_criteria = true;
			}
		}
		
		if(isset($_GET['cc_sort'])){
			if(strlen(trim($_GET['cc_sort'])) > 0){
				$page_sort = $_GET['cc_sort'];
				$criteria['cc_sort'] = $_GET['cc_sort'];
			}else{
				$reload_criteria = true;
			}
		}
		
		if($reload_criteria){
			$this->redirect('/education/search?'.http_build_query($criteria));
			//print_r($criteria);
			//exit();
		}
		
		$search->setPageSize($page_size);
		
		$this->set('criteria', $criteria);
		$this->set('page_size', $page_size);
		$this->set('search', $search);
		
		//$this->render('/subjects/subject_view');
	}
	
}