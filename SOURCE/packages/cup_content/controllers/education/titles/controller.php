<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::model('title/model', 'cup_content');
Loader::model('cup_content_search', 'cup_content');
Loader::helper('tools', 'cup_content');

class TitlesController extends Controller {

	public function on_start(){
		CupContentToolsHelper::initialLocate();
		
		$this->error = Loader::helper('validation/error');
		$this->addHeaderItem(Loader::helper('html')->css('ccm.profile.css'));
		
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
	}

	public function view($title_pretty_url = false) {
		if($title_pretty_url){
			$this->titleView($title_pretty_url);
		}else{
			$this->redirect("/search");
		}
	}
	
	public function titleView($title_pretty_url = false){
		// Nick Ingarsia, 29/5/14
		// Wrapped urldecode() around the URL, some title strings were causing errors
		$title = CupContentTitle::fetchByPrettyUrl(urldecode($title_pretty_url));
		
		$headerCode = "";
		$bodyCode = "";
		
		if($title->isEnabled){
			$series = $title->getSeriesObject();
			if($series && in_array($series->seriesID, array(335))){
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
			
			$this->addHeaderItem($headerCode);
			$this->set('bodyCode', $bodyCode);
		
			$this->set('pageTitle', $title->getDisplayName());
			
			$this->set('titleObj', $title);
			$this->render('/titles/title_view');
			//$this->render('/titles/title_disabled');
		}else{
			$this->redirect("/search");
		}
	}
	
	
	public function downloadSamplePage($isbn, $sample_id, $requested_filename){
		Loader::model('title_sample_page/model', 'cup_content');
		$sample_page = new CupContentTitleSamplePage($sample_id);
		
		$filePath = $sample_page->getFilePath();
		
		$fileName = $sample_page->filename;
		$fileMeta = $sample_page->filemeta;

        /*
		if(strcmp($requested_filename, $fileName) != 0){
			echo "File not available";
		}else
        */
        if(!$filePath){
			echo "File not available";
		}else{
			header('Content-type: '.$fileMeta);
			header('Content-Disposition: attachment; filename="'.$fileName.'"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            ob_clean();
            flush();
			readfile($filePath);
		}
		exit();
	}
}
