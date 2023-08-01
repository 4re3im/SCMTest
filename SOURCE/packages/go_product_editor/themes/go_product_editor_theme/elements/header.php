<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="<?php echo LANGUAGE?>" xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php // Loader::element('header_required'); ?>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"
          name="viewport">
        <link rel="stylesheet" href="<?php echo $this->getStyleSheet('css/jquery-ui.min.css','go_product_editor')?>" />
        <link rel="stylesheet" href="<?php echo $this->getStyleSheet('css/bootstrap/bootstrap.min.css','go_product_editor')?>" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" />
        <link rel="stylesheet" href="<?php echo $this->getStyleSheet('css/skins/skin-green.min.css','go_product_editor')?>" />
        <link rel="stylesheet" href="<?php echo $this->getStyleSheet('css/plugins/select2/select2.min.css','go_product_editor')?>" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic);" />
        <link rel="stylesheet" href="<?php echo $this->getStyleSheet('css/AdminLTE.min.css','go_product_editor')?>" />
        <?php // SB-243 modified by jbernardez 20190703 ?>
        <link rel="stylesheet" href="<?php echo $this->getStyleSheet('css/custom.css','go_product_editor')?>?v=1.0" />
        <link rel="stylesheet" href="<?php echo $this->getStyleSheet('css/main.css','go_product_editor')?>?v=1.1" />

    </head>
    <body class="hold-transition skin-green sidebar-mini">
        <div id="popup" class="popup-changes" style="top:0px;">Please wait...</div>
        <div class="wrapper body-block">
            <header class="main-header">
                <a href="<?php echo $this->url('/go_product_editor'); ?>" class="logo">
                    <span class="logo-mini">
                        <svg width="43" height="42">
                            <image xlink:href="<?php echo $this->getThemePath(); ?>/images/logo-mini.svg" src="<?php echo $this->getThemePath(); ?>/images/logo-mini.png" width="43" height="42" />
                        </svg>
                    </span>
                    <span class="logo-lg">
                        <svg width="180" height="42">
                            <image xlink:href="<?php echo $this->getThemePath(); ?>/images/logo-lg.svg" src="<?php echo $this->getThemePath(); ?>/images/logo-lg.png" width="180" height="42" />
                        </svg>
                    </span>
                </a>
                <!-- Header Navbar -->

                <nav class="navbar navbar-static-top" role="navigation">
                    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                        <span class="sr-only">Toggle navigation</span>
                    </a>
                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav nav-tabs" id="main-tab" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#tabs" class="mainTabs" aria-controls="tabs" role="tab" data-toggle="tab">TABS</a>
                            </li>
                            <li role="presentation">
                                <a href="#content" class="mainContent" aria-controls="content" role="tab" data-toggle="tab">CONTENT</a>
                            </li>
                        </ul>
                        <ul class="nav navbar-nav logout">
                            <li>
                                <form role="search" class="navbar-form navbar-left form-inline" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <input size="25" type="text" placeholder="Search" id="navbar-search-input" class="form-control">
                                    </div>
                                </form>
                            </li>
                            <li>
                                <a id="to-dashboard" data-toggle="control-sidebar"><i class="fa fa-th"></i> DASHBOARD</a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
