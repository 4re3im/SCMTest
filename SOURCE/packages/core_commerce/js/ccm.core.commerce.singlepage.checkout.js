var ccmSinglePageCheckout = {	
	
	currentStepPath : '',
	
	init : function(path) {
		this.setCurrentStep(path);
		$('.ccm-core-commerce-checkout-singlepage h2').addClass('ccm-cart-step-disabled');
		$('.ccm-core-commerce-checkout-singlepage h2:first').removeClass('ccm-cart-step-disabled').addClass('ccm-cart-step-current');
		$('.ccm-core-commerce-checkout-singlepage form').submit(function() {
			ccmSinglePageCheckout.submitStep($(this));
			return false;
		});
		$('input[name=useBillingAddressForShipping], label[for=useBillingAddressForShipping]').click(function() {
			$(this).parent().next('table').toggle();
		});
		
		$('.ccm-core-commerce-checkout-singlepage h2').click(function() {
			if($(this).hasClass('ccm-cart-step-disabled')) { return false;}
		});
		
	},
	
	submitStep : function (formElem) {
		this.wipeError();
		formElem.ajaxSubmit({dataType:'json', success:function(res) {
			if(!res.success && res.error.length) {
				ccmSinglePageCheckout.displayError(res.error);
			}
			
			if(res.nextStep.length && res.success) {
				ccmSinglePageCheckout.toggleStep(res.nextStep);
			} else if (res.nextStep.length) { // not required, enable the next step
				ccmSinglePageCheckout.enableStep(res.nextStep);
			}
		}});	
		return false;
	},

	getCurrentStep : function () {
		return this.currentStepPath;
	}, 
	
	setCurrentStep : function (path) {
		this.currentStepPath = path;
	},
	
	enableStep : function (path) {
		elem = this.getStepElement(path);
		elem.prev('h2').removeClass('ccm-cart-step-disabled').addClass('ccm-cart-step-current');
	},
	
	getStepFormElement : function (path) {
		return $('#ccm-core-commerce-checkout-form-'+path+' form');
	},
	
	getStepElement : function (path) {
		return $('#ccm-core-commerce-checkout-form-'+path);
	},
	
	toggleStep : function (path) {
		elem = this.getStepElement(path);
		$('.ccm-core-commerce-checkout-singlepage h2').addClass('ccm-cart-step-disabled');
		elem.prev('h2').removeClass('ccm-cart-step-disabled').addClass('ccm-cart-step-current');
		$('.ccm-core-commerce-checkout-form').hide('fast');
		elem.show('fast');
	},
	
	displayError: function (error) {
		elem = this.getStepElement(this.getCurrentStep());
		elem.children('.ccm-error').append(error.toString()); 
	},
	
	wipeError : function () {
		elem = this.getStepElement(this.getCurrentStep());
		elem.children('.ccm-error').empty();
	}
};