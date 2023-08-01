<?php
defined('C5_EXECUTE') or die("Access Denied.");
$v = View::getInstance();
$col1_select = ($tab_result['Columns'] == 1) ? "selected" : "";
$col2_select = ($tab_result['Columns'] == 2) ? "selected" : "";
$is_active = ($tab_result['Active'] == 'Y') ? "checked" : "";
$public_visibility = ($tab_result['Visibility'] == 'Public') ? "selected" : "";
$private_visibility = ($tab_result['Visibility'] == 'Private') ? "selected" : "";
$is_resources_link = ($tab_result['MyResourcesLink'] == 'Y') ? "checked" : "";
$is_elevate_product = ($tab_result['ElevateProduct'] == 'Y') ? "checked" : "";
$is_hm_product = ($tab_result['HMProduct'] == 'Y') ? "checked" : "";
$use_public_text = ($tab_result['AlwaysUsePublicText'] == 'Y') ? "checked" : "";

// SB-249 added by mabrigos 20190710
$coming_soon = ($tab_result['ComingSoon'] == 'Y') ? "checked" : "";
// GCAP-1734 added by jdchavez 2022/09/08
$knowledge_check =($tab_result['KnowledgeCheck'] == 'Y') ? "checked" : "";
// CGO-233 added by mmascarinas 2023/01/20
$cogbooks =($tab_result['Cogbooks'] == 'Y') ? "checked" : "";
// CGO-343 added by tperez 2023/04/27
$saras =($tab_result['Saras'] == 'Y') ? "checked" : "";

$free_access = ($tab_result['ContentAccess'] == 'Free') ? "checked" : "";
$login_only_access = ($tab_result['ContentAccess'] == 'Login only') ? "checked" : "";
$subscription_access = ($tab_result['ContentAccess'] == 'Subscription') ? "checked" : "";
$free_access_radio = ($tab_result['ContentAccess'] == 'Free') ? "active" : "";
$login_only_access_radio = ($tab_result['ContentAccess'] == 'Login only') ? "active" : "";
$subscription_access_radio = ($tab_result['ContentAccess'] == 'Subscription') ? "active" : "";

$col1_radio_class = ($tab_result['Columns'] == 1) ? "active" : "";
$col2_radio_class = ($tab_result['Columns'] == 2) ? "active" : "";
$public_radio_class = ($tab_result['Visibility'] == 'Public') ? "active" : "";
$private_radio_class = ($tab_result['Visibility'] == 'Private') ? "active" : "";

$student_id = 0;
$teacher_id = 0;
foreach ($groups as $gKey => $gVal) {
    if (strtolower($gVal) == "student") {
        $student_id = $gKey;
    }

    if(strtolower($gVal) == "teacher") {
        $teacher_id = $gKey;
    }
}

$student_access = ($student_id == $tab_result['UserTypeIDRestriction']) ? "selected" : "";
$teacher_access = ($teacher_id == $tab_result['UserTypeIDRestriction']) ? "selected" : "";

$student_radio_class = ($student_id == $tab_result['UserTypeIDRestriction']) ? "active" : "";
$teacher_radio_class = ($teacher_id == $tab_result['UserTypeIDRestriction']) ? "active" : "";

$icon_select_bg = ($tab_result['TabIcon']) ? DIR_REL . '/files/cup_content/images/formats/' . $tab_result['TabIcon'] : "";

// ANZGO-2902
$hm_selected = '';


?>
<input type="hidden" id="general-delete-url" value="<?php echo $v->url('/go_product_editor/general_delete/delete_tab_action/' . $tab_id); ?>" />
<div class="row">
    <div class="col-lg-4 col-lg-offset-1">
        <!-- ############# -->
        <div class="row">
            <div class="col-lg-12">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">Information</h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>

                    <div class="box-body">

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="tabname">Tab Name</label>
                                    <input type="text" placeholder="Enter tab name" id="tabname"
                                           name="edit_tab[TabName]" class="form-control go-product-editor-input"
                                           value="<?php echo $tab_result['TabName']; ?>">
                                    <input type="hidden" name="edit_tab[ID]" value="<?php echo $tab_result['ID']; ?>" />
                                </div>
                            </div>
                        </div>

                        <!-- SB-662 Added by mtanada 20201023 -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="tabname">Tab Title 2</label>
                                    <input type="text" placeholder="Enter tab title 2" id="tabTitle2"
                                           name="edit_tab[TabTitle2]" class="form-control go-product-editor-input"
                                           value="<?php echo $tab_result['TabTitle2']; ?>">
                                    <input type="hidden" name="edit_tab[ID]" value="<?php echo $tab_result['ID']; ?>" />
                                </div>
                            </div>
                        </div>

                        <?php // SB-491 modified by jbernardez 20200403 ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="tab-message">Tab Description</label>
                                    <textarea id="CustomAccessMessage" 
                                        class="form-control go-product-editor-input" 
                                        rows="3" 
                                        name="edit_tab[CustomAccessMessage]"><?php echo $tab_result['CustomAccessMessage']; ?></textarea>
                                </div>
                            </div>
                        </div>
  
                     <!-- GCAP 845 Modified by JSulit 2020/05/08 -->
                     <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                <label for="tabName">Group Tab Name</label>
                                <select class="form-control" id="groupTab-select" name="edit_tab[group_id]" >
                                    <option value = '0'
                                    <?php if ($tab_result['group_id'] === NULL || $tab_result['group_id'] === 0 ||
                                        $tab_result['group_id'] === '0'){
                                        echo "selected"; } ?> ></option>
                                        <?php if(!empty($groupTabList)){ ?>
                                            <?php foreach ($groupTabList as $tabList) { ?>
                                            <option value="<?php echo $tabList['ID']; ?>" 
                                            <?php if($tab_result['group_id'] == $tabList['ID']) {
                                                echo "selected"; }?>>
                                                <?php echo $tabList['group_name']; ?>
                                            </option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="tabname">Columns</label>
                                    <div class="container">
                                        <div class="row">
                                            <div class="pill">

                                                <div id="tab" class="btn-group" data-toggle="buttons">
                                                    <a href="#" class="btn btn-default <?php echo $col1_radio_class; ?>" data-toggle="tab">
                                                        <input type="radio" selected="<?php echo $col1_select; ?>" name="edit_tab[Columns]" class="go-product-editor-input" value="1" /> 1</a>
                                                    <a href="#" class="btn btn-default <?php echo $col2_radio_class; ?>" data-toggle="tab">
                                                        <input type="radio" selected="<?php echo $col2_select; ?>" name="edit_tab[Columns]" class="go-product-editor-input" value="2" /> 2</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">Access Rights</h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>

                    <div class="box-body">

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="tabname">Active</label>
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="edit_tab[Active]" value="Y" class="onoffswitch-checkbox go-product-editor-input" id="tab-active" <?php echo $is_active; ?>>
                                        <label class="onoffswitch-label" for="tab-active">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group tab-icon">
                                    <label>Tab Icon</label>
                                    <input type="hidden" value="<?php echo DIR_REL . '/files/cup_content/images/formats/' . $tab_result['TabIcon']; ?>" id="icon-select-url" />
                                    <select class="form-control" id="icon-select" name="edit_tab[TabIcon]">
                                        <option value="0"></option> <!--ANZGO-3175 Modified by GBalila and SCamus 25/04/2017-->
                                        <?php if(!empty($icons)) { ?>
                                            <?php foreach ($icons as $icon) { ?>
                                            <option value="<?php echo $icon['id'] ?>"
                                                    data-icon="<?php echo DIR_REL . '/files/cup_content/images/formats/' . $icon['id'] ?>"
                                                    <?php echo ($tab_result['TabIcon'] == $icon['id']) ? "selected" : "" ?>>
                                                <?php echo $icon['name'] ?>
                                            </option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="tabname">Tab visibility</label>
                                    <div class="container">
                                        <div class="row">
                                            <div class="pill">
                                                <div id="content-visibility" class="btn-group" data-toggle="buttons">
                                                    <a href="#" class="btn btn-sm btn-default <?php echo $public_radio_class; ?>" data-toggle="tab">
                                                        <input type="radio" selected="<?php echo $public_visibility; ?>" name="edit_tab[Visibility]" value="Public" class="go-product-editor-input" /> Public</a>
                                                    <a href="#" class="btn btn-sm btn-default <?php echo $private_radio_class; ?>" data-toggle="tab">
                                                        <input type="radio" selected="<?php echo $private_visibility; ?>" name="edit_tab[Visibility]" value="Private" class="go-product-editor-input" /> Private</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="tabname">Access to Tab</label>
                                    <div class="container">
                                        <div class="row">
                                            <div class="pill">
                                                <div id="access-to-tab" class="btn-group" data-toggle="buttons">
                                                    <a href="#" class="btn btn-sm btn-default <?php echo $student_radio_class; ?>" data-toggle="tab">
                                                        <input type="radio" selected="<?php echo $student_access; ?>" name="edit_tab[UserTypeIDRestriction]" value="<?php echo $student_id; ?>" class="go-product-editor-input" /> Student</a>
                                                    <a href="#" class="btn btn-sm btn-default <?php echo $teacher_radio_class ?>" data-toggle="tab">
                                                        <input type="radio" selected="<?php echo $teacher_access; ?>" name="edit_tab[UserTypeIDRestriction]" value="<?php echo $teacher_id; ?>" class="go-product-editor-input" /> Teacher</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="tabname">Content Access</label>
                                    <div class="container">
                                        <div class="row">
                                            <div class="pill">
                                                <div id="content-access" class="btn-group" data-toggle="buttons">
                                                    <a href="#" class="btn btn-sm btn-default <?php echo $free_access_radio; ?>" data-toggle="tab">
                                                        <input type="radio" value="Free" name="edit_tab[ContentAccess]" /> Free</a>
                                                    <a href="#" class="btn btn-sm btn-default <?php echo $login_only_access_radio; ?>" data-toggle="tab">
                                                        <input type="radio" value="Login only" name="edit_tab[ContentAccess]" /> Login only</a>
                                                    <a href="#" class="btn btn-sm btn-default <?php echo $subscription_access_radio; ?>" data-toggle="tab">
                                                        <input type="radio" value="Subscription" name="edit_tab[ContentAccess]" /> Subscription</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">Setup for Preview HM Products Only</h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="row">

                            <div class="col-lg-12">
                                <div class="form-group ui-widget">
                                    <label>Preview HM Product IDs</label>
                                    <!--ANZGO-3700 added by jbernardez 20180507-->
                                    <select class="form-control select2" style="width: 100%;" name="edit_tab[HmID]" id="combobox">
                                        <option value="0"></option> <!--ANZGO-3175 Modified by GBalila and SCamus 25/04/2017-->
                                        <!-- ANZGO-2902 -->
                                        <?php foreach($hm_products as $key => $value) { ?>
                                        <?php if ($tab_result['HmID'] == $key) {
                                            $hm_selected = "selected='selected' ";
                                        } ?>
                                        <option <?php echo $hm_selected; ?> value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                        <?php $hm_selected = ""; ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="hmurl">HM Testing URL</label>
                                    <input type="text" placeholder="HM Testing URL" name="edit_tab[hm_test_url]" id="edit_tab[hm_test_url]" class="form-control" value="<?php echo $tab_result['hm_test_url']; ?>">
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="hmprul">HM Production URL</label>
                                    <input type="text" placeholder="HM Production URL" name="edit_tab[hm_prod_url]" id="edit_tab[hm_prod_url]" class="form-control" value="<?php echo $tab_result['hm_prod_url']; ?>">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="col-lg-6">


        <!-- ############# -->

        <div class="row">
            <div class="col-lg-12">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">Options</h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="tabname">My Resources Link</label>
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="edit_tab[MyResourcesLink]" value="Y" class="onoffswitch-checkbox go-product-editor-input" id="myonoffswitch" <?php echo $is_resources_link; ?>>
                                        <label class="onoffswitch-label" for="myonoffswitch">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!--
                            * ANZGO-3492 Modified by John Renzo Sunico, June 12, 2017
                            * Added HMProduct Toggle button
                            !-->

                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="myonoffswitch-elevate">HM Product</label>
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="edit_tab[HMProduct]" value="Y" class="onoffswitch-checkbox go-product-editor-input" id="myonoffswitch-hotmaths" <?php echo $is_hm_product; ?>>
                                        <label class="onoffswitch-label" for="myonoffswitch-hotmaths">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="myonoffswitch-elevate">Elevate Product</label>
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="edit_tab[ElevateProduct]" value="Y" class="onoffswitch-checkbox go-product-editor-input" id="myonoffswitch-elevate" <?php echo $is_elevate_product; ?>>
                                        <label class="onoffswitch-label" for="myonoffswitch-elevate">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <?php // SB-249 mabrigos 20190710 - added coming soon switch. ?>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="myonoffswitch-comingsoon">Coming Soon</label>
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="edit_tab[ComingSoon]" value="Y" class="onoffswitch-checkbox go-product-editor-input" id="myonoffswitch-comingsoon" <?php echo $coming_soon; ?>>
                                        <label class="onoffswitch-label" for="myonoffswitch-comingsoon">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <!-- 
                            * GCAP-1734 added by jdchavez September 08, 2022 - 
                            * Added knowledge check 
                            -->
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="myonoffswitch-knowledgecheck">Knowledge Check</label>
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="edit_tab[KnowledgeCheck]" value="Y" class="onoffswitch-checkbox go-product-editor-input" id="myonoffswitch-knowledgecheck" <?php echo $knowledge_check; ?>>
                                        <label class="onoffswitch-label" for="myonoffswitch-knowledgecheck">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <!-- 
                            * CGO-233 added by mmascarinas January 20, 2023 - 
                            * Added CogBooks
                            -->
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="myonoffswitch-Cogbooks">Cogbooks</label>
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="edit_tab[Cogbooks]" value="Y" class="onoffswitch-checkbox go-product-editor-input" id="myonoffswitch-Cogbooks" <?php echo $cogbooks; ?>>
                                        <label class="onoffswitch-label" for="myonoffswitch-Cogbooks">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <!-- 
                            * CGO-343 added by tperez April 27, 2023 
                            * Added Saras
                            -->
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="myonoffswitch-Saras">Saras</label>
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="edit_tab[Saras]" value="Y" class="onoffswitch-checkbox go-product-editor-input" id="myonoffswitch-Saras" <?php echo $saras; ?>>
                                        <label class="onoffswitch-label" for="myonoffswitch-Saras">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="resource">Resource URL</label>
                                    <input type="text" placeholder="Enter resource URL" id="resource" class="form-control go-product-editor-input" name="edit_tab[ResourceURL]" value="<?php echo $tab_result['ResourceURL']; ?>">
                                </div>
                            </div>
                            <div class="col-lg-4 hidden">
                                <div class="form-group">
                                    <label for="elevate-isbn">Elevate ISBN</label>
                                    <input type="text" class="form-control go-product-editor-input" name="elevateISBN" id="elevate-isbn" placeholder="Enter Elevate ISBN">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">Text</h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>

                    <div class="box-body">

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="tabname">Always Use Public Text?</label>
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="edit_tab[AlwaysUsePublicText]" class="onoffswitch-checkbox go-product-editor-input" value="Y" id="public-text" <?php echo $use_public_text; ?>>
                                        <label class="onoffswitch-label" for="public-text">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Public Tab Text</label>

                                    <textarea id="Public_TabText" name="edit_tab[Public_TabText]" rows="10" cols="80" class="go-product-editor-input">
                                        <?php echo $tab_result['Public_TabText']; ?>
                                    </textarea>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Private Tab Text</label>

                                    <textarea id="Private_TabText" name="edit_tab[Private_TabText]" rows="10" cols="80" class="go-product-editor-input">
                                        <?php echo $tab_result['Private_TabText']; ?>
                                    </textarea>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!--ANZGO-3700 added by jbernardez 20180507-->
<script src="/packages/go_product_editor/themes/go_product_editor_theme/js/combobox.js"></script>
<link rel="stylesheet" href="/packages/go_product_editor/themes/go_product_editor_theme/css/combobox.css" />
