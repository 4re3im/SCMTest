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


// ADD CLASS DISPLAY
$('.open').click(function() {
   $('#activated').addClass('in');
});
$('.open').click(function() {
   $('#activated-icon').addClass('in');
});
$('.open').click(function() {
   $('.open').addClass('disabled');
});
$('.open').click(function() {
   $('.field-remove').removeClass('in');
});


// DELAYED LINK
function delay (URL) {
    setTimeout( function() { window.location = URL }, 1500 );
}
