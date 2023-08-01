// ANZGO-3415 Added by Shane Camus 7/26/2017
// ANZGO-3881 modified by mtanada 20181017
$('#header-support').click(function () {
    try {
        var data = {
            'pageName': 'Support',
            'action': 'View Tab',
            'info': ''
        };
        trackUser(data);
    }
    catch (e) { }
});