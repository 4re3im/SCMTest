<?php
    $ih = Loader::helper('concrete/interface');
?>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Subjects'), false);?>
<?php Loader::packageElement('alert_message_header', 'cup_content'); ?>

<div>
    <a href="<?php  echo View::url('/dashboard/cup_content/subjects/add')?>" style="float: right; position:relative;top:-5px"  class="btn primary"><?php  echo t("New Subject")?></a>
    <div class="span4">
        <form class="ajax_form" action="<?php  echo View::url('/dashboard/cup_content/subjects/search')?>" method="get">
            <input type="hidden" name="ajax" value="yes"/>
            Keywords: <input type="text" name="keywords"/>
        <form>
    </div>
</div>

<div style="clear:both;"></div>

<div id="page_content">
    <?php Loader::packageElement('subject/dashboard_search', 'cup_content', array('subjects' => $subjects, 'subjectList' => $subjectList, 'pagination' => $pagination));?>
    <div style="clear:both;height:5px;width:100%"></div>
</div>

<div style="clear:both"></div>
<?php  echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(true);?>

    <script>
    function deleteItem(at_id){
        var action_url = "<?php echo rtrim($this->url('/dashboard/cup_content/subjects/delete'), "/");?>";
        
        var at = jQuery('tr[ref="' + at_id + '"]');
        var at_name = at.find('td').eq(0).html();
        var r = confirm("Are you sure to delete '"+ at_name +"'?\nThis action cannot be undone.");
        if(r == true){
            action_url = action_url+"/"+at_id;
            jQuery.getJSON(action_url, function(json){
                if(json.result == 'success'){
                    at.remove();
                }else{
                    alert(json.error);
                }
            });
        }
    }

    function gotoPage(dom, pageNumber){
        var ref = jQuery(dom).attr('href');
        if(ref.indexOf("ajax=yes") == -1){
            ref = ref+'&ajax=yes';
        }
        //alert(ref);
        jQuery('#page_content').addLoadingMask();
        jQuery.get(ref, 
            function(html_data){
                jQuery('#page_content').html(html_data);
                jQuery('#page_content').removeLoadingMask();
            }
        );
        return false;
    }
    
    function sortColumn(dom){
        //return true;
        var ref = jQuery(dom).attr('href');
        if(ref.indexOf("ajax=yes") == -1){
            ref = ref+'&ajax=yes';
        }
        
        //alert(ref);
        jQuery('#page_content').addLoadingMask();
        jQuery.get(ref, 
            function(html_data){
                jQuery('#page_content').html(html_data);
                jQuery('#page_content').removeLoadingMask();
            }
        );
        return false;
    }
    
    jQuery('.ajax_form').submit(function(){
        var action_url = jQuery(this).attr('action');
        var submit_type = jQuery(this).attr('method');
        if(typeof(submit_type) === 'undefined' || submit_type === false){
            submit_type = 'GET';
        }
        
        jQuery('#page_content').addLoadingMask();
        jQuery.ajax({
            type: submit_type,
            url: action_url, 
            data: jQuery(this).serialize(),
            success: function(html_data){
                jQuery('#page_content').html(html_data);
                jQuery('#page_content').removeLoadingMask();
            }
        });
        
        return false;
    });

    </script>