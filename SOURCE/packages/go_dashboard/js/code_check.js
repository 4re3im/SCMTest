$(function () {
  $('input#search-accesscode').autocomplete({
      source: '/tools/packages/go_dashboard/accesscode-autocomplete',
      select: function (event, ui) {
          $('#accesscode_id').val(ui.item.id)
          var url = '/dashboard/code_check/' + ui.item.value
          window.location = url
      },
      // SB-645 modified by jbernardez 20200716
      delay: 2000,
      minLength: 3
  })

  // ANZGO-3554 Added by Shane Camus 10/26/17
  $('#hmCheckbox').on('change', function (e) {
      if (this.checked) {
          $('#search-accesscode').unbind('autocomplete').autocomplete({
              source: function (request, response) {
                  $.ajax({
                      method: 'GET',
                      url: '/dashboard/code_check/getAccessCodeViaPartialCode',
                      data: { term: request.term },
                      success: function (data) {
                          response(data)
                      }
                  })
              },
              select: function (event, ui) {
                  $('#search-accesscode').val(ui.item.value)
                  $('div#searchform input[type="submit"]').trigger('click')
              },
              // SB-645 modified by jbernardez 20200716
              delay: 2000,
              minLength: 3
          })
      } else {
          $('#search-accesscode').unbind('autocomplete').autocomplete({
              source: '<?php echo $url ?>',
              select: function (event, ui) {
                  $('#accesscode_id').val(ui.item.id)
                  var url = '<?php echo $this->url("/dashboard/code_check") ?>' + ui.item.value
                  window.location = url
              },
              // SB-645 modified by jbernardez 20200716
              delay: 2000,
              minLength: 3
          })
      }

  })

  //GCAP-844 Modified by machua 20200430 to get data from Gigya
  $('input#searchuser').autocomplete({
      source: function (request, response) {
          $.ajax({
              method: 'GET',
              url: '/dashboard/code_check/searchUserByEmail',
              data: { term: request.term },
              success: function (data) {
                  console.log(data)
                  response([data])
              }
          })
      },
      // SB-645 modified by jbernardez 20200716
      delay: 2000,
      minLength: 3,
      select: function (event, ui) {
          if (ui.item.value !== 'No results found...') {
              
              if (confirm('The access code will be redeemed. Proceed?')) {
                  $('#uID').val(ui.item.id)
                  console.log(ui.item);
                  console.log($('#redeem-code').serialize());
                  var url = '/dashboard/code_check/codeAction'
                  $.ajax({
                    type: 'POST',
                    url: url,
                    data: $('#redeem-code').serialize(),
                    success: function (data) {
                        console.log(data);
                      location.reload()
                    },
                    error: function (xhr, status, err) {
                      console.log(xhr.responseText)
                      alert(err)
                    }
                  })
              }
              return false
          }

          return false
      }
    })
    .data('autocomplete')._renderItem = function (ul, item) {
          if (item.value === 'No results found...') {
              return $('<li></li>')
                  .data('item.autocomplete', item)
                  .append('<a><strong>' + item.value + '</strong></a>')
                  .appendTo(ul);
          } else {
              var resultsHtml = '<a><strong>' + item.value + '</strong><br/><span>' + item.name + '</span></a>';
              return $('<li></li>')
                  .data('item.autocomplete', item)
                  .append(resultsHtml)
                  .appendTo(ul);
          }

      }
})

//GCAP-844 Added by machua 20200430 to prevent adding subscription to admin
$(document).on("keydown", "input#searchuser", function(event) {
    if (event.key == "Enter") {
        event.preventDefault();
    }
});