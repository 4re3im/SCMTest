<?php
/**
 * Search and display product subjects UI
 * @author Ariel Tabag <atabag@cambridge.org>
 * @author Paul Gerard Balila <pbalila@cambridge.org>
 * March 25, 2015
 */
defined('C5_EXECUTE') or die(_("Access Denied"));

$region_array = array(
    '0'                     => 'Select Region',
    'Australia'             => 'Australia',
    'New South Wales'       => 'New South Wales',
    'New Zealand'           => 'New Zealand',
    'Northern Territory'    => 'Northern Territory',
    'Queensland'            => 'Queensland',
    'South Australia'       => 'South Australia',
    'Tasmania'              => 'Tasmania',
    'Victoria'              => 'Victoria',
    'Western Australia'     => 'Western Australia',
);

$year_level_array = array(
    '0'     => 'Select Year ',
    '7'     => 'Year 7',
    '8'     => 'Year 8',
    '9'     => 'Year 9',
    '10'    => 'Year 10',
    '11'    => 'Year 11',
    '12'    => 'Year 12'
);

$region = ($region) ? $region : 0;
$year_level = ($year_level) ? $year_level : 0;
// SB-102 added by mabrigos 2019/03/20 Reinstated breadcrumbs 
global $u;
$ug = $u->getUserGroups();
$form = Loader::helper('form');


?>
<script> var subject_url = "<?php echo BASE_URL . $this->url($curent_page); ?>"</script>
<script> var base_url = "<?php echo BASE_URL . DIR_REL . '/go/subject/'; ?>"</script>
<script> var series_url = "<?php echo BASE_URL . $this->url($series_page); ?>"</script>
<script> var subject_page = "<?php echo $subject_page; ?>"</script>
<script> var series_page = "<?php echo $series_page; ?>"</script>
<script> var pretty_url = "<?php echo $pretty_url; ?>"</script>
<script> var current_series = "<?php echo $current_series; ?>"</script>

<?php if ($noSubject) { ?>
<div class="container-fluid">
    <?php // SB-346 added by jbernardez 20190919 Reinstated breadcrumbs ?>
    <div id="breadcrumbs-wrapper">
        <div class="container" style="margin-left: 20px !important;">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumbs">
                        <ol>
                            <?php if ($u->isLoggedIn()) { ?>
                                <?php if ($ug[3] == "Administrators") { ?>
                                    <li><a href="/go/">Home</a></li>
                                    <?php if ($_SESSION['visitedMyResources']) { ?>
                                        <li><a href="/go/myresources/">My resources</a></li>
                                    <?php } ?>
                                <?php } else { ?>
                                    <li><a href="/go/myresources/">My resources</a></li>
                                <?php } ?>
                            <?php } else { ?>
                                <li><a href="/go/">Home</a></li>
                            <?php } ?>
                            <?php if (strlen($current_series) > 1) { ?>
                                <li><a href="<?php echo $breadcrumb['subject']['url']; ?>"><?php echo $breadcrumb['subject']['title'] ?></a></li>
                                <li><a class="active"><?php echo $title ?></a></li>
                            <?php } else { ?>
                                <li><a class="active"><?php echo $title ?></a></li>
                            <?php } ?>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 col-xs-12"
    style="margin-bottom:20px; margin-top:-10px;">
    <div class="tip-box">
        <p style="text-align: center; padding-bottom: 20px;">
            <br />
            This product is currently unavailable. Contact your Customer service 1800 005 210 or your <code><a href="/education/about/contact-us/" target="_blank">Education Resource Consultant</a></code>.
        </p>
    </div>
</div>
<?php 
} else { 
?>
<div class="container-fluid">
    <!-- SB-102 added by mabrigos 2019/03/20 Reinstated breadcrumbs -->
    <div id="breadcrumbs-wrapper">
        <div class="container" style="margin-left: 20px !important;">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumbs">
                        <ol>
                            <?php if ($u->isLoggedIn()) { ?>
                                <?php if ($ug[3] == "Administrators") { ?>
                                    <li><a href="/go/">Home</a></li>
                                    <?php if ($_SESSION['visitedMyResources']) { ?>
                                        <li><a href="/go/myresources/">My resources</a></li>
                                    <?php } ?>
                                <?php } else { ?>
                                    <li><a href="/go/myresources/">My resources</a></li>
                                <?php } ?>
                            <?php } else { ?>
                                <li><a href="/go/">Home</a></li>
                            <?php } ?>
                            <?php if (strlen($current_series) > 1) { ?>
                                <li><a href="<?php echo $breadcrumb['subject']['url']; ?>"><?php echo $breadcrumb['subject']['title'] ?></a></li>
                                <li><a class="active"><?php echo $title ?></a></li>
                            <?php } else { ?>
                                <li><a class="active"><?php echo $title ?></a></li>
                            <?php } ?>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-wrapper">
            <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <h1 class="go-search-head"><?php echo $title; ?></h1>
                        <input id="current_subject" type="hidden" value="<?php echo $current_subject; ?>">
                    </div>
                </div>
                <br />
                
                <div class="row text-center">
                    <div class="col-lg-4 col-md-4 col-sm-4">
                        <label>Refine by Subject</label>
                        <?php // echo $form->select('subject',$subject_list,$_SESSION['subject_pretty_url'],array('class'=>'form-control go-input','isOpen'=>0)) ?>
                        <div class="go-subj-dropdown">
                            <a href="#" class="btn btn-default" id="subject"> 
                                <span class="glyphicon glyphicon-triangle-bottom pull-right" aria-hidden="true"></span>
                                <span class="val-container"><?php echo $subject_list[$current_subject]; ?></span>
                                <input type="hidden" value="<?php echo $current_subject; ?>" />
                            </a>

                            <ul id="arrange">
                                <?php foreach ($subject_list as $s_key => $s_val) { ?>
                                    <li style="cursor: pointer;"><?php echo $s_val; ?>
                                        <input type="hidden" value="<?php echo $s_key?>" />
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                        <label>Refine by Region</label>
                        <?php // echo $form->select('region', $region_array, $region, array('class'=>'form-control go-input')) ?>
                        <div class="go-subj-dropdown">
                            <a href="#" class="btn btn-default" id="region">
                                <span class="glyphicon glyphicon-triangle-bottom pull-right" aria-hidden="true"></span>
                                <span class="val-container"><?php echo $region_array[$region] ?></span>
                                <input type="hidden" value="<?php echo $region; ?>"/>
                            </a>
                            <ul id="arrange">
                                <?php foreach ($region_array as $r_key => $r_val) { ?>
                                    <li><span><?php echo $r_val; ?></span>
                                        <input type="hidden" value="<?php echo $r_key; ?>" />
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                        <label>Refine by Year level</label>
                        <?php // echo $form->select('year_level', $year_level_array, $year_level, array('class'=>'form-control go-input')) ?>
                        <div class="go-subj-dropdown">
                            <a href="#" class="btn btn-default" id="year_level">
                                <span class="glyphicon glyphicon-triangle-bottom pull-right" aria-hidden="true"></span>
                                <span class="val-container"><?php echo $year_level_array[$year_level]; ?></span>
                                <input type="hidden" value="<?php echo $year_level; ?>" />
                            </a>
                            <ul id="arrange">
                                <?php foreach ($year_level_array as $y_key => $y_val) { ?>
                                    <li><?php echo $y_val; ?>
                                        <input type="hidden" value="<?php echo $y_key; ?>" />
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>

                </div>
                
                <br />
                <br />
            </div>
        </div>
    </div>
    <?php echo $search_list; ?>
</div>
<?php } ?>