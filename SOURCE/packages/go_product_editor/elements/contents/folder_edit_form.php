<?php
defined('C5_EXECUTE') or die("Access Denied.");
$v = View::getInstance();
?>
<input type="hidden" id="general-delete-url" value="<?php echo $v->url('/go_product_editor/delete_folder_action/' . $folder_id); ?>" />
<div class="row">
    <div class="col-sm-10 col-md-offset-1">
        <div class="row">
            <div class="col-sm-12">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $folder_name; ?></h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                        <!-- /.box-tools -->
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="folname">Folder Name</label>
                                    <input type="text" placeholder="Name" id="folname" name="edit_folder[FolderName]" class="form-control" value="<?php echo $folder_name; ?>">
                                    <input type="hidden" name="edit_folder[ID]" class="form-control" value="<?php echo $folder_id; ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>