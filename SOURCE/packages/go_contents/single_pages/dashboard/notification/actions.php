<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>
<div class="ccm-ui">
    <div class="ccm-pane">
        <div class="ccm-pane-header">
            <h3>Go Notifications</h3>
        </div>
        <div class="ccm-pane-body">
            <div class="ccm-pane-body-inner">
                <?php if(isset($status)) { ?>
                <div class="alert-message info">
                    <a class="close" href="#">×</a>
                    <p><?php echo $status; ?></p>
                </div>
                <?php } ?>
                <div id="ccm-list-wrapper">
                    <h3>Search Notifications</h3>
                    <br />
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>
                                    <label>Search Titles
                                        <input type="text" style="width:80%" class="notif-search" id="nTitle" />
                                    </label>
                                </td>
                                <td>
                                    <label>Page
                                        <select class="notif-search" id="nPage" href="<?php echo $this->url('/dashboard/notification/actions/paginate'); ?>">
                                            <option value="10" selected="">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                            <option value="500">500</option>
                                        </select>
                                    </label>
                                </td>
                            </tr>
                            <tr id="advanced-search" style="display:none;">
                                <td colspan="2">
                                    <label>Search by Date
                                        <input type="text" class="datepicker notif-search" id="nDate" style="width:50% !important;" />
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <a href="#" id="advanced-search-trigger" class="pull-right">Advanced Search <i class="icon-circle-arrow-down"></i></a>
                                    <a href="<?php echo $this->url('/dashboard/notification/actions/'); ?>" class="btn btn-info">Clear Searches</a>
                                    <a href="<?php echo $this->url('/dashboard/notification/actions/search/'); ?>" class="btn btn-success" id="go-search">Search <i class="icon-search"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <a href="<?php echo $this->url('/dashboard/notification/actions/delete') ?>" class="btn btn-danger" id="del-ticked" disabled>Delete checked</a>
                    <br />
                    <br />
                    <table class="ccm-results-list" id="announcementTable">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Date</th>
                                <th>Title</th>
                                <th>Date Created</th>
                                <th>Status</th>
                                <th>Type</th>
                                <th>Last Modified</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($notif) { ?>
                                <?php foreach ($notif as $a) { ?>
                                <tr>
                                    <td><input type="checkbox" value="<?php echo $a['nID']; ?>" class="tick-notif" name="tick-notifs[]"/></td>
                                    <td><a href="<?php echo $this->url('/dashboard/notification/edit/' . $a['nID']); ?>"><?php echo date('M d, Y', strtotime($a['nDate'])); ?></a></td>
                                    <td><?php echo $a['nTitle']; ?></td>
                                    <td><?php echo date('M d, Y H:i',  strtotime($a['dateCreated'])); ?></td>
                                    <td><?php echo ($a['nStatus']) ? 'Active' : 'Hidden'; ?></td>
                                    <td><?php echo ($a['linkedTitles'] == "0") ? 'General' : 'Linked to Titles'; ?></td>
                                    <td><?php echo date('M d, Y H:i',  strtotime($a['dateModified'])); ?></td>
                                </tr>    
                                <?php } ?>
                            <?php } else { ?>
                            <tr>
                                <td>No notifications... :|</td>
                            </tr>
                            <?php } ?>
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="pagination ccm-pagination">
                <input type="hidden" value="<?php echo $this->url('/dashboard/notification/actions/navigate'); ?>" id="navigateUrl" />
                <input type="hidden" value="<?php echo $this->url('/dashboard/notification/actions/refresh_nav'); ?>" id="widgetUrl" />
                <div id="tableWidget">
                    <?php echo $table_widget; ?>
                </div>
            </div>
            <hr />
            <form action="<?php echo $this->url('/dashboard/notification/actions/add'); ?>" method="POST" id="announcementForm">
                <h3>Add Notification</h3>
                <br />
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td>Notification Title</td>
                            <td>Notification Date</td>
                        </tr>
                        <tr>
                            <td><input type="text" name="notif[nTitle]" value="" ></td>
                            <td><input type="text" name="notif[nDate]" value="" class="datepicker" ></td>
                        </tr>
                        <tr>
                            <td colspan="2">Content</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <?php
                                $form = Loader::helper('form');
                                Loader::element('editor_config');
                                echo $form->textarea('notif[nContent]', array(
                                    'class' => 'ccm-advanced-editor'
                                ));
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Notification Type</td>
                            <td>
                                <label class="radio"><input type="radio" name="notif[nType]" value="0" checked="" class="annType"> <span>General</span></label>
                                <label class="radio"><input type="radio" name="notif[nType]" value="1" class="annType"> <span>Linked To Titles</span></label>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="text" id="searchTitles" href="<?php echo $this->url('/dashboard/notification/actions/get_titles'); ?>" disabled="" placeholder="Search Titles"/></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <select id="titleList" multiple="" name="notif[linkedTitles][]" disabled="">
                                    <option id="tlPlaceholder">Nothing selected</option>
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
            <div class="ccm-pane-body-footer">
                
            </div>
        </div>
    </div>
</div>
