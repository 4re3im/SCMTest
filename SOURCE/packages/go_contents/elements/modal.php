<!-- ANZGO-3947/ANZGO-3946 Added by Shane Camus 12/10/18 -->

<?php defined('C5_EXECUTE') || die(_('Access Denied.'));  ?>
<?php
// SB-293 added by machua 20190808 to display tab text
if (!is_null($assets['tabText'])) { ?>
    <p><?php echo $assets['tabText']; ?></p>
<?php }
foreach ($assets['tabContents'] as $tabContent) { ?>
    <?php 
    // SB-75 added by jbernardez 20190217
    if (!is_null($tabContent['contentHeading']) && count($tabContent['contents']) > 0)  { ?>
        <h5 style="text-align: left;"><?php echo $tabContent['contentHeading']; ?></h5>
    <?php } ?>
    <?php foreach ($tabContent['contents'] as $content) { ?>
        <li>
            <a id="<?php echo $content['contentID'];?>"
               href="<?php echo $content['url'] ?: '#';?>"
               class="item <?php echo $content['fileName'] ? "content-downloadable" : ''?>"
               target="_blank"
               download>
                <div class="resource-thumbnail">
                    <img class="img-resource-thumbnail" alt="<?php echo $content['fileType']; ?>">
                </div>
                <div class="resource-title">
                    <p><?php echo $content['contentName'];?></p>
                    <?php if ($content['fileName']) { ?>
                        <span>
                            <?php 
                            // SB-230 modified by machua 20190626 to display the correct filesize
                            echo $content['fileInfo'];
                            ?>                        
                        </span>
                    <?php } else { ?>
                        <span>Weblink</span>
                    <?php } ?>
                </div>
            </a>
        </li>
    <?php } ?>
<?php } ?>
