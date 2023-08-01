(function ($) {
  // Instantiate app object
  var searchApp = {
    init: function () {
      searchApp.BASE_URL = '/dashboard/institution_management/search/';
      searchApp.INSTITUTION_SEARCH_URL = searchApp.BASE_URL + 'getInstitutions';

      searchApp.BODY = $('.ccm-pane-body');
      searchApp.FOOTER = $('.ccm-pane-footer');
      searchApp.PAGER = $('#app-institution-pager');
      searchApp.TABLE_BODY = null;

      searchApp.bindSearch();
    },

    bindSearch: function () {
      $('#app-institution-search').submit(function (e) {
        e.preventDefault();
        var term = $('#app-keywords-input').val()
        var filter = $('#app-keywords-filter').val()
        $.ajax({
          url: searchApp.INSTITUTION_SEARCH_URL,
          type: 'GET',
          dataType: 'json',
          data: {
            keyword: term,
            filter
          },
          beforeSend: function () {
            jQuery.fn.dialog.showLoader();
          },
          success: function (data) {
            $(searchApp.BODY).html(data.table);
            $(searchApp.PAGER).html(data.pager);
            jQuery.fn.dialog.hideLoader();
            searchApp.showBody();
            searchApp.TABLE_BODY = $('#app-institutions-list > tbody');

            if (data.hasResult) {
              searchApp.showFooter();
              searchApp.bindPager();
            }
          },
          error: function (xhr, status, err) {
            console.log(xhr.responseText)
          }
        })
      });
    },

    bindPager: function () {
      $(document).on('click', '.gigya-page > a', null, searchApp.navigateTable);
      $(document).on('click', '.next > a', null, function (e) {
        e.preventDefault();
        var next = $('li.numbers.disabled').next().children('a');
        $(next).trigger('click');
      });
      $(document).on('click', '.prev > a', null, function (e) {
        e.preventDefault();
        var prev = $('li.numbers.disabled').prev().children('a');
        $(prev).trigger('click');
      });
    },

    navigateTable : function (e) {
      e.preventDefault();
      var term = $('#app-keywords-input').val()
      var filter = $('#app-keywords-filter').val()

      if ($(this).parent('li').hasClass('disabled')) {
        return false;
      }

      $.ajax({
        url: $(this).attr('href'),
        method: 'GET',
        dataType: 'json',
        data: {
          'keyword': term,
          filter
        },
        beforeSend: function () {
          jQuery.fn.dialog.showLoader();
        },
        success: function (data) {
          $(searchApp.TABLE_BODY).html(data.tableBody);
          $(searchApp.PAGER).html(data.pager)
        },
        error: function (xhr, status, err) {
          $(searchApp.TABLE_BODY).html('<tr><td>There was an error retrieving Gigya institutions....</td></tr>');
        },
        complete: function () {
          jQuery.fn.dialog.hideLoader();
        }
      });
    },

    showBody: function () {
      $(searchApp.BODY).show();
    },

    showFooter: function () {
      $(searchApp.FOOTER).show();
    }
  };

  // Start jQuery binding
  $(document).ready(function () {
    searchApp.init();
  });
})(jQuery);

// SB-1117 modified by timothy.perez - Users who have been rejected from a school registration are unable to join another school
function removeRejection(oid, institutionName) {
    var action_url = '/dashboard/institution_management/search/remove_rejection';
    var tableRow = jQuery('tr[ref="' + oid + '"]');
    var isConfirmed = confirm("Are you sure to remove rejection status of '" + institutionName + "'?\n\nThis action cannot be undone.");

    if (isConfirmed) {
        action_url = action_url + "/" + oid;
        jQuery.getJSON(action_url, function (json) {
            if (json.result == true) {
              tableRow.children('.rejected').replaceWith('<td>NOT REJECTED</td>');
            } else {
                alert(json.error);
            }
        });
    }
}
