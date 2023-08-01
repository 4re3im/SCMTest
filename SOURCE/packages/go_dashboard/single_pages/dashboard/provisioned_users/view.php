<?php

/**
 * PROVISIONED_USERS View File
 * ANZGO-3595 Added by Shane Camus 01/25/2018
 */

$packageName = 'go_dashboard';
$package = Package::getByHandle($packageName);
$packageDir = Loader::helper('concrete/urls')->getPackageURL($package); ?>

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

    input#searchUser {
        width: 300px;
    }

    .ui-autocomplete-loading {
        background: rgba(0,0,0,0) url("<?php echo $packageDir ?>/images/ajax-loader.gif") no-repeat scroll right center;
    }

</style>

<?php
    if (isset($user) && !empty($user)) {
        $title = $user[0]->uID . ' / ' . $user[0]->Email . ' ( ' . $user[0]->Email . ' )';
    } else {
        $title = 'Provisioned Users';
    }
    echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(
        t($title),
        false,
        false,
        false
    );

    $urlHelper = Loader::helper('concrete/urls');
    $url = $urlHelper->getToolsURL('provisionedUserAutoComplete', $packageName);
?>

<div style='clear:both'></div>

<div class='ccm-pane-body'>

    <div id='searchForm' align='center'>
        <form id='search-user-form' action="<?php echo $this->action('search'); ?>" method='post'>
            <table width='100%' cellpadding='5' cellspacing='5' border='1'>
                <tr>
                    <?php if (isset($user) && !empty($user)) { ?>
                        <td align='left'>
                            <a href="<?php echo $this->action('view'); ?>">
                                <input type='button' class='btn primary' value="<?php echo t(' << Show Summary') ?>"/>
                            </a>
                        </td>
                    <?php } ?>
                    <td align='right'>
                        <input type='text' id='searchUser' name='searchString'/>
                        <input type='hidden' name='userID' id='userID'/>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <div style='clear:both''></div>

    <?php
    if (isset($user) && !empty($user)) {
        Loader::packageElement(
            'provisioned_users/userSearch',
            $packageName,
            array(
                'user' => @$user,
                'tabs' => @$tabs,
                'noteResults' => @$noteResults,
                'userSubscriptions' => @$userSubscriptions,
                'userTrackingGeneral' => @$userTrackingGeneral,
                'userActivationErrors' => @$userActivationErrors
            )
        );
    } else {
        Loader::packageElement(
            'provisioned_users/provisionedUsersTable',
            $packageName,
            array(
                'provisionedUsersList' => @$provisionedUsersList,
                'pagination' => @$pagination
            )
        );
    }

    ?>

    <div style='clear:both;height:5px;width:100%'></div>

</div>

<div style='clear:both'></div>

<div class='ccm-pane-footer'>
    <?php echo $paging; ?>
</div>


<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false) ?>

<script type='text/javascript'>

    $(function () {
        $('input#searchUser').autocomplete({
            source: '<?php echo $url ?>',
            select: function (event, ui) {
                $('#userID').val(ui.item.id);
                $('#search-user-form').submit();
            }
        });

        $('input#searchUser').on('keypress', function (event) {
            if (event.which == 13) {
                event.preventDefault();
                return false
            }
        });
    });
</script>
