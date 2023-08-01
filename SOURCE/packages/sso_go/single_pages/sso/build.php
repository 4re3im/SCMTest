<?php
if (isset($_COOKIE['ccmUserHash']) && $_COOKIE['ccmUserHash']) {
    $cookie = $_COOKIE['ccmUserHash'];
}
// Add set cookies below
$epub_cookie_url = COOKIE_URL_EPUB."?c=".$cookie;
$itb_cookie_url = COOKIE_URL_ITB."?c=".$cookie;
$th_cookie_url = COOKIE_URL_TH."?c=".$cookie; // ANZGO-3456 added by james bernardez 20170802
$ths_cookie_url = COOKIE_URL_THS."?c=".$cookie; // ANZGO-3456 added by james bernardez 20170802 set both https and http
?>
<img src="<?php echo $epub_cookie_url; ?>" style="display:none;">
<img src="<?php echo $itb_cookie_url; ?>" style="display:none;">
<img src="<?php echo $th_cookie_url; ?>" style="display:none;"> <!-- ANZGO-3456 added by james bernardez 20170802 -->
<img src="<?php echo $ths_cookie_url; ?>" style="display:none;"> <!-- ANZGO-3456 added by james bernardez 20170802 -->
<!-- SB-102 added by mabrigos 20190321 moved header spacer to specific pages -->
<div class="header-spacer">
    &nbsp;
</div>
<div class="container" id="build-poster">
    <div class="row text-center">
        <div class="col-lg-12 resources-container">
            <div>
                <p><span class="glyphicon glyphicon-refresh" id="process-icon"></span></p>
                <a href="<?php echo $requestUrl ?>" id="requestUrl"></a>
                <br />
                <br />
            </div>
        </div>
    </div>
</div>