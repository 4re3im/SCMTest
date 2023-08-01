<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <pre>
                <?php print_r($content) ?>
            </pre>
        </div>
        <?php if ($content) { ?>
            <div class="col-lg-4 col-lg-offset-4">
                <h1><?php echo $content->main->TITLE; ?></h1>
                <p><?php echo date('d F Y',  strtotime($content->main->DATE)); ?></p>
            </div>
        <?php } else { ?>
            <div class="alert alert-warning" role="alert">
                <p>Content not found... :Z</p>
            </div>
        <?php } ?>
    </div>
</div>