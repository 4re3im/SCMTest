<?php
defined('C5_EXECUTE') or die(_("Access Denied"));

?>
<script> var titlesURL = "<?php echo $this->url('/go/titles/'); ?>"</script>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="generalModalLabel"><?php echo $title; ?> Chapters</h4>
</div>
<div class="modal-body container-bg-2">
    <div class="container-fluid">
        <?php  echo $contents; ?>
    </div>
</div>
