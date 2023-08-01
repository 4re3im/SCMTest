<?php
    $pkgHandle = 'institution_management';
    Loader::packageElement('commons/js_notification', $pkgHandle);
?>

<div id="confirmation-modal-content" style="display: none;">
    <p>
        Are you sure you want to add user: <br><br><span id="user-data"></span>
    </p>

    <div class="dialog-buttons">
        <button type="button" class="ccm-button-left btn" id="cancel-btn">Cancel</button>
        <button type="button" class="btn primary ccm-button-right" id="proceed-btn">Proceed</button>
    </div>
</div>

<div class="tab">
    <button class="tablinks active" id="student" onclick="openTab(event, 'Student')">Student</button>
    <button class="tablinks" id="teacher" onclick="openTab(event, 'Teacher')">Teacher</button>
    <button class="tablinks" id="teacher" onclick="openTab(event, 'Subscription')">Subscription</button>
</div>

<!-- Tab content -->
<div id="Student" class="tabcontent activeTab">
    <?php echo Loader::packageElement(
                    'review/institution_users',
                    $pkgHandle,
                    ['role' => 'student',
                        'name' => $json->name,
                        'oid' => $json->oid ]
                ); 
    ?>
</div>

<div id="Teacher" class="tabcontent">
    <?php echo Loader::packageElement(
                    'review/institution_users',
                    $pkgHandle,
                    ['role' => 'teacher',
                        'name' => $json->name,
                        'oid' => $json->oid ]
                ); 
    ?>
</div>

<div id="Subscription" class="tabcontent">
    <?php 
    echo Loader::packageElement(
                    'review/subscriptions',
                    $pkgHandle,
                    ['oid' => $json->oid,
                        'userSubscriptions' => $userSubscriptions ]
                ); 
    ?>
</div>
