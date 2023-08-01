<!-- ANZGO-3979 Added by machua 20181220 modal for weblinks -->

<?php defined('C5_EXECUTE') || die(_('Access Denied.'));  ?>

<?php
// SB-305 added by machua 20190823 to display tab text
if (!is_null($assets['tabText'])) { ?>
    <p><?php echo $assets['tabText']; ?></p>
<?php }
foreach ($assets['redirects'] as $chapter => $contents) { ?>
    <p style="font-weight:bold"><?php echo $chapter; ?></p>
    <?php foreach ($contents as $content) { ?>
        <li>
            <a
               href="<?php echo $content['url']; ?>"
               class="item"
               target="_blank">
                <div class="resource-thumbnail">
                    <img class="img-resource-thumbnail" alt="Weblink">
                </div>
                <div class="resource-title">
                    <p><?php echo $content['title'];?></p>
                    <span>Weblink</span>
                </div>
            </a>
        </li>
    <?php } ?>
<?php } ?>
