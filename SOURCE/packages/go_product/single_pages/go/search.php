<?php
/*
 *
 * @author pbalila
 */
defined('C5_EXECUTE') or die(_("Access Denied"));
// ANZGO-3727 Modified by Maryjes Tanada 2018-06-05 Referrer-Policy
Header('Referrer-Policy: no-referrer');
// SB-102 added by mabrigos 2019/03/20 Reinstated breadcrumbs
global $u;
$ug = $u->getUserGroups();
?>
<!--ANZGO-3889 modified by mtanada 20181025-->
<div class="container-fluid container-bg-2 resources-container search-content" id="go-search-container">
    <!-- SB-102 added by mabrigos 2019/03/20 Reinstated breadcrumbs -->
    <div id="breadcrumbs-wrapper">
        <div class="container" style="margin-left: 20px !important;">
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
                            <li><a class="active">Search</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-10
                    col-xs-offset-1">
                        <input type="text" class="form-control go-input go-search" id="go-search" placeholder="Search"
                               search-url="<?php echo $this->url('/go/search/searchByKeyWord'); ?>" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br />
    <div class="row container-bg-1 row-list row-loader" style="display:none;">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="text-center load"></div>
        </div>
    </div>
    <div id="search-result"></div>
    <br />
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="container">
                <div class="row text-center">
                    <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-10
                    col-xs-offset-1">
                        <p>DIGITAL RESOURCES BY SUBJECT</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br />
</div>
<br />

<div class="container subjects">
    <div class="row text-center">
        <div class="col-lg-10 col-lg-offset-1 col-md-10 col-md-offset-1 col-sm-4 col-sm-offset-4 col-xs-4
        col-xs-offset-4">
            <div class="row text-center">
                <?php echo $subjects; ?>
            </div>
        </div>
    </div>
</div>
<br />
<br />
<br />
<br />
<div class="container">
    <div class="row text-center">
        <div class="col-lg-4 col-lg-offset-4">
            <p>Can't find what you're looking for?</p>
            <p>Visit our online <a href="https://cambridge.edu.au/education/" rel="noreferrer">store</a>.</p>
        </div>
    </div>
</div>
