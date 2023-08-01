/*
 * Handles all marketing interactivity. This includes initial popup in the homepage
 * and the user guide in the My Resources page.
 */

$(document).ready(function(){
    windowResize($(window).width());
    var url = window.location.pathname;

    // ANZUAT-128
    // force showHelpFlag to 0 if TRUE
    firstLoginTemp = parseInt( $("#firstLogin").val() );
    showHelpTemp = parseInt( $("#showHelpFlag").val() );
    hideHelpSessionTemp = parseInt( $("#hideHelpSession").val() );

    if ( (firstLoginTemp == 1) && (showHelpTemp == 0) ) {
        $("#showHelpFlag").val(1);
    }

    if ( hideHelpSessionTemp == 1 ) {
        $("#showHelpFlag").val(1);
    }

    /** ANZGO-3675 Modified by Maryjes Tanada to remove user guide pop-up 03/22/2018
     * Commenting instead of removing, in case needed again
     */
    // Check what kind of popup should we show.
    // if(url.search("myresources") > 0) {
    //     displayHelpPopup(); // For the user guide.
    // } else {
        //popupSeenCheck(); // For the initial marketing popup.
    // }

    $(document).on("click","#helpClose",function(){
        var hidePopup = $("#hideHelpPopupFlag").is(":checked");
        if(hidePopup) {
            $.ajax({
                url : hideHelpPopupURL
            });
        }

        // ANZUAT-128
        $.ajax({
            url : hideHelpSessionURL
        });
    });

    /**
     * ANZGO-3344 Modified by John Renzo S. Sunico, May 03, 2017
     * Remove marketing popup.
     */

    // $('#mktgModal').on('hidden.bs.modal', function () {
    //     document.cookie="mktg-popup-flag=seen";
    // });

    $('#helpModal').on('hidden.bs.modal', function () {
        var helpImgSrc = $('#helpImg').attr('src');
        var newSrc = '';
        if(helpImgSrc.indexOf('userguide-mobile') > 0) {
            newSrc = helpImgSrc.replace('userguide-mobile','userguide');
        } else {
            newSrc = helpImgSrc;
        }
        $('#helpImg').attr('src',newSrc);
    });

    $(window).resize(function(){
        windowResize($(window).width());
    });

    // $('#marketing-img-container').click(function(){
    //     var parentDiv = $(this).parent("div");
    //     $(this).hide();
    //     parentDiv.html('<iframe style="display:none;" id="marketing-video" width="100%" height="40%" src="https://www.youtube.com/embed/LEmJFFtA110?autoplay=1" frameborder="0" allowfullscreen></iframe>');
    //     $('#marketing-video').show();
    // });

    // $('#mktgModal').on('hide.bs.modal',function(e){
    //     $('#marketing-video').remove();
    //     $('#marketing-img-container').show();
    // });
});


/**
 * Set in the session if the initial marketing has been seen or not.
 * If this is the first time a user enters a site, the popup will show
 * and the session is toggled right away. So in the next loading of the
 * site no popup would show.
 *
 * @returns {void}
 */
function popupSeenCheck() {
    $.ajax({
        url : checkPopupCookieURL,
        dataType : "json",
        success : function(d) {
            if(d.status === "not-seen") {
                displayMktgPopup();
            }
        },
        error : function(xhr,status,err) {
            console.log(xhr.responseText);
        }
    });
}

/**
 * Displays the initial marketing popup.
 *
 * @returns {void}
 */
function displayMktgPopup() {
    $("#mktgModal").modal('show');
    updatePopupSeen();
}

/**
 * Toggles the variable in the session that the marketing popup
 * has been seen.
 *
 * @returns {void}
 */
function updatePopupSeen() {
    $.ajax({
        url : updatePopupCookieURL
    });
}

/**
 * Displays the user guide.
 *
 * @returns {void}
 */
function displayHelpPopup() {
    var helpFlag = parseInt($("#showHelpFlag").val());

    if(!helpFlag) {
        $("#helpModal").modal('show');
    }
}

function windowResize(width) {
    var helpImgSrc = $('#helpImg').attr('src');



    if(width >= 1024) {
        $('#helpImg').attr('src',helpImgSrc.replace('userguide-mobile.png','userguide.png'));
    } else if(width >= 768) {
        $('#helpImg').attr('src',helpImgSrc.replace('userguide.png','userguide-mobile.png'));
    } else {
        $('#helpImg').attr('src',helpImgSrc.replace('userguide.png','userguide-mobile.png'));
    }
}

// ANZUAT-128
function firstLoginCheck(handleData) {

    return $.ajax({
        url : firstLoginCheckURL,
        dataType : "text",
        success : function(d) {
            newD = $.trim(d);

            if (newD == 1) {
                $("#firstLogin").val(1);
            }
        }
    });

}
