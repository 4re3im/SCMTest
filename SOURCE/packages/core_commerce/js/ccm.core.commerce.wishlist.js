/* used on wishlist-detail page */

var ccm_wishlist = {
		
	wishlistData : null,
		
	add : function(id, formaction) {
		this.wishlistData = $('#'+id).serialize();

		ccm_coreCommerceGetCartWindow().load(formaction).dialog({
			autoOpen: true,
				modal: true,
				height: 400,
				width: 480,
				dialogClass: 'ccm-core-commerce-cart-dialog',
				title: 'Wishlist'
		});
		return false;	
		
	}, 
	
	submitToList : function(action) {
		$.ajax({
			type: 'POST',
			url: action,
			data : this.wishlistData,
			dataType: 'json',
			success: function(resp) {
				$('#ccm-wishlist-dialog-content .ccm-message').show();
				if(resp.error) {
					$('#ccm-wishlist-dialog-content .ccm-message').html(resp.message);
				} else {
					// a bit of delay hackery
					$('#ccm-wishlist-empty').hide(1500, function() {
						$('.ccm-dialog-close').trigger('click');
					});
				}
			}
		});
	}

};




ccm_coreCommerceCreateWishlist = function(formaction) {
	jQuery.fn.dialog.open({
		width: 300,
		height: 100,
		modal: false,
		href: formaction, 
		title: ''
	});
};

ccm_coreCommerceShareWishlist = function(formaction) {
	jQuery.fn.dialog.open({
		width: 300,
		height: 100,
		modal: false,
		href: formaction, 
		title: ''
	});
};

ccm_coreCommerceRegisterAddToWishList = function(id, formaction) {
	if (ccm_coreCommerceUseAdvancedCart) {
		$('#'+id).find('.ccm-core-commerce-add-to-wishlist-button').click(function() {
			ccm_wishlist.add(id, formaction);
		});
		return false;
	} else {
		return true;
	}
};

ccm_coreCommerceRegisterAddToRegistry = function(id, formaction) {
	if (ccm_coreCommerceUseAdvancedCart) {
		$('#'+id).find('.ccm-core-commerce-add-to-registry-button').click(function() {
			ccm_wishlist.add(id, formaction);
		});
		return false;
	} else {
		return true;
	}
};