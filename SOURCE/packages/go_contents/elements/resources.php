<!-- ANZGO-3947 Added by Shane Camus 12/10/18 -->

<?php defined('C5_EXECUTE') || die(_('Access Denied.'));  ?>

<?php foreach ($resources as $resource) { ?>
    <?php
        // ANZGO-3947 added by jbernardez 20181211
        $collapseState = $index > 1 ? 'collapsed' : 'active';
        $collapseStateIn = $index > 1 ? '' : 'in';
    ?>
    <div class="panel" id="panel<?php echo $index ?>">
        <div class="panel-heading <?php echo $collapseState; ?>"
             data-toggle="collapse"
             data-target="#collapse<?php echo $index ?>"
             href="#collapse<?php echo $index ?>">
            <div class="panel-title">
                <div class="book-thumbnail">
                    <img src="<?php echo $resource['image']; ?>" alt="Book">
                </div>
                <p><?php echo $resource['title']; ?></p>
            </div>
        </div>
        <div id="collapse<?php echo $index ?>" class="panel-collapse collapse <?php echo $collapseStateIn; ?>">
            <div class="panel-body">
            <!-- SB-2 Added by Michael Abrigos 01/16/19, SB-44 Modified by Shane & Errol 01/28/19 -->
            <?php if ($resource['limited']) { ?>
                <strong class="page-notification" style="margin-top:0px;margin-bottom: 15px;">
                    Please insert your activation code to validate your resource.
                     You will be permitted to access the product without entering your activation code until
                    <?php echo $resource['limitedEndDate']; ?>.
                    Click <a href="/go/activate/">HERE</a> to activate.</strong>
            <?php } ?>
                <?php
                    Loader::packageElement(
                            'tiles',
                            'go_contents',
                            array('tiles' => $resource['tiles'])
                    );
                ?>
            </div>
        </div>
    </div>
    <?php $index++; ?>
<?php } ?>


