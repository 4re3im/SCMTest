<?php
defined('C5_EXECUTE') or die("Access Denied.");
global $u, $pageTitle;
$c = $this->getCollectionObject();
if (is_object($c)) {
    $cp = new Permissions($c);
}
$show_header = false;
$user_grp = $u->getUserGroups();

//SB-252 added by mabrigos - session to check if user visited any go website before going to login page
$_SESSION['redirectback'] = true;

$isCupStaff = in_array('CUP Staff', $user_grp);
$isCustomerService = in_array('Customer Service', $user_grp);
$isAdministrator = in_array('Administrators', $user_grp);

if ($isCupStaff || $isCustomerService || $isAdministrator) {
    $show_header = true;
}

/**
 * Handle page title
 */
if (is_object($c)) {
    // We can set a title 3 ways:
    // 1. It comes through programmatically as $pageTitle. If this is the case then we pass it through, no questions asked
    // 2. It comes from meta title
    // 3. It comes from getCollectionName()
    // In the case of 3, we also pass it through page title format.

    if (!isset($pageTitle) || !$pageTitle) {
        // we aren't getting it dynamically.
        $pageTitle = $c->getCollectionAttributeValue('meta_title');
        if (!$pageTitle) {
            $pageTitle = $c->getCollectionName();
            if ($c->isSystemPage()) {
                $pageTitle = t($pageTitle);
            }
            $pageTitle = sprintf(PAGE_TITLE_FORMAT, SITE, ucfirst($pageTitle));
        }
    }
    $pageDescription = (!isset($pageDescription) || !$pageDescription) ? $c->getCollectionDescription() : $pageDescription;
    $cID = $c->getCollectionID();
    $isEditMode = ($c->isEditMode()) ? "true" : "false";
    $isArrangeMode = ($c->isArrangeMode()) ? "true" : "false";
} else {
    $cID = 1;
}
?>
    <meta http-equiv="content-type"
          content="text/html; charset=<?php echo APP_CHARSET ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
$akd = $c->getCollectionAttributeValue('meta_description');
$akk = $c->getCollectionAttributeValue('meta_keywords');
?>
    <title><?php echo htmlspecialchars($pageTitle, ENT_COMPAT,
            APP_CHARSET); ?></title>
<?php if ($akd) { ?>
    <meta name="description"
          content="<?= htmlspecialchars($akd, ENT_COMPAT, APP_CHARSET) ?>"/>
<?php } else { ?>
    <meta name="description"
          content="<?= htmlspecialchars($pageDescription, ENT_COMPAT,
              APP_CHARSET) ?>"/>
<?php }
if ($akk) {
    ?>
    <meta name="keywords"
          content="<?= htmlspecialchars($akk, ENT_COMPAT, APP_CHARSET) ?>"/>
<?php }
if ($c->getCollectionAttributeValue('exclude_search_index')) {
    ?>
    <meta name="robots" content="noindex"/>
<?php } ?>
<?php
if (defined('APP_VERSION_DISPLAY_IN_HEADER') && APP_VERSION_DISPLAY_IN_HEADER) {
    echo '<meta name="generator" content="concrete5 - ' . APP_VERSION . '" />';
} else {
    echo '<meta name="generator" content="concrete5" />';
}
?>

<?php $u = new User(); ?>
    <script type="text/javascript">
        <?php
        echo "var CCM_DISPATCHER_FILENAME = '" . DIR_REL . '/' . DISPATCHER_FILENAME . "';\r";
        echo "var CCM_CID = " . ($cID ? $cID : 0) . ";\r";
        if (isset($isEditMode)) {
            echo("var CCM_EDIT_MODE = {$isEditMode};\r");
        }
        if (isset($isEditMode)) {
            echo("var CCM_ARRANGE_MODE = {$isArrangeMode};\r");
        }

        // ANZGO-2036
        $v = View::getInstance();
        $time = time();
        ?>

        var CCM_IMAGE_PATH = '<?php echo ASSETS_URL_IMAGES ?>';
        var CCM_TOOLS_PATH = '<?php echo REL_DIR_FILES_TOOLS_REQUIRED ?>';
        var CCM_BASE_URL = '<?php echo BASE_URL ?>';
        var CCM_REL = '<?php echo DIR_REL ?>';

        var checkSessionURL = '<?php echo $v->url('/go/checkCookie') ?>';
        // ANZGO-3639 modified by jbernardez 201802021
        // will block this codes as this is not being used
        // see ANZGO-3344
        // var checkPopupCookieURL = "<?php // echo $v->url('/go/popupSeenCheck') ?>";
        var updatePopupCookieURL = '<?php echo $v->url('/go/popupSeen') ?>';
        var hideHelpPopupURL = '<?php echo $v->url('/go/hideHelpPopup') ?>';
        var goLoginURL = '<?php echo $v->url('/go/login') ?>';
        var goResourcesURL = '<?php echo $v->url('/go/myresources') ?>';
        // ANZUAT-128
        var firstLoginCheckURL = '<?php echo $v->url('/go/firstLoginCheck') ?>';
        var hideHelpSessionURL = '<?php echo $v->url('/go/hideHelpSession') ?>';

        var uniqueEmailCheckUrl = '<?php echo $v->url('/go/signup/checkIfEmailIsUnique'); ?>';
    </script>


<?php
$html = Loader::helper('html');
$this->addHeaderItem($html->css('ccm.base.css'), 'CORE');
// ANZGO-3760 modified by mtanada
$this->addHeaderItem('<link rel="stylesheet" type="text/css" href="' . (string)$html->css(
        'bootstrap.min.css',
        'go_theme'
    )->href . '?v=4"></link>');

// ANZGO-3789 modified by jdchavez 07/09/2018
$this->addHeaderItem($html->css('go_product_editor/gpe.css', 'go_theme'));
// SB-251 nodified by jbernardez 20190715
$this->addHeaderItem('<link rel="stylesheet" type="text/css" href="' . (string)$html->css(
        'go-core.css',
        'go_theme'
    )->href . '?v=6.51"></link>');

if ($show_header && !$_REQUEST['ccm_token']) {
    $this->addFooterItem($html->javascript('jquery.js', 'go_theme'));
    $this->addFooterItem($html->javascript('ccm.base.js', false, true), 'CORE');
    // SB-9 modifed by jbernardez 20191106
    $this->addFooterItem('<script type="text/javascript" src="' . (string)$html->javascript('go-core.js',
            'go_theme')->href . '?v=26.0"></script>');
    $this->addFooterItem($html->javascript('bootstrap.js', 'go_theme'));
    $this->addFooterItem($html->javascript('jquery.autotab.js', 'go_contents'));
    // ANZGO-3853 Modified by jbernardez 20180913
    $this->addFooterItem('<script type="text/javascript" src="' . (string)$html->javascript('activate.js',
            'go_contents')->href . '?v=18.9"></script>');
    // Added by Paul Balila, 2016-04-21
    // For ticket ANZUAT-139
    $this->addFooterItem($html->javascript('swfobject.js'));
    // ANZGO-3451 Added by Shane Camus ANZGO-3881 modified by mtanada 20181017
    $this->addFooterItem('<script type="text/javascript" src="' . (string)$html->javascript('support.js',
            'go_contents')->href . '?v=1.1"></script>');

} else {
    $this->addHeaderItem($html->javascript('jquery.js', 'go_theme'));
    $this->addHeaderItem($html->javascript('ccm.base.js', false, true), 'CORE');
    // SB-9 modifed by jbernardez 20191106
    $this->addHeaderItem('<script type="text/javascript" src="' . (string)$html->javascript('go-core.js',
            'go_theme')->href . '?v=26.0"></script>');
    $this->addHeaderItem($html->javascript('bootstrap.js', 'go_theme'));
    $this->addHeaderItem($html->javascript('jquery.autotab.js', 'go_contents'));
    // ANZGO-3853 Modified by jbernardez 20180913
    $this->addHeaderItem('<script type="text/javascript" src="' . (string)$html->javascript('activate.js',
            'go_contents')->href . '?v=18.9"></script>');
    // Added by Paul Balila, 2016-04-21
    // For ticket ANZUAT-139
    $this->addHeaderItem($html->javascript('swfobject.js'));
    // ANZGO-3451 Added by Shane Camus ANZGO-3881 modified by mtanada 20181017
    $this->addFooterItem('<script type="text/javascript" src="' . (string)$html->javascript('support.js',
            'go_contents')->href . '?v=1.1"></script>');

}
$this->addHeaderItem($html->css('style.css', 'go_product'));
$this->addHeaderItem('<link rel="stylesheet" type="text/css" href="' . (string)$html->css(
        'gigya.css',
        'go_contents'
    )->href . '?v=2"></link>');

// ANZGO-3913 added by jbernardez 20181105
// ANZGO-3920 modified by jbernardez 20181115
$pageURL = filter_input(INPUT_SERVER, 'REQUEST_URI');
$needleArray = array('go/login', 'go/signup', 'go/myresources', 'go/activate',
                     'go/forgot_password', 'go/resend_verification');
$newPage = false;
foreach ($needleArray as $needle) {
    $page = strpos($pageURL, $needle);
    if ($page !== false) {
        $newPage = true;
    }
}

if (str_replace('/', '', $pageURL) === 'go') {
    $newPage = true;
}

if ($newPage) {
    $this->addHeaderItem($html->css('normalize.css', 'go_contents'));
    // SB-9 modified by jbernardez 20191106
    $this->addHeaderItem('<link rel="stylesheet" type="text/css" href="' . (string)$html->css(
            'main.css',
            'go_contents'
        )->href . '?v=1.17"></link>');
}

// ANZGO-3675 Modified by Maryjes Tanada to increase js version 03/22/2018
$this->addFooterItem('<script type="text/javascript" src="' . (string)$html->javascript('marketing-popup.js',
        'go_theme')->href . '?v=2"></script>');
// ANZGO-3451 Added by Shane Camus
$this->addFooterItem('<script type="text/javascript" src="' . (string)$html->javascript('product.js',
        'go_product')->href . '?v=4"></script>');
// ANZGO-3913 added by jbernardez 20181107
$this->addFooterItem('<script type="text/javascript" src="' . (string)$html->javascript('main.js',
        'go_contents')->href . '?v=3.4"></script>');

$this->addFooterItem('<script type="text/javascript" src="/packages/go_elevate/js/elevate.js?v=10"></script>');

$favIconFID = intval(Config::get('FAVICON_FID'));
$appleIconFID = intval(Config::get('IPHONE_HOME_SCREEN_THUMBNAIL_FID'));
$modernIconFID = intval(Config::get('MODERN_TILE_THUMBNAIL_FID'));
$modernIconBGColor = strval(Config::get('MODERN_TILE_THUMBNAIL_BGCOLOR'));

if ($favIconFID) {
    $f = File::getByID($favIconFID);
    ?>
    <link rel="shortcut icon" href="<?php echo $f->getRelativePath() ?>"
          type="image/x-icon"/>
    <link rel="icon" href="<?php echo $f->getRelativePath() ?>"
          type="image/x-icon"/>
    <?php
}

if ($appleIconFID) {
    $f = File::getByID($appleIconFID);
    ?>
    <link rel="apple-touch-icon" href="<?php echo $f->getRelativePath() ?>"/>
    <?php
}

if ($modernIconFID) {
    $f = File::getByID($modernIconFID);
    ?>
    <meta name="msapplication-TileImage"
          content="<?php echo $f->getRelativePath(); ?>" /><?php
    echo "\n";
    if (strlen($modernIconBGColor)) {
        ?>
        <meta name="msapplication-TileColor"
              content="<?php echo $modernIconBGColor; ?>" /><?php
        echo "\n";
    }
}

if (is_object($cp)) {

    if ($this->editingEnabled()) {
        Loader::element('page_controls_header', array('cp' => $cp, 'c' => $c));
    }

    if ($this->areLinksDisabled()) {
        $this->addHeaderItem('<script type="text/javascript">window.onload = function() {ccm_disableLinks()}</script>',
            'CORE'
        );
    }
    $cih = Loader::helper('concrete/interface');
    if ($cih->showNewsflowOverlay()) {
        $this->addFooterItem('<script type="text/javascript">$(function() { ccm_showDashboardNewsflowWelcome(); });</script>');
    }
}

// SB-18 moved by machua 20190125 to remove activate.js dependency
// ANZGO-3581 Added by Shane Camus
$this->addFooterItem('<div id="rebotifyChatbox" botid="5a039f95653367000586111b">
    <script src="https://enterprise.rebotify.com/js/chatbox/rebotifyChatbox.js"></script></div>');

print $this->controller->outputHeaderItems();
$_trackingCodePosition = Config::get('SITE_TRACKING_CODE_POSITION');
if (empty($disableTrackingCode) && $_trackingCodePosition === 'top') {
    echo Config::get('SITE_TRACKING_CODE');
}
echo $c->getCollectionAttributeValue('header_extra_content');

