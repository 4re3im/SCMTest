<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

class CupBlockMainCarousel extends Object {
	protected $carousel_config = array(
								'interval'=>8,
								'carousel_data' => array(
										array(
											'enable'=>0,
											'image'=>"",
											'title'=>"",
											'description'=>"",
											'link'=>""
										),
										array(
											'enable'=>0,
											'image'=>"",
											'title'=>"",
											'description'=>"",
											'link'=>""
										),
										array(
											'enable'=>0,
											'image'=>"",
											'title'=>"",
											'description'=>"",
											'link'=>""
										),
										array(
											'enable'=>0,
											'image'=>"",
											'title'=>"",
											'description'=>"",
											'link'=>""
										),
										array(
											'enable'=>0,
											'image'=>"",
											'title'=>"",
											'description'=>"",
											'link'=>""
										)
									)
							);
							
	function __construct($region = 'AU') {
		$pkg  = Package::getByHandle('cup_content');
		$store_value = $pkg->config('CAROUSEL_CONFIG');
		if(strcmp($region, 'AU') == 0){
			$store_value = $pkg->config('CAROUSEL_CONFIG');
		}else{
			$store_value = $pkg->config('CAROUSEL_CONFIG_NZ');
		}
		$store_value = unserialize($store_value);
		if(is_array($store_value)){
			$this->carousel_config = array_merge($this->carousel_config, $store_value);
		}
	}
	
	public function saveConfig($config_value, $region = 'AU'){
		$pkg  = Package::getByHandle('cup_content');
		if(strcmp($region, 'AU') == 0){
			$store_value = $pkg->saveConfig('CAROUSEL_CONFIG', serialize($config_value));
		}else{
			$store_value = $pkg->saveConfig('CAROUSEL_CONFIG_NZ', serialize($config_value));
		}
	}
	
	public function getConfig(){
		return $this->carousel_config;
	}
	
	public function toResultJSON(){
		$config = $this->carousel_config;
		$config['carousel_data'] = array();
		foreach($this->carousel_config['carousel_data'] as $idx => $each){
			if($each['enable']){
				array_push($config['carousel_data'], $each);
			}
		}
		
		return json_encode($config);
	}
	
}