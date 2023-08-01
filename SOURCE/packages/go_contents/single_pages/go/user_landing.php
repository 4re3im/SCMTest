<?php
defined('C5_EXECUTE') or die(_("Acess Denied"));

// ANZGO-3792 added by jbernardez 20180710
$activateMode = Config::get('ACTIVATE_MODE'); 
?>
<div class="container-fluid">
    <div class="row text-center">
        <div class="col-lg-12 resources-container">
            <p>Hi there</p>
            <h1><?php echo $user; ?></h1>
            <br />
        </div>
    </div>
</div>
<br />
<br />
<div class="container">
    <div class="row">
        <div class="col-lg-8 col-lg-offset-2">
            <div class="row text-center">
                <div class="col-lg-3">
                    <a href="<?php echo $this->url('/go/myresources'); ?>">
                        <svg class="svg-mid">
                            <use xlink:href="#icon-my_resources"></use>
                        </svg>
                        <span class="svg-text-mid">My Resources</span>
                    </a>            
                </div>
                <div class="col-lg-3">
                    <a href="<?php echo $this->url('go/account'); ?>">
                        <svg class="svg-mid">
                            <use xlink:href="#icon-my_details"></use>
                        </svg>
                        <span class="svg-text-mid">My Details</span>
                    </a>           
                </div>
                <div class="col-lg-3">
                    <?php 
                    // ANZGO-3789 added by jbernardez 20180706
                    if ($activateMode) { 
                    ?>
                    <a id="front-deactivate">
                    <?php } else { ?>
                    <a href="<?php echo $this->url('/go/activate'); ?>" class="front-ajax-btn">
                    <?php } ?>
                        <svg class="svg-mid">
                            <use xlink:href="#icon-activate"></use>
                        </svg>
                        <span class="svg-text-mid">Activate Resource</span>
                    </a>
                </div>
                <div class="col-lg-3">
                    <!-- ANZGO-3378, James Bernardez, 20170518 '/go/login/logout'-->
                    <a href="<?php echo $this->url('/sso/logout'); ?>" id="logout"  class="btn btn-danger">
                        <svg class="svg">
                            <use xlink:href="#icon-logout"></use>
                        </svg>
                        <span class="svg-text">Logout</span>
                    </a>
                    <br />
                    <br />
                </div>
            </div>
        </div>
    </div>
</div>