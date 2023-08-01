<!--FOR DELETION jdchavez-->
<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
$af = Loader::helper('form/attribute', 'go_contents');

$u = new User();
$ui = UserInfo::getByID($u->uID);

?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="generalModalLabel">
        <?php echo ($mode == 'password') ? 'Change Password' : 'Edit Contact Details'; ?>
    </h4>
</div>
<form class="form-horizontal"
      method="POST"
      action="<?php echo $this->url('/go/account', 'do_edit/' . $mode); ?>"
      id="editAcctForm">
    <input type="hidden" name="type" value="<?php echo $type ? $type : 'teacher'; ?>" />
    <input type="hidden" name="manAct[status]" value="<?php echo $mad['man_act']; ?>" />
    <input type="hidden" name="manAct[staff]" value="<?php echo $mad['man_staffid']; ?>" />
    <input type="hidden" name="manAct[date]" value="<?php echo $mad['man_date']; ?>" />
    <div class="modal-body container-bg-2">
        <div class="container-fluid">
            <?php
            if($mode == 'password') { ?>
                <div class="form-group row">
                    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label">Previous password</label>
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 form-col">
                        <input type="password" class="form-control go-input" name="oldPassword" placeholder="" />
                        <span class="help-block"></span>
                    </div>
                </div>
                <?php
                foreach ($login_attribs as $la) {
                    if(in_array($la->getAttributeKeyHandle(), $required)) {
                        echo $af->display($la, false, true, 'composer', '');
                    }
                }
            } else {
                foreach ($contact_attribs as $ca) {
                    if(in_array($ca->getAttributeKeyHandle(), $required)) {
                        echo $af->display($ca);
                    }
                }

                if($type != 'Student') {
                    foreach ($teacher_attribs as $ta) {
                        if (in_array($ta->getAttributeKeyHandle(), $required)) {
                            if($ta->getAttributeKeyHandle() == "uState") {
                                $postCountry = array(
                                    "United States" => "uStateUS",
                                    "Canada" => "uStateCA",
                                    "Australia" => "uStateAU",
                                    "New Zealand" => "uStateNZ"
                                );
                                $country = "" . $ui->getAttribute("uCountry");

                                foreach ($teacher_attribs as $ta) {
                                    if($ui->getAttribute($postCountry[$country])) {
                                        if($ta->getAttributeKeyHandle() == $postCountry[$country]) {
                                            echo $af->display($ta, false, true, 'composer', '', false, true);
                                        }
                                    } else {
                                        if($ta->getAttributeKeyHandle() == "uState") {
                                            echo $af->display($ta, false, true, 'composer', '', false, true);
                                        }
                                    }
                                }
                            } elseif($ta->getAttributeKeyHandle() == "uCountry") {
                                echo $af->display($ta, false, true, 'composer', '', false, true);
                            }else {
                                echo $af->display($ta);
                            }
                        }
                    }

                    // ANZGO-3809 Deleted by Shane Camus 07/25/2018

                } else {
                    foreach ($general_checkboxes as $gc) {
                        echo $af->display($gc);
                    }
                }
            } ?>
        </div>
    </div>
    <div class="modal-footer">
        <input type="submit" class="btn btn-success" value="Apply Changes"/>
    </div>
</form>
