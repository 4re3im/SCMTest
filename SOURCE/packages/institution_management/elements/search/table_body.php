<?php foreach ($results as $result) {
    $oid = $result['oid'];
    $data = $result['data'];
    // SB-1117 added by timothy.perez - Users who have been rejected from a school registration are unable to join another school
    $isRejected = $result['isRejected'];

    $fullAddress = !isset($data['addressCity']) ? $data['formattedAddress'] : implode(", ", [
        $data['formattedAddress'],
        $data['addressCity'],
        $data['addressRegion'],
        $data['addressCountry']
    ]);


    ?>
<! -- SB-1117 modified by timothy.perez - Users who have been rejected from a school registration are unable to join another school -->
<tr class="ccm-list-record" ref="<?php echo $oid; ?>">
    <td><?php echo $oid; ?></td>
    <td>
        <a href="/dashboard/institution_management/review/<?php echo $oid; ?>">
            <?php echo $data['name']; ?>
        </a>
    </td>
    <td>
        <p><?php echo $fullAddress; ?></p>
        <?php
        if (isset($data['url'])) { ?>
            <p><?php echo @$data['url']?></p>
        <?php } ?>
        <?php
        if (isset($data['telephone'])) { ?>
            <p><?php echo $data['telephone']; ?></p>
        <?php } ?>
        <?php
        if (isset($data['systemID']) && isset($data['systemID']['idValue'])) { ?>
            <p>HM ID: <?php echo $data['systemID']['idValue']; ?></p>
        <?php } ?>
    </td>
    <! -- SB-1117 modified by timothy.perez - Users who have been rejected from a school registration are unable to join another school -->
    <?php if ($isRejected) { ?>
        <td class="rejected">
            <button onclick="removeRejection(&quot;<?php echo $oid; ?>&quot;, &quot;<?php echo $data['name']; ?>&quot;)" class="btn danger ccm-input-submit">REMOVE</button><br />
        </td>
    <?php } else { ?>
        <td>
            NOT REJECTED
        </td>
    <?php } ?>
</tr>
<?php } ?>
