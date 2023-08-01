<?php
$pkg = Package::getByHandle('go_dashboard');
$package_dir = Loader::helper('concrete/urls')->getPackageURL($pkg);
?>

<style type="text/css">
    div.ccm-pagination span.numbers { padding: 3px 8px; }

    .ui-autocomplete li { font-size: 12px; font-weight: bold;}

    .ui-autocomplete {
        border:  1px solid #ccc;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        border-radius: 5px 5px 5px\ 5px; 
        max-height: 500px !important;
        overflow: auto !important;
    }

    input#searchuser { width: 300px;}
    .ui-autocomplete-loading {
        background: rgba(0, 0, 0, 0) url("<?php echo $package_dir; ?>/images/ajax-loader.gif") no-repeat scroll right center;
    }
</style>

<?php
if (isset($_POST['searchstring'])) {
    $title = $userlist[0]->uID . ' / ' . $userlist[0]->uName . ' ( ' . $userlist[0]->uEmail . ' )';
} else {
    $title = 'Teachers Registration';
}
echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(
        t($title), false, false, false)
?>

<div style="clear:both;"></div>

<div class="ccm-pane-body">

    <div id="searchform" style="" align="center">	 
        <form method="post" action="<?php echo $this->action('search') ?>" method="post">
            <table width="100%" cellpadding="5"; cellspacing="5" border="1">
                <tr>
<?php if ($_POST['searchstring'] != '') { ?>
                        <td align="left">	
                            <a href="">	
                                <input type="button" class="btn primary" value="<?php echo t(' << Show Summary') ?>" /> 	            	
                            </a>	
                        </td>	
<?php } ?>
                    <td align="right"><input type="text" name="searchstring" />               

                        <input type="submit" class="btn primary" value="<?php echo t('Search User') ?>" /> 	            	
                    </td>
                </tr>
            </table>
        </form>
    </div> 

    <div style="clear:both"></div>

<?php
if ($_POST['searchstring'] != '') {
    Loader::packageElement('go_users/user-search', 'go_dashboard', array('user' => @$user,
        'tabs' => @$tabs
            )
    );
} else {
    Loader::packageElement('go_users/teacher-summary', 'go_dashboard', array('teacherlist' => @$teacherlist));
}
?>

    <div style="clear:both;height:5px;width:100%"></div>
</div>

<div style="clear:both"></div>

<div>
<?php if (!isset($_POST['searchstring'])) {
    echo $teacherlistPagination;
} ?>
</div>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false) ?>

<script type="text/javascript">

    $(function () {

        // auto-complete 
        $('input#searchuser').autocomplete({source: '<?php echo $url ?>',
            select: function (event, ui) {
                $('#userid').val(ui.item.id);

                //alert($('#userid').val());           
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