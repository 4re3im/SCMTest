<?php
defined('C5_EXECUTE') || die(_("Access Denied"));
define('GO_THEME_PATH', DIR_REL . '/packages/go_theme');
define('SUBJECT', 'subject');
define('TITLE', 'title');
define('SERIES', 'series');
// ANZGO-3727 Modified by Maryjes Tanada 2018-06-05 Referrer-Policy
Header('Referrer-Policy: no-referrer');

$u = new User();

$displayText = "Login";
$logoutPath = '/go/login/logout';
if ($u->isLoggedIn()) {
    $ui = UserInfo::getByID($u->getUserID());

    //work around to display username
    $email = explode("@", $ui->uEmail);
    $displayText = !empty($ui->getAttribute('uFirstName')) ? $ui->getAttribute('uFirstName') : $email[0];

    $showHeader = false;
    $userGrp = $u->getUserGroups();
    if (in_array('CUP Staff', $userGrp) ||
        in_array('Customer Service', $userGrp) ||
        in_array('Administrators', $userGrp)) {
        $showHeader = true;
    }
    $uGroup = array_slice($u->uGroups, 1, 1);
    $uGroup[0] = rtrim($uGroup[0], 's');

    $adminGroup = Group::getByName('Administrators');
    if ($u->inGroup($adminGroup)) {
        $logoutPath = '/login/logout';
    }
}

?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN"
        "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
<html lang="en">
<head>
    <?php Loader::element('header_required', '',
        '/go_theme/themes/go_theme/'); ?>
    <script src="//use.typekit.net/tgs4nhs.js"></script>
    <script>try {
        Typekit.load();
      } catch (e) {
      }</script>
    <script>
        <?php if (isset($viewmore) && $viewmore == 1) { ?>
        var contactusmore = true;
        <?php } else { ?>
        var contactusmore = false;
        <?php } ?>
    </script>
    <script>
      (function (i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function () {
          (i[r].q = i[r].q || []).push(arguments);
        }
          , i[r].l = 1 * new Date();
        a = s.createElement(o),
          m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src = g;
        m.parentNode.insertBefore(a, m);
      })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');
      ga('create', 'UA-40538957-1', 'auto');
      ga('send', 'pageview');
    </script>
    <!-- ANZGO-3736 added by jbernardez 20180531 -->
    <script src='https://www.google.com/recaptcha/api.js' async
            defer></script>
    <!-- ANZG0-3869 Added by mtanada 20181003 UI/UX-->
    <link rel="manifest" href="site.webmanifest">
    <link href="https://fonts.googleapis.com/css?family=Muli:300,400,600,700"
          rel="stylesheet">
    <link rel="preload" href="<?php echo GIGYA_CDN; ?>" as="script">
    <script type="text/javascript"
            lang="javascript"
            src=<?php echo GIGYA_CDN; ?>>
    </script>

    <script type="text/javascript">
      var GIGYA_API_KEY = '<?php echo GIGYA_API_KEY ?>';
      var GIGYA_CDN = '<?php echo GIGYA_CDN ?>';
    </script>
    <script type="text/javascript"
            src="/packages/go_contents/js/gigya.js?v=38.4">
    </script>
    <script>
        var GIGYA_REGISTRATION_LOGIN_SCREENS = '<?php echo GIGYA_REGISTRATION_LOGIN_SCREENS; ?>';
        var GIGYA_PROFILE_UPDATE_SCREENS = '<?php echo GIGYA_PROFILE_UPDATE_SCREENS; ?>';
    </script>
</head>
<body class="<?php echo ($u->isSuperUser() || $showHeader) ? 'super-user' : ''; ?>">
<div style="width: 1px;height: 1px;">
    <?php
    include(DIR_PACKAGES . '/go_theme/elements/svg-defs.svg');
    ?>
</div>

<div class = "loader"></div>

<!--ANZGO-3869 modified by mtanada 20181004 New Header -->
<div id="header-wrapper" class="header-wrapper">
    <div class="container-fluid">
        <div class="row align-center">
            <div class="col-xs-4 col-sm-4 col-md-4">
                <div class="logo">
                    <a href="<?php echo $this->url("/go"); ?>">
                        <img src="/packages/go_theme/elements/svgs/go_logo.svg"
                             alt="Cambridge Go Logo"/>
                    </a>
                </div>
            </div>
            <div class="col-xs-8 col-md-8">
                <div id="navigation-wrapper">
                    <div class="mobile-navigation">
                        <span class="indicator">Menu</span>
                        <div class="mobile-nav">
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                    <!--start navi-->
                    <nav class="navigation">
                        <?php $href = ($u->isLoggedIn()) ? $this->url('/go/user_landing') : $this->url('/go/login'); ?>
                        <?php if ($u->isLoggedIn()) { ?>
                            <ul>
                                <li>
                                    <a href="<?php echo $this->url('go/account'); ?>"
                                       class="hide header-font-weight">
                                        Profile</a>
                                </li>
                                <!-- ANZGO-3980 added by jbernardez 20181019 -->
                                <li><a class="header-font-weight"
                                       href="<?php echo $this->url('/go/myresources/'); ?>"
                                       rel="noopener noreferrer"> My
                                        resources</a>
                                </li>
                                <?php // SB-294 added by jbernardez 20190904 ?>
                                <li><a class="header-font-weight"
                                       href="<?php echo $this->url('/codecheck/'); ?>"
                                       target="_blank"
                                       rel="noopener noreferrer"> Code check</a>
                                </li>
                                <li><a class="header-font-weight"
                                       href="<?php echo $this->url('/education/'); ?>"
                                       target="_blank"
                                       rel="noopener noreferrer"> Store</a>
                                </li>
                                <!--Modified by mtanada 20181211 Subject page-->
                                <li><a id="header-browse"
                                       class="header-font-weight"
                                       href="<?php echo $this->url('/go/search/'); ?>">
                                        Subjects
                                    </a>
                                </li>
                                <li>
                                    <a id="header-support"
                                       class="header-font-weight"
                                       href="https://cambridgehelp.zendesk.com"
                                       target="_blank">
                                        Support
                                    </a>
                                </li>
                                <li>
                                    <a class="hide header-font-weight"
                                       href="<?php echo $this->url($logoutPath); ?>"
                                       id="logout">Log out</a>
                                </li>
                            </ul>
                            <!-- Not Logged-In-->
                        <?php } else { ?>
                            <ul>
                                <?php // SB-294 added by jbernardez 20190904 ?>
                                <li><a class="header-font-weight"
                                       href="<?php echo $this->url('/codecheck/'); ?>"
                                       target="_blank"
                                       rel="noopener noreferrer"> Code check</a>
                                </li>
                                <li><a class="header-font-weight"
                                       href="<?php echo $this->url('/education/'); ?>"
                                       target="_blank"
                                       rel="noopener noreferrer" style="">
                                        Store</a>
                                </li>
                                <!--Modified by mtanada 20190102 Subject page-->
                                <li><a id="header-browse"
                                       class="header-font-weight"
                                       href="<?php echo $this->url('/go/search'); ?>">
                                        Subjects</a>
                                </li>
                                <li id="header-support">
                                    <a class="header-font-weight"
                                       href="https://cambridgehelp.zendesk.com"
                                       target="_blank">
                                        Support
                                    </a>
                                </li>
                                <li class="login-btn show">
                                    <!-- ANZGO-3913 modified by jbernardez 20181106 -->
                                    <a href="<?php echo $href; ?>">
                                        <button id="header-login"
                                                class="header-font-weight btn btn-primary btn-lg"
                                                href="<?php echo $href; ?>">
                                            Log in
                                        </button>
                                    </a>
                                </li>
                            </ul>
                        <?php } ?>
                    </nav>
                    <!-- end navi-->
                    <!--start post nav-->
                    <?php if ($u->isLoggedIn()) { ?>
                        <div class="avatar-wrapper" tabindex="0">
                            <div class="image-holder">
                                <img src="/packages/go_theme/elements/img/avatar.png"
                                     alt="Avatar"/>
                            </div>
                            <span class="role"><?php echo $uGroup[0]; ?></span>
                            <div class="top-nav">
                                <ul>
                                    <li><a class="header-font-weight"
                                           href="<?php echo $this->url('go/account'); ?>">Profile
                                        </a>
                                    </li>
                                    <li><a class="header-font-weight"
                                           href="<?php echo $this->url($logoutPath); ?>"
                                           id="logout">Log out</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    <?php } else { ?>
                        <!-- <div class="login-btn">
                            <form method="get" action="dashboard.php">
                                <button type="submit" class="btn btn-primary btn-lg">Log In</button>
                            </form>
                        </div> -->
                    <?php } ?>
                    <!--end post nav-->
                </div>
            </div>
        </div>
    </div>
</div>
<!--end-->
<!-- do not delete.  -->
<input type="hidden" id="login-text" value="<?php echo $displayText; ?>"/>
<br/>
<?php
// ANZGO-3920 modified by jbernardez 20181203
$pageURL = $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
$urlVerifier = explode('/', $pageURL);
$needleArray = array('go/login', 'go/signup', 'go/myresources');
$newPage = false;
foreach ($needleArray as $needle) {
    $page = strpos($pageURL, $needle);
    if ($page !== false) {
        $newPage = true;
    }
    // use this as override for home page
    if ($urlVerifier[1] === 'go' && $urlVerifier[2] === '') {
        $newPage = true;
    }
}
if (!$newPage) {
    ?>

<?php } ?>
<script>
    <?php if ($u->isLoggedIn()): ?>
    jQuery(document).ready(function () {
      $('#front-signup-teacher').addClass('disabled');
      $('#front-signup-student').addClass('disabled');
    });
    <?php endif; ?>
</script>
