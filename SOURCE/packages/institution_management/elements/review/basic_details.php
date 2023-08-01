<div class="ccm-list-wrapper">
    <div>
        <a href="/dashboard/institution_management/edit/<?php echo $json->oid; ?>" style="float: right"
           class="btn primary">Edit</a>
        <h3 class="span3" style="margin-left: 0px;">Details</h3>
    </div>
    <br>
    <br>
    <table class="ccm-results-list" cellpadding="0" cellspacing="0" border="0">
        <tbody>
        <tr>
            <th>Formatted Address</th>
            <td colspan="2"><?php echo $json->formattedAddress; ?></td>
        </tr>
        <tr>
            <th>City/Town</th>
            <td colspan="2"><?php echo $json->addressCity; ?></td>
        </tr>
        <tr>
            <th>County/State</th>
            <td colspan="2"><?php echo $json->addressRegion; ?></td>
        </tr>
        <tr>
            <th>Country</th>
            <td colspan="2"><?php echo $json->addressCountry; ?></td>
        </tr>
        <tr>
            <th>Country Code</th>
            <td colspan="2"><?php echo $json->addressCountryCode; ?></td>
        </tr>
        <tr>
            <th>Post/Zip Code</th>
            <td colspan="2"><?php echo $json->addressRegionCode; ?></td>
        </tr>
        <tr>
            <th>Phone number</th>
            <td colspan="2"><?php echo $json->telephone; ?></td>
        </tr>
        <tr>
            <th>Website</th>
            <td colspan="2"><?php echo $json->url; ?></td>
        </tr>
        <tr>
            <th>EDUELT Teacher Code</th>
            <td colspan="2"><?php echo $json->edueltTeacherCode; ?></td>
        </tr>
        <tr>
            <th>Full Address</th>
            <td colspan="2"><?php echo $json->fullAddress; ?></td>
        </tr>
        <tr>
            <th>OID</th>
            <td colspan="2"><?php echo $json->oid; ?></td>
        </tr>
        <tr>
            <th>Date Created</th>
            <td colspan="2"><?php echo date('Y-m-d H:i.s', strtotime($json->createdTime)); ?></td>
        </tr>
        <tr>
            <th>Last Updated</th>
            <td colspan="2"><?php echo date('Y-m-d H:i.s', strtotime($json->lastUpdatedTime)); ?></td>
        </tr>
        <tr>
            <th rowspan="<?php echo count($json->systemID) + 1 ?>">System IDs</th>
            <?php
            if (count($json->systemID) > 0) {
            foreach ($json->systemID as $key => $value) { ?>
        <tr>
            <td><?php echo $value->idSystem ?></td>
            <td><?php echo $value->idValue ?></td>
        </tr>
        <?php
        }
        }
        ?>
        </tr>
        </tbody>
    </table>
</div>
<div class="panel">
    <div class="panel-header">

    </div>
    <div class="panel-body">
        <?php
        echo Loader::packageElement(
            'review/tabs',
            'institution_management',
            [
                'json' => $json,
                'userSubscriptions' => $userSubscriptions
            ]
        );
        ?>
    </div>
    <div class="panel-footer">

    </div>
</div>