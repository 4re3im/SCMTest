<?php  defined('C5_EXECUTE') or die(_("Access Denied."));

class CupContentToolsHelper { 
	
	public static function string2prettyURL($string) {
		$string = trim($string);
		setlocale(LC_CTYPE, 'en_US.UTF8');
		$string = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
		
		$string = str_replace(array('`','~','!','@','#','$','%','^',
									'&','*','(',')','+','=','|','\\',
									':',';','"','\'','<','>',',','.'), 
									'', $string);
									
		$string = str_replace(array('/'),'-', $string);							

		$string = preg_replace('/\\s+/', '-', $string);

		return $string;
	}
	
	public static function setLocate($locate){
		setcookie('DEFAULT_LOCALE', $locale, time()+60*60*24*365);
		$_SESSION['DEFAULT_LOCALE'] = $locale;
	}
	
	public static function initialLocate(){
		if(!isset($_SESSION['DEFAULT_LOCALE'])){
			self::setLocate('en_AU');
		}
	}
	
}
