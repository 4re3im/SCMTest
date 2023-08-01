<?php 
	$c = Page::getByID($_REQUEST['destCID']);
?>

<div id="ccm-popup-select-parent">

		<p><?php echo t('You have selected the page %s. If this is correct click okay.', $c->getCollectionName())?></p>

        <div class="dialog-buttons">
            <a onclick="$.fn.dialog.closeTop();$.fn.dialog.closeAll();" href="javascript:void(0)" class="ccm-button-right btn primary accept"><?php echo t('Okay')?></a>
            <a onclick="$.fn.dialog.closeTop();" href="javascript:void(0)" class="btn"><?php echo t('Cancel')?></a>
        </div>

</div>
