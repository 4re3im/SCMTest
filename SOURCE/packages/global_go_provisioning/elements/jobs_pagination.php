<?php
/**
 * SB-674 Added by mtanada 20200904
 */

defined('C5_EXECUTE') || die("Access Denied.");

$pageNumbers = ((int)$total / 20) + 1;

if ($activePage === 0) {
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
        <span class="numbers <?php echo ($i === (int)$activePage) ? "active" : "" ?>">
      <a class="job-page" href="<?php echo $v->url('/dashboard/global_go_provisioning/job/displayJobs', $i) ?>">
          <?php echo $i; ?>
      </a>
  </span>
    <?php } ?>
    <span class="ccm-page-right">
    <a href="#"> Next&raquo;</a>
  </span>
</div>
