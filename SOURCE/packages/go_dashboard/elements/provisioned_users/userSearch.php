<?php

/**
 * User Search for Provisioned Users Element
 * ANZGO-3595 Added by Shane Camus 01/25/2018
 */

defined('C5_EXECUTE') || die("Access Denied."); ?>

<div class='ccm-pane-body'>
    <?php echo Loader::helper('concrete/interface')->tabs($tabs); ?>
    <?php foreach ($tabs as $tab) : ?>
        <div id="ccm-tab-content-<?php echo $tab[0]; ?>" class='ccm-tab-content'>
            <?php

            Loader::packageElement(
                "provisioned_users/$tab[1]",
                'go_dashboard',
                array(
                    'user' => @$user,
                    'noteResults' => @$noteResults,
                    'userSubscriptions' => @$userSubscriptions,
                    'userTrackingGeneral' => @$userTrackingGeneral,
                    'userActivationErrors' => @$userActivationErrors
                )
            );
            ?>
        </div>
    <?php endforeach; ?>
</div>
