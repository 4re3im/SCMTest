<?php
defined('C5_EXECUTE') || die(_("Access Denied."));
$af = Loader::helper('form/attributeLogin', 'go_contents');

// ANZGO-1872
$countriesHelper = Loader::helper('lists/countries', 'go_contents');
$countries = $countriesHelper->getCountries();

$u = new User();
if (!$uType) {
    $uType = $u->uGroups[4];
}
?>
<!--added by jdchavez-->
<style>
    .noclick {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        height: 10000px;
        width: 10000px;
        z-index: 20000; /* or maybe higher */
        background-color: transparent;
    }
</style>

<!--added by jdchavez-->
<div id="main-wrapper">
    <div id="form-wrapper">
        <div class="container">
            <div class="row">
                <div class="form-container gigya-signup-bg" id="gigya-signup">
                </div>

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
<?php //SB-499 mabrigos removed modal for confirmation email  ?>
<!-- added by jdchavez-->
<script>
  // Display overlay during load
  showGigyaOverlay(true);

  var url = window.location.href; // get url using window.location
  var urlArray = url.split('/');// explode to get last item in array from string
  var urlGroup = urlArray[5]; // get if teacher or student

  var gigyaConfig = {
    'screenSet': '<?php echo GIGYA_REGISTRATION_LOGIN_SCREENS; ?>',
    'containerID': 'gigya-signup',
    'startScreen': 'gigya-register-screen',
    'customLang': {
      email_address_is_invalid: 'Invalid format of email address.',
      this_field_is_required: 'This field is required.',
      passwords_do_not_match: 'The password does not match.'
    },
    'context': {
      'userType': urlGroup
    }
  };

  gigyaConfig.onAfterSubmit = function (e) {
    var subscriptions = [].slice.call(
      document
        .querySelectorAll('.go-teacher-registration.go-teacher-register-subjects-checkboxes input[type="checkbox"]:checked')
    )
      .map(function (checkbox) {
        return checkbox.parentElement.querySelector('label span').innerText.trim();
      });

    var userData = {
      UID: e.response.userInfo.UID,
      UIDSignature: e.response.userInfo.UIDSignature,
      signatureTimestamp: e.response.userInfo.signatureTimestamp,
      data: e.response.data,
      subscriptions: subscriptions
    };

    $.ajax({
      url: '/go/signup/registerGigyaUser/',
      type: 'POST',
      data: userData,
      async: false,
      success: function () {
        showGigyaOverlay(false);
      }
    });
  };

  gigya.accounts.showScreenSet(gigyaConfig);

</script>
