<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>


<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
   <title>Dynamic English Skills | Australian Curriculum</title>

   <!-- Bootstrap -->
   <link href="<?php echo $this->getThemePath(); ?>/css/bootstrap.min.css" rel="stylesheet">
   <link href="<?php echo $this->getThemePath(); ?>/css/custom.css" rel="stylesheet">   
   <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,300,700,800' rel='stylesheet' type='text/css'>
   <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
   <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
   <!--[if lt IE 9]>
   <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
   <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
   <![endif]-->

   <!--[if IE]>
   <link rel="stylesheet" type="text/css" href="<?php echo $this->getThemePath(); ?>/css/ie.css" />
   <![endif]-->
   <?php //SB-847 Google Tag Manager added by jdchavez
      $this->addHeaderItem(Loader::helper('html')->javascript('googleTagManager.js', 'go_theme'));
   ?>


<?php  Loader::element('header_required'); ?>
	
<!-- Site Header Content //-->
<!-- <link rel="stylesheet" media="screen" type="text/css" href="<?php //echo $this->getStyleSheet('main.css')?>" />
<link rel="stylesheet" media="screen" type="text/css" href="<?php //echo $this->getStyleSheet('typography.css')?>" />
 -->

<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function()
{ (i[r].q=i[r].q||[]).push(arguments)}
,i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
ga('create', 'UA-77651703-1', 'auto');
ga('send', 'pageview');
</script>

</head>

<body>
   <!-- SB-847 Google Tag Manager added by jdchavez -->
   <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K5WD64"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

   <div id="logo" class="container-fluid">
      <div class="logo row">

         <div class="col-md-3"></div>
         <div class="col-md-6">
            <div class="branding">
               <img src="<?php echo $this->getThemePath(); ?>/images/logo.svg" />
               <img class="bubble animated headShake infinite long" src="<?php echo $this->getThemePath(); ?>/images/speechbubble.png" />
            </div>
         </div>
         <div class="col-md-3">
            <a class="btn btn-info hvr-rectangle-out" href="#" role="button">Join Now</a>
            <a class="btn btn-info hvr-rectangle-out" href="#" role="button">Sign In</a>
         </div>

      </div>
   </div>

   <nav class="navbar navbar-default">
      <div class="container">
         <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-nav" aria-expanded="false">
               <span class="sr-only">Toggle navigation</span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
            </button>
         </div>
         <div class="collapse navbar-collapse" id="main-nav">
            <ul class="nav navbar-nav">
                  <?php if(strtolower($c->getCollectionName()) == 'dynamic english') { ?>
                  <li><a class="hvr-underline-from-center" href="#home">Home</a></li>
                  <li><a class="hvr-underline-from-center" href="#try">Try it now</a></li>
                  <li><a class="hvr-underline-from-center" href="#buy">Buy it now</a></li>
                  <li><a class="hvr-underline-from-center" href="#contact">Contact</a></li>
               <?php } else { ?>
                  <li><a class="hvr-underline-from-center" href="<?php echo $this->url("/dynamicenglish/#home"); ?>">Home</a></li>
                  <li><a class="hvr-underline-from-center" href="<?php echo $this->url("/dynamicenglish/#try"); ?>">Try it now</a></li>
                  <li><a class="hvr-underline-from-center" href="<?php echo $this->url("/dynamicenglish/#buy"); ?>">Buy it now</a></li>
                  <li><a class="hvr-underline-from-center" href="<?php echo $this->url("/dynamicenglish/#contact"); ?>">Contact</a></li>
               <?php } ?>
               <!-- <li><a class="hvr-underline-from-center" href="https://www.hotmaths.com.au/usersCommon/showHelp.action#/1/210/" target="_blank">Help</a></li> -->
            </ul>
         </div>

      </div>
   </nav>

   <!-- Don't delete. -->
   <input type="hidden" value="<?php echo $this->url('dynamicenglish/processContactForm'); ?>" id="contact-us-handler"/>

<div id="home" class="container-fluid small">
<!-- <div id="page"> -->
	<!-- <div id="headerSpacer"></div> -->