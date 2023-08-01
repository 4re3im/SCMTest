$(document).on('ready', function () {
  getGigyaTeachers();

  $('#gigya-search').autocomplete({
    source: '/dashboard/gigya/users/searchByEmail',
    minLength: 3,
    select: function (event, ui) {
      location.href = '/dashboard/gigya/subscriptions/' + ui.item.label;
    }
  })
    .data('autocomplete')._renderItem = function (ul, item) {
    var resultsHtml = '<a><strong>' + item.value + '</strong><br/><span>' + item.name + '</span></a>';
    return $('<li></li>')
      .data('item.autocomplete', item)
      .append(resultsHtml)
      .appendTo(ul);
  };

  $(document).on('submit', '#ccm-user-advanced-search', function(e){
    e.preventDefault();
  })
  $(document).on('click', '.gigya-page > a', null, navigateTable);
  $(document).on('click', '.next > a', null, nextPage);
  $(document).on('click', '.prev > a', null, prevPage);
});

var tableBody = $('#gigya-users-list > tbody');
var pagination = $('.gigya-pagination');

function getGigyaTeachers () {
  $.ajax({
    url: '/dashboard/gigya/users/loadTable',
    method: 'GET',
    dataType: 'json',
    success: function (data) {
      tableBody.html(data.tableBody);
      $(pagination).html(data.pager).show();
    },
    error: function (xhr, status, err) {
      tableBody.html('<tr><td>There was an error retrieving Gigya users....</td></tr>');
    }
  });
}

function navigateTable (e) {
  e.preventDefault();

  if ($(this).parent('li').hasClass('disabled')) {
    return false;
  }

  $.ajax({
    url: $(this).attr('href'),
    method: 'GET',
    dataType: 'json',
    success: function (data) {
      tableBody.html(data.tableBody);
      $(pagination).html(data.pager);
    },
    error: function (xhr, status, err) {
      tableBody.html('<tr><td>There was an error retrieving Gigya users....</td></tr>');
    }
  });
}

function nextPage (e) {
  e.preventDefault();
  var next = $('li.numbers.disabled').next().children('a');
  $(next).trigger('click');
}

function prevPage (e) {
  e.preventDefault();
  var prev = $('li.numbers.disabled').prev().children('a');
  $(prev).trigger('click');
}
