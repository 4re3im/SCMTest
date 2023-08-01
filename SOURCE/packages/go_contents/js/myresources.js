/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function () {
  if (triggerActivate == 1) $('#header_login').trigger('click');

  // SB-42 Added by Errol & Shane 01/28/19
  $(document).on('click', '.panel-heading', function () {
    $(this).toggleClass('active');
  });

  $(document).on('click', '#arrange li', function () {
    window.location.replace(myResourceURL + $(this).html());
  });

  // ANZGO-3942 added by machua 20181129 refresh page with appended sort type on dropdown change
  $('.sort-resources').change(function () {
    window.location.replace(myResourceURL + $('.sort-resources option:selected').val());
  });

  $(document).on('click', '.sort', function () {
    window.location.replace(myResourceURL + $(this).html());
  });
  // ANZGO-3630 added by Maryjes Tanada 02/12/2018 Myresources activate trigger ANZGO-3943 modified 20181210
  if (trigger_hmUserType_activate == 1) {
    window.location.replace(activateURL);
  }

  $(document).on('click', 'span.delete_resource', function () {
    var us_id = $(this).attr('name');
    // ANZGO-3325 added by jbernardez 20180222
    // record the object that has been clicked, which is the delete
    var deleteButton = $(this);
    bootbox.confirm('Are you sure you want to delete this resource?', function (result) {
      if (result) {
        $.ajax({
          url: myResourceURL + 'deleteResource/' + us_id,
          success: function (data) {
            // ANZGO-3325 modified by jbernardez 20180222
            // get the parent of the parent of the parent of the delete button
            // done like this as there are no identifying id for the said row class
            if (data) {
              deleteButton.parents('div.resources-container.expired').html('');
            } else {
              bootbox.alert('There seems to be an error deleting the resource.');
            }

            return false;
          }
        });
      }
    });
  });

  // ANZGO-3872 Modified by Shane Camus 10/05/18
  // ANZGO-3946 Modified by Shane Camus 12/11/18
  $(document).on('click', '.content-downloadable', function (event) {
    event.preventDefault();
    closeModal();
    displayDownloadNotification();
    downloadContent(titlesURL + 'downloadFile/' + $(this).attr('id'));
    event.stopImmediatePropagation();
    return false;
  });

  /* ANZGO-3326 Modified by John Renzo S. Sunico, 04.21.2017
   * Modifying this because it does not bind to dynamically created
   * elements.
   */
  $('a.edu-downloadables').unbind('click');
  $(document).on('click', 'a.edu-downloadables', function () {
    var tab_id = $(this).attr('id');
    var title = $(this).html();
    displayModalMyResources(myResourceURL + 'viewPDF/' + tab_id, '', 'modal-lg', title);
    $('#generalModalLabel').html(title);
    return false;
  });

  // ANZGO-3946 added by machua 20181210 upon clicking, populate modal with fetched resource data
  $('.resource-has-modal').unbind('click');
  $(document).on('click', '.resource-has-modal', function () {
    var tabID = $(this).attr('id');
    var parentElemTitle = $(this).parents('.panel-collapse').prev().find('p').text();
    var parentElemIcon = $(this).find('img').attr('src');
    var tileType = $(this).find('.resource-title').text(); // ANZGO-3979

    $('.modal-title').html(parentElemTitle);

    // ANZGO-3979 added by machua 20181220 to get the correct data based from the tab/tile type
    var $ajaxURL = tileType === 'Weblinks' ? 'fetchWeblinks/' : 'fetchContents/';

    $.ajax({
      url: myResourceURL + $ajaxURL + tabID,
      type: 'GET',
      success: function (response) {
        $('.resource-items').html(response);
        $('.img-resource-thumbnail').attr('src', parentElemIcon);
      },
      error: function () {
        $('.resource-has-modal').html('Cannot retrieve content.');
      }
    });

    $('#resources-list').modal('show');
    return false;
  });

  $('#resources-list').on('hidden.bs.modal', function () {
    $('.resource-items').html('');
    $('.img-resource-thumbnail').attr('src', '');
  });

  function displayModalMyResources (sourceUrl, sourceData, resizeModal, title) {
    $.ajax({
      url: sourceUrl,
      data: sourceData,
      type: 'POST',
      dataType: 'html',
      success: function (response) {
        if (resizeModal) {
          $('#generalModal').children('.modal-dialog').addClass(resizeModal);
        }

        $('.modal-content').html(response);
      },
      error: function (xhr, status, error) {
        $('.modal-content').html(xhr.responseText);
      }
    });
    $('#generalModal').modal('show');
  }

  /* ANZGO-3300 Added by John Renzo Sunico, 04/21/2017
   * AJAX loader My Resources.
   */

  var loading = false;
  var completed = false;
  var loaded = 0;
  $('#more-titles').hide();
  // ANZGO-3897 added by jdchavez 10/19/2018
  // EST-78 blocked by jbernardez 20200422
  // updateHotmathsUrl(); 

  /** added by Ariel **/
  var sort = $('[name="sort"]').val();
  $.ajax({
      url: 'loadMoreResources/' + 0,
      type: 'POST',
      data: {'sort': sort},
      dataType: 'json',
      beforeSend: function () {
          $('#more-titles').fadeIn();
          loading = true;
      },
      complete: function () {
          $('#more-titles').fadeOut();
          loading = false;
      },
      success: function (response) {

      $('.panel-group-resources').css('display', '');
      if (response.titles !== '') {
        $('#resources-panel').append(response.titles);
        mainContent = $('#main-content .container-fluid').html();
        completed = response.completed;
        loaded += 8;
        recursivePreAuthentication();
        // ANZGO-3897 added by jdchavez 10/19/2018
        // EST-78 blocked by jbernardez 20200422
        // updateHotmathsUrl(); 
        $('#loadmore').fadeIn(); // SB-419 added by mabrigos
      } else {
        $('.page-notification').show();
      }

    },
    error: function (xhr, status, error) {
      console.log(error);
    }
  });
  /** end - by Ariel **/

  $(window).scroll(function () {
    var pageHeight = $(document).height() - window.innerHeight;
    var sort = $('[name="sort"]').val();

    // ANZGO-3947 modified by machua 20181212 use the new function to load more resources with the new UI/UX design
    // ANZGO-3383 Modified by John Renzo Sunico, May 12, 2017
    var scrollTop = Math.ceil($(window).scrollTop());

    if (scrollTop == pageHeight && !loading && !completed) {
      $('#loadmore').fadeOut(); // SB-419 added by mabrigos
      $.ajax({
        url: 'loadMoreResources/' + loaded,
        type: 'POST',
        data: {'sort': sort},
        dataType: 'json',
        beforeSend: function () {
          $('#more-titles').fadeIn();
          loading = true;
        },
        complete: function () {
          $('#more-titles').fadeOut();
          loading = false;
        },
        success: function (response) {
          $('#resources-panel').append(response.titles);
          mainContent = $('#main-content .container-fluid').html();
          completed = response.completed;
          if (!completed) {
            $('#loadmore').fadeIn(); // SB-419 added by mabrigos
          }
          loaded += 8;
          // Pre-authenticates newly loaded Elevate resources
          recursivePreAuthentication();
          // ANZGO-3897 added by jdchavez 10/19/2018
          // EST-78 blocked by jbernardez 20200422
          // updateHotmathsUrl();
        },
        error: function (xhr, status, error) {
          console.log(error);
        }
      });
    }
  });

  // ANZGO-3897 added by jdchavez 10/19/2018
  function updateHotmathsUrl () {
    $('a[href*="/go/myresources/toHotmaths/"]').each(function (index, el) {
      var resolver = $(el).attr('href');

      $.ajax({
        url: resolver + '$isAjax=true',
        type: 'GET',
        success: function (response) {
          $(el).attr('href', response);
          console.log(response);
        },
        error: function (xhr, status, error) {
          console.log(error);
        }
      });
    });
  }
  // SB-419 added by mabrigos
  $('#loadmore').on('click', function () {
    var tempScrollTop = $(window).scrollTop()
    $('#loadmore').fadeOut();
    if (!loading && !completed) {
      $.ajax({
        url: 'loadMoreResources/' + loaded,
        type: 'POST',
        data: {'sort': sort},
        dataType: 'json',
        beforeSend: function () {
          $('#more-titles').fadeIn();
          loading = true;
        },
        complete: function () {
          $('#more-titles').fadeOut();
          $(window).scrollTop(tempScrollTop);
          loading = false;
        },
        success: function (response) {
          $('#resources-panel').append(response.titles);
          mainContent = $('#main-content .container-fluid').html();
          completed = response.completed;
          if (!completed) {
            $('#loadmore').fadeIn();
          }
          loaded += 8;
          // Pre-authenticates newly loaded Elevate resources
          recursivePreAuthentication();
          // ANZGO-3897 added by jdchavez 10/19/2018
          // EST-78 blocked by jbernardez 20200422
          // updateHotmathsUrl();
        },
        error: function (xhr, status, error) {
          console.log(error);
        }
      });
    }
  })
});