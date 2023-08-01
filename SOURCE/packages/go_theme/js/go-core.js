var navBarCollapsed = false;
var toggleButtonsMinimized = false;
var scrollHit = false;
var cancelHeaderFunctions = false;
var logoMovedToLeft = false;
var frontMemoryScroll = 0;
// ANZGO-3326 Modified By John Renzo Sunico, 04/24/2017
// Declaring this as global variable for dynamic elements
var mainContent = null;

$(document).ready(function () {
    // ANZGO-3889 modified by mtanada 20181022
    mainContent = $("#main-content div").html();
    var togglingItems = {
        browseSpan: $("#toggle-browse").html(),
        loginSpan: $("#toggle-login").html(),
        browseWidth: $("#toggle-browse").width(),
        loginWidth: $("#toggle-login").width(),
        navbarFormWidth: $(".navbar-form").width()
    };

    // ANZGO-3889 added by mtanada 20181018 New Resources landing page
    var thumbnails = $(".thumbnail");
    var size_li = $(".thumbnail").hide().size();

    if ($(window).width() > 768) {
        var take = 8;
    } else {
        var take = 4;
    }

    thumbnails.filter(':lt(' + take + ')').show();

    $('.show-more').click(function (e) {
        e.preventDefault();
        var visible = $(".thumbnail:visible");
        count = visible.length;

        thumbnails.not(visible).show();

        if (count == size_li || count != size_li) {
            $('.show-more').hide();
            $('.show-less').show();
        }
    });

    $('.show-less').click(function (e) {
        e.preventDefault();

        var visible = $(".thumbnail:visible"),
            count = visible.length,
            realTake = count === size_li ? size_li % take : take,
            enoughShowing = (count - take > 0);

        if (enoughShowing)
            visible.slice(-realTake).hide();

        $('.show-less').hide();

        if (visible.length - take <= take) {
            $('.show-more').show();
        }
    });
    // End resources landing page

    /* Header animation and resize functions */
    // 1) Resize header according to screen size while considering scroll count.
    resizeHeaderOnLoad();

    // 2) Resize header after scroll
    $(window).scroll(function () {
        var toggleLogin = $("#toggle-login");
        var toggleBrowse = $("#toggle-browse");

        if ($(window).width() < 500) {
            toggleLogin.children("svg").attr("class", "svg-mid svg-white");
            toggleBrowse.children("svg").attr("class", "svg-mid svg-white");
        }

        if (!cancelHeaderFunctions) {
            if (scrollHit) {
                if (navBarCollapsed && toggleButtonsMinimized) {
                    if ($(window).width() < 500) {
                        toggleLogin.children("svg").attr("class", "svg-mid svg-black");
                        toggleBrowse.children("svg").attr("class", "svg-mid svg-black");
                    } else if ($(window).width() < 1295) {
                        minimizeToggleButtons(499);
                    } else {
                        maximizeNavBarForm($(window).width());
                    }
                    toggleButtonsMinimized = false;
                } else {

                    maximizeNavBarForm($(window).width());
                }
                $(".navbar-brand").removeClass("logo-fade");
                scrollHit = false;
            }
        }
        //}
    });

    // 3) Resize header after window resize considering scroll count
    $(window).resize(function () {
        if (!cancelHeaderFunctions) {
            resizeHeader($(window).width(), true);
        }
    });
    /* end */

    $('.notification button').click(function () {
        $('#notification-wrapper').fadeOut().css('display', 'none');
    });

    // force to display overlay
    var currentUrl = location.href;
    if (currentUrl.indexOf("do_login") > 0 ||
        currentUrl.indexOf("/go/login/v/") > 0 ||
        currentUrl.indexOf("go/signup/student") > 0 ||
        currentUrl.indexOf("go/signup/teacher") > 0 ||
        currentUrl.indexOf("/epub_reader/login/") > 0) {
        setToOverlayView();
        $("#main-footer").hide();
        $("#toggle-menu-close").attr("class", "navbar-toggle");
    }


    $(".go-panel-header").click(function () {
        // Find all spans in the headers...
        var anchorSpans = $(".panel-heading span.glyphicon");
        anchorSpans.each(function (index) {
            // ...then reset the span glyphicons to triabgle-bottom
            $(this).removeClass("glyphicon-triangle-top").addClass("glyphicon-triangle-bottom");
        });

        // Set target's (the clicked panel header anchor) parent's needed style
        if ($(this).hasClass("collapsed")) {
            $(this).parents(".panel-heading").css("border-bottom", "none");
            $(this).parents(".panel-heading").find("span.glyphicon").removeClass("glyphicon-triangle-bottom").addClass("glyphicon-triangle-top");
        } else {
            $(this).parents(".panel-heading").css("border-bottom", "1px solid black");
        }
    });

    $(".go-accordion > .panel-heading").mouseenter(function () {
        if ($(this).siblings(".panel-collapse").hasClass("in")) {
            $(this).css("border-bottom", "none");
        } else {
            $(this).css("border-bottom", "1px solid black");
            $(this).css("color", "#4D4D4D");
        }
    });

    $(".go-accordion > .panel-heading").mouseleave(function () {
        if ($(this).siblings(".panel-collapse").hasClass("in")) {
            $(this).css("border-bottom", "none");
        } else {
            $(this).css("border-bottom", "1px solid #CCCCCC");
            $(this).css("color", "#777777");
        }

        $(".go-accordion > .panel-heading").css("color", "#777777");
    });

    $(document).on("click", ".lower", function () {
        $(".cookie-info").show();
    });

    $(document).on("click", "#dismiss-cookie", function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(".cookie-info").hide();
    });

    // ANZGO-3789 added by jbernardez 20180706
    $(document).on("click", "#dismiss-announce", function (e) {
        e.preventDefault();
        e.stopPropagation();
        // ANZGO-3789 modified by jbernardez 20180710
        $("#loadTeamp").load('/go/user_landing/stopBanner/1');
        $(".announce-info").hide();
    });

    // SB-251 added by jbernardez 20190711
    $(document).on('click', '#dismiss-surveymonkey', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $.get('/go/myresources/stopSurveyMonkey/');
        $('.surveymonkey-info').remove();
    });

    // ANZGO-3789 added by jbernardez 20180706
    $(document).on("click", "#front-deactivate", function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(".announce-info").show();
    });

    $(".go-dropdown > a").click(function (e) {
        e.preventDefault();
        var arrowBtn = $(this).children("#arrow");
        var arrowBtnSrc = arrowBtn.attr("src");
        if ($(this).hasClass("collapsed")) {
            $(this).removeClass("collapsed");
            $(this).siblings("ul").slideUp("fast");
            arrowBtnSrc = arrowBtnSrc.replace("arrow_up", "arrow_down");
        } else {
            $(this).addClass("collapsed");
            $(this).siblings("ul").addClass("show-options")
            $(this).siblings("ul").slideDown("fast");
            arrowBtnSrc = arrowBtnSrc.replace("arrow_down", "arrow_up");
        }
        arrowBtn.attr("src", arrowBtnSrc);
    });

    $(".go-dropdown li").click(function () {
        $(".go-dropdown a").children("span").text($(this).text());
        var arrowBtn = $(".go-dropdown a").children("#arrow");
        var arrowBtnSrc = arrowBtn.attr("src");
        $(this).parent("ul").slideUp("fast");
        $(this).parent("ul").removeClass("collapsed");
        arrowBtnSrc = arrowBtnSrc.replace("arrow_up", "arrow_down");
        arrowBtn.attr("src", arrowBtnSrc);
    });

    /** social btns */
    $(".social-fb").mouseenter(function () {
        $(this).attr("src", $(this).attr("src").replace("social_fb", "social_fb_hover"));
    });

    $(".social-fb").mouseleave(function () {
        $(this).attr("src", $(this).attr("src").replace("social_fb_hover", "social_fb"));
    });

    $(".social-twt").mouseenter(function () {
        $(this).attr("src", $(this).attr("src").replace("social_twt", "social_twt_hover"));
    });

    $(".social-twt").mouseleave(function () {
        $(this).attr("src", $(this).attr("src").replace("social_twt_hover", "social_twt"));
    });

    $(".social-you").mouseenter(function () {
        $(this).attr("src", $(this).attr("src").replace("social_you", "social_you_hover"));
    });

    $(".social-you").mouseleave(function () {
        $(this).attr("src", $(this).attr("src").replace("social_you_hover", "social_you"));
    });

    $(".social-g").mouseenter(function () {
        $(this).attr("src", $(this).attr("src").replace("social_g+", "social_g+_hover"));
    });

    $(".social-g").mouseleave(function () {
        $(this).attr("src", $(this).attr("src").replace("social_g+_hover", "social_g+"));
    });
    /** end */

    /** Signup functions */

    $("#register").click(function (e) {
        // ANZGO-3844 added by mtanada 20180829
        if (grecaptcha.getResponse() == "") {
            e.preventDefault();
            $('.g-recaptcha div div').css('height', '100%');
            $('.g-recaptcha div div').css('border', '1px solid #f30000');
            $('#recaptchaError').removeAttr('hidden');
            $('#recaptchaError').addClass('in');

            $('#captcha div div').css('height', '100%');
            $('#captcha div div').css('border', '1px solid #f30000');
            $('#recaptchaError').removeAttr('hidden');
            $('#recaptchaError').addClass('in');
            return false;
        }
        e.preventDefault();

        // ANZGO-3672 modified by jbernardez 20180328
        if ($(".uTermsConditions").is(":checked")) {
            // ANZGO-3920 modified by jbernardez 20181206
            var flag = validateFormLogin($("#registerForm"));
            if (flag) {
                var data = { email: $("#email").val() };
                displayModal($(this).attr("href"), data);
            }
        } else {
            $(".uTermsConditions").trigger("change");
            // ANZGO-3920 modified by jbernardez 20181206
            validateFormLogin($("#registerForm"));
        }
    });

    $("#forgot-pass-submit").click(function (e) {
        e.preventDefault();
        var flag = validateForm($(this).parent("form"));
        if (flag) {
            $(this).parent("form").submit();
        } else {
            return false;
        }
    });

    $(document).on("click", "#submit-signup", function () {
        $("#registerForm").submit();
    });

    $(document).on("blur", ".form-control", function () {
        validateForm("", $(this));
    });

    // ANZGO-3920 added by jbernardez 20181206
    $(document).on("blur", "#registerForm input", function () {
        validateFormLogin("", $(this));
    });

    $(document).on("change", ".form-control-tickable", function () {
        if ($.trim($(this).parent("label").text()) == "All") {
            $(".form-control-tickable").prop("checked", $(this).is(":checked"));
        }
        validateForm("", $(this));
    });

    $(document).on("submit", "#editAcctForm", function (e) {
        // e.preventDefault();
        var flag = validateForm($(this));
        return flag;
    });

    $(".subjControl").click(function () {
        var selected = $(this).find(":selected");
        var target = "";
        if ($(this).attr("id") === "source") {
            target = $("#pool");
        } else {
            target = $("#source");
        }
        target.append(selected);
        var poolOpts = target.children("option");
        poolOpts.sort(function (a, b) {
            if (a.value === 'NA') {
                return 1;
            }
            else if (b.value === 'NA') {
                return -1;
            }
            return (a.value > b.value) ? 1 : -1;
        });
        poolOpts.removeAttr("selected");
        if ($(target).attr("id") === "source") {
            $(target).scrollTop(0);
        }


        target.html(poolOpts);

        if ($(target).attr("id") === "pool") {
            $(this).parents("div").parents("div.row").parents("div.form-col").removeClass("has-error");
        }

    });

    $("#uPositionType").change(function () {
        if ($(this).val() == "Other") {
            $(this).parent("div").attr("class", "col-lg-4");
            $(this).parent("div").siblings("div").show();
            $(this).parent("div").siblings("div").children("input").attr("name", "signup[ak_uPositionType]");
            $(this).parent("div").siblings("div").children("input").attr("class", "form-control go-input");
            $(this).attr("name", "")
        } else {
            $(this).parent("div").attr("class", "col-lg-9");
            $(this).parent("div").siblings("div").hide();
            $(this).parent("div").siblings("div").children("input").attr("name", "");
            $(this).parent("div").siblings("div").children("input").attr("class", "");

            $(this).attr("name", "signup[ak_uPositionType]");
        }
    });

    // ANZGO-3672 modified by jbernardez 20180328
    $(".uTermsConditions").change(function () {
        if ($(this).is(":checked")) {
            $(this).parent("div").css("color", "");
        } else {
            $(this).parent("div").css("color", "#E30031");
        }
    });
    /** end */

    /** contact functions */
    $(document).on("click", "#submit-contact", function (e) {
        e.preventDefault();
        var flag = validateForm($("#contactUsForm"));

        // ANZGO-3451 Added by Shane Camus 07/26/2017
        var data = {
            pageName: "Contact",
            action: "Enquiry",
            info: ""
        };

        if (flag) {
            trackUser(data);
            $("#contactUsForm").submit();
        }
    });

    /** Edit account */
    $(".edit-account-trigger").click(function (e) {
        e.preventDefault();
        var data = { flag: "IN" };
        var resizeModal = $(this).attr('resize-modal');
        displayModal($(this).attr("href"), data, resizeModal);
    });

    /** end */

    /** Modal resetter */
    // Reset state of modal when closed. Some functions may have resized the modal.
    // We also need to remove previously placed content to "clean" the modal.
    $("#generalModal").on("hidden.bs.modal", function (e) {
        $(this).children(".modal-dialog").attr("class", "modal-dialog");
        $(this).children(".modal-dialog").children(".modal-content").html("");
        // $(this).children(".modal-dialog").children(".modal-content").html($("#temp-modal-content").html());
    });

    $(document).on("click", ".toggle-rsrc-panel", function (e) {
        e.preventDefault();
        $(this).siblings(".resources-panel").slideToggle();
    });

    // AJAX calls
    $(document).on("click", ".front-ajax-btn", function (e) {
        e.preventDefault();
        e.stopPropagation();

        var btn = $(this);
        // Check if it is a login button
        if ($(this).hasClass("homepage-login")) {
            // check session
            $.ajax({
                url: checkSessionURL, // from header_required.php in themes
                dataType: 'html',
                success: function (d) {
                    var data = parseInt(d);
                    if (data === 1) {
                        $(this).attr("href", goResourcesURL);
                        window.location = $(this).attr("href");
                    } else {
                        if ($(btn).attr("id") == "front-resources" || $(btn).attr("id") == "footer-resources") {
                            $(this).attr("href", goLoginURL + 1);
                        } else {
                            $(this).attr("href", goLoginURL);
                        }
                        frontMemoryScroll = $("body").scrollTop();
                        displayOverlay($(this), togglingItems);
                    }
                },
                error: function (xhr, status, err) {
                    console.log(xhr.responseText);
                }
            });
        } else {
            if ($(this).attr("id") !== "toggle-menu-close") {
                frontMemoryScroll = $("body").scrollTop();
                displayOverlay($(this), togglingItems);
            } else {
                hideOverlay(mainContent);
                // ANZGO-3300 Added by John Renzo Sunico, 05/03/2017
                $('#more-titles').hide();
                cancelHeaderFunctions = false;
            }
        }
    });

    // ANZGO-2851
    $("#activate-login").click(function (e) {
        e.preventDefault();
        displayActivateLogin($(this), togglingItems);
        cancelHeaderFunctions = true;
    });

    // ANZGO-3036
    if (contactusmore == true) {
        displayOverlay($("#header-contact"), togglingItems);
        cancelHeaderFunctions = true;
    }

    $("#header-contact").click(function (e) {
        e.preventDefault();
        displayOverlay($(this), togglingItems);
        cancelHeaderFunctions = true;
    });

    $(".footer-ajax").click(function (e) {
        e.preventDefault();
        displayOverlay($(this), togglingItems);
        cancelHeaderFunctions = true;
    });

    $(document).on("change", ".contact-country-select", function () {
        var option = $(this);
        var country = option.children("option:selected").text();
        var url = option.siblings("input").val();
        $.ajax({
            url: url,
            data: { country: country },
            type: "POST",
            dataType: "html",
            beforeSend: function () {
                if (option.val()) {
                    $(option).removeClass("has-error");
                    $(option).siblings("span").text("");
                }
            },
            success: function (data) {
                var html = data.replace("has-error", "");
                $("#contact-state-selector").removeClass("has-error");
                $("#contact-state-selector").html(html);
            },
            error: function (xhr, response, status) {
                $("#error").html(xhr.responseText);
            }
        });
    });

    // for the My resources button
    $(document).on("click", "#front-resources", function (e) {
        e.preventDefault();
        // check session
        $.ajax({
            url: checkSessionURL, // from header_required.php in themes
            dataType: 'html',
            success: function (d) {
                var data = parseInt(d);
                if (data === 1) {
                    $(this).attr("href", goResourcesURL);
                    window.location = $(this).attr("href");
                } else {
                    $(this).attr("href", goLoginURL + 1);
                    frontMemoryScroll = $("body").scrollTop();
                    displayOverlay($(this), togglingItems);
                }
            },
            error: function (xhr, status, err) {
                alert(xhr.responseText);
            }
        });
    });

    // SB-9 added by jbernardez 20191106
    $(document).on('click', '.delete-resource', function (e) {
        e.preventDefault();
        delURL = $(this)[0].value;
        parentPanel = $(this).parents('div.panel');
        
        $.ajax({
            url: delURL, // from header_required.php in themes
            type: 'DELETE',
            success: function (d) {
                // hide panel parent
                parentPanel.remove();
            },
            error: function (xhr, status, err) {
                alert(xhr.responseText);
            }
        });
    });

    // IE Fixes
    $(document).on("click", "#cookie-info-dismiss-div", function (e) {
        $(this).find("a").trigger("click");
    });

    var titleUrl = currentUrl,
        delimiter = '/',
        start = 5,
        tokens = titleUrl.split(delimiter).slice(start),
        prettyUrl = tokens.join(delimiter);
    prettyUrl = prettyUrl.slice(0, -1);
    var extraUrl = prettyUrl.split("/");

    //Weblinks with redirector class (Title page)
    $("a.redirector-links").on('click', function (e) {
        var tabName = $(this).closest('div.go-accordion').find('div.tab-name').text();
        // remove whitespace
        tabName = tabName.replace(/\s/g, '');
        // Need to check if the prettyUrl has /weblinks from myresources page 'ViewAllResources' link
        if (inArray('weblinks', extraUrl)
            || inArray('weblinks%20', extraUrl)
            || inArray('teacher%20resource%20package', extraUrl)
            || inArray('word%20activities', extraUrl)
            || inArray('pdf%20textbook', extraUrl)) {
            prettyUrl = extraUrl[0];
        }
        if (tabName === 'Weblinks') {
            var data = {
                pageName: 'Title',
                action: tabName,
                info: prettyUrl
            };
            trackUser(data);
        }
    });

    //Weblinks with no class (Title page)
    $("div.go-accordion ul.content-detail a").on('click', function (e) {
        var tabName = $(this).closest('div.go-accordion').find('div.tab-name').text();
        // remove whitespace
        tabName = tabName.replace(/\s/g, '');
        if ((tabName === 'Weblinks')
            && (inArray('weblinks', extraUrl)
                || inArray('weblinks%20', extraUrl)
                || inArray('teacher%20resource%20package', extraUrl)
                || inArray('word%20activities', extraUrl)
                || inArray('pdf%20textbook', extraUrl))) {
            prettyUrl = extraUrl[0];
            var data = {
                pageName: 'Title',
                action: tabName,
                info: prettyUrl
            };
            trackUser(data);
        }
    });

    //Weblinks with no class (Resources page)
    $('#generalModal').on('click', 'a', function (e) {
        var tabName = $('#generalModalLabel').text();
        var contentID = $(this).closest('p').attr('id');
        if (tabName === 'Weblinks Chapters') {
            var data = {
                pageName: 'Resources',
                action: tabName,
                info: contentID
            };
            trackUser(data);
        }
    });

    /**
     * ANZGO-3529 Added by Jeszy Tanada 10/19/2017
     * To log resources with NO content-file-download class (Title page)
     */
    $("div.go-accordion p a").not(".content-file-download").on('click', function (e) {
        var tabName = $(this).closest('div.go-accordion').find('div.tab-name').text();
        var zipped = $(this).closest('p').text().toLowerCase();
        var smallZip = zipped.split(" ");
        // Need to check if the prettyUrl has extraUrl when clicking 'ViewAllResources' link in my resources page
        if (inArray("weblinks", extraUrl)
            || inArray("teacher%20resource%20package", extraUrl)
            || inArray("word%20activities", extraUrl)
            || inArray("pdf%20textbook", extraUrl)) {
            prettyUrl = extraUrl[0];
        }
        if (tabName === 'Word activities'
            || (tabName === 'Teacher Resource Package' && inArray("[zipped", smallZip))
            || tabName === 'PDF Textbook') {
            var data = {
                pageName: 'Title',
                action: tabName,
                info: prettyUrl
            };
            trackUser(data);
        }
    });

    /**
     * ANZGO-3452 added by John Renzo S. Sunico, November 11, 2017
     * Logs My Resources ACTIVATE button click
     */

    // Open activate popup analytics
    $(document).on('click', 'a[href*="/go/activate/"]', function (e) {
        if (location.pathname.indexOf('/go/myresources') > -1) {
            trackUser({
                pageName: 'My Resources',
                action: 'Click',
                info: 'activatePopupResources'
            });
        } else if (location.pathname.indexOf('/go/titles/') > -1) {
            trackUser({
                pageName: 'Titles',
                action: 'Click',
                info: 'activatePopupTitles'
            });
        } else {
            // Do nothing
        }
    });

    // Open login popup analytics
    $(document).on('click', 'div[href*="/go/login/"], a[href*="/go/login/"]', function (e) {
        var identifier;

        if ($(e.target).parents('.carousel-inner').length > 0) {
            identifier = ' Carousel';
        } else if ($(e.target).attr('id') === 'header-login') {
            identifier = ' Header';
        } else if ($(e.target).hasClass('homepage-login')) {
            identifier = ' Accordion'
        } else {
            identifier = '';
        }

        trackUser({
            pageName: 'Home' + identifier,
            action: 'Click',
            info: 'loginPopup'
        });
    });


    // <!-- ANZGO-3791 added by jdchavez 07/09/2018 -->
    $(document).on('click', 'div[href*="/go/login/"], a[href*="/go/login/"]', function (e) {

        if ($(e.target).parents('.carousel-inner').length > 0 ||
            $(e.target).attr('id') === 'header-login' ||
            $(e.target).hasClass('homepage-login')) {
            $("#dismiss-announce").click();
        }
    });

    // <!-- ANZGO-3791 added by jdchavez 07/09/2018 -->
    $(document).on('click', 'div[href*="/go/search/"], a[href*="/go/search/"]', function (e) {

        if ($(e.target).attr('id') === 'header-browse') {
            $("#dismiss-announce").click();
        }
    });

    // ANZGO-3913 added by jbernardez 20181112
    // the addition of the new login page design does not include a footer
    // on calling Subjects link, it will not display footer by DT design
    // this will force footer-wrapper to display block to show when Subject is clicked
    $(document).on('click', '#header-browse', function (e) {
        $("#footer-wrapper").show();
    });

    // ANZGO-3791 added by jdchavez 07/09/2018
    // ANZGO-3881 modified by mtanada 20181017
    $(document).on('click', '#header-support', function (e) {

        if ($(e.target).attr('id') === 'header-support' ||
            $(e.target).attr('id') === 'front-support') {
            $("#dismiss-announce").click();
        }
    });

    // <!-- ANZGO-3791 added by jdchavez 07/09/2018 -->
    $(document).on('click', 'div[href*="/go/contact/"], a[href*="/go/contact/"]', function (e) {

        if ($(e.target).attr('id') === 'header-contact') {
            $("#dismiss-announce").click();
        }
    });

    //<!-- ANZGO-3791 added by jdchavez 07/09/2018 -->
    $(document).ready(function () {
        if ($('#banner-on').length) {
            $("#dismiss-announce").click();
        }
    });


    // Login button analytics
    // ANZGO-3913 modified by jbernardez 20181108
    $(document).on('click', 'button[value="LOGIN"]', function (e) {
        // ANZGO-3736 added by jbernardez 20180601
        if (grecaptcha.getResponse() == "") {
            $('.g-recaptcha div div').css('height', '100%');
            $('.g-recaptcha div div').css('border', '1px solid #f30000');
            $('#recaptchaError').removeAttr('hidden');
            $('#recaptchaError').addClass('in');

            $('#captcha div div').css('height', '100%');
            $('#captcha div div').css('border', '1px solid #f30000');
            $('#recaptchaError').removeAttr('hidden');
            $('#recaptchaError').addClass('in');
            return false;
        }

        trackUser({
            pageName: 'Login popup',
            action: 'Click',
            info: 'loginBtn'
        });
    });

    //ANZGO-3812 jdchavez 08/01/2018 added recaptcha
    //contact us form recaptcha
    $(document).on('submit', '#contactUsForm', function (e) {
        if (grecaptcha.getResponse() == "") {
            e.preventDefault();
            $('.g-recaptcha div div').css('height', '100%');
            $('.g-recaptcha div div').css('border', '1px solid #f30000');
            $('#recaptchaError').removeAttr('hidden');
            $('#recaptchaError').addClass('in');

            $('#captcha div div').css('height', '100%');
            $('#captcha div div').css('border', '1px solid #f30000');
            $('#recaptchaError').removeAttr('hidden');
            $('#recaptchaError').addClass('in');
            return false;
        }
    });

    // Teacher signup link analytics
    $(document).on('click', 'a[href*="/go/signup/teacher"]', function (e) {
        var page = $('#toggle-menu-close').is(':visible') ? 'Login popup' : 'Home Accordion';
        trackUser({
            pageName: page,
            action: 'Click',
            info: 'signupTeacherLink'
        });
    });

    // Student signup link analytics
    $(document).on('click', 'a[href*="/go/signup/student"]', function (e) {
        var page = $('#toggle-menu-close').is(':visible') ? 'Login popup' : 'Home Accordion';
        trackUser({
            pageName: page,
            action: 'Click',
            info: 'signupStudentLink'
        });
    });

    // Forgot password link analytics
    $(document).on('click', 'a[href*="/go/forgot_password/"]', function (e) {
        trackUser({
            pageName: 'login popup',
            action: 'Click',
            info: 'forgotPasswordLink'
        });
    });
}); // End of document ready

/**
 * ANZGO-3529 Added by Jeszy Tanada 10/19/2017
 * Same function as in_array in php
 */
function inArray (needle, haystack) {
    for (var i = 0; i < haystack.length; i++) {
        if (haystack[i] === needle) {
            return true;
        }
    }
    return false;
}

function resizeHeader (windowWidth, animateLogo) {
    var consClasses = "btn btn-black-text ";
    var toggleLogin = $("#toggle-login");
    var toggleBrowse = $("#toggle-browse");
    var loginSpan = "<span class='svg-text'>" + $("#login-text").val() + "</span>";
    var browseSpan = "<span class='svg-text'>Browse</span>";

    if (windowWidth <= 1024) {
        navBarCollapsed = true;
        toggleButtonsMinimized = true;
        $(".go-header").addClass("go-header-resize");

        if ($(window).width() > 500) {
            toggleLogin.children("svg").attr("class", "svg-mid svg-white");
            toggleBrowse.children("svg").attr("class", "svg-mid svg-white");
        }

        toggleLogin.removeClass(consClasses + "btn-success");
        toggleBrowse.removeClass(consClasses + "btn-info");

        toggleLogin.addClass("mini");
        toggleBrowse.addClass("mini");

        if (toggleLogin.find("span").length > 0) {
            toggleLogin.find("span").remove();
        }
        if (toggleBrowse.find("span").length > 0) {
            toggleBrowse.find("span").remove();
        }

        toggleLogin.css("width", "48px");
        toggleBrowse.css("width", "48px");

        if (animateLogo) {
            moveBrandToLeft();
        }
    } else if (windowWidth < 1295) {
        navBarCollapsed = true;
        toggleButtonsMinimized = false;
        $(".go-header").addClass("go-header-resize");
        if (!toggleButtonsMinimized) {
            toggleLogin.children("svg").attr("class", "");
            toggleBrowse.children("svg").attr("class", "");


            toggleLogin.addClass(consClasses + "btn-success");
            toggleBrowse.addClass(consClasses + "btn-info");

            toggleLogin.removeClass("mini");
            toggleBrowse.removeClass("mini");

            if (toggleLogin.find("span").length <= 0) {
                toggleLogin.append(loginSpan);
            }
            if (toggleBrowse.find("span").length <= 0) {
                toggleBrowse.append(browseSpan);
            }

            toggleLogin.css("width", "169px");
            toggleBrowse.css("width", "169px");

        } else {
            toggleLogin.children("svg").attr("class", "svg-mid svg-white");
            toggleBrowse.children("svg").attr("class", "svg-mid svg-white");
        }

        if (animateLogo) {
            moveBrandToRight();
        }
    } else {
        navBarCollapsed = false;
        toggleButtonsMinimized = true;
        $(".go-header").removeClass("go-header-resize");

        toggleLogin.children("svg").attr("class", "svg-mid svg-white");
        toggleBrowse.children("svg").attr("class", "svg-mid svg-white");

        toggleLogin.removeClass(consClasses + "btn-success");
        toggleBrowse.removeClass(consClasses + "btn-info");

        toggleLogin.removeClass("mini");
        toggleBrowse.removeClass("mini");

        if (toggleLogin.find("span").length > 0) {
            toggleLogin.find("span").remove();
        }
        if (toggleBrowse.find("span").length > 0) {
            toggleBrowse.find("span").remove();
        }

        toggleLogin.css("width", "48px");
        toggleBrowse.css("width", "48px");
        if (animateLogo) {
            moveBrandToRight();
        }
    }
}

function minimizeNavBarForm (windowWidth) {
    $(".navbar-form").stop().animate({
        width: "100",
        opacity: "0"
    }, 500, function () {
        $(this).hide();
        resizeHeader(windowWidth);
    });
}

function maximizeNavBarForm (windowWidth, animateLogo, cancelLogoFade) {
    cancelHeaderFunctions = false;
    resizeHeader(windowWidth, animateLogo, cancelLogoFade);
    $(".navbar-form").show();
    $(".navbar-form").stop().animate({
        width: "400",
        opacity: "1"
    }, 500);
}

function minimizeToggleButtons (windowWidth) {
    $(".go-toggle").stop().animate({
        width: "48"
    }, 500, function () {
        resizeHeader(windowWidth);
    });
}

function maximizeToggleButtons () {
    var consClasses = "btn btn-black-text ";
    var toggleLogin = $("#toggle-login");
    var toggleBrowse = $("#toggle-browse");
    var loginSpan = "<span class='svg-text'>" + $("#login-text").val() + "</span>";
    var browseSpan = "<span class='svg-text'>Browse</span>";
    toggleLogin.children("svg").attr("class", "");
    toggleBrowse.children("svg").attr("class", "");

    if (toggleLogin.find("span").length <= 0) {
        toggleLogin.append(loginSpan);
    }
    if (toggleBrowse.find("span").length <= 0) {
        toggleBrowse.append(browseSpan);
    }

    toggleLogin.addClass(consClasses + "btn-success");
    toggleBrowse.addClass(consClasses + "btn-info");
    $(".go-toggle").stop().animate({
        width: "169"
    }, 500);
}


function resizeHeaderOnLoad () {

    resizeHeader($(window).width(), true);

    if ($(window).width() < 500) {

        var toggleLogin = $("#toggle-login");
        var toggleBrowse = $("#toggle-browse");

        toggleLogin.children("svg").attr("class", "svg-mid svg-black");
        toggleBrowse.children("svg").attr("class", "svg-mid svg-black");
    }
}

function displayOverlay (element) {
    $(".breadcrumb-container").css("visible", "hidden");

    // ANZGO-3013
    var pageName = '';
    var action = '';
    switch (element.attr("href")) {
        case '/go/contact/':
            pageName = 'Contact';
            action = 'View';
            break;
        case '/go/user_landing/':
            pageName = 'User Landing';
            action = 'View';
            break;
        case '/go/search/':
            pageName = 'Browse';
            action = 'Search';
            break;
        case '/go/activate/':
        default:
            pageName = 'Activate';
            action = 'View';
            break;
    }

    // ANZGO-3013
    var data = {
        'pageName': pageName,
        'action': action,
        'info': ''
    };
    trackUser(data);

    $.ajax({
        url: element.attr("href"),
        data: {
            flag: 'IN'
        },
        type: "POST",
        dataType: "html",
        success: function (response) {
            // ANZGO-3889 modified by mtanada 20181022
            $("#main-content div").html(response);
            setToOverlayView();
            $("body").scrollTop(0);
            // ANZGO-3889 added by mtanada 20181025
            if (element.attr("href") == '/go/search/' || element.attr("href") == '/go/login/') {
                $("#bread").removeClass("container breadcrumb-container");
                $("#breadcrumb-row").removeClass("row");
            }
            if (element.attr("href") == '/go/contact/') {
                $("#bread").addClass("container breadcrumb-container");
                $("#breadcrumb-row").addClass("row");
            }

            // ANZGO-3736 added by jbernardez 20180601
            grecaptcha.render("captcha", { sitekey: "6Lc7ti0UAAAAAEsVUT0q6um2a2WCjuhdb4CxpjoX", theme: "light" });
        },
        error: function (xhr, status, error) {
            alert(error + ": " + status);
        }
    });

}

// ANZGO-2851
// rcId for /activate
function displayActivateLogin (element, rcId) {

    $.ajax({
        url: element.attr("href"),
        data: {
            flag: 'IN',
            rcId: rcId
        },
        type: "POST",
        dataType: "html",
        success: function (response) {
            $("#main").html(response);
            setToOverlayView();
            $("body").scrollTop(0);
        },
        error: function (xhr, status, error) {
            alert(error + ": " + status);
        }
    });

}

function hideOverlay (mainContent) {
    cancelHeaderFunctions = true;
    // ANZGO-3889 modified by mtanada 20181022
    $("#main-content div").html(mainContent);
    $("#toggle-menu-close").hide();
    $("#toggle-login").css("display", "");
    $("#toggle-browse").css("display", "");
    $("#toggle-menu").css("display", "");
    $(".breadcrumb").css("visibility", "visible");
    $("body").scrollTop(frontMemoryScroll);

    resizeHeader($(window).width());

    // restart carousel
    $(".carousel").carousel("cycle");

    $(window).trigger("scroll");
}

function moveBrandToLeft () {

    var windowWidth = $(window).width();
    var marginLeft = "-140px";

    if (windowWidth >= 760 && windowWidth <= 780) {
        marginLeft = "-175px";
        logoMovedToLeft = false;
    } else if (windowWidth > 780 && windowWidth <= 1024) {
        logoMovedToLeft = true;
        moveBrandToRight()
    } else if (windowWidth < 760) {
        marginLeft = "-140px";
        logoMovedToLeft = false;
    }

    if (!logoMovedToLeft) {

        $(".navbar-brand").animate({
            "margin-left": marginLeft
        }, 500, function () {
            logoMovedToLeft = true;
        });
    }
}

function moveBrandToRight () {
    if (logoMovedToLeft) {
        $(".navbar-brand").animate({
            "margin-left": "-15px"
        }, 500, function () {
            logoMovedToLeft = false;
        });
    }
}

function validateForm (form, element) {
    var errorFlag = true;
    var inputs = "";
    var oInputs = ""; // handler for checkboxes and radio buttons
    var ticked = 0;
    var notification = "* ";
    var initElement = null;
    if (element) {
        if (element.hasClass("form-control-tickable")) {
            oInputs = element;
        } else {
            inputs = element;
        }
    } else {
        inputs = form.find(".form-control");
        oInputs = form.find(".form-control-tickable");
    }

    if (inputs.length > 0) {
        inputs.each(function () {
            var value = $(this).val();
            var name = $(this).attr("name");
            var type = $(this).attr("type");
            var equalto = $(this).attr("equalto");

            var container = $(this).parents(".form-col");
            var notif = $(this).siblings(".help-block");

            var notRequired = $(this).attr('no-required');
            var noCheck = $(this).attr('no-check');

            // ANZGO-3002
            var placeHolder = $(this)[0].placeholder;

            if (typeof notRequired === typeof undefined || notRequired === false) {
                if (!value) {
                    container.addClass("has-error");
                    notif.html(notification + "This field is required.");
                    errorFlag = false;
                    if (form && (!initElement)) {
                        initElement = $(this);
                    }
                } else {
                    if (type === "email") {
                        // lets not include the validation of forgot password emails
                        // ANZGO-2657
                        if ($(this).attr("id") !== "forgot-email") {
                            if (typeof noCheck === typeof undefined || noCheck === false) {
                                $.post(uniqueEmailCheckUrl, { uEmail: value }, function (data) {
                                    if (!data.success) {
                                        container.addClass("has-error");
                                        notif.html(notification + "The email address " + value + " is already in use.");
                                        errorFlag = false;
                                        if (form && (!initElement)) {
                                            initElement = $(this);
                                        }
                                    }
                                }, "json");
                            }
                        }

                        // email validation fix to handle all special characters, etc
                        if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(value)) {
                            container.removeClass("has-error");
                            notif.html("");
                        } else {
                            container.addClass("has-error");
                            notif.html(notification + "Invalid format of email address.");
                            errorFlag = false;
                            if (form && (!initElement)) {
                                initElement = $(this);
                            }
                        }

                    } else if (type === "password" && name !== "oldPassword") {

                        if (/^\s/g.test(value) === true || /\s$/g.test(value) === true) {
                            container.removeClass("has-success").addClass("has-error");
                            notif.html(notification + "Password must not have leading and trailing whitespaces.");
                            errorFlag = false;
                            if (form && (!initElement)) {
                                initElement = $(this);
                            }
                        } else if (value.length < 8) {
                            container.removeClass("has-success").addClass("has-error");
                            notif.html(notification + "Password must be 8 characters or longer.");
                            errorFlag = false;
                            if (form && (!initElement)) {
                                initElement = $(this);
                            }
                        } else if (/\d/g.test(value) === false) {
                            container.removeClass("has-success").addClass("has-error");
                            notif.html(notification + "Password must contain at least 1 number.");
                            errorFlag = false;
                            if (form && (!initElement)) {
                                initElement = $(this);
                            }
                        } else if (/[a-zA-Z]/g.test(value) === false) {
                            container.removeClass("has-success").addClass("has-error");
                            notif.html(notification + "Password must contain at least 1 letter.");
                            errorFlag = false;
                            if (form && (!initElement)) {
                                initElement = $(this);
                            }
                        } else {
                            container.removeClass("has-error");
                            notif.html("");
                        }

                        if (typeof equalto !== typeof undefined && equalto !== false) {
                            if (value !== $("input[name='" + equalto + "']").val()) {
                                container.addClass("has-error");
                                notif.html(notification + "The password does not match.");
                                errorFlag = false;
                                if (form && (!initElement)) {
                                    initElement = $(this);
                                }
                            } else {
                                container.removeClass("has-error");
                                notif.html("");
                            }
                        }
                    } else {
                        var placeholder = $(this).attr("placeholder");
                        if (typeof placeholder === typeof undefined || placeholder === false) {
                            container.removeClass("has-error");
                            notif.html("");
                        } else {
                            if ((placeholder.toLowerCase() === "postcode" || placeholder.toLowerCase() === "postcode *") && isNaN(value)) {
                                container.removeClass("has-success").addClass("has-error");
                                notif.html(notification + "Invalid characters found.");
                                errorFlag = false;
                                if (form && (!initElement)) {
                                    initElement = $(this);
                                }
                            } else {
                                container.removeClass("has-error");
                                notif.html("");
                            }
                        }
                    }
                }

                // ANZGO-3002
                if (placeHolder === "School Phone Number") {
                    var phoneno = /^[\+]?[(]?[0-9]{2}[)]?[-\s\.]?[0-9]{2}[-\s\.]?[0-9]{4,6}$/im;
                    if ((value.match(phoneno))) {
                        return true;
                    } else {
                        container.addClass("has-error");
                        notif.html(notification + "Phone number must be a number and be a minimum of 8 digits.");
                        errorFlag = false;
                    }
                }
            }
        });
    }

    if (oInputs.length > 0) {
        oInputs.each(function () {
            var name = $(this).attr("name");
            var type = $(this).attr("type");
            var equalto = $(this).attr("equalto");

            var container = $(this).parents(".form-col");
            var notif = $(this).siblings(".help-block");

            var notRequired = $(this).attr('no-required');
            if (typeof notRequired === typeof undefined || notRequired === false) {
                var c = $("input[type='checkbox']");
                $(c).each(function () {
                    if ($(this).hasClass("form-control-tickable") && $(this).is(":checked")) {
                        ticked++;
                    }
                });
            }
        });

        var tickNotif;
        if (ticked <= 0) {
            oInputs.parents(".form-col").addClass("has-error");
            tickNotif = oInputs.parents(".form-col").children(".help-block");
            tickNotif.html("Required. Please tick at least one.");
            errorFlag = (errorFlag && false);
        } else {
            oInputs.parents(".form-col").removeClass("has-error");
            tickNotif = oInputs.parents(".form-col").children(".help-block");
            tickNotif.html("");
            errorFlag = (errorFlag && true);
        }
    }

    if (form && $(initElement).length > 0) {
        $("body").animate({
            scrollTop: ($(initElement).offset().top) - 250
        }, "medium");
        $(initElement).focus();
    }

    return errorFlag;
}

function displayModal (sourceUrl, sourceData, resizeModal) {
    var html = "";
    $.ajax({
        url: sourceUrl,
        data: sourceData,
        type: "POST",
        dataType: "html",
        beforeSend: function () {
            $("#generalModal").modal("show");
        },
        success: function (response) {
            if (resizeModal) {
                $("#generalModal").children(".modal-dialog").addClass(resizeModal);
            }
            html = response;
        },
        error: function (xhr, status, error) {
            $(".modal-content").html(xhr.responseText);
        },
        complete: function () {
            $(".modal-content").html(html);
        }
    });
}

// ANZGO-3872 Added by Shane Camus 10/05/18
function displayDownloadNotification () {
    $('#notification-wrapper').css('display', 'none').fadeIn();
    setTimeout(function () {
        $('#notification-wrapper').fadeOut();
    }, 5000);
}

function displayHMPendingNotification (msg) {
    $('#pending-hm-wrapper p').text(msg);
    $('#pending-hm-wrapper').css('display', 'none').fadeIn();
    setTimeout(function () {
        $('#pending-hm-wrapper').fadeOut();
    }, 10000);
  }

// ANZGO-3872 Added by Shane Camus 10/05/18
function downloadContent (sourceUrl) {
    window.location.href = sourceUrl;
}

// ANZGO-3872 Added by Shane Camus 10/05/18
// ANZGO-3946 Modified by Shane Camus 12/11/18
function closeModal () {
    $("#generalModal").modal("hide");
    $('#resources-list').modal('hide');
}

function setToOverlayView () {
    resizeHeader(500);
    $("#toggle-menu").hide();
    $("#toggle-login").hide();
    $("#toggle-browse").hide();

    $("#toggle-menu-close").show();
    $(".breadcrumb").css("visibility", "hidden");
    cancelHeaderFunctions = true;
}

// ANZGO-3013
function trackUser (data) {
    $.ajax({
        url: '/go/myresources/userTracking/',
        type: 'POST',
        data: data,
        success: function (data) {
            return true;
        }
    });
}

// ANZGO-3920 added by jbernardez 20181206
function validateFormLogin (form, element) {
    var errorFlag = true;
    var inputs = "";
    var oInputs = ""; // handler for checkboxes and radio buttons
    var ticked = 0;
    var notification = "";
    var initElement = null;
    if (element) {
        // ANZGO-3957 modified by machua 20181207 to get the correct element
        if (element.hasClass("ccm-input-checkbox")) {
            oInputs = element;
        } else {
            inputs = element;
        }
    } else {
        inputs = form.find("input");
        // ANZGO-3957 modified by machua 20181207 to get the correct element
        oInputs = form.find(".ccm-input-checkbox");
    }

    if (inputs.length > 0) {
        inputs.each(function () {
            var value = $(this).val();
            var name = $(this).attr("name");
            var type = $(this).attr("type");
            var equalto = $(this).attr("equalto");

            var container = $(this).parents(".input-field");
            var notif = $(this).siblings(".field-error");

            var notRequired = $(this).attr('no-required');
            var noCheck = $(this).attr('no-check');

            // ANZGO-3002
            var placeHolder = $(this)[0].placeholder

            // ANZGO-3980
            var attrID = $(this).attr('id');

            if (typeof notRequired === typeof undefined || notRequired === false) {
                if (!value) {
                    container.addClass("has-error");
                    notif.html(notification + "This field is required.");
                    errorFlag = false;
                    if (form && (!initElement)) {
                        initElement = $(this);
                    }
                } else {
                    if (type === "email") {
                        // lets not include the validation of forgot password emails
                        // ANZGO-2657
                        if ($(this).attr("id") !== "forgot-email") {
                            if (typeof noCheck === typeof undefined || noCheck === false) {
                                $.post(uniqueEmailCheckUrl, { uEmail: value }, function (data) {
                                    if (!data.success) {
                                        container.addClass("has-error");
                                        notif.html(notification + "The email address " + value + " is already in use.");
                                        errorFlag = false;
                                        if (form && (!initElement)) {
                                            initElement = $(this);
                                        }
                                    }
                                }, "json");
                            }
                        }

                        // email validation fix to handle all special characters, etc
                        if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(value)) {
                            container.removeClass("has-error");
                            notif.html("");
                        } else {
                            container.addClass("has-error");
                            notif.html(notification + "Invalid format of email address.");
                            errorFlag = false;
                            if (form && (!initElement)) {
                                initElement = $(this);
                            }
                        }

                    } else if ((type === "password" && name !== "oldPassword")
                        || (attrID && attrID !== '' && attrID.includes('password-field'))) { // ANZGO-3980 added checker if field type changes to text

                        if (/^\s/g.test(value) === true || /\s$/g.test(value) === true) {
                            container.removeClass("has-success").addClass("has-error");
                            notif.html(notification + "Password must not have leading and trailing whitespaces.");
                            errorFlag = false;
                            if (form && (!initElement)) {
                                initElement = $(this);
                            }
                        } else if (value.length < 8) {
                            container.removeClass("has-success").addClass("has-error");
                            notif.html(notification + "Password must be 8 characters or longer.");
                            errorFlag = false;
                            if (form && (!initElement)) {
                                initElement = $(this);
                            }
                        } else if (/\d/g.test(value) === false) {
                            container.removeClass("has-success").addClass("has-error");
                            notif.html(notification + "Password must contain at least 1 number.");
                            errorFlag = false;
                            if (form && (!initElement)) {
                                initElement = $(this);
                            }
                        } else if (/[a-zA-Z]/g.test(value) === false) {
                            container.removeClass("has-success").addClass("has-error");
                            notif.html(notification + "Password must contain at least 1 letter.");
                            errorFlag = false;
                            if (form && (!initElement)) {
                                initElement = $(this);
                            }
                        } else {
                            container.removeClass("has-error");
                            notif.html("");
                        }

                        if (typeof equalto !== typeof undefined && equalto !== false) {
                            if (value !== $("input[name='" + equalto + "']").val()) {
                                container.removeClass("success");
                                container.addClass("has-error");
                                notif.html(notification + "The password does not match.");
                                errorFlag = false;
                                if (form && (!initElement)) {
                                    initElement = $(this);
                                }
                            } else {
                                container.removeClass("has-error");
                                // container.addClass("success"); // ANZGO-3920 modified by jbernardez 20181122
                                notif.html("");
                            }
                        }
                    } else {
                        var placeholder = $(this).attr("placeholder");
                        if (typeof placeholder === typeof undefined || placeholder === false) {
                            container.removeClass("has-error");
                            notif.html("");
                        } else {
                            if ((placeholder.toLowerCase() === "postcode" || placeholder.toLowerCase() === "postcode *") && isNaN(value)) {
                                container.removeClass("has-success").addClass("has-error");
                                notif.html(notification + "Invalid characters found.");
                                errorFlag = false;
                                if (form && (!initElement)) {
                                    initElement = $(this);
                                }
                            } else {
                                container.removeClass("has-error");
                                notif.html("");
                            }
                        }

                        // ANZGO-3920 added by jbernardez 20181205
                        // this is added here as when user clicks on show password, the attribute type is changed
                        // to text, and would not fall under the filter above for type password
                        // type text falls under else
                        if (typeof equalto !== typeof undefined && equalto !== false) {
                            if (value !== $("input[name='" + equalto + "']").val()) {
                                container.removeClass("success");
                                container.addClass("has-error");
                                notif.html(notification + "The password does not match.");
                                errorFlag = false;
                                if (form && (!initElement)) {
                                    initElement = $(this);
                                }
                            } else {
                                container.removeClass("has-error");
                                // container.addClass("success"); // ANZGO-3920 modified by jbernardez 20181122
                                notif.html("");
                            }
                        }
                    }
                }

                // ANZGO-3002
                if (placeHolder === "School Phone Number") {
                    var phoneno = /^[\+]?[(]?[0-9]{2}[)]?[-\s\.]?[0-9]{2}[-\s\.]?[0-9]{4,6}$/im;
                    if ((value.match(phoneno))) {
                        return true;
                    } else {
                        container.addClass("has-error");
                        notif.html(notification + "Phone number must be a number and be a minimum of 8 digits.");
                        errorFlag = false;
                    }
                }
            }
        });
    }

    if ($('.uCustomerCare')[0] != null) {
        if ($('.uCustomerCare')[0].checked === true) {
            if (oInputs.length > 0) {
                oInputs.each(function () {

                    var container = $(this).parents(".checkbox-field");
                    var notif = $(this).siblings(".field-error");

                    // ANZGO-3957 modified by machua 20181207 to get the checked subjects
                    if ($(this)[0].checked) {
                        ticked++;
                    }
                });

                var tickNotif;
                if (ticked <= 0) {
                    oInputs.parents(".input-field").addClass("has-error");
                    tickNotif = oInputs.parents(".input-field").children(".field-error");
                    tickNotif.html("Required. Please tick at least one.");
                    errorFlag = (errorFlag && false);
                } else {
                    oInputs.parents(".input-field").removeClass("has-error");
                    tickNotif = oInputs.parents(".input-field").children(".field-error");
                    tickNotif.html("");
                    errorFlag = (errorFlag && true);
                }
            }
        }
    }

    if (form && $(initElement).length > 0) {
        $("body").animate({
            scrollTop: ($(initElement).offset().top) - 250
        }, "medium");
        $(initElement).focus();
    }

    return errorFlag;
}
