<?php
defined('C5_EXECUTE') or die(_("Access Denied"));
$v = View::getInstance();
?>
<?php if ($title_id) { ?>
<aside class="main-sidebar">
    <section class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="image">
                <img src="<?php echo $url . $title['isbn13'] ?>" alt="Book Cover">
            </div>
            <div class="info">
                <h4 title="<?php echo $title['name']; ?>">
                    <?php //SB-313 Added by mabrigos 20190903 ?>
                    <a class="title-link" href="/go/titles/<?php echo $title['prettyUrl'] ?>" onclick="checkUnsaveData(event)"><?php echo $title['name'] ?></a>
                </h4>
                <input type="hidden" id="title-id" value="<?php echo $title_id ?>">
            </div>
        </div>
        <ul class="sidebar-menu tabs list-group fade in">
            <?php Loader::packageElement('tabs/tabs_list', 'go_product_editor', array('tabs' => $tabs));?>
        </ul>
        <ul class="sidebar-menu content fade">
            <?php
            Loader::packageElement(
                'contents/folders_list',
                'go_product_editor',
                array('folders' => $folders,'title_id' => $title_id)
            );
            ?>
        </ul>
    </section>

</aside>

<form role="form" action="<?php echo $v->url('/go_product_editor/general_form_landing'); ?>" id="general-form">
    <div class="content-wrapper">
        <section class="content-header">
            <div class="save">
                <?php //SB-827 modified by JSulit 20220202 ?>
                <button 
                        class="btn btn-dark superdelete"
                        type="submit" 
                        id = 'general-delete-btn'
                        index="0"
                        style="color:white;"
                        >
                    Delete
                </button>
                <button class="btn btn-success" type="submit" id="general-save-btn">Save</button>
            </div>
        </section>
        <!-- Main content -->
        <section class="content">
        </section>
    </div>s
</form>
<?php //CGO-253 Added by mmascarinas 20230209 ?>

<script type="text/javascript">
    var el = document.getElementById('general-delete-btn');
    el.onclick = function(e){
        const confirmed = confirm(
            'This tab will be removed from all PEAS products. Are you sure to delete?'
        );
        if (!confirmed) {
            e.preventDefault();
            e.stopPropagation();
        }
    }
</script>

<?php } else { ?>
<h3 style="color: white" class="text-center">No title selected.</h3>
<?php } ?>
