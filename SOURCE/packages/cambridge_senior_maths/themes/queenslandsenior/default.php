<?php
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>
<?php
print $innerContent;

$a = new Area('queenslandsenior_content_head');
$a->display($c);
print "</div>";

$b = new Area('queenslandsenior_content_body');
$b->display($c);

?>
<?php $this->inc('elements/footer.php'); ?>
