<!--ANZGO-3819 added by jdchavez 08/15/18-->
<head>
    <style>
        .noclick {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            height: 10000px;
            width: 10000px;
            z-index: 20000; /* or maybe higher */
            background-color: transparent;
        }
    </style>
</head>


<!--ANZGO-3819 modified by jdchavez 08/14/18-->
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close-modal">
        <span aria-hidden="true">&times;</span>
    </button>
    <h4 class="modal-title" id="generalModalLabel">Confirm Email</h4>
</div>
<div class="modal-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <p>Please confirm that your email is correct:</p>
                <h4 class="text-center"><strong><?php echo $email; ?></strong></h4>
                <p>If your email address has not been typed correctly, you will not be able to activate your
                    account.</p>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger pull-left" data-dismiss="modal" id="cancel-signup">
        There's a mistake, let me fix it.
    </button>
    <!--    ANZGO-3819 modified by jdchavez 08/14/18-->
    <input type="button"
           class="btn btn-success pull-right"
           id="submit-signup"
           value="My email is correct, proceed"
           onclick="setTimeout(disableSubmitButton, 100);"/>
</div>
<!--ANZGO-3819 added by jdchavez 08/14/18-->
<script>
    function disableSubmitButton(e) {
        jQuery(document).ready(function() {
            jQuery('html').append('<div class="noclick" />');
        });
        document.getElementById('submit-signup').disabled = true;
        document.getElementById('submit-signup').value = "Processing...";
        document.getElementById('cancel-signup').disabled = true;
        document.getElementById('cancel-signup').style.visibility = 'hidden';
        document.getElementById('close-modal').disabled = true;
        document.getElementById('close-modal').style.visibility = 'hidden';
    }
</script>