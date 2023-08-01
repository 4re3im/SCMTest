<div id='<?php echo $role ?>-search'>
  <form class="form-horizontal" id="-<?php echo $role ?>-search">
    <div class="ccm-pane-options-permanent-search" style="margin-top: 18px !important;">
        <div class="span11">
            <label for="gigya-search" class="control-label">Search</label>
            <div class="controls">
                <input id="gigya-search-<?php echo $role ?>" type="text" name="email" value="" placeholder="Email" class="ccm-input-text">
                <input type="hidden" class="oid" value="<?php echo $oid; ?>">
                <input type="hidden" class="schoolName" value="<?php echo $name; ?>">
                <input type="submit" style="position: absolute; right: 35px;" class="btn ccm-input-submit" id="<?php echo $role ?>-submit" name="ccm-search-users" value="Submit selected users">
            </div>
        </div>
    </div>
  </form>
</div>

<div id="<?php echo $role ?>-temp-users">
  <table class="table table-bordered" id="user-temp-table" style="display:none">
        <thead>
            <tr>
                <th>UID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>GO Role</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
  </table>
</div>

<hr>

<div id="<?php echo $role ?>-linked-users">
    <div id='table-header'>
        <div style="display:inline-block; width: 80%;" >
        </div>
        <div style="display:inline-block;">
            <input type="submit" class="btn danger ccm-input-submit" id="<?php echo $role ?>-delete-users" name="ccm-delete-users" value="Remove selected users">
        </div>
    </div>
    <?php 
        $helper = Loader::helper('review', 'institution_management');
        echo $helper->buildTable($role);
    ?>
</div>