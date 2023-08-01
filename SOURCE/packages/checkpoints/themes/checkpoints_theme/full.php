<?php
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>
    <?php
      $b = new Area('CambridgeMain');
      $b->display($c);
    ?>
<?php $this->inc('elements/footer.php'); ?>
