<?php
defined('C5_EXECUTE') or die(_("Access Denied"));
Loader::library('authentication/open_id');
$form = Loader::helper('form');

// ANZGO-3727 Modified by Maryjes Tanada 2018-06-05 Referrer-Policy
Header('Referrer-Policy: no-referrer');

?>
<div id="main-wrapper">
    <div id="form-wrapper">
        <div class="container">
            <div class="row">
                <div class="form-container" id="gigya-login"></div>
                <div class="login-links">
                    <a href="<?php echo $this->url('/go/terms'); ?>">Terms of
                        Use</a>
                    <a href="<?php echo $this->url('/go/privacy'); ?>">Privacy
                        Policy</a>
                    <a href="<?php echo $this->url('/go/contact'); ?>">
                        Contact Us
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
<br/>

<script>
  // Display overlay during load
  showGigyaOverlay(true);

  // Added by mabrigos 20200320
  var onGigyaServiceReady = function () {
    gigya.accounts.getAccountInfo({
        callback: function () {
            gigya.accounts.showScreenSet({
                screenSet: '<?php echo GIGYA_REGISTRATION_LOGIN_SCREENS; ?>',
                containerID: 'gigya-login',
                startScreen: 'gigya-resend-verification-screen',
                onBeforeValidation: function(event) {
                    var email = event.formData['profile.email'];
                    if (email == '') {
                        return {'profile.email': 'Please input an email address'};
                    }
                },
                onBeforeSubmit: function() {
                    showGigyaOverlay(true);
                },
                onSubmit: function(event) {
                    var error = document.getElementsByClassName('tng-resend-email-error')[1];
                    if (error) {
                        error.style.display = 'none';
                    }
                    var email = event.formModel.profile.email;
                    resendVerification(email);
                },
                onAfterScreenLoad: function() {
                  showGigyaOverlay(false);
                },
                onFieldChanged: function(event) {
                    var error = document.getElementsByClassName('tng-resend-email-error')[1];
                    if (error) {
                        error.style.display = 'none';
                    }
                }
            });
        }
    })
  };

  function resendVerification(email) {
    $.ajax({
        url: '/go/resend_verification/checkEmailIfExists',
        type: 'POST',
        dataType: 'json',
        data: {data: email},
        success: function(data) {
            showGigyaOverlay(false);
            if (data.result === false) {
                var error = document.getElementsByClassName('tng-resend-email-error')
                error[1].setAttribute('style' , 'display: block !important');
            } else {
                $.ajax({
                    url: '/go/resend_verification/resendSucess',
                    type: 'POST',
                    success: function () {
                    showGigyaOverlay(false);
                    gigya.accounts.showScreenSet({
                        screenSet: '<?php echo GIGYA_REGISTRATION_LOGIN_SCREENS; ?>',
                        containerID: 'gigya-login',
                        startScreen: 'gigya-verification-sent-screen'
                    })
                    }
                })
            }
        },
        error: function() {
            showGigyaOverlay(false);
            console.log("Error in sending the verification email. Please try again.")
        }
    })
  }
</script>

<!-- ANZGO-3791 added by jdchavez 07/09/2018 -->
<?php $announcementMode = Config::get('ANNOUNCEMENT_MODE'); ?>
<?php if ($announcementMode) { ?>
    <input id="banner-on" type="hidden">
<?php } ?>

<?php if (isset($_SESSION['redirectError'])) {
    // Once displayed, destroy so it won't appear again if ever user does not login.
    unset($_SESSION['redirectError']);
} ?>
