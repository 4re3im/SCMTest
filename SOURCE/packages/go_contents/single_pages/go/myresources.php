<?php defined('C5_EXECUTE') || die(_('Access Denied')); ?>
<!-- ANZGO-3947 Added by Shane Camus 12/10/18 -->
<?php define('GO_THEME_PATH', DIR_REL . '/packages/go_theme'); ?>
<?php echo $triggerClick; ?>

<script> var myResourceURL = "<?php echo $this->url('/go/myresources'); ?>";</script>
<!--ANZGO-3943 added by mtanada 20181210 -->
<script> var activateURL = "<?php echo $this->url('/go/activate/'); ?>";</script>
<script> var titlesURL = "<?php echo $this->url('/go/titles/'); ?>"</script>
<script> var triggerActivate = "<?php echo $displayName === ''; ?>";</script>
<script>
    // Just to make sure that the overlay is truly hidden
    showGigyaOverlay(false)
</script>

<?php
    // ANZGO-3789 added by jbernardez 20180706
    $activateMode = Config::get('ACTIVATE_MODE');

    $u = new User();
    $ui = UserInfo::getByID($u->getUserID());

    if ($u->isLoggedIn()) { ?>

    <input type="hidden" id="showHelpFlag" value="<?php echo $ui->getAttribute('showHelp'); ?>"/>
    <input type="hidden" id="firstLogin" value="<?php echo $ui->getAttribute('firstLogin'); ?>"/>

    <!--ANZGO-3630 Added by Maryjes Tanada 02/12/2018 Trigger for myresources activate--->
    <script> var hmUser_activate_url = "<?php echo $this->url('/go/activate') ?>";</script>
    <!-- ANZGO-3943 modified by mtanada 20181210 -->
    <?php $errorMsg = isset($_SESSION['userTypeMessage']) ? 1 : 0; ?>
        <script> var trigger_hmUserType_activate = "<?php echo $errorMsg; ?>";</script>
<?php } ?>

<input type="hidden" id="hideHelpSession" value="<?php echo $displayHideHelpSession; ?>" />
<div id="gigya-completion"></div>

<!-- ANZGO-3942 added by machua 20181129 New UI/UX for my resources-->
<div id="main-wrapper" class="content-wrapper">

    <div class="resources-wrapper">
        <!-- SB-102 added by mabrigos 2019/03/20 Reinstated breadcrumbs -->
        <div id="breadcrumbs-wrapper">
            <div class="container" style="margin-left: 35px !important;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="breadcrumbs">
                            <ol>
                                <?php if ($ug[3] == "Administrators") { ?>
                                    <li><a href="/go/">Home</a></li>
                                <?php } ?>
                                <li><a href="/go/myresources/" class="active" style="pointer-events: none;">My resources</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1>My resources</h1>

                    <div class="filter-wrapper">
                        <div class="input-field sort-wrapper">
                            <input type="hidden" name="sort" value="<?php echo $arrange; ?>">
                            <label for="sort" class="hide">Sort</label>
                            <select name="sort" class="sort-resources">
                                <option
                                        value="Newest - oldest"
                                        <?php echo $arrange === "Newest - oldest" ? 'selected' : ''?>>
                                    Newest - Oldest
                                </option>
                                <option
                                        value="Oldest - newest"
                                    <?php echo $arrange === "Oldest - newest" ? 'selected' : '' ?>>
                                    Oldest - Newest
                                </option>
                            </select>
                        </div>
                        <div class="input-field add-resources">
                            <!-- ANZGO-3789 added by jbernardez 20180706 -->
                            <?php if ($activateMode) { ?>
                                <a class="btn btn-primary btn-lg disabled"
                                   role="button">
                                    Add new resources
                                </a>
                            <?php } else { ?>
                                <a href="<?php echo $this->url('/go/activate/'); ?>"
                                   class="btn btn-primary btn-lg"
                                   role="button">Add new resources
                                </a>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- ANZGO-3942 added by machua 20181129 to check if there are no resources -->
                        <div class="page-notification" style="display:none;">
                            <!-- ANZGO-3942 added by machua 20181129 added blue banner if there are no resources -->
                            <!-- ANZGO-3789 added by jbernardez 20180706 -->
                            <?php if ($activateMode) { ?>
                                <p>
                                    You do not have any resources activated - click on
                                    <span style="color:gray">Add new resources</span>
                                    to enter a code or connect to a product.
                                </p>
                            <?php } else { ?>
                                <p>
                                    You do not have any resources activated - click on
                                    <a href="<?php echo $this->url('/go/activate/'); ?>">Add new resources</a>
                                    to enter a code or connect to a product.
                                </p>
                            <?php } ?>
                        </div>
                        <!-- Resources Panel -->
                        <div id="resources-panel" class="panel-group panel-group-resources">
                        </div>
                        <!-- End of Resources Panel -->
                        
                        <!-- Loading Panel -->
                        <div class="text-center loadcontainer">
                            <?php if ($totalSubscriptions > 8) { ?>
                                <a id="loadmore" class="text-center">
                                    Click or scroll to load more resources
                                </a>
                            <?php } ?>
                            <div id="more-titles" class="text-center loadNew"></div>
                        </div>

                        <!-- ANZGO-3946 Added by Shane Camus 12/10/18 -->
                        <!-- Modal Panel -->
                        <div class="modal fade panel-group-resources" id="resources-list" tabindex="-1" role="dialog"
                             aria-labelledby="resources-list" style="display:none;">
                            <div class="modal-dialog modal-dialog-centered resources-list" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true" class="close-modal">
                                                <img src="/packages/go_theme/elements/img/close_modal.png"
                                                     alt="Close modal">
                                            </span>
                                        </button>
                                        <h4 class="modal-title" id="resources-list"></h4>
                                </div>
                                <div class="modal-body resource-modal-body">
                                    <ul class="resource-items">
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- End of Modal Panel -->
                        <br/>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
/* SB-46 by atabag/mabrigos 20190130
 * this should only run twice, second run is a fallback whenever the user move to a different page right away.
 */
if ($_SESSION['myResourcesVisitCount'] <= 2) {
    if (isset($_COOKIE['ccmUserHash']) && $_COOKIE['ccmUserHash']) {
        $cookie = $_COOKIE['ccmUserHash'];
    }

// Add set cookies below
$epub_cookie_url = COOKIE_URL_EPUB."?c=" . $cookie;
$itb_cookie_url = COOKIE_URL_ITB."?c=" . $cookie;
// ANZGO-3456 added by james bernardez 20170802
$th_cookie_url = COOKIE_URL_TH."?c=" . $cookie;
// ANZGO-3456 added by james bernardez 20170802 set both https and http
$ths_cookie_url = COOKIE_URL_THS."?c=" . $cookie;
?>
<img src="<?php echo $epub_cookie_url; ?>" style="display:none;">
<img src="<?php echo $itb_cookie_url; ?>" style="display:none;">
<!-- ANZGO-3456 added by james bernardez 20170802 -->
<!-- SB-59 removed by machua 20190204 -->
<!-- ANZGO-3456 added by james bernardez 20170802 -->
<img src="<?php echo $ths_cookie_url; ?>" style="display:none;">

<?php } ?>