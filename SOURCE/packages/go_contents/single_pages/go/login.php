<?php
defined('C5_EXECUTE') or die(_("Access Denied"));
Loader::library('authentication/open_id');
$form = Loader::helper('form');

// ANZGO-3727 Modified by Maryjes Tanada 2018-06-05 Referrer-Policy
Header('Referrer-Policy: no-referrer');

if (isset($error)) {
    $log_error = $error->getList();
}
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

  // Modified by gxbalila GCAP-531
  var onGigyaServiceReady = function () {
    gigya.accounts.getAccountInfo({
      include: 'userInfo',
      extraProfileFields: 'samlData',
      callback: function (response) {
          if (response.errorCode > 0) {
          gigya.accounts.showScreenSet({
            screenSet: '<?php echo GIGYA_REGISTRATION_LOGIN_SCREENS; ?>',
            containerID: 'gigya-login',
            startScreen: 'gigya-sp-login-screen',
            onBeforeSubmit: function () {
              showGigyaOverlay(true);
            },
            onError: function (event) {
              showGigyaOverlay(false);
              // SB-455 added by jbernardez/mabrigos 20200129
              if (event.errorCode == 400002) {
                alert('If you can see the error "Missing required parameter" please clear your cookies and try again.' +
                    'If the problem persists, please email digitalsupportau@cambridge.edu.au');
              }
            }
          });
        } else {
          loginToGo(response.userInfo);
        }
      }
    });
  };
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
