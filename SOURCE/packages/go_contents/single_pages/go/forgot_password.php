<script>
  showGigyaOverlay(true);
</script>
<div id="main-wrapper">
    <div id="form-wrapper">
        <div class="container">
            <div class="row">
                <!-- GCAP-322 Modified by cgodoy -->
                <script>
                  var gigyaConfig;
                </script>
                <?php
                if ($doChangePassword) { ?>
                    <div id="gigya-change-password"
                         class="form-container"></div>
                    <script>
                      gigyaConfig = {
                        'screenSet': '<?php echo GIGYA_REGISTRATION_LOGIN_SCREENS; ?>',
                        'containerID': 'gigya-change-password',
                        'startScreen': 'gigya-reset-password-screen',
                        'customLang': {
                          'passwords_do_not_match': 'The password does not match'
                        }
                      };

                      gigyaConfig.onError = function (e) {
                        // Request has expired error code
                        if (e.errorCode === 403025) {
                          gigya.accounts.showScreenSet({
                            'screenSet': '<?php echo GIGYA_REGISTRATION_LOGIN_SCREENS; ?>',
                            'containerID': 'gigya-change-password',
                            'startScreen': 'gigya-reset-password-expired-screen'
                          });
                        }
                      };
                    </script>
                <?php } else { ?>
                    <div id="gigya-forgot-password"
                         class="form-container"></div>
                    <script>
                      gigyaConfig = {
                        'screenSet': '<?php echo GIGYA_REGISTRATION_LOGIN_SCREENS; ?>',
                        'containerID': 'gigya-forgot-password',
                        'startScreen': 'gigya-forgot-password-screen'
                      };
                    </script>
                <?php } ?>

                <script>
                  gigya.accounts.showScreenSet(gigyaConfig);
                </script>
                <div class="login-links">
                    <a href="<?php echo $this->url('/go/terms'); ?>">Terms of
                        Use</a>
                    <a href="<?php echo $this->url('/go/privacy'); ?>">Privacy
                        Policy</a>
                    <a href="<?php echo $this->url('/go/contact'); ?>">Contact
                        Us</a>
                </div>
            </div>
        </div>
    </div>
</div>
