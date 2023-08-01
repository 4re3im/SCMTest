<?php defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<?php
$form = Loader::helper('form');
$wform = Loader::helper('wform', 'cup_content');
$th = Loader::helper('concrete/urls');
$url = $th->getToolsURL('autocomplete', 'cup_content');

$v = View::getInstance();
$html = Loader::helper('html');

$this->addHeaderItem($html->javascript('tiny_mce/tiny_mce.js'));
$this->addHeaderItem($html->javascript('malsup/jquery.form.min.js', 'cup_content'));
?>
<?php echo $form->hidden('id', @$entry['id']); ?>
<div class="span16">
    <!-- GCAP-625 added by mtanada 20200127 -->
    <div class="clearfix">
        <?php echo $form->label('demo_id', t('Demo Entitlement ID')) ?>
        <div class="input">
            <input type="text" class="form-control span6" id="search-entitlement" search-url="<?php echo $url ?>"
                   placeholder="Just type in and select an entitlement."><br />
            <?php echo $form->text(
                'demo_id',
                @$entry['demo_id'],
                array(
                    'class' => "span6",
                    'style'=>"pointer-events: none;background-color:#E9ECEF"
                )
            )
            ?>
        </div>
    </div>
    <!-- end -->
    <div class="clearfix">
        <?php echo $form->label('search_priority', t('Search Priority')) ?>
        <div class="input">
            <?php
            $tmp_opt;
            for ($i = 0; $i <= 50; $i++) {
                $tmp = $i;
                if ($i == 0) {
                    $tmp = $i . ' [Default / No priority]';
                } elseif ($i == 50) {
                    $tmp = $i . ' [Highest]';
                }
                $tmp_opt[$i] = $tmp;
            }
            ?>
            <?php echo $form->select('search_priority', $tmp_opt, @$entry['search_priority'], array('class' => "span6")) ?>
        </div>
    </div>

    <div class="clearfix">
        <?php echo $form->label('isEnabled', t('Enable')) ?>
        <div class="input">
            <?php echo $form->select('isEnabled', array('1' => 'Yes', '0' => 'No'), @$entry['isEnabled'], array('class' => "span6")) ?>
        </div>
    </div>

    <div class="clearfix">
        <?php echo $form->label('new_product_flag', t('New Product Flag')) ?>
        <div class="input">
            <?php echo $form->select('new_product_flag', array('1' => 'Yes', '0' => 'No'), @$entry['new_product_flag'], array('class' => "span6")) ?>
        </div>
    </div>

    <div class="clearfix">
        <?php echo $form->label('is_free_shipping', t('Is Free Shipping')) ?>
        <div class="input">
            <?php echo $form->select('is_free_shipping', array('1' => 'Yes', '0' => 'No'), @$entry['is_free_shipping'], array('class' => "span6")) ?>
        </div>
    </div>

    <div class="clearfix">
        <?php echo $form->label('hasInspectionCopy', t('Has Inspection Copy')) ?>
        <div class="input">
            <?php echo $form->select('hasInspectionCopy', array('1' => 'Yes', '0' => 'No'), @$entry['hasInspectionCopy'], array('class' => "span6")) ?>
        </div>
    </div>
    <div class="clearfix">
        <?php echo $form->label('hasDownloadableFile', t('Has Downloadable File')) ?>
        <div class="input">
            <?php echo $form->select('hasDownloadableFile', array('1' => 'Yes', '0' => 'No'), @$entry['hasDownloadableFile'], array('class' => "span6")) ?>
        </div>
    </div>

    <!-- ANZGO-1708 -->
    <div class="clearfix">
        <?php echo $form->label('isGoProduct', t('Is Go Product')) ?>
        <div class="input">
            <!-- ANZGO-2648 -->
            <?php echo $form->select('isGoProduct', array('1' => 'Yes', '0' => 'No'), @$entry['isGoTitle'], array('class' => "span6")) ?>
        </div>
    </div>
    <!-- https://jira.cambridge.org/browse/ANZUAT-90 -->
    <div class="clearfix">
        <?php echo $form->label('showBuyNow', t('Show Buy Now')) ?>
        <div class="input">
            <?php echo $form->select('showBuyNow', array('1' => 'Yes', '0' => 'No'), @$entry['showBuyNow'], array('class' => "span6")) ?>
        </div>
    </div>

    <!-- ANZGO-3043 -->
    <div class="clearfix">
        <?php echo $form->label('hmTitle', t('HM Title')) ?>
        <div class="input">
            <?php echo $form->select('hmTitle', array('' => '', 'HOTMATHS' => 'HOTMATHS', 'SENIORMATHS' => 'SENIORMATHS', 'EMACS' => 'EMACS', 'DYNAMICSCIENCE' => 'DYNAMICSCIENCE'), @$entry['hmTitle'], array('class' => "span6")) ?>
        </div>
    </div>
    
    <div class="clearfix">
        <?php echo $form->label('image', t('IMAGE')) ?>
        <div class="input">
            <?php echo $form->file('image', '', array('class' => "span6")) ?>
        </div>
    </div>

    <div class="clearfix">
        <?php echo $form->label('isbn13', t('IBSN 13') . '	<span class="ccm-required">*</span>') ?>
        <div class="input">
            <?php echo $form->text('isbn13', @$entry['isbn13'], array('class' => "span6")) ?>
        </div>
    </div>

    <?php // SB-345 blocked by jbernardez 20190923 ?>
    <!--
    <div class="clearfix">
        <?php echo $form->label('isbn10', t('IBSN 10')) ?>
        <div class="input">
            <?php echo $form->text('isbn10', @$entry['isbn10'], array('class' => "span6")) ?>
        </div>
    </div>
    -->

    <div class="clearfix">
        <?php echo $form->label('name', t('Name') . '	<span class="ccm-required">*</span>') ?>
        <div class="input">
            <?php echo $form->text('name', @$entry['name'], array('class' => "span6")) ?>
        </div>
    </div>

    <?php // SB-425 added by jbernardez 20200205 ?>
    <div class="clearfix">
        <?php echo $form->label('myResourcesTitle', t('My Resources Title'))?>
        <div class="input">
            <?php echo $form->text('myResourcesTitle', @$entry['myResourcesTitle'], array('class' => "span6"))?>
        </div>
    </div>

    <div class="clearfix">
        <?php echo $form->label('customName', t('Custom Name')) ?>
        <div class="input">
            <?php echo $form->text('customName', @$entry['customName'], array('class' => "span6")) ?>
        </div>
    </div>

    <div class="clearfix">
        <?php echo $form->label('subtitle', t('Subtitle')) ?>
        <div class="input">
            <?php echo $form->text('subtitle', @$entry['subtitle'], array('class' => "span6")) ?>
        </div>
    </div>

    <div class="clearfix">
        <?php echo $form->label('customSubtitle', t('Custom Subtitle')) ?>
        <div class="input">
            <?php echo $form->text('customSubtitle', @$entry['customSubtitle'], array('class' => "span6")) ?>
        </div>
    </div>

    <div class="clearfix">
        <?php echo $form->label('edition', t('Edition')) ?>
        <div class="input">
            <?php echo $form->text('edition', @$entry['edition'], array('class' => "span6")) ?>
        </div>
    </div>

    <div class="clearfix">
        <?php echo $form->label('shortDescription', t('Short Description')) ?>
        <div class="input">
            <?php Loader::element('editor_init'); ?>
            <?php // Loader::element('editor_config'); ?>
            <?php Loader::element('editor_controls', array('mode' => 'full')); ?>
            <?php echo $form->textarea('shortDescription', @$entry['shortDescription'], array('class' => "ccm-advanced-editor")) ?>
        </div>
    </div>

    <div class="clearfix">
        <?php echo $form->label('longDescription', t('Long Description')) ?>
        <div class="input">
            <?php echo $form->textarea('longDescription', @$entry['longDescription'], array('class' => "ccm-advanced-editor")) ?>
        </div>
    </div>

    <div class="clearfix">
        <?php echo $form->label('authors', t('Authors')) ?>
        <div class="input">
            <?php echo $wform->multipleItems('authors', @$entry['authors']); ?>
        </div>
    </div>

    <div class="clearfix">
        <?php echo $form->label('content', t('Content')) ?>
        <div class="input">
            <?php echo $form->textarea('content', @$entry['content'], array('class' => "ccm-advanced-editor")) ?>
        </div>
    </div>

    <div class="clearfix">
        <?php echo $form->label('feature', t('Feature')) ?>
        <div class="input">
            <?php echo $form->textarea('feature', @$entry['feature'], array('class' => "ccm-advanced-editor")) ?>
        </div>
    </div>

    <div class="clearfix">
        <?php echo $form->label('yearLevels', t('Year Levels')) ?>
        <div class="input">
            <?php echo $wform->yearlevelSelection(@$entry['yearLevels'], 'yearLevels'); ?>
        </div>
    </div>

    <div class="clearfix">
        <?php echo $form->label('formats', t('Formats')) ?>
        <div class="input">
            <?php echo $wform->multipleItems('formats', @$entry['formats']); ?>
        </div>
    </div>

    <div class="clearfix">
    <fieldset style="padding-left: 80px;">
        <div class="clearfix">
            <?php echo $form->label('aus_circa_prize', t('Aus Circa Prize') . '') ?>
            <div class="input">
                <?php echo $form->text('aus_circa_prize', @$entry['aus_circa_prize'], array('class' => "span2")) ?>
            </div>
        </div>

        <div class="clearfix">
            <?php echo $form->label('nz_circa_prize', t('NZ Circa Prize') . '') ?>
            <div class="input">
                <?php echo $form->text('nz_circa_prize', @$entry['nz_circa_prize'], array('class' => "span2")) ?>
            </div>
        </div>
    </fieldset>

    <div class="clearfix">
        <?php echo $form->label('goUrl', t('Interactive Textbook Url') . '	<span class="ccm-required">*</span>') ?>
        <div class="input">
            <?php echo $form->text('goUrl', @$entry['goUrl'], array('class' => "span6")) ?>
        </div>
    </div>

    <div class="clearfix">
        <?php echo $form->label('previewUrl', t('previewUrl') . '	<span class="ccm-required">*</span>') ?>
        <div class="input">
            <?php echo $form->text('previewUrl', @$entry['previewUrl'], array('class' => "span6")) ?>
        </div>
    </div>

    <div class="clearfix">
        <?php echo $form->label('type', t('Type') . '	<span class="ccm-required">*</span>') ?>
        <div class="input">
            <?php echo $wform->titleTypeSelection(@$entry['type']); ?>
        </div>
    </div>

    <div class="clearfix">
        <?php echo $form->label('descriptionOption', t('Description Option') . '	<span class="ccm-required">*</span>') ?>
        <div class="input">
            <?php echo $wform->titleDescriptionOptionSelection(@$entry['descriptionOption']); ?>
        </div>
    </div>

    <div class="clearfix title-field-type-part-of-series">
        <?php echo $form->label('series', t('Series') . '	<span class="ccm-required">*</span>') ?>
        <div class="input">
            <?php echo $wform->singleItem('series', @$entry['series']); ?>
        </div>
    </div>


    <div class="clearfix title-field-type-stand-alone">
        <?php echo $form->label('divisions', t('Division') . '	<span class="ccm-required">*</span>') ?>
        <div class="input">
            <?php echo $wform->divionsSelection(@$entry['divisions']); ?>
        </div>
    </div>

    <div class="clearfix title-field-type-stand-alone">
        <?php echo $form->label('regions', t('Region') . '	<span class="ccm-required">*</span>') ?>
        <div class="input">
            <?php echo $wform->regionsSelection(@$entry['regions']); ?>
        </div>
    </div>


    <div class="clearfix title-field-type-stand-alone">
        <?php echo $form->label('subjects', t('Subjects')) ?>
        <div class="input">
            <?php echo $wform->multipleItems('subjects', @$entry['subjects']); ?>
        </div>
    </div>

    <div class="clearfix title-field-type-stand-alone">
        <?php echo $form->label('tagline', t('Tagline')) ?>
        <div class="input">
            <?php echo $form->textarea('tagline', @$entry['tagline'], array('class' => "span6")) ?>
        </div>
    </div>

    <div class="clearfix title-field-type-stand-alone">
        <?php echo $form->label('reviews', t('Reviews')) ?>
        <div class="input">
            <?php echo $form->textarea('reviews', @$entry['reviews'], array('class' => "ccm-advanced-editor")) ?>
        </div>
    </div>



    <div style="width:100%;height:3px;border-top:1px solid #333333;border-bottom:1px solid #333333;">
    </div>

    <div style="width:100%;height:20px;">
    </div>

    <div class="clearfix">
        <?php echo $form->label('relatedTitleIDs', t('Related Titles')) ?>
        <div class="input">
            <?php echo $wform->multipleTitleItems('relatedTitleIDs', @$entry['relatedTitleIDs']); ?>
        </div>
    </div>


    <div class="clearfix">
        <?php echo $form->label('supportingTitleIDs', t('Supporting Titles')) ?>
        <div class="input">
            <?php echo $wform->multipleTitleItems('supportingTitleIDs', @$entry['supportingTitleIDs']); ?>
        </div>
    </div>


    <hr/>
    <div class="clearfix">
        <?php echo $form->label('cart_message', t('Cart Message')) ?>
        <div class="input">
            <?php echo $form->text('cart_message', @$entry['cart_message'], array('class' => "span6")) ?>
        </div>
    </div>

    <div class="clearfix">
        <?php echo $form->label('cart_popup_content', t('Cart Popup Content')) ?>
        <div class="input">
            <?php echo $form->textarea('cart_popup_content', @$entry['cart_popup_content'], array('class' => "ccm-advanced-editor")) ?>
        </div>
    </div>


</div>

<div class="clearfix"></div>

<script>
    var switchTitleType = function () {
        var selectedType = jQuery('select#type').val();

        if (selectedType == 'part of series') {
            jQuery('.title-field-type-stand-alone').hide('slow');
            jQuery('.title-field-type-study-guide').hide('slow');
            jQuery('.title-field-type-part-of-series').show('slow');

            jQuery('select#descriptionOption').find('option').removeAttr('disabled').show();
        } else if (selectedType == 'stand alone') {
            jQuery('.title-field-type-part-of-series').hide('slow');
            jQuery('.title-field-type-study-guide').hide('slow');
            jQuery('.title-field-type-stand-alone').show();

            if (jQuery('select#descriptionOption').val() == "series short description"
                    || jQuery('select#descriptionOption').val() == "series long description") {
                jQuery('select#descriptionOption').val("title short description");
            }

            jQuery('select#descriptionOption').find('option[value="series short description"]').attr('disabled', 'disabled').hide();
            jQuery('select#descriptionOption').find('option[value="series long description"]').attr('disabled', 'disabled').hide();
        } else if (selectedType == 'study guide') {
            jQuery('.title-field-type-stand-alone').hide('slow');
            jQuery('.title-field-type-part-of-series').hide('slow');
            jQuery('.title-field-type-study-guide').show('slow');

            if (jQuery('select#descriptionOption').val() == "series short description"
                    || jQuery('select#descriptionOption').val() == "series long description") {
                jQuery('select#descriptionOption').val("title short description");
            }

            jQuery('select#descriptionOption').find('option[value="series short description"]').attr('disabled', 'disabled').hide();
            jQuery('select#descriptionOption').find('option[value="series long description"]').attr('disabled', 'disabled').hide();
        }
    };

    jQuery('select#type').change(switchTitleType);

    jQuery(function () {
        switchTitleType();
    });






</script>




<?php
$uh = Loader::helper('concrete/urls');
$dashboard_select_format_link = $uh->getToolsURL('format/dashboard_selection', 'cup_content');
$dashboard_select_author_link = $uh->getToolsURL('author/dashboard_selection', 'cup_content');
$dashboard_select_subject_link = $uh->getToolsURL('subject/dashboard_selection', 'cup_content');
$dashboard_select_series_link = $uh->getToolsURL('series/dashboard_selection', 'cup_content');
$dashboard_select_titles_link = $uh->getToolsURL('title/dashboard_multiple_selection', 'cup_content');

// ANZGO-1738
$dashboard_select_tab_link = $uh->getToolsURL('tabs/dashboard_tab', 'cup_content');
$dashboard_select_tabs_link = $uh->getToolsURL('tabs/dashboard_selection', 'cup_content');

// ANZGO-1812
$dashboard_content_folders_link = $uh->getToolsURL('content_folders/dashboard_add_folders', 'cup_content');
$dashboard_content_folder_link = $uh->getToolsURL('content_folders/dashboard_content_folder', 'cup_content');
?>

<script>
    jQuery('.wform-button[ref="authors"]').click(function () {
        var dashboard_select_format_link = "<?php echo $dashboard_select_author_link; ?>";

        jQuery.colorbox({html: '<div style="width:630px;height:500px" id="popup-window-content"></div>',
            width: "630px",
            height: "500px"});

        var selected_formats = new Array();
        jQuery('.multiple-items-group[ref="authors"] span.value_item input').each(function () {
            selected_formats.push(jQuery(this).val());
        });

        var submit_data = {
            'selected_values': selected_formats
        };

        jQuery.ajax({
            type: 'post',
            url: dashboard_select_format_link,
            data: submit_data, //jQuery(this).serialize(),
            success: function (html_data) {
                var p = jQuery('#popup-window-content').parent();
                p.empty();
                p.html(html_data);
            }
        });

    });
    
    jQuery('.wform-button[ref="formats"]').click(function () {
        
        var dashboard_select_format_link = "<?php echo $dashboard_select_format_link; ?>";

        jQuery.colorbox({html: '<div style="width:630px;height:500px" id="popup-window-content"></div>',
            width: "630px",
            height: "500px"});

        var selected_formats = new Array();
        jQuery('.multiple-items-group[ref="formats"] span.value_item input').each(function () {
            selected_formats.push(jQuery(this).val());
        });

        var submit_data = {
            'selected_values': selected_formats
        };

        jQuery.ajax({
            type: 'post',
            url: dashboard_select_format_link,
            data: submit_data, //jQuery(this).serialize(),
            success: function (html_data) {
                var p = jQuery('#popup-window-content').parent();
                p.empty();
                p.html(html_data);
            }
        });
    });
    
    jQuery('.wform-button[ref="series"]').click(function () {
        var dashboard_select_subject_link = "<?php echo $dashboard_select_series_link; ?>";

        jQuery.colorbox({html: '<div style="width:630px;height:500px" id="popup-window-content"></div>',
            width: "630px",
            height: "500px"});

        var selected_values = new Array();
        jQuery('.multiple-items-group[ref="series"] span.value_item input').each(function () {
            selected_values.push(jQuery(this).val());
        });

        var submit_data = {
            'selected_values': selected_values
        };

        jQuery.ajax({
            type: 'post',
            url: dashboard_select_subject_link,
            data: submit_data, //jQuery(this).serialize(),
            success: function (html_data) {
                var p = jQuery('#popup-window-content').parent();
                p.empty();
                p.html(html_data);
            }
        });

    });
    
    jQuery('.wform-button[ref="relatedTitleIDs"]').click(function () {
        var dashboard_select_titles_link = "<?php echo $dashboard_select_titles_link; ?>";
        /*
         jQuery.colorbox({href:dashboard_select_format_link,
         width: "530px",
         height: "500px"});
         */

        //alert('subjects');

        jQuery.colorbox({html: '<div style="width:630px;height:500px" id="popup-window-content"></div>',
            width: "630px",
            height: "500px"});

        var selected_values = new Array();
        jQuery('.multiple-items-group[ref="relatedTitleIDs"] span.value_item input').each(function () {
            selected_values.push(jQuery(this).val());
        });

        var submit_data = {
            'selected_values': selected_values,
            'fieldname': 'relatedTitleIDs'
        };

        jQuery.ajax({
            type: 'post',
            url: dashboard_select_titles_link,
            data: submit_data, //jQuery(this).serialize(),
            success: function (html_data) {
                var p = jQuery('#popup-window-content').parent();
                p.empty();
                p.html(html_data);
            }
        });

    });
    
    jQuery('.wform-button[ref="supportingTitleIDs"]').click(function () {
        var dashboard_select_titles_link = "<?php echo $dashboard_select_titles_link; ?>";
        /*
         jQuery.colorbox({href:dashboard_select_format_link,
         width: "530px",
         height: "500px"});
         */

        //alert('subjects');

        jQuery.colorbox({html: '<div style="width:630px;height:500px" id="popup-window-content"><div style="width:620px;height:499px" id="popup-ajax-loading"></div></div>',
            width: "630px",
            height: "500px"});

        var selected_values = new Array();
        jQuery('.multiple-items-group[ref="supportingTitleIDs"] span.value_item input').each(function () {
            selected_values.push(jQuery(this).val());
        });

        var submit_data = {
            'selected_values': selected_values,
            'fieldname': 'supportingTitleIDs'
        };

        jQuery.ajax({
            type: 'post',
            url: dashboard_select_titles_link,
            data: submit_data, //jQuery(this).serialize(),
            success: function (html_data) {
                var p = jQuery('#popup-window-content').parent();
                p.empty();
                p.html(html_data);
            }
        });

    });
    
    jQuery('.wform-button[ref="subjects"]').click(function () {
        var dashboard_select_subject_link = "<?php echo $dashboard_select_subject_link; ?>";
        /*
         jQuery.colorbox({href:dashboard_select_format_link,
         width: "530px",
         height: "500px"});
         */

        //alert('subjects');

        jQuery.colorbox({html: '<div style="width:630px;height:500px" id="popup-window-content"></div>',
            width: "630px",
            height: "500px"});

        var selected_values = new Array();
        jQuery('.multiple-items-group[ref="subjects"] span.value_item input').each(function () {
            selected_values.push(jQuery(this).val());
        });

        var submit_data = {
            'selected_values': selected_values
        };

        jQuery.ajax({
            type: 'post',
            url: dashboard_select_subject_link,
            data: submit_data, //jQuery(this).serialize(),
            success: function (html_data) {
                var p = jQuery('#popup-window-content').parent();
                p.empty();
                p.html(html_data);
            }
        });

    });
</script>