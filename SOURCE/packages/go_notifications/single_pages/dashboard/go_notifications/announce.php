<?php defined('C5_EXECUTE') or die("Access Denied.");
$h = Loader::helper('concrete/dashboard');
$ih = Loader::helper('concrete/interface');
$form = Loader::helper('form');
echo $h->getDashboardPaneHeaderWrapper(t('Announcement Banner Settings'), false, 'span6 offset3', false);?>
<form id="announce-mode-form" action="<?php echo $this->action('save')?>" method="post">
<div class="ccm-pane-body">
    <?php if (!empty($token_error) && is_array($token_error)) { ?>
    <div class="alert-message error"><?php echo $token_error[0]?></div>
    <?php } ?>
    <div class="clearfix">
        <?php echo $form->label('announcement_banner', t('Announcement Banner'))?>
        <div class="input">
            <ul class="inputs-list">
                <li>
                    <label>
                        <?php echo $form->radio('isActive', '1', $isActive)?>
                        <span><?php echo t('Enabled')?></span>
                    </label>
                </li>
                <li>
                    <label>
                        <?php echo $form->radio('isActive', '0', $isActive)?>
                        <span><?php echo t('Disabled')?></span>
                    </label>
                </li>
            </ul>
        </div>
    </div>

    <!-- SB-972 modified by mabrigos 03-11-21 -->
    <div class="clearfix">
        <?php echo $form->label('banner_message', t('Banner Message'))?>
        <div class="input">
            <ul class="inputs-list">
                <li>
                    <label>
                        <?php echo $form->textarea('content', $bannerMessage); ?>
                    </label>
                </li>     
           </ul>
        </div>
    </div>
    <div class="clearfix">
        <?php echo $form->label('countries', t('Country selection'))?>
        <div class="input">
            <div style=' margin-bottom:5px;'>
                <?php Loader::packageElement('countries', 'go_notifications'); ?>
                <input id="addCountry" type="button" name="view" class="btn primary" value="Add">
            </div>
            <textarea name="countryArray" id="countryArray" cols="30" rows="4"><?php echo $country ?></textarea>
        </div>
    </div>
    <div class="clearfix" id="error" style="display:none;">
        <div class="alert-message error" id="error-message"></div>
    </div>
    <div class="clearfix">
        <?php echo $form->label('enable_default', t('Enable default'))?>
        <div class="input">
            <ul class="inputs-list">
                <li>
                    <label>
                        <?php echo $form->checkbox('enableDefault', '1', $defaultMode)?>
                    </label>
                </li>
            </ul>
        </div>
    </div>

    <div class="default" style="display:none">
        <hr>
        <div class="clearfix">
            <?php echo $form->label('defaultMsg', t('Default Banner Message'))?>
            <div class="input">
                <ul class="inputs-list">
                    <li>
                        <label>
                            <?php echo $form->textarea('defaultMsg', $default_content); ?>
                        </label>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>
<div class="ccm-pane-footer">
<?php
    $submit = $ih->submit( t('Save'), 'save', 'right', 'primary');
    print $submit;
?>
</div>
</form>
<?php echo $h->getDashboardPaneFooterWrapper(false);?>