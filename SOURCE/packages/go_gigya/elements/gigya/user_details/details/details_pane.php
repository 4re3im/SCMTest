<div id="ccm-tab-content-tab-1" class="ccm-tab-content">
    <div class="panel">
        <div class="panel-body">
            <table class="table table-bordered">
                <tbody>
                <tr>
                    <th>Full Name</th>
                    <td><?php echo $user->getFullName(); ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>
                        <a href="mailto:<?php echo $user->getEmail(); ?>">
                            <?php echo $user->getEmail(); ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <th>Gigya ID</th>
                    <td><?php echo $UID ?></td>
                </tr>
                <tr>
                    <th>Go ID</th>
                    <td><?php echo $user->getSystemID(); ?></td>
                </tr>
                </tbody>
            </table>
            <input type="hidden" id="gigya-system-id" value="<?php echo $user->getSystemID(); ?>">
            <input type="hidden" id="gigya-uid" value="<?php echo $UID; ?>">
        </div>
    </div>
</div>