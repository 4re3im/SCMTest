<?php

/**
 * Test for Queensland Theme
 */

defined('C5_EXECUTE') || die(_("Access Denied."));
?>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <link rel="stylesheet" type="text/css" href="<?php print $this->getStyleSheet('css/main.css'); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php print $this->getStyleSheet('css/typography.css'); ?>" />
    <?php Loader::element('header_required'); ?>
</head>
<body>
    <div class="cup-content-area-main">
        <?php
            $content = new Area('Main');
            $content->display($c);
        ?>
    </div>
    <?php require(DIR_FILES_ELEMENTS_CORE . '/footer_required.php');?>
</body>
</html>
