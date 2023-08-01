<?php
/* My Resources Activate */
defined('C5_EXECUTE') or die(_("Acess Denied"));
// ANZGO-3943 modified by mtanada 20181203
$hmApiLink = HOTMATHS_CONNECT;
$u = new User();
$userId = $u->getUserID();
?>
<!-- SB-102 added by mabrigos 20190321 moved header spacer to specific pages -->
<div class="header-spacer">
    &nbsp;
</div>
<div class="resources-wrapper">
<div class="container new-resource">
    <div class="row">
        <div class="col-md-12">
            <h1>Add a new resource</h1>
            <div id="messaging"></div>
            <form name="myresourcesActivate" class="activate-form">
                <p>Enter the 16-character code found in the front of your textbook, sealed pocket or email.</p>
                <div class="input-field">
                    <label class="access-code-field" for="access-code-1">Access code 1
                        <input type="text" id="accesscode_box1" name="accesscode_box[1]"
                               class="accesscodetext" maxlength="4">
                    </label>
                     –
                    <label class="access-code-field" for="access-code-2">Access code 2
                        <input type="text" id="accesscode_box2" name="accesscode_box[2]"
                               class="accesscodetext" maxlength="4">
                    </label>
                    –
                    <label class="access-code-field" for="access-code-3">Access code 3
                        <input type="text" id="accesscode_box3" name="accesscode_box[3]"
                               class="accesscodetext" maxlength="4">
                    </label>
                    –
                    <label class="access-code-field" for="access-code-4">Access code 4
                        <input type="text" id="accesscode_box4" name="accesscode_box[4]"
                               class="accesscodetext" maxlength="4">
                    </label>
                    <div class="reset-code" id="accesscode-refresh" onclick="refresh();" title="Activate Another Code">
                        <img src="/packages/go_theme/elements/svgs/reset.svg" alt="Reset"><span> Reset</span>
                    </div>
                </div>
                <span class="field-error"></span>
                <div class="checkbox-field">
                    <input id="activate_checkbox" class="terms-of-use checkbox" type="checkbox" name="terms" value="1"
                    required>
                    <label for="terms" class="checkbox-label">Yes, I agree to the
                        <a href="<?php echo $this->url('/go/terms'); ?>" target="_blank">
                            Terms of Use.
                        </a>
                    </label>
                </div>
                <div class="input-field submit-btn">
                    <!---Added by Maryjes Tanada 02/12/2018-->
                    <?php if (isset($_SESSION['userTypeMessage'])) {?>
                        <p class="alert-danger animated bounce alert-dismissible"
                           style="padding-bottom: 10px; padding-top: 10px; padding-left: 35px">
                            <?php echo $_SESSION['userTypeMessage'];  ?>
                        </p>
                    <?php unset($_SESSION['userTypeMessage']); } 
                    // SB-190 modified by machua 20190531
                    ?>
                    <button id="activate" class="btn btn-primary btn-lg">Add to My Resources</button>
                </div>
            </form>
            <hr>
            <p>If you have <strong>Cambridge HOTmaths</strong>,
                <strong>Cambridge Senior Mathematics</strong> or <strong>Essential Mathematics</strong>
                for the Australian Curriculum, you can connect these resources to your Cambridge GO account below.
            </p>
            <div class="input-field connect-btn">
                <a class="btn btn-primary btn-lg"
                   href="https://<?php echo $hmApiLink; ?>/cambridgeLogin?externalId=<?php echo $userId; ?>"
                   target="_blank">Connect</a>
            </div>
        </div>
    </div>
</div>
</div>
<input type="hidden" value="<?php echo $this->url('/go/activate/processCode'); ?>" id="activate_code_url" />
<input type="hidden" value="<?php echo BASE_URL . $this->url('/go/myresources'); ?>" id="myresources_url" />

<script>
    // ANZGO-3694 Added by Maryjes Tanada 04/19/2018 Automatically tab to next input field
    // ANZGO-3943 moved by mtanada 20181204 to avoid ajax loading to complete
    $(".accesscodetext").on("input", function() {
        if ($(this).val().length == $(this).attr("maxlength")) {
            $(this).closest('label').next().find(':input').first().focus();
        }
    });
</script>