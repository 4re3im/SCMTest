<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
class CartLinksBlockController extends BlockController {
		
		protected $btTable = 'btCoreCommerceCartLinks';
		protected $btInterfaceWidth = "400";
		protected $btInterfaceHeight = "300";
		
		protected $showCartLink = true;
		protected $showItemQuantity = true;
		protected $showCheckoutLink = true;
		protected $cartLinkText = 'View Cart';
		protected $checkoutLinkText = 'Checkout';
		public $order;

		public function getBlockTypeDescription() {
			return t("Adds links to the Shopping Cart and Checkout page.");
		}

		public function getBlockTypeName() {
			return t("Cart Links");
		}


		public function on_start() {
			Loader::model('order/current', 'core_commerce');
			$this->order = CoreCommerceCurrentOrder::get();
		}
		
		
		public function on_page_view() {
			Loader::model('cart', 'core_commerce');	
			Loader::model('attribute/categories/core_commerce_product', 'core_commerce');
			$this->addHeaderItem(Loader::helper('html')->css('ccm.core.commerce.cart.css', 'core_commerce'));
			$this->addHeaderItem(Loader::helper('html')->javascript('ccm.core.commerce.cart.js', 'core_commerce'));
			$this->addHeaderItem(Loader::helper('html')->css('jquery.ui.css'));
			$this->addFooterItem(Loader::helper('html')->javascript('jquery.form.js'));
			$this->addFooterItem(Loader::helper('html')->javascript('jquery.ui.js'));
			$this->bogus = 'test';
		}
		
		
		public function view() {
			$db = Loader::db();
			if (is_object($this->order) && $this->order instanceof CoreCommerceCurrentOrder) {
				$this->set('items', $this->order->getTotalProducts());
			} else {
				$this->set('items',0); 
			}
		}

		
		public function add() {
			$this->set('showCartLink', $this->showCartLink);
			$this->set('showItemQuantity', $this->showItemQuantity);
			$this->set('showCheckoutLink', $this->showCheckoutLink);
			$this->set('cartLinkText', $this->cartLinkText);
			$this->set('checkoutLinkText', $this->checkoutLinkText);
		}
		
		public function save($data) {
			if (!isset($data['showCartLink'])) {
				$data['showCartLink'] = 0;
			}
			if (!isset($data['showItemQuantity'])) {
				$data['showItemQuantity'] = 0;
			}
			if (!isset($data['showCheckoutLink'])) {
				$data['showCheckoutLink'] = 0;
			}
			parent::save($data);
		}
	}
