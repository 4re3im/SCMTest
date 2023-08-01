(function($){
  var reviewPendingApp = {
    init: function() {
      reviewPendingApp.ACTIVE_PAGE = 1
      reviewPendingApp.BASE_URL = '/dashboard/institution_management/review_pending'
      reviewPendingApp.CHECKBOXES = $('.review-pending-checkboxes')
      reviewPendingApp.MASTER_CHECKBOX = $('#review-pending-master-checkbox')
      reviewPendingApp.MODAL = $('#confirmation-modal-content')
      reviewPendingApp.MODAL_CANCEL_BTN = $('#review-pending-cancel-btn')
      reviewPendingApp.MODAL_CONFIG = {
        width: '75%',
        height: '50%',
        appendButtons: true,
        modal: true,
        title: 'Modal Title'
      };
      reviewPendingApp.MODAL_FORM = $('#review-pending-modal-form')
      reviewPendingApp.MODAL_FORM_SUBMIT_BTN = $('#review-pending-submit-btn')
      reviewPendingApp.MODAL_TABLE = $('#review-pending-modal-confirmation')
      reviewPendingApp.PAGER = $('#app-review-pending-pager')
      reviewPendingApp.PAGES = $('.gigya-page')
      reviewPendingApp.PROCEED_BTN = $('#review-pending-proceed-btn')
      reviewPendingApp.RESULTS_TABLE = $('#review-pending-results-table')
      reviewPendingApp.SELECTS = $('.review-pending-results-select')

      reviewPendingApp.INSTITUTION_SEARCH_URL = reviewPendingApp.BASE_URL + '/searchInstitution';
      reviewPendingApp.bindSearch();
    },

    bindEvents: function () {
      // Bind master checkbox events
      $(reviewPendingApp.MASTER_CHECKBOX).change(reviewPendingApp.handleMasterCheckbox)

      // Bind checkboxes event
      $(reviewPendingApp.CHECKBOXES).change(
        reviewPendingApp.handleCheckbox
      )

      $(reviewPendingApp.PROCEED_BTN).click(
        reviewPendingApp.showPreview
      )

      $(reviewPendingApp.MODAL_FORM).submit(
        reviewPendingApp.submitInstitution
      )

      $(reviewPendingApp.MODAL_FORM_SUBMIT_BTN).click(function () {
        $(this).addClass('disabled')
        $(reviewPendingApp.MODAL_FORM).submit()
      })

      $(reviewPendingApp.SELECTS).change(
        reviewPendingApp.handleDropdown
      )

      $(reviewPendingApp.MODAL_CANCEL_BTN).click(function () {
        $.fn.dialog.closeTop();
      })

      $(document).on('click', '.gigya-page > a', null, reviewPendingApp.navigateTable);
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

    bindSearch: function () {
      $('#app-institution-search').submit(function (e) {
        e.preventDefault();
        var term = $('#app-keywords-input').val()
        var filter = $('#app-keywords-filter').val()
        $.ajax({
          url: reviewPendingApp.INSTITUTION_SEARCH_URL,
          type: 'GET',
          dataType: 'json',
          data: {
            keyword: term,
            filter
          },
          beforeSend: function () {
            $.fn.dialog.showLoader();
          },
          success: function (data) {
            $('#review-pending-results-table > tbody').html(data.data.tableBody)
            $(reviewPendingApp.PAGER).html(data.data.pager)
            reviewPendingApp.rebindSelectsAndDropdowns()
            $.fn.dialog.hideLoader();
          },
          error: function (xhr, status, err) {
            console.log(xhr.responseText)
          }
        })
      });
    },

    handleMasterCheckbox: function (e) {
      const masterCheckbox = e.target;
      $(reviewPendingApp.CHECKBOXES).prop('checked', $(masterCheckbox).is(':checked'))
      $(reviewPendingApp.CHECKBOXES).trigger('change')
    },

    handleCheckbox: function(e) {
      $(reviewPendingApp.MODAL_TABLE).find('tbody').html('')
      $(reviewPendingApp.CHECKBOXES).each(function(){
        const row = $(this).parent('td').parent('tr')
        const rowId = $(row).attr('id')
        const remark = $(row).find('select').find('option:selected').val()

        if (remark === '') {
          return
        }
        const newHtml = `<p>${remark}<input type="hidden" name="institution[${rowId}][remarks]" value="${remark}" /></p>`
        const clone = $(row).clone()

        $(reviewPendingApp.MODAL_TABLE).find(`tr#${rowId}`).remove()
        if ($(this).is(':checked')) {
          $(clone).find('td:first').remove()
          $(clone).find('td:last').html(newHtml)
          $(reviewPendingApp.MODAL_TABLE).find('tbody').append(clone)
          $(reviewPendingApp.MODAL_FORM_SUBMIT_BTN).removeClass('disabled')
        } else {
          $(row).find('select').val('')
        }
      })
      reviewPendingApp.resetModalTable()
    },

    handleDropdown: function() {

      const value = $(this).val()
      const row = $(this).parent('td').parent('tr')
      const rowId = $(row).attr('id')
      const checkbox = $(row).find('td:first').find('input')

      $(checkbox).prop('checked', value !== '')
      $(checkbox).trigger('change')

      if (value === '') {
        $(reviewPendingApp.MODAL_TABLE).find(`tr#${rowId}`).remove()
      }
    },

    showPreview: function() {
      jQuery.fn.dialog.showLoader();
      reviewPendingApp.MODAL_CONFIG.element = '#confirmation-modal-content';
      reviewPendingApp.MODAL_CONFIG.title = 'Are you sure?';
      $.fn.dialog.open(reviewPendingApp.MODAL_CONFIG);
    },

    submitInstitution: async function (e) {
      e.preventDefault();
      const url = $(this).attr('action')
      $.fn.dialog.showLoader();
      await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: $(this).serialize()
      })

      await reviewPendingApp.refreshTable(reviewPendingApp.ACTIVE_PAGE)
      reviewPendingApp.rebindSelectsAndDropdowns()
      $.fn.dialog.closeTop();
      $(reviewPendingApp.MODAL_TABLE).find('tbody').html('')
      reviewPendingApp.resetModalTable()
      $(reviewPendingApp.MODAL_FORM_SUBMIT_BTN).removeClass('disabled')
    },

    refreshTable: async function(page) {
      const response = await fetch(
        `${reviewPendingApp.BASE_URL}/navigate/${page}`
      )
      const json = await response.json()
      if (json.success) {
        $('#review-pending-results-table > tbody').html(json.data.tableBody)
        $(reviewPendingApp.PAGER).html(json.data.pager)
        $.fn.dialog.hideLoader();
      }
    },

    resetModalTable: function() {
      const rows = $(reviewPendingApp.MODAL_TABLE).find('tr')
      if (rows.length === 1) {
        $(reviewPendingApp.MODAL_TABLE).find('tbody').html(
          '<tr><td colspan="4">No selected institutions. Please make sure to place a remark per school.</td></tr>'
        )
        $(reviewPendingApp.MODAL_FORM_SUBMIT_BTN).addClass('disabled')
      }
    },

    navigateTable: async function(e) {
      e.preventDefault();
      var term = $('#app-keywords-input').val()
      var filter = $('#app-keywords-filter').val()
      const url = $(this).attr('href')
      $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        data: {
          keyword: term,
          filter
        },
        beforeSend: function () {
          $.fn.dialog.showLoader();
        },
        success: function (data) {
          $('#review-pending-results-table > tbody').html(data.data.tableBody)
          $(reviewPendingApp.PAGER).html(data.data.pager)
          reviewPendingApp.rebindSelectsAndDropdowns()
          $.fn.dialog.hideLoader();
        },
        error: function (xhr, status, err) {
          console.log(xhr.responseText)
        }
      })
    },

    rebindSelectsAndDropdowns: function(e) {
      reviewPendingApp.CHECKBOXES = $('.review-pending-checkboxes')
      reviewPendingApp.SELECTS = $('.review-pending-results-select')
      $(reviewPendingApp.SELECTS).change(
        reviewPendingApp.handleDropdown
      )
      $(reviewPendingApp.CHECKBOXES).change(
        reviewPendingApp.handleCheckbox
      )
    }
  };

  $(document).ready(function() {
    reviewPendingApp.init()
    reviewPendingApp.bindEvents()
  })
})(jQuery)