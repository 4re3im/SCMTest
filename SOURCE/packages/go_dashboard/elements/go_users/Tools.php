<style>
    .panel { margin:  0px 8px; height: 100%; }

    .panel-default {
        border:  1px solid #ccc;
        padding: 10px;
        margin:  8px;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        border-radius: 5px 5px 5px 5px;
        min-height: 100px;
    }

    .panel-default .activation-header   {
        padding-bottom: 5px;
        border-bottom:  1px solid #333;
    }

    .panel-default .activation-header span  {
        /*padding: 10px 0;*/
        /*font-size: 14px;*/
        /*border-bottom:  1px solid #333;*/
    }

    .email-content {
        padding: 5px 0;
    }

</style>

<?php
    Loader::helper('user_validation_hashes', 'go_dashboard');
    $uh = Loader::helper('concrete/urls');

    $u = new GoDashboardGoUsers();
    $data = $u->getUserInfo($user[0]->uID);

    $ui = UserInfo::getByID($user[0]->uID);
    $uHash = $ui->setupValidation();


    /**
     * ANZGO-3169 Added by: jsunico@cambridge.org, July 26, 2017
     * Generate password reset link
     */

    $https = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $activationLink = "http://" . $_SERVER['HTTP_HOST'] . View::url('/go/login', 'v', $uHash);

    $rHash = UserValidationHashesHelper::getHashByUserID($user[0]->uID, UVTYPE_CHANGE_PASSWORD);
    $resetLink = $https . $_SERVER['HTTP_HOST'] . View::url('/go/forgot_password', 'change_password', $rHash);
?>

<div id='userToolsActivationLink'>

    <div class="panel-default" style="height:100%">
        <p class='header'><strong>Activation Link</strong></p>
        <?php echo  $activationLink;  ?>
    </div>

</div>


<!-- ANZGO-3169 Added by: jsunico@cambridge.org, July 26, 2017-->
<!-- Generate password reset link-->

<div id='userToolsResetLink'>
    <div class="panel-default" style="height:100%">
        <div>
            <p class='header'><strong>Reset Link</strong></p>
            <div id="alert" class="alert alert-success hide" role="alert"></div>
            <input id="resetInputLink" style="width: 92%;" value="<?php echo $resetLink ?>" readonly>
            <a id="copy" data-target="resetInputLink" href="#" class="btn btn-small btn-default">Copy</a>
        </div>
        <div style="margin-top: 5px;">
            <input id="resetEmail" value="<?php echo $user[0]->uEmail ?>" class="hide">
            <a id="generateResetPassword" href="#" class="btn btn-small btn-primary">Generate Reset Password Link</a>
        </div>
    </div>
</div>

<div id='userToolsActivationEmail'>
    <div class="panel-default" style="height:100%">
        <div class='activation-header'>
            <span>
                <strong>Activation Email</strong>
            </span>
            <!-- TODO: -->
<!--             <span>
                <a href="javascript:void(0)" onclick="askAboutAPage()" class="ccm-button-right btn primary accept"><?php echo t('Edit Email Content')?></a>
            </span>
 -->        </div>

        <div style="clear:both;"></div>
        <!-- TODO: This should be editable in-place -->

        <div class='email-content'>
            <p>Hi <?php echo $data['ak_uFirstName']; ?>, </p>
            <p>It's possible the email may have been identified as spam.</p>
            <p>Below is a copy of the email you should have received.</p>
            <p>Thank-you for your patience.</p>

            <p class='spaced-p'>***** ACTIVATION EMAIL ****</p>

            <p>Hi <?php echo $data['ak_uFirstName']; ?>, </p>
            <p>Thank you for registering with Cambridge GO.</p>
            <p>Your account has been created.</p>
            <p>To activate your account, open the following link:</p>
            <p> <a href="<?php echo $activationLink; ?>" target="_blank"> <?php echo $activationLink ?> </a></p>
            <p>If you are unable to click the link, simply copy and paste it into your web browser.</p>
            <p>The Cambridge GO team.</p>
        </div>
    </div>
</div>


<script type="text/javascript">
    function askAboutAPage() {
        $.fn.dialog.open({
            href: "<?php echo $uh->getToolsURL('activation_email')?>",
            title: "<?php echo t('Edit Activation Email Content')?>",
            width: 550,
            modal: true,
            appendButtons: true,
            //onOpen:function(){},
            //onClose: function(){$('#ccm-core-commerce-product-add-form').get(0).submit()},
            height: 480
        });
    }

    /**
     * ANZGO-3169 Added by: jsunico@cambridge.org, July 26, 2017
     * Generate password reset link
     */

    $('#generateResetPassword').on('click', function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            dataType: "json",
            data: {uEmail: $('#resetEmail').val()},
            url: "/dashboard/go_users/generateResetPasswordLink/",
            success: function(data){
                console.log(data);
                if(data.success) {
                    console.log(data.success);
                    $('#resetInputLink').val(data.resetLink);
                    popupMessage('<strong>Reset link</strong> has been created.', true, '#alert');
                }
            },
            error:function (xhr, ajaxOptions, thrownError){
                popupMessage('<strong>Error!</strong> Something wen\'t wrong.', copied, '#alert');
            }

        });
    });

    /**
     * ANZGO-3169 Added by: jsunico@cambridge.org, July 26, 2017
     * Copy field
     */

    $('#copy').on('click', function(e) {
        e.preventDefault();
        var target = $(this).data('target');
        $('#' + target).focus();
        $('#' + target).select();
        var copied = document.execCommand('copy');

        if (copied) {
            popupMessage('<strong>Reset link</strong> has been copied to clipboard.', copied, '#alert');
        } else {
            popupMessage('<strong>Error!</strong> Copy is not supported. Copy manually instead.', copied, '#alert');
        }
    });

    /**
     * ANZGO-3169 Added by: jsunico@cambridge.org, July 26, 2017
     * Copy field.
     */

    $('#resetInputLink').on('click', function() {
        $(this).select();
        document.execCommand('copy');
    });

    function popupMessage(message, success, target, autohide=true) {
        $(target).removeClass('hide').fadeIn();
        if (success) {
            $(target).removeClass('alert-danger').addClass('alert-success');
            $(target).html(message);
        } else {
            $(target).removeClass('alert-success').addClass('alert-danger');
            $(target).html(message);
        }

        if (autohide) {
            setTimeout(function() {
                $('#alert').fadeOut();
            }, 2000);
        }
    }
</script>
