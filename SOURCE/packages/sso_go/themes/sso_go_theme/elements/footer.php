<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php
if(!$c->isEditMode()) {
    // We put this in this "if" construct to avoid conflicting with the concrete5 edit functionality.
    ?>
    <script src="<?php echo $this->getThemePath(); ?>/js/jquery/jquery-2.2.3.min.js"></script>
<?php } ?>
    <script src="<?php echo $this->getThemePath(); ?>/js/jquery/jquery-ui.min.js"></script>
    <script src="<?php echo $this->getThemePath(); ?>/js/bootstrap/bootstrap.min.js"></script>
<?php Loader::element('footer_required'); ?>
</body>
</html>
