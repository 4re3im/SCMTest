<?php if($error){ ?>
<div class="alert alert-error">
	<button class="close" data-dismiss="alert" type="button">x</button>

        <?php foreach($error->getList() as $each_error):?>
        <?php echo $each_error;?>
        <?php endforeach;?>
</div>
<?php } ?>