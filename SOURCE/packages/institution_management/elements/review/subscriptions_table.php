<table id='ccm-product-list' class='ccm-results-list' cellspacing='0' cellpadding='0' border='0'>
    <thead>
    <tr>
        <th><input type='checkbox' id='checkAll'></th>
        <th>Subscription</th>
        <th>SubType</th>
        <th>Creation Date</th>
        <th>EndDate</th>
        <th>Duration</th>
        <th>Active</th>
        <th>AccessCode</th>
        <th>Days Remaining</th>
        <th>Purchase Type</th>
        <th>Added by</th>
    </tr>
    </thead>
    <tbody>
    <?php Loader::packageElement(
        'review/subscriptions_refresh',
        $pkgHandle,
        ['subscriptions' => $subscriptions, 'showCreator' => $showCreator]
    ); ?>
    </tbody>
</table>