<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

$valt = Loader::helper('validation/token');
$json = json_decode($institution);
$title = $json->name;
$helpText = t('View and edit Gigya institution');
echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(
    $title,
    $helpText,
    '',
    false,
    [],
    true
);

$pkgHandle = 'institution_management';

?>
    <div class="ccm-pane-body">
        <?php
        echo Loader::packageElement('alert_message_header', 'cup_content');
        echo Loader::packageElement(
                'review/basic_details',
                $pkgHandle,
                ['json' => $json,
                    'userSubscriptions' => $userSubscriptions]
        );
        ?>
    </div>

    <div class="ccm-pane-footer">

    </div>
<?php
echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(true);
?>