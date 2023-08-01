<?php
// ANZGO-3727 Modified by Maryjes Tanada 2018-06-05 Referrer-Policy
Header('Referrer-Policy: no-referrer');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Code Health Check | Cambridge Australia</title>

    <!-- Bootstrap -->
    <link href="<?php echo $this->getThemePath(); ?>/css/bootstrap.min.css?v=1" rel="stylesheet">
    <link href="<?php echo $this->getThemePath(); ?>/css/custom.css" rel="stylesheet">
    <link href="<?php echo $this->getThemePath(); ?>/css/animate.css" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,700,600' rel='stylesheet' type='text/css'>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!--[if IE]>
    <link rel="stylesheet" type="text/css" href="css/ie.css" />
    <![endif]-->
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <?php //SB-341 added by mabrigos 20190917 
        $this->addHeaderItem(Loader::helper('html')->javascript('googleTagManager.js', 'go_theme'));
    ?>
</head>

<body>
<?php Loader::element('header_required'); ?>
<?php //SB-341 added by mabrigos 20190917 ?>
<!-- SB-847 Google Tag Manager added by jdchavez -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K5WD64"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<div class="page-wrap">
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">
                    <img alt="Cambridge" src="<?php echo $this->getThemePath(); ?>/images/logo-blue.svg">
                </a>
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-nav"
                        aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse" id="main-nav">
                <ul class="nav navbar-nav navbar-right">
                    <!--ANZGO-3881 modified by mtanada 20181017-->
                    <li><a class="hvr-underline-from-center hvr-bounce-to-top" href="https://cambridgehelp.zendesk.com"
                           target="_blank">
                            Support
                        </a>
                    </li>
                    <!--ANZGO-3919 modified by mtanada 20181120-->
                    <li><a class="hvr-underline-from-center hvr-bounce-to-top" href="/go/contact/">
                            Contact Us</a>
                    </li>
                    <li><a class="hvr-underline-from-center hvr-bounce-to-top" href="/go/">Cambridge GO</a></li>
                    <li><a class="hvr-underline-from-center hvr-bounce-to-top" href="/education/">Store</a></li>
                </ul>
            </div>

        </div>
    </nav>


    <div id="main" class="container-fluid codeCheck">
        <div class="container">
