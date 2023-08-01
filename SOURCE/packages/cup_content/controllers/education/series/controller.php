<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('series/model', 'cup_content');
Loader::model('cup_content_search', 'cup_content');
Loader::helper('tools', 'cup_content');

class SeriesController extends Controller {

	public function on_start(){
		$html = Loader::helper('html');
		$this->addHeaderItem($html->css('cup_content.css', 'cup_content')); 
		$this->addHeaderItem($html->css('title_content.css', 'cup_content')); 
		
                $v = View::getInstance();
                $v->setTheme(PageTheme::getByHandle("education_theme"));
	
		$this->addHeaderItem(Loader::helper('html')->javascript('jquery.js'));
		$this->addHeaderItem(Loader::helper('html')->css('ccm.core.commerce.cart.css', 'core_commerce'));
		$this->addHeaderItem(Loader::helper('html')->javascript('ccm.core.commerce.cart.js', 'core_commerce'));
		$this->addFooterItem(Loader::helper('html')->javascript('jquery.form.js'));
		$this->addHeaderItem(Loader::helper('html')->javascript('jquery.ui.js'));
		$this->addHeaderItem(Loader::helper('html')->css('jquery.ui.css'));
		
		$pkg = Package::getByHandle('core_commerce');
		if($pkg->config('WISHLISTS_ENABLED')) {
			$this->addHeaderItem(Loader::helper('html')->javascript('ccm.core.commerce.wishlist.js', 'core_commerce'));
			$this->addHeaderItem(Loader::helper('html')->css('ccm.core.commerce.wishlist.css', 'core_commerce'));
		}
		
		if(!isset($_SESSION['DEFAULT_LOCALE'])){
			$_SESSION['DEFAULT_LOCALE'] = 'en_AU';
		}elseif(!in_array($_SESSION['DEFAULT_LOCALE'], array('en_AU', 'en_NZ'))){
			$_SESSION['DEFAULT_LOCALE'] = 'en_AU';
		}
	}

	public function view($title_pretty_url = false) { 
		if($title_pretty_url){
			$this->seriesView($title_pretty_url);
		}else{
			$this->redirect("/search");
		}
	}
	
	public function seriesView($title_pretty_url = false){
		$series = CupContentSeries::fetchByPrettyUrl($title_pretty_url);
		
		$headerCode = "";
		$bodyCode = "";
		if(in_array($series->seriesID, array(335))){
			$headerCode = <<<EOF

<script type="text/javascript">
var fb_param = {};
fb_param.pixel_id = '6010305185593';
fb_param.value = '0.00';
fb_param.currency = 'AUD';
(function(){
  var fpw = document.createElement('script');
  fpw.async = true;
  fpw.src = '//connect.facebook.net/en_US/fp.js <http://connect.facebook.net/en_US/fp.js> [^] ';
  var ref = document.getElementsByTagName('script')[0];
  ref.parentNode.insertBefore(fpw, ref);
})();
</script>
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/offsite_event.php?id=6010305185593&value=0&currency=AUD" [^] /></noscript>

EOF;
		
		
			$bodyCode = <<<EOF
		
<!-- Google Code for TextGuidesPurchase Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 1014117663;
var google_conversion_language = "en";
var google_conversion_format = "2";
var google_conversion_color = "ffffff";
var google_conversion_label = "1JVJCImmxgcQn-rI4wM";
var google_conversion_value = 0;
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript"
src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt=""
src="//www.googleadservices.com/pagead/conversion/1014117663/?value=0&label=1JVJCImmxgcQn-rI4wM&guid=ON&script=0"/>
</div>
</noscript>
EOF;
		}
		
		
		
		if($series->isEnabled){
			$this->addHeaderItem($headerCode);
			
			$this->set('bodyCode', $bodyCode);
			//print_r($series);
			//exit();
			$this->set('seriesObj', $series);
			$this->render('/series/series_view');
			//$this->render('/titles/title_disabled');
		}else{
			$this->redirect("/search");
		}
	}
	
}