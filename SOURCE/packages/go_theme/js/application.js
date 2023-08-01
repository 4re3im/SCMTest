// JavaScript Document for Presence
$(document).ready(function() {
	$("<select />").appendTo(".select_nav");
		$("<option />", {
		"selected": "selected",
		"value"   : "",
		"text"    : "Menu"
		}).appendTo(".select_nav select");
		// Populate dropdown with nav items
		$(".navbar .nav li a").each(function() {
		var el = $(this);
		$("<option />", {
		"value"   : el.attr("href"),
		"text"    : el.text()
		}).appendTo(".select_nav select");
		});
		// To make dropdown actually work
		// To make more unobtrusive: http://css-tricks.com/4064-unobtrusive-page-changer/
		$(".select_nav select").each(function(){
			$(this).change(function() {
				window.location = $(this).find("option:selected").val();
			});
		});
});

function resizeStuff() {
	var bar = $('.navbar-inner .logo a').height();
	$('.navbar .nav li a').css('line-height',bar + 'px');
	$('.navbar .nav .dropdown-toggle .caret').css('margin-top',bar/2 + 'px');
	$('.btn-navbar').css('margin-top', bar/2 + 'px');
	if($('#myCarousel').length > 0){
		var diff = $('#myCarousel').offset().top;
		console.log(diff);
		diff *= -1;
		var orig = $('.navbar-wrapper').css('margin-bottom').replace(/[^-\d\.]/g, '');
		$('.navbar-wrapper').css('margin-bottom',(parseInt(orig) + parseInt(diff)) + 'px');
		
	}
}

$(window).load(function(){
	resizeStuff();
	var TO = false;
	
	$(window).resize(function(){
		if(TO !== false)
		clearTimeout(TO);
		TO = setTimeout(resizeStuff, 200); //200 is time in miliseconds
	});
});


function mainmenu(){
	//$(" .navbar .nav ul").css({display: "none"}); // Opera Fix
	$(" .navbar li").hover(
		function(){
			//clearInterval(myVar);
			$(this).find('ul:first').css({visibility: "visible",display: "none"}).slideDown(400);
		},
		function(){
			$(this).find('ul:first').css({visibility: "visible",display: "block"}).slideUp(600);	
		}
	);

}



$(document).ready(function(){
	mainmenu();
	
});
