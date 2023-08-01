var ccm_coreCommerceActiveProductField = '';
ccm_coreCommerceSetupSearch = function() {
	
	ccm_setupAdvancedSearch('core-commerce-product');

	$("#ccm-core-commerce-product-sets-search-wrapper select").chosen().unbind();
	$("#ccm-core-commerce-product-sets-search-wrapper select").chosen().change(function() {
		var sel = $("#ccm-core-commerce-product-sets-search-wrapper option:selected");
		$("#ccm-core-commerce-product-advanced-search").submit();
	});
	
	$("#ccm-core-commerce-product-list-cb-all").click(function() {
		if ($(this).attr('checked') == true) {
			$('td.ccm-core-commerce-product-list-cb input[type=checkbox]').attr('checked', true);
			$("#ccm-core-commerce-product-list-multiple-operations").attr('disabled', false);
		} else {
			$('td.ccm-core-commerce-product-list-cb input[type=checkbox]').attr('checked', false);
			$("#ccm-core-commerce-product-list-multiple-operations").attr('disabled', true);
		}
	});
	$("td.ccm-core-commerce-product-list-cb input[type=checkbox]").click(function(e) {
		if ($("td.ccm-core-commerce-product-list-cb input[type=checkbox]:checked").length > 0) {
			$("#ccm-core-commerce-product-list-multiple-operations").attr('disabled', false);
		} else {
			$("#ccm-core-commerce-product-list-multiple-operations").attr('disabled', true);
		}
	});

	$("div.ccm-product-search-set input[type=checkbox]").unbind();
	$("div.ccm-product-search-set input[type=checkbox]").click(function() {
		$("#ccm-core-commerce-product-advanced-search").submit();
	});


}

ccm_coreCommerceSetupOrderSearch = function() {
	ccm_setupAdvancedSearchFields('core-commerce-order');
	ccm_setupSortableColumnSelection();
}

ccm_coreCommerceLaunchProductSelector = function(selector) {
	ccm_coreCommerceActiveProductField = selector;
	ccm_coreCommerceLaunchProductManager();
}

ccm_coreCommerceLaunchProductManager = function() {
	$.fn.dialog.open({
		width: '90%',
		height: '70%',
		modal: false,
		href: ccm_coreCommerceProductManagerURL,
		title: 'Product Search'
	});
}

ccm_coreCommerceSelectProduct = function(productID) {
	ccm_coreCommerceTriggerSelectProduct(productID);
}

ccm_coreCommerceTriggerSelectProduct = function(productID, af) {
	if (af == null) {
		var af = ccm_coreCommerceActiveProductField;
	}
	//alert(af);
	var obj = $('#' + af + "-core-commerce-product-selected");
	var dobj = $('#' + af + "-core-commerce-product-display");
	dobj.hide();
	obj.show();
	obj.load(ccm_coreCommerceProductManagerSelectorDataURL + '?productID=' + productID + '&ccm_core_commerce_product_selected_field=' + af, function() {
		obj.click(function(e) {
			e.stopPropagation();
			ccm_coreCommerceActivateProductMenu($(this),e);
		});
		
	});
	var vobj = $('#' + af + "-core-commerce-product-value");
	vobj.attr('value', productID);
}

ccm_coreCommerceSelectProductMultiple = function(productID, productName) {
	if (af == null) {
		var af = ccm_coreCommerceActiveProductField;
	}
	$("#ccmCoreCommerceProductSelectorNone" + af).hide();
	var trow = '<tr><td><input type="hidden" name="' + af + '[]" value="' + productID + '" />' + productName + '</td><td><a href="javascript:void(0)" onclick="ccm_coreCommerceRemoveProductMultiple(this)"><img src="' + CCM_IMAGE_PATH + '/icons/remove_minus.png" width="16" height="16" /></a></td></tr>';
	var tbody = $("#ccmCoreCommerceProductSelector" + af + "_body");
	tbody.append(trow);
}

ccm_coreCommerceRemoveProductMultiple = function(obj) {
	var tbody = $(obj).parent().parent().parent();
	$(obj).parent().parent().remove();
	var cnt = tbody.find('tr').length;
	if (cnt == 1) {		
		tbody.find('tr').show();
	}	
}

ccm_coreCommerceActivateProductMenu = function(obj, e) {
	// Is this a file that's already been chosen that we're selecting?
	// If so, we need to offer the reset switch
	
	var selectedProduct = $(obj).find('div[ccm-core-commerce-product-manager-field]');
	var selector = '';
	if(selectedProduct.length > 0) {
		selector = selectedProduct.attr('ccm-core-commerce-product-manager-field');
	}
	ccm_hideMenus();
	
	var productID = $(obj).attr('productID');

	// now, check to see if this menu has been made
	var bobj = document.getElementById("ccm-core-commerce-product-menu" + productID + selector);
	
	if (!bobj) {
		// create the 1st instance of the menu
		el = document.createElement("DIV");
		el.id = "ccm-core-commerce-product-menu" + productID + selector;
		el.className = "ccm-menu";
		el.style.display = "none";
		document.body.appendChild(el);
		
		bobj = $("#ccm-core-commerce-product-menu" + productID + selector);
		bobj.css("position", "absolute");
		
		//contents  of menu
		var html = '<div class="popover"><div class="arrow"></div><div class="inner"><div class="content ccm-ui">';
		html += '<ul>';
		
		if (ccm_alLaunchType != 'DASHBOARD') {
			// if we're launching this at the selector level, that means we've already chosen a file, and this should instead launch the library
			var onclick = (selectedProduct.length > 0) ? 'ccm_coreCommerceLaunchProductSelector(\'' + selector + '\')' : 'ccm_triggerSelectFile(' + productID + ')';
			var chooseText = (selectedProduct.length > 0) ? 'Choose New Product' : 'Select';
			html += '<li><a class="ccm-menu-icon ccm-icon-edit-menu" id="menuSelectFile' + productID + '" href="javascript:void(0)" onclick="' + onclick + '">'+ chooseText + '<\/a><\/li>';
		}
		if (selectedProduct.length > 0) {
			html += '<li><a class="ccm-menu-icon ccm-icon-delete-menu" href="javascript:void(0)" id="ecMenuClearFile' + productID + selector + '">'+ ccmi18n.clear + '<\/a><\/li>';
		}
		
		html += '</ul>';
		html += '</div></div></div>';
		bobj.append(html);
		
		$("#ccm-core-commerce-product-menu" + productID + selector + " a.dialog-launch").dialog();
		
		$('a#ecMenuClearFile' + productID + selector).click(function(e) {
			ccm_coreCommerceClearProduct(e, selector);
			ccm_hideMenus();
		});

	} else {
		bobj = $("#ccm-core-commerce-product-menu" + productID + selector);
	}
	
	ccm_fadeInMenu(bobj, e);
}

ccm_coreCommerceClearProduct = function(e, af) {
	e.stopPropagation();
	var obj = $('#' + af + "-core-commerce-product-selected");
	var dobj = $('#' + af + "-core-commerce-product-display");
	var vobj = $('#' + af + "-core-commerce-product-value");
	vobj.attr('value', 0);
	obj.hide();
	dobj.show();
}