<?php
defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>
<div class="container-fluid">
  <div class="row">
    <div class="col-lg-12" style="padding-left:0px;padding-right:0px;">
      <?php
        print $innerContent;
        $a = new Area('IceEmMain');
        $a->display($c);
      ?>
    </div>
  </div>
</div>
<?php $this->inc('elements/footer.php'); ?>
