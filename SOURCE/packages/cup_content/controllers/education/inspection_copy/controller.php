<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
class EducationInspectionCopyController extends Controller {

	public function on_start(){
            $v = View::getInstance();
                $v->setTheme(PageTheme::getByHandle("education_theme"));
		$html = Loader::helper('html');
		$this->addHeaderItem($html->javascript('jquery.wspecial.js', 'cup_content')); 
		$this->addHeaderItem($html->css('cup_content.css', 'cup_content')); 
		$this->addHeaderItem($html->css('inspection_copy.css', 'cup_content')); 
		
		if(!isset($_SESSION['DEFAULT_LOCALE'])){
			$_SESSION['DEFAULT_LOCALE'] = 'en_AU';
		}elseif(!in_array($_SESSION['DEFAULT_LOCALE'], array('en_AU', 'en_NZ'))){
			$_SESSION['DEFAULT_LOCALE'] = 'en_AU';
		}
	}

	public function view($subject = false) { 
		Loader::model('title/list', 'cup_content');
		$list = new CupContentTitleList();
		$list->filterByIsEnabled();
		$list->filterByHasInspectionCopy();
		
		$criteria = array();
		
		if($subject){
			Loader::model('subject/model', 'cup_content');
			$subjectObj = CupContentSubject::fetchByPrettyUrl($subject);
			$list->filterBySubject($subjectObj->name);
			$criteria['subject_prettyUrl'] = $subject;
		}
		
		$selected_region = 'New Zealand';
		if(strcmp($_SESSION['DEFAULT_LOCALE'], 'en_AU') == 0){
			$selected_region = 'All Australia';
			if(isset($_SESSION['inspection_copy']['filter_region'])){
				$selected_region = $_SESSION['inspection_copy']['filter_region'];
			}
			$list->filterByRegion($selected_region);
		}else{
			$list->filterByRegion($selected_region);
		}
		
		
		if(isset($_GET['cc_sort'])){
			$list->sortBy('name', $_GET['cc_sort']);
		}
		
		if(isset($_GET['q_department'])){
			$list->filterByDepartment($_GET['q_department']);
		}
		
		
		$page_size = 10;
		if(isset($_GET['cc_size'])){
			$page_size = $_GET['cc_size'];
		}
		$list->setItemsPerPage($page_size);
		
		
		if(isset($_GET['ajax']) && $_GET['ajax'] == 'yes'){
			Loader::element('frontend/inspection_result', array('list'=>$list, 'criteria'=>$criteria), 'cup_content');
			exit();
		}
		
		$this->set('criteria', $criteria);
		$this->set('selected_region', $selected_region);
		$this->set('list', $list);
	}
	
	public function place_order($title_id){
		if(!isset($_SESSION['inspection_copy']['order_list'])){
			$_SESSION['inspection_copy']['order_list'] = array();
		}

		if(!isset($_SESSION['inspection_copy']['order_list_product_name'])){
			$_SESSION['inspection_copy']['order_list_product_name'] = array();
		}
		
		Loader::model('title/model', 'cup_content');
		$titleObj = CupContentTitle::fetchByID($title_id);
		if($titleObj){
			$_SESSION['inspection_copy']['order_list'][] = $title_id;
			$_SESSION['inspection_copy']['order_list_product_name'][] = $titleObj->name;	

			/*
			 * SAG ANZGO-194
			 * Inspection copy orders on Edu Mar
			 * Salesforce do not include the ISBN, please include it
			 * 
			 * */
			$_SESSION['inspection_copy']['order_list_isbn'][] = $titleObj->isbn13;
		}
		$this->redirect('/education/inspection_copy/order');
	}
	
	public function order(){
		Loader::model('title/model', 'cup_content');
		Loader::model('inspection_copy_order/model', 'cup_content');
		
		if(!isset($_SESSION['inspection_copy']['order_list'])){
			$_SESSION['inspection_copy']['order_list'] = array();
		}
		
		if(count($_SESSION['inspection_copy']['order_list']) < 1){
			$this->redirect('/inspection_copy');
		}
		
		//$ids_to_be_removed = array();
		foreach($_SESSION['inspection_copy']['order_list'] as $title_id){
			$to_be_removed = false;
			$titleObj = CupContentTitle::fetchByID($title_id);
			if($titleObj){
				if(strcmp($_SESSION['DEFAULT_LOCALE'], 'en_AU') == 0){
					if(!in_array('Australia', $titleObj->regions)){
						//$ids_to_be_removed[] = $title_id;
						$to_be_removed = true;
					}
				}else{
					if(!in_array('New Zealand', $titleObj->regions)){
						//$ids_to_be_removed[] = $title_id;
						$to_be_removed = true;
					}
				}
			}else{
				//$ids_to_be_removed[] = $title_id;
				$to_be_removed = true;
			}
			
			if($to_be_removed){
				if(($key = array_search($title_id, $_SESSION['inspection_copy']['order_list'])) !== false) {
					unset($_SESSION['inspection_copy']['order_list'][$key]);
				}
				
				if($titleObj){
					if(($key = array_search($titleObj->name, $_SESSION['inspection_copy']['order_list_product_name'])) !== false) {
						unset($_SESSION['inspection_copy']['order_list_product_name'][$key]);
					}
				
					if(($key = array_search($titleObj->isbn13, $_SESSION['inspection_copy']['order_list_isbn'])) !== false) {
						unset($_SESSION['inspection_copy']['order_list_isbn'][$key]);
					}
				}
			}
		}
		
		/*
		foreach($ids_to_be_removed as $title_id){
			if(($key = array_search($title_id, $_SESSION['inspection_copy']['order_list'])) !== false) {
				unset($_SESSION['inspection_copy']['order_list'][$key]);
			}
		}
		*/
		
		$order = new CupContentInspectionCopyOrder();
		if(count($this->post()) > 0){
			$post = $this->post();
			
			$recv_email = 1;
			$recv_post = 1;
			
			if(isset($post['no_email']) && $post['no_email'] == 1){
				$recv_email = 0;
			}
			
			if(isset($post['no_post']) && $post['no_post'] == 1){
				$recv_post = 0;
			}
			
			$order->school_order_number = $post['school_order_number'];
			$order->email = $post['email'];
			$order->title = $post['title'];
			$order->first_name = $post['first_name'];
			$order->last_name = $post['last_name'];
			$order->position = $post['position'];
			$order->school_campus = $post['school_campus'];
			$order->school_postcode = $post['school_postcode'];
			$order->phone = $post['phone'];
			$order->add_to_mailling_list = $recv_email;
			$order->add_to_post_list = $recv_post;
			$order->shipping_address_line_1 = $post['shipping_address_line_1'];
			$order->shipping_address_line_2 = $post['shipping_address_line_2'];
			$order->shipping_address_state = $post['shipping_address_state'];
			$order->shipping_address_city = $post['shipping_address_city'];
			$order->shipping_address_postcode = $post['shipping_address_postcode'];
			
			$country = 'Australia';
			if(strcmp($_SESSION['DEFAULT_LOCALE'], 'en_NZ') == 0){
				$country = 'New Zealand';
			}
			
			$order->shipping_address_country = $country;
			$order->status = "pending";
			$order->syncData = "";
			$order->syncAt = "";
				
			if(isset($post['agreed_terms_and_conditions'])
				&& $post['agreed_terms_and_conditions'] == 'yes'){
					if($order->save()){
						if($order->saveOrderItems($_SESSION['inspection_copy']['order_list'])){
							$this->on_order_success($order);
						
							//put the code to save to SF here after is has successfully saved in DB
							/*
							echo '<pre>';
							print_r($post);
							echo '</pre>';
							echo '<hr />';
							echo '<pre>';
							print_r($_SESSION['inspection_copy']['order_list']);
							echo '</pre>';
							*/
							$order->savetoSalesForce();

							unset($_SESSION['inspection_copy']['order_list']);
							$this->render('/education/inspection_copy/order_success');
						}else{
							$_SESSION['alerts']['error'] = $order->getErrors();
						}
					}else{
						$_SESSION['alerts']['error'] = $order->getErrors();
					}
			}else{
				$_SESSION['alerts']['error'] = 'Terms and Conditions must be accepted';
			}
		}
		
		
		$this->set('order', $order);
		$this->render('/education/inspection_copy/order');
	}
	
	public function order_success(){
		$this->render('/education/inspection_copy/order_success');
	}
	
	
	public function on_order_success($order){
		$pkg  = Package::getByHandle('cup_content');
        $from_email = $pkg->config('FROM_EMAIL_ADDRESS');

        $mh = Loader::helper('mail');
        $mh->addParameter('order', $order);
        //$mh->addParameter('title_access_codes', $title_access_codes);
        $mh->to($order->email);
        $mh->from($from_email);
        $mh->load('inspection_copy_order','cup_content');
        @$mh->sendMail();
	}
}
