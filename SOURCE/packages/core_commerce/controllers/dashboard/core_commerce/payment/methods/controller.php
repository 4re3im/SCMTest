<?php  

Loader::model('payment/method', 'core_commerce');
Loader::model('sales/tax/rate', 'core_commerce');
class DashboardCoreCommercePaymentMethodsController extends Controller {

	public function view() {		
      $methods = CoreCommercePaymentMethod::getList();

      $defaults = array('default','authorize_net_sim','authorize_net_aim','paypal_website_payments_standard');
      foreach($methods as $method) {
         $handles[] = $method->getPaymentMethodHandle();
      }

      $missing = 0;
      if (count($handles)) {
         foreach($defaults as $default) {
            if (!(in_array($default,$handles))) {
               $missing++;
            }
         }
      }else {
         $missing = 1; //no payment methods at all
      }

      if($missing) {
         $this->set('missing_defaults',true);
      }

		$this->set('methods', $methods);
	}
	
	public function on_start() {
		$this->set('ih', Loader::helper('concrete/interface'));
		$this->set('form', Loader::helper('form'));
		$this->set('disableThirdLevelNav', true);
	}

	public function edit_method($methodID) {
		$est = CoreCommercePaymentMethod::getByID($methodID);
		$this->set("method", $est);
	}

   public function delete_method($methodID, $token=NULL){
		try {
				
         $method = CoreCommercePaymentMethod::getByID($methodID);

			if(!($method instanceof CoreCommercePaymentMethod)) {
				throw new Exception(t('Invalid payment method ID.'));
			}
	
			$valt = Loader::helper('validation/token');
			if (!$valt->validate('delete_method', $token)) {
				throw new Exception($valt->getErrorMessage());
			}

         $method->delete();
			$this->redirect('/dashboard/core_commerce/payment/methods', 'payment_method_deleted');
		} catch (Exception $e) {
			$this->set('error', $e);
		}
   }

   public function restore_defaults() {
      $pkg = Package::getByHandle('core_commerce');
      $method = CoreCommercePaymentMethod::getByHandle('default');
      if (!(is_object($method))) {
         CoreCommercePaymentMethod::add('default', t('Default Gateway'), 0, NULL, $pkg);
      }
      unset($method);
      $method = CoreCommercePaymentMethod::getByHandle('paypal_website_payments_standard');
      if (!(is_object($method))) {
         CoreCommercePaymentMethod::add('paypal_website_payments_standard', t('Paypal Website Payments Standard'), 0, NULL, $pkg);
      }
      unset($method);
      $method = CoreCommercePaymentMethod::getByHandle('authorize_net_sim');
      if (!(is_object($method))) {
         CoreCommercePaymentMethod::add('authorize_net_sim', t('Authorize.Net - Server Integration Method'), 0, NULL, $pkg);
      }
      unset($method);
      $method = CoreCommercePaymentMethod::getByHandle('authorize_net_aim');
      if (!(is_object($method))) {
         CoreCommercePaymentMethod::add('authorize_net_aim', t('Authorize.Net - Advanced Integration Method'), 0, NULL, $pkg);
      }
      $this->redirect('/dashboard/core_commerce/payment/methods/');
   }

	public function add_payment_method() {
		$pat = CoreCommercePendingPaymentMethod::getByHandle($this->post('paymentMethodHandle'));
		if (is_object($pat)) {
			$pat->install();
		}
		$this->redirect('dashboard/core_commerce/payment/methods', 'payment_method_added');
	}
	
	public function save() {
		$paymentMethodID = $this->post('paymentMethodID');
		$method = CoreCommercePaymentMethod::getByID($paymentMethodID);
		
		$cnt = $method->getController();
		$e = $cnt->validate();
		if ($e->has()) {
			$this->set('error', $e);
			$this->edit_method($paymentMethodID);
		} else {
			$method->update($this->post());
			$this->redirect('/dashboard/core_commerce/payment/methods', 'payment_method_updated');
		}		
	}
	
	public function payment_method_updated() {
		$this->set('message', t('Payment Method Saved'));
		$this->view();
	}

	public function payment_method_deleted() {
		$this->set('message', t('Payment Method Deleted'));
		$this->view();
	}

	public function payment_method_added() {
		$this->set('message', t('Payment Method Installed'));
		$this->view();
	}


}
