<?php 
defined('C5_EXECUTE') or die(_("Access Denied.")) ;
$toggle_id = preg_replace('/[^A-Za-z0-9\-]/', '', strtolower($title));
?>
<div class="row"> 
    <div class="col-lg-4 col-lg-offset-4 col-md-6  col-md-offset-3 col-sm-12 col-xs-12">
        <div class="go-accordion" id="accordion_<?php echo $toggle_id; ?>">
            <div class="panel panel-default go-panel">
                <div class="panel-heading go-first-panel" role="tab" id="headingOne_<?php echo $toggle_id; ?>">
                    <a class="collapsed go-panel-header" data-toggle="collapse" href="#<?php echo $toggle_id; ?>" data-parent="#accordion_<?php echo $toggle_id; ?>" aria-expanded="false" aria-controls="<?php echo $toggle_id; ?>">
                        <span class="glyphicon glyphicon-triangle-bottom pull-right"></span>
                        <h3 class="panel-title about">
                            <div class="tab-name">
                                <?php echo $title ?>
                            </div>
                        </h3>
                    </a>
                </div>
                <div id="<?php echo $toggle_id; ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne_<?php echo $toggle_id; ?>">
                    <div>
                        <?php echo $content ?>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>