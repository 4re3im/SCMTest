<?php
echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Title'), false);
Loader::packageElement('alert_message_header', 'cup_content');
//To be continued by ariel
$th = Loader::helper('concrete/urls');
$title_autocomplete = $th->getToolsURL('titles-autocomplete', 'go_dashboard');
$isbn_autocomplete = $th->getToolsURL('isbn-autocomplete', 'go_dashboard');

$pkg = Package::getByHandle('go_dashboard');
$package_dir = Loader::helper('concrete/urls')->getPackageURL($pkg);
$show_title = View::url('/dashboard/cup_content/titles/show')
?>

<style type="text/css">
    div.ccm-pagination span.numbers { padding: 3px 8px; }

    .ui-autocomplete li { font-size: 12px; font-weight: bold;}

    .ui-autocomplete {
        border:  1px solid #ccc;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        border-radius: 5px 5px 5px\ 5px; 
        max-height: 500px !important;
        overflow: auto !important;
    }

    .ui-autocomplete-loading {
        background: rgba(0, 0, 0, 0) url("<?php echo $package_dir; ?>/images/ajax-loader.gif") no-repeat scroll right center;
    }
</style>


<div>
    <a href="<?php echo View::url('/dashboard/cup_content/titles/add') ?>" style="float: right; position:relative;top:-5px"  class="btn primary"><?php echo t("New Title") ?></a>
    <div class="span4">
        <form action="<?php echo View::url('/dashboard/cup_content/titles/search') ?>" method="get" id='search-title-form' >
<!--			<input type="hidden" name="ajax" value="yes"/>-->
            <table>
                <tr>
                    <td>Keywords: <input type="text" id="keywords" name="keywords"/></td>
                    <td>&nbsp;</td>
                    <td>ISBN: <input type="text" id="isbn" name="isbn"/></td>
                </tr>
                <tr>
                    <td style="padding-top:10px"><input type="submit" value="Search"/></td>
                </tr>
            </table>

            <!--
            Format: <input type="text" name="format"/>
            <input type="submit" name="submit" value="Submit"/>
            -->
            <form>
                </div>
                </div>

                <div style="clear:both;"></div>

                <div id="page_content">
                    <?php Loader::packageElement('title/dashboard_search', 'cup_content', array('titles' => $titles, 'titleList' => $titleList, 'pagination' => $pagination)); ?>
                    <div style="clear:both;height:5px;width:100%"></div>
                </div>

                <div style="clear:both"></div>
                <?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false); ?>



                <script>
                    // Modified by Paul Balila, 2016-04-12
                    // For ticket ANZUAT-16
                    function deleteItem(ref_id) {
                        var action_url = "<?php echo rtrim($this->url('/dashboard/cup_content/titles/delete'), "/"); ?>";

                        var sr = jQuery('tr[ref="' + ref_id + '"]');
                        var sr_name = sr.find('td').eq(1).html();
                        var r = confirm("Are you sure to delete '" + sr_name + "'?\n\nThis action cannot be undone.");
                        if (r == true) {
                            action_url = action_url + "/" + ref_id;
                            jQuery.getJSON(action_url, function (json) {
                                if (json.result == 'success') {
                                    sr.remove();
                                } else {
                                    alert(json.error);
                                }
                            });
                        }
                    }

                    function gotoPage(dom, pageNumber) {
                        var ref = jQuery(dom).attr('href');
                        if (ref.indexOf("ajax=yes") == -1) {
                            ref = ref + '&ajax=yes';
                        }
                        //alert(ref);
                        jQuery('#page_content').addLoadingMask();
                        jQuery.get(ref,
                                function (html_data) {
                                    jQuery('#page_content').html(html_data);
                                    jQuery('#page_content').removeLoadingMask();
                                }
                        );
                        return false;
                    }

                    function sortColumn(dom) {
                        //return true;
                        var ref = jQuery(dom).attr('href');
                        if (ref.indexOf("ajax=yes") == -1) {
                            ref = ref + '&ajax=yes';
                        }

                        //alert(ref);
                        jQuery('#page_content').addLoadingMask();
                        jQuery.get(ref,
                                function (html_data) {
                                    jQuery('#page_content').html(html_data);
                                    jQuery('#page_content').removeLoadingMask();
                                }
                        );
                        return false;
                    }

                    //ccm_setupInPagePaginationAndSorting();
                    //ccm_setupSortableColumnSelection();

                    jQuery('.ajax_form').submit(function () {
                        var action_url = jQuery(this).attr('action');
                        var submit_type = jQuery(this).attr('method');
                        if (typeof (submit_type) === 'undefined' || submit_type === false) {
                            submit_type = 'GET';
                        }

                        jQuery('#page_content').addLoadingMask();
                        jQuery.ajax({
                            type: submit_type,
                            url: action_url,
                            data: jQuery(this).serialize(),
                            success: function (html_data) {
                                jQuery('#page_content').html(html_data);
                                jQuery('#page_content').removeLoadingMask();
                            }
                        });

                        return false;
                    });

                    $(function () {
                        // auto-complete 
                        $('input#keywords').autocomplete({source: '<?php echo $title_autocomplete ?>',
                            select: function (event, ui) {
                                event.preventDefault();
                                $('#keywords').val(ui.item.keywords);
                                document.location.href = '<?php echo $show_title; ?>' + ui.item.id + '?keywords=' + ui.item.keywords;
                                return false
                            }
                        });

                        $('input#keywords').on("keypress", function (event) {
                            if (event.which == 13) {
                                event.preventDefault();
                                return false
                            }
                        });

                        // auto-complete 
                        $('input#isbn').autocomplete({source: '<?php echo $isbn_autocomplete ?>',
                            select: function (event, ui) {
                                event.preventDefault();
                                $('#isbn13').val(ui.item.isbn13);
                                document.location.href = '<?php echo $show_title; ?>' + ui.item.id + '?isbn=' + ui.item.isbn;
                                return false
                            }
                        });

                        $('input#isbn').on("keypress", function (event) {
                            if (event.which == 13) {
                                event.preventDefault();
                                return false
                            }
                        });

                    });

                </script>