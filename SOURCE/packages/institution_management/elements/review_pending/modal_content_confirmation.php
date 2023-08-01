<?php
$pkgHandle = 'institution_management';
?>
<div id="confirmation-modal-content" style="display: none;">
    <p>
        You are now <strong>Rejecting</strong> these institutions. Ask your Senior/Admin for the reversal.
        <br/>
        <br/>
        Click <strong>Submit</strong> to continue.
    </p>
    <hr>
    <form id="review-pending-modal-form" action="/dashboard/institution_management/review_pending/save" method="POST">
        <table id="review-pending-modal-confirmation" class="ccm-results-list">
            <?php
            echo Loader::packageElement(
                'review_pending/table_head',
                $pkgHandle,
                [
                    'hideCheckbox' => true
                ]
            );
            ?>
            <tbody>
            <tr><td colspan="4">No selected institutions. Please make sure to place a remark per school.</td></tr>
            </tbody>
        </table>
    </form>

    <div class="dialog-buttons">
        <button type="button" class="ccm-button-left btn" id="review-pending-cancel-btn">Cancel</button>
        <button type="button" class="btn primary ccm-button-right disabled" id="review-pending-submit-btn">Submit</button>
    </div>
</div>
