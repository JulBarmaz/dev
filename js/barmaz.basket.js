//BARMAZ_COPYRIGHT_TEMPLATE

var basketWidgetID=new Array();

function addToBasketSingle(gid) {
	addToBasket(gid, true);
}
function addToBasket(gid, redirect_on_success) {
	var fn = window['getOptionsForBasket']; 
	if(typeof fn === 'function') {
		var fd=getOptionsForBasket(gid);
	} else {
		var fd = new FormData;
	}
	if(typeof $('#quant_' + gid).val() === 'undefined') {
		return false;
	}
	if($('#quant_' + gid).val() == 0){
		return false;
	}
	fd.append('type', 'module');
	fd.append('option', 'ajax');
	fd.append('module', 'catalog');
	fd.append('task', 'addBasketPosition');
	fd.append('view', 'goods');
	fd.append('psid', gid);
	fd.append('quantity', $('#quant_' + gid).val());
	ajaxShowActivity();
	$.ajax({
		type: 'POST',
		dataType: 'json',
		processData: false,
		contentType: false,
		url: siteConfig['siteUrl']+'index.php',
		data:fd,
		success: function(data) {
			if(data.result=="OK") updateMiniBasket();
			if(data.redirect!='' && redirect_on_success==true) document.location.href=data.redirect;
			else alert(data.message);
			ajaxHideActivity();
		},
		error: function () {
			ajaxHideActivity();
		}
	});
}
function deleteBasketPosition(gid, basketdiv) {
	ajaxShowActivity();
	$.ajax({
		url: siteConfig['siteUrl']+'index.php',
		data:({type:'module',
			option:'ajax',
			module:'catalog',
			psid:gid,
			task:'deleteBasketPosition',
			view:'goods'
		}),
		success: function(data) {
			basket_parent=$('#'+basketdiv).parent();
			$('#'+basketdiv).remove();
			basket_parent.append(data);
			ajaxHideActivity();
			updateMiniBasket();
		},
		error: function () {
			ajaxHideActivity();
		}
	});
}
function updateBasketPosition(gid, quantity_el, basketdiv) {
	var g_quantity=$('#'+basketdiv+' #'+quantity_el).val();
	ajaxShowActivity();
	$.ajax({
		url: siteConfig['siteUrl']+'index.php',
		data:({type:'module',
			option:'ajax',
			module:'catalog',
			psid:gid,
			task:'updateBasketPosition',
			view:'goods',
			quantity: g_quantity
		}),
		success: function(data) {
			basket_parent=$('#'+basketdiv).parent();
			$('#'+basketdiv).remove();
			ajaxHideActivity();
			basket_parent.html(data);
			afterAjaxUpdate("mybasket_ajax");
			updateMiniBasket();
		},
		error: function () {
			ajaxHideActivity();
		}
	});
}
function changeOrderStep(step) {
	$('#step').val(step);
}
function updateMiniBasket(){
	if(typeof(basketWidgetID)==="undefined") return;
	// ajaxShowActivity();
	$.ajax({
		url: siteConfig['siteUrl']+'index.php',
		data:({type:'module',
			option:'ajax',
			module:'catalog',
			task:'getMiniBasket',
			view:'goods'
		}),
		success: function(data) {
			$.each(basketWidgetID, function(index, value){
				$('#'+value).html(data);
				afterAjaxUpdate(value);
			});
			// ajaxHideActivity();
		},
		error: function () {
			// ajaxHideActivity();
		}
	});
}
