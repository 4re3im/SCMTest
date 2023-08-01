<?php  defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model('attribute/categories/core_commerce_order','core_commerce');
Loader::model('cart', 'core_commerce');
Loader::model('order/current', 'core_commerce');
Loader::model('order/product', 'core_commerce');
class CartController extends Controller {
	
	/**
	 * @var boolean
	 */
	protected $allowRedirect = true;
	
	public function on_start() {
		$this->addHeaderItem(Loader::helper('html')->css('ccm.core.commerce.cart.css', 'core_commerce'));
		$this->addHeaderItem(Loader::helper('html')->css('ccm.core.commerce.checkout.css', 'core_commerce'));
		$this->addHeaderItem(Loader::helper('html')->javascript('ccm.core.commerce.cart.js', 'core_commerce'));
		$this->error = Loader::helper('validation/error');
		$this->set('cart', $this->getOrder());
	}
	
	public function getOrder() {
		return CoreCommerceCurrentOrder::get();	
	}
	
	public function update() {
		$cart = $this->getOrder();
		$js = Loader::helper('json');
		$obj = new stdClass;
      $obj->notification = false;
		$obj->error = false;
		$obj->message = '';
      $obj->bonus_message = '';

		if ($_POST['productID']) {
			$pr = CoreCommerceProduct::getByID($_POST['productID']);
			
			if(Loader::helper('multilingual','core_commerce')->isEnabled()) {
				$cartLanguage = $cart->getOrderLanguage();
				if(strlen($cartLanguage) && strlen($pr->getProductLanguage())) {
					if(strtolower(trim($cartLanguage)) != strtolower(trim($pr->getProductLanguage()))) {
						$this->error->add(t('You have items in a different language in your cart, you can purchase items from one language at a time.'));
					}
				}
			}
			
			$attribs = $pr->getProductConfigurableAttributesRequired();
			foreach($attribs as $at) { 
				$e1 = $at->validateAttributeForm();
				if ($e1 == false) {
					$this->error->add(t('The field "%s" is required', $at->getAttributeKeyName()));
				} else if ($e1 instanceof ValidationErrorHelper) {
					$this->error->add($e1);
				}
			}
			
			$quantity = $pr->getMinimumPurchaseQuantity();
			if (isset($_POST['quantity'])) {
				$quantity = $_POST['quantity'];
			} else if ( $cart->getProductTotalQuantityInOrder($pr) > 0 || $pr->getMinimumPurchaseQuantity() == 0){
				$quantity = 1;
			}

/*
			$e_q = $quantity + $cart->getProductTotalQuantityInOrder($pr);
			$this->error->add(t("this doesn't look like it's working. %s", $e_q));
*/
			if ($quantity + $cart->getProductTotalQuantityInOrder($pr) < $pr->getMinimumPurchaseQuantity()) {
				$this->error->add(t("You must buy at least %s of this product.",$pr->getMinimumPurchaseQuantity())); 
			}

			if (($quantity + $cart->getProductTotalQuantityInOrder($pr) > $pr->getProductQuantity()) && (!$pr->productAllowsNegativeQuantity())) {
				$quantity = $pr->getProductQuantity();
				$this->error->add(t("Not enough stock to complete your request.\nAvailable stock is %s", $pr->getProductQuantity()));
			}
			
			if ($this->error->has()) {
				$obj->error = true;
				foreach($this->error->getList() as $s) {
					$obj->message .= $s . "\n";
				}
			} else {
				// add the product to the cart
				$pr = $cart->addProduct($pr, $quantity);
	            $eventResult = Events::fire('core_commerce_add_to_cart', $cart, $pr, $quantity );
	            if(strlen($eventResult)) {
	               $obj->notification = true;
	               $obj->bonus_message = $eventResult;
	            }
				// append attributes to order product
				$attribs = $pr->getProductConfigurableAttributes();
				foreach($attribs as $at) { 
					$at->saveAttributeForm($pr);
				}

				$pr->rescanOrderProductPricePaid();
				
				// decide wether to increment quantity or add additional line item
				if ($other = $cart->orderContainsOtherProduct($pr)) {
					$cart->removeProduct($pr);	
					if (!$other->productIsPhysicalGood()) {
						$newQuant = 1;
					}
					else {
						$newQuant = $other->getQuantity()+$quantity;
					}
					$other->setQuantity($newQuant);
				}
			}
			
			if ($_POST['method'] == 'JSON') {
				print $js->encode($obj);
				exit;
			}

			
		} else { 
			$products = $cart->getProducts();
			foreach($products as $ecp) {
            //$postQuantity = $_POST['quantity_' . $ecp->getOrderProductID()];
				if (!$ecp->productIsPhysicalGood()) {
					$ecp->setQuantity(1);
				} else if ($_POST['quantity_' . $ecp->getOrderProductID()] > 0) {
					if (!$ecp->product->productAllowsNegativeQuantity() && $_POST['quantity_' . $ecp->getOrderProductID()]+$cart->getProductTotalQuantityInOrder($ecp)-$ecp->getQuantity()>$ecp->product->getProductQuantity()) {
						//get full product description for error
						$name = $ecp->getProductName();
						$attribs = $ecp->getProductConfigurableAttributes();
						foreach($attribs as $ak) {
							$name .= ", ".$ecp->getAttribute($ak);
						}
						$this->error->add(t('Not enough stock to update "%s", current stock is %d',$name,$ecp->product->getProductQuantity()));
						$_POST['quantity_' . $ecp->getOrderProductID()] = $ecp->product->getProductQuantity();
					} else if ($_POST['quantity_' . $ecp->getOrderProductID()]+$cart->getProductTotalQuantityInOrder($ecp)-$ecp->getQuantity()<$ecp->product->getMinimumPurchaseQuantity()) {
						//get full product description for error
						$name = $ecp->getProductName();
						$attribs = $ecp->getProductConfigurableAttributes();
						foreach($attribs as $ak) {
							$name .= ", ".$ecp->getAttribute($ak);
						}
						$this->error->add(t('To buy "%s", you need to order at least %d of them.',$name,$ecp->product->getMinimumPurchaseQuantity()));
						$_POST['quantity_' . $ecp->getOrderProductID()] = $ecp->product->getMinimumPurchaseQuantity();
					}
					else {
						$ecp->setQuantity($_POST['quantity_' . $ecp->getOrderProductID()]);
					}
				} else if ($_POST['quantity_' . $ecp->getOrderProductID()] == 0) {
					$cart->removeProduct($ecp);
				}
			}
			
			if ($_POST['method'] == 'JSON' && (!$this->error->has())) { 
				print $js->encode($obj);
				exit;
			}
		}
		
		if (!$this->error->has() && $this->allowRedirect) {
			if (isset($_POST['checkout_no_dialog'])) { 
				$chs = Loader::helper('checkout/step', 'core_commerce');
				header('Location: ' . CoreCommerceCheckoutStep::getBase() . View::url('/checkout'));
				exit;
			} else {
				$c = $this->getCollectionObject();
				$this->redirect($c->getCollectionPath());
			}
		}
		
	}

	public function on_before_render() {
		if ($_POST['method'] == 'JSON') { 
			if ($this->error->has()) {
				$js = Loader::helper('json');
				$obj = new stdClass;
				$obj->error = false;
				$obj->message = '';
				if ($this->error->has()) {
					$obj->error = true;
					foreach($this->error->getList() as $s) {
						$obj->message .= $s . "\n";
					}
				}
				print $js->encode($obj);
				exit;
			}
		}
		$this->set('error', $this->error);
	}

	public function remove_product($productID = 0) {
		$js = Loader::helper('json');
		$obj = new stdClass;
		$obj->error = false;
		$obj->message = '';
		if ($productID > 0) {
			$pr = CoreCommerceOrderProduct::getByID($productID);
			if (is_object($pr)) {
				$cart = $this->getOrder();
				$cart->removeProduct($pr);
				if ($_REQUEST['method'] == 'JSON') { 
					print $js->encode($obj);
					exit;
				}
			} else {
				$this->error->add(t('Invalid product ID.'));
			}
		}
	}
	
	public function submit() {
		parent::submit();
		/*$t = Loader::helper('validation/strings');
		if (!$t->email($this->post('oEmail'))) {
			$this->error->add(t('You must specify a valid email address.'));
		}
		
		$validAttributes = array(
			CoreCommerceOrderAttributeKey::getByHandle('billing_first_name'),
			CoreCommerceOrderAttributeKey::getByHandle('billing_last_name'),
			CoreCommerceOrderAttributeKey::getByHandle('billing_address'),
			CoreCommerceOrderAttributeKey::getByHandle('billing_phone')
		);
		
		foreach($validAttributes as $eak) {
			if (!$eak->validateAttributeForm()) {
				$this->error->add(t('The field "%s" is required', $eak->getAttributeKeyName()));
			}
		}
		
		if (!$this->error->has()) {
			$o = CoreCommerceCurrentOrder::get();
			$attributes = AttributeSet::getByHandle('billing')->getAttributeKeys();
			foreach($attributes as $eak) {
				$eak->saveAttributeForm($o);				
			}
			$o->setOrderEmail($this->post('oEmail'));
			$this->redirect($this->getNextCheckoutStep()->getRedirectURL());
		}
		*/
	}
	
}
