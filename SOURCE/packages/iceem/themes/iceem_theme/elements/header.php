<!DOCTYPE html>
<html lang="en">

<head>
    <?php Loader::element('header_required'); ?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>ICE-EM Mathematics Third Edition | Australian Curriculum</title>

    <!-- Bootstrap -->
    <link href="<?php echo $this->getThemePath(); ?>/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $this->getThemePath(); ?>/css/custom.css" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,300,700,800' rel='stylesheet' type='text/css'>
    <?php // SB-299 added by jbernardez 20190808 ?>
    <script src='https://www.google.com/recaptcha/api.js' async defer></script>
    <?php //SB-341 added by mabrigos 20190917 
        $this->addHeaderItem(Loader::helper('html')->javascript('googleTagManager.js', 'go_theme'));
    ?>
</head>

<body>
<?php //SB-341 added by mabrigos 20190917 ?>
<!-- SB-847 Google Tag Manager added by jdchavez -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K5WD64"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  <div id="logo" class="container-fluid">
      <div class="logo row">
          <div class="cup">
              <svg width="190" height="50">
                  <image xlink:href="<?php echo $this->getThemePath(); ?>/images/Logo-Cambridge.svg" src="images/Logo-Cambridge.png" width="100%" height="100%" />
              </svg>
          </div>
          <div class="col-md-3"></div>
          <div class="col-md-6">
              <div class="branding">
                  <svg width="250" height="90">
                      <image xlink:href="<?php echo $this->getThemePath(); ?>/images/Logo-ICE-EM.svg" src="images/Logo-ICE-EM.png" width="100%" height="100%" />
                  </svg>
              </div>
          </div>
          <div class="col-md-3"></div>
      </div>
  </div>
