$(document).ready(function(){
	var count = 1;
	setInterval(function(){
		if(count == 1) {
			location.replace($('#requestUrl').attr('href'));
		}
		count++;
	},2500);
});
