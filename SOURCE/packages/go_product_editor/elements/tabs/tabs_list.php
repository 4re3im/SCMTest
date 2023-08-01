<?php
defined("C5_EXECUTE") or die(_("Acess Denied"));
$v = View::getInstance();
// ANZGO-2899
$sortCounter = 1;
?>
<li class="header">TABS</li>
<div>
    <a href="<?php echo $v->url('/go_product_editor/get_tab_add_form'); ?>"
       id="add-tab"
       type="button"
       class="btn btn-primary btn-xs">Add Tab</a>
</div>
<?php if (!empty($tabs)) { ?>
    <!-- ANZGO-2899 -->
    <ul id="sidebar-menu-sortable" class="sidebar-menu tabs list-group fade in">

        <!-- ANZGO-3167 Modified by Shane Camus 2017-03-21 Sorting Issue on Content-Details -->
        <input class="urltab-sort"
               type="hidden"
               value="<?php echo $v->url('/go_product_editor/updateSorting/tabs/'); ?>">

        <?php foreach ($tabs as $tab) { ?>
            <li class="ui-state-default stack">
            <input class="tab-sort"
                   type="hidden"
                   name="sorting[<?php echo $tab['ID']; ?>]"
                   value="<?php echo $sortCounter; ?>">
                <a class="move fade" href="#">
                    <i class="fa fa-sort"></i>
                </a>

                <!-- ANZGO-3701 Modified by Maryjes Tanada 2018-05-03 Default screen to Edit Tab Detail -->
                <a href="<?php echo $v->url('/go_product_editor/get_tab_edit_form/' . $tab['ID']); ?>"
                   class="edit-tab">
                    <i class="fa fa-columns"></i>
                    <span><?php echo $tab['TabName']; ?></span>
                </a>

                <small class="tabEdit label">
                    <a href="<?php echo $v->url('/go_product_editor/get_tab_contents/'. $tab['ID']); ?>"
                       data-toggle="tooltip"
                       data-placement="left"
                       title="Edit Block of Content"
                       class="tab-name">
                        <i class="fa fa-pencil"></i>
                    </a>
                </small>
            </li>
        <?php $sortCounter++; ?>
        <?php } ?>
    </ul>
<?php } ?>
