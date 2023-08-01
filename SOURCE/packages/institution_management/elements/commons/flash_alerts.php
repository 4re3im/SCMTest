<?php
$alerts = false;
if (isset($_SESSION['alerts'])) {
    $alerts = $_SESSION['alerts'];
    unset($_SESSION['alerts']);
}
?>
<div class="span11">
    <?php if (isset($alerts['error'])):
        $error = $alerts['error']; ?>
        <div class="alert alert-error">
            <button class="close" data-dismiss="alert" type="button">x</button>
            <?php if (is_array($error)): ?>
                <?php foreach ($error as $each_error): ?>
                    <p><?php echo $each_error; ?></p>
                <?php endforeach; ?>
            <?php elseif (is_string($error)): ?>
                <p><?php echo $error; ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>



    <?php if (isset($alerts['success'])):
        $success = $alerts['success'] ?>
        <div class="alert alert-success">
            <button class="close" data-dismiss="alert" type="button">x</button>
            <?php if (is_array($success)): ?>
                <?php foreach ($success as $each): ?>
                    <p><?php echo $each; ?></p>
                <?php endforeach; ?>
            <?php elseif (is_string($success)): ?>
                <p><?php echo $success; ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>


    <?php if (isset($alerts['info'])):
        $info = $alerts['info'] ?>
        <div class="alert alert-info">
            <button class="close" data-dismiss="alert" type="button">x</button>
            <?php if (is_array($info)): ?>
                <?php foreach ($info as $each): ?>
                    <p><?php echo $each; ?></p>
                <?php endforeach; ?>
            <?php elseif (is_string($info)): ?>
                <p><?php echo $info; ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
