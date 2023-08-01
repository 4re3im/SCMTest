<?php
$pkgHandle = 'institution_management';
?>
<div class="ccm-list-wrapper">
    <table class="ccm-results-list" id="review-pending-results-table">
        <?php
        echo Loader::packageElement('review_pending/table_head', $pkgHandle);
        ?>
        <tbody>
        <?php
        echo Loader::packageElement(
                'review_pending/table_body',
                $pkgHandle,
                ['data' => $data]
        );
        ?>
        </tbody>
    </table>
</div>