<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::library('payment/controller', 'core_commerce');
class CoreCommercePaypalWebsitePaymentsStandardPaymentMethodController extends CoreCommercePaymentController {

	public function method_form() {
		$pkg = Package::getByHandle('core_commerce');
		$this->set('PAYMENT_METHOD_PAYPAL_STANDARD_EMAIL', $pkg->config('PAYMENT_METHOD_PAYPAL_STANDARD_EMAIL'));
		$this->set('PAYMENT_METHOD_PAYPAL_STANDARD_TEST_MODE', $pkg->config('PAYMENT_METHOD_PAYPAL_STANDARD_TEST_MODE'));
		$this->set('PAYMENT_METHOD_PAYPAL_STANDARD_TRANSACTION_TYPE', $pkg->config('PAYMENT_METHOD_PAYPAL_STANDARD_TRANSACTION_TYPE'));
		$this->set('PAYMENT_METHOD_PAYPAL_STANDARD_CURRENCY_CODE', 
			(strlen($pkg->config('PAYMENT_METHOD_PAYPAL_STANDARD_CURRENCY_CODE'))?$pkg->config('PAYMENT_METHOD_PAYPAL_STANDARD_CURRENCY_CODE'):'USD')
			);
		$this->set('PAYMENT_METHOD_PAYPAL_STANDARD_PASS_ADDRESS',$pkg->config('PAYMENT_METHOD_PAYPAL_STANDARD_PASS_ADDRESS'));
		
		$paypal_currency_codes = array(
			'AUD'=>t('Australian Dollar'),
			'CAD'=>t('Canadian Dollar'),
			'CZK'=>t('Czech Koruna'),
			'DKK'=>t('Danish Krone'),
			'EUR'=>t('Euro'),
			'HKD'=>t('Hong Kong Dollar'),
			'HUF'=>t('Hungarian Forint'),
			'ILS'=>t('Israeli New Sheqel'),
			'JPY'=>t('Japanese Yen'),
			'MXN'=>t('Mexican Peso'),
			'NOK'=>t('Norwegian Krone'),
			'NZD'=>t('New Zealand Dollar'),
			'PLN'=>t('Polish Zloty'),
			'GBP'=>t('Pound Sterling'),
			'SGD'=>t('Singapore Dollar'),
			'SEK'=>t('Swedish Krona'),
			'CHF'=>t('Swiss Franc'),
			'USD'=>t('U.S. Dollar')
		);
		asort($paypal_currency_codes);
		$this->set('paypal_currency_codes',$paypal_currency_codes);	
	}
	
	public function validate() {
		$e = parent::validate();
		$ve = Loader::helper('validation/strings');
		
		if ($this->post('PAYMENT_METHOD_PAYPAL_STANDARD_EMAIL') == '') {
			$e->add(t('You must specify your Paypal ID, which is an email address.'));
		}

		return $e;
	}
	
	public function action_notify_complete() {
		$success = false;
       Loader::model('order/model', 'core_commerce');
		$pkg = Package::getByHandle('core_commerce');

		if ($this->validateIPN()) {
			$eh = Loader::helper('encryption');
			$orderID = $eh->decrypt($_REQUEST['invoice']);
			$o = CoreCommerceOrder::getByID($orderID);
         Log::addEntry($orderID.": ".PHP_EOL.var_export($_REQUEST));
			if ($o) {
				// deal with float comparison problems
				$order_total = number_format($o->getOrderTotal(),2,'.','');
				$paid_total = number_format($_REQUEST['mc_gross'],2,'.','');
				
				if ($paid_total >= $order_total) {
					if ($_REQUEST['payment_status'] == 'Pending') {
						$o->setStatus(CoreCommerceOrder::STATUS_PENDING);
						parent::finishOrder($o, 'Paypal - Website Payments Standard');
					} else if ($_REQUEST['payment_status'] == 'Processed' || $_REQUEST['payment_status'] == 'Completed') {
						$o->setStatus(CoreCommerceOrder::STATUS_AUTHORIZED);				
						parent::finishOrder($o, 'Paypal - Website Payments Standard');
					} else {
						Log::addEntry('Unable to set status. Status received: ' . $_REQUEST['payment_status']);
					}			
				} else {
					Log::addEntry('Invalid payment for order# '.$o->getOrderID() . " Requested ". $pkg->config('CURRENCY_SYMBOL').$order_total.', got '.$pkg->config('CURRENCY_SYMBOL').$paid_total );
					Log::addEntry('Invalid payment debug info for order# '.$o->getOrderID().'\n'.var_export($_REQUEST,true) . var_export($o,true));
				}
				
			} else {
				Log::addEntry('Received order notification with unknown order: '.$orderID);
			}
		}
	}
	
	public function form() {
		$pkg = Package::getByHandle('core_commerce');
		if ($pkg->config('PAYMENT_METHOD_PAYPAL_STANDARD_TEST_MODE') == 'test') { 
			$this->set('action', 'https://www.sandbox.paypal.com/cgi-bin/webscr');
		} else {
			$this->set('action', 'https://www.paypal.com/cgi-bin/webscr');
		}
		$o = CoreCommerceCurrentOrder::get();
      //would it be possible to set the status here
      //then we would have a legitimate invoice number
      $o->setStatus(CoreCommerceOrder::STATUS_INCOMPLETE);
		$this->set('item_name', SITE);

		// paypal fields
		$fields['cmd'] = '_xclick';
		$fields['address_override'] = 0;
		$fields['rm'] = 2;
		$fields['no_note'] = 1;
		
		// address information
		$fields['item_name'] = t('Purchase from %s', SITE)." - ".t('Order #').$o->getInvoiceNumber();
		
		$shipping_address = $o->getAttribute('shipping_address');
		if(($pkg->config('PAYMENT_METHOD_PAYPAL_STANDARD_PASS_ADDRESS') =='shipping') && is_object($shipping_address)) {
			$fields['first_name'] = $o->getAttribute('shipping_first_name');
			$fields['last_name'] = $o->getAttribute('shipping_last_name');
			$fields['address1'] = $shipping_address->getAddress1();
			$fields['address2'] = $shipping_address->getAddress2();
			$fields['city'] = $shipping_address->getCity();
			$fields['state'] = $shipping_address->getStateProvince();
			$fields['zip'] = $shipping_address->getPostalCode();
			$fields['country'] = $shipping_address->getCountry();
			$fields['night_phone_a'] = $o->getAttribute('shipping_phone');
		} else {
			$fields['first_name'] = $o->getAttribute('billing_first_name');
			$fields['last_name'] = $o->getAttribute('billing_last_name');
			$fields['address1'] = $o->getAttribute('billing_address')->getAddress1();
			$fields['address2'] = $o->getAttribute('billing_address')->getAddress2();
			$fields['city'] = $o->getAttribute('billing_address')->getCity();
			$fields['state'] = $o->getAttribute('billing_address')->getStateProvince();
			$fields['zip'] = $o->getAttribute('billing_address')->getPostalCode();
			$fields['country'] = $o->getAttribute('billing_address')->getCountry();
			$fields['night_phone_a'] = $o->getAttribute('billing_phone');
		}
		
		$fields['amount'] = $o->getOrderTotal();
		
		$fields['currency_code'] = $pkg->config('PAYMENT_METHOD_PAYPAL_STANDARD_CURRENCY_CODE');
		
		// email
		$u = new User();
		$ui = UserInfo::getByID($u->getUserID());
		$fields['business'] = $pkg->config('PAYMENT_METHOD_PAYPAL_STANDARD_EMAIL');
		$fields['email'] = $ui ? $ui->getUserEmail() : '';
		$fields['paymentaction'] = $pkg->config('PAYMENT_METHOD_PAYPAL_STANDARD_TRANSACTION_TYPE');
		
		$fields['address_override'] = 1;
		$fields['no_shipping'] = 1;
		
 		//callback
		$fields['notify_url'] = $this->action('notify_complete');
	
		$ch = Loader::helper('checkout/step', 'core_commerce');
		$ns = $ch->getNextCheckoutStep();
		$ps = $ch->getCheckoutStep();
		$returnURL = $ns->getURL();
		$fields['return'] = $returnURL;
		$fields['cancel_return'] = $ps->getURL();
		$eh = Loader::helper('encryption');
		$fields['invoice'] = $eh->encrypt($o->getOrderID());
		$this->set('fields', $fields);
	}
		
	public function save() {
		$pkg = Package::getByHandle('core_commerce');
		$pkg->saveConfig('PAYMENT_METHOD_PAYPAL_STANDARD_EMAIL', $this->post('PAYMENT_METHOD_PAYPAL_STANDARD_EMAIL'));
		$pkg->saveConfig('PAYMENT_METHOD_PAYPAL_STANDARD_TEST_MODE', $this->post('PAYMENT_METHOD_PAYPAL_STANDARD_TEST_MODE'));
		$pkg->saveConfig('PAYMENT_METHOD_PAYPAL_STANDARD_TRANSACTION_TYPE', $this->post('PAYMENT_METHOD_PAYPAL_STANDARD_TRANSACTION_TYPE'));
		$pkg->saveConfig('PAYMENT_METHOD_PAYPAL_STANDARD_CURRENCY_CODE', $this->post('PAYMENT_METHOD_PAYPAL_STANDARD_CURRENCY_CODE'));
		$pkg->saveConfig('PAYMENT_METHOD_PAYPAL_STANDARD_PASS_ADDRESS', $this->post('PAYMENT_METHOD_PAYPAL_STANDARD_PASS_ADDRESS'));
	}
	

   private function validateIPN() {
      $pkg = Package::getByHandle('core_commerce');
      $req = 'cmd=' . urlencode('_notify-validate');

      foreach ($_POST as $key => $value) {
         $value = urlencode(stripslashes($value));
         $req .= "&$key=$value";
      }

      $sandbox = '';
		if ($pkg->config('PAYMENT_METHOD_PAYPAL_STANDARD_TEST_MODE') == 'test') { 
         $sandbox = ".sandbox";
      }

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, 'https://www'.$sandbox.'.paypal.com/cgi-bin/webscr');
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host: www'.$sandbox.'.paypal.com'));
      $res = curl_exec($ch);
      curl_close($ch);
 
 
// assign posted variables to local variables
      $item_name = $_POST['item_name'];
      $item_number = $_POST['item_number'];
      $payment_status = $_POST['payment_status'];
      $payment_amount = $_POST['mc_gross'];
      $payment_currency = $_POST['mc_currency'];
      $txn_id = $_POST['txn_id'];
      $receiver_email = $_POST['receiver_email'];
      $payer_email = $_POST['payer_email'];

      if (strcmp ($res, "VERIFIED") == 0) {
      // check the payment_status is Completed
      // check that txn_id has not been previously processed
      // check that receiver_email is your Primary PayPal email
      // check that payment_amount/payment_currency are correct
      // process payment
         return true;
      }
      else if (strcmp ($res, "INVALID") == 0) {
         // log for manual investigation
         Log::addEntry(t("Paypal had an IPN issue : ").var_export($res));
         return false;
      }
	}
	
}
