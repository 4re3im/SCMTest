<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
?>
<?php $this->inc('elements/header.php'); ?>
<div id="main-content">
    <div class="container-fluid">
        <div class="row">     
            <?php print $innerContent; ?>
            <?php
            $a = new Area('GoMain');
            $a->display($c);
            ?>
        </div><!--/row-->
    </div>
<?php $this->inc('elements/footer.php');