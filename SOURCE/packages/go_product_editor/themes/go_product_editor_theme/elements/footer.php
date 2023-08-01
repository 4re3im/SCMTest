<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php 
    // SB-385 added by jbernardez 20191104
    global $c;
    $naviHelper = Loader::helper('navigation');
    $cpl = $naviHelper->getCollectionURL($c);
    $result = strpos($cpl, 'go_series_editor');

    if ($result) {
        $editor = 'Go Series Editor';
    } else {
        $editor = 'Go Product Editor';
    }
?>
<!-- Main Footer -->
    <footer class="main-footer">
        <div>
            <strong>Copyright &copy; <?php echo date('Y'); ?> | <?php echo $editor; ?></strong>
            <a class="pull-right" href="#HOME">Cambridge GO</a>
        </div>
    </footer>
</div>
<?php
if(!$c->isEditMode()) {
    // We put this in this if construct to avoid conflicting with the concrete5 edit functionality.
    ?>
    <script src="<?php echo $this->getThemePath(); ?>/js/jquery/jquery-2.2.3.min.js"></script>
<?php } ?>
    <!-- ANZGO-2899 -->
    <script src="<?php echo $this->getThemePath(); ?>/js/jquery/jquery-ui.min.js"></script>
    <script src="<?php echo $this->getThemePath(); ?>/js/bootstrap/bootstrap.min.js"></script>
    <script src="<?php echo $this->getThemePath(); ?>/js/plugins/select2/select2.full.min.js"></script>
    <script src="<?php echo $this->getThemePath(); ?>/js/plugins/fastclick/fastclick.js"></script>
    <script src="<?php echo $this->getThemePath(); ?>/js/AdminLTE.min.js"></script>
    <script src="<?php echo $this->getThemePath(); ?>/js/plugins/ckeditor-4.5.11/ckeditor.js"></script>
    <script src="<?php echo $this->getThemePath(); ?>/js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
    <script src="<?php echo $this->getThemePath(); ?>/js/plugins/malsup/jquery.form.min.js"></script>
    <script src="<?php echo $this->getThemePath(); ?>/js/custom.js"></script>
    <!-- Modified by JSulit 11/03/2021 SB-976 -->
    <script src="<?php echo $this->getThemePath(); ?>/js/plugins/plupload/plupload.full.min.js"></script>
    <?php
    // SB-313 modified by mabrigos 20190905
    // SB-491 modified by jbernardez 20200406
     ?>
    <script src="<?php echo $this->getThemePath(); ?>/js/main.js?v=6.8"></script>
</body>
</html>
