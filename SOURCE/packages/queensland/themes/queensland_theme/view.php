<?php

/**
 * View for Education Theme
 */

defined('C5_EXECUTE') || die(_("Access Denied."));
?>
<?php if(isset($theme_competition) && $theme_competition):?>
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <?php $this->inc('elements/html_head_competition.php');?>
    </head>
    <body>
        <?php $this->inc('elements/header_competition.php');?>

        <div class="cup-content-area-main">
            <?php print $innerContent; ?>
        </div>

        <?php $this->inc('elements/footer_competition.php'); ?>
    </body>
    </html>
<?php else:?>
    <!DOCTYPE html>
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <?php $this->inc('elements/html_head.php');?>
    </head>
    <body>
        <?php // SB-341 added by mabrigos 20190917 ?>
        <!-- SB-847 Google Tag Manager added by jdchavez -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K5WD64"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <?php $this->inc('elements/header.php');?>

        <div class="cup-content-area-main">
            <?php print $innerContent; ?>
        </div>

        <?php $this->inc('elements/footer.php'); ?>
    </body>
    </html>
<?php endif;?>
