<?php
defined('C5_EXECUTE') or die("Access Denied.");
$v = View::getInstance();

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
?>
<!-- To accommodate the general saving, please name all inputs to "add_tab". -->
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
                                    <input type="text" placeholder="Enter tab name" id="tabname" name="add_tab[TabName]"
                                           class="form-control go-product-editor-input" value="">
                                </div>
                            </div>
                        </div>

                        <!-- SB-662 Added by mtanada 20201023 -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="tabTitle2">Tab Title 2</label>
                                    <input type="text" placeholder="Enter tab title 2" id="tabTitle2"
                                           name="add_tab[tabTitle2]" class="form-control go-product-editor-input"
                                           value="">
                                </div>
                            </div>
                        </div>

                        <?php // SB-491 modified by jbernardez 20200403 ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="tab-message">Tab Description</label>
                                    <textarea 
                                        class="form-control go-product-editor-input" 
                                        id="CustomAccessMessage" rows="3" 
                                        name="add_tab[CustomAccessMessage]"></textarea>
                                </div>
                            </div>
                        </div>

                     <!-- GCAP 845 Modified by JSulit 2020/05/08-->
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                <label for="tabName">Group Tab Name</label>
                                <select class="form-control" id="group_id" name="add_tab[group_id]">
                                    <option value = ""> Choose Group Tab Name </option>
                                        <?php if(!empty($groupTabList)){ ?>
                                            <?php foreach ($groupTabList as $TabList) { ?>
                                            <option value="<?php echo $TabList['ID']?>">
                                                <?php echo $TabList['group_name']?>
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
                                                    <a href="#" class="btn btn-default" data-toggle="tab">
                                                        <input type="radio" selected="" name="add_tab[Columns]" class="go-product-editor-input" value="1" /> 1</a>
                                                    <a href="#" class="btn btn-default" data-toggle="tab">
                                                        <input type="radio" selected="" name="add_tab[Columns]" class="go-product-editor-input" value="2" /> 2</a>
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
                                        <input type="checkbox" name="add_tab[Active]" value="Y" class="onoffswitch-checkbox go-product-editor-input" id="tab-active">
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
                                    <select class="form-control" id="icon-select" name="add_tab[TabIcon]">
                                        <option value=""></option>
                                        <?php if(!empty($icons)) { ?>
                                            <?php foreach ($icons as $icon) { ?>
                                            <option value="<?php echo $icon['id'] ?>"
                                                    data-icon="<?php echo DIR_REL . '/files/cup_content/images/formats/' . $icon['id'] ?>">
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
                                    <label for="tabname">Visibility</label>
                                    <div class="container">
                                        <div class="row">
                                            <div class="pill">
                                                <div id="content-visibility" class="btn-group" data-toggle="buttons">
                                                    <a href="#" class="btn btn-sm btn-default" data-toggle="tab">
                                                        <input type="radio" selected="" name="add_tab[Visibility]" value="Public" class="go-product-editor-input" /> Public</a>
                                                    <a href="#" class="btn btn-sm btn-default" data-toggle="tab">
                                                        <input type="radio" selected="" name="add_tab[Visibility]" value="Private" class="go-product-editor-input" /> Private</a>
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
                                                    <a href="#" class="btn btn-sm btn-default" data-toggle="tab">
                                                        <input type="radio" name="add_tab[UserTypeIDRestriction]" value="<?php echo $student_id; ?>" class="go-product-editor-input" /> Student</a>
                                                    <a href="#" class="btn btn-sm btn-default" data-toggle="tab">
                                                        <input type="radio" name="add_tab[UserTypeIDRestriction]" value="<?php echo $teacher_id; ?>" class="go-product-editor-input" /> Teacher</a>
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
                                                    <a href="#" class="btn btn-sm btn-default" data-toggle="tab">
                                                        <input type="radio" name="add_tab[ContentAccess]" value="Free" /> Free</a>
                                                    <a href="#" class="btn btn-sm btn-default" data-toggle="tab">
                                                        <input type="radio" name="add_tab[ContentAccess]" value="Login only" /> Login only</a>
                                                    <a href="#" class="btn btn-sm btn-default" data-toggle="tab">
                                                        <input type="radio" name="add_tab[ContentAccess]" value="Subscription" /> Subscription</a>
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
                                        <input type="checkbox" name="add_tab[MyResourcesLink]" value="Y" class="onoffswitch-checkbox go-product-editor-input" id="myonoffswitch" <?php echo $is_resources_link; ?>>
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
                                        <input type="checkbox" name="add_tab[HMProduct]" value="Y" class="onoffswitch-checkbox go-product-editor-input" id="myonoffswitch-hotmaths" <?php echo $is_elevate_product; ?>>
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
                                        <input type="checkbox" name="add_tab[ElevateProduct]" value="Y" class="onoffswitch-checkbox go-product-editor-input" id="myonoffswitch-elevate" <?php echo $is_elevate_product; ?>>
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
                                        <input type="checkbox" name="add_tab[ComingSoon]" value="Y" class="onoffswitch-checkbox go-product-editor-input" id="myonoffswitch-comingsoon" <?php echo $coming_soon; ?>>
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
                                        <input type="checkbox" name="add_tab[KnowledgeCheck]" value="Y" class="onoffswitch-checkbox go-product-editor-input" id="myonoffswitch-knowledgecheck" <?php echo $knowledge_check; ?>>
                                        <label class="onoffswitch-label" for="myonoffswitch-knowledgecheck">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <!-- 
                            * CGO-233 added by mmascarinas January 20, 2023 
                            * Added CogBooks
                            -->
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="myonoffswitch-Cogbooks">Cogbooks</label>
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="add_tab[Cogbooks]" value="Y" class="onoffswitch-checkbox go-product-editor-input" id="myonoffswitch-Cogbooks" <?php echo $cogbooks; ?>>
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
                                        <input type="checkbox" name="add_tab[Saras]" value="Y" class="onoffswitch-checkbox go-product-editor-input" id="myonoffswitch-Saras" <?php echo $saras; ?>>
                                        <label class="onoffswitch-label" for="myonoffswitch-Saras">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="resource">Resource URL</label>
                                    <input type="text" placeholder="Enter resource URL" id="resource" class="form-control go-product-editor-input" name="add_tab[ResourceURL]" value="<?php echo $tab_result['ResourceURL']; ?>">
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
                                        <input type="checkbox" name="add_tab[AlwaysUsePublicText]" class="onoffswitch-checkbox go-product-editor-input" value="Y" id="public-text">
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

                                    <textarea id="Public_TabText" name="add_tab[Public_TabText]" rows="10" cols="80" class="go-product-editor-input">
                                    </textarea>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Private Tab Text</label>

                                    <textarea id="Private_TabText" name="add_tab[Private_TabText]" rows="10" cols="80" class="go-product-editor-input">
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
