var timeOuts = [];

function preAuthenticate(ISBN) {
  var selector = 'a[href*="/go/ereader/' + ISBN + '"]';
  var readerCredentialUri = '/Elevate/services/credentials/generate_credentials/';

  console.log('Pre-authentication started for ' + ISBN + '.');

  $(selector).each(function(i, e) {
    $(e).attr('pre-authenticated', 'started');

    $.ajax({
      url: readerCredentialUri + ISBN + "/",
      type: "POST",
      dataType: "json",
      async: false,
      error: function (xhr, status, error) {
        console.log(error);
        e.preventDefault();
        return false;
      },
      success: function (data) {
        console.log('Pre-authentication: Cookies created.');
        console.log('Pre-authentication: Attempting to create iFrame.');

        var iFrame = document.createElement('iframe');
        iFrame.setAttribute('src', window.location.origin + '/go/ereader/' + ISBN + '/');
        iFrame.setAttribute('style', 'display: none;');
        iFrame.setAttribute('class', 'elevatePreload');
        iFrame.onload = function () {
          var originalUrl = this.src;
          var isbn = this.src.match(/\/go\/ereader\/([A-Za-z0-9]+)/);

          console.log('Pre-authentication: ISBN ' + isbn[1]);

          if (isbn) {
            $('a[href *= "/go/ereader/' + isbn[1] + '/"]')
              .attr('href', this.contentWindow.location.href)
              .attr('old-url', originalUrl)
              .attr('pre-authenticated', true);
            console.log('Pre-authentication: Url swapped with iFrame.');
          }

          console.log('Pre-authentication: Starting timeout to revert link. ' + new Date());

          timeOuts.push(setTimeout(function () {
            console.log('Pre-authentication: Timeout reached. Reverting URL. ' + new Date());

            $('a[href *= "/go/ereader/read/' + isbn[1] + '/"]')
              .attr('href', originalUrl)
              .removeAttr('pre-authenticated')
              .removeAttr('old-url');

            console.log('Pre-authentication: Url reverted.');
            console.log('Pre-authentication: Ready for re-authentication.');
          }, 1140000));

          console.log('Pre-authentication: iFrame created and setup.');

          $(this).attr('ready', true);
          recursivePreAuthentication();
        };

        document.body.appendChild(iFrame);

        console.log('Pre-authentication: iFrame attached to document body.');

        return true;
      }
    });

    return true;
  });

  return false;
}

function recursivePreAuthentication() {
  var readerSelector = 'a[href*="/go/ereader/"]';
  $(readerSelector)
    .not('[pre-authenticated="true"]')
    .not('[pre-authenticated="started"]')
    .each(function(i, e) {

      if (i > 0) {
        console.log('Pre-authentication: Should not pre-authenticate more than one links.');
        return false;
      }

      var href = $(e).attr('href') || '';
      var elevateISBN = href.match(/\/go\/ereader\/([A-Za-z0-9]+)/);
      if (elevateISBN) {
        preAuthenticate(elevateISBN[1]);
      }
    });
}

$(document).ready(function () {
  var readerSelector = 'a[href*="/go/ereader/"]';

  // Pre-authenticate all elevate links
  recursivePreAuthentication();

  // Handles authentication on elevate anchor tag click
  $(document).on('click', readerSelector, function (e) {
    var cookieLink = '/Elevate/services/credentials/generate_credentials/';
    var href = $(this).attr('href') || '';
    var elevateISBN = href.match(/\/go\/ereader\/([A-Za-z0-9]+)/);

    if (elevateISBN && elevateISBN[1] !== 'read') {
      $.ajax({
        url: cookieLink + elevateISBN[1] + "/",
        type: "POST",
        dataType: "json",
        async: false,
        error: function (xhr, status, error) {
          console.log(error);
          e.preventDefault();
        }
      });
    }
  });


  // ANZGO-3406 Added by John Renzo Sunico
  // Disable sample link on mobile
  $('a[href*="/go/ereader/"]').each(function () {
    var isbn = $(this).attr('href');
    var isbn = isbn.match(/\/go\/ereader\/([A-Za-z0-9]+)/);
    var is_sample_uri = "/Elevate/services/GetBookDetails/isISBNSample/";
    var element = $(this);
    var not_available = $('<p><p><span style="font-size:12px;">The interactive textbook is unavailable on a mobile device. Please login on your desktop.</span></p>');
    var is_mobile = /(android|webos|avantgo|iphone|ipad|ipod|blackbe‌​rry|iemobile|bolt|bo‌​ost|cricket|docomo|f‌​one|hiptop|mini|oper‌​a mini|kitkat|mobi|palm|phone|pie|tablet|up\.browser|up\.link|‌​webos|wos)/i.test(navigator.userAgent);

    if (isbn) {
      $.ajax({
        url: is_sample_uri + isbn[1] + "/",
        type: "POST",
        dataType: "json",
        async: false,
        success: function (response) {
          if (response.is_sample && is_mobile) {
            $(element).css('cursor', 'default');
            $(element).css('pointer-events', 'none');
            $(element).css('color', 'gray');
            $(element).parents('.panel-collapse').prepend(not_available);
          }
        },
        error: function (e) {
          console.log(e.responseText);
        }
      });
    }
  });
});
