<?php  defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model('order/list', 'core_commerce');
class DashboardCoreCommerceOrdersSearchController extends Controller {
	
	public $helpers = array('form','html', 'concrete/urls', 'validation/token');
	
	public function view() {
		$orderList = $this->getRequestedSearchResults();
		$orders = $orderList->getPage();
		$this->set('orderList', $orderList);		
		$this->set('orders', $orders);		
		$this->set('pagination', $orderList->getPagination());
	}
	
	public function update() {
	
	}
	
	public function on_start() {
		$helpers = $this->getHelperObjects();
		$this->addHeaderItem($helpers['html']->javascript('ccm.core.commerce.search.js', 'core_commerce')); 
		$this->set('disableThirdLevelNav', true);
	}
	
	
	public function edit($orderID = false) {

	}
	
	public function delete($orderID) {
		
		$valt = Loader::helper('validation/token');
		
		Loader::model('order/model', 'core_commerce');
		$order = CoreCommerceOrder::getByID($orderID);
		$number = $order->getInvoiceNumber();
		if($valt->validate('delete') && $order->delete($orderID)) {
			$this->set('message',t('Order # %s removed sucessfully',$number));
		} else {
			$this->set('message',t('An error occured while attempting to remove Order # %s ',$number));
		}
		$this->view();
	}
	
	
	public function detail($id, $update = false) {
		Loader::model('order/model', 'core_commerce');
		$order = CoreCommerceOrder::getByID($id);
		$this->set('order', $order);		
		
		if ($update != false) {
			switch($update) {
				case 'status_updated':
					$this->set('message', t('Order status updated.'));
					break;
			}
		}
    }

    public function update_order_status() {
		Loader::model('order/model', 'core_commerce');
		$order = CoreCommerceOrder::getByID($this->post('orderID'));
		if (is_object($order)) {
			$order->setOrderStatus($this->post('oStatus'));
		}
		$this->redirect('/dashboard/core_commerce/orders/search', 'detail', $this->post('orderID'), 'status_updated');
    }

	public function getRequestedSearchResults() {
		$orderList = new CoreCommerceOrderList();
		$orderList->sortBy('oDateAdded', 'desc');
		
		if(is_numeric($_GET['keywords'])) {
		
		}
		
		if ($_GET['keywords'] != '') {
			$orderList->filterByKeywords($_GET['keywords']);
		}	
		
		if ($_REQUEST['numResults']) {
			$orderList->setItemsPerPage($_REQUEST['numResults']);
		}
		
		if ($_REQUEST['oStatus']) {
			$orderList->filterByOrderStatus($_REQUEST['oStatus']);
		}
		
		if (is_array($_REQUEST['selectedSearchField'])) {
			foreach($_REQUEST['selectedSearchField'] as $i => $item) {
				// due to the way the form is setup, index will always be one more than the arrays
				if ($item != '') {
					switch($item) {
						case 'product_set':
							Loader::model('product/set', 'core_commerce');
							if (is_array($_REQUEST['prsID'])) {
								foreach($_REQUEST['prsID'] as $prsID) {
									$prs = CoreCommerceProductSet::getByID($prsID);
									$orderList->filterBySet($prs);
								}
							}
							break;
						case "date_added":
							$dateFrom = $_REQUEST['date_from'];
							$dateTo = $_REQUEST['date_to'];
							if ($dateFrom != '') {
								$dateFrom = date('Y-m-d', strtotime($dateFrom));
								$orderList->filterByDateAdded($dateFrom, '>=');
								$dateFrom .= ' 00:00:00';
							}
							if ($dateTo != '') {
								$dateTo = date('Y-m-d', strtotime($dateTo));
								$dateTo .= ' 23:59:59';
								
								$orderList->filterByDateAdded($dateTo, '<=');
							}
							break;

						default:
							$akID = $item;
							$fak = CoreCommerceOrderAttributeKey::getByID($akID);
							$type = $fak->getAttributeType();
							$cnt = $type->getController();
							$cnt->setAttributeKey($fak);
							$cnt->searchForm($orderList);
							break;
					}
				}
			}
		}
		return $orderList;
	}
	public function export() {
	//	echo var_dump($this->getRequestedSearchResults()->get(0)); // models/order/model.php delete() function.  Look in there to see how the attributes can be gotten like the userlist.  
		$orderList = $this->getRequestedSearchResults();
		$orderList->setItemsPerPage(-1);
		$orderList = $orderList->getPage();	
		if(is_array($orderList) && count($orderList)) {
			header("Content-Type: application/vnd.ms-excel");
			header("Cache-control: private");
			header("Pragma: public");
			$date = date('Ymd');
			header("Content-Disposition: inline; filename=orders_export_{$date}.xls"); 
			header("Content-Title: Order Export - Run on {$date}");
			echo("<table>");
			Loader::packageElement('orders/export_row_headers','core_commerce',array('o'=>$orderList[0]));
			foreach($orderList as $o){
				$products = $o->getProducts();
				foreach($products as $op) { 
					// make it easy for folks to change this around.. 
					Loader::packageElement('orders/export_row','core_commerce',array('p' => $op, 'o'=>$o));
				}
			}
			echo("</table>");
		}
		exit;
	}
	
	public function getBaseUrl() {
        $pkg = Package::getByHandle('core_commerce');
        if ($pkg->config('SECURITY_USE_SSL') == 'true') {
            return Config::get('BASE_URL_SSL');
        } else {
            return BASE_URL;
        }
	}

}
