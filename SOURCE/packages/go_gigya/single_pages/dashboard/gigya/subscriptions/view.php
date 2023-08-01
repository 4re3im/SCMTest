<?php
$params = [
    'tabs' => $tabs,
    'user' => $user,
    'UID' => $UID
];

echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Go - Gigya Subscriptions'),
    t('Manage subscriptions of Gigya users'), false, false);
?>

<div class="ccm-pane-options">
    <form class="form-horizontal" id="ccm-user-advanced-search">
        <div class="ccm-pane-options-permanent-search">
            <div class="span7">
                <label for="gigya-search" class="control-label">Search</label>
                <div class="controls">
                    <input id="gigya-search" type="text" name="email" value="" placeholder="Email"
                           class="ccm-input-text">
                </div>
            </div>
            <div class="span4">
                <a href="/dashboard/gigya/users" class="btn primary ccm-button-v2-right">« Back to summary</a>
            </div>
            
        </div>
    </form>
</div>

<?php if ($user) { ?>
    <div class="ccm-pane-body ccm-pane-body-footer" id="ccm-dashboard-user-body">
        <h3>Basic Details</h3>
        <div class="panel">
            <div class="panel-body">
                <table class="table table-condensed" id="gigya-basic-details">
                    <tbody>
                    <tr>
                        <th>Full Name</th>
                        <td><?php echo $user->getFullName(); ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><a href="mailto:<?php echo $user->getEmail(); ?>"><?php echo $user->getEmail(); ?></a></td>
                    </tr>
                    <tr>
                        <th>Gigya ID</th>
                        <td><?php echo $UID ?></td>
                    </tr>
                    <tr>
                        <th>Go ID</th>
                        <td><?php echo $user->getSystemID(); ?></td>
                    </tr>
                    <tr>
                        <th>Platform/s</th>
                        <td><?php echo $user->getOriginPlatform(); ?></td>
                    </tr>
                    </tbody>
                </table>
                <input type="hidden" id="gigya-system-id" value="<?php echo $user->getSystemID(); ?>">
                <input type="hidden" id="gigya-uid" value="<?php echo $UID; ?>">
            </div>
        </div>
        <?php Loader::packageElement('gigya/user_details/view', 'go_gigya', $params);?>
    </div>
<?php } ?>

