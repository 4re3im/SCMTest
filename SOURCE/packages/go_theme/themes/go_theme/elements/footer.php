<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
$html = Loader::helper('html');

// ANZGO-3789 added by jbernardez 20180706
$announcementMode = Config::get('ANNOUNCEMENT_MODE');
// ANZGO-3789 modified by jbernardez 20180710
$stopBanner = $_SESSION['stopBanner'];
// ANZGO-3800 added by jdchavez 07/17/2018
$bannerMessage = Config::get('BANNER_MESSAGE');
?>
<!-- General use modal -->
<div class="modal fade" id="generalModal" style="vertical-align: middle;"
     tabindex="-1" role="dialog"
     aria-labelledby="generalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" id="generalClose"
                        data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">&nbsp;</h4>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>

<!-- Marketing Modal -->
<div class="modal fade" id="mktgModal" style="vertical-align: middle;"
     tabindex="-1" role="dialog"
     aria-labelledby="mktgModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" id="helpClose"
                        data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="mktgModalLabel">&nbsp;</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                        <div href="" id="marketing-img-container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- User help Modal -->
<div class="modal fade" id="helpModal" tabindex="-1" role="dialog"
     aria-labelledby="userguideModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="vertical-align: middle; width:100%">
            <div class="modal-header">
                <button type="button" class="close" id="helpClose"
                        data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="userguideModalLabel">&nbsp;</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center"
                         id="myresources-help-div">
                        <!-- ANZGO- 3471 Modified by John Renzo Sunico -->
                        <img src="" id="helpImg"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <label>
                    <input type="checkbox" id="hideHelpPopupFlag"> Do not show
                    this again.
                </label>
            </div>
        </div>
    </div>
</div>

<!-- Download Notification Modal -->
<!-- ANZGO-3872 Added by Shane Camus 10/05/18 -->
<div id="notification-wrapper" style="display: none">
    <div class="notification">
        <p>Your download has begun. Your files will save to your downloads
            folder or desktop.</p>
        <button class="close-btn"><img
                    src="/packages/go_theme/elements/img/close-icon.png"
                    alt="Close"></button>
    </div>
</div>

<!-- My Resouces Pending HM notification -->
<div id="pending-hm-wrapper" style="display: none">
    <div class="pending-notification">
        <p>No HM pending</p>
        <button class="close-btn"><img
                    src="/packages/go_theme/elements/img/close-icon.png"
                    alt="Close"></button>
    </div>
</div>

<!--ANZGO-3870 new footer added by jdchavez 10/04/18-->
<div id="main-footer">
    <div id="footer-wrapper" class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4 col-md-push-6">
                    <div class="footer-navigation">
                        <ul>
                            <li>
                                <a href="<?php echo $this->url('/go/privacy'); ?>"
                                   tabindex="0">Cookie Policy</a></li>
                            <li>
                                <a href="<?php echo $this->url('/go/privacy'); ?>"
                                   tabindex="0">Privacy Policy</a></li>
                            <li><a href="<?php echo $this->url('/go/terms'); ?>"
                                   tabindex="0">Terms of Use</a></li>
                        </ul>
                        <!-- ANZGO-3919 modified by mtanada 20181120 -->
                        <button class="contact-us-btn btn btn-lg"
                                onclick="location.href='https://www.cambridge.edu.au/servicedesk'">
                            Contact Us
                        </button>
                    </div>

                </div>
                <div class="col-md-2 col-md-push-6">
                    <div class="smi-wrapper">
                        <a href="https://www.facebook.com/CambridgeUniversityPressEducationAustralia?ref=hl"
                           class="smi fb" target="_blank" title="Facebook"
                           tabindex="0">
                            <img src="/packages/go_theme/elements/img/fb.png"
                                 alt="Facebook icon"></a>
                        <a href="https://www.youtube.com/user/CUPANZEducation"
                           class="smi yt" target="_blank" title="Youtube"
                           tabindex="0">
                            <img src="/packages/go_theme/elements/img/youtube.png"
                                 alt="Youtube icon"></a>
                        <a href="https://twitter.com/cambridge_aused"
                           class="smi tw" target="_blank" title="Twitter"
                           tabindex="0">
                            <img src="/packages/go_theme/elements/img/twitter.png"
                                 alt="Twitter icon"></a>
                    </div>
                </div>
                <div class="col-md-6 col-md-pull-6">
                    <div class="footer-copyright">
                        <p>&copy; <?php echo date("Y") ?> Cambridge University
                            Press Australia and New Zealand - ABN 28 508 204
                            178</p>
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
    <div class="navbar-inverse announce-info"
         style="display: <?php echo $display; ?>;">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <br/>
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10"
                                 id="cookie-info-warning">
                                <h1 style="color:white;">
                                    <svg id="cookie-info-warning-sign">
                                        <use xlink:href="#icon-cookies"></use>
                                    </svg>
                                    <span class="svg-text"
                                          id="announce-info-warning-text">Cambridge GO Announcement</span>
                                </h1>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-2"
                                 id="announce-info-dismiss-div">
                                <a href="#" id="dismiss-announce"
                                   class="pull-right" style="color:white;">
                                    <span class="svg-text"
                                          id="announce-info-dismiss-text">Dismiss</span>
                                    <svg style="margin-left: 2px;"
                                         id="dismiss-cookie-svg">
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

<?php
// SB-251 added by jbernardez 20190711
if (($myResourcesLogCount >= 6) && (!$isSurveyMonkey) && ($source)) {
    ?>
    <div id="loadSurveyMonkey" style="display: none;"></div>
    <div class="navbar-inverse surveymonkey-info"
         style="display: block;">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <br/>
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10"
                                 id="cookie-info-warning">
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-2"
                                 id="announce-info-dismiss-div">
                                <a href="#" id="dismiss-surveymonkey"
                                   class="pull-right" style="color:white;">
                                    <span class="svg-text"
                                          id="announce-info-dismiss-text">Dismiss</span>
                                    <svg style="margin-left: 2px;"
                                         id="dismiss-cookie-svg">
                                        <use xlink:href="#icon-dismiss"></use>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <iframe  style="width: 100%; height: 70%;" src="<?php echo $source; ?>"></iframe> 
                        </div>
                    </div>
                    <br/>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php Loader::element('footer_required'); ?>
</body>
</html>
