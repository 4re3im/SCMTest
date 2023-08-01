<?php
/**
 * ANZGO-3553 Added by Jeszy Tanada 10/23/2017
 * Getting Started header page
 */
session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        var contactusmore=false;
    </script>
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Getting Started Page | Cambridge Australia</title>

    <!-- Bootstrap -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,700,600' rel='stylesheet' type='text/css'>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <?php //SB-341 added by mabrigos 20190917 
        $this->addHeaderItem(Loader::helper('html')->javascript('googleTagManager.js', 'go_theme'));
    ?>

<?php
print $this->controller->outputHeaderItems();
?>

</head>

<body>
<?php Loader::element('header_required'); ?>
<!-- SB-847 Google Tag Manager added by jdchavez -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K5WD64"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <div style="width: 1px;height: 1px;">
        <?php
        include_once(DIR_PACKAGES . '/go_theme/elements/svg-defs.svg');
        ?>
    </div>
