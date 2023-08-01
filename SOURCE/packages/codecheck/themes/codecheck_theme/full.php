<?php
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>
<?php

print $innerContent;

$a = new Area('CodeCheckMain');
$a->display($c);

?>
<?php $this->inc('elements/footer.php'); ?>
