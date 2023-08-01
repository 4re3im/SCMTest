<?php
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>
    <?php
    /**
     * ANZGO-3666 by Maryjes Tanada
     * remove Header area to only contain one block of content
     * so images will be editable as well
     */

      $b = new Area('CambridgeMain');
      $b->display($c);
    ?>
<?php $this->inc('elements/footer.php'); ?>
