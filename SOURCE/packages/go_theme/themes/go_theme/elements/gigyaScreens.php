<div id="gigyaScreenset-container" class="hidden">
    <div id="form-wrapper">
        <div class="container">
            <div class="row">
                <style>
                    label {
                        text-transform: unset;
                    }

                    #main-wrapper h1, #main-wrapper h2 {
                        font-family: "Muli", sans-serif;
                    }

                    #banner-wrapper {
                        width: 100%;
                        position: relative;
                        background-color: #E5EBF1;
                        font: 400 14px/1 "Muli", sans-serif;
                        padding-top: 180px;
                        padding-bottom: 137px;
                    }

                    #banner-wrapper .banner-content, #banner-wrapper h1 {
                        font: 400 14px/1 "Muli", sans-serif;
                        text-align: center;
                    }

                    #main-wrapper {
                        min-height: unset;
                    }

                    #carousel-wrapper {
                        background-color: #f2f7fb;
                        position: relative;
                        padding: 10rem 0 8rem;
                        font: 400 14px/1 "Muli", sans-serif;
                        overflow: hidden;
                    }

                    #resources-wrapper * {
                        font-family: sans-serif;
                    }

                    #carousel-wrapper .item .carousel-caption h2 {
                        font-size: 30px;
                        font-weight: 700;
                        line-height: 40px;
                    }

                    .noclick {
                        position: absolute;
                        top: 0;
                        right: 0;
                        bottom: 0;
                        left: 0;
                        height: 10000px;
                        width: 10000px;
                        z-index: 20000;
                        background-color: transparent;
                    }

                    /*
 * Autocomplete styling
 */
                    .institute-panel, .institute-panel-loading {
                        margin-top: -13px;
                        border: 1px solid #a1aacc;
                        border-radius: 3px;
                        width: 445px;
                        position: absolute;
                        z-index: 1;
                        background-color: white;
                        font: 400 14px/1 'Muli', sans-serif;
                        overflow-y: scroll;
                        max-height: 500px;
                    }

                    .institute-element, .institute-element-no-result, .institute-element-loading {
                        margin: 0;
                        border: 1px solid #a1aacc;
                        padding: 3px;
                        font-weight: bold;
                    }

                    .institute-element {
                        cursor: pointer;
                    }

                    .institute-element:hover {
                        background-color: #b6bdc5;
                    }

                    .institute-address {
                        font-size: 11px;
                        font-color: lightgray;
                        font-weight: normal;
                    }

                    #gigya-forgot-password {
                        margin-top: 160px;
                    }

                    body > div#gigya-login_content {
                        display: none;
                    }
                </style>

                <div class="form-container hidden" id="gigya-login"></div>

                <div class="form-container gigya-signup-bg hidden"
                     id="gigya-signup"></div>

                <div id="gigya-forgot-password"
                     class="form-container hidden"></div>

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

<div class="modal" tabindex="-1" role="dialog" id="sample-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close" id="close-modal"
                        onclick="closeModal();">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="generalModalLabel">Confirm
                    Email</h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <p>Please confirm that your email is correct:</p>
                            <h4 class="text-center" id="email_name">
                                <strong><span id="email_field"></span></strong>
                            </h4>
                            <p>If your email address has not been typed
                                correctly, you will not be able to activate your
                                account.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left"
                        data-dismiss="modal" id="cancel-signup"
                        onclick="closeModal();">
                    There's a mistake, let me fix it.
                </button>
                <!--    ANZGO-3819 modified by jdchavez 08/14/18-->
                <input type="button"
                       class="btn btn-success pull-right"
                       id="submit-signup"
                       value="My email is correct, proceed"
                       onclick="setMetadataValue();"/>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="modal-flag" value="0"/>


<script>
  gigya.accounts.showScreenSet({
    screenSet: '<?php echo GIGYA_REGISTRATION_LOGIN_SCREENS; ?>',
    containerID: 'gigya-login',
    startScreen: 'gigya-sp-login-screen'
  });

  gigya.accounts.showScreenSet({
    'screenSet': '<?php echo GIGYA_REGISTRATION_LOGIN_SCREENS; ?>',
    'containerID': 'gigya-forgot-password',
    'startScreen': 'gigya-forgot-password-screen'
  });

  var registationConfig = {
    screenSet: '<?php echo GIGYA_REGISTRATION_LOGIN_SCREENS; ?>',
    containerID: 'gigya-signup',
    startScreen: 'gigya-register-screen',
    customLang: {
      email_address_is_invalid: 'Invalid format of email address.',
      this_field_is_required: 'This field is required.',
      passwords_do_not_match: 'The password does not match.'
    },
    context: {
      'userType': null
    }
  };

  var registrationOnAfterSubmit = function (e) {
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
      async: false
    });
  };

  gigya.socialize.addEventHandlers({
    onLogin: onGigyaLogin
  });

  //  Registration Gigya
  function setMetadataValue () {
    const modal = $('#sample-modal');
    modal.css('display', 'none');

    var flagInput = $('#modal-flag');
    $(flagInput).val('1');
    console.log($(flagInput).val());

    setTimeout(function () {
      $('.gigya-input-submit').click();
    }, 1000);
  }

  function closeModal () {
    const modal = $('#sample-modal');
    modal.css('display', 'none');
  }

  // End of Registration

  function hideAll () {
    var elements = document
      .querySelectorAll('#main-content div[id^="HTMLBlock"], #gigya-login, #gigya-signup, #gigya-forgot-password, #main-footer');

    for (var i = 0; i < elements.length; i++) {
      elements[i].classList.add('hidden');
    }

    window.scrollTo(0, 0);
  }

  function showElementById (id) {
    document
      .getElementById(id)
      .classList
      .remove('hidden');
  }

  function pushHistory (title, url) {
    if (!('pushState' in window.history)) {
      return false;
    }

    var state = {
      body: document.getElementById('main-content').innerHTML,
      path: window.location.pathname
    };

    window.history.replaceState(state, title, url);

    document.title = title;
  }

  $(document).on('click', '[href^="/go/login"]', function (e) {
    e.preventDefault();

    hideAll();
    showElementById('gigyaScreenset-container');
    showElementById('gigya-login');

    gigya.accounts.showScreenSet({
      screenSet: '<?php echo GIGYA_REGISTRATION_LOGIN_SCREENS; ?>',
      containerID: 'gigya-login',
      startScreen: 'gigya-sp-login-screen'
    });

    pushHistory('Cambridge GO :: Login', '/go/login/');
  });

  $(document).on('click', '[href^="/go/signup/student"]', function (e) {
    e.preventDefault();

    hideAll();
    showElementById('gigyaScreenset-container');
    showElementById('gigya-signup');

    var studentRegistration = JSON.parse(JSON.stringify(registationConfig));
    studentRegistration.context.userType = 'student';
    studentRegistration.onAfterSubmit = registrationOnAfterSubmit;
    gigya.accounts.showScreenSet(studentRegistration);

    pushHistory('Cambridge GO :: Student Signup', '/go/signup/student/');
  });

  $(document).on('click', '[href^="/go/signup/teacher"]', function (e) {
    e.preventDefault();

    hideAll();
    showElementById('gigyaScreenset-container');
    showElementById('gigya-signup');

    var teacherRegistration = JSON.parse(JSON.stringify(registationConfig));
    teacherRegistration.context.userType = 'teacher';
    teacherRegistration.onAfterSubmit = registrationOnAfterSubmit;
    gigya.accounts.showScreenSet(teacherRegistration);

    pushHistory('Cambridge GO :: Teacher Signup', '/go/signup/teacher/');
  });

  $(document).on('click', '[href^="/go/forgot_password"]', function (e) {
    e.preventDefault();

    hideAll();
    showElementById('gigyaScreenset-container');
    showElementById('gigya-forgot-password');

    gigya.accounts.showScreenSet({
      'screenSet': '<?php echo GIGYA_REGISTRATION_LOGIN_SCREENS; ?>',
      'containerID': 'gigya-forgot-password',
      'startScreen': 'gigya-forgot-password-screen'
    });

    pushHistory('Cambridge GO :: Forgot Password', '/go/forgot_password/');
  });

  window.addEventListener('load', function (e) {
    if ('replaceState' in window.history) {
      var state = {
        body: document.getElementById('main-content').innerHTML,
        path: window.location.pathname
      };

      window.history.replaceState(
        state,
        document.title,
        window.location.pathname
      );
    }
  });
</script>
