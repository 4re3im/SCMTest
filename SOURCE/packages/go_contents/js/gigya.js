var getQueryParameter = function (name, url) {
  if (!url) url = window.location.href;
  name = name.replace(/[\[\]]/g, '\\$&');
  var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
    results = regex.exec(url);
  if (!results) return null;
  if (!results[2]) return '';
  return decodeURIComponent(results[2].replace(/\+/g, ' '));
};

var loginToGo = function (user) {
  // GCAP-531 gxbalila
  // Converted to plain js ajax to be called in Gigya's Global config
  var reqParams = JSON.stringify(user);

  var xhr = new XMLHttpRequest();
  xhr.onload = function () {
    if (xhr.status >= 200 && xhr.status < 300) {
      var resp = JSON.parse(xhr.responseText);
      if (resp && resp.redirectTo) {
        window.location = resp.redirectTo;
      } else {
        console.log('loginToGo failed!');
      }
    } else {
      console.log('loginToGo failed!');
    }
  };

  xhr.open('POST', '/go/login/gigyaLogin');
  xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.send(JSON.stringify(user));
};

var onGigyaLogin = function (user) {
  // SB-144 added by jbernardez/mabrigos 20190507
  if (window.location.pathname.indexOf('login') < 0) {
    return;
  }

  user.redirect = getQueryParameter('u');

  gigya.accounts.getAccountInfo({
    callback: function (u) {
      var hasInstitution = 'data' in u;
      hasInstitution = hasInstitution && 'eduelt' in u.data;
      hasInstitution = hasInstitution && 'instituteRole' in u.data.eduelt;
      hasInstitution =
        hasInstitution && u.data.eduelt.instituteRole[0].institute !== '';

      var inRegistrationCompletion = document.querySelector(
        '#gigya-login #gigya-complete-registration-screen'
      );

      if (!hasInstitution && !inRegistrationCompletion) {
        gigya.accounts.showScreenSet({
          screenSet: GIGYA_REGISTRATION_LOGIN_SCREENS,
          containerID: 'gigya-login',
          startScreen: 'gigya-complete-school-screen',
          onAfterSubmit: function (e) {
            loginToGo(user);
            saveProgressiveProfiling(e);
          }
        });
      } else {
        loginToGo(user);
        saveProgressiveProfiling(u);
      }
    }
  });
};

var handleSSO = function (data, redirect) {
  if (typeof redirect === 'undefined') {
    redirect = true;
  }

  var ajaxes = [];
  var queryParameter = data.userHash ? '?c=' + data.userHash : '';

  for (var i = 0; i < data.sso.length; i++) {
    ajaxes.push(
      $.ajax({
        url: data.sso[i] + queryParameter,
        method: 'GET',
        crossDomain: true,
        async: false
      })
    );
  }

  $.when
    .apply($, ajaxes)
    .done(function () {
      if (redirect) {
        redirectNowTo(data.redirectTo);
      }
    })
    .fail(function () {
      if (redirect) {
        redirectNowTo(data.redirectTo);
      }
    });
};

var handleLogout = function () {
  var ssoUrls = getQueryParameter('ssoLogout');

  if (!ssoUrls) {
    return false;
  }

  if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.pathname);
  }

  ssoUrls = JSON.parse(atob(ssoUrls));
  handleSSO({ sso: ssoUrls, userHash: false }, false);
  gigya.accounts.logout();
};

var redirectNowTo = function (redirectUrl) {
  var redirectOverride = getQueryParameter('u');

  if (!redirectOverride) {
    window.location = redirectUrl;
    return;
  }

  window.location = redirectOverride;
};

function showGigyaOverlay (isShown) {
  var gigyaLoader = $('.loader');
  if (isShown) {
    $(gigyaLoader).addClass('loading');
  } else {
    $(gigyaLoader).removeClass('loading');
  }
}

// CUSTOM AUTOCOMPLETE
var currentRequest = null;

// SB-571 Added by mtanada 20200518
$(document).on('click', '.nextStepSpFlow', function (e) {
    $( '.loginEmail > input[name="username"]' ).each(function() {
        var userEmail = $( this ).val();
        if (userEmail) {
            e.preventDefault();
            $.ajax({
                url: '/go/signup/getUserIdpList',
                dataType: 'json',
                type: 'POST',
                data: {
                    email: userEmail
                },
                beforeSend: function () {
                    showGigyaOverlay(true);
                },
                success: function (data) {
                  showGigyaOverlay(false);
                    if (data.length < 1) {
                        // No IdP, show normal login screenset
                        goLogin(userEmail);
                        gigya.accounts.showScreenSet({
                            screenSet: GIGYA_REGISTRATION_LOGIN_SCREENS,
                            containerID: 'gigya-login',
                            startScreen: 'gigya-login-screen',
                            onBeforeSubmit: function () {
                                showGigyaOverlay(true);
                            },
                            onError: function (event) {
                                showGigyaOverlay(false);
                                // SB-455 added by jbernardez/mabrigos 20200129
                                if (event.errorCode == 400002) {
                                    alert('If you can see the error "Missing required parameter" please clear your ' +
                                        'cookies and try again. If the problem persists, please email ' +
                                        'digitalsupportau@cambridge.edu.au');
                                }
                            }
                        });
                    } else {
                        loginToIdp(data, userEmail);
                    }
                }
            });
        }
    });
    // Auto-populate email field for normal login screenset if no IdP is available
    function goLogin(email) {
        var emailField = $('.gigya-composite-control-loginID > input[name="username"]');
        emailField.attr('value', email);
    }

    // Logs into the IdP
    function loginToIdp(idps, email) {
        if (idps.length > 1) {
            alert('Multiple Provider detected!');
        } else {
            idps.forEach(function (idp) {
                $.ajax({
                    url: '/go/signup/campionSsoUrl',
                    type: 'POST',
                    data: {
                        email: email
                    },
                    beforeSend: function () {
                        showGigyaOverlay(true);
                    },
                    success: function (data) {
                        window.location.assign(data);
                    }
                });
            });
        }
    }
});
// end

$(document).on(
  'focus',
  'input[name="data.eduelt.instituteRole[0].institute"]',
  function () {
    $(this).attr('autocomplete', 'off');
  }
);

// SB-433 modified by mabrigos 010820
// Waits for 1 second then runs the ajax
var typingTimer;
var doneTypingInterval = 1000;

$(document).on(
  'keyup',
  'input[name="data.eduelt.instituteRole[0].institute"]',
  function () {
    var TYPE_LIMIT = 3;
    clearTimeout(typingTimer);
    // Reset to default/falsey values every time user types.
    // This is to make sure that if the user types in a school name (custom school) the fields are already set
    setOID('');
    setIsVerified(false);

    if ($(this).val().length >= TYPE_LIMIT) {
      var _this = $(this);
      typingTimer = setTimeout(function () {
        removeResults();
        currentRequest = $.ajax({
          url: '/go/signup/getInstitutions',
          dataType: 'json',
          type: 'POST',
          data: {
            keyword: $(_this).val()
          },
          beforeSend: function () {
            if (currentRequest !== null) {
              currentRequest.abort();
            }
            removeLoader();
            buildLoadingPanel(_this);
            disableSubmitBtn(true);
          },
          success: function (data) {
            buildResultPanel(data, _this);
          }
        });
      }, doneTypingInterval);
    } else {
      if (currentRequest !== null) {
        currentRequest.abort();
      }
      removeResults();
    }
  }
);

$(document).on(
  'blur',
  'input[name = "data.eduelt.instituteRole[0].institute"]',
  function () {
    if (!$('.institute-panel').hasClass('searching')) {
      disableSubmitBtn(true);
    }
  }
);

$(document).on('mouseenter', '.institute-panel', function () {
  $(this).addClass('searching');
});

$(document).on('mouseleave', '.institute-panel', function () {
  $(this).removeClass('searching');
});

function buildResultPanel (data, textInput) {
  removeLoader();
  var inputWidth = $(textInput).css('width');
  var panel = $(
    '<div class="institute-panel" style="width:' + inputWidth + '"></div>'
  );
  var panelElement = '';
  if (data.status) {
    var institutes = data.data.results;
    if (institutes.length > 0) {
      $.each(institutes, function (index, d) {
        panelElement = '';
        var oid = d.oid;
        var instituteName = d.data.name;
        var instituteAddress = d.data.formattedAddress;
        panelElement = $(
          '<p class="institute-element">' +
          instituteName +
          '<br/><span class="institute-address">' +
          instituteAddress +
          '</span></p>'
        );

        panelElement.click(function (e) {
          $(textInput).val(instituteName);
          setOID(oid);
          setIsVerified(true);
          removeResults();
          disableSubmitBtn(false);
        });

        panel.append(panelElement);
      });
    } else {
      panelElement = $(
        '<p class="institute-element-no-result">' + $(textInput).val() + '</p>'
      );
      panelElement.click(function (e) {
        $.ajax({
          url: '/go/signup/getUnvalidatedSchoolData',
          dataType: 'json',
          success: function (data) {
            var result = data.data.results[0];
            setOID(result.oid);
            setIsVerified(false);
            removeResults();
            disableSubmitBtn(false);
          }
        });
      });
      panel.append(panelElement);
    }
  } else {
    panel.html('<p class="institute-element-no-result">' + data.error + '</p>');
  }

  $(textInput)
    .parent('div')
    .append(panel);
}

function buildLoadingPanel (textInput) {
  var inputWidth = $(textInput).css('width');
  var panel = $(
    '<div class="institute-panel-loading" style="width:' +
    inputWidth +
    '"></div>'
  );
  panel.html('<p class="institute-element-loading">Searching...</p>');
  $(textInput)
    .parent('div')
    .append(panel);
}

function disableSubmitBtn (disable) {
  var submitBtn = $.find('input.gigya-input-submit');
  // For KT Session
  // var submitBtn = $(
  //   '.go-teacher-register-submit > input[type="submit"], ' +
  //   '.go-prog-profiling-school > input[type="submit"], ' +
  //   '.go-registration-complete-submit > input[type="submit"], ' +
  //   '.edit-details-submit-button > input[type="submit"]'
  // );

  $(submitBtn).prop('disabled', disable);

  if (disable) {
    $(submitBtn).val('Please wait');
    $(submitBtn).css('cursor', 'not-allowed');
  } else {
    $(submitBtn).each(function (i, e) {
      var label = '';
      var submitBtnDiv = $(e).parent('div');
      if ($(submitBtnDiv).hasClass('go-teacher-register-submit')) {
        label = 'Create account';
      } else if ($(submitBtnDiv).hasClass('edit-details-submit-button')) {
        label = 'Apply Changes';
      } else {
        label = 'Submit';
      }
      $(e).val(label);
      $(e).css('cursor', 'pointer');
    });
  }
}

function setOID (oid) {
  var oidField = $('input[name="data.eduelt.instituteRole[0].key_s"]');
  $(oidField).val(oid);
}

function setIsVerified (isVerified) {
  var isVerifiedField = $(
    'input[name="data.eduelt.instituteRole[0].isVerified"]'
  );
  $(isVerifiedField).val(isVerified);
}

function removeResults () {
  $('.institute-panel').remove();
}

function removeLoader () {
  $('.institute-panel-loading').remove();
}

// END OF CUSTOM AUTOCOMPLETE

// JS FOR REGISTRATION
var homeSchoolReq = null;
var homeSchoolData = null;
$(document).on(
  'change',
  'input[name="local.school_type"], input[name="local.institute_type"], input[name="local.institution_type"]',
  function (e) {
    var schoolName = $('input[name="data.eduelt.instituteRole[0].institute"]');
    $(schoolName).val('');
    setOID('');
    setIsVerified(false);

    $(schoolName).removeClass('gigya-empty gigya-error');
    $(schoolName)
      .siblings('span.gigya-error-msg')
      .removeClass('gigya-error-msg-active');

    var schoolType = $(this).val();
    if (schoolType === 'home_school') {
      if (currentRequest) {
        currentRequest.abort();
      }
      removeLoader();
      removeResults();
      disableSubmitBtn(false);
      disableSchoolField(true);

      if (!homeSchoolData) {
        homeSchoolReq = $.ajax({
          url: '/go/signup/getHomeSchoolData',
          dataType: 'json',
          beforeSend: function () {
            if (homeSchoolReq !== null) {
              homeSchoolReq.abort();
            }
            disableSubmitBtn(true);
          },
          success: function (data) {
            homeSchoolData = data.data.results[0];
            $(schoolName).val(homeSchoolData.data.name);
            setOID(homeSchoolData.oid);
            disableSubmitBtn(false);
          }
        });
      } else {
        $('input[name="data.eduelt.instituteRole[0].institute"]').val(
          homeSchoolData.data.name
        );
        setOID(homeSchoolData.oid);
      }
    } else {
      if (homeSchoolReq !== null) {
        homeSchoolReq.abort();
      }
      disableSchoolField(false);
    }
  }
);

function disableSchoolField (disable) {
  var schoolNameField = $(
    'input[name="data.eduelt.instituteRole[0].institute"]'
  );
  $(schoolNameField).prop('disabled', disable);
}

// TRIGGER GIGYA LOGOUT
$(document).on('click', '#logout', function (e) {
  e.preventDefault();
  var logoutRef = $(this).attr('href');
  gigya.accounts.logout({
    callback: function () {
      window.location.href = logoutRef;
    }
  });
});

// SB-516 added by mabrigos 20200317
document.oncontextmenu = function(e) {
  var el = window.event.srcElement || e.target;
  var confirmEmailField = el.getAttribute("name");
  var tp = el.tagName || '';
  if (tp.toLowerCase() == 'input' && confirmEmailField == 'local.confirmEmail') {
    return false;
  }
};

document.onpaste = function(e) {
  var el = window.event.srcElement || e.target;
  var confirmEmailField = el.getAttribute("name");
  var tp = el.tagName || '';
  if (tp.toLowerCase() == 'input' && confirmEmailField == 'local.confirmEmail') {
    return false;
  }
}

$(document).on('keydown', 'input[name="local.confirmEmail"]', function(e) {
  if (e.ctrlKey === true && (e.which == '118' || e.which == '86')) {
    e.preventDefault();
  }
}); 