<?php 
defined('C5_EXECUTE') or die("Access Denied.");
Loader::model('product/set', 'core_commerce');
class DashboardCoreCommerceProductsSetsController extends Controller {

	var $helpers = array('form','validation/token','concrete/interface'); 
	
	public function view() {
		$this->set('disableThirdLevelNav', true);
		$this->set('productSets', CoreCommerceProductSet::getList());
	}

	public function view_detail($prsID, $action = false) {
		$prs = CoreCommerceProductSet::getByID($prsID);
		$this->addHeaderItem(Loader::helper('html')->css("ccm.core.commerce.search.css", "core_commerce"));
		$this->set('prs', $prs);
		switch($action) {
			case 'order_saved':
				$this->set('message', t('Product set order updated.'));
				break;
		}
		$this->view();		
	}

	public function delete($prsID) {

		$u=new User();
		//$prsID = $this->post('prsID');
      if((integer)$prsID > 0) {
         $prs = CoreCommerceProductSet::getByID($prsID);
      } else {
         throw new Exception(t('Invalid product set ID'));
      }
			
		$valt = Loader::helper('validation/token');
		if (!$valt->validate('delete_product_set')) {
			throw new Exception($valt->getErrorMessage());
		}
			
		$prs->delete(); 
		$this->redirect('/dashboard/core_commerce/products/sets', 'set_deleted');			
	}

	public function edit_set(){
		extract($this->getHelperObjects());
		//do my editing
		if (!$validation_token->validate("product-sets-edit")) {			
			$this->set('error', array($validation_token->getErrorMessage()));
			$this->view();
			return;
		}
		
		if(!$this->post('prsID')){
			$this->set('error', array(t('Invalid product set ID')));
			$this->view();			
			return;
		}

		$prs = CoreCommerceProductSet::getByID($this->post('prsID'));
		$prs->update($this->post('prsName'));

      parse_str($this->post('prsDisplayOrder'));
      if($productID > 0 ) {
         $prs->updateProductSetDisplayOrder($productID);
      }
		
		$this->set('message',t('Changes Saved'));
		$this->view();
	}
	public function add(){
		extract($this->getHelperObjects());
		
		if (!$validation_token->validate("add_set")) {
			$this->set('error', array($validation_token->getErrorMessage()));
			$this->view();
			return;
		}
		
		if (!$this->post('prsName')) {
			$this->set('error', array(t('Your product set must have a name.')));
			$this->view();
			return;		
		}

		$u = new User();
		$prs = CoreCommerceProductSet::add($this->post('prsName'), $u);

		$this->redirect('/dashboard/core_commerce/products/sets', 'set_added');		
	}
	
	public function set_added() {
		$this->set('message', t('New product set added successfully.'));
		$this->view();
	}
	public function set_deleted() {
		$this->set('message', t('Product set deleted successfully.'));
		$this->view();
	}
	

	
}
