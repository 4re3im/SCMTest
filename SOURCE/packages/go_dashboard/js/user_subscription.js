$(document).ready(function () {
    var source = $('input#subscription').attr('search-url');
    // auto-complete
    $('input#subscription').autocomplete({
        source: source,
        select: function (event, ui) {
            $('#subscription').val(ui.item.label);
            $('#sa_id').val(ui.item.id);
            $('#s_id').val(ui.item.s_id);
            $('#product_id').val(ui.item.p_id);
        }
    });
    // Go User - Subscriptions - Add Subscriptions
    // Revised by Paul Balila 2016-11-25
    $(document).on("submit", "#add-subscription", function (e) {
        e.preventDefault();

        if (!$('#sa_id').val()) {
            $('#subscription-alert').html("Subscription is empty.");
            $('#subscription-alert').addClass('alert-danger');
            $('#subscription-alert').show();
            setTimeout(function () {
                $('#subscription-alert').hide();
            }, 4500);
            return false;
        }

        var data = $(this).serializeArray();
        var user_id = $('#user_id').val();
        data.push(
            {name: 'user_id', value: user_id},
            {name: 'is_ajax', value: 'yes'},
            {name: 'func', value: 'addusersubscription'}
        );

        $.ajax({
            type: 'POST',
            data: data,
            url: $(this).attr('action'),
            dataType: 'json',
            beforeSend: function () {
                $("#loader").show();
            },
            success: function (d) {
                if (d.success === false) {
                    // better if model dialog/alert
                    // alert(data.message);
                    // showModalMessage(data.message);
                    $('#subscription-alert').html(d.message);
                    $('#subscription-alert').addClass('alert-danger');
                    $('#subscription-alert').show();
                    setTimeout(function () {
                        $('#subscription-alert').hide();
                    }, 4500);
                    $('input#subscription').val('');
                } else {
                    //  showModalMessage('a new subscriptions has been successfully added');
                    $('input#subscription').val('');
                    // ANZGO-3634 Modified by John Renzo Sunico, 02/12/2018
                    $('.usersubscription #ccm-product-list tbody').html(d.message);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $('#subscription-alert').html(xhr.responseText);
                $('#subscription-alert').addClass('alert-danger');
                $('#subscription-alert').show();
                setTimeout(function () {
                    $('#subscription-alert').hide();
                }, 4500);
                //$('.usersubscription').html(xhr.status + "<br/>" + thrownError);
            },
            complete: function () {
                $("#loader").hide();
            }
        });
    });
});
