

function cup_global_simple_field_focusout(){
	var simple_input_dom = jQuery('.cup-main-sidebar input[name="simple_search_query"]');
	if(jQuery.trim(simple_input_dom.val()).length < 1){
		simple_input_dom.val('Search');
		simple_input_dom.addClass('empty');
	}else{
		simple_input_dom.removeClass('empty');
	}
}

function cup_global_simple_field_focusin(){
	var simple_input_dom = jQuery('.cup-main-sidebar input[name="simple_search_query"]');
	if(simple_input_dom.hasClass('empty')){
		simple_input_dom.val('');
		simple_input_dom.removeClass('empty')
	}
}

jQuery(document).ready(function() {
	cup_global_simple_field_focusout();
	jQuery('.cup-main-sidebar input[name="simple_search_query"]').focusout(cup_global_simple_field_focusout);
	jQuery('.cup-main-sidebar input[name="simple_search_query"]').focusin(cup_global_simple_field_focusin);
});
