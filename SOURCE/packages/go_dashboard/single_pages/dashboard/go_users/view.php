<?php

$pkg = Package::getByHandle('go_dashboard');
$package_dir = Loader::helper('concrete/urls')->getPackageURL($pkg);

?>

<style type="text/css">

    div.ccm-pagination span.numbers {
        padding: 3px 8px;
    }

    .ui-autocomplete li {
        font-size: 12px;
        font-weight: bold;
    }

    .ui-autocomplete {
        border: 1px solid #ccc;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        border-radius: 5px 5px 5px 5px;
        max-height: 500px !important;
        overflow: auto !important;
    }

    input#searchuser {
        width: 300px;
    }

    .ui-autocomplete-loading {
        background: rgba(0, 0, 0, 0) url("<?php echo $package_dir; ?>/images/ajax-loader.gif") no-repeat scroll right center;
    }
</style>

<?php
if (isset($user) && !empty($user)) {
    $title = $user[0]->uID . ' / ' . $user[0]->uName . ' ( ' . $user[0]->uEmail . ' )';
} else {
    $title = 'Teachers Registration';
}
echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(
    t($title),
    false,
    false,
    false
);

$th = Loader::helper('concrete/urls');
$url = $th->getToolsURL('user-autocomplete', 'go_dashboard');
?>

<div style="clear:both;"></div>

<div class="ccm-pane-body">

    <div id="searchform" style="" align="center">
        <form method="post" id="search-user-form" action="<?php echo $this->action('search'); ?>" method="post">
            <table width="100%" cellpadding="5" cellspacing="5" border="1">
                <tr>
                    <?php if (isset($user) && !empty($user)) { ?>
                        <td align="left">
                            <a href="<?php echo $this->action('view'); ?>">
                                <input type="button" class="btn primary" value="<?php echo t(' << Show Summary') ?>"/>
                            </a>
                        </td>
                    <?php } ?>
                    <td align="right">
                        <input type="text" id="searchuser" name="searchstring"/>
                        <input type="hidden" name="user_id" id="userid"/>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <div style="clear:both"></div>

    <?php
    if (isset($user) && !empty($user)) {
        Loader::packageElement('go_users/user-search', 'go_dashboard', array(
                'user' => @$user,
                'tabs' => @$tabs,
                'noteResults' => @$noteResults,
                'userSubscriptions' => @$userSubscriptions,
                'userTrackingGeneral' => @$userTrackingGeneral,
                'userActivationErrors' => @$userActivationErrors
            )
        );
    } else {
        Loader::packageElement('go_users/teacher-summary', 'go_dashboard', array('teacherList' => @$teacherlist));
    }
    ?>

    <div style="clear:both;height:5px;width:100%"></div>
</div>

<div style="clear:both"></div>

<div class="ccm-pane-footer">
    <?php echo $pagination; ?>
    <!-- ANZGO-3745 added by jbernardez 20180606 -->
    <?php if (isset($teacherL)) { ?>
        <div align="center" style="margin-top: 10px;">
            <form method="post" action="/dashboard/go_users/">
                Page: <input type="text" name="ccm_paging_p" style="width: 40px;"
                             value="<?php echo $teacherL->getCurrentPage(); ?>">
            </form>
        </div>
    <?php } ?>
</div>


<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false) ?>

<script type="text/javascript">

    $(function () {
        $('input#searchuser').autocomplete({
            // SB-645 modified by jbernardez 20200716
            minLength: 3,
            delay: 2000,
            source: '<?php echo $url ?>',
            select: function (event, ui) {
                $('#userid').val(ui.item.id);
                $("#search-user-form").submit();
            }
        });

        $('input#searchuser').on("keypress", function (event) {
            if (event.which == 13) {
                event.preventDefault();
                return false
            }
        });

    });

</script>
