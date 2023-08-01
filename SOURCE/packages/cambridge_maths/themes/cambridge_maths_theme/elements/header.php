<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
       <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>Cambridge Maths</title>

        <!-- Bootstrap -->
        <link href="<?php echo $this->getThemePath(); ?>/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo $this->getThemePath(); ?>/css/custom.css?v=3" rel="stylesheet">
        <!-- <link href="css/animate.css" rel="stylesheet"> -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,300,700,800"
        rel="stylesheet" type="text/css">
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]><![endif]-->

        <!--[if IE]>
            <link rel="stylesheet" type="text/css" href="css/ie.css" />
        <![endif]-->
        <?php // SB-299 added by jbernardez 20190808 ?>
        <script src='https://www.google.com/recaptcha/api.js' async defer></script>
        <?php //SB-341 added by mabrigos 20190917 
            $this->addHeaderItem(Loader::helper('html')->javascript('googleTagManager.js', 'go_theme'));
        ?>
        
    </head>
    <body>
        <?php Loader::element('header_required'); ?>
        <!--Moved the image logo and banner display in c5 Content Block so it can be editable-->
        <!--Images still needs to be uploaded to be able to use on front-end -->
        <?php //SB-341 added by mabrigos 20190917 ?>
        <!-- SB-847 Google Tag Manager added by jdchavez -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K5WD64"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
