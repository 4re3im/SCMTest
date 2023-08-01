var studentUIDs = [];
var teacherUIDs = [];
var searchUI;
var role = "student";
var oid = $(".oid")[0].value;
var modalConfig = {
  width: "50%",
  height: "50%",
  appendButtons: true,
  modal: true,
  title: "Modal Title",
};

var notif = $("#app-notification");
var notif_box = $("#app-notification-box");

var progress_bar = $("#app-notification-progress-bar");
var progress_bar_label = $("#progress-label");

var submitBtn = $("#student-submit, #teacher-submit");
var removeUserBtn = $("#student-delete-users, #teacher-delete-users");

BASE_URL = "/dashboard/institution_management/review";

$(document).on("ready", function () {
  getInsitutionUsers();

  $("#gigya-search-student")
    .autocomplete({
      source: function (req, res) {
        $.getJSON(
          "/dashboard/institution_management/review/searchByEmail",
          { term: req.term, role: role },
          res
        );
      },
      minLength: 3,
      delay: 1000,
      select: function (event, ui) {
        if (!ui.item.success) {
          console.log("No users found!");
          return;
        }
        openModal(ui);
      },
    })
    .data("autocomplete")._renderItem = function (ul, item) {
    if (item.success) {
      var resultsHtml =
        "<a><strong>" +
        item.email +
        "</strong><br/><span>" +
        item.name +
        "</span></a>";
    } else {
      var resultsHtml = "<a><span>No results found...</span></a>";
    }

    return $("<li></li>")
      .data("item.autocomplete", item)
      .append(resultsHtml)
      .appendTo(ul);
  };

  $("#gigya-search-teacher")
    .autocomplete({
      source: function (req, res) {
        $.getJSON(
          "/dashboard/institution_management/review/searchByEmail",
          { term: req.term, role: role },
          res
        );
      },
      minLength: 3,
      select: function (event, ui) {
        if (!ui.item.success) {
          console.log("No users found!");
          return;
        }
          openModal(ui);
      },
    })
    .data("autocomplete")._renderItem = function (ul, item) {
    if (item.success) {
      var resultsHtml =
        '<a><strong style="font-size: 12px;">' +
        item.email +
        "</strong><br/><span>" +
        item.name +
        "</span></a>";
    } else {
      var resultsHtml = "<a><span>No results found...</span></a>";
    }

    return $("<li></li>")
      .data("item.autocomplete", item)
      .append(resultsHtml)
      .appendTo(ul);
  };

  $(document).on("submit", "#student-search, #teacher-search", function (e) {
    e.preventDefault();
  });
});

$(document).on("click", "#student-submit, #teacher-submit", function () {
  var uidCount = studentUIDs.length + teacherUIDs.length;
  if (studentUIDs.length === 0 && teacherUIDs.length === 0) {
    showNotification("error", "No users selected");
    return;
  } else {
    var res = confirm("Submit " + uidCount + " users to gigya?");
    if (res) {
      runAttributeInsitutionProcess();
    }
  }
});

$(document).on("click", ".remove-user", function () {
  $(this).closest("tr").remove();
  var uid = $(this).closest("tr").find("td.uid").html();

  if (role === "student") {
    var index = studentUIDs.indexOf(uid);
    if (index !== -1) {
      studentUIDs.splice(index, 1);
    }
  } else {
    var index = teacherUIDs.indexOf(uid);
    if (index !== -1) {
      teacherUIDs.splice(index, 1);
    }
  }

  var userTempTable = $("#" + role + "-temp-users #user-temp-table");
  if ($(userTempTable).find("tbody").children("tr").length < 1) {
    $(userTempTable).hide();
  }
});

$(document).on("click", ".gigya-page > a", null, navigateTable);

$(document).on("click", ".next > a", null, function (e) {
  e.preventDefault();
  e.stopPropagation();
  var next = $(
    ".student-pagination > li.numbers.disabled, .teacher-pagination > li.numbers.disabled"
  )
    .next()
    .children("a");
  $(next).trigger("click");
});

$(document).on("click", ".prev > a", null, function (e) {
  e.preventDefault();
  e.stopPropagation();
  var prev = $(
    ".student-pagination > li.numbers.disabled, .teacher-pagination > li.numbers.disabled"
  )
    .prev()
    .children("a");
  $(prev).trigger("click");
});

$("#proceed-btn").click(function (e) {
  if (role === 'student') {
    if (studentUIDs.includes(searchUI.item.uid)) {
      alert("User already added in the list!");
      this.value = "";
      return false;
    }
    studentUIDs.push(searchUI.item.uid);
  } else {
    if (teacherUIDs.includes(searchUI.item.uid)) {
      alert("User already added in the list!");
      this.value = "";
      return false;
    }
    teacherUIDs.push(searchUI.item.uid);
  }

  $("#"+ role +"-temp-users #user-temp-table").show();
  var html = "<tr>";
  html += "<td class='uid'>" + searchUI.item.uid;
  html += "<td class='firstName'>" + searchUI.item.firstName;
  html += "<td class='lastName'>" + searchUI.item.lastName;
  html += "<td class='email'>" + searchUI.item.email;
  html += "<td class='role'>" + searchUI.item.role;
  html += "</td>";
  html +=
    "<td><a href='#' class='btn btn-small btn-default remove-user' title='Remove subscription'>";
  html += "<i class='icon-trash'></i></a></td>";
  html += "</tr>";
  $("#"+ role +"-temp-users #user-temp-table > tbody").append(html);

  this.value = "";
  hideModal();
  return false;
});

$("#cancel-btn").click(function (e) {
  hideModal();
});

function openModal(ui) {
  searchUI = ui;
  $("#user-data").html(
    "<strong>Name: </strong>" +
      ui.item.name +
      "<br><strong>Email: </strong>" +
      ui.item.email +
      "<br><strong>Role: </strong>" +
      ui.item.role
  );
  modalConfig.height = "15%";
  modalConfig.width = "25%";
  modalConfig.element = "#confirmation-modal-content";
  modalConfig.title = "Confirm user";
  $.fn.dialog.open(modalConfig);
}
function hideModal() {
  jQuery.fn.dialog.closeTop();
  resetModalConfig();
}

function resetModalConfig() {
  modalConfig = {
    width: "50%",
    height: "50%",
    appendButtons: true,
    modal: true,
    title: "Modal Title",
    element: "",
  };
}

function openTab(evt, tabName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  role = evt.target.id;
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(tabName).style.display = "block";
  evt.currentTarget.className += " active";
  // GCAP-1372 added by mabrigos
  if (tabName == 'Teacher' || tabName == 'Student') {
    getInsitutionUsers();
  }
}

function runAttributeInsitutionProcess() {
  var schoolName = document.getElementsByClassName("schoolName")[0].value;
  var data = {
    name: schoolName,
    studentIds: studentUIDs,
    teacherIds: teacherUIDs,
  };

  $.ajax({
    url: BASE_URL + "/runAttributeInsitution/" + oid,
    dataType: "json",
    type: "POST",
    data: data,
    beforeSend: function () {
      jQuery.fn.dialog.showLoader();
      disableButtons();
      console.log("Uploading to S3 started: " + new Date());
    },
    success: function (data) {
      if (!data.success) {
        showNotification("error", d.message);
        return;
      }
      console.log("Uploaded To S3 on: " + new Date());
      showNotification(
        "info",
        "Your request has been submitted. Dataflows for this feature are scheduled to run every 10 minutes. Please check back in 10-15 minutes",
        10000
      );
      hideProgressBar();
      resetUIDs();
      enableButtons();
      getInsitutionUsers();
    },
    error: function (xhr, status, err) {
      showNotification(
        "error",
        "There was an error in uploading to S3 process"
      );
    },
    complete: function () {
      jQuery.fn.dialog.hideLoader();
    },
  });
}

function getInsitutionUsers() {
  var pagination = $(".student-pagination");
  var tableBody = $("#student-user-list > tbody");

  if (role === "teacher") {
    tableBody = $("#teacher-user-list > tbody");
    pagination = $(".teacher-pagination");
  }

  $.ajax({
    url: "/dashboard/institution_management/review/loadTable",
    method: "GET",
    data: {
      oid: oid,
      role: role,
    },
    dataType: "json",
    success: function (data) {
      tableBody.html(data.tableBody);
      if (!data.hasResult) {
        $(pagination).hide();
      } else {
        $(pagination).html(data.pager).show();
      }
    },
    error: function (xhr, status, err) {
      tableBody.html(
        "<tr><td>There was an error retrieving Gigya users....</td></tr>"
      );
    },
  });
}

function navigateTable(e) {
  var tableBody = $("#student-user-list > tbody");
  var pagination = $(".student-pagination");

  if (role === "teacher") {
    tableBody = $("#teacher-user-list > tbody");
    pagination = $(".teacher-pagination");
  }

  e.preventDefault();
  e.stopPropagation();

  if ($(this).parent("li").hasClass("disabled")) {
    return false;
  }

  $.ajax({
    url: $(this).attr("href"),
    method: "GET",
    data: {
      oid: oid,
      role: role,
    },
    dataType: "json",
    success: function (data) {
      tableBody.html(data.tableBody);
      $(pagination).html(data.pager);
    },
    error: function (xhr, status, err) {
      tableBody.html(
        "<tr><td>There was an error retrieving Gigya users....</td></tr>"
      );
    },
  });
}

function resetUIDs() {
  studentUIDs = [];
  teacherUIDs = [];
  var studentTempTable = $("#student-temp-users #user-temp-table");
  var teacherTempTable = $("#teacher-temp-users #user-temp-table");
  $(studentTempTable).find("tbody").children("tr").remove();
  $(studentTempTable).hide();
  $(teacherTempTable).find("tbody").children("tr").remove();
  $(teacherTempTable).hide();
}

function showNotification(type, message, timeout) {
  if (!timeout) {
    timeout = 5000;
  }
  notif_box.html(message);
  notif_box.addClass("alert-" + type);
  notif.show();
  notif_box.slideDown();
  setTimeout(function () {
    hideNotification();
  }, timeout);
}

function hideNotification() {
  notif_box.slideUp(400, function () {
    notif_box.html("");
    notif_box.removeClass();
    notif_box.addClass("alert");
  });
}

function showProgressBar(message) {
  progress_bar_label.html(message);
  progress_bar.show();
}

function hideProgressBar() {
  progress_bar.hide();
  progress_bar_label.html("");
}

function enableButtons() {
  submitBtn.removeAttr("disabled");
  removeUserBtn.removeAttr("disabled");
}

function disableButtons() {
  submitBtn.attr("disabled", true);
  removeUserBtn.attr("disabled", true);
}
