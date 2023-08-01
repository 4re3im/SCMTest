<?php
defined('C5_EXECUTE') or die(_("Acess Denied"));
$af = Loader::helper('form/attribute', 'go_contents');

/* Set needed attributes to display. We are using handles of created attribues */
$required = array(
    'uPassword',
    'uFirstName',
    'uLastName',
    'uSchoolName'
);

$u = new User();
$ui = UserInfo::getByID($u->getUserID());

// GCAP-289 Modified by Shane Camus 02/08/19
$isAdminOrBookseller = in_array('Administrators', $u->getUserGroups()) || in_array('Bookseller', $u->getUserGroups());
?>

<div class="header-spacer">
    &nbsp;
</div>
<div class="container">
    <div class="row">
        <div class="col-lg-8 col-lg-offset-2">
            <?php if (isset($errors)) { ?>
                <div class="row">
                    <div class="col-lg-12">
                        <br/>
                        <div class="alert alert-danger">
                            <button type="button" class="close"
                                    data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <?php
                            foreach ($errors as $e) {
                                echo $e;
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if (isset($success)) { ?>
                <div class="row">
                    <div class="col-lg-12">
                        <br/>
                        <div class="alert alert-success">
                            <button type="button" class="close"
                                    data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <?php echo $success; ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <!--GCAP-236 modified by jdchavez 01/15/19-->
            <div class="row" id="success-message" style="display: none;">
                <div class="col-lg-12">
                    <br/>
                    <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert"
                                aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <p>Your details have been updated.</p>
                    </div>
                </div>
            </div>
            <?php if (isset($contact_success)) { ?>
                <div class="row">
                    <div class="col-lg-12">
                        <br/>
                        <div class="alert alert-success">
                            <button type="button" class="close"
                                    data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <?php echo $contact_success; ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <form class="form-horizontal" method="POST" action="#"
                  id="registerForm">
                <?php // SB-560 added by jbernardez 20200518 ?>
                <?php if ($isAdminOrBookseller) { ?>
                <div class="row">
                    <div class="col-lg-12 resources-container"
                         style="padding-bottom: 5px;">
                        <h1 style="display:inline;">Account details</h1>
                        <!--GCAP-236 added by jdchavez 01/10/19-->
                        <!-- GCAP-289 Modified by Shane Camus 02/08/19 -->
                        <?php // SB-560 added by jbernardez 20200518 ?>
                        <a style="float: right; margin-top: 10px;"
                           href="<?php echo $this->url('/editaccount/password/'); ?>"
                           class="edit-account-trigger"
                           id="editAcctPassword">
                                Change Password
                        </a>
                    </div>
                </div>
                <!--GCAP-236 added by jdchavez 01/10/19-->
                <div class="modal" tabindex="-1" role="dialog" id="change-modal"
                     style="display: none;">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close"
                                        data-dismiss="modal" aria-label="Close"
                                        id="close-modal">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="generalModalLabel">
                                    Change Password</h4>
                            </div>
                            <div class="modal-body">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-lg-12"
                                             id="change-password-modal"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br/>
                <?php
                $login_attribs = AttributeSet::getByHandle('uLoginDetails');
                foreach ($login_attribs->getAttributeKeys() as $la) {
                    if (in_array($la->getAttributeKeyHandle(), $required)) {
                        echo $af->display_label($la, "&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;");
                    }
                }
                ?>
                <br/>
                <div class="row">
                    <div class="col-lg-12 resources-container"
                         style="padding-bottom: 5px;">
                        <h1 style="display:inline;">Contact details</h1>
                        <!--GCAP-236 added by jdchavez 01/10/19-->
                        <!-- GCAP-289 Modified by Shane Camus 02/08/19 -->
                        <?php // SB-560 added by jbernardez 20200518 ?>
                        <a href="<?php echo $this->url('/editaccount/contact/' . $type); ?>"
                           class="edit-account-trigger"
                           id="editAcctContact"
                           style="float: right; margin-top: 10px;"
                           resize-modal="modal-lg">
                                Edit Details
                        </a>
                    </div>
                    <br/>
                </div>
                <!--GCAP-236 added by jdchavez 01/10/19-->
                <div class="modal" tabindex="-1" role="dialog" id="edit-modal"
                     style="display: none;">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close"
                                        data-dismiss="modal" aria-label="Close"
                                        id="close-modal">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="generalModalLabel">
                                    Edit Contact Details</h4>
                            </div>
                            <div class="modal-body">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-lg-12"
                                             id="edit-details-modal"></div> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br/>
                <?php
                $login_attribs = AttributeSet::getByHandle('uLoginDetails');
                foreach ($login_attribs->getAttributeKeys() as $la) {
                    if ($la->getAttributeKeyHandle() == 'uEmail') {
                        echo $af->display_label($la, $email);
                    }
                }
                $contact_attribs = AttributeSet::getByHandle('uContactDetails');
                foreach ($contact_attribs->getAttributeKeys() as $ca) {
                    if (in_array($ca->getAttributeKeyHandle(), $required)) {
                        echo $af->display_label($ca, $ui->getAttribute($ca->getAttributeKeyHandle()));
                    }
                }

                $teacherAttribs = AttributeSet::getByHandle('uTeacherContactDetails');
                foreach ($teacherAttribs->getAttributeKeys() as $ta) {
                    if ($ta->getAttributeKeyHandle() === 'uPostcode') {
                        if (in_array($ta->getAttributeKeyHandle(), $required)) {
                            echo $af->display_label($ca, $ui->getAttribute($ca->getAttributeKeyHandle()));
                        }
                    }
                }
                ?>
                <?php // SB-560 added by jbernardez 20200518 ?>
                <?php } else { ?>
                <div>
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#contactDetails" >Edit Profile</a></li>
                        <li><a data-toggle="tab" href="#changePassword">Change Password</a></li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div id="changePassword" class="tab-pane fade">
                        <div class="row">
                            <div class="col-lg-12 resources-container"
                                 style="padding-bottom: 5px;">
                                <div class="col-lg-12" id="change-password-modal"></div>
                            </div>
                        </div>
                    </div>
                    <br/>
                    <div id="contactDetails" class="tab-pane fade in active">
                        <div class="row">
                            <div class="col-lg-12 resources-container"
                                 style="padding-bottom: 5px;">
                                <div class="col-lg-12" id="edit-details-modal"></div>
                            </div>
                            <br/>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </form>
        </div>
    </div>
</div>

<!--GCAP-236 added by jdchavez 01/10/19-->
<script>
  var group = document.getElementById('navigation-wrapper').children[2].children[1];
  var urlGroup = group.innerHTML.toLowerCase();

  // SB-156 added by machua 20190530 reload screenset upon modal show
  // GCAP-522 modified by scamus 2019
  $('#edit-modal').on('show.bs.modal', function (e) {
    gigya.accounts.showScreenSet({
      screenSet: '<?php echo GIGYA_PROFILE_UPDATE_SCREENS; ?>',
      containerID: 'edit-details-modal',
      startScreen: 'gigya-update-profile-screen',
      onAfterSubmit: onGigyaEdit,
      'context': { 'userType': urlGroup }
    });
  });

  var onGigyaEdit = function (user) {
    $.ajax({
      url: '/go/account/gigyaEditDetails',
      method: 'POST',
      data: {
        'firstName': user.profile.firstName,
        'lastName': user.profile.lastName,
        'school': user.data.eduelt.instituteRole[0].institute
      },
      async: false,
      success: function (response) {
        showGigyaOverlay(false)
        if (!response.success) {
          return false;
        }
      }
    });

    $('#edit-modal').modal('hide');

    const success = $('#success-message');
    success.css('display', 'block');

    var firstName = user.profile.firstName;
    var lastName = user.profile.lastName;
    var institute = user.data.eduelt.instituteRole[0].institute;

    var firstNameVal = document.getElementById('registerForm').children[9].children[1].children[0];
    var lastNameVal = document.getElementById('registerForm').children[10].children[1].children[0];
    var instituteVal = document.getElementById('registerForm').children[11].children[1].children[0];

    firstNameVal.innerHTML = firstName;
    lastNameVal.innerHTML = lastName;
    instituteVal.innerHTML = institute;
  };

  gigya.accounts.showScreenSet({
    screenSet: '<?php echo GIGYA_PROFILE_UPDATE_SCREENS; ?>',
    containerID: 'edit-details-modal',
    startScreen: 'gigya-update-profile-screen',
    onBeforeSubmit: function(){
      showGigyaOverlay(true)
    },
    onAfterSubmit: onGigyaEdit,
    'context': {
      'userType': urlGroup
    }
  });

  gigya.accounts.showScreenSet({
    screenSet: '<?php echo GIGYA_PROFILE_UPDATE_SCREENS; ?>',
    containerID: 'change-password-modal',
    startScreen: 'gigya-change-password-screen',
    onBeforeSubmit: function(){
      showGigyaOverlay(true)
    },
    onAfterSubmit: function (event) {
        showGigyaOverlay(false)
        if (event.response.errorCode === 0) {
            $('#success-message').show();
            $('#change-modal').modal('hide');
        }
    },
    customLang: {
      invalid_login_or_password: 'Your previous password is incorrect.',
      password_does_not_meet_complexity_requirements: ' ',
      passwords_do_not_match: 'Password does not match.'
    }
  });
</script>
