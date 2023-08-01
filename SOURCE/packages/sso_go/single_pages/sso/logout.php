<?php
// Add set cookies below to destroy
$epub_cookie_url = COOKIE_URL_EPUB;
$itb_cookie_url = COOKIE_URL_ITB;
?>
<img src="<?php echo $epub_cookie_url; ?>" style="display:none;">
<img src="<?php echo $itb_cookie_url; ?>" style="display:none;">
<div class="container" id="build-poster">
    <div class="row text-center">
        <div class="col-lg-12 resources-container">
            <div class="">
                <p><span class="glyphicon glyphicon-refresh" id="process-icon"></span></p>
                <a href="<?php echo $requestUrl ?>" id="requestUrl"></a>
                <br />
                <br />
            </div>
        </div>
    </div>
</div>