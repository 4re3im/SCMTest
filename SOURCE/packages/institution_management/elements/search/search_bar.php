<div class="ccm-pane-options" id="ccm-user-pane-options">
    <form id="app-institution-search" class="form-horizontal">
        <div class="ccm-pane-options-permanent-search">
            <div class="span3">
            <label for="filter" class="control-label">Filter</label>
                <div class="controls">
                    <select name="filter" id="app-keywords-filter" style="width: 165px;">
                        <option value="oid">OID</option>
                        <option value="name">Name</option>
                        <option value="formattedAddress">Address</option>
                        <option value="addressCountry">Country</option>
                        <option value="addressRegion">Region</option>
                        <option value="systemID">System ID</option>
                        <option value="edueltTeacherCode">Teacher joining code</option>
                    </select>
                </div>
            </div>
            <div class="span7">
                <div class="controls">
                    <input id="app-keywords-input" type="text" name="keywords" value="" placeholder="Institution search..." style="width: 300px;"
                           class="ccm-input-text">
                </div>
                <input type="submit" style="margin-left: 10px" class="btn ccm-input-submit" id="ccm-search-users" name="ccm-search-users" value="Search">
            </div>
        </div>
    </form>
</div>