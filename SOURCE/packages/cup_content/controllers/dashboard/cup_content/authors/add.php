<?php  

Loader::model('author/model', 'cup_content');

class DashboardCupContentAuthorsAddController extends Controller {

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
		$val->addRequired("name", t("Name required."));
		$val->test();
		
		$error = $val->getError();
	
		if (!$vat->validate('create_author')) {
			$error->add($vat->getErrorMessage());
		}
		
	
		if ($error->has()) {
			//$this->set('error', $error);
			//print_r($error);
			//exit();
			$_SESSION['alerts'] = array('error' => $error->getList());
			$this->set('entry', $this->post());
		}else{
			//print_r($this->post());
			$post = $this->post();
			Loader::helper('tools', 'cup_content');
			
			$author = new CupContentAuthor();
			
			$author->id = $post['id'];
			$author->name = $post['name'];
			$author->prettyUrl = CupContentToolsHelper::string2prettyURL($author->name);
			$author->biography = $post['biography'];
			
			//print_r($form);
			$entry = $author->getAssoc();
			//print_r($entry);
			
			
			if($author->save()){
				$_SESSION['alerts'] = array('success' => 'New Format has been added successfully');
				$this->redirect("/dashboard/cup_content/authors");
				//$this->set('entry', $entry);
			}else{
				$this->set('entry', $entry);
				$_SESSION['alerts'] = array('error' => $author->errors);
				
				//$this->set('error', $author->errors);
			}
			
		}
	}
}