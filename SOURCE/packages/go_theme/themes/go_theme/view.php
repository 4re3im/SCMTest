<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
?>
<?php $this->inc('elements/header.php'); ?>

<!-- Marketing messaging and featurettes
================================================== -->
<!-- Wrap the rest of the page in another container to center all the content. -->
<div id="main-content">
    <?php //SB-47 added by machua 20190130 as the old implementation is modified
    $u = new User();
    if (isset($_COOKIE["AuthToken"]) && !$u->isLoggedIn()) {
        // Add set cookies below to destroy
        $epub_cookie_url = COOKIE_URL_EPUB;
        $itb_cookie_url = COOKIE_URL_ITB;
        ?>
        <img src="<?php echo $epub_cookie_url; ?>" style="display:none;">
        <img src="<?php echo $itb_cookie_url; ?>" style="display:none;">
    <?php } ?>

    <div>
        <div class="row">
            <div class="col-lg-12">
                <?php print $innerContent; ?>
                <?php
                $a = new Area('GoMain');
                $a->display($c);

                if (filter_input(INPUT_SERVER, 'REQUEST_URI') === '/go/') {
                    $this->addHeaderItem('<link rel="stylesheet" type="text/css" href="' . (string)$html->css(
                            'main.css',
                            'go_contents'
                        )->href . '?v=1.15"></link>');
                }
                ?>
            </div>
        </div>
    </div>
</div>
<!--ANZGO-3870 modified by jdchavez 10/09/18-->
<?php $this->inc('elements/footer.php'); ?>
