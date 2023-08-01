<form method="post" action="<?php echo $this->action('saveUserGeneralInfo'); ?>" name="usergeneral" id="usergeneral">

    <div class="panel" style="text-align:right">
        <input type="button" class="btn" id="archive" name="archive" value="Archive">
        <input type="button" class="btn primary" id="save" name="save" value="Save">
    </div>

    <div style="clear:both"></div>

    <?php


    $u = new GoDashboardGoUsers();
    $data = $u->getUserInfo($user[0]->uID);

    $ui = UserInfo::getByID($user[0]->uID);

    $stateHelper = Loader::helper('lists/states_provinces');
    $state = $stateHelper->getStateProvinceArray('AU');

    //manually added by
    $u = new GoDashboardGoUsers($data['ak_uMAStaffID']);
    $manually_added_by = $u->uName;

    if($data['ak_uManuallyActivated']) $manually_activated = $data['ak_uManuallyActivated']==1 ? 'Y' : 'N';

    $statesListAU = array();
    $statesListNZ = array();
    $statesListCA = array();
    $statesListUS = array();
    $countryList = array();

    $set = AttributeSet::getByHandle('uTeacherContactDetails');
    $keys = $set->getAttributeKeys();

    foreach($keys as $key) {
        $handle = $key->getAttributeKeyHandle();

        if( $handle == 'uStateNZ' ) {
            $type = $key->getAttributeType();
            $cont = $type->getController();
            $cont->setAttributeKey($key);
            foreach($cont->getOptions() as $item){
                $statesListNZ[] = $item->value;
            }
        }
        elseif( $handle == 'uStateAU' ) {
            $type = $key->getAttributeType();
            $cont = $type->getController();
            $cont->setAttributeKey($key);
            foreach($cont->getOptions() as $item){
                $statesListAU[] = $item->value;
            }
        }
        elseif( $handle == 'uStateCA' ) {
            $type = $key->getAttributeType();
            $cont = $type->getController();
            $cont->setAttributeKey($key);
            foreach($cont->getOptions() as $item){
                $statesListCA[] = $item->value;
            }
        }
        elseif( $handle == 'uStateUS' ) {
            $type = $key->getAttributeType();
            $cont = $type->getController();
            $cont->setAttributeKey($key);
            foreach($cont->getOptions() as $item){
                $statesListUS[] = $item->value;
            }
        }
        elseif( $handle == 'uCountry' ) {
            $type = $key->getAttributeType();
            $cont = $type->getController();
            $cont->setAttributeKey($key);
            foreach($cont->getOptions() as $item){
                $countryList[] = $item->value;
            }
        }
    }

    ?>
    <script>
        var statesListNZ = <?php echo json_encode($statesListNZ);?>;
        var statesListAU = <?php echo json_encode($statesListAU);?>;
        var statesListCA = <?php echo json_encode($statesListCA);?>;
        var statesListUS = <?php echo json_encode($statesListUS);?>;

        function changeStateList(country) {
            var statesList = [];
            var optionHTML = '<select id="state" name="state"><option> -- Pick One -- </option>';

            if(country == "Canada"){
                statesList = statesListCA;
            }
            else if(country == "Australia"){
                statesList = statesListAU;
            }
            else if(country == "New Zealand"){
                statesList = statesListNZ;
            }
            else if(country == "United States"){
                statesList = statesListUS;
            }
            else{
                optionHTML = '<input type="text" id="state" name="state" value=""/>'
            }

            if(statesList.length > 0){
                for (i = 0; i < statesList.length; i++) {
                    optionHTML += '<option value="' + statesList[i] + '">' + statesList[i] + '</option>';
                }

                optionHTML += '</select>';
            }

            $("#state").replaceWith(optionHTML);

        }
    </script>
    <form method="post" action="">
        <input type="hidden" id="userid" name="userid" value="<?php echo $data['uID']; ?>">
        <div id="container2">
            <div id="alertMessageDiv" class="alert" style="display:none;">
                <button class="close" type="button">×</button>
                <span id="alertMessage"></span>
            </div>
            <div class='panel'>

                <p class='header'><strong>Name</strong></p>

                <?php

                //foreach ($user as $data) { ?>

                <table id="tbl">
                    <tr>
                        <td id="hdr">First Name</td>
                        <td id="dtl"><input type="text" id="firstname" name="firstname" value="<?php echo $data['ak_uFirstName']; ?>"></td>
                    </tr>

                    <tr>
                        <td id="hdr">Last Name</td>
                        <td id="dtl"><input type="text" id="lastname" name="lastname" value="<?php echo $data['ak_uLastName']; ?>"></td>
                    </tr>

                    <tr>
                        <td id="hdr">Email</td>
                        <td id="dtl">
                            <?php echo $data['uEmail']; ?>
                            <input type="hidden" id="email" name="email" value="<?php echo $data['uEmail']; ?>">
                        </td>
                    </tr>

                </table>

                <?php
                //  }
                ?>
            </div>

            <!--   <div class='panel'>
       <p class='header'><strong>Security</strong></p>

       <table id="tbl">
           <tr>
               <td id="hdr">Question</td>
               <td id="dtl"><input type="text" id="question" name="question" value="<?php echo $data['ak_uSecurityQuestion']; ?>"></td>
           </tr>
           <tr>
               <td id="hdr">Answer</td>
               <td id="dtl"><input type="text" id="answer" name="answer" value="<?php echo $data['ak_uSecurityAnswer']; ?>"></td>
           </tr>

           <tr>
               <td id="hdr">Active</td>
               <td id="dtl">
                   <?php
            $yes = $data['uIsActive'] == 1 ? "CHECKED" : "";
            $no = $data['uIsActive'] == 0 ? "CHECKED" : "";
            ?>
                   <input type="radio" name="active" value="1" <?php echo $yes; ?>  style="margin-right:5px">Yes

                   <input type="radio" name="active" value="0" <?php echo $no; ?> style="margin-right:5px">No
               </td>
           </tr>
       </table>

   </div>-->


            <div class='panel'>
                <p class='header'><strong>User Type</strong></p>
                <?php
                // foreach ($previousReleaseDates as $data) { ?>

                <table id="tbl">
                    <tr>
                        <td id="hdr">User Type</td>
                        <td id="dtl">
                            <?php

                            if ( $data['gID'] == 4) {
                                $studentSelected = "selected='true'";
                            } else {
                                $teacherSelected = "selected='true'";
                            }


                            ?>
                            <select id="usertype" name="usertype">
                                <option selected="true" > -- Pick One -- </option>
                                <option value='4' <?php echo $studentSelected ?> >Student</option>
                                <option value='5' <?php echo $teacherSelected ?> >Teacher</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td id="hdr">Active</td>
                        <td id="dtl">
                            <?php
                            $yes = $data['uIsActive'] == 1 ? "CHECKED" : "";
                            $no = $data['uIsActive'] == 0 ? "CHECKED" : "";
                            ?>
                            <input type="radio" name="active" value="1" <?php echo $yes; ?>  style="margin-right:5px">Yes

                            <input type="radio" name="active" value="0" <?php echo $no; ?> style="margin-right:5px">No
                        </td>
                    </tr>
                    <tr>
                        <td id="hdr">Verified</td>
                        <td id="dtl">
                            <?php
                            $isValidated = $data['uIsValidated'] == 1 ? "CHECKED" : "";
                            $notValidated = $data['uIsValidated'] <= 0 ? "CHECKED" : "";
                            ?>
                            <input type="radio" name="verified" value="1" <?php echo $isValidated; ?> style="margin-right:5px">Yes
                            <input type="radio" name="verified" value="0" <?php echo $notValidated; ?> style="margin-right:5px">No
                        </td>
                    </tr>

                </table>
                <?php
                //}
                ?>
            </div>
            <div class='panel'>&nbsp;</div>
        </div>

        <div id="container2">

            <div class='panel' style="height:100%">
                <p class='header'><strong>Contact Details</strong></p>

                <?php
                //foreach ($previouslyActivatedBy as $data) { ?>
                <table id="tbl">
                    <tr>
                        <td id="hdr">Address</td>
                        <td id="dtl"><input type="text" id="address" name="address" value="<?php echo $data['ak_uSchoolAddress']; ?>"></td>
                    </tr>

                    <tr>
                        <td id="hdr">Suburb</td>
                        <td id="dtl"><input type="text" id="suburb" name="suburb" value="<?php echo $data['ak_uSuburb']; ?>"></td>
                    </tr>



                    <tr>
                        <td id="hdr">State</td>
                        <td id="dtl">
                            <?php if(trim($data['ak_uCountry']) != "" && trim($data['ak_uCountry']) != "-- Pick One --" && trim($data['ak_uCountry']) != "Australia" && trim($data['ak_uCountry']) != "New Zealand" && trim($data['ak_uCountry']) != "Canada" && trim($data['ak_uCountry']) != "United States"): ?>
                                <input type="text" id="state" name="state" value="<?php echo $data['ak_uState']; ?>">
                            <?php else:
                                $statesList = array();
                                $stateValue = "";

                                if (trim($data['ak_uCountry']) == "Australia") {
                                    $statesList = $statesListAU;
                                    $stateValue = $data['ak_uStateAU'];
                                }
                                elseif (trim($data['ak_uCountry']) == "New Zealand") {
                                    $statesList = $statesListNZ;
                                    $stateValue = $data['ak_uStateNZ'];
                                }
                                elseif (trim($data['ak_uCountry']) == "Canada") {
                                    $statesList = $statesListCA;
                                    $stateValue = $data['ak_uStateCA'];
                                }
                                elseif (trim($data['ak_uCountry']) == "United States") {
                                    $statesList = $statesListUS;
                                    $stateValue = $data['ak_uStateUS'];
                                }


                                ?>

                                <select id="state" name="state">
                                    <option> -- Pick One -- </option>
                                    <?php
                                    foreach ($statesList as $state) { ?>
                                        <option value="<?php echo $state?>" <?php if (trim($state) == trim($stateValue)) { echo "selected='selected'"; } ?> > <?php echo $state; ?></option>
                                    <?php }
                                    ?>
                                </select>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <tr>
                        <td id="hdr">Postcode</td>
                        <td id="dtl"><input type="text" id="postcode" name="postcode" value="<?php echo $data['ak_uPostcode']; ?>"></td>
                    </tr>

                    <tr>
                        <td id="hdr">Country</td>
                        <td id="dtl">
                            <select id="country" name="country" onchange="changeStateList(this.value);">
                                <option> -- Pick One -- </option>
                                <?php
                                foreach ($countryList as $country) { ?>

                                    <option value="<?php echo $country?>" <?php if (trim($data['ak_uCountry']) == trim($country)) { echo "selected='selected'"; } ?> > <?php echo $country; ?></option>
                                <?php    }
                                ?>
                            </select>
                        </td>
                    </tr>


                    <tr>
                        <td id="hdr">Phone Number</td>
                        <td id="dtl"><input type="text" name="phonenumber" name="phonenumber" value="<?php echo $data['ak_uSchoolPhoneNumber']; ?>"></td>
                    </tr>

                </table>
                <?php
                //  }
                ?>
            </div>


            <div class='panel' style="height:100%">
                <p class='header'><strong>User Information</strong></p>

                <table id="tbl">
                    <tr>
                        <td id="hdr">Position</td>
                        <td id="dtl"><input type="text" id="position" name="position" value="<?php echo $data['ak_uPositionTitle']; ?>"></td>
                    </tr>

                    <tr>
                        <td id="hdr">School</td>
                        <td id="dtl"><input type="text" id="school" name="school" value="<?php echo $data['ak_uSchoolName']; ?>"></td>
                    </tr>

                    <tr>
                        <td id="hdr">Title</td>
                        <td id="dtl"><input type="text" id="title" name="title" value="<?php echo $data['ak_uPositionType']; ?>"></td>
                    </tr>

                    <tr>
                        <td id="hdr">SalesForce ID</td>
                        <td id="dtl"><input type="text" id="salesforceid" name="salesforceid" value="<?php echo $data['accountID']; ?>"></td>
                    </tr>
                    <!-- ANZGO-3671 Modified by Shane Camus 03/20/2018 -->
                    <tr>
                        <td id="hdr">Customer Care</td>
                        <td id="dtl">
                            <input type="radio" name="customercare" value="1" <?php echo ($ui->getAttribute('uCustomerCare') == "1") ? "checked" : ""; ?> style="margin-right:5px">Yes
                            <input type="radio" name="customercare" value="0" <?php echo ($ui->getAttribute('uCustomerCare') == "0") ? "checked" : ""; ?> style="margin-right:5px">No
                        </td>
                    </tr>
                    <tr>
                        <td id="hdr">Promotional By Email</td>
                        <td id="dtl">
                            <?php
                            $notpromotional_email = ($data['ak_uPMByEmail']) ? "" : "CHECKED";
                            $promotional_email = ($data['ak_uPMByEmail']) ? "CHECKED" : "";
                            ?>
                            <input type="radio" name="promotional_email" value="1" <?php echo $promotional_email; ?> style="margin-right:5px">Yes
                            <input type="radio" name="promotional_email" value="0" <?php echo $notpromotional_email; ?> style="margin-right:5px">No
                        </td>
                    </tr>
                    <tr>
                        <td id="hdr">Promotional By Post</td>
                        <td id="dtl">
                            <?php
                            $notpromotional_post = ($data['ak_uPMByRegularPost']) ? "" : "CHECKED";
                            $promotional_post = ($data['ak_uPMByRegularPost']) ? "CHECKED" : "";
                            ?>
                            <input type="radio" name="promotional_post" value="1" <?php echo $promotional_post; ?> style="margin-right:5px">Yes
                            <input type="radio" name="promotional_post" value="0" <?php echo $notpromotional_post; ?> style="margin-right:5px">No
                        </td>
                    </tr>
                </table>

            </div>


            <div class='panel' style="height:100%; margin-left: 20px; width: 30%;">
                <p class='header'><strong>Activation Info</strong></p>
                <?php
                // foreach ($previousReleaseDates as $data) { ?>

                <table id="tbl">
                    <tr>
                        <td id="hdr">Created</td>
                        <td id="dtl">
                            <?php echo $data['uDateAdded']; ?>
                            <input type="hidden" id="creationdate" name="creationdate" value="<?php echo $data['uDateAdded']; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td id="hdr">Activated</td>
                        <td id="dtl">
                            <span id="activateddate_span"><?php echo $data['ak_uActivatedDate']  ?></span>
                            <input type="hidden" id="activateddate" name="activateddate" value="<?php echo $data['ak_uActivatedDate']; ?>">
                        </td>
                    </tr>
                    <?php
                    // ANZGO-3597 added by jbernardez 20180131
                    $createdByUser = $u->getUserInfo($data['ak_uCreatedByID']);
                    ?>
                    <tr>
                        <td id="hdr">User Created By</td>
                        <td id="dtl">
                            <span id="activateddate_span"><?php echo $createdByUser['ak_uFirstName'] . " " . $createdByUser['ak_uLastName']; ?></span>
                            <input type="hidden" id="activatedby" name="activatedby" value="<?php echo $createdByUser['uEmail']; ?>">
                        </td>
                    </tr>

                    <tr>
                        <td id="hdr">Manually Activated</td>
                        <td id="dtl">
                            <span id="manuallyactivated_span"><?php echo $manually_activated; ?></span>
                            <input type="hidden" id="manuallyactivated" name="manuallyactivated" value="<?php echo $data['ak_uManuallyActivated']; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td id="hdr">Manually Activated By</td>
                        <td id="dtl">
                            <span id="manuallyactivatedby_span"><?php echo $manually_added_by; ?></span>
                            <input type="hidden" id="manuallyactivatedby" name="manuallyactivatedby" value="<?php echo $data['ak_uMAStaffID']; ?>">
                        </td>
                    </tr>

                    <tr>
                        <td id="hdr">Notes</td>
                        <td id="dtl">
                           <textarea id="usernotes" name="usernotes" value="<?php echo $data['ak_uNotes']; ?>">
                               <?php echo $data['ak_uNotes']; ?>
                           </textarea>
                        </td>
                    </tr>


                </table>
                <?php
                //}
                ?>
            </div>


        </div>


    </form>



    <div style="clear:both"></div>

    <div class="panel-default">
        <table width="100%" id="tbl" border="1">

            <tr>
                <td>
                    <strong>User Notes</strong>
                </td>
                <td align="right">
                    <input type="button" class="btn primary" id="addnote" name="addnote" value="Add New" style="float:right">
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <input type="text" name="note" id="note"  />
                    <input type="hidden" name="user_id" id="user_id" value="<?php echo $user[0]->uID; ?>" />

                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <div class="usernotes">

                        <?php if (isset($noteResults) && count($noteResults) > 0 ) { ?>
                            <table id='ccm-product-list' class='ccm-results-list' cellspacing='0' cellpadding='0' border='0' width="100%">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Note</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($noteResults as $noteResult) {  ?>
                                    <tr class='ccm-list-record'>
                                        <td><?php echo $noteResult['CreationDate']; ?> </td>
                                        <td><?php echo $noteResult['NoteText']; ?></td>
                                    </tr>
                                <?php } ?>

                                </tbody>
                            </table>
                        <?php } ?>
                    </div>
                </td>
            </tr>

        </table>
    </div>
