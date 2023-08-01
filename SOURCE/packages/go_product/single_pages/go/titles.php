<?php
/**
 * Product Display
 * @author Ariel Tabag <atabag@cambridge.org>
 * March 25, 2015
 */
defined('C5_EXECUTE') || die(_("Access Denied"));

// Added by Paul Balila for ANZGO-2647, 2016-08-24
$v = View::getInstance();

// ANZGO-3792 added by jbernardez 20180710
$activateMode = Config::get('ACTIVATE_MODE');

// ANZGO-3930 added by jbernardez 20181112
global $u;
$ug = $u->getUserGroups();
$frontAjaxBtn = 'front-ajax-btn';
?>
<!-- SB-102 added by mabrigos 2019/03/20 Reinstated breadcrumbs -->
<div id="breadcrumbs-wrapper">
    <div class="container" style="margin-left: 35px !important;">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumbs">
                    <ol>
                        <?php if ($u->isLoggedIn()) { ?>
                            <?php if ($ug[3] == "Administrators") { ?>
                                <li><a href="/go/">Home</a></li>
                            <?php } ?>
                            <?php if ($_SESSION['visitedMyResources']) { ?>
                                <li><a href="/go/myresources/">My resources</a></li>
                            <?php } ?>
                        <?php } else { ?>
                            <li><a href="/go/">Home</a></li>
                        <?php } ?>
                        <li><a href="<?php echo $breadcrumb['subject']['url']; ?>"><?php echo $breadcrumb['subject']['title'];?></a></li>
                        <?php if (strlen($breadcrumb['series']['title']) > 1) { ?> 
                            <li><a href="<?php echo $breadcrumb['series']['url']; ?>"><?php echo $breadcrumb['series']['title']; ?></a></li>
                        <?php } ?>
                        <li><a class="active"><?php echo CommonHelper::formatProductDisplayName($title_details['name']); ?></a></li>
                    </ol>
                </div>  
            </div>
        </div>
    </div>
</div>
<script> var titlesURL = "<?php echo $this->url('/go/titles'); ?>"</script>
<script> var triggerActivate = "<?php echo $triggerActivate ?>"</script>
<div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-12"
    style="margin-bottom:20px; margin-top:-10px;">
    <!-- ANZGO-3691 added by jbernardez 20180417 -->
    <?php if ($noISBN) { ?>
    <div class="tip-box">
        <p style="text-align: center; padding-bottom: 20px;">
            <br />
            This product is currently unavailable.
            Contact your Customer service 1800 005 210 or your
            <code><a href="/education/about/contact-us/" target="_blank">Education Resource Consultant</a></code>.
        </p>
    </div>
    <?php } else { ?>
    <div class="col-lg-4 col-md-3 col-sm-4">
        <div class="product-thumb-wrap">
            <div class="book-wrap">
                <div class="cover">
                    <?php //temporary image source only, waiting for amazon bucket ?>
                    <img src="<?php echo DIR_REL . '/files/cup_content/images/titles/' . $title_details['isbn13']; ?>">
                </div>
                <div style="border-color: #5c5959;" class="undercover"></div>
            </div>
            <div class="product-btn-wrap row">
                <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                    <?php // ANZGO-3665 Modified by Shane Camus ?>
                    <?php if ($title_details['showBuyNow'] && !$hasSubscription) {?>
                    <a id="toggle-activate"
                       class="btn btn-lg btn-success btn-block"
                       type="button" href='<?php echo $buy_now_link; ?>'
                       target="_blank">
                        <span>BUY NOW</span>
                    </a>
                    <?php } ?>
                    <?php if (!$hasSubscription) {?>
                        <?php
                        // ANZGO-3789 added by jbernardez 20180706
                        if (!$activateMode) {
                            // ANZGO-3930 added by jbernardez 20181112
                            if (!$u->isLoggedIn()) {
                                $frontAjaxBtn = '';
                            }
                        ?>
                    <a class="btn btn-lg btn-info btn-block"
                       href="<?php echo BASE_URL . $this->url('/go/activate'); ?>" >ACTIVATE</a>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
   <!--ANZGO-3920 modified by mtanada 20190103 remove front-ajax-btn class when redirecting to /login-->
    <div class="col-lg-8 col-md-9 col-sm-8">
        <h2 class="tablet-show"><?php echo CommonHelper::formatProductDisplayName($title_details['name']); ?></h2>
        <?php if (!$user_id) { ?>
        <div class="row text-center">
            <div class="col-lg-12">
                <div class="color-box space">
                    <div class="shadow">
                        <div title="Useful Tips" class="info-tab tip-icon">&nbsp;</div>
                        <div class="tip-box">
                            <p>
                                To access your resources
                                <code>
                                    <a href='<?php echo BASE_URL . $this->url('/go/login'); ?>' >
                                        Log in
                                    </a>
                                </code> or
                                <code>
                                    <a href='<?php echo BASE_URL . $this->url('/go/login'); ?>' >
                                        create your Cambridge GO account</a>
                                </code>
                                Activate your resources by entering the access code found in the front of your print
                                textbook, sealed pocket or supplied via email.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <?php } ?>
        <?php echo $contents; ?>
        <div class="row">
            <div class="col-lg-12 col-md-10 col-sm-12 col-xs-12">
                <div class="authors">
                    <h3>Authors</h3>
                    <p>
                        <?php
                        echo $title_details['author'];
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
