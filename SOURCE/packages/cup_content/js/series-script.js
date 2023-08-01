// GCAP-1272 Added by Shane Camus 04/08/2021
$(document).ready(function() {
  var searchEntitlement = $('#search-entitlement');
  var autoCompleteSource = $(searchEntitlement).attr('search-url');
  $(searchEntitlement).autocomplete({
      delay: 800,
      source: autoCompleteSource,
      select: function (event, ui) {
          if (ui.item.value !== 0) {
              $("#trialID").val(ui.item.id);
              $("#trialID").change();
          }
      }
  });

  $("#trialID").on("change", function() {
      var trialId = $("#trialID").val();
      trialIdExists(trialId);
  });

});

function trialIdExists(trialId) {
  var html = null;
  var url = '/dashboard/cup_content/series/getTrialId/';
  $.ajax({
      type: "POST",
      url: url,
      data: {trialId: trialId},
      dataType: "json",
      success: function (data) {
          if (data.seriesName !== 'available') {
              alert("Entitlement ID: " + trialId + ' is already used on Series: ' + data.seriesName);
              $("#trialID").val(null);
              $("#search-entitlement").val(null);
          }
      },
      error: function (xhr) {
          html = xhr.responseText;
      }
  });
}
