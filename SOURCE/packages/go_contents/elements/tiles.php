<?php
// ANZGO-3947 Added by Shane Camus 12/10/18
// ANZGO-3990 Modified by Shane Camus 01/08/19
defined('C5_EXECUTE') || die(_('Access Denied.'));

// SB-15 added by machua 20190124
$isSafari12orHigher = false;
$userAgent = $_SERVER['HTTP_USER_AGENT'];
$userAgentInfoArr = explode('/', $userAgent);
$uAgentSize = count($userAgentInfoArr);
$browserInfo = $userAgentInfoArr[$uAgentSize - 2];

$browserInfoArr = explode('.', $browserInfo);
if (strpos($browserInfo, 'Safari') !== false
    && strpos($userAgent, 'Macintosh') !== false
    && strpos($userAgent, 'Chrome') === false
    && (int)$browserInfoArr[0] >= 12) {
    $isSafari12orHigher = true;
}

foreach ($tiles as $id => $tile) {
    $tabName = strtolower(trim($tile['name']));
    // SB-305 modified by machua 20190823 to save tabID for weblinks
    $tileID = strcmp($tabName, 'weblinks') === 0 ? $tile['productID'] . '-' . $id : $id;

    if (!$tile['isActive'] || !$tile['isResourceLink']) {
        continue;
    }

    // SB-249 mabrigos 20190710 - added else if for coming soon tabs. 
    if ($tile['isExpired'] || $tile['isDeactivated']) {
        $validity = 'Expired';
    } elseif (is_null($tile['endDate'])) {
        $validity = '';
    } elseif ($tile['ComingSoon']) {
        $validity = 'Coming soon';
    } else {
        $validity = 'Valid until ' . $tile['endDate'];
    }

    // SB-249 mabrigos 20190710 - added condition for coming soon tabs.
    if ($tile['isExpired'] || $tile['isDeactivated'] || $tile['ComingSoon']) { ?>
        <a href="#" class="resource-item expired">

            <?php // SB-9 added by jbernardez/jdemellites 20191105 ?>
            <?php if ($tile['source'] === 'HOTMATHS') { ?>
            <button type="button" class="delete-resource-delete-btn" data-toggle="modal" data-target="#deleteModal<?php echo $tile['brandCode']; ?>">
                <i class="glyphicon glyphicon-remove"></i>
            </button>

            <!-- Modal -->
            <div class="modal fade" id="deleteModal<?php echo $tile['brandCode']; ?>" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"></button>
                            <h4 class="modal-title">Delete resource?</h4>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete this expired resource from your account? Once deleted it will be removed from your resources.</p>
                            <p>You can add this product to your account again by activating a new code.</p>
                        </div>
                        <div class="modal-footer">
                            <div class="delete-resource-buttons">
                                <button type="button" class="btn btn-outline-primary" data-dismiss="modal">No, keep resource</button>
                                <button type="button" class="btn btn-primary delete-resource" data-dismiss="modal" value="<?php echo $tile['deleteURL']; ?>">Yes, delete resource</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <?php } ?>
    <?php } elseif ($tile['hasModal']) { ?>
        <a id="<?php echo $tileID ?>"
            href="#"
            class="resource-item resource-has-modal"
            data-toggle="modal"
            data-target="#tiles-list">
    <?php } elseif ($tile['resourceURL'] !== '' || is_null($tile['resourceURL'])) {
            // SB-15 added by machua 20190124
            if ($isSafari12orHigher && strpos($tile['resourceURL'], 'go/epub/preview_content') !== false) { ?>
                <a href="<?php echo $tile['resourceURL'] ?>" class="resource-item">
            <?php } else { ?>
                <a href="<?php echo $tile['resourceURL'] ?>" class="resource-item" target="_blank">
            <?php }
        } else { ?>
            <a href="#" class="resource-item">
        <?php } ?>
                <div class="resource-thumbnail">
                    <img src="<?php echo $tile['tileIcon']; ?>" alt="<?php echo $tile['tabName']; ?>">
                </div>
                <div class="resource-info">
                    <span class="validity">
                            <?php echo $validity; ?>
                    </span>
                    <p class="resource-title"><?php echo $tile['name']; ?></p>
                </div>
            </a>
<?php } ?>
