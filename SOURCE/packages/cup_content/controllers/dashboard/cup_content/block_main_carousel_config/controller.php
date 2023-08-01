<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('block_main_carousel/model', 'cup_content');

class DashboardCupContentBlockMainCarouselConfigController extends Controller {

	public function view() {
		$carousel = new CupBlockMainCarousel();
		
		$this->set('carousel_config', $carousel->getConfig());
	}
	
	public function edit(){
		$carousel = new CupBlockMainCarousel();
		if(count($this->post()) > 0){
			$carousel->saveConfig($this->post());
			$this->redirect('/dashboard/cup_content/block_main_carousel_config');
		}
		
		$this->set('carousel_config', $carousel->getConfig());
		$this->render('/dashboard/cup_content/block_main_carousel_config/edit');
	}
}