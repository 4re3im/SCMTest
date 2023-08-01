<?php  

Loader::model('format/model', 'cup_content');

class DashboardCupContentFormatsEditController extends Controller {

	public function view($format_id = false) {
		$format = CupContentFormat::fetchByID($format_id);
		if($format === FALSE){
			$this->redirect("/dashboard/cup_content/formats");
		}
		
		$this->set('entry', $format->getAssoc());
	}
	
	public function on_start() {
		$this->set('disableThirdLevelNav', true);
	}
	
	public function submit() {
		Loader::model('collection_types');
		$val = Loader::helper('validation/form');
		$vat = Loader::helper('validation/token');
		
		$val->setData($this->post());
		$val->addRequired("name", t("Format name required."));
		$val->addRequired("shortDescription", t("Short Description required."));
		$val->addRequired("longDescription", t("Long Description required."));
		$val->test();
		
		$error = $val->getError();
	
		if (!$vat->validate('edit_format')) {
			$error->add($vat->getErrorMessage());
		}
		
		$productDetailType = null;
		if ($this->post('parentCID')) {
			$productDetailType = CollectionType::getByHandle('product_detail');
			if (!$productDetailType) {
				$error->add('Unable to create product detail page.  The product detail page type is not defined.');
			}
			$parent = Page::getByID($this->post('parentCID'));
			if (!parent) {
				$error->add('Unable to create product detail page.  The parent page is not valid.');
			}
		}
	
		if ($error->has()) {
			$this->set('error', $error);
			$this->set('entry', $this->post());
		}else{
			//print_r($this->post());
			$post = $this->post();
			
			$format = new CupContentFormat();
			
			$format->id = $post['id'];
			$format->shortDescription = $post['shortDescription'];
			$format->longDescription = $post['longDescription'];
			
			//print_r($form);
			$entry = $format->getAssoc();
			//print_r($entry);
			
			
			if($format->save()){
				$_SESSION['alerts'] = array('success' => 'Format has been saved successfully');
				$this->redirect("/dashboard/cup_content/formats");
				//$this->set('entry', $entry);
			}else{
				$this->set('entry', $entry);
				//$_SESSION['alerts'] = array('error' => $format->errors);
				$this->set('error', $format->errors);
			}
			
		}
	}
}