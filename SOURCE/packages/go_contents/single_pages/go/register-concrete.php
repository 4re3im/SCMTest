<?php
defined('C5_EXECUTE') or die("Access Denied.");

$attribs = UserAttributeKey::getRegistrationList();

$af = Loader::helper('form/attribute', 'go_contents');
?>

<div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-6 col-lg-offset-3">
    <form method="post" id="registerForm" action="<?= $this->url('/go//register', 'do_register/' . $uType) ?>" class="form-horizontal">
        <br/>
        <div class="row resultMessage">
            <?php
            if (isset($successMsg)) {

                echo "<ul class='col-sm-6 col-sm-offset-3'>";

                foreach ($successMsg as $message) {
                    if (isset($message) || $message != "")
                        echo "<li>" . $message . "</li>";
                }

                echo "</ul>";
            }
            ?>
        </div>
        <h2 class="signup-header">Login Details</h2>
        <div class="form-group row">
            <div class="col-sm-6 col-sm-offset-3">
                <p class="italic-text"><span class="bold-text">Important:</span> You will need to activate you account using this email address and it will be your login name</p></span>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-3 sign-up-label"><?= $form->label('uEmail', t('Email')); ?></div >
            <div class="col-sm-6">
<?= $form->text('uEmail'); ?>
            </div>  
            <div class="col-sm-3 error-label">
                <label class="error" for="uEmail" generated="true"></label>
            </div> 
        </div>	
        <div class="form-group row">
            <div class="col-sm-3 sign-up-label"><?= $form->label('uPassword', t('Create Password')); ?></div >
            <div class="col-sm-6">
<?= $form->password('uPassword'); ?>
            </div>
            <div class="col-sm-3 error-label">
                <label class="error" for="uPassword" generated="true"></label>
            </div> 
        </div>
        <div class="form-group row">
            <div class="col-sm-3 sign-up-label"><?= $form->label('uPasswordConfirm', t('Verify Password')); ?></div >
            <div class="col-sm-6">
<?= $form->password('uPasswordConfirm'); ?>
            </div>
            <div class="col-sm-3 error-label">
                <label class="error" for="uPasswordConfirm" generated="true"></label>
            </div> 
        </div>
        <br/>
        <h2 class="signup-header">Contact Details</h2>

        <?php
        $set = AttributeSet::getByHandle('uContactDetails');
        $attributes = $set->getAttributeKeys();

        foreach ($attributes as $ak) {

            if ($ak->isAttributeKeyEditableOnRegister())
                echo $af->display($ak);
        }

        if ($uType == 'teacher') {

            $set = AttributeSet::getByHandle('uTeacherContactDetails');
            $attributes = $set->getAttributeKeys();

            foreach ($attributes as $ak) {

                if ($ak->isAttributeKeyEditableOnRegister())
                    echo $af->display($ak);
            }
        }
        ?>

        <div class="form-group row">
            <div class="col-sm-offset-3 col-sm-6">
                <p>Cambridge University Press and its affiliate, Cambridge HOTmaths, may occasionally send you additional product information. Cambridge University Press and Cambridge HOTmaths respect your privacy and will not pass your details on to any third party, in accordance with our privacy policy. This policy also contains information about how to access and seek correction to your personal data, or to complain about a breach of Australian Privacy Principles.</p>
            </div>
        </div>


        <div class="form-group row">
            <div class="col-sm-offset-3 col-sm-6">

                <?php
                $set = AttributeSet::getByHandle('uCheckboxes');
                $attributes = $set->getAttributeKeys();

                foreach ($attributes as $ak) {
                    ?>
                    <div class="checkbox">
                        <label>
                            <input id="akID[<?php echo $ak->akID; ?>][value]" name="akID[<?php echo $ak->akID; ?>][value]" type="checkbox"><?php echo $ak->akName; ?>
                        </label>
                    </div>

                    <?php
                }

                if ($uType == 'teacher') {

                    $set = AttributeSet::getByHandle('uTeacherCheckboxes');
                    $attributes = $set->getAttributeKeys();

                    foreach ($attributes as $ak) {
                        ?>
                        <div class="checkbox">
                            <label>
                                <input id="akID[<?php echo $ak->akID; ?>][value]" name="akID[<?php echo $ak->akID; ?>][value]" type="checkbox"><?php echo $ak->akName; ?>
                            </label>
                        </div>

                    <?php
                    }
                }
                ?>	
                <div class="checkbox">
                    <label id="uTermsLabel">
                        <input type="checkbox" id="uTerms" name="uTerms"> You must accept the <a href="#">Terms Of Use</a> to register.
                    </label>
                </div>
                <br/>
                <center>
                    <?= $form->hidden('rcID', $rcID); ?>
<?= $form->submit('register', t('Submit'), array('class' => 'btn main lrg')) ?>
                </center>
            </div>
        </div>

    </form>
</div>