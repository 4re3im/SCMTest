/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var typeFunction;
var dropFlag = false;
$(document).ready(function () {
    $(".go-subj-dropdown > a").click(function (e) {
        e.preventDefault();
        if ($(this).hasClass("go-subj-dropdown-collapsed")) {
            $(this).removeClass("go-subj-dropdown-collapsed");
            $(this).siblings("ul").slideUp("fast");
            $(this).children(".glyphicon").removeClass("glyphicon-triangle-top").addClass("glyphicon-triangle-bottom");
            dropFlag = false;
        } else {
            $(this).addClass("go-subj-dropdown-collapsed");
            $(this).siblings("ul").addClass("go-subj-show-options");
            $(this).siblings("ul").slideDown("fast");
            $(this).children(".glyphicon").removeClass("glyphicon-triangle-bottom").addClass("glyphicon-triangle-top");
            dropFlag = true;
        }
    });

    $(".go-subj-dropdown > a").blur(function () {
        /**
         * ANZGO-3493 Added by John Renzo S. Sunico
         * Added so it wouldn't collapse the dropdown if
         * list is not shown.
         */
        if ($(this).hasClass("go-subj-dropdown-collapsed")) {
            $(this).trigger("click");
        }
    });


    $(document).on("mousedown", ".go-subj-dropdown li", function (e) {
        e.preventDefault();
        $(this).parents(".go-subj-dropdown").children("a").children("span.val-container").text($(this).text());
        var subjSelectFlag = $(this).parents(".go-subj-dropdown").children("a").attr("id");
        $(this).parents(".go-subj-dropdown").children("a").children("input").val($(this).children("input").val());
        $(this).parents(".go-subj-dropdown").children("a").trigger("click");
        goToURL(subjSelectFlag);
    });

    $(document).on('keydown', '#go-search', function (e) {
        if (e.which == 13) {
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        } else {
            clearTimeout(typeFunction);
        }
    });

    $(document).on('keyup', '#go-search', function (e) {
        if (e.which == 13) {
            e.stopPropagation();
            e.stopImmediatePropagation();
            return false;
        } else {
            var term = $(this).val();
            var url = $(this).attr("search-url");
            if (term.length < 3) {
                return false;
            } else {
                clearTimeout(typeFunction);
                typeFunction = setTimeout(function () {
                    $.ajax({
                        url: url,
                        data: {
                            keyword: term
                        },
                        type: "POST",
                        dataType: "html",
                        beforeSend: function () {
                            $('.row-loader').show();
                        },
                        success: function (data) {
                            $('.row-loader').hide();
                            $('#search-result').html(data);
                            $('ul li.result').each(function () {
                                var height = $(this).height();
                                if (height < 350) {
                                    $(this).css('height', '350px');
                                }
                            });
                        },
                        error: function (xhr, status, err) {
                            $('.row-loader').hide();
                            console.log(xhr.responseText);
                        },
                        complete: function () {
                            var trackData = {
                                'pageName': 'Search',
                                'action': 'Perform Search',
                                'info': term
                            };
                            trackUser(trackData);
                        }
                    });
                }, 1000);
            }
        }
    });

    // ANZGO-3872 Modified by Shane Camus 10/05/18
    $('.content-file-download').on('click', function (event) {
        event.preventDefault();
        displayDownloadNotification();
        downloadContent(titlesURL + 'downloadFile/' + $(this).attr('href'));
        event.stopImmediatePropagation();
        return false;
    });

    // ANZGO-3013
    $('.panel-heading').click(function () {
        try {
            var expanded = $(this).find('.go-panel-header')[0]['attributes']['aria-expanded'].value;
            var titleName = $(this).parent().parent().parent().parent().parent().find('.tablet-show')[0].innerText;
            var tabName = $(this).find('.tab-name')[0].firstChild.textContent;
            var info = titleName + ' > ' + tabName;

            var data = {
                'pageName': 'Resources',
                'action': 'View Tab',
                'info': info
            };

            if (expanded == 'false') {
                trackUser(data);
            }
        }
        catch (e) {} // ANZGO-3451 Added by Shane Camus 7/26/2017 (to avoid conflicting with panel-heading on support page)
    });

});

function goToURL(subjectFlag) {
    var subject = $("#subject").children("input").val();
    if (subject == "None") {
        subject = $('#current_subject').val();
    }
    var region = null;
    var year_level = null;
    if (subjectFlag == "subject") {
        region = 0;
        year_level = 0;
    } else {
        region = $("#region").children("input").val();
        year_level = $("#year_level").children("input").val();
    }
    // alert(subject + " " + region + " " + year_level);
    window.location.href = base_url += subject + "/" + region + "/" + year_level + "/";
}
