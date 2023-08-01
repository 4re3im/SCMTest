<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('author/list', 'cup_content');
Loader::model('author/model', 'cup_content');

class DashboardCupContentAuthorsController extends Controller {

	public function view() {
		$this->redirect('/dashboard/cup_content/authors/search');
	}

	public function edit($author_id = false) {
		$author = CupContentAuthor::fetchByID($author_id);
		if($author === FALSE){
			$_SESSION['alerts'] = array('failure' => 'Invalid Author ID');
			$this->redirect("/dashboard/cup_content/authors");
		}
		
		if(count($this->post()) > 0){	//save
			Loader::model('collection_types');
			$val = Loader::helper('validation/form');
			$vat = Loader::helper('validation/token');
		
			$val->setData($this->post());
			$val->addRequired("name", t("name required."));
			$val->test();
			
			$error = $val->getError();
		
			if (!$vat->validate('edit_author')) {
				$error->add($vat->getErrorMessage());
			}
			
			if ($error->has()) {
				//$this->set('error', $error);
				$_SESSION['alerts'] = array('error' => $error->getList());
			}else{
				$post = $this->post();
				
				$author->name = $post['name'];
				$author->biography = $post['biography'];
				
				if($author->save()){
					$_SESSION['alerts'] = array('success' => 'Author has been saved successfully');
					$this->redirect("/dashboard/cup_content/authors");
					//$this->set('entry', $entry);
				}else{
					$this->set('entry', $entry);
					$_SESSION['alerts'] = array('error' => $format->errors);
					//$this->set('error', $format->errors);
				}
			}
		}
		
		
		$this->set('entry', $author->getAssoc());
		$this->render('/dashboard/cup_content/authors/edit');
	}
	
	function delete($author_id = false) {
		$result = array('result'=>'failure', 'error'=>'unknown error');
		
		$author = CupContentAuthor::fetchByID($author_id);
		if($author->delete() === TRUE){
			$result = array('result'=>'success', 'error'=>'unknown error');
		}else{
			$result = array('result'=>'failure', 'error'=>array_shift($author->errors));
		}
		
		echo json_encode($result);
		exit();
	}
	
	public function testImport(){
		$csv_filepath = dirname(__FILE__).DIRECTORY_SEPARATOR.'authors.csv';
		
		$db = Loader::db();
		
		if (($handle = fopen($csv_filepath, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
				$name = array();
				$name[] = trim($data[0]);
				$name[] = trim($data[1]);
				
				$name = implode(" ", $name);
				
				$desc = trim($data[2]);
				
				$author = new CupContentAuthor();
				$author->name = $name;
				$author->biography = $desc;
				
				if(!$author->save()){
					echo $name."\n";
					print_r($author->errors);
				}
				
			}
			fclose($handle);
		}
		
		echo "finished";
		exit();
	}
}