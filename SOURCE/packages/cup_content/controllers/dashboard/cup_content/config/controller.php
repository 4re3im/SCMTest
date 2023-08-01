<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('block_main_carousel/model', 'cup_content');

class DashboardCupContentConfigController extends Controller {
	public function view($name = false) {
		if($name){
			if(strcmp($name, 'block_type_main_carousel') == 0){
				return $this->block_type_main_carousel();
			}elseif(strcmp($name, 'solr_server') == 0){
				return $this->solr_server();
			}elseif(strcmp($name, 'test') == 0){
				return $this->test();
			}
		}
						
		$config_links = array(
						'(Block Type) Main Page Carousel - AU' => 'block_type_main_carousel',
						'(Block Type) Main Page Carousel - NZ' => 'block_type_main_carousel_nz',
						'(Block Type) Main Page Sidebar' => 'block_type_main_sidebar',
						//'Solr Server Config' =>  'solr_server',
						'Contact Form Email Receiver' => 'contact_form_email_receiver',
						'From Email Address' => 'from_email_address',
						'Manual Sync title prices' => 'sync_title_prices',
						'Sync Config' => 'over_night_sync_config',
						'VISTA Config' => 'vista_config',
						'Order Vista Synchronous Listing' => 'order_listing',
						
					);
		
		$this->set('config_links', $config_links);
	}
	
	
	public function block_type_main_carousel(){
		Loader::model('block_main_carousel/model', 'cup_content');
		$carousel = new CupBlockMainCarousel('AU');
		if(count($this->post()) > 0){
			$carousel->saveConfig($this->post(), 'AU');
			$_SESSION['alerts'] = array('success' => 'Config saved');
			
			$carousel = new CupBlockMainCarousel();
		}
		
		$this->set('carousel_config', $carousel->getConfig());
		$this->render('/dashboard/cup_content/config/block_type_main_carousel');
	}
	
	public function block_type_main_carousel_nz(){
		Loader::model('block_main_carousel/model', 'cup_content');
		$carousel = new CupBlockMainCarousel('NZ');
		if(count($this->post()) > 0){
			$carousel->saveConfig($this->post(), 'NZ');
			$_SESSION['alerts'] = array('success' => 'Config saved');
			
			$carousel = new CupBlockMainCarousel();
		}
		
		$this->set('carousel_config', $carousel->getConfig());
		$this->render('/dashboard/cup_content/config/block_type_main_carousel_nz');
	}
	
	public function block_type_main_sidebar(){
		$config = array('title'=>'',
						'items'=>array());
		
		$pkg  = Package::getByHandle('cup_content');
		if(count($this->post()) > 0){
			
			$post = $this->post();

			$config['title'] = $post['title'];
			foreach($post['content'] as $idx => $content){
				$content = trim($content);
				if(strlen($content) > 0){
					$entry = array(
									'content'=>$content,
									'url'=>trim($post['url'][$idx]),
									'region'=>trim($post['region'][$idx])
								);
					$config['items'][] = $entry;
				}
			}
			
			
			$pkg->saveConfig('MAIN_SIDEBAR_CONFIG', serialize($config));
			$_SESSION['alerts'] = array('success' => 'Config saved');
		}

		
		$store_value = $pkg->config('MAIN_SIDEBAR_CONFIG');
		$res = unserialize($store_value);
		if(is_array($res)){
			$config = $res;
		}
		
		//print_r($config);
		//exit();
		
		$this->set('config', $config);
		$this->render('/dashboard/cup_content/config/block_type_main_sidebar');
	}

	public function solr_server(){
		/*
		$config = array(
					'adapteroptions' => array(
						'host' => '192.168.0.41',
						'port' => 8080,
						'path' => '/solr/',
					)
				);
		*/
		$config = false;
		
		$pkg  = Package::getByHandle('cup_content');
		if(count($this->post()) > 0){
			//print_r($this->post());
			$tmp_config = $this->post();
			$store_value = $pkg->saveConfig('SOLR_CONFIG', serialize($tmp_config));
			$_SESSION['alerts'] = array('success' => 'Config saved');
		}

		
		$store_value = $pkg->config('SOLR_CONFIG');
		if($store_value && unserialize($store_value) !== FALSE){
			$config = unserialize($store_value);
		}else{
			$config = array(
					'adapteroptions' => array(
						'host' => '',
						'port' => '',
						'path' => '',
					)
				);
		}
		
		
		$this->set('solr_config', $config);
		$this->render('/dashboard/cup_content/config/solr_server');
		
	}
	
	public function contact_form_email_receiver(){
		$config = false;
		
		$pkg  = Package::getByHandle('cup_content');
		if(count($this->post()) > 0){
			//print_r($this->post());
			$tmp_config = $this->post();
			$email = trim($tmp_config['email_address']);
			if(filter_var($email, FILTER_VALIDATE_EMAIL)){
				$store_value = $pkg->saveConfig('CONTACT_FORM_EMAIL_RECEIVER', $email);
				$_SESSION['alerts'] = array('success' => 'Config saved');
			}else{
				$_SESSION['alerts'] = array('error' => "Invalid Email Address: {$email}");
			}
		}

		
		$store_value = $pkg->config('CONTACT_FORM_EMAIL_RECEIVER');
		
		
		$this->set('store_value', $store_value);
		$this->render('/dashboard/cup_content/config/contact_form_email_receiver');
	}
	
	public function from_email_address(){
		$config = false;
		
		$pkg  = Package::getByHandle('cup_content');
		if(count($this->post()) > 0){
			//print_r($this->post());
			$tmp_config = $this->post();
			$email = trim($tmp_config['email_address']);
			if(filter_var($email, FILTER_VALIDATE_EMAIL)){
				$store_value = $pkg->saveConfig('FROM_EMAIL_ADDRESS', $email);
				$_SESSION['alerts'] = array('success' => 'Config saved');
			}else{
				$_SESSION['alerts'] = array('error' => "Invalid Email Address: {$email}");
			}
		}

		
		$store_value = $pkg->config('FROM_EMAIL_ADDRESS');
		
		
		$this->set('store_value', $store_value);
		$this->render('/dashboard/cup_content/config/from_email_address');
	}
	
	
	public function sync_title_prices(){
	
		if(isset($_FILES['file'])){
			Loader::model('title/batch_sync', 'cup_content');
		
			$worksheet_name = "Sheet1";
			if(isset($_POST['worksheet_name']) && strlen($_POST['worksheet_name']) > 0){
				$worksheet_name = $_POST['worksheet_name'];
			}
		
			$ext = pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION);
			
			Loader::model('title/import', 'cup_content');
			$bs = new CupContentTitleBatchSync($_FILES['file']['tmp_name']);
			
			if(strcmp($ext, 'xls') == 0){
				$bs->process('excel', $worksheet_name, 'Excel5');
			}elseif(strcmp($ext, 'xml') == 0){
				$bs->process('excel', $worksheet_name, 'Excel2003XML');
			}elseif(strcmp($ext, 'xlsx') == 0){
				$bs->process('excel', $worksheet_name, 'Excel2007');
			}else{
				$bs->process();
			}

			
			$errors = $bs->getErrors();
			if(is_array($errors) && count($errors) > 0){
				$_SESSION['alerts'] = array('error' => $errors);
			}else{
				$_SESSION['alerts'] = array('success' => 'Update Success');
			}
		}
	
		$this->render('/dashboard/cup_content/config/sync_title_prices');
	}
	
	public function over_night_sync_config(){
		$config = false;
		
		$pkg  = Package::getByHandle('cup_content');
		if(count($this->post()) > 0){
			$post = $this->post();
			$post = $post['config'];
			
			$enabled = 0;
			$page_size = 30;
			$wsdl = null;
			$location = false;
			$uri = false;
			
			if(isset($post['enabled']) && strlen(trim($post['enabled'])) > 0){
				$enabled = trim($post['enabled']);
			}
			
			if(isset($post['page_size']) && strlen(trim($post['page_size'])) > 0){
				$page_size = trim($post['page_size']);
			}
			
			if(isset($post['wsdl']) && strlen(trim($post['wsdl'])) > 0){
				$wsdl = trim($post['wsdl']);
			}
			
			if(isset($post['location']) && strlen(trim($post['location'])) > 0){
				$location = trim($post['location']);
				$params['location'] = $location;
			}
			
			if(isset($post['uri']) && strlen(trim($post['uri'])) > 0){
				$uri = trim($post['uri']);
				$params['uri'] = $uri;
			}
			
			$to_save_config = array(
									'enabled' => $enabled,
									'page_size' => $page_size,
									'wsdl' => $wsdl,
									'location' => $location,
									'uri' => $uri
								);
								
			$pkg->saveConfig('SYNC_CONFIG', serialize($to_save_config));
			$_SESSION['alerts'] = array('success' => 'Config saved');
		}
		
		$store_value = $pkg->config('SYNC_CONFIG');
		if($store_value){
			$config = unserialize($store_value);
		}
		
		$sync_note = false;
		$store_value = $pkg->config('SYNC_LOG_NOTE');
		if($store_value){
			$sync_note = unserialize($store_value);
		}
		
		$this->set('config', $config);
		$this->set('sync_note', $sync_note);
		$this->render('/dashboard/cup_content/config/over_night_sync_config');
	}
	
	public function test_sync_api(){
		$wsdl = null;
		$location = false;
		$uri = false;
		$params = array();
		
		$arg = "";
		
		if(isset($_POST['wsdl']) && strlen(trim($_POST['wsdl'])) > 0){
			$wsdl = trim($_POST['wsdl']);
		}
		
		if(isset($_POST['location']) && strlen(trim($_POST['location'])) > 0){
			$location = trim($_POST['location']);
			$params['location'] = $location;
		}
		
		if(isset($_POST['uri']) && strlen(trim($_POST['uri'])) > 0){
			$uri = trim($_POST['uri']);
			$params['uri'] = $uri;
		}
		
		if(isset($_POST['arg']) && strlen(trim($_POST['arg'])) > 0){
			$arg = trim($_POST['arg']);
		}
		
		$client = new SoapClient($wsdl, $params);
		$result = $client->getisbn($arg);
		
		print_r($result);
		exit();
		
	}
	
	public function vista_config(){
		$config = false;
		
		$pkg  = Package::getByHandle('cup_content');
		
		if(count($this->post()) > 0){
			$post = $this->post();
			$to_save_config = $post['vista'];
			$pkg->saveConfig('VISTA_CONFIG', serialize($to_save_config));
			$_SESSION['alerts'] = array('success' => 'Config saved');
		}
		
		$store_value = $pkg->config('VISTA_CONFIG');
		if($store_value){
			$config = unserialize($store_value);
		}
		
		$this->set('config', $config);
		$this->render('/dashboard/cup_content/config/vista_config');
	}
	
	public function test_vista_api(){	//ajax
		$api = false;
		$content = false;
		if(isset($_POST['api_url']) && strlen($_POST['api_url']) > 0){
			$api = $_POST['api_url'];
		}
		if(isset($_POST['content'])){
			$content = $_POST['content'];
		}
		
		if($api === false){
			echo "API URL can not be empty";
		}else{
			$request = curl_init($api);
			curl_setopt($request, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded;"));
			curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($request, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($request, CURLOPT_HEADER, 1);
			curl_setopt($request, CURLOPT_PORT, 443);
			//curl_setopt($request, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
			curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($request, CURLOPT_POSTFIELDS, 'input='.$content);
			curl_setopt($request, CURLOPT_POST, 1);  
			curl_setopt($request, CURLOPT_URL, $api);
			$data = curl_exec($request);
			$status = curl_getinfo($request, CURLINFO_HTTP_CODE); 
			curl_close ($request);
			echo $data;
		}
		
		exit();
	}
	
	public function order_listing(){
		Loader::model('cup_content_order/list', 'cup_content');
		
		$list = new CupContentOrderList();
		$list->sortBy('invoiceID', 'desc');
		
		if(isset($_GET['q_invoiceID']) && strlen(trim($_GET['q_invoiceID']))>0){
			$list->filterByInvoiceID(trim($_GET['q_invoiceID']));
		}
		
		if(isset($_GET['q_orderID']) && strlen(trim($_GET['q_orderID']))>0){
			$list->filterByOrderID(trim($_GET['q_orderID']));
		}
		
		if(isset($_GET['q_email']) && strlen(trim($_GET['q_email']))>0){
			$list->filterByEmail(trim($_GET['q_email']));
		}
		
		if(isset($_GET['q_vistaID']) && strlen(trim($_GET['q_vistaID']))>0){
			$list->filterByVistaOrderID(trim($_GET['q_vistaID']));
		}

        if(isset($_GET['q_datetime_start']) && strlen(trim($_GET['q_datetime_start']))>0){
            $time = strtotime($_GET['q_datetime_start']);
            if($time > 0){
                $list->filterByModifiedAtStart(date('Y-m-d H:i:s', $time));
            }
        }

        if(isset($_GET['q_datetime_end']) && strlen(trim($_GET['q_datetime_end']))>0){
            $time = strtotime($_GET['q_datetime_end']);
            if($time > 0){
                $list->filterByModifiedAtEnd(date('Y-m-d H:i:s', $time));
            }
        }
		
		
		$this->set('list', $list);		
		$this->render('/dashboard/cup_content/config/order_listing');
	}
	
	public function send_invoice($order_id){
        Loader::model('cup_content_order/model', 'cup_content');
        $cup_content_order = CupContentOrder::fetchByOrderID($order_id);

        if($this->isPost() && $this->post("to_email")){
            $pkg  = Package::getByHandle('cup_content');
            $from_email = $pkg->config('FROM_EMAIL_ADDRESS');

            $to_email = $this->post("to_email");
            if (!filter_var($to_email, FILTER_VALIDATE_EMAIL)) {
                echo '<span style="color:RED;">Invalid Email Address</span>';
                die();
            }

            $cupEmailHelper = Loader::helper('email', 'cup_content');
            $email_data = $cupEmailHelper->fetchOrderEmailData($cup_content_order->orderObject);
            $mh = Loader::helper('mail');
            $mh->addParameter('email_data', $email_data);
            @$mh->to(array($to_email));
            $mh->load('tax_invoice','cup_content');
            $mh->from($from_email);
            @$mh->sendMail();

            echo '<span style="color:GREEN;">Email Sent</span>';
            exit();
        }

        Loader::packageElement('send_invoice', 'cup_content', array('cup_content_order' => $cup_content_order));
        die();
    }
	
	
	
	
	
	
	
	
	/*********************************/
	
	public function fixInstall(){
		$pkg = Package::getByHandle('cup_content');
		/*
		$sinlge_pages = array(
							'/dashboard/cup_content/inspection_copy_orders',
							'/inspection_copy',
							'/page_proofs'
						);
						
		Loader::model('single_page');
		*/
		
		/*
		foreach($sinlge_pages as $path) {
			if(Page::getByPath($path)->getCollectionID() <= 0) {
				$page = SinglePage::add($path, $pkg);
			}
		}
		*/
		
		Loader::model("job");
		Job::installByPackage("cup_inventory_sync", $pkg);
		Job::installByPackage("cup_order_sync", $pkg);
		
		exit();
		
		Events::extend('core_commerce_on_get_payment_methods',
						'CupContentEvent',
						'onGetPaymentMethods',
						'packages/cup_content/models/cup_content_event.php',
						array($currentOrder, $methods));
						
		Events::extend('core_commerce_on_get_shipping_methods',
						'CupContentEvent',
						'onGetShippingMethods',
						'packages/cup_content/models/cup_content_event.php',
						array($currentOrder, $methods));
						
		Events::extend('core_commerce_on_checkout_shipping_address_submit',
						'CupContentEvent',
						'onCheckoutShippingAddressSubmit',
						'packages/cup_content/models/cup_content_event.php',
						array($address));
						
		Events::extend('core_commerce_on_checkout_start',
						'CupContentEvent',
						'onCheckoutStart',
						'packages/cup_content/models/cup_content_event.php',
						array($checkout));
						
						
						
		$db = Loader::db();		
						
		$q = "CREATE TABLE IF NOT EXISTS `CupContentTitleSupportingTitle` (
			  `titleID` int(10) DEFAULT NULL,
			  `supporting_titleID` int(10) DEFAULT NULL,
			  `isbn13` varchar(25) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$r = $db->prepare($q);
		$res = $db->Execute($r, array());
		
		
		
		$blocks = array(
						//'cup_simple_header',
						//'cup_contact_form',
						'cup_main_sidebar'
					);
					
		
		
		Loader::model('single_page');
		
		foreach($blocks as $each_block){
			BlockType::installBlockTypeFromPackage($each_block, $pkg);	
		}
		
		
		
		echo "finished";
		exit();
	}
	
	public function testJob(){
		Loader::model('cup_content_event', 'cup_content');
		$ev = new CupContentEvent();
		$ev->inventorySync();
		//$ev->titleOrderSyncDeamon();
		exit();
	}
	
	public function cleanUpCoreCommerce(){
		$scripts = <<<EOF
TRUNCATE TABLE `CoreCommerceOrderAttributeKeys` ;
TRUNCATE TABLE `CoreCommerceOrderAttributeValues` ;
TRUNCATE TABLE `CoreCommerceOrderDownloadableFiles` ;
TRUNCATE TABLE `CoreCommerceOrderInvoiceNumbers` ;
TRUNCATE TABLE `CoreCommerceOrderProducts` ;
TRUNCATE TABLE `CoreCommerceOrders` ;
TRUNCATE TABLE `CoreCommerceOrderStatusHistory` ;
TRUNCATE TABLE `CoreCommerceProductAttributeKeys` ;
TRUNCATE TABLE `CoreCommerceProductAttributeValues` ;
TRUNCATE TABLE `CoreCommerceProducts` ;
TRUNCATE TABLE `CoreCommerceProductSearchPurchaseGroups` ;
TRUNCATE TABLE `CoreCommerceProductSetProducts` ;
TRUNCATE TABLE `CoreCommerceProductSets` ;
TRUNCATE TABLE `CoreCommerceProductStats` ;
TRUNCATE TABLE `CoreCommerceProductTieredPricing` ;
EOF;
		$scripts = explode("\n", $scripts);
		
		$db = Loader::db();
		
		foreach($scripts as $tq){
			$results = $db->execute($tq, array());
		}
		echo "finished";
		exit();
	}
	
	
	
	public function t1(){
		Loader::model('order/model', 'core_commerce');
		Loader::model('order/current', 'core_commerce');
		Loader::model('order/product', 'core_commerce');
		Loader::model('attribute/categories/core_commerce_order', 'core_commerce');
		Loader::model('cup_content_order/model', 'cup_content');
		Loader::model('title/model', 'cup_content');
		
		$obj = CupContentOrder::fetchByID(8);

		
		$order = $obj->orderObject;
		
		$attributes = CoreCommerceOrderAttributeKey::getList();//$order->getAttributeValueObject('sales_tax');
		print_r($attributes);
		//print_r($order->get('sales_tax'));
		
		
		/*
		$curreny = 'AUD';
		$country = 'Australia';
		$customer_id = '116213';
		
		
		
		$hasDigitalProduct = false;
		$hasPhysicalProduct = false;
		
		$billing = $order->getAttribute('billing_address');
		$shipping = $order->getAttribute('shipping_address');
		
		$billto = array(
				'email' => $order->getOrderEmail(),
				'first_name' => $order->getAttribute('billing_first_name'),
				'last_name' => $order->getAttribute('billing_last_name'),
				'telphone' => $order->getAttribute('billing_phone'),
				'address_line_1' => $billing->getAddress1(),
				'address_line_2' => $billing->getAddress2(),
				'address_town' => $billing->getCity(),
				'address_state' => $billing->getStateProvince(),
				'address_postcode' => $billing->getPostalCode(),
				'address_country' => $billing->getCountry(),
		);
		
		$billto['address_state'] = strtr(strtolower($billto['address_state']), array(
											'Australian Antarctic Territory' => 'AAT',
											'Australian Capital Territory' => 'ACT',
											'New South Wales' => 'NSW',
											'Northern Territory' => 'NT',
											'Queensland' => 'QLD',
											'South Australia' =>'SA',
											'Tasmania' =>'TAS',
											'Victoria' =>'VIC',
											'Western Australia' => 'WA',
											'aat' => 'AAT',
											'act' => 'ACT',
											'nsw' => 'NSW',
											'nt' => 'NT',
											'qld' => 'QLD',
											'sa' =>'SA',
											'tas' =>'TAS',
											'vic' =>'VIC',
											'wa' => 'WA',
										));
		
		$billto['areacode'] = strtr($billto['address_state'], array(
											'ACT' => '9ACT',
											'NSW' => '9ANS',
											'NT' => '9ANT',
											'QLD' => '9AQL',
											'SA' =>'9ASA',
											'TAS' =>'9ATA',
											'VIC' =>'9AVI',
											'WA' => '9AWA'
										));
		if(strcmp($billto['address_country'], 'New Zealand') == 0){
			$billto['areacode'] = '9NZE';
		}
		
		
		$shipto = array(
					'first_name' => $order->getAttribute('shipping_first_name'),
					'last_name' => $order->getAttribute('shipping_last_name'),
					'telphone' => $order->getAttribute('shipping_phone'),
					'address_line_1' => $shipping->getAddress1(),
					'address_line_2' => $shipping->getAddress2(),
					'address_town' => $shipping->getCity(),
					'address_state' => $shipping->getStateProvince(),
					'address_postcode' => $shipping->getPostalCode(),
					'address_country' => $shipping->getCountry()
				);
				
		$shipto['address_state'] = strtr(strtolower($shipto['address_state']), array(
											'Australian Antarctic Territory' => 'AAT',
											'Australian Capital Territory' => 'ACT',
											'New South Wales' => 'NSW',
											'Northern Territory' => 'NT',
											'Queensland' => 'QLD',
											'South Australia' =>'SA',
											'Tasmania' =>'TAS',
											'Victoria' =>'VIC',
											'Western Australia' => 'WA',
											'aat' => 'AAT',
											'act' => 'ACT',
											'nsw' => 'NSW',
											'nt' => 'NT',
											'qld' => 'QLD',
											'sa' =>'SA',
											'tas' =>'TAS',
											'vic' =>'VIC',
											'wa' => 'WA',
										));
		
		$shipto['areacode'] = strtr($shipto['address_state'], array(
											'ACT' => '9ACT',
											'NSW' => '9ANS',
											'NT' => '9ANT',
											'QLD' => '9AQL',
											'SA' =>'9ASA',
											'TAS' =>'9ATA',
											'VIC' =>'9AVI',
											'WA' => '9AWA'
										));
		if(strcmp($shipto['address_country'], 'New Zealand') == 0){
			$shipto['areacode'] = '9NZE';
		}
		
		$depatch_code = false;
		if(strcmp($shipto['address_country'], 'New Zealand') == 0){
			$depatch_code = '04';
		}else{
			$depatch_code = '30';
		}
		
		
		$pobox_reg = array("/^\s*((p(ost)?.?\s*o(ff(ice)?)?.?\s+(b(in|ox))?)|b(in|ox))/i",
						   "/^\s*((p(ost)?.?\s*(o(ff(ice)?)?)?.?\s+(b(in|ox))?)|B(in|ox))/i",
						  // "^(p[\s|\.|,]*| ^post[\s|\.]*)(o[\s|\.|,]*| o(ff(ice)?)?[\s|\.]*)(box)"
						   );
							
		foreach($pobox_reg as $each_reg){
			if(preg_match($each_reg, $shipto['address_line_1'])) {
				$depatch_code = '20';
			}
		}
		
		$orderAttrs = array();
		$adjustments = $order->getOrderLineItems();
		foreach($adjustments as $each){
			$orderAttrs[] = array(
								'name' => $each->getLineItemName(),
								'type' => $each->getLineItemType(),
								'price' => $each->getLineItemTotal()
							);
		}
		
		
		
		$items = array();
		
		$products = CoreCommerceOrderProduct::getByOrderID($order->getOrderID());
		foreach($products as $pr){
			$pid = $pr->getProductID();
			$title = CupContentTitle::fetchByProductId($pid);
			
			if($title->auProductID == $pid){
				$curreny = 'AUD';
				$country = 'Australia';
			}else{
				$curreny = 'NZD';
				$country = 'New Zealand';
			}
			
			$item = array(	
							'isbn10' => $title->isbn10,
							'price' => $pr->getOrderProductPrice(),
							'qty' => $pr->getQuantity()
						);
						
			if($title->hasAccessCode || $title->hasDownloadableFile){
				$item['MailShot'] = '[D]';
				$hasDigitalProduct = true;
			}else{
				$hasPhysicalProduct = true;
			}
			
			$items[] = $item;
		}
		
		$doctype = 'INWN';
		if(strcmp($country, 'New Zealand') == 0){
			$customer_id = '116218';
			if($hasPhysicalProduct){
				$doctype = 'INNZ';
			}elseif($hasDigitalProduct){
				$doctype = 'INSO';
			}
		}else{
			$customer_id = '116213';
			if($hasPhysicalProduct){
				$doctype = 'INWN';
			}elseif($hasDigitalProduct){
				$doctype = 'INSO';
			}
		}
		
		
		$record = array(
					'doctype' => $doctype,
					'curreny' => $curreny,
					'country' => $country,
					'customer_id' => $customer_id,
					'billto' =>$billto,
					'shipto' =>$shipto,
					'depatch_code' => $depatch_code,
					'items' => $items,
					'orderAttrs' => $orderAttrs,
				);
				
		print_r($record);
		*/
		
		echo "finished";
		exit();
	}
	
	public function t2(){
		Loader::model('title/model', 'cup_content');
		Loader::model('product/model', 'core_commerce');
		Loader::model('cup_content_order/model', 'cup_content');
		
		$order = new CupContentOrder(6);
		print_r($order);

		$order->response = "test";
		$order->save();
		print_r($order);
		exit();
	}
	
	public function t3(){
		Loader::model('cup_content_event', 'cup_content');
		$ev = new CupContentEvent();
		$ev->inventorySync();
		
		exit();
	}
	
	public function t4(){
		$pkg = Package::getByHandle('core_commerce');
		Loader::model('shipping/type', 'core_commerce');
		//CoreCommerceShippingType::add('flat', t('Flat Shipping'), 1);
		//CoreCommerceShippingType::add('aus_flat', t('Flat Shipping AUS'), 0);
		//CoreCommerceShippingType::add('nz_flat', t('Flat Shipping NZ'), 0);
		
		exit('finished');
		$mh = Loader::helper('mail');
		//$mh->addParameter('order', $order);
		//$mh->addParameter('title_downloadable_files', $title_downloadable_files);
		$mh->to(array('test@wayneathk.com'));
		//$mh->from($from_email);
		$mh->load('sample_tax_invoice','cup_content');
		@$mh->sendMail();
		exit('sent email');
	}
	
	public function t5(){
		Loader::model('order/model', 'core_commerce');
		Loader::model('order/current', 'core_commerce');
		Loader::model('order/product', 'core_commerce');
		Loader::model('cup_content_order/model', 'cup_content');
		Loader::model('title/model', 'cup_content');
		
		$orderRecord = CupContentOrder::fetchByID(99);

		//print_r($obj);
		
		$cup_ecommerce_helper = Loader::helper('ecommerce', 'cup_content');
		//echo "Begin";
		if($cup_ecommerce_helper->vistaPostOrder($orderRecord->orderObject)){
			//print_r($cup_ecommerce_helper->response);
			
			//echo "before get order number\n";
			$vistaIDs = $cup_ecommerce_helper->getVistaResponseOrderNumber();
			//echo "after get order number\n";
			//print_r($vistaIDs);
			/*
			if(isset($cup_ecommerce_helper->response['physical'])){
				$orderRecord->response = $cup_ecommerce_helper->response['physical'];
				$orderRecord->vistaOrderID = $vistaIDs['physical'];
			}
			*/
			//echo "\nA\n";
			if(isset($cup_ecommerce_helper->response['physical_au'])){
				$orderRecord->response = $cup_ecommerce_helper->response['physical_au'];
				$orderRecord->vistaOrderID = $vistaIDs['physical_au'];
			}
			//echo "\n\nB\n";
			if(isset($cup_ecommerce_helper->response['physical_nz'])){
				$orderRecord->response_nz = $cup_ecommerce_helper->response['physical_nz'];
				$orderRecord->vistaOrderID_nz = $vistaIDs['physical_nz'];
			}
			//echo "\n\nC\n";
			if(isset($cup_ecommerce_helper->response['digital'])){
				$orderRecord->response_digital = $cup_ecommerce_helper->response['digital'];
				$orderRecord->vistaOrderID_digital = $vistaIDs['digital'];
			}
			
			//exit();
			
			//$orderRecord->vistaOrderID = $cup_ecommerce_helper->getVistaResponseOrderNumber();
			$orderRecord->status = "completed";
			$orderRecord->save();
		}
		
		
		exit();
		$cupEmailHelper = Loader::helper('email', 'cup_content');
		$email_data = $cupEmailHelper->fetchOrderEmailData($obj->orderObject);
		print_r($email_data );
		print_r($obj->orderObject->getStatus());
		exit();
		
		$mh = Loader::helper('mail');
		$mh->addParameter('email_data', $email_data);
		//$mh->addParameter('title_downloadable_files', $title_downloadable_files);
		//@$mh->to(array('jade@sparkmill.com.au','wei@sparkmill.com.au'));
		@$mh->to(array('test@wayneathk.com'));
		//$mh->from($from_email);
		$mh->load('tax_invoice','cup_content');
		@$mh->sendMail();
		exit('sent email');
		
		exit();
	}
	
	public function t6(){
		$hashKey_count = "CupContentSearch_test";
		$records = Cache::get($hashKey_count, false);
		echo "Existing: ";
		var_dump($records);
		echo "\n\n<br/><br/>\n\n";
		$value = "test test";
		Cache::set($hashKey_count, false, $value, 60);
		echo "Set: ";
		var_dump($value);
		exit();
	}
	
	public function t7(){
		Loader::model('order/model', 'core_commerce');
		Loader::model('order/current', 'core_commerce');
		Loader::model('order/product', 'core_commerce');
		Loader::model('attribute/categories/core_commerce_order', 'core_commerce');
		Loader::model('cup_content_order/model', 'cup_content');
		Loader::model('title/model', 'cup_content');
		/*
		$product = new CoreCommerceProduct();
		$title = new CupContentTitle(666);
		$product->load($title->nzProductID);
		print_r($product);
		exit();
		*/
		/*
		$tmp_title = new CupContentTitle(678);

		$tmp_title->updateProduct('NZ');
		echo "finished";
		exit();
		*/
		Loader::model('title/model', 'cup_content');
		$db = Loader::db();
		$query = "SELECT id, isbn13, isbn10 FROM `CupContentTitle`";
		$rows = $db->getAll($query);
		foreach($rows as $row){
				if(strlen($row['isbn13']) > 0){
				
					$tmp_title = CupContentTitle::fetchByISBN13($row['isbn13']);
					
					if($tmp_title->nzProductID && $tmp_title->nzProductID > 0){
						$tmp_title->updateProduct('NZ');
						echo "{$row['id']}\t{$row['isbn13']}\tdone\n";
					}
				}
			}
		exit();
	}
	
	public function t8(){
	
		Loader::model('go_access_code_email_template/model', 'cup_content');
	
		$each = array(
					//'title' => $title_object,
					'template' => CupContentGoAccessCodeEmailTemplate::fetchByTitleID(71),
					'access_code' => "XXYY-EERR-TTEE"
				);
		$replacements = array(
						'%%FIRST_NAME%%' => "Wei",
						'%%LAST_NAME%%' => "Test"
					);
					
		$access_codes = explode(";", $each['access_code']);
		for($i = 0; $i < count($access_codes); $i++){
			$code = trim($access_codes[$i]);
			$y = $i+1;
			$replacements['%%ACCESSCODE_'.$y.'%%'] = $code;
		}
		
		$each['replacements'] = $replacements;
		
		print_r($each);
		
		$pkg  = Package::getByHandle('cup_content');
		$from_email = $pkg->config('FROM_EMAIL_ADDRESS');
		
		
		$mh = Loader::helper('mail');
		$mh->addParameter('order', $order);
		$mh->addParameter('code_detail', $each);
		$mh->to("test_buyer@wayneathk.com");
		$mh->from($from_email);
		$mh->load('access_code_with_template','cup_content');
		@$mh->sendMail();
		
		exit("Finish");
	}
	
	
	public function chkTime(){
		if(isset($_GET['clear']) && $_GET['clear'] == '1'){
			file_put_contents('/tmp/tmpTimenote', "");
		}
		header("Content-type: text/plain");
		echo file_get_contents('/tmp/tmpTimenote');
		exit();
	}
	
	protected function fetchOrderEmailData($order) {
		Loader::model('title/model', 'cup_content');
		$oinfo = array(
					//'orderObj' => $order,
					'orderID' => $order->getOrderID(),
					'invoiceNumber' => $order->getInvoiceNumber(),
					'totalAmount' => $order->getOrderDisplayTotal(),
					'orderDate' => $order->getOrderDateAdded()
				);

		
		// link to order history
		$pkg = Package::getByHandle('core_commerce');
		if($pkg->config('PROFILE_MY_ORDERS_ENABLED') && $order->getOrderUserID() > 0) {
			$url = Loader::helper('navigation');
			$history = Page::getByPath('/profile/order_history');
			if($history instanceof Page) {
				$orderHistoryLink = $url->getLinkToCollection($history,true);	
				$oinfo['orderHistoryLink'] = $orderHistoryLink;
			}
		}
		
		$items = $order->getProducts();
		$i = 0;
		$oinfo['products'] = array();
		foreach ($items as $item) {
			$product = array();
			//$product['object'] = $item;
			$product['id'] = $item->getProductID();
			$product['name'] = $item->getProductName();
			$product['attributes'] = array();
			$attribs = $item->getProductConfigurableAttributes();
			$j = 0;
			foreach($attribs as $ak) {
				//$products[$i]['attributeNames'][$j++] = ;
				$product['attributes'][$ak->getAttributeKeyName()] = $item->getAttribute($ak);
			}
			
			$product['quantity'] = $item->getQuantity();
			$product['unit_price'] = $item->getProductObject()->getProductPrice();
			$product['price'] = $item->getProductCartDisplayPrice();
			
			$titleObject = CupContentTitle::fetchByProductID($product['id']);
			if($titleObject){
				$product['isbn13'] = $titleObject->isbn13;
				$product['display_name'] = $titleObject->displayName;
				$product['edition'] = $titleObject->edition;
			}
			$i++;
			
			$oinfo['products'][] = $product;
		}
		//$mh->addParameter('products', $products);


		$items = $order->getOrderLineItems();
		$oinfo['adjustments'] = array();
		foreach ($items as $item) {
			$adjustment = array();
			$adjustment['name'] = $item->getLineItemName();
			$adjustment['type'] = $item->getLineItemType();
			$adjustment['total'] = $item->getLineItemDisplayTotal();
			
			$oinfo['adjustments'][] = $adjustment;
		}
		//$mh->addParameter('adjustments', $adjustments);

		$billing = array();
		$billing['first_name'] = $order->getAttribute('billing_first_name');
		$billing['last_name'] = $order->getAttribute('billing_last_name');
		$billing['email'] = $order->getOrderEmail();
		$billing['address1'] = $order->getAttribute('billing_address')->getAddress1();
		$billing['address2'] = $order->getAttribute('billing_address')->getAddress2();
		$billing['city'] = $order->getAttribute('billing_address')->getCity();
		$billing['state'] = $order->getAttribute('billing_address')->getStateProvince();
		$billing['zip'] = $order->getAttribute('billing_address')->getPostalCode();
		$billing['country'] = $order->getAttribute('billing_address')->getCountry();
		$billing['phone'] = $order->getAttribute('billing_phone');
		$oinfo['billing'] = $billing;

		if ($order->getAttribute('shipping_address')) {
			$shipping = array();
			$shipping['first_name'] = $order->getAttribute('shipping_first_name');
			$shipping['last_name'] = $order->getAttribute('shipping_last_name');
			$shipping['email'] = $order->getOrderEmail();
			$shipping['address1'] = $order->getAttribute('shipping_address')->getAddress1();
			$shipping['address2'] = $order->getAttribute('shipping_address')->getAddress2();
			$shipping['city'] = $order->getAttribute('shipping_address')->getCity();
			$shipping['state'] = $order->getAttribute('shipping_address')->getStateProvince();
			$shipping['zip'] = $order->getAttribute('shipping_address')->getPostalCode();
			$shipping['country'] = $order->getAttribute('shipping_address')->getCountry();
			$shipping['phone'] = $order->getAttribute('shipping_phone');
			$oinfo['shipping'] = $shipping;
		}

		$bill_attr = AttributeSet::getByHandle('core_commerce_order_billing');
		if ($bill_attr > 0) {
			$akHandles = array('billing_first_name', 'billing_last_name', 'billing_address', 'billing_phone');
			$keys = $bill_attr->getAttributeKeys();
			$billing_attrs = array();
			foreach($keys as $ak) {
				if (!in_array($ak->getAttributeKeyHandle(), $akHandles)) {
					$billing_attrs[$ak->getAttributeKeyName()] = $order->getAttribute($ak);
				}
			}
			$oinfo['billing_attrs'] = $billing_attrs;
		}

		$ship_attr = AttributeSet::getByHandle('core_commerce_order_shipping');
		if ($ship_attr > 0) {
			$akHandles = array('shipping_first_name', 'shipping_last_name', 'shipping_address', 'shipping_phone');
			$keys = $ship_attr->getAttributeKeys();
			$shipping_attrs = array();
			foreach($keys as $ak) {
				if (!in_array($ak->getAttributeKeyHandle(), $akHandles)) {
					$shipping_attrs[$ak->getAttributeKeyName()] = $order->getAttribute($ak);
				}
			}
			$oinfo['shipping_attrs'] = $shipping_attrs;
		}
		
		return $oinfo;
	}

/*
    public function test_resend_invoice(){
        Loader::model('cup_content_order/list', 'cup_content');
        Loader::model('order/model', 'core_commerce');
        Loader::model('order/product', 'core_commerce');

        $orderObj = CupContentOrder::fetchByID(127);


        $cupEmailHelper = Loader::helper('email', 'cup_content');
        $email_data = $cupEmailHelper->fetchOrderEmailData($orderObj->orderObject);
        $mh = Loader::helper('mail');
        $mh->addParameter('email_data', $email_data);
        @$mh->to(array('abc_test@wayneathk.com'));
        $mh->load('tax_invoice','cup_content');
        $mh->from('randon@wayneathk.com');
        @$mh->sendMail();

        //print_r($obj);
        //$obj->orderObject->setOrderEmail('abc@abc.com');


        exit('hi');
    }
*/
	/*	native core_commerce
	protected function setEmailData($mh, $order, $data=null) {
		$mh->addParameter('orderObj', $order);
		$mh->addParameter('orderID', $order->getOrderID());
		$mh->addParameter('invoiceNumber', $order->getInvoiceNumber());
		$mh->addParameter('totalAmount', $order->getOrderDisplayTotal());
		
		// link to order history
		$pkg = Package::getByHandle('core_commerce');
		if($pkg->config('PROFILE_MY_ORDERS_ENABLED') && $order->getOrderUserID() > 0) {
			$url = Loader::helper('navigation');
			$history = Page::getByPath('/profile/order_history');
			if($history instanceof Page) {
				$orderHistoryLink = $url->getLinkToCollection($history,true);		
				$mh->addParameter('orderHistoryLink',$orderHistoryLink);
			}
		}
		
		$items = $order->getProducts();
		$i = 0;
		$products = array();
		foreach ($items as $item) {
			$products[$i]['object'] = $item;
			$products[$i]['name'] = $item->getProductName();
			$products[$i]['attributes'] = array();
			$attribs = $item->getProductConfigurableAttributes();
			$j = 0;
			foreach($attribs as $ak) {
				//$products[$i]['attributeNames'][$j++] = ;
				$products[$i]['attributes'][$ak->getAttributeKeyName()] = $item->getAttribute($ak);
			}
			
			$products[$i]['quantity'] = $item->getQuantity();
			$products[$i]['price'] = $item->getProductCartDisplayPrice();
			$i++;
		}
		$mh->addParameter('products', $products);

		$i = 0;
		$items = $order->getOrderLineItems();
		$adjustments = array();
		foreach ($items as $item) {
			$adjustments[$i]['name'] = $item->getLineItemName();
			$adjustments[$i]['type'] = $item->getLineItemType();
			$adjustments[$i]['total'] = $item->getLineItemDisplayTotal();
			$i++;
		}
		$mh->addParameter('adjustments', $adjustments);

		$billing['first_name'] = $order->getAttribute('billing_first_name');
		$billing['last_name'] = $order->getAttribute('billing_last_name');
		$billing['email'] = $order->getOrderEmail();
		$billing['address1'] = $order->getAttribute('billing_address')->getAddress1();
		$billing['address2'] = $order->getAttribute('billing_address')->getAddress2();
		$billing['city'] = $order->getAttribute('billing_address')->getCity();
		$billing['state'] = $order->getAttribute('billing_address')->getStateProvince();
		$billing['zip'] = $order->getAttribute('billing_address')->getPostalCode();
		$billing['country'] = $order->getAttribute('billing_address')->getCountry();
		$billing['phone'] = $order->getAttribute('billing_phone');
		$mh->addParameter('billing', $billing);

		if ($order->getAttribute('shipping_address')) {
			$shipping['first_name'] = $order->getAttribute('shipping_first_name');
			$shipping['last_name'] = $order->getAttribute('shipping_last_name');
			$shipping['email'] = $order->getOrderEmail();
			$shipping['address1'] = $order->getAttribute('shipping_address')->getAddress1();
			$shipping['address2'] = $order->getAttribute('shipping_address')->getAddress2();
			$shipping['city'] = $order->getAttribute('shipping_address')->getCity();
			$shipping['state'] = $order->getAttribute('shipping_address')->getStateProvince();
			$shipping['zip'] = $order->getAttribute('shipping_address')->getPostalCode();
			$shipping['country'] = $order->getAttribute('shipping_address')->getCountry();
			$shipping['phone'] = $order->getAttribute('shipping_phone');
			$mh->addParameter('shipping', $shipping);
		}

		$bill_attr = AttributeSet::getByHandle('core_commerce_order_billing');
		if ($bill_attr > 0) {
			$akHandles = array('billing_first_name', 'billing_last_name', 'billing_address', 'billing_phone');
			$keys = $bill_attr->getAttributeKeys();
			$billing_attrs = array();
			foreach($keys as $ak) {
				if (!in_array($ak->getAttributeKeyHandle(), $akHandles)) {
					$billing_attrs[$ak->getAttributeKeyName()] = $order->getAttribute($ak);
				}
			}
			$mh->addParameter('billing_attrs', $billing_attrs);
		}

		$ship_attr = AttributeSet::getByHandle('core_commerce_order_shipping');
		if ($ship_attr > 0) {
			$akHandles = array('shipping_first_name', 'shipping_last_name', 'shipping_address', 'shipping_phone');
			$keys = $ship_attr->getAttributeKeys();
			$shipping_attrs = array();
			foreach($keys as $ak) {
				if (!in_array($ak->getAttributeKeyHandle(), $akHandles)) {
					$shipping_attrs[$ak->getAttributeKeyName()] = $order->getAttribute($ak);
				}
			}
			$mh->addParameter('shipping_attrs', $shipping_attrs);
		}
	}
	*/
	
	public function checkSyncJob(){
		Loader::model('cup_content_event', 'cup_content');
		$ev = new CupContentEvent();
		
		if($ev->inventorySync()){
			return t('Finished!');
		}else{
			return t('Finished with Errors');
		}
		exit();
	}
	
	public function checkProducts($pageNumber = 0){
		
		Loader::model('title/list', 'cup_content');
		
		$titleList = new CupContentTitleList();
		//$titleList->setItemsPerPage(50);
		$titleList->setItemsPerPage(300);
		
		$titles = $titleList->getPage($pageNumber);
		
		
		
		foreach($titles as $each_title){
			$phy = 1;
			if($each_title->hasAccessCode || $each_title->hasDownloadableFile){
				$phy = 0;
			}
			
			$auProduct = $each_title->getAuProduct();
			$nzProduct = $each_title->getNzProduct();
		
			echo $each_title->isbn13;
			echo "\t";
			
			if($phy){
				echo "Phy";
			}else{
				echo "Ele";
			}
			
			echo "\t";
			
			if($auProduct->getProductID()){
				echo "Yes(".$auProduct->getProductID().")";

				if($phy && $auProduct->productRequiresShipping()){
					echo "Good";
				}else if(!$phy && !$auProduct->productRequiresShipping()){
					echo "Good";
				}else{
					echo "Error";
				}
			}else{
				echo "No";
			}
			
			echo "\t";
			
			if($nzProduct->getProductID()){
				echo "Yes(".$nzProduct->getProductID().")";
				if($phy && $nzProduct->productRequiresShipping()){
					echo "Good";
				}else if(!$phy && !$nzProduct->productRequiresShipping()){
					echo "Good";
				}else{
					echo "Error";
				}
			}else{
				echo "No";
			}
			
			echo "\n";
		}
		
		exit();
	}
	
	public function checkSyncLog(){
		$pkg  = Package::getByHandle('cup_content');
		$log = false;
		$store_value = $pkg->config('SYNC_LOG_NOTE');
		if($store_value){
			$log = unserialize($store_value);
		}
		
		print_r($log);
		exit();
	}
}
