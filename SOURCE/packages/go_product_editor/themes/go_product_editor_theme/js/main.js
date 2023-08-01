var changeFlag = false;
var textEditors = [];
var formType = false;

var unsavedPopup = 'You have unsaved changes. <button class=\'btn deleteyes unsaved\'>Discard changes</button><button class=\'btn deleteno unsaved\'>Continue editing</button>';
var unsavedPopupRedirect = 'You have unsaved changes. <button class=\'btn deleteyes redirectyes unsaved\'>Discard changes</button><button class=\'btn deleteno unsaved\'>Continue editing</button>';

var popupTriggerElement = null;

var resourceURI;

var contentDetailId = null;

var uploader = null;

// Icon select menu options
var options = {
  create: function (e, ui) {
    var selectMenu = $('.ui-selectmenu-button');
    var imgSrc = $('#icon-select-url').val();

    $('<img>', {
      src: imgSrc,
      id: 'selected-icon'
    }).prependTo(selectMenu);

  },
  change: function (e, ui) {
    var selected = ui.item.element.context;
    var selectMenu = $('.ui-selectmenu-button');
    var imgSrc = $(selected).attr('data-icon');

    $(selectMenu).find('img').remove();

    $('<img>', {
      src: imgSrc,
      id: 'selected-icon'
    }).prependTo(selectMenu);
  }
};

var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

$(document).ready(function () {

  $('[data-toggle="popover"]').popover();
  $('#to-dashboard').click(function (e) {
    e.preventDefault();
    history.back();
  });

  // ANZGO-3167 Paul Balila, 2017-03-24
  // Updates sort order of content details as some has NULL values.
  updateSortOrderOnLoad();

  // Bind content details as sortable
  bindSortableContentDetails();

  //SB-118 added by machua 20190404 to activate upload tab
  CKEDITOR.config.filebrowserUploadUrl = '/packages/go_product_editor/helpers/imageupload.php?type=Images';

  $('.nav-tabs a').click(function (e) {
    if (!changeFlag) {
      if ($(this).hasClass('mainContent')) {
        $('.sidebar-menu.content').addClass('in');
        $('.sidebar-menu.tabs').removeClass('in');
      } else {
        $('.sidebar-menu.tabs').addClass('in');
        $('.sidebar-menu.content').removeClass('in');
      }
    } else {
      e.preventDefault();
      e.stopImmediatePropagation();
      $(this).removeClass('active');
      $(this).trigger('blur');
      triggerPopup(unsavedPopup, false);
      popupTriggerElement = $(this);
    }
    $('.superdelete').show();
  });

  $(document).on('input', 'input, textarea, select', function () {
    changeFlag = true;
    closePopup();
  });

  $(document).on('click', '.deleteno', function () {
    closePopup();
  });

  $(document).on('click', '.deleteyes', function () {
    discardChanges();
  });
  // SB-313 addded by mabrigos redirect after disregard changes
  $(document).on('click', '.redirectyes', function () {
    window.location = $('.title-link').attr('href');
  });

  // CONTENT functions
  $(document).on('click', '.edit-folder', function (e) {
    e.preventDefault();
    if (changeFlag) {
      e.stopImmediatePropagation();
      triggerPopup(unsavedPopup, false);
      popupTriggerElement = $(this);
      return false;
    }
    var tmpFolderName = $(this).children('span').text();
    $.ajax({
      url: $(this).attr('href'),
      type: 'POST',
      data: {
        folderName: tmpFolderName
      },
      dataType: 'html',
      success: function (data) {
        $('section.content').html(data);
        $('.superdelete').show();
      },
      error: displayError
    });
  });

  $(document).on('click', '#add-folder', function (e) {
    e.preventDefault();
    $.ajax({
      url: $(this).attr('href'),
      type: 'GET',
      dataType: 'html',
      success: function (data) {
        $('section.content').html(data);
      },
      error: displayError,
      complete: function () {

      }
    });
  });

  $(document).on('click', '.subfolder-name', function (e) {
    // ANZGO-2915
    enableActivate(this);
    e.preventDefault();
    $.ajax({
      url: $(this).attr('href'),
      type: 'GET',
      dataType: 'html',
      success: function (data) {
        $('section.content').html(data);
      },
      error: displayError,
      complete: function () {
        CKEDITOR.replace('ContentData');
        editorChangeDetector();
        $('.select2').select2();
      }
    });
  });

  $(document).on('click', '.add-subfolder', function (e) {
    e.preventDefault();
    $.ajax({
      url: $(this).attr('href'),
      type: 'GET',
      dataType: 'html',
      success: function (data) {
        $('section.content').html(data);
      },
      error: displayError,
      complete: function () {
        CKEDITOR.replace('ContentData');
        editorChangeDetector();
        $('.select2').select2();
      }
    });
  });

  $(document).on('click', '.subfolder-content-name', function (e) {
    // ANZGO-2915
    enableActivate(this);
    e.preventDefault();
    $.ajax({
      url: $(this).attr('href'),
      type: 'GET',
      dataType: 'html',
      success: function (data) {
        $('section.content').html(data);
        initializeUpload();
      },
      error: displayError,
      complete: function () {
        $('#filetype-subcontent').trigger('change');
        CKEDITOR.replace('HTML_Content');
        editorChangeDetector();
        $('.select2').select2();
      }
    });
  });

  $(document).on('change', '#filetype-subcontent', function () {
    var value = $(this).val();
    var type = '';
    $('.type-panel').removeClass('in');
    if (value == '1005') { // File
      type = 'file';
    } else if (value == '1001') { // Link
      type = 'link';
    } else { // HTML
      type = 'html';
    }
    $('.row .' + type).addClass('in');

  });

  $(document).on('click', '.content-plus', function (e) {
    e.preventDefault();
    changeFlag = true;
    var thisButton = $(this);
    var heading = $.trim($(this).parent('li').text());
    var contentId = $(this).attr('id');
    $.ajax({
      url: $(this).attr('href'),
      type: 'GET',
      data: {
        heading: heading,
        id: contentId
      },
      dataType: 'html',
      beforeSend: function () {
        thisButton.addClass('content-plus-hide');
      },
      success: function (data) {
        $('#add-content-tbody').append(data);
        updateContentAddedSorting();
      },
      error: displayError
    });
  });

  $(document).on('click', '.content-trash', function (e) {
    e.preventDefault();
    changeFlag = true;
    var contentId = $(this).attr('content-id');
    $(this).parents('td').parents('tr').children('td.content-heading').children('input.content-heading-delete').val('Y');
    $('.content-plus').each(function () {
      if ($(this).attr('id') == contentId) {
        $(this).removeClass('content-plus-hide');
        return false;
      }
    });
    updateContentAddedSorting();
    $(this).parents('td').parents('tr').hide();
  });

  $(document).on('click', '.add-content-detail', function (e) {
    e.preventDefault();
    $.ajax({
      url: $(this).attr('href'),
      dataType: 'html',
      type: 'GET',
      success: function (data) {
        $('section.content').html(data);
        initializeUpload();
      },
      error: displayError,
      complete: function () {
        $('#filetype-subcontent').trigger('change');
        CKEDITOR.replace('HTML_Content');
        editorChangeDetector();
        $('.select2').select2();
      }
    });
  });

  // ANZGO-2904
  $(document).on('change', '#input-file-a', function () {
    var files = $(this)[0].files;
    var html = '';
    var tmp = '';
    for (var i = 0; i < files.length; i++) {
      fileId = files[i].name.replace(/ /g, '|');
      html += '<tr>';
      html += '<td  class=\'attachfilename\'>';
      html += files[i].name;
      html += '<input type=\'hidden\' name=\'add_subfolder[FilesMeta][' + fileId + ']\' value=\'accept\' />';
      html += '</td>';
      html += '<td>';
      html += '<a href=\'#\'><i class=\'fa fa-half-2x fa-trash-o\' title = \'Delete File\'></i></a>';
      html += '</td>';
      html += '</tr>';
    }
    $('#file-display tbody').html(html);
    $('.attach-file').show();
  });

  // ANZGO-2904
  $(document).on('change', '#input-file-e', function () {
    var files = $(this)[0].files;
    var html = '';
    var tmp = '';
    for (var i = 0; i < files.length; i++) {
      fileId = files[i].name.replace(/ /g, '|');
      html += '<tr>';
      html += '<td  class=\'attachfilename\'>';
      html += files[i].name;
      html += '<input type=\'hidden\' name=\'edit_subfolder[FilesMeta][' + fileId + ']\' value=\'accept\' />';
      html += '</td>';
      html += '<td>';
      html += '<a href=\'#\'><i class=\'fa fa-half-2x fa-trash-o\' title = \'Delete File\'></i></a>';
      html += '</td>';
      html += '</tr>';
    }
    $('#file-display tbody').html(html);
    $('.attach-file').show();
  });

  $(document).on('click', '.remove-file', function (e) {
    e.preventDefault();
    $(this).parent('td').siblings('td').children('input').val('reject');
    $(this).parents('td').parents('tr').hide();
  });

  // ANZGO-3363 Modified by Shane Camus 02/22/18
  $(document).on('change', '#add-content-detail-file', function () {
    var contentDetailFile = $(this)[0].files[0];

    var rawSize = parseInt(contentDetailFile.size);
    var sizeToKb = rawSize / 1024;
    var sizeToMb = rawSize / 1048576;
    var finalSize = (sizeToMb < 1) ? sizeToKb.toFixed(2) + ' KB' : sizeToMb.toFixed(2) + ' MB';

    var date = new Date();
    var day = date.getDate();
    var monthIndex = date.getMonth();
    var year = date.getFullYear();
    var readableDate = months[monthIndex] + ' ' + day + ', ' + year;
    $('.edit-public-name').each(function () {
      if ($(this).val() == '') {
        $(this).val(contentDetailFile.name);
      }
    });

    $('#FileNameLabel').text(contentDetailFile.name);
    $('#FileSizeLabel').text(finalSize);
    $('#FileUploadDateLabel').text(readableDate);
    $('#FileName').val(contentDetailFile.name);
    $('#FileSize').val(rawSize);
    $('#FileUploadDate').val((date.valueOf()) / 1000);
  });

  $(document).on('input', '.edit-public-name', function () {
    var genValue = $(this).val();
    $('.edit-public-name').each(function () {
      $(this).val(genValue);
    });
  });

  $(document).on('input', '.edit-public-description', function () {
    var genValue = $(this).val();
    $('.edit-public-description').each(function () {
      $(this).val(genValue);
    });
  });

  // END

  // TAB functions
  $(document).on('click', '.edit-tab', function (e) {
    e.preventDefault();
    if (changeFlag) {
      e.stopImmediatePropagation();
      triggerPopup(unsavedPopup, false);
      popupTriggerElement = $(this);
      return false;
    }
    $.ajax({
      url: $(this).attr('href'),
      type: 'GET',
      dataType: 'html',
      success: function (data) {
        $('section.content').html(data);
        $('.superdelete').show();
      },
      error: displayError,
      complete: function () {
        // ANZGO-3035
        CKEDITOR.config.allowedContent = true;
        CKEDITOR.replace('Public_TabText');
        CKEDITOR.replace('Private_TabText');
        // SB-491 blocked by jbernardez 20200406
        // CKEDITOR.replace('CustomAccessMessage');
        editorChangeDetector();

        $('#icon-select').iconselectmenu(options).iconselectmenu('menuWidget');
      }
    });
  });

  $(document).on('click', '#add-tab', function (e) {
    e.preventDefault();
    $.ajax({
      url: $(this).attr('href'),
      type: 'GET',
      dataType: 'html',
      success: function (data) {
        $('section.content').html(data);
        $('#icon-select').iconselectmenu(options).iconselectmenu('menuWidget');
      },
      error: displayError,
      complete: function () {
        CKEDITOR.replace('Public_TabText');
        CKEDITOR.replace('Private_TabText');
        // SB-491 blocked by jbernardez 20200406
        // CKEDITOR.replace('CustomAccessMessage');
        editorChangeDetector();
        $('#icon-select').iconselectmenu(options).iconselectmenu('menuWidget');
      }
    });
  });

  $(document).on('click', '.tab-name', function (e) {
    e.preventDefault();
    enableActivate($(this));
    if (changeFlag) {
      e.stopImmediatePropagation();
      triggerPopup(unsavedPopup, false);
      popupTriggerElement = $(this);
      return false;
    }

    var tmpTabName = $(this).children('span').text();
    var tmpTitleId = $('#title-id').val();
    $.ajax({
      url: $(this).attr('href'),
      data: {
        tabName: tmpTabName,
        titleId: tmpTitleId
      },
      dataType: 'html',
      type: 'POST',
      beforeSend: function () {
        $('.superdelete').css('pointer-events', 'none');
      },
      success: function (data) {
        $('section.content').html(data);
        $('.superdelete').css('pointer-events', '');
        $('.superdelete').hide();
      },
      error: displayError
    });
  });

  $(document).on('mouseenter', '#add-content-table tbody', function () {
    $(this).sortable({
      handle: '.acsort',
      cursor: 'move',
      update: updateContentAddedSorting
    });
  });
  // END

  /**
   * Clear the section.content element.
   */
  $('#main-tab > li').click(function () {
    $('section.content').html('');

  });

  var formOptions = {
    beforeSubmit: presubmitForm,
    success: postsubmitForm,
    error: displayError,
    type: 'post',
    dataType: 'json'
  };
    $('#general-form').submit(function (e) {
      e.preventDefault();
      $(this).ajaxSubmit(formOptions);
      return false;
    });

  $(document).on('click', '#general-delete-btn', function (e) {
    e.preventDefault();

    if ($('#general-delete-url').length <= 0) {
      triggerPopup('Nothing to delete...', true, 'auto');
      return false;
    }

    var url = $('#general-delete-url').val();
    var titleId = $('#title-id').val();

    var strArr = url.split('/');
    var method = '';
    for (var i = 0; i < strArr.length; i++) {
      if (strArr[i].indexOf('action') > 0) {
        method = strArr[i];
      }
    }

    $.ajax({
      url: url,
      type: 'POST',
      data: {
        title_id: titleId
      },
      dataType: 'json',
      beforeSend: function () {
        triggerPopup('Please wait...', true);
        $('.wrapper').addClass('body-block');
      },
      success: function (d) {
        // Run the function.
        window[method](d);
      },
      error: displayError
    });
  });

  // ANZGO-2904
  // delete button on before uploading files
  $(document).on('click', '#file-display .fa', function (e) {
    e.preventDefault();
    var fileName = $(this).parents('td').parents('tr').find('.attachfilename')[0].firstChild.textContent;
    $(this).parents('td').parents('tr').remove();

  });

  $(document).on('change', '#myonoffswitch-elevate', function (e) {
    e.preventDefault();
    var elevateURI = generateElevateURI($('#elevate-isbn').val());
    var matchElevate = resourceURI.match(/\/go\/ereader/);
    resourceURI = (matchElevate) ? elevateURI : resourceURI;

    if ($(this).is(':checked')) {
      $('#resource').parent().parent().removeClass('col-lg-12');
      $('#resource').parent().parent().addClass('col-lg-8');
      $('#elevate-isbn').parent().parent().removeClass('hidden');
      $('#resource').val(elevateURI);
      $('#resource').attr('readonly', true);
    } else {
      $('#resource').parent().parent().removeClass('col-lg-8');
      $('#resource').parent().parent().addClass('col-lg-12');
      $('#elevate-isbn').parent().parent().addClass('hidden');
      $('#resource').val(resourceURI);
      $('#resource').attr('readonly', false);
    }
  });

  $(document).on('keyup', '#elevate-isbn', function (e) {
    var validKeyCodes = [8, 46];
    var isbnValue = $(this).val().replace(/[^0-9A-Za-z\-]/g, '');

    $(this).val(isbnValue);

    if (e.key.search(/[A-Za-z0-9\-]/) > -1 || validKeyCodes.indexOf(e.keyCode) > -1) {
      $('#resource').val(generateElevateURI(isbnValue));
    }
  });

  $(document).on('click', '#resource', function (e) {
    if ($('#myonoffswitch-elevate').is(':checked')) {
      $(this).select();
      try {
        var success = document.execCommand('copy');
        if (success) {
          triggerPopup('Resource URL has been copied to clipboard.', true, 'auto');
        }
      } catch (e) {
        console.log('Does not support copy paste.');
      }
    }
  });

  $(document).on('DOMSubtreeModified', 'section.content', function() {
    setResourceUri();
    showHideElevateInputs();
  });

  // SB-234 added by jbernardez 20190628
  function displayNewDimensions(e, ui) {
    window.dispatchEvent(new Event('resize'));
    var newWidth = ui.size['width'];
    $('.content-wrapper').css({
      marginLeft: newWidth+"px"
    });
    $('.main-footer').css({
      marginLeft: newWidth+"px"
    });
    $('.navbar').css({
      marginLeft: newWidth+"px"
    });
  }

  $('.main-sidebar').resizable({
    stop: displayNewDimensions
  });
});

function presubmitForm (formData, jqForm, options) {
  if (formData.length <= 0) {
    triggerPopup('Nothing to save...', true, 'auto');
    return false;
  }

  changeFlag = false;

  for (var i in CKEDITOR.instances) {
    var editorName = CKEDITOR.instances[i].name;

    // Update the equivalent form textarea.
    for (var j in formData) {
      if (formData[j].name.indexOf(editorName) > 0) {
        formData[j].value = CKEDITOR.instances[i].getData();
      }
    }
    textEditors.push(editorName);
  }

  var tmpArr;
  for (var k in formData) {
    if (!formType) {
      tmpArr = formData[k].name.split('[');
      formType = tmpArr[0];
      break;
    }
  }

  var titleId = $('#title-id').val();
  formData.push({
    name: 'title_id',
    value: titleId
  });

  $('.wrapper').addClass('body-block');

  // SB-1129 modified by machua 08262022
  if (formData.some(item => item.type = 'file')) {
    triggerPopup('Uploading files. Please wait...', true);
  } else {
    triggerPopup('Please wait...', true);
  }
}

function postsubmitForm (data) {
  window[formType](data);
  if ('error' in data) {
    triggerPopup(data.error, false, 'auto');
    $('.wrapper').removeClass('body-block');
  }
}

// Form functions
function add_tab (data) {
  triggerPopup('Tab successfully created', true, 'auto');
  $('ul.tabs').html(data.tabs_list);
  $('section.content').html(data.tab_form);
  CKEDITOR.replace('Public_TabText');
  CKEDITOR.replace('Private_TabText');
  // SB-491 blocked by jbernardez 20200406
  // CKEDITOR.replace('CustomAccessMessage');
  editorChangeDetector();
  $('.wrapper').removeClass('body-block');
  formType = false;

  /**
   * ANZGO-3331 Added by John Renzo Sunico, Aug 02, 2017
   * Reinitializes sortables.
   */
  reinitializeSortables();
}

function edit_tab (data) {
  triggerPopup('Tab changes saved', true, 'auto');
  $('ul.tabs').html(data.tabs_list);
  $('section.content').html(data.tab_form);
  CKEDITOR.replace('Public_TabText');
  CKEDITOR.replace('Private_TabText');
  // SB-491 blocked by jbernardez 20200406
  // CKEDITOR.replace('CustomAccessMessage');
  editorChangeDetector();
  $('#icon-select').iconselectmenu(options).iconselectmenu('menuWidget');
  $('.wrapper').removeClass('body-block');
  formType = false;

  /**
   * ANZGO-3331 Added by John Renzo Sunico, Aug 02, 2017
   * Reinitializes sortables.
   */
  reinitializeSortables();
}

function edit_content_added (data) {
  triggerPopup('Tab contents successfully changed', true, 'auto');
  $('#add-content-tbody').html(data.content_added);
  $('.wrapper').removeClass('body-block');
  formType = false;

  /**
   * ANZGO-3331 Added by John Renzo Sunico, Aug 02, 2017
   * Reinitializes sortables.
   */
  reinitializeSortables();
}

function add_folder (data) {
  triggerPopup('Folder successfully created', true, 'auto');
  $('ul.content').html(data.folders_list);
  $('section.content').html(data.edit_form);
  $('.wrapper').removeClass('body-block');
  formType = false;

  /**
   * ANZGO-3331 Added by John Renzo Sunico, Aug 02, 2017
   * Reinitializes sortables.
   */
  reinitializeSortables();
}

function edit_folder (data) {
  triggerPopup('Folder changes saved', true, 'auto');
  $('ul.content').html(data.folders_list);
  $('section.content').html(data.edit_form);
  $('.wrapper').removeClass('body-block');
  formType = false;

  /**
   * ANZGO-3331 Added by John Renzo Sunico, Aug 02, 2017
   * Reinitializes sortables.
   */
  reinitializeSortables();
}

function add_subfolder (data) {
  if (!('error' in data)) {
    triggerPopup('Block of content successfully created', true, 'auto');
  }
  $('section.content').html(data.edit_form);
  $('ul.content').html(data.folders_list);
  CKEDITOR.replace('ContentData');
  editorChangeDetector();
  $('.wrapper').removeClass('body-block');
  formType = false;

  /**
   * ANZGO-3331 Added by John Renzo Sunico, Aug 02, 2017
   * Reinitializes sortables.
   */
  reinitializeSortables();
}

function edit_subfolder (data) {
  if (!('error' in data)) {
    triggerPopup('Block of content changes saved', true, 'auto');
  }
  $('section.content').html(data.edit_form);
  $('ul.content').html(data.folders_list);
  CKEDITOR.replace('ContentData');
  editorChangeDetector();
  $('.wrapper').removeClass('body-block');
  $('select.select2').select2();
  formType = false;

  /**
   * ANZGO-3331 Added by John Renzo Sunico, Aug 02, 2017
   * Reinitializes sortables.
   */
  reinitializeSortables();
}

function edit_content_detail (data) {
  // <!-- Modified by JSulit SB-976 -->
  contentDetailId = data.content_detail_id;
  uploader.bind('UploadComplete', function() {
    triggerPopup('Content detail changes saved', true, 'auto');
    $('section.content').html(data.edit_form);
    $('ul.content').html(data.folders_list);
    CKEDITOR.remove(CKEDITOR.instances['HTML_Content']);
    CKEDITOR.replace('HTML_Content');
    $('#filetype-subcontent').trigger('change');
    editorChangeDetector();
    $('.wrapper').removeClass('body-block');
    formType = false;
    bindSortableContentDetails();
    $(`a[href*="${data.content_detail_id}"`).trigger('click');
    initializeUpload();


    /**
     * ANZGO-3331 Added by John Renzo Sunico, Aug 02, 2017
     * Reinitializes sortables.
     */
    reinitializeSortables();
  });
  uploader.start(); 
}

function add_content_detail (data) {
  // <!-- Modified by JSulit SB-976 -->
  contentDetailId = data.content_detail_id;
  uploader.bind('UploadComplete', function() {
    triggerPopup('Content detail successfully created', true, 'auto');
    $('section.content').html(data.edit_form);
    $('ul.content').html(data.folders_list);
    CKEDITOR.remove(CKEDITOR.instances['HTML_Content']);
    CKEDITOR.replace('HTML_Content');
    $('#filetype-subcontent').trigger('change');
    editorChangeDetector();
    $('.wrapper').removeClass('body-block');
    formType = false;
    bindSortableContentDetails();
    $(`a[href*="${data.content_detail_id}"`).trigger('click');
    /**
     * ANZGO-3331 Added by John Renzo Sunico, Aug 02, 2017
     * Reinitializes sortables.
     */
    reinitializeSortables();
  });
  uploader.start();
}

// Delete functions
function delete_tab_action (data) {
  triggerPopup('Tab successfully deleted', true, 'auto');
  $('section.content').html('');
  $('ul.tabs').html(data.tabs_list);
  $('.wrapper').removeClass('body-block');
  formType = false;

  /**
   * ANZGO-3331 Added by John Renzo Sunico, Aug 02, 2017
   * Reinitializes sortables.
   */
  reinitializeSortables();
}

function delete_content_detail_action (data) {
  triggerPopup('Content detail successfully deleted', true, 'auto');
  $('section.content').html('');
  $('ul.content').html(data.folders_list);
  $('.wrapper').removeClass('body-block');
  formType = false;

  /**
   * ANZGO-3331 Added by John Renzo Sunico, Aug 02, 2017
   * Reinitializes sortables.
   */
  reinitializeSortables();
}

function delete_subfolder_action (data) {
  triggerPopup('Block of content successfully deleted', true, 'auto');
  $('section.content').html('');
  $('ul.content').html(data.folders_list);
  $('.wrapper').removeClass('body-block');
  formType = false;

  /**
   * ANZGO-3331 Added by John Renzo Sunico, Aug 02, 2017
   * Reinitializes sortables.
   */
  reinitializeSortables();
}

function delete_folder_action (data) {
  triggerPopup('Folder successfully deleted', true, 'auto');
  $('section.content').html('');
  $('ul.content').html(data.folders_list);
  $('.wrapper').removeClass('body-block');
  formType = false;

  /**
   * ANZGO-3331 Added by John Renzo Sunico, Aug 02, 2017
   * Reinitializes sortables.
   */
  reinitializeSortables();
}

// Tab content added update function
function updateContentAddedSorting () {
  changeFlag = true;
  var index = 1;

  $('.content-heading-sort').each(function () {
    if ($(this).siblings('input.content-heading-delete').val() == 'Y') {
      $(this).val('');
    } else {
      $(this).val(index);
      index++;
    }

  });

}

function triggerPopup (text, success, auto) {
  $('#popup').html(text);
  if (success) {
    $('#popup').removeClass('popup-delete').addClass('popup-changes');
  } else {
    $('#popup').removeClass('popup-changes').addClass('popup-delete');
  }

  $('#popup').animate({ top: '0px' });

  if (auto === 'auto') {
    $('#popup').delay(2000).animate({ top: '-50px' });
  } else if (auto === 'close') {
    $('#popup').css('top', '-50px');
  }
}

function closePopup () {
  $('#popup').animate({ top: '-50px' });
}

function discardChanges () {
  changeFlag = false;
  popupTriggerElement.trigger('click');
  closePopup();
}

// ANZGO-2899
$(function () {
  $('#sidebar-menu-sortable').sortable({
    update: function (event, ui) {

      var input;
      var index = 1;

      hiddentinputs = $(this).find('.tab-sort');
      $(hiddentinputs).each(function () {

        input = $(this);
        input.attr('value', index);
        index++;

      });

      var sort = $(hiddentinputs).serialize();
      var url = $(this).find('.urltab-sort').attr('value'); //Modified by Shane Camus 2017-03-21 (ANZGO-3167)

      $.ajax({
        url: url,
        data: sort,
        dataType: 'html',
        type: 'POST',
        beforeSend: function () {
          // $(notif).show();
        },
        success: function (data) {
          // console.log(data);
          // $(notif).text('Sorted.');
        },
        error: displayError,
        complete: function () {
          setTimeout(function () {
            // $(notif).hide();
          }, 3000);
        }
      });

    }
  });
});

// ANZGO-2910
// content details sorting
function bindSortableContentDetails () {
  $('.content-details-sortable').sortable({
    update: function (event, ui) {

      var input;
      var index = 1;

      hiddentinputs = $(this).find('.content-detail-sort');
      $(hiddentinputs).each(function () {

        input = $(this);
        input.attr('value', index);
        index++;

      });

      var sort = $(hiddentinputs).serialize();
      var url = $(this).find('.urlcontent-details').attr('value'); //Modified by Shane Camus 2017-03-21 (ANZGO-3167)

      $.ajax({
        url: url,
        data: sort,
        dataType: 'html',
        type: 'POST',
        beforeSend: function () {
          // $(notif).show();
        },
        success: function (data) {
          // console.log(data);
          // $(notif).text('Sorted.');
        },
        error: displayError,
        complete: function () {
          setTimeout(function () {
            // $(notif).hide();
          }, 3000);
        }
      });
    }
  });
}

// ANZGO-2915
function enableActivate (obj) {
  $(obj).parent().addClass('active');
  $(obj).parent().siblings().removeClass('active');
}

// ANZGO-2895 by Paul Balila, 2016-10-26
// Custom selectmenu widget
$.widget('custom.iconselectmenu', $.ui.selectmenu, {
  _renderItem: function (ul, item) {
    var li = $('<li>');
    var wrapper = $('<div>', {
      text: item.label
    });

    if (item.element.attr('data-icon')) {
      var img = $('<img>', {
        src: item.element.attr('data-icon'),
        class: 'icon-select-display'
      }).prependTo(wrapper);
    }
    return li.append(wrapper).appendTo(ul);
  }
});

// ANZGO-3167 by Paul Balila, 2017-03-24
function updateSortOrderOnLoad () {
  // Trigger sort for all content details.
  // 1. Gather all content detail ids or ids being used by the sorter
  // and the sort numbers
  var sortDetails = $('.content-detail-sort').serialize();
  // 2. Send all the details to the sorter.
  $.ajax({
    url: $('body').find('.urlcontent-details').val(),
    data: sortDetails,
    type: 'POST',
    success: function (data) {
      console.log('SUCCESS');
    },
    error: function (xhr, status, error) {
      $('.content').html(xhr.responseText());
    },
    complete: function () {
      $('.wrapper').removeClass('body-block');
      $('#popup').animate({ top: '-50px' });
    }
  });
}

function setResourceUri () {
  if ($('#resource').length > 0) {
    resourceURI = $('#resource').val();
  }
}

function showHideElevateInputs () {
  if ($('#myonoffswitch-elevate').length > 0 && $('#myonoffswitch-elevate').is(':checked')) {
    $('#resource').parent().parent().removeClass('col-lg-12');
    $('#resource').parent().parent().addClass('col-lg-8');
    $('#elevate-isbn').parent().parent().removeClass('hidden');
    $('#resource').attr('readonly', true);

    if ($('#resource').val() && $('#resource').val().match(/\/go\/ereader\/([0-9A-Za-z\-]+)\//)) {
      $('#elevate-isbn').val($('#resource').val().match(/\/go\/ereader\/([0-9A-Za-z\-]+)\//)[1]);
    }
  }
}

function generateElevateURI (isbn) {
  var elevateUri = window.location.origin + '/go/ereader/';
  var terminator = (isbn.length > 0) ? '/' : '';
  return elevateUri + isbn + terminator;
}

/**
 * ANZGO-3331 Added by John Renzo Sunico, Aug 02, 2017
 * Reinitializes sortables.
 */

function reinitializeSortables () {
  $('#sidebar-menu-sortable').sortable({
    update: function (event, ui) {

      var input;
      var index = 1;

      hiddentinputs = $(this).find('.tab-sort');
      $(hiddentinputs).each(function () {

        input = $(this);
        input.attr('value', index);
        index++;

      });

      var sort = $(hiddentinputs).serialize();
      var url = $(this).find('.urltab-sort').attr('value');

      $.ajax({
        url: url,
        data: sort,
        dataType: 'html',
        type: 'POST'
      });

    }
  });

  $('.content-details-sortable').sortable({
    update: function (event, ui) {

      var input;
      var index = 1;

      hiddentinputs = $(this).find('.content-detail-sort');
      $(hiddentinputs).each(function () {

        input = $(this);
        input.attr('value', index);
        index++;

      });

      var sort = $(hiddentinputs).serialize();
      var url = $(this).find('.urlcontent-details').attr('value');

      $.ajax({
        url: url,
        data: sort,
        dataType: 'html',
        type: 'POST'
      });
    }
  });
}

function checkUnsaveData (e) {
  if (changeFlag) {
    e.preventDefault();
    e.stopImmediatePropagation();
    $(this).removeClass('active');
    $(this).trigger('blur');
    triggerPopup(unsavedPopupRedirect, false);
    popupTriggerElement = $(this);
    return false;
  }
  return true;
}

function editorChangeDetector () {
  for (var x in CKEDITOR.instances) {
    CKEDITOR.instances[x].on('change', function () {
      changeFlag = true;
    });
  }
}
// <!-- Added by JSulit SB-976 -->
function initializeUpload () {
  //Start
    uploader = new plupload.Uploader({
      browse_button: 'upload-file', // this can be an id of a DOM element or the DOM element itself
      container:'container',
      url: '/go_product_editor/initialize_upload',
      chunk_size: "100000kb",
      max_retries: 3,
      max_file_count: 1,
      multi_selection: false,
      filters : {
        max_file_size : '0',
        prevent_duplicates: true
      }
    });

    uploader.init();

      var html = '';
      var date = new Date();
      var day = date.getDate();
      var monthIndex = date.getMonth();
      var year = date.getFullYear();
      var readableDate = months[monthIndex] + ' ' + day + ', ' + year;

      uploader.bind('FilesAdded', function(up, files) {

      $('#plupload-files').html('');

      if(up.files.length > 1) {
        up.splice(0,up.files.length -1);
      }
      
      plupload.each(files, function(file) {

      var contentDetailFile = file.name.substring(0, file.name.lastIndexOf('.'));
      var rawSize = parseInt(file.size);
      var sizeToKb = rawSize / 1024;
      var sizeToMb = rawSize / 1048576;
      var finalSize = (sizeToMb < 1) ? sizeToKb.toFixed(2) + ' KB' : sizeToMb.toFixed(2) + ' MB';


      $('.edit-public-name').each(function () {
        if ($(this).val() == '') {
          $(this).val(contentDetailFile);
        }
      });

      $('#FileNameLabel').text(contentDetailFile);
      $('#FileSizeLabel').text(finalSize);
      $('#FileUploadDateLabel').text(readableDate);
      $('#FileName').val(file.name);
      $('#FileSize').val(rawSize);
      $('#FileUploadDate').val((date.valueOf()) / 1000);

      });
      document.getElementById('filelist').innerHTML += html;
  });

  uploader.bind('BeforeChunkUpload', function(up, file, post){ 
    post['content_detail_id'] = contentDetailId;
    post['public_name'] = document.getElementById('publicname').value;
    post['public_description'] = document.getElementById('publicdesc').value;
    post['file_info'] = document.getElementById('finfo').value;
    post['file_size'] = document.getElementById('FileSize').value;
    post['con_notes'] = document.getElementById('con-notes').value;
    return post;
  });
  //End of pluploader
}

// SB-1129 added by machua 08262022
function displayError (error) {
  triggerPopup(error.responseText, false, 'auto');
  $('.wrapper').removeClass('body-block');
}
