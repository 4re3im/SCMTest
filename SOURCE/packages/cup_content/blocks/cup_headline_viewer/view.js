jQuery(document).ready(function(){
	jQuery('.cup-headline-viewer-frame').each(function(){
		var interval = 5;
		if(jQuery(this).attr('config_interval') !== undefined){
			interval = jQuery(this).attr('config_interval');
		}
		
		var length = jQuery(this).find('.cup-headline-item').length;
		var items = jQuery(this).find('.cup-headline-item');
		items.hide();
		
		var idx = 0;
		var timer = false;
		var showNext = function(){
			items.eq(idx).fadeOut(300);
			idx++;
			if(idx >= length){
				idx = 0
			}
			items.eq(idx).delay(300).fadeIn(300);
		};
		
		if(length > 0){
			items.eq(idx).show('slow');
			timer = setInterval(showNext, interval*1000);
		}
	});
});