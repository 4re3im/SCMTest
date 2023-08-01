<?php 
	$ch = Loader::helper('cup_content_html', 'cup_content'); 
	$cu = Loader::helper('concrete/urls');
?>

<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
<link href='https://fonts.googleapis.com/css?family=PT+Sans:400,700,400italic,700italic|Source+Sans+Pro:200,700' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="<?php print $this->getStyleSheet('css/main.css'); ?>" />
<link rel="stylesheet" type="text/css" href="<?php print $this->getStyleSheet('css/typography.css'); ?>" />
<link rel="stylesheet" type="text/css" href="<?php print $this->getStyleSheet('css/global.css'); ?>" />
<link rel="stylesheet" type="text/css" href="<?php print $this->getStyleSheet('css/content_style.css'); ?>" />
<link rel="stylesheet" type="text/css" href="<?php print $this->getStyleSheet('css/cup_global_nav.css'); ?>" />
<link rel="stylesheet" type="text/css" href="<?php print $this->getStyleSheet('css/bootstrap.min.css'); ?>" />
<link rel="stylesheet" type="text/css" href="<?php print $this->getStyleSheet('css/ccm.app.css'); ?>" />


<!--[if lt IE 9]>
	<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL.DIR_REL.'/themes/cup/css/PT_Sans/stylesheet.css';?>" />
<![endif]-->

<style>
@font-face {
    font-family: 'netto_otregular';
    src: url('ufonts.com_netto-ot-webfont.eot');
    src: url('<?php echo BASE_URL.DIR_REL.'/themes/cup/css/webfontkit/ufonts.com_netto-ot-webfont.eot';?>?#iefix') format('embedded-opentype'),
         url('<?php echo BASE_URL.DIR_REL.'/themes/cup/css/webfontkit/ufonts.com_netto-ot-webfont.woff';?>') format('woff'),
         url('<?php echo BASE_URL.DIR_REL.'/themes/cup/css/webfontkit/ufonts.com_netto-ot-webfont.ttf';?>') format('truetype'),
         url('<?php echo BASE_URL.DIR_REL.'/themes/cup/css/webfontkit/ufonts.com_netto-ot-webfont.svg';?>#netto_otregular') format('svg');
    font-weight: normal;
    font-style: normal;

}
</style>
<?php Loader::element('header_required', array('pageTitle' => $pageTitle)); ?>
<script src="<?php echo $this->getThemePath();?>/js/gae_helper.js"></script>
<script>
	<?php 
		$current_locale = $ch->getCurrentLocate('en_AU'); 
		$locale_change_url = $cu->getToolsURL('locale', 'cup_content');
	?>
	jQuery(document).ready(function(){
		var locate_select = jQuery('.frame_header .frame_content .btn_location #global_current_locate');
		var selected_locate = "<?php echo $current_locale;?>";
		var locale_change_url = "<?php echo $locale_change_url;?>";
		locate_select.find('option#loading').hide();
		locate_select.val(selected_locate);
		
		
		locate_select.change(function(){
			//alert(selected_locate);
			jQuery.get(
				locale_change_url,
				{locale: locate_select.val()},
				function(data){
					/*window.location.href=window.location.href;*/
					//cache: false,
					window.location.reload();
					//window.location.reload(true);
				}
			);
		});
		
		
		
		
		var scrollToTop_div = jQuery('<div></div>');
		scrollToTop_div.addClass("global_scroll_to_top");
		//scrollToTop_div.css({width:'30px',height:'30px',background:'#FF0000'});
		scrollToTop_div.css({position:'fixed',bottom:'5px',right:'5px',cursor:'pointer'});
		scrollToTop_div.hide();
		
		function detectToShowScrollBtn(){
			if(jQuery(window).scrollTop() > 300) { 
				scrollToTop_div.fadeIn(); 
			}

			if(jQuery(window).scrollTop() < 300) { 
				scrollToTop_div.fadeOut(); 
			} 
		}
		
		jQuery('body').append(scrollToTop_div);
		jQuery(window).on('scroll', detectToShowScrollBtn);
		
		detectToShowScrollBtn();
		
		scrollToTop_div.click(function(){
			 jQuery('html, body').animate({scrollTop:0}, 'slow', detectToShowScrollBtn);
		});
	});
	
</script>