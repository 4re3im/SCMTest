<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>
<div class="ccm-ui">
    <div class="ccm-pane">
        <div class="ccm-pane-header">
            <h3>Edit Notification</h3>
        </div>
        <div class="ccm-pane-body">
            <div class="ccm-pane-body-inner">
                <?php if(isset($add_status)) { ?>
                <div class="alert-message info">
                        <a class="close" href="#">×</a>
                        <p><?php echo $add_status; ?></p>
                    </div>
                <?php } ?>
                <form action="<?php echo $this->url('/dashboard/notification/edit/do_edit/' . $notif['nID']); ?>" method="POST" id="announcementForm">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>Creation Date: <?php echo date('M d, Y H:i A', strtotime($notif['dateCreated'])); ?></td>
                                <td>Status: <?php echo ($notif['nStatus']) ? 'Active' : 'Inactive`'; ?>
                                    <label class="pull-right">
                                        <?php echo ($notif['nStatus']) ? 'Deactivate' : 'Activate`'; ?>
                                        <input type="checkbox" name="notif[nStatus]" value="<?php echo ($notif['nStatus']) ? 0 : 1; ?>" class="pull-left" style="margin-right: 8px;"/>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                            <tr>
                                <td>Notification Title</td>
                                <td>Notification Date</td>
                            </tr>
                            <tr>
                                <td><input type="text" name="notif[nTitle]" value="<?php echo $notif['nTitle']; ?>" ></td>
                                <td><input type="text" name="notif[nDate]" value="<?php echo date('M d, Y', strtotime($notif['nDate'])); ?>" class="datepicker" ></td>
                            </tr>
                            <tr>
                                <td colspan="2">Content</td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <?php Loader::element('editor_config'); ?>
                                    <textarea name="notif[nContent]" class="ccm-advanced-editor">
                                        <?php echo ($notif['nContent']); ?>
                                    </textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>Notification Type</td>
                                <td>
                                    <label class="radio"><input type="radio" name="notif[nType]" value="0" <?php echo ($notif['linkedTitles'] == "0") ? "checked" : ""; ?> class="annType"> <span>General</span></label>
                                    <label class="radio"><input type="radio" name="notif[nType]" value="1" <?php echo ($notif['linkedTitles'] != "0") ? "checked" : ""; ?> class="annType"> <span>Linked To Titles</span></label>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"><input type="text" id="searchTitles" href="<?php echo $this->url('/dashboard/notification/actions/get_titles'); ?>" <?php echo ($notif['linkedTitles'] == "0") ? "disabled" : ""; ?> placeholder="Search Titles"/></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <select id="titleList" multiple="" name="notif[linkedTitles][]" <?php echo ($notif['linkedTitles'] == "0") ? "disabled" : ""; ?>>
                                        <?php if($notif['linkedTitles'] == "0") { ?>
                                            <option id="tlPlaceholder">Nothing selected</option>
                                        <?php } else { ?>
                                            <?php foreach ($notif['titles'] as $title) { ?>
                                            <option value="<?php echo $title['id']; ?>" selected=""><?php echo $title['displayName']; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                        
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type="submit" class="btn btn-success" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
                
            </div>
            
            <hr />
            
            <div class="ccm-pane-body-footer">
                
            </div>
        </div>
    </div>
</div>