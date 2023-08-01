<?php

/**
 * HTML Head for Education Theme
 */

$ch = Loader::helper('cup_content_html', 'cup_content');
$cu = Loader::helper('concrete/urls');

?>

<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
<link href='https://fonts.googleapis.com/css?family=PT+Sans:400,700,400italic,700italic|Source+Sans+Pro:200,700'
      rel='stylesheet'
      type='text/css'>
<link rel="stylesheet" type="text/css" href="<?php print $this->getStyleSheet('css/main.css'); ?>" />
<link rel="stylesheet" type="text/css" href="<?php print $this->getStyleSheet('css/typography.css'); ?>" />
<link rel="stylesheet" type="text/css" href="<?php print $this->getStyleSheet('css/global.css'); ?>" />
<link rel="stylesheet" type="text/css" href="<?php print $this->getStyleSheet('css/content_style.css'); ?>" />
<link rel="stylesheet" type="text/css" href="<?php print $this->getStyleSheet('css/cup_global_nav.css'); ?>" />

<!--[if lt IE 9]>
<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL.DIR_REL.'/themes/cup/css/PT_Sans/stylesheet.css';?>" />
<![endif]-->

<style>
@font-face {
    font-family: 'netto_otregular';
    src: url('ufonts.com_netto-ot-webfont.eot');
    src:
        url('<?php echo BASE_URL.DIR_REL.'/themes/cup/css/webfontkit/ufonts.com_netto-ot-webfont.eot';?>?#iefix')
        format('embedded-opentype'),
        url('<?php echo BASE_URL.DIR_REL.'/themes/cup/css/webfontkit/ufonts.com_netto-ot-webfont.woff';?>')
        format('woff'),
        url('<?php echo BASE_URL.DIR_REL.'/themes/cup/css/webfontkit/ufonts.com_netto-ot-webfont.ttf';?>')
        format('truetype'),
        url('<?php echo BASE_URL.DIR_REL.'/themes/cup/css/webfontkit/ufonts.com_netto-ot-webfont.svg';?>#netto_otregular')
        format('svg');
    font-weight: normal;
    font-style: normal;

}
</style>
<?php

    Loader::element('header_required', array('pageTitle' => $pageTitle));

    if (!isset($_SESSION['locale_change'])) {
        $countryCode = $_SERVER["HTTP_CF_IPCOUNTRY"];
        if (empty($countryCode) || $countryCode == 'AU') {
            $currentLocation = 'en_AU';
        } elseif ($countryCode == 'NZ') {
            $currentLocation = 'en_NZ';
        } else {
            $currentLocation = $countryCode;
        }
        $_SESSION['DEFAULT_LOCALE'] = $currentLocation;
    }
    $localeChangeURL = $cu->getToolsURL('locale', 'cup_content');
?>

<script>
    jQuery(document).ready(function(){
        var locate_select = jQuery('.frame_header .frame_content .btn_location #global_current_locate');
        var locale_change_url = "<?php echo $localeChangeURL;?>";

        locate_select.find('option#loading').hide();
        locate_select.change(function(){
            jQuery.get(
                locale_change_url,
                {locale: locate_select.val()},
                function(data){
                    window.location.reload();
                }
            );
        });

        var scrollToTop_div = jQuery('<div></div>');
        scrollToTop_div.addClass("global_scroll_to_top");
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

<!--[if IE 8]>
<style>
    .cup-main-sidebar .guide-title {
        font-family: 'PT Sans',sans-serif;
        font-size: 20px;
        letter-spacing: -1px;
    }
</style>
<![endif]-->
