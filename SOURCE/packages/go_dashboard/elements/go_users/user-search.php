<?php defined('C5_EXECUTE') || die(_("Access Denied.")); ?>

<!-- ANZGO-3634 Modified by John Renzo Sunico, 02/12/2018 -->
<style>
    .container, .navbar-fixed-top .container, .navbar-fixed-bottom .container {
        width: 1024px;
    }
</style>
<div class="ccm-pane-body">

    <?php echo Loader::helper('concrete/interface')->tabs($tabs); ?>

    <?php foreach ($tabs as $tab): ?>
        <div id="ccm-tab-content-<?php echo $tab[0]; ?>"
             class="ccm-tab-content">
            <?php

            Loader::packageElement("go_users/$tab[1]", "go_dashboard", array(
                "user" => @$user,
                "noteResults" => @$noteResults,
                "userSubscriptions" => @$userSubscriptions,
                "userTrackingGeneral" => @$userTrackingGeneral,
                "userActivationErrors" => @$userActivationErrors
            ));
            ?>
        </div>
    <?php endforeach; ?>
</div>




