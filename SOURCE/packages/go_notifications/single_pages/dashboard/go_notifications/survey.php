<?php defined('C5_EXECUTE') or die("Access Denied.");
$h = Loader::helper('concrete/dashboard');
$ih = Loader::helper('concrete/interface');
$form = Loader::helper('form');
echo $h->getDashboardPaneHeaderWrapper(t('Survey Settings'), false, 'span6 offset3', false);?>
<form id="announce-mode-form" action="<?php echo $this->action('save')?>" method="post">
<div class="ccm-pane-body">
    <?php if (!empty($token_error) && is_array($token_error)) { ?>
    <div class="alert-message error"><?php echo $token_error[0]?></div>
    <?php } ?>
    <div class="clearfix">
        <?php echo $form->label('survey_popup', t('Survey Pop Up'))?>
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
        <?php echo $form->label('url', t('URL'))?>
        <div class="input">
            <label>
                <?php echo $form->text('content', $url); ?>
            </label>
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
        <?php echo $form->label('role', t('Role selection'))?>
        <div class="input">
            <div style=' margin-bottom:5px;'>
                <select id="role" name='role' style='width:66%;'>
                    <option <?php echo $role === 'ALL' ? 'selected' : '' ?> value="ALL">All</option>
                    <option <?php echo $role === 'student' ? 'selected' : '' ?> value="student">Student</option>
                    <option <?php echo $role === 'teacher' ? 'selected' : '' ?> value="teacher">Teacher</option>
                    <option <?php echo $role === 'admin' ? 'selected' : '' ?> value="admin">Admin</option>
                </select>
            </div>
        </div>
    </div>

    <div class="clearfix">
        <?php echo $form->label('cookie', t('Interim period (days)'))?>
        <div class="input">
            <label>
                <input type="number" id="cookie" name="cookie" min="0" value='<?php echo $cookie; ?>'>
            </label>
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