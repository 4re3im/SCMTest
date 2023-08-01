<?php 
// SB-346 added by jbernardez 20190919
global $u;
$ug = $u->getUserGroups();
?>
<!-- ANZGO-3691 added by jbernardez 20180417 -->
<?php if ($noSeries) { ?>
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
    include_once 'subject.php';
}
?>
