// SMOOTH SCROLL
$(function() {
   $('a[href*="#"]:not([href="#"])').click(function() {
      if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
         var target = $(this.hash);
         target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
         if (target.length) {
            $('html, body').animate({
               scrollTop: target.offset().top
            }, 1000);
            return false;
         }
      }
   });
});


// BACK TO TOP
jQuery(document).ready(function($) {
   // browser window scroll (in pixels) after which the "back to top" link is shown
   var offset = 300,
      //browser window scroll (in pixels) after which the "back to top" link opacity is reduced
      offset_opacity = 1200,
      //duration of the top scrolling animation (in ms)
      scroll_top_duration = 700,
      //grab the "back to top" link
      $back_to_top = $('.cd-top');

   //hide or show the "back to top" link
   $(window).scroll(function() {
      ($(this).scrollTop() > offset) ? $back_to_top.addClass('cd-is-visible'): $back_to_top.removeClass('cd-is-visible cd-fade-out');
      if ($(this).scrollTop() > offset_opacity) {
         $back_to_top.addClass('cd-fade-out');
      }
   });

   //smooth scroll to top
   $back_to_top.on('click', function(event) {
      event.preventDefault();
      $('body,html').animate({
         scrollTop: 0,
      }, scroll_top_duration);
   });

});


// ADD CLASS DISPLAY
$('.open').click(function() {
   $('#displayAlert').addClass('in'); // shows alert with Bootstrap CSS3 implem
});

$('.close').click(function() {
   $(this).parent().removeClass('in'); // hides alert with Bootstrap CSS3 implem
});


// MODAL 1
$(".modal-transparent").on('show.bs.modal', function() {
   setTimeout(function() {
      $(".modal-backdrop").addClass("modal-backdrop-transparent");
   }, 0);
});
$(".modal-transparent").on('hidden.bs.modal', function() {
   $(".modal-backdrop").addClass("modal-backdrop-transparent");
});

$(".modal-fullscreen").on('show.bs.modal', function() {
   setTimeout(function() {
      $(".modal-backdrop").addClass("modal-backdrop-fullscreen");
   }, 0);
});
$(".modal-fullscreen").on('hidden.bs.modal', function() {
   $(".modal-backdrop").addClass("modal-backdrop-fullscreen");
});

function understood(element)
{
    $('.cookie').fadeOut();
    $.cookie('understand_cookie', 1);
}

$(document).ready(function(){
   if ($.cookie('understand_cookie') == 1) {
         $('.cookie').hide();
   }
   $('#footer .icon-facebook')
    .attr('href', 'https://www.facebook.com/CambridgeUniversityPressEducationAustralia?ref=hl')
    .attr('target', '_blank');
   $('#footer .icon-twitter')
      .attr('href', 'https://twitter.com/Cambridge_AusEd')
      .attr('target', '_blank');
   $('#footer .icon-youtube')
      .attr('href', 'https://www.youtube.com/user/CUPANZEducation')
      .attr('target', '_blank');
   $('#footer .icon-googlePlus')
      .attr('href', 'https://plus.google.com/107441058984269920723/posts')
      .attr('target', '_blank');
});

// SB-579 added by mabrigos 20200709
$('#contactForm').on('submit', function(e){
   e.preventDefault();
   if ($('#contactMail').val() != $('#contactConfirm').val()) {
       alert('Your email did not match. Please make sure that your email is correct');
   }

   if ($('#contactName').val() != "" && $('#contactLastName').val() != "" &&
       $('#contactMail').val() != "" && $('#contactConfirm').val() != ""
       && $('#contactConfirm').val() != "") {
       $.ajax({
           url : "/queenslandsenior/sendEmail/",
           data : $(this).serialize(),
           type : "POST",
           success : function(response) {
               alert("Your message has been sent.");
               document.getElementById("contactForm").reset();
           },
           error : function(xhr,status,error) {
               alert("We cannot send your message right now.");
               document.getElementById("contactForm").reset();
           }
       });
   } else {
       alert("Please complete the form fields.");
   }
});
