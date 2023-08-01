<?php
/**
 * Created by PhpStorm.
 * User: gxbalila
 * Date: 29/04/2020
 * Time: 9:07 AM
 */
$html = Loader::helper('html');

$GO_THEME = 'go_theme';

$BOOTSTRAP_MIN_CSS_VERSION = 4;
$GO_CORE_CSS_VERSION = 6.51;
$GIGYA_CSS_VERSION = 2;

$elements = $links = [];

// Get all hrefs
// bootstrap.min.css
$links[] = (string)$html->css('bootstrap.min.css', $GO_THEME)->href . '?v=' . $BOOTSTRAP_MIN_CSS_VERSION;

// go_product_editor/gpe.css
$links[] = (string)$html->css('go_product_editor/gpe.css', $GO_THEME)->href;

// go-core.css
$links[] = (string)$html->css('go-core.css', $GO_THEME)->href . '?v=' . $GO_CORE_CSS_VERSION;

// style.css
$links[] = (string)$html->css('style.css', 'go_product')->href;

// gigya.css
$links[] = (string)$html->css('gigya.css', 'go_contents')->href . '?v=' . $GIGYA_CSS_VERSION;

foreach ($links as $link) {
    // Build preload links
    $elements[] = '<link rel="preload" href="' . $link . '" as="style">';
}

foreach ($links as $link) {
    // Build css links
    $elements[] = '<link rel="stylesheet" type="text/css" href="' . $link . '" />';
}

foreach ($elements as $element) {
    $this->addHeaderItem($element);
}