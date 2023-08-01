<?php 
/**
*
* Responsible for loading the indexed search class and initiating the reindex command.
* @package Utilities
*/

defined('C5_EXECUTE') or die("Access Denied.");
class CupInventorySync extends Job {

	//public $jNotUninstallable=1;
	
	public function getJobName() {
		return t("CUP Inventory Synchronisation");
	}
	
	public function getJobDescription() {
		return t("CUP Inventory Synchronisation");
	}
	
	public function run() {
		Cache::disableCache();
		Loader::model('cup_content_event', 'cup_content');
		$ev = new CupContentEvent();
		
		if($ev->inventorySync()){
			return t('Finished!');
		}else{
			return t('Finished with Errors');
		}
	}

}

?>