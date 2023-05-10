//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO


var orderDeliveryNeedRecalc=0;

function getDeliveryForm(obj){ /* deprecated */
	setOrderUserDataForm();
}
function setOrderUserDataForm()  {
	ajaxShowActivity();
	$('#calculate_delivery_button').hide();
	$('#submit_order_button').hide();
	$('#delivery_form').hide();
	$('#info_container').html('');
	if($('#payment_selector input').prop('type')=='hidden') pt_id = $('#payment_selector input[name=payment_type]').val();
	else pt_id = $('#payment_selector input[name=payment_type]:checked').val();
	if($('#delivery_selector input').prop('type')=='hidden') dt_id = $('#delivery_selector input[name=delivery_type]').val();
	else dt_id = $('#delivery_selector input[name=delivery_type]:checked').val();
	$.ajax({
		url : siteConfig['siteUrl']+'index.php',
		data:({type:'module',
			option:'ajax',
			module:'catalog',
			task:'renderUserDataForm',
			pt_id: pt_id, 
			dt_id: dt_id
		}),
		dataType:'json',
		success: function (data, textStatus) {
			$('#delivery_form').html(data.html);
			$('#delivery_form').show();
			calculateDelivery(1);
/*
			if(data.need_recalc==0){
				$('#submit_order_button').show();
			} else {
				$('#calculate_delivery_button').show();
			}
*/
			afterAjaxUpdate("delivery_form");
			ajaxHideActivity();
		},
		error: function () { ajaxHideActivity(); return false; }
	});
}
function setDeliveryList(){
	ajaxShowActivity();
	if($('#payment_selector input').prop('type')=='hidden') pt_id = $('#payment_selector input[name=payment_type]').val();
	else pt_id = $('#payment_selector input[name=payment_type]:checked').val();
	$.ajax({
		url : siteConfig['siteUrl']+'index.php',
		data:({type:'module',
			option:'ajax',
			module:'catalog',
			task:'renderDeliverySelector',
			psid:pt_id
		}),
		dataType:'html',
		success: function (data, textStatus) {
			$('#delivery_selector').html(data);
			$('#delivery_form').html("");
			ajaxHideActivity();
			setOrderUserDataForm();
		},
		error: function () { ajaxHideActivity(); return false; }
	});
}
function fillAddressPanel(obj) {
	ajaxShowActivity();
	$.ajax({
		url : siteConfig['siteUrl']+'index.php',
		data:({type:'module',
			option:'ajax',
			module:'catalog',
			task:'getAddress',
			psid:$(obj).val()
		}),
		dataType:'json',
		success: function (data, textStatus) {
			var AddrFields=['country_id', 'region_id','district_id','locality_id'];
			var country_eid='country_id';
			var region_eid='region_id';
			var district_eid='district_id';
			var locality_eid='locality_id';
			var ctrl_pref="";
			for(var key in data) {
				if(key==='use_as_default') { continue; }
				if ($('#'+key).prop('type')!=undefined) {
					switch($('#'+key).prop('type')){
						case 'checkbox':
							if (data[key]=='1') { $('#'+key).attr('checked','checked');	} 
							else { $('#'+key).attr('checked',''); }
							$('#'+key).change();
							break
						case 'select-one':
							if (AddrFields.indexOf(key)==-1) {
								$('#'+key).val(data[key]);
								$('#'+key).change();
							} else if(key==='country_id'){
								// it is address string
								$('#'+key).val(data[key]);
								ajaxShowActivity();
								$.ajax({
									url : siteConfig['siteUrl'] + "index.php",
									data : ({
										type : 'module',
										option : 'ajax',
										module : 'user',
										view : 'panel',
										task : 'getRegionSelector',
										ctrl_pref: ctrl_pref,
										psid: data[country_eid]
									}),
									dataType : 'html',
									success : function(data_r, textStatus) {
										ajaxShowActivity();
										is_required=$("#"+region_eid).hasClass('required');
										elem=$("#"+region_eid).parent();
										$("#"+region_eid).remove();
										elem.append(data_r);
										if (is_required){if (!$("#"+region_eid).hasClass('required'))$("#"+region_eid).addClass('required'); }
										$('#'+region_eid).val(data[region_eid]);
										$.ajax({
											url : siteConfig['siteUrl'] + "index.php",
											data : ({
												type : 'module',
												option : 'ajax',
												module : 'user',
												view : 'panel',
												task : 'getDistrictSelector',
												ctrl_pref: ctrl_pref,
												psid: data[region_eid]
											}),
											dataType : 'html',
											success : function(data_d, textStatus) {
												is_required=$("#"+district_eid).hasClass('required');
												elem=$("#"+district_eid).parent();
												$("#"+district_eid).remove();
												elem.append(data_d);
												if (is_required){if (!$("#"+district_eid).hasClass('required'))$("#"+district_eid).addClass('required'); }
												$('#'+district_eid).val(data[district_eid]);
												ajaxShowActivity();
												$.ajax({
													url : siteConfig['siteUrl'] + "index.php",
													data : ({
														type : 'module',
														option : 'ajax',
														module : 'user',
														view : 'panel',
														task : 'getLocalitySelector',
														ctrl_pref: ctrl_pref,
														psid: data[district_eid]
													}),
													dataType : 'html',
													success : function(data_l, textStatus) {
														is_required=$("#"+locality_eid).hasClass('required');
														elem=$("#"+locality_eid).parent();
														$("#"+locality_eid).remove();
														elem.append(data_l);
														if (is_required){if (!$("#"+locality_eid).hasClass('required'))$("#"+locality_eid).addClass('required'); }
														$('#'+locality_eid).val(data[locality_eid]);
														$('#'+locality_eid).change();
														ajaxHideActivity();
													},
													error : function() {
														alert('Error occured');
														ajaxHideActivity();
														return false;
													}
												});
											},
											error : function() {
												alert('Error occured');
												ajaxHideActivity();
												return false;
											}
										});
									},
									error : function() {
										alert('Error occured');
										ajaxHideActivity();
										return false;
									}
								});
							} 
							break
						default:
							$('#'+key).val(data[key]);
							break
					}
				}
			}
			ajaxHideActivity();
		},
		error: function () { ajaxHideActivity(); return false; }
	});
}
function toggleAddressPanel(obj,ehide){
	if ($(obj).val()>0) $('#'+ehide).hide();
	else $('#'+ehide).show();
}
function checkDeliveryForm(visible_only,fillManual) {
	if (form_checkfill('dt_selector',visible_only)){
		if (fillManual) fillManualInput('dt_selector');
		return true;
	} else {
		return false;
	}
}  
function setCustomGoodsFilter(filter_by, id, redirect){
	ajaxShowActivity();
	$.ajax({
		url : siteConfig['siteUrl']+'index.php',
		data:({type:'module',
			option:'ajax',
			module:'catalog',
			task:'setCustomFilter',
			filter_by:filter_by,
			psid:id
		}),
		dataType:'html',
		success: function (data, textStatus) {
			ajaxHideActivity();
			if (data=='OK'){
				window.location.href=redirect;
				return true;
			}
		},
		error: function () { ajaxHideActivity(); return false; }
	});
}
function resetCustomGoodsFilter(filter_by){
	ajaxShowActivity();
	$.ajax({
		url : siteConfig['siteUrl']+'index.php',
		data:({type:'module',
			option:'ajax',
			module:'catalog',
			task:'resetCustomFilter',
			filter_by:filter_by
		}),
		dataType:'html',
		success: function (data, textStatus) {
			if (data=='OK'){
				window.location.reload();
			}
		},
		error: function () { ajaxHideActivity(); return false; }
	});
}
function goodsComplectSetVisible(el,tab_id){
	if ($(el).val()==5) $('#gmtab_'+tab_id).show();
	else $('#gmtab_'+tab_id).hide();
}
function setMaskPriceSetting()
{
	var nab='{';	
	nab+='"parent_group":"'+$("#parent_group").val()+'",';
	nab+='"enabled_only":"'+$("#enabled_only").prop("checked").toString()+'",';
	nab+='"break_by_groups":"'+$("#break_by_groups").prop("checked").toString()+'",';
	nab+='"show_pack_price":"'+$("#show_pack_price").prop("checked").toString()+'",';
	nab+='"show_weight_price":"'+$("#show_weight_price").prop("checked").toString()+'",';
	nab+='"show_volume_price":"'+$("#show_volume_price").prop("checked").toString()+'",';
	nab+='"show_company_info":"'+$("#show_company_info").prop("checked").toString()+'",';
	nab+='"show_thumbs":"'+$("#show_thumbs").prop("checked").toString()+'",';
	nab+='"show_dimensions":"'+$("#show_dimensions").prop("checked").toString()+'",';
	nab+='"show_weight":"'+$("#show_weight").prop("checked").toString()+'"';
	nab+='}';
	return nab;
}
function savePriceSettings()
{
	$.ajax({
		url : siteConfig['siteUrl']+'index.php',
		type: "POST",
		data:({type:'module',
			option:'ajax',
			module:'catalog',
			task:'savePriceSettings',
			p_id:$("#p_id").val(),
			p_new:$("#p_new").prop("checked"),
			p_name:$("#p_name").val(),
			p_checkbox: setMaskPriceSetting(),
			p_discont:$("#price_discount").val(),
			p_price:$("#price_type").val(),
			p_comment:$("#priceset_comment").val(),
			p_head_colon:$("#p_head_colon").val(),
			p_foot_colon:$("#p_foot_colon").val(),
			p_comment:$("#p_comment").val(),
			p_template:$("#p_template").val()
		}),
		dataType:'html',
		success: function (data, textStatus) {
			alert(data);
		},
		error: function () { ajaxHideActivity(); return false; }
	});
}
function getPriceSettings(el){
	$.ajax({
		url : siteConfig['siteUrl']+'index.php',
		data:({type:'module',
			option:'ajax',
			module:'catalog',
			task:'getPriceSettings',
			p_id:$("#"+el).val()
		}),
		dataType:'json',
		success: function (data, textStatus) {
			if(data['p_checkbox']!=undefined)
			{
				var checkmas=JSON.parse(data['p_checkbox']);
				for (keyck in checkmas)
				{   					
					if(checkmas[keyck]=="true")
					{	
					$("#"+keyck).attr('checked', 'checked');					
					$("label[for='" + keyck + "']:first").addClass("CheckboxChecked");
					}
					else if(checkmas[keyck]=="false")
					{
						$("#"+keyck).removeAttr('checked');	
						$("label[for='" + keyck + "']:first").removeClass("CheckboxChecked");
					}	
					else{
						$("#"+keyck).val(checkmas[keyck]);
					}
				}	
			}
			for (key in data)
			{
			if(key=='p_checkbox') continue;
			$("#"+key).val(data[key]);
			};
		},
		error: function () { ajaxHideActivity(); return false; }
	});	
}
function calculateDelivery(on_load){
	ajaxShowActivity();
	$('#info_container').html('');
	$('#submit_order_button').hide();
	$('#calculate_delivery_button').show();
	var form_data = $("form#dt_selector").serializeArray();
	$.each(form_data, function(i, field){
		if(field.name=='view'){ form_data[i].value=''; }
		if(field.name=='layout'){ form_data[i].value=''; }
	});
	form_data[form_data.length] = { name: "module", value: "catalog" };
	form_data[form_data.length] = { name: "option", value: "ajax" };
	form_data[form_data.length] = { name: "task", value: "calculateDelivery" };
	form_data[form_data.length] = { name: "on_load", value: on_load };
	form_data[form_data.length] = { name: "order_results_weight", value: $('#order_results_weight').val() };
	form_data[form_data.length] = { name: "order_results_summa", value: $('#order_results_summa').val() };
	form_data[form_data.length] = { name: "order_results_summa_text", value: $('#order_results_summa_text').val() };
	form_data[form_data.length] = { name: "order_results_total", value: $('#order_results_total').val() };
	form_data[form_data.length] = { name: "order_results_total_text", value: $('#order_results_total_text').val() };
	form_data[form_data.length] = { name: "order_results_currency", value: $('#order_results_currency').val() };
	$.ajax({
		url : siteConfig['siteUrl']+'index.php',
		data: form_data,
		dataType:'json',
		success: function (data, textStatus) {
			if(data.is_error===0){
//				$('#info_container').html('');
				orderDeliveryNeedRecalc=data.need_recalc;
				if(data.delivery_sum>0){
					$('#info_container').append(data.delivery_text);
				}
				if(data.taxes_sum>0){
					$('#info_container').append(data.taxes_text);
				}
				if(data.total_sum>0){
					$('#info_container').append(data.total_text);
				}
				if(on_load==1){
					if(data.need_recalc==0){
						$('#submit_order_button').show();
						$('#calculate_delivery_button').hide();
					} else {
						$('#submit_order_button').hide();
						$('#calculate_delivery_button').show();
					}
				} else {
					$('#submit_order_button').show();
					$('#calculate_delivery_button').hide();
				}
//				$('#calculate_delivery_button').hide();
//				$('#submit_order_button').show();
			} else {
				$('#info_container').html(data.error_text);
				$('#submit_order_button').hide();
				$('#calculate_delivery_button').show();
			}
			ajaxHideActivity();
		},
		error: function () { 
			ajaxHideActivity(); 
			return false; 
		}
	});
}
function orderUserdataChanged(el){
	if(orderDeliveryNeedRecalc==1){
		$('#submit_order_button').hide();
		$('#calculate_delivery_button').show();
		$('#info_container').html('');
	}
}
function orderOnSubmit(){
	if (checkDeliveryForm(true,true)){
		$('form#dt_selector').submit();
	}
}
function addToFavourites(el, gid) {
	ajaxShowActivity();
	$.ajax({
		url: siteConfig['siteUrl']+'index.php',
		data:({type:'module',
			option:'ajax',
			module:'catalog',
			psid:gid,
			task:'addToFavourites',
			view:'goods'
		}),
		success: function(data) {
			$(el).parent(".favouritesButtons").removeClass('notInFavourites').addClass('inFavourites');
			ajaxHideActivity();
		},
		error: function () {
			ajaxHideActivity();
		}
	});
}
function removeFromFavourites(el, gid, remove_el, reload) {
	ajaxShowActivity();
	$.ajax({
		url: siteConfig['siteUrl']+'index.php',
		data:({type:'module',
			option:'ajax',
			module:'catalog',
			psid:gid,
			task:'removeFromFavourites',
			view:'goods'
		}),
		success: function(data) {
			$(el).parent(".favouritesButtons").removeClass('inFavourites').addClass('notInFavourites');
			if(remove_el != undefined) {
				$(remove_el).remove();
				if(reload != undefined && reload===1) location.reload();
			}
			applyAfterContentLoadHandlers('.catalog_list');
			ajaxHideActivity();
		},
		error: function () {
			ajaxHideActivity();
		}
	});
}
function addToCompare(el, gid) {
	ajaxShowActivity();
	$.ajax({
		url: siteConfig['siteUrl']+'index.php',
		data:({type:'module',
			option:'ajax',
			module:'catalog',
			psid:gid,
			task:'addToCompare',
			view:'goods'
		}),
		success: function(data) {
			$(el).parent(".compareButtons").removeClass('notInCompare').addClass('inCompare');
			ajaxHideActivity();
		},
		error: function () {
			ajaxHideActivity();
		}
	});
}
function removeFromCompare(el, gid, remove_el, reload) {
	ajaxShowActivity();
	$.ajax({
		url: siteConfig['siteUrl']+'index.php',
		data:({type:'module',
			option:'ajax',
			module:'catalog',
			psid:gid,
			task:'removeFromCompare',
			view:'goods'
		}),
		success: function(data) {
			$(el).parent(".compareButtons").removeClass('inCompare').addClass('notInCompare');
			if(remove_el != undefined) {
				$(remove_el).remove();
				if(reload != undefined && reload===1) location.reload();
			}
			ajaxHideActivity();
		},
		error: function () {
			ajaxHideActivity();
		}
	});
}
function toggleGoodsListMode(el) {
	if ($('.catalog_list .goods-list').hasClass('goods-list-list')) {
		if($(el).hasClass('switch-list')) return;
		$('.catalog_list .goods-list').removeClass('goods-list-list').addClass('goods-list-grid');
		$('.catalog_list .goods-list-mode-switcher .switch-list').removeClass('active');
		$(el).addClass('active');
		
		applyAfterContentLoadHandlers('.catalog_list');
		$.cookie('BARMAZ_goods_mode', '', { expires: 7, path: '/', domain: siteConfig['siteDomain'],	secure: siteConfig['siteSecure ']});
	} else {
		if($(el).hasClass('switch-grid')) return;
		$('.catalog_list .goods-list').removeClass('goods-list-grid').addClass('goods-list-list');
		$('.catalog_list .goods-list-mode-switcher .switch-grid').removeClass('active');
		$(el).addClass('active');
		
		$.cookie('BARMAZ_goods_mode', 'list', { expires: 7, path: '/',domain: siteConfig['siteDomain'],	secure: siteConfig['siteSecure ']  });
	}
	/*
	if ($('.catalog_list .goods-list').hasClass('goods-list-list')) {
		$('.catalog_list .goods-list').removeClass('goods-list-list');
		$('.catalog_list .goods-list-mode-switcher .switch-list').removeClass('active');
		$(el).addClass('active');
		applyAfterContentLoadHandlers('.catalog_list');
		$.cookie('BARMAZ_goods_mode', '', { expires: 7, path: '/',domain: siteConfig['siteDomain'],	secure: siteConfig['siteSecure ']  });
	} else {
		$('.catalog_list .goods-list').addClass('goods-list-list');
		$('.catalog_list .goods-list-mode-switcher .switch-grid').removeClass('active');
		$(el).addClass('active');
		
		$.cookie('BARMAZ_goods_mode', 'list', { expires: 7, path: '/',domain: siteConfig['siteDomain'],	secure: siteConfig['siteSecure ']  });
	}
	*/
}
function toggleParamElementDescripion(elm){
	$(".modify-wrapper .params-select .prm_descr_elem").hide();
	if($("#" + $(elm).val() + '_descr_elem').html() != '') $("#" + $(elm).val() + '_descr_elem').show();
}
$(document).ready(function() {
	appendDTPicker($(".option_row .dateselector"));
	appendDTPicker($(".option_row .datetimeselector"), true);
	appendDTPicker($(".option_row .timeselector"), true, false);
});
