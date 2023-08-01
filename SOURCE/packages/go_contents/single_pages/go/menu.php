<?php
defined('C5_EXECUTE') or die(_("Access Denied"));
?>
<div class="container-fluid" style="border-bottom: 1px solid #CCCCCC">
    <div class="row text-center">
        <div class="col-lg-4 col-lg-offset-4 col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2">
            <input type="text" class="form-control go-input go-search" style="border-radius: 16px;" placeholder="Search" id="search-bar" />
        </div>
    </div>
    <br />
    <br />
</div>
<br />
<br />
<div class="container">
     <div class="row text-center">
        <div class="col-lg-6 col-lg-offset-3 col-md-10 col-md-offset-1 col-sm-12 col-xs-12 menu-overlay">
            <a href="/go">
                <svg class="svg-mid">
                <use xlink:href="#icon-home"></use>
                </svg>
                <span class="svg-text-mid">Home</span>
            </a>
            <!--ANZGO-3881 modified by mtanada 20181017-->
            <a href="https://cambridgehelp.zendesk.com">
                <svg class="svg-mid">
                <use xlink:href="#icon-support"></use>
                </svg>
                <span class="svg-text-mid">Support</span>
            </a>
            <a href="https://www.cambridge.edu.au/education/">
                <svg class="svg-mid">
                <use xlink:href="#icon-store"></use>
                </svg>
                <span class="svg-text-mid">Store</span>
            </a>
            <!-- ANZGO-3919 modified by mtanada 20181120 -->
            <a href="<?php echo $this->url('/go/contact/'); ?>">
                <svg class="svg-mid">
                <use xlink:href="#icon-contact"></use>
                </svg>
                <span class="svg-text-mid">Contact Us</span>
            </a>
        </div>
    </div>
</div>