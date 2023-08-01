<?php

defined('C5_EXECUTE') || die("Access Denied.");

// ANZGO-3642 Added by John Renzo Sunico, 02/22/2018
$pageNumbers = ((int)($total / $interval)) + 1;

if ($activePage == 0) {
    $activePage = 1;
}
$v = View::getInstance();
?>
<div class="ccm-pagination">
  <span class="ccm-page-left">
    <a href="#"> &laquo;Previous</a>
  </span>
    <?php
    for ($i = 1; $i <= $pageNumbers; $i++) { ?>
        <span class="numbers <?php echo ($i == $activePage) ? "active" : "" ?>">
      <a href="<?php echo $v->url('/dashboard/provisioning/setup/displayUsers', $i) ?>"><?php echo $i; ?></a>
  </span>
    <?php } ?>
    <span class="ccm-page-right">
    <a href="#"> Next&raquo;</a>
  </span>
</div>
