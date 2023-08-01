<div class="alert alert-success" role="alert" id="modal-alert" style="display:none;">...</div>

<form action="../tng_redirect/files/db_handler.php" type="post" id="redirector-form">
    <input type="hidden" name="details[id]" value="<?php echo $result['id'] ?>"/>
    <input type="hidden" name="details[epub]" value="<?php echo $result['epub'] ?>"/>
    <div class="form-group">
        <label>Redirector URL</label>
        <input type="url" class="form-control" name="details[url]" placeholder="URL" value="<?php echo $result['url']; ?>">
    </div>
    <div class="form-group">
        <label>Title</label>
        <input type="text" class="form-control" name="details[title]" placeholder="Title" value="<?php echo $result['title']; ?>">
    </div>
    <div class="form-group">
        <label>Subheading</label>
        <input type="text" class="form-control" name="details[chapter]" placeholder="Subheading" value="<?php echo $result['chapter']; ?>">
    </div>
    <div class="form-group">
        <label>Notes</label>
        <textarea class="form-control" name="details[notes]" rows="3"><?php echo $result['notes']; ?></textarea>
    </div>
</form>
