<?php

/**
 * Activate Single Page
 */

defined('C5_EXECUTE') || die("Access Denied");
// ANZGO-3727 Modified by Maryjes Tanada 2018-06-05 Referrer-Policy
Header('Referrer-Policy: no-referrer');

//ANZGO-3760 mtanada 20180718
if (isset($_POST['newReactivationCode'])) {
   $newCode = explode('-', $_POST['newReactivationCode']);
   $_SESSION['form-accesscode_s1'] = $newCode[0];
   $_SESSION['form-accesscode_s2'] = $newCode[1];
   $_SESSION['form-accesscode_s3'] = $newCode[2];
   $_SESSION['form-accesscode_s4'] = $newCode[3];

   // ANZGO-3854 added by jbernardez 20180913
   $printAccessCode = $_POST['printAccessCode'];
}

$v = View::getInstance();

// ANZGO-3789 added by jbernardez 20180706
// modified 20180710
$announcementMode = Config::get('ANNOUNCEMENT_MODE');
$activateMode = Config::get('ACTIVATE_MODE');
// ANZGO-3789 modified by jbernardez 20180710
$stopBanner = $_SESSION['stopBanner'];
// ANZGO-3800 added by jdchavez 07/17/2018
$bannerMessage = Config::get('BANNER_MESSAGE');
?>

<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5KPZ5P"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

    <div class="page-wrap">
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="/activate">
                        <img alt="Cambridge" src="/packages/go_theme/elements/svgs/activate_logo.svg">
                    </a>
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                            data-target="#main-nav" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                </div>

                <div class="collapse navbar-collapse" id="main-nav">
                    <!-- ANZGO-2851 -->
                    <a id="activate-login" class="front-ajax-btn" href="<?php echo $v->url('/go/login/'); ?>">
                        <span class="svg-text">&nbsp;</span></a>
                    <ul class="nav navbar-nav navbar-right">
                        <!-- ANZGO-3564 Added by John Renzo S. Sunico, 11/23/2017 -->
                        <!-- ANZGO-3682 Added by Maryjes Tanada, 04/18/2018 added /gettingstarted link -->
                        <li>
                            <a class="hvr-underline-from-center hvr-bounce-to-top" href="/gettingstarted/">
                                Getting Started
                            </a>
                        </li>
                        <li>
                            <a class="hvr-underline-from-center hvr-bounce-to-top" href="/codecheck/">
                                Code Check
                            </a>
                        </li>
                        <li>
                            <!--ANZGO-3881 modified by mtanada 20181017-->
                            <a class="hvr-underline-from-center hvr-bounce-to-top"
                               href="https://cambridgehelp.zendesk.com" target="_blank">
                                Support
                            </a>
                        </li>
                        <li>
                            <!--ANZGO-3919 modified by mtanada 20181120-->
                            <a class="hvr-underline-from-center hvr-bounce-to-top" href="../go/contact/">
                                Contact Us
                            </a>
                        </li>
                        <li>
                            <a class="hvr-underline-from-center hvr-bounce-to-top" href="../go">
                                Cambridge GO
                            </a>
                        </li>
                        <li>
                            <a class="hvr-underline-from-center hvr-bounce-to-top"
                               href="https://www.cambridge.edu.au/education/">
                                Store
                            </a>
                        </li>
                        <!-- ANZGO-3755 Added by Shane Camus 06/14/18 -->
                        <?php if (isset($name) && $name !== '') { ?>
                        <li> 
                            <a id="header-user" href="../go/myresources/">
                                <?php echo $name; ?>
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </nav>

        <div id="main" class="container-fluid">
            <div class="container">

                <h1 class="animated fadeInLeft">Enter your access code</h1>
                <div class="row">

                <div id="activated">
                    <img src='/packages/go_theme/elements/svgs/checkmark-circle.svg' alt='checkmark'>
                </div>

                <div id="newactivateform">
                    <form class="form-inline activate col-md-6 col-md-offset-3 activate-form"
                        name="activate-access-code-form"
                        id="activate-access-code-form"
                        enctype="multipart/form-data"
                        action=""
                        method="post">
                        <div class="form-group animated bounceInDown">
                            <label for="First" class="sr-only">First</label>
                            <input type="text" name="accesscode_s1dummy[1]"
                                   id="accesscode_s1dummy"
                                   class="form-control accesscodetext"
                                   value="<?php echo $_SESSION['form-accesscode_s1'];?>"
                                   maxlength="4"
                                   placeholder="* * * *" />
                        </div>
                        <div class="form-group animated bounceInDown">
                            <label for="Second" class="sr-only">Second</label>
                            <input type="text" name="accesscode_s1dummy[2]"
                                   id="accesscode_s2dummy"
                                   class="form-control accesscodetext"
                                   value="<?php echo $_SESSION['form-accesscode_s2'];?>"
                                   maxlength="4"
                                   placeholder="* * * *" />
                        </div>
                        <div class="form-group animated bounceInDown">
                            <label for="Third" class="sr-only">Third</label>
                            <input type="text" name="accesscode_s1dummy[3]"
                                   id="accesscode_s3dummy"
                                   class="form-control accesscodetext"
                                   value="<?php echo $_SESSION['form-accesscode_s3'];?>"
                                   maxlength="4" placeholder="* * * *" />
                        </div>
                        <div class="form-group animated bounceInDown">
                            <label for="Fourth" class="sr-only">Fourth</label>
                            <input type="text" name="accesscode_s1dummy[4]"
                                   id="accesscode_s4dummy"
                                   class="form-control accesscodetext"
                                   value="<?php echo $_SESSION['form-accesscode_s4'];?>"
                                   maxlength="4"
                                   placeholder="* * * *" />
                        </div>
                        <?php // SB-254 modified by jbernardez 20190912 ?>
                        <br /id="magicBR">
                        <div class="form-group animated bounceInDown">
                            <!-- ANZGO-3759 added by mtanada 20180703 -->
                            <span id="accesscode-refresh"
                                  class="glyphicon glyphicon-refresh"
                                  data-toggle="tooltip"
                                  title="Activate Another Code">
                            </span>
                        </div>
                        <!-- ANZGO-2851 -->
                        <?php if ($error_message != '') { ?>
                        <div id="error-message" style="display: block;" role="alert">
                            <div class="alert alert-danger animated bounce alert-dismissible" role="alert">
                            <?php echo $error_message;?>
                            </div>
                        </div>
                        <?php } else { ?>
                        <div id="error-message"></div>
                        <?php } ?>
                        <p class="instructions animated fadeInRight">
                            Enter the access code provided in the front of your printed textbook, the sealed pocket or
                            the email supplied on purchase.
                        </p>

                        <div class="checkbox animated fadeInRight">
                            <label>
                                <input type="checkbox" name="acceptance" value="accept"  id="accept">
                                I accept and agree to the <a href="../go/terms">terms of use</a>.
                            </label>
                        </div>

                        <p class="animated bounceInUp long">
                            <!-- ANZGO-3854 added by jbernardez 20180913 -->
                            <?php if (isset($printAccessCode)) { ?>
                            <input type="hidden" name="printAccessCode" id="printAccessCode" value="<?php echo $printAccessCode; ?>">
                            <?php } ?>
                            <?php
                            // ANZGO-3789 added by jbernardez 20180706
                            if ($activateMode) {
                            ?>
                            <a href="#" id="front-deactivate" class="btn gray">Activate</a>
                            <?php } else { ?>
                            <a href="#" id="dummyActivator" class="btn btn-default green activate">Activate</a>
                            <?php } ?>
                        </p>
                    </form>
                </div>
               <!-- newactivateform -->
               <!--  ANZGO-3759 added by mtanada 20180716  -->
               <form action="/education/cart/update" name="cartForm" id="cartForm" method="POST"></form>
               <div class="row">
                   <div class="col-md-12">
                       <p class="activated">
                           Already activated your product?<br />
                           <a href="#" data-toggle="modal" data-target="#logos">Login now</a>
                       </p>
                   </div>
                </div>
                </div>
            </div>
        </div>
    </div>
    <!-- page-wrap -->

    <footer class="footer">
        <div class="footer-container">
            <div class="container-fluid footer-logo">
                <ul>
                    <li>
                        <img class="activate-img-icon" src="/packages/go_theme/elements/img/hotmaths-grey.png" width="208">
                    </li>
                    <li>
                        <a class="bw-powered icon" href="#">Powered by Cambridge Hotmaths</a>
                    </li>
                    <li>
                        <a class="bw-go icon" href="#">Cambridge GO</a>
                    </li>
                    <li>
                        <a class="bw-dscience icon" href="#">Dynamic Science</a>
                    </li>
                    <li>
                        <a class="bw-denglish icon" href="#">Dynamic English</a>
                    </li>
                </ul>
            </div>

            <div class="container-fluid footer-links">
                <ul>
                    <?php // SB-202 modified by machua 20190611 automatically update the value of year ?>
                    <li><a href="#"><?php echo date("Y") ?> Cambridge University Press</a></li>
                    <li><a href="#">ABN 28 508 204 178</a></li>
                    <li><a href="#">Privacy Statement</a></li>
                    <li><a href="#">Terms of Use</a></li>
                </ul>
            </div>
        </div>
    </footer>

    <?php // SB-202 modified by machua 20190611 new layout for activate popup ?>
    <div class="modal fade" id="logos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Already activated your product?</h3>
                    <p>LOGIN NOW by clicking on a link below</p>
                    <div class="col-md-10 col-md-offset-1">
                            <p class="subtitle">To login to your resources, visit Cambridge GO:</p>
                            <ul class="logos">
                                <li>
                                    <a class="go icon" href="http://www.cambridge.edu.au/go">
                                        Cambridge GO
                                    </a>
                                </li>
                        </ul>
                    </div>
                    <div class="col-md-10 col-md-offset-1">
                        <span class="faint">- or -</span>
                    </div>
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1">
                            <p class="subtitle">Choose below for Maths products:</p>
                            <ul class="logos">
                                <li><a class="hvr-grow" href="http://www.hotmaths.com.au">
                                        <img src="/packages/go_theme/elements/img/hotmaths-colored.png" width="300">
                                    </a>
                                </li>
                                <li><a class="senior icon hvr-grow" href="http://www.cambridge.edu.au/seniormaths">
                                        Senior Maths
                                    </a>
                                </li>
                                <li><a class="hvr-grow" href="https://emac.hotmaths.com.au/">
                                        <img src="/packages/go_theme/elements/img/essentialmaths-solid.png" width="250">
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
// ANZGO-3789 added by jbernardez 20180706
if ($announcementMode) {
    // ANZGO-3789 modified by jbernardez 20180710
    $display = $stopBanner ? "none" : "block";
?>
<!-- ANZGO-3789 added by jbernardez 20180706 -->
<div id="loadTeamp" style="display: none;"></div>
<div class="navbar-inverse announce-info" style="display: <?php echo $display; ?>;">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <br/>
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10" id="cookie-info-warning">
                            <h1 style="color:white;">
                                <svg id="cookie-info-warning-sign">
                                    <use xlink:href="#icon-cookies"></use>
                                </svg>
                                <span class="svg-text" id="announce-info-warning-text">Cambridge GO Announcement</span>
                            </h1>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-2" id="announce-info-dismiss-div">
                            <a href="#" id="dismiss-announce" class="pull-right" style="color:white;">
                                <span class="svg-text" id="announce-info-dismiss-text">Dismiss</span>
                                <svg style="margin-left: 2px;" id="dismiss-cookie-svg">
                                    <use xlink:href="#icon-dismiss"></use>
                                </svg>
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <p style="color:white;">
                                <!-- ANZGO-3800 added by jdchavez 07/17/2018 -->
                                <?php echo $bannerMessage; ?>
                            </p>
                            <p style="color:white;">
                                The Cambridge GO team.
                            </p>
                        </div>
                    </div>
                </div>
                <br/>
            </div>
        </div>
    </div>
</div>
<?php } ?>