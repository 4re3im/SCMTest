<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

class VceSlider extends Object {
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
							
	function __construct() {
		$pkg  = Package::getByHandle('cup_competition');
		$store_value = $pkg->config('VCE_SLIDER_CONFIG');
		$store_value = unserialize($store_value);
		if(is_array($store_value)){
			$this->carousel_config = array_merge($this->carousel_config, $store_value);
		}
	}
	
	public function saveConfig($config_value){
		$pkg  = Package::getByHandle('cup_competition');
		$store_value = $pkg->saveConfig('VCE_SLIDER_CONFIG', serialize($config_value));
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