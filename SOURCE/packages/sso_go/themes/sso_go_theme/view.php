<?php
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>
<?php
print $innerContent;
$a = new Area('SsoGo');
$a->display($c);
?>
<?php $this->inc('elements/footer.php'); ?>
