<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
class CoreCommercePrice {

	static $symbol = '';
	static $thousands = '';
	static $decimal = '';
	static $leftPlacement = '';
	
	public static function getThousandsSeparator() {
		if (empty(self::$thousands)) {
			$pkg = Package::getByHandle('core_commerce');
			self::$thousands = $pkg->config('CURRENCY_THOUSANDS_SEPARATOR');
			if (empty(self::$thousands)) {
				self::$thousands = ',';
			}
		}
		return self::$thousands;
	}
	
	public static function getDecimalPoint() {
		if (empty(self::$decimal)) {
			$pkg = Package::getByHandle('core_commerce');
			self::$decimal = $pkg->config('CURRENCY_DECIMAL_POINT');
			if (empty(self::$decimal)) {
				self::$decimal = '.';
			}
		}
		return self::$decimal;
	}

	
	/**
	 * returns a formatted price string for display, if the package config USE_ZEND_CURRENCY is set to true, 
	 * this function will ignore the other settings and formate the price off the current Locale
	 * @param $number
	 * @return string
	 */
	public static function format($number) {
		$pkg = Package::getByHandle('core_commerce');
		if($pkg->config('USE_ZEND_CURRENCY')) {
			Loader::library('3rdparty/Zend/Currency');
			if(Loader::helper('multilingual','core_commerce')->isEnabled()) {
				$locale = Loader::helper('section','multilingual')->getLocale();
			} else {
				$locale = new Zend_Locale(ACTIVE_LOCALE);
			}
			$currency = new Zend_Currency($locale);
			return $currency->toCurrency($number);
			
		} else {
			if (empty(self::$symbol)) {
				$pkg = Package::getByHandle('core_commerce');
				self::$symbol = t($pkg->config('CURRENCY_SYMBOL'));
				if (empty(self::$symbol)) {
					self::$symbol = t('$');
				}
			}
			if (isset(self::$leftPlacement)) {
				$pkg = Package::getByHandle('core_commerce');
				self::$leftPlacement =  $pkg->config('CURRENCY_SYMBOL_LEFT_PLACEMENT');
				if(self::$leftPlacement != '0') {
					self::$leftPlacement = 1;
				}
					
			}
			if (self::$leftPlacement){ 
				$left = self::$symbol;
				$right = '';
			} else {
				$left = '';
				$right = self::$symbol;
			} 
			return ($number<0 ? '-': '') . $left . number_format(abs($number), 2, self::getDecimalPoint(), self::getThousandsSeparator()) . $right;
		}
	}
	
}
