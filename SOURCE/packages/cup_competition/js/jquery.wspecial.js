(function($){  
	$.fn.addLoadingMask = function() {
		/*
		var defaults = {   
		};  
		var options = $.extend(defaults, options);  
		*/
		return this.each(function() {  
			var obj = $(this);
			var check_attr = obj.attr('mask_ref');
			if(typeof check_attr === 'undefined' || check_attr === false){
				var offset = obj.offset();
				var width = obj.width();
				var height = obj.height();
				
				var maskObj = jQuery('<div></div>');
				var mask_id = new Date().getTime();
				mask_id = 'cup-mask-'+mask_id;
				maskObj.attr('id', mask_id);
				maskObj.attr('class', 'cup-mask');
				//maskObj.css({background:'#FFF000'});
				
				maskObj.css({'position':'absolute'});
				maskObj.width(width);
				maskObj.height(height);
				maskObj.offset(offset);
				//alert(mask_id);
				obj.attr('mask_ref', mask_id);
				
				
				
				
				var maskShade = jQuery('<div></div>');
				maskShade.width(width);
				maskShade.height(height);
				maskShade.css({background:'#FFFFFF', opacity: 0.5});
				
				maskObj.append(maskShade);
				
				var maskImage = jQuery('<div></div>');
				maskImage.width(width);
				maskImage.height(height);
				maskImage.attr('class', 'image-loading');
				maskImage.css({position:'absolute',top:0,left:0});
				
				maskObj.append(maskImage);
				
				jQuery('body').append(maskObj);
				
			}
		});  
	};  
})(jQuery);  

(function($){  
	$.fn.removeLoadingMask = function(options) {

		return this.each(function() {  
			var obj = $(this);
			var mask_ref = obj.attr('mask_ref');
			if(typeof mask_ref !== 'undefined' && mask_ref !== false){
				jQuery('.cup-mask#'+mask_ref).remove();
				obj.removeAttr('mask_ref');
			}
		});  
	};  
})(jQuery);  