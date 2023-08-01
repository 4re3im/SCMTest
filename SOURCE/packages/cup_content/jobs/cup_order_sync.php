<?php 
/**
*
* Responsible for loading the indexed search class and initiating the reindex command.
* @package Utilities
*/

defined('C5_EXECUTE') or die("Access Denied.");
class CupOrderSync extends Job {

	//public $jNotUninstallable=1;
	
	public function getJobName() {
		return t("CUP Order Synchronisation");
	}
	
	public function getJobDescription() {
		return t("CUP Order Synchronisation");
	}
	
	public function run() {
		Cache::disableCache();

		//echo "hello world";
		Loader::model('cup_content_event', 'cup_content');
		$ev = new CupContentEvent();
		$ev->titleOrderSyncDeamon();
		
		return t('CupOrderSync Finished!');
	}

}

?>