<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('inspection_copy_order/list', 'cup_content');
Loader::model('inspection_copy_order/model', 'cup_content');

class DashboardCupContentInspectionCopyOrdersController extends Controller {
	public function view() {
		$html = Loader::helper('html');
		$this->addHeaderItem($html->css('cup_content.css', 'cup_content')); 
		
		$list = new CupContentInspectionCopyOrderList();
		$list->sortBy('id', 'desc');
		
		$this->set('list', $list);
	}
	
	public function order_detail($order_id){
		$obj = CupContentInspectionCopyOrder::fetchByID($order_id);
		if($obj === FALSE){
			$_SESSION['alerts'] = array('failure' => 'Invalid Order ID');
			$this->redirect("/dashboard/cup_content/inspection_copy_orders");
		}
		
		$this->set('orderObj', $obj);
		$this->render('/dashboard/cup_content/inspection_copy_orders/order_detail');
	}
	
	public function delete($order_id){
		$html = Loader::helper('html');
		$this->addHeaderItem($html->css('cup_content.css', 'cup_content')); 
	
		$result = array('result'=>'failure', 'error'=>'unknown error');
		
		$obj = CupContentInspectionCopyOrder::fetchByID($order_id);
		if($obj->delete() === TRUE){
			$result = array('result'=>'success', 'error'=>'unknown error');
		}else{
			$result = array('result'=>'failure', 'error'=>array_shift($series->errors));
		}
		
		echo json_encode($result);
		exit();
	}
}