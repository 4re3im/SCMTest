<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Global Tab Manager'), false, false, false) ?>

<div class="ccm-pane-body">
    <div class="ccm-pane-body-inner">
        <div class="row">
            <div class="span3">
                <h3>Global Content</h3>
                <div style="max-height:700px;overflow: scroll">
                    <table class="table table-bordered" style="cursor: pointer">
                        <tbody>
                            <?php foreach ($tabs as $t) { ?>
                                <?php if($t['ContentHeading'] || $t['CMS_Name']) { ?>

                                <tr>
                                    <td class="global-content-select"><?php echo trim(trim($t['ContentHeading']) . ' (' . trim($t['CMS_Name']) . ')'); ?>
                                        <input type="hidden" value="<?php echo $t['ID']; ?>" />
                                    </td>
                                </tr>

                                <?php } ?>
                                
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="span8">
                <fieldset id="global-content-display">
                    <legend>&nbsp;</legend>
                    <div class="alert" role="alert" style="display:none;">IN</div>
                    <img src="<?php echo DIR_REL; ?>/concrete/images/throbber_white_32.gif" id="loading" />
                    <div id="gc-display"></div>
                </fieldset>
            </div>
        </div>
    </div>
</div>