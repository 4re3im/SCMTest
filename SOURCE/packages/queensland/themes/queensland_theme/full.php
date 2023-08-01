<?php

/**
 * Full File for Queensland Theme
 */

defined('C5_EXECUTE') || die(_("Access Denied."));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <?php $this->inc('elements/html_head.php');?>
</head>
<body>
<?php $this->inc('elements/header.php');?>

<div class="cup-content-area-main">
    <?php
        $content = new Area('Main');
        $content->display($c);
    ?>
</div>

<?php $this->inc('elements/footer.php'); ?>
</body>
</html>