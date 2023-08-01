<?php  

Loader::model('format/model', 'cup_content');

class DashboardCupContentFormatsAddController extends Controller {

	public function view() {

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
	
		if (!$vat->validate('create_format')) {
			$error->add($vat->getErrorMessage());
		}
		

	
		if ($error->has()) {
			$this->set('error', $error);
			$this->set('entry', $this->post());
		}else{
			//print_r($this->post());
			$post = $this->post();
			
			Loader::helper('tools', 'cup_content');
			
			$format = new CupContentFormat();
			
			$format->id = $post['id'];
			$format->name = $post['name'];
			$format->prettyUrl = CupContentToolsHelper::string2prettyURL($format->name);
			$format->shortDescription = $post['shortDescription'];
			$format->longDescription = $post['longDescription'];
			$format->isDigital = $post['isDigital'];
			
			//print_r($form);
			$entry = $format->getAssoc();
			//print_r($entry);
			
			
			if($format->save()){
				$_SESSION['alerts'] = array('success' => 'New Format has been added successfully');
				
				if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
					$format->saveImage($_FILES['image']['tmp_name']);
				}
				
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