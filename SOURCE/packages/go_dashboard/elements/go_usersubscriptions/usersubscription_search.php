<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-pane-body">
 
<?php
// Print tab element
echo Loader::helper('concrete/interface')->tabs($tabs);
//echo Loader::helper('concrete/interface')->tabs($tabs, false); 
?>

    <div id="ccm-tab-content-tab-1" class="ccm-tab-content">
        <?php 

            Loader::packageElement('go_users/teacher_summary', 'go_dashboard', array('teacherlist' => @$teacherlist));  
        ?>
    </div>

    <div id="ccm-tab-content-tab-2" class="ccm-tab-content">
        Tools
        <?php 
            Loader::packageElement('go_users/teacher_summary', 'go_dashboard', array('teacherlist' => @$teacherlist));  
        ?>

    </div>
 
    <div id="ccm-tab-content-tab-3" class="ccm-tab-content">
        Subscriptions
        <?php 
            Loader::packageElement('go_users/teacher_summary', 'go_dashboard', array('teacherlist' => @$teacherlist));  
        ?>

    </div>

    <div id="ccm-tab-content-tab-4" class="ccm-tab-content">
        Tracking General
        <?php 
            Loader::packageElement('go_users/teacher_summary', 'go_dashboard', array('teacherlist' => @$teacherlist));  
        ?>

    </div>

    <div id="ccm-tab-content-tab-5" class="ccm-tab-content">
        Activation Errors
        <?php 
            Loader::packageElement('go_users/teacher_summary', 'go_dashboard', array('teacherlist' => @$teacherlist));  
        ?>

    </div>



</div>




