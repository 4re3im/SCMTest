<?php

/**
 * PoweredByHotMaths Single Page
 */

defined('C5_EXECUTE') || die("Access Denied.");?>

<div class="container-fluid">
  <div class="row">
    <div class="col-lg-12" style="padding-left:0px;padding-right:0px;">
      <?php
        print $innerContent;
        $a = new Area('PoweredByHotmathsMain');
        $a->display($c);
      ?>
    </div>
  </div>
</div>