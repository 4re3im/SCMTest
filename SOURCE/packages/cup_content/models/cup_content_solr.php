<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

require_once('../libraries/Solarium/Autoloader.php');
Solarium_Autoloader::register();


class CupContentSolr extends Object {
	protected $solr_config = false;
	protected $solr_client = false;
	
	function __construct() {
		if($this->getSolrConfig()){
			$this->solr_client = new Solarium_Client($this->solr_config);
		}
	}
	
	public function getSolrConfig(){
		if(!$this->solr_config){
			$pkg  = Package::getByHandle('cup_content');
			$store_value = $pkg->config('SOLR_CONFIG');
			if($store_value && unserialize($store_value) !== FALSE){
				$this->solr_config = unserialize($store_value);
			}
		}
		return $this->solr_config;
	}
	
	public function saveCupObject($object){
		$solr_update = $this->solr_client->createUpdate();
		$solr_doc = $solr_update->createDocument();
	
		$class_name = get_class($object);
		if(strcmp($class_name, 'CupContentSeries') == 0){
			$solr_doc->id = $class_name.'_'.$object->id;
			$doc->name = $object->name;
			$doc->object_type = $class_name;
		}elseif(strcmp($class_name, 'CupContentTitle') == 0){
			
		}
	}
}