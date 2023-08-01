<h1 class="animated fadeInLeft">Code health check</h1>
<h2 class="animated fadeInLeft">Enter your code</h2>
<div id="activated-icon" class="alert alert-block fade" role="alert">
    <img src="<?php echo $this->getThemePath(); ?>/images/checkmark-circle.svg" alt="checkmark">
</div>

<div id="activated" class="alert alert-success alert-block fade" role="alert">
</div>

<?php /* ANZGO-3253 modified by jbernardez 20170920 removed minlength following business requirements */ ?>
<div class="row">
    <form id="accessCodeForm" class="form-inline activate col-md-6 col-md-offset-3">
        <div class="form-group animated fade in field-remove">
            <label for="First" class="sr-only">First</label>
            <input type="text"
                   class="form-control access-code-input"
                   id="First"
                   placeholder="****"
                   maxlength="4"
                   name="access_code[]" required>
        </div>
        <div class="form-group animated fade in field-remove">
            <label for="Second" class="sr-only">Second</label>
            <input type="text"
                   class="form-control access-code-input"
                   id="Second"
                   placeholder="****"
                   maxlength="4"
                   name="access_code[]" required>
        </div>
        <div class="form-group animated fade in field-remove">
            <label for="Third" class="sr-only">Third</label>
            <input type="text"
                   class="form-control access-code-input"
                   id="Third"
                   placeholder="****"
                   maxlength="4"
                   name="access_code[]" required>
        </div>
        <div class="form-group animated fade in field-remove">
            <label for="Fourth" class="sr-only">Fourth</label>
            <input type="text"
                   class="form-control access-code-input"
                   id="Fourth"
                   placeholder="****"
                   maxlength="4"
                   name="access_code[]" required>
            <!-- ANZGO-3556 added by jbernardez 20171026 -->
            <span id="accesscode-refresh"
                  class="glyphicon glyphicon-refresh"
                  data-toggle="tooltip"
                  title="Check Another Code">
            </span>
        </div>

        <div id="generalError" class="alert alert-danger alert-dismissible" role="alert" hidden>
        </div>

        <p class="instructions animated fadeInRight fade in field-remove">
            Enter the access code provided in the front of your printed textbook, the sealed pocket or the email
            supplied on purchase.
        </p>

        <div class="fade in field-remove">
            <div class="reCaptcha">
                <div class="g-recaptcha" data-sitekey="6Lc7ti0UAAAAAEsVUT0q6um2a2WCjuhdb4CxpjoX"
                     data-callback="recaptchaCallback">
                </div>
            </div>
        </div>

        <div id="recaptchaError" class="alert alert-danger fade" role="alert" hidden>
            Please verify with recaptcha
        </div>

        <p class="animated bounceInUp long field-remove fade in">
            <button  class="open btn btn-default green activate" type="submit" value="submit">Check code</button>
        </p>
    </form>
</div>
</div>

<script>
    // ANZGO-3694 Added by Maryjes Tanada 04/19/2018 Automatically tab to next input field
    $(".access-code-input").on("input", function() {
        if ($(this).val().length == $(this).attr("maxlength")) {
            $(this).closest('div').next().find(':input').first().focus();
        }
    });
</script>
