<?php  
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('block_main_carousel/model', 'cup_content');

class DashboardCupCompetitionConfigController extends Controller {
	public function view($name = false) {
		if($name){
			if(strcmp($name, 'block_type_main_carousel') == 0){
				return $this->block_type_main_carousel();
			}elseif(strcmp($name, 'solr_server') == 0){
				return $this->solr_server();
			}elseif(strcmp($name, 'test') == 0){
				return $this->test();
			}
		}
						
		$config_links = array(
						'HSC Slider Config' => 'hsc_slider',
						'VCE Slider Config' => 'vce_slider',
						'Email Settings' => 'email_settings'
					);
		
		$this->set('config_links', $config_links);
	}
	
	public function hsc_slider(){
		Loader::model('hsc_slider', 'cup_competition');
		$slider = new HscSlider();
		if(count($this->post()) > 0){
			$slider->saveConfig($this->post());
			$_SESSION['alerts'] = array('success' => 'Config saved');
			
			$slider = new HscSlider();
		}
		
		$this->set('slider_config', $slider->getConfig());
		$this->render('/dashboard/cup_competition/config/hsc_slider');
	}
	
	public function vce_slider(){
		Loader::model('vce_slider', 'cup_competition');
		$slider = new VceSlider();
		if(count($this->post()) > 0){
			$slider->saveConfig($this->post());
			$_SESSION['alerts'] = array('success' => 'Config saved');
			
			$slider = new VceSlider();
		}
		
		$this->set('slider_config', $slider->getConfig());
		$this->render('/dashboard/cup_competition/config/vce_slider');
	}
	
	public function email_settings(){
		$pkg  = Package::getByHandle('cup_competition');
		$hsc_email = $pkg->config('HSC_NOTIFICATION_EMAIL');
		$vce_email = $pkg->config('VCE_NOTIFICATION_EMAIL');
		
		$errors = array();
		$success = array();
		if(count($this->post())>0){
			$post = $this->post();
			if(isset($post['hsc_email']) && filter_var($post['hsc_email'], FILTER_VALIDATE_EMAIL)) {
				$pkg->saveConfig('HSC_NOTIFICATION_EMAIL', $post['hsc_email']);
				$hsc_email = $post['hsc_email'];
				$success[] = 'HSC NOTIFICATION EMAIL has been saved';
			}else{
				$errors[] = 'HSC Email is invalid';
				$hsc_email = $post['hsc_email'];
			}
			
			if(isset($post['vce_email']) && filter_var($post['vce_email'], FILTER_VALIDATE_EMAIL)) {
				$pkg->saveConfig('VCE_NOTIFICATION_EMAIL', $post['vce_email']);
				$vce_email = $post['hsc_email'];
					$success[] = 'VCE NOTIFICATION EMAIL has been saved';
			}else{
				$errors[] = 'VCE Email is invalid';
				$vce_email = $post['hsc_email'];
			}
			
			if(count($errors) > 0)
			$_SESSION['alerts']['error'] = $errors;
			
			if(count($success) > 0)
			$_SESSION['alerts']['success'] = $success;
		}
	
		
		
		$this->set('hsc_email', $hsc_email);
		$this->set('vce_email', $vce_email);
		
		$this->render('/dashboard/cup_competition/config/email_settings');
	}

	public function testEmail($email = false){
		if($email === false){
			$email = 'feiloatwork@live.com';
		}
		
		$mh = Loader::helper('mail');
		$mh->setSubject('Simple Message');
		$mh->setBody('This is my simple message body.');
		$mh->to($email);
		$mh->from('competitions@cambridge.edu.au');
		$mh->sendMail();
		
		echo "Email Sent";
		exit();
	}	

}
