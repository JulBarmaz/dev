//BARMAZ_COPYRIGHT_TEMPLATE

var RE_NUM = /^\-?\d+$/;
var ajaxQueryFinished = true;
var formCounter = 0;
var equalizedHeightElements=new Array();
var equalizedHeightElementsCounter=0;
var defaultGetContListTask='getContList';
var afterContentLoadHandlers=new Array();
$(document).ready(function() {
	if ($('#sys_msg') && $('#sys_msg').length > 0 && $('#sys_msg').css("display") == 'block') {
		$('div.prime').height($(document).height());
		setTimeout("close_sysmes()", 5000);
	}
	setMobileClass();
	afterContentLoad(false);
	$(document).unbind('keydown.fb');
	acrmStatUpdate();
	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		var activeTab = $(e.target).attr("data-key") // activated tab
		if(activeTab!='') $("#activeTab").val(activeTab);
	});
});
function isMobile(){
	if( (/(ipad|iphone|android|mobile|touch|blackberry|iemobile)/i.test(navigator.userAgent)) ){
		return true;
	} else {
		return false;
	}
} 
function setMobileClass(){
	if( isMobile() ){
		$('html').removeClass('is-not-mobile').addClass('is-mobile');
	} else {
		$('html').removeClass('is-mobile').addClass('is-not-mobile');
	}
} 
function checkBrowserIsIE7() { 
	return ((navigator.userAgent.indexOf("MSIE 7") > -1) && (navigator.userAgent.indexOf("Opera") == -1)); 
}
function checkBrowserIsIE8() { 
	return ((navigator.userAgent.indexOf("MSIE 8") > -1) && (navigator.userAgent.indexOf("Opera") == -1)); 
}
function checkBrowserIsIE() { 
	return ( checkBrowserIsIE7()|| checkBrowserIsIE8());
}
function applyHTML5Required(parent_prefix){
	if(parent_prefix) _current_prefix_ws=parent_prefix + " "; else _current_prefix_ws="";
	if (!(elementSupportsAttribute('input','required') && elementSupportsAttribute('input','placeholder') && elementSupportsAttribute('textarea','placeholder'))) {
		$(_current_prefix_ws + 'form').validatr(); 
	}
}
function applyJSFormats(parent_prefix){
	if(parent_prefix){
		_current_prefix_ws=parent_prefix + " ";
		$(_current_prefix_ws + 'input.numeric').numeric();
		$(_current_prefix_ws + 'input.decimal').numeric({decimal : "."});
		$(_current_prefix_ws + 'input.timeselector').each(function(){ appendDTPicker($(this), true, false); });
		$(_current_prefix_ws + 'input.dateselector').each(function(){ appendDTPicker($(this)); });
		$(_current_prefix_ws + 'input.datetimeselector').each(function(){ appendDTPicker($(this),true); });
		if (typeof templateEveryLoad == 'function') templateEveryLoad(parent_prefix); 
		applyAfterContentLoadHandlers(parent_prefix); 
	}
}
function elementSupportsAttribute(element,attribute) {
	var test = document.createElement(element);
	return test.hasAttribute(attribute);
	/*
	if (attribute in test) {
		if ($.browser.safari && !/Chrome/.test(navigator.appVersion))  return false;
		else return true;
	} else {
		return false;
	}
	*/
}
function checkCookies(quiet_check) {
	if(quiet_check==undefined) quiet_check=0; else quiet_check=1;
	$.cookie('BARMAZ', 'dfgdhdfhdfghfdhdffhdfhdf', {expires:1, path: '/' ,domain: siteConfig['siteDomain'],	secure: siteConfig['siteSecure ']});
	if ($.cookie('BARMAZ')!='dfgdhdfhdfghfdhdffhdfhdf') {
		// Failed
		if (quiet_check==0) alert('Enable cookies first !!!');
		$.cookie('BARMAZ', null, {expires: -1, path: '/' ,domain: siteConfig['siteDomain'],	secure: siteConfig['siteSecure ']});
		return 0;
	} else {
		// OK
		$.cookie('BARMAZ', null, {expires: -1, path: '/' ,domain: siteConfig['siteDomain'],	secure: siteConfig['siteSecure ']});
		return 1;
	}
}

function updateAddressPanel(id, module){
	if(module==undefined) module= 'user';
	// тут готовим что отправить
	$new_text=getManualInputText(false,'pa_');
	$.fancybox.close();
	ajaxShowActivity();
	$.ajax({
		url : siteConfig['siteUrl']+"index.php",
		data:({
			type:'module',
			option:'ajax',
			module:module,
			task:'checkAddressPanelData',
			pa_country_id: $('#pa_country_id').val(),
			pa_region_id: $('#pa_region_id').val(),
			pa_district_id: $('#pa_district_id').val(),
			pa_locality_id: $('#pa_locality_id').val(),
			pa_country: $('#pa_country').val(),
			pa_region: $('#pa_region').val(),
			pa_district: $('#pa_district').val(),
			pa_locality: $('#pa_locality').val(),
			pa_zipcode: $('#pa_zipcode').val(),
			pa_street: $('#pa_street').val(),
			pa_house: $('#pa_house').val(),
			pa_apartment: $('#pa_apartment').val(),
			pa_fullinfo: $new_text
		}),
		dataType:'html',
		success: function (html_data, textStatus) {
			$('#'+id).val(html_data);
			$('#'+id).next().text($new_text);
			ajaxHideActivity();
		},
		error: function () { ajaxHideActivity(); return false; }
	});
}
function showAddressEditor(id, module, title){
	if(module==undefined) return false;
	if(module=='') return false;
	if(title==undefined) title= '';
	var data=$('#'+id).val();
	ajaxShowActivity();
	$.ajax({
		url : siteConfig['siteUrl']+"index.php",
		data:({
			type:'module',
			option:'ajax',
			module:module,
			id : id,
			data:data,
			task:'showAddressEditor'
		}),
		dataType:'html',
		success: function (html_data, textStatus) {
			$.fancybox({
				'title'		: title,
				'content'	: html_data,
				'onComplete': function() {
					ajaxHideActivity();
					afterAjaxUpdate("fancybox-content");
				}
			});
			$.fancybox.resize();
		},
		error: function () { ajaxHideActivity(); return false; }
	});
}
function showFilter(current_mod, view, layout, multy_code, title, trash_flag, controller) {
	if(controller==undefined || controller=='default') controller='';
	if(layout==undefined) layout='default';
	if(title==undefined) title= '';
	if(multy_code==undefined) multy_code=0;
	if(trash_flag==undefined) trash_flag= 0;
	ajaxShowActivity();
	$.ajax({
		url : siteConfig['siteUrl']+"index.php",
		data:({type:'module',
			option:'ajax',
			module:current_mod,
			controller: controller,
			task:'showfilter',
			view:view,
			multy_code:multy_code,
			layout:layout,
			trash: trash_flag
		}),
		dataType:'html',
		success: function (data, textStatus) {
			$.fancybox({
				'title'		: title,
				'content':data,
				'onComplete': function() {
					ajaxHideActivity();
					$("#fancybox-content .timeselector").each(function(){ 	appendDTPicker($(this), true, false); });
					$('#fancybox-content .datetimepicker').each(function() { 	appendDTPicker($(this), true); });
					$('#fancybox-content .datepicker').each(function() { 	appendDTPicker($(this)); });
					afterAjaxUpdate("fancybox-content");
				}
			});
			$.fancybox.resize();
		},
		error: function () { 
			ajaxHideActivity(); return false; 
		}
	});
}

function intval( mixed_var, base ) {
    var tmp;
    if( typeof( mixed_var ) == 'string' ){
        tmp = parseInt(mixed_var);
        if(isNaN(tmp)){
            return 0;
        } else{
            return tmp.toString(base || 10);
        }
    } else if( typeof( mixed_var ) == 'number' ){
        return Math.floor(mixed_var);
    } else{
        return 0;
    }
}
//проверяет значение на количество знаков и состав (только числа)
function validate_numeric(__in_val, __digits) {
	var i = __in_val.value;
	if (i.length != __digits)
		return false;
	if (!RE_NUM.exec(i))
		return false;
	return true;
}

// проверка на заполнение поля числом 
function check_numeric(id_fld) {
	if($('#'+id_fld).val() != "") {
	    var value = $('#'+id_fld).val().replace(/^\s\s*/, '').replace(/\s\s*$/, '');
	    var intRegex = /^\d+$/;
	    if(!intRegex.test(value)) {
	         return false;
	    }
	} else { return  false; }
  return true;			
}
function VtoInt(perem){
	var a=parseInt(perem);
	if(isNaN(a))return 0;
	else return a;
}
function VtoFloat(txt, digits){
	if( typeof( digits ) == 'undefined' ) digits=0;
	var perem = new String(txt);
	//	perem=str_replace_reg(perem,',','.');
	perem=str_replace(perem,',','.');
	numObj = Number(perem);
	res = numObj.toFixed(digits);
	if(isNaN(numObj))return 0;
	else return parseFloat(res);
}
function formatFloat(txt, digits){
	if( typeof( digits ) == 'undefined' ) digits=0;
	var perem = new String(txt);
	//	perem=str_replace_reg(perem,',','.');
	perem=str_replace(perem,',','.');
	numObj = Number(perem);
	res = numObj.toFixed(digits);
	if(isNaN(numObj))return 0;
	else return res.replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1'+siteConfig['thousandSeparator']);
}

/** замена спец символа - аналог html_special_chars **/
function txt2html(xStr) {
	var htmlarr = new Array();
	htmlarr["&"] = "&amp;";
	htmlarr["\n"] = "<br />";
	htmlarr["<"] = "&lt;";
	htmlarr[">"] = "&gt;";
	htmlarr["\""] = "&#34;";
	htmlarr["\'"] = "&#39;";
	// htmlarr[" "]="&nbsp;";
	for (x in htmlarr)
		xStr = xStr.replace(x, htmlarr[x]);
	return xStr;
}
function str_replace_reg(haystack, needle, replacement) {
	var r = new RegExp(needle, 'g');
	return haystack.replace(r, replacement);
}
function str_replace(haystack, needle, replacement) {
	var temp = haystack.split(needle);
	return temp.join(replacement);
}
function array_key_exists ( key, search ) {
	if( !search || (search.constructor !== Array && search.constructor !== Object) ) return false;
	return search[key] !== undefined;
}
function is_empty( mixed_var ) {
	return ( $.trim(mixed_var) === "" || mixed_var === 0   || mixed_var === "0" || mixed_var === null  || mixed_var === false  ||  ( is_array(mixed_var) && mixed_var.length === 0 ) );
}
function is_array( mixed_var ) {return ( mixed_var instanceof Array );}

function close_sysmes() {
	if ($('#sys_msg') && $('#sys_msg').length > 0){
		$('#sys_msg').hide('slow');
		if ($('#prime_1') && $('#prime_1').length > 0) $('#prime_1').hide();
	}
//	$('#sys_msg').hide('slow');
//	$('#prime_1').hide();
}

function switchSortLink(module,view,layout,trash,sort,orderby,page,lol,multycode,spravid,task,controller) {
	if (spravid===undefined) return false;
	if (task===undefined) task=defaultGetContListTask;

	if (controller===undefined) controller="default";	
	ajaxShowActivity();
	$.ajax({
		url : "index.php",
				data:({type:'module',
					   option:'ajax',
					   module:module,
					   psid:multycode,
					   task:task,
					   view:view,
					   trash:trash,
					   sort:sort,
					   orderby:orderby,
					   page:page,
					   layout:layout,
					   lol:lol,
					   controller:controller
					   }),
		dataType:'html',
		success : function(data, textStatus) {
			sprav_parent=$('#'+spravid).parent();
			$('#'+spravid).remove();
			sprav_parent.append(data);
			afterAjaxUpdate(spravid);
			ajaxHideActivity();
		},
		error : function() { ajaxHideActivity(); return false; }
	});
}
// For selector search form
function handleAjaxFilterInput(e,field_name,module,view,layout,psid,controller,trash,sort,orderby,page,lol,spravid,task) {
	if(e.keyCode === 13) {
		alterAjaxFilter(field_name,module,view,layout,psid,controller,trash,sort,orderby,page,lol,spravid,task)
	}
}
//For selector search form
function alterAjaxFilterReset() {
	$('.sprav-list-selector').find('.filter-selector-apply').get(0).click()
}
//For selector search form
function alterAjaxFilter(field_name,module,view,layout,psid,controller,trash,sort,orderby,page,lol,spravid,task) {
	if (spravid===undefined) return false;
	if (task===undefined) task=defaultGetContListTask;
	if (controller===undefined) controller="default";
	
	var field_val=$('#filter_'+field_name).val();
	$.ajax({
		url : siteConfig['siteUrl']+"index.php",
		data:({type:'module',
			option:'ajax',
			module:module,
			task:'setfilter',
			view:view,
			filter_string:field_val,
			filter_name:field_name,
			layout:layout,
			controller:controller
		}),
		dataType:'html',
		success: function (data, textStatus) {
			reloadContList(module,view,layout,psid,controller,0,sort,orderby,page,lol,spravid,task);
		},
		error: function () { ajaxHideActivity(); return false; }
	});
}
function reloadContList(module,view,layout,psid,controller,trash,sort,orderby,page,lol,spravid,task) {
	ajaxShowActivity();
	$.ajax({
		url : "index.php",
		data:({type:'module',
			option:'ajax',
			module:module,
			view:view,
			layout:layout,
			task:task,
			psid:psid,
			trash:trash,
			page:page,
			sort:sort,
			orderby:orderby,
			lol:lol,
			controller:controller
		}),
		dataType:'html',
		success : function(data, textStatus) {
			sprav_parent=$('#'+spravid).parent();
			$('#'+spravid).remove();
			sprav_parent.append(data);
			afterAjaxUpdate(spravid);
			ajaxHideActivity();
		},
		error : function() {
			alert('Does not contain the data.');
			ajaxHideActivity();
			return false;
		}
	});
}
function resetSpravFilterAll(obj, module, view, layout, multy_code, psid, orderby, sort, page, controller, trash){
	ajaxShowActivity();
	$.ajax({
		url: siteConfig['siteUrl'] + 'index.php',
		data: ({
			type: 'module',
			option: 'ajax',
			full_reset: 1,
			module:module,
			view:view,
			controller:controller,
			layout:layout,
			multy_code:multy_code,
			psid:psid,
			trash:trash,
			page:page,
			sort:sort,
			orderby:orderby,
			task: 'resetfilter'
		}),
		dataType: 'json',
		success: function(data, textStatus) {
			if (data.result=='OK'){
//				console.dir(data);
				window.location.href=data.href;
			} else {
				console.dir(data.result);
				alert('Error resetting filter');
				ajaxHideActivity();
			}
		},
		error: function(qq) {
			ajaxHideActivity();
			return false;
		}
	});
}
function resetSpravFilterKey(obj, module, view, layout, multy_code, psid, orderby, sort, page, controller, trash){
	if(obj) {
		ajaxShowActivity();
		var key_val=$(obj).attr('data-key');
		$.ajax({
			url : siteConfig['siteUrl']+'index.php',
			data:({type:'module',
				option:'ajax',
				module:module,
				view:view,
				controller:controller,
				layout:layout,
				multy_code:multy_code,
				psid:psid,
				trash:trash,
				page:page,
				sort:sort,
				orderby:orderby,
				task:'resetFilterKey',
				key_val:key_val
			}),
			dataType:'json',
			success: function (data, textStatus) {
				if (data.result=='OK'){
//					console.log(data.href);
					window.location.href=data.href;
				} else {
					alert('Error resetting filter');
					ajaxHideActivity();
				}
			},
			error: function () { ajaxHideActivity(); return false; }
		});
	}
}
function setContList(obj, psid) {
	if(obj) {
		obj_parent_wrapper=$(obj).parents('.tree-panel').get(0);
		if(obj_parent_wrapper===undefined) return false;
		spravid_holder=$(obj_parent_wrapper).children('input[type=hidden].sprav_list_id').get(0);
		if(spravid_holder===undefined) return false;
		spravid=$(spravid_holder).val();
		if (spravid===undefined || spravid==='') return false;
		var module=$('#module').val();
		var view=$('#view').val();
		var sort=$('#sort').val();
		var orderby=$('#orderby').val();
		var page=$('#page').val();
		var layout=$('#layout').val();
		var controller=$('#controller').val();

		ajaxShowActivity();
		$.ajax({
			url : "index.php",
			data:({type:'module',
				   option:'ajax',
				   module:module,
				   view:view,
				   controller:controller,
				   layout:layout,
				   task:'showData',
				   multy_code:psid,
				   trash:0,
				   page:page,
				   sort:sort,
				   orderby:orderby
				   }),
			dataType:'html',
			success : function(data, textStatus) {
				  sprav_parent=$('#'+spravid).parent();
				  $('#'+spravid).remove();
				  sprav_parent.append(data);
				  $(obj_parent_wrapper).find('li').removeClass("active");
				  $($(obj).parent().get(0)).addClass("active");
				  afterAjaxUpdate(spravid);
				  ajaxHideActivity();
			},
			error : function() {
				alert('Does not contain the data.');
				ajaxHideActivity();	return false;
			}
		});
	}
}
function switchPageOnContList(module,view,layout,psid,controller,trash,sort,orderby,page,lol,spravid,task) {
	if (spravid===undefined) return false;
	if (task===undefined) task=defaultGetContListTask;
	if (controller == undefined) controller='';

	ajaxShowActivity();
	$.ajax({
		url : "index.php",
		data:({type:'module',
			   option:'ajax',
			   module:module,
			   controller:controller,
			   psid:psid,
			   task:task,
			   view:view,
			   trash:trash,
			   sort:sort,
			   orderby:orderby,
			   page:page,
			   layout:layout,
			   lol:lol
			   }),
		dataType:'html',
		success : function(data, textStatus) {
			sprav_parent=$('#'+spravid).parent();
			$('#'+spravid).remove();
			sprav_parent.append(data);
			afterAjaxUpdate(spravid);
			ajaxHideActivity();
		},
		error : function() {
			alert('Does not contain the data.');
			ajaxHideActivity();	return false;
		}
	});
}
function clickACRM(bid){
	$.ajax( {
		url : 'index.php?module=acrm&task=clickACRM&psid=' + bid,
		success : function() { return true; },
		error : function() { return false; }
	});
}
function deleteMultyRow(id,mess) {
	if(typeof(mess)=="undefined") mess='Delete';
	title=$('#'+id).parent().children('label[for='+id+']').html();
	if (confirm(mess+' : '+title+' ?')) $('#'+id).parent().remove();
}
function addFromSelector(el_from, element, id, title, hidemsg) {
	var exists_msg=$('#'+element+'_exists').val();
	if ($('#'+element+'_'+id).val()===undefined){
		del_msg=$('#'+element+'_delete').val();
		$new_html = '<p class="newline float-fix">'
		+'<img width="1" height="1" src="/images/blank.gif" class="delete-but" onclick="deleteMultyRow(\''+element+'_'+id+'\',\''+del_msg+'\')" title="'+del_msg+'" alt="-" />'
		+'<input type="hidden" name="'+element+'[]" value="'+id+'" id="'+element+'_'+id+'">'
		+'<label for="'+element+'_'+id+'">'+title+'<\/label>';
		if($('#'+element+'_wq').val()==1){
			$new_html = $new_html +'<input type="text" class="quantity numeric form-control" name="'+element+'_quantity['+id+']" value="1.0000" id="'+element+'_quantity_'+id+'">';
		}
		$new_html = $new_html +'<\/p>';
		$('#'+element).append($new_html);
		$('#'+element+'_quantity_'+id).numeric();
		$(el_from).addClass('selected').attr("onclick", "").unbind("click");
		$(el_from).find('img').attr("title", "");
	} else {
		if(typeof(hidemsg)=="undefined") alert(exists_msg); 
	}
}
function checkAll(elem, fldName ,formName) {
	if (!fldName) {fldName = 'cps_id[]';}
	if (!formName) {formName = 'frmList';}
	$("form[name='"+formName+"'] input[name='"+fldName+"']").each(function(i) {
		if ($(elem).is(':checked')) $(this).attr('checked','checked');
		else $(this).removeAttr('checked');
		$(this).change();
    });
}
function toggleChecked(elem,className) {
	if (className) {
		var is_on = $(elem).is(':checked');
		$('input.'+className).each(function(i) {
			$(this).attr('checked',is_on);
			$(this).change();
		});
	}
}
function isChecked(fldName ,formName) {
	if (!fldName) {fldName = 'cps_id[]';}
	if (!formName) {formName = 'frmList';}
	var flag=0;
	$("form[name='"+formName+"'] input[name='"+fldName+"']").each(function(i) {
		if ($(this).is(':checked')) { flag=1; }
	});
	return flag;
}
//used only in sprav list
function submitbutton(module,view,layout,task,formName,target,option,reset_multy,controller) {
	if (!formName) {formName = 'frmList';}
	if (!target||target==='0') { target=""; }
	if (!option||option==='0') { option="module"; }
	if (!reset_multy) { reset_multy=false; }
	if (!layout) { layout=""; }
	if (controller==undefined) controller='default';
	submitform(module,view,layout,task,formName,target,option,reset_multy,controller);
}
//used in sprav list from submitbutton and directly
function submitform(module,view,layout,task,formName,target,option,reset_multy,controller){
	if (!formName) {formName = 'frmEdit';}
	if (!target||target==='0') { target=""; }
	if (!option||option==='0') { option="module"; }
	if (!layout) { layout=""; }
	if (!reset_multy) { reset_multy=false; }
	if (controller==undefined) controller='default';
	$("#view").val(view);
	$("#module").val(module);
	$("#layout").val(layout);
	$("#task").val(task);
	 
	if(reset_multy===true) $("#multy_code").val(""); 
	var fcol = document.getElementsByName(formName);
	var f = fcol[0];
	if($("#controller").length){ 
		$("#controller").val(controller);
	}
	else
	{  f.controller=controller;}
	if(target)	f.target=target;
	if(option)  f.option.value=option;
	try {f.onsubmit();}
	catch(e){} f.submit();
}
//очищает значение поля
function clearfieldVal(id) {
  $("#"+id).val('').trigger('change');
}
function go_form(formId) {
	var f = $("#"+formId);
	try {f.onsubmit();}
	catch(e){} f.submit();
	return true;
}
function form_checkfill(formId, visible_only) {
	var visible_selector="";
	if (visible_only) visible_selector=':visible';
	var no_error=true;	
	$('#'+formId+' input'+visible_selector).filter('[required]').each(function(i){
		$(this).removeClass('invalid');
		if($(this).val()=='') { $(this).addClass('invalid') .focus(); no_error=false;}  	   
	});	
	$('#'+formId+' select'+visible_selector).filter('[required]').each(function(i){
		$(this).removeClass('invalid');
		if($(this).val()==0) { $(this).addClass('invalid') .focus(); no_error=false;}  	   
	});
	$('#'+formId+' input[type=checkbox]'+visible_selector).filter('[required]').each(function(i){
		$(this).parent().removeClass('invalid');
		if(!$(this).attr('checked')) { $(this).parent().addClass('invalid'); no_error=false;}  	   
	});	
	$('#'+formId+' input.required'+visible_selector).each(function(i){
		$(this).removeClass('invalid');
		if($(this).val()=='') { $(this).addClass('invalid') .focus(); no_error=false;}  	   
	});	
	$('#'+formId+' select.required'+visible_selector).each(function(i){
		$(this).removeClass('invalid');
		if($(this).val()==0) { $(this).addClass('invalid') .focus(); no_error=false;}  	   
	});
	$('#'+formId+' input[type=checkbox].required'+visible_selector).each(function(i){
		$(this).parent().removeClass('invalid');
		if(!$(this).attr('checked')) { $(this).parent().addClass('invalid'); no_error=false;}  	   
	});	
	return no_error;
}
function ajaxShowActivity(){
	if(ajaxQueryFinished===true) {
		ajaxQueryFinished=false; 
		$('#BARMAZ-overlay').height($(document).height());
		$('#BARMAZ-loading').bind("click", function() { ajaxHideActivity(true); });
		// position adjustment
		$('#BARMAZ-loading').css('left',$(window).width()/2-$('#BARMAZ-loading').width()/2);
		$('#BARMAZ-loading').css('top',$(window).height()/2-$('#BARMAZ-loading').height()/2);
		$('#BARMAZ-loading').show();
		$('#BARMAZ-overlay').show();
	}
}
function ajaxHideActivity(ok_flag){
	if (ok_flag===false) ajaxQueryFinished=false; else ajaxQueryFinished=true; 
	$('#BARMAZ-overlay').hide();
	$('#BARMAZ-loading').unbind('click');
	$('#BARMAZ-loading').hide();
}
function ajaxUpdateActivity(msg){
	$('#BARMAZ-loading p').html(msg);
}
// May be for remove ??? Check first !!!
function checkFieldUnique(module, obj, psid){
	if ($(obj).val()!=""){
		ajaxShowActivity();
		$.ajax({
			url : siteConfig['siteUrl']+"index.php",
				data:({type:'module',
					   option:'ajax',
					   module:module,
					   fld:obj.id,
					   val:$(obj).val(),
					   psid:psid,
					   task:'checkField'
				}),
			dataType:'html',
			success : function(data, textStatus) {
				if (data=='OK') {
					verify_class='verify_ok'; 
					$("#save").removeAttr('disabled');
					$("#apply").removeAttr('disabled');
				} else { 
					verify_class='verify_err'; 
					$("#save").attr("disabled", "true");
					$("#apply").attr("disabled", "true");
				} 
				
				lbl_tmpl='<label generated="true" for="'+obj.id+'" class="'+verify_class+'">'+data+'<\/label>';
				attr=$(obj).next().attr("for");
				
				if (attr==obj.id){
					$(obj).next().removeClass('verify_ok');
					$(obj).next().removeClass('verify_err');
					$(obj).next().addClass(verify_class);
					$(obj).next().html(data);
					$(obj).next().show();
				} else { 
					//$(obj).width($(obj).width()-50);
					$(obj).wrap("<div class='verify_wrapper'></div>");
					$(obj).after(lbl_tmpl);
				}
				
				ajaxHideActivity();
				return true;
			},
			error : function() {
				ajaxHideActivity();
				return false;
			}
		});
	}
}
function checkSpravFieldUnique(module, view, layout, obj, psid, controller, multy_code){
	if ($(obj).val()!=""){
		ajaxShowActivity();
		$.ajax({
			url : siteConfig['siteUrl']+"index.php",
				data:({type:'module',
					option:'ajax',
					module:module,
					view:view,
					layout:layout,
					fld:obj.id,
					val:$(obj).val(),
					psid:psid,
					controller:controller,
					multy_code:multy_code,
					task:'checkSpravField'
				}),
			dataType:'html',
			success : function(data, textStatus) {
				if (data=='OK') {
					verify_class='verify_ok'; 
					$("#save").removeAttr('disabled');
					$("#apply").removeAttr('disabled');
					$("#add_new").removeAttr('disabled');
				} else { 
					verify_class='verify_err'; 
					$("#save").attr("disabled", "true");
					$("#apply").attr("disabled", "true");
					$("#add_new").attr("disabled", "true");
				} 
				
				lbl_tmpl='<label generated="true" for="'+obj.id+'" class="'+verify_class+'">'+data+'<\/label>';
				attr=$(obj).next().attr("for");
				
				if (attr==obj.id){
					$(obj).next().removeClass('verify_ok');
					$(obj).next().removeClass('verify_err');
					$(obj).next().addClass(verify_class);
					$(obj).next().html(data);
					$(obj).next().show();
				} else { 
					//$(obj).width($(obj).width()-50);
					$(obj).wrap("<div class='verify_wrapper'></div>");
					$(obj).after(lbl_tmpl);
				}
				
				ajaxHideActivity();
				return true;
			},
			error : function() {
				ajaxHideActivity();
				return false;
			}
		});
	}
}
function submitAddress(){
	var frm_id='addressForm';
	var f = $(frm_id);
	fillManualInput(frm_id);
	try {f.onsubmit();}
	catch(e){} f.submit();
}
function getManualInputText(frm_id, fld_pref){
	var frm_pref = '#' + frm_id + ' ';
	if (frm_id===false) frm_pref = "";
	if (!fld_pref) fld_pref="";
	var address = "";
	var separator = ", ";
	var addrArr = new Array();
	
	if( $(frm_pref + '#' + fld_pref + 'zipcode').length > 0 ) addrArr.push($(frm_pref + '#' + fld_pref + 'zipcode').val());
	if (siteConfig['useTextAddress'] == 0) {
		if( $(frm_pref + '#' + fld_pref + 'country_id').length > 0 && $(frm_pref + '#' + fld_pref + 'country_id :selected').val() != "0") addrArr.push($(frm_pref + '#' + fld_pref + 'country_id :selected').text());
		if( $(frm_pref + '#' + fld_pref + 'region_id').length > 0 && $(frm_pref + '#' + fld_pref + 'region_id :selected').val() != "0") addrArr.push($(frm_pref + '#' + fld_pref + 'region_id :selected').text());
		if( $(frm_pref + '#' + fld_pref + 'district_id').length > 0 && $(frm_pref + '#' + fld_pref + 'district_id :selected').val() != "0") addrArr.push($(frm_pref + '#' + fld_pref + 'district_id :selected').text());
		if( $(frm_pref + '#' + fld_pref + 'locality_id').length > 0 && $(frm_pref + '#' + fld_pref + 'locality_id :selected').val() != "0") addrArr.push($(frm_pref + '#' + fld_pref + 'locality_id :selected').text());
	} else {
		if( $(frm_pref + '#' + fld_pref + 'country').length > 0 && $(frm_pref + '#' + fld_pref + 'country').val() != "") addrArr.push($(frm_pref + '#' + fld_pref + 'country').val());
		if( $(frm_pref + '#' + fld_pref + 'region').length > 0 && $(frm_pref + '#' + fld_pref + 'region').val() != "") addrArr.push($(frm_pref + '#' + fld_pref + 'region').val());
		if( $(frm_pref + '#' + fld_pref + 'district').length > 0 && $(frm_pref + '#' + fld_pref + 'district').val() != "") addrArr.push($(frm_pref + '#' + fld_pref + 'district').val());
		if( $(frm_pref + '#' + fld_pref + 'locality').length > 0 && $(frm_pref + '#' + fld_pref + 'locality').val() != "") addrArr.push($(frm_pref + '#' + fld_pref + 'locality').val());
	}
	if( $(frm_pref + '#' + fld_pref + 'street').length > 0 && $(frm_pref + '#' + fld_pref + 'street').val() != "") addrArr.push($(frm_pref + '#' + fld_pref + 'street').val());
	if( $(frm_pref + '#' + fld_pref + 'house').length > 0 && $(frm_pref + '#' + fld_pref + 'house').val() != "") addrArr.push($(frm_pref + '#' + fld_pref + 'house').val());
	if( $(frm_pref + '#' + fld_pref + 'apartment').length > 0 && $(frm_pref + '#' + fld_pref + 'apartment').val() != "") addrArr.push($(frm_pref + '#' + fld_pref + 'apartment').val());
	
	address = addrArr.join(separator);
	
	return address;
}
/*
function getManualInputText_OLD(frm_id, fld_pref){
	var frm_pref='#'+frm_id+' ';
	if (frm_id===false) frm_pref="";
	if (!fld_pref) fld_pref="";
	var	adr="";	
	var zip_text="";
	var country_text="";
	var region_text="";
	var district_text="";
	var locality_text="";
	var street_text="";
	var house_text="";
	var apartment_text="";
	if( $(frm_pref+'#'+fld_pref+'zipcode').length > 0 ) zip_text=$(frm_pref+'#'+fld_pref+'zipcode').val();
	if (siteConfig['useTextAddress']==0) {
		if( $(frm_pref+'#'+fld_pref+'country_id').length > 0 && $(frm_pref+'#'+fld_pref+'country_id :selected').val()!="0") country_text = $(frm_pref+'#'+fld_pref+'country_id :selected').text();
		if( $(frm_pref+'#'+fld_pref+'region_id').length > 0 && $(frm_pref+'#'+fld_pref+'region_id :selected').val()!="0") region_text = $(frm_pref+'#'+fld_pref+'region_id :selected').text();
		if( $(frm_pref+'#'+fld_pref+'district_id').length > 0 && $(frm_pref+'#'+fld_pref+'district_id :selected').val()!="0") district_text = $(frm_pref+'#'+fld_pref+'district_id :selected').text();		
		if( $(frm_pref+'#'+fld_pref+'locality_id').length > 0 && $(frm_pref+'#'+fld_pref+'locality_id :selected').val()!="0") locality_text = $(frm_pref+'#'+fld_pref+'locality_id :selected').text();		
	} else {
		if( $(frm_pref+'#'+fld_pref+'country').length > 0 && $(frm_pref+'#'+fld_pref+'country').val()!="") country_text = $(frm_pref+'#'+fld_pref+'country').val();
		if( $(frm_pref+'#'+fld_pref+'region').length > 0 && $(frm_pref+'#'+fld_pref+'region').val()!="") region_text = $(frm_pref+'#'+fld_pref+'region').val();
		if( $(frm_pref+'#'+fld_pref+'district').length > 0 && $(frm_pref+'#'+fld_pref+'district').val()!="") district_text = $(frm_pref+'#'+fld_pref+'district').val();		
		if( $(frm_pref+'#'+fld_pref+'locality').length > 0 && $(frm_pref+'#'+fld_pref+'locality').val()!="") locality_text = $(frm_pref+'#'+fld_pref+'locality').val();		
	}
	if( $(frm_pref+'#'+fld_pref+'street').length > 0 && $(frm_pref+'#'+fld_pref+'street').val()!="") street_text = $(frm_pref+'#'+fld_pref+'street').val();	
	if( $(frm_pref+'#'+fld_pref+'house').length > 0 && $(frm_pref+'#'+fld_pref+'house').val()!="") house_text = $(frm_pref+'#'+fld_pref+'house').val();	
	if( $(frm_pref+'#'+fld_pref+'apartment').length > 0 && $(frm_pref+'#'+fld_pref+'apartment').val()!="") apartment_text = $(frm_pref+'#'+fld_pref+'apartment').val();	
	
	adr = zip_text + "," + country_text + "," + region_text + "," + district_text + "," + locality_text + "," + street_text + "," + house_text + "," + apartment_text;
	return adr;
}
*/
function fillManualInput(frm_id, fld_pref){
	if( typeof(frm_id)==='undefined' || frm_id===false) frm_pref="";
	else frm_pref='#'+frm_id+' ';
	if( typeof(fld_pref)==='undefined' || fld_pref===false) fld_pref="";
	if( $(frm_pref+'#'+fld_pref+'fullinfo').length > 0 && $(frm_pref+'#'+fld_pref+'fullinfo').val()=='' ) {
		$(frm_pref+'#'+fld_pref+'fullinfo').val(getManualInputText(frm_id,fld_pref));
		return true;
	} else {
		return false;
	}
}
function sweepInput(id){
	$('#'+id).val('');
}
function updateRegionSelector(ctrl_pref, country_eid, region_eid, district_eid, locality_eid){
	/*
	if (ctrl_pref != '') {
		country_eid=ctrl_pref+country_eid;
		region_eid=ctrl_pref+region_eid;
		district_eid=ctrl_pref+district_eid;
		locality_eid=ctrl_pref+locality_eid;
	}
	*/ 
	var parent_id=$("#"+ctrl_pref+country_eid).val();
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
			psid: parent_id
		}),
		dataType : 'html',
		success : function(data, textStatus) {
			elem="#"+ctrl_pref+region_eid;
			is_required=$(elem).hasClass('required');
			elem_parent=$(elem).parent();
			data_onchange = $(elem).attr('data-onchange');
			$(elem).remove();
			elem_parent.append(data);
			if (is_required && !$(elem).hasClass('required')) $(elem).addClass('required');
			$(elem).attr('data-onchange', data_onchange);
			$(elem).attr('onchange', data_onchange);
			ajaxHideActivity();
			updateDistrictSelector(ctrl_pref, region_eid, district_eid, locality_eid);
		},
		error : function() {
			alert('Error occured');
			ajaxHideActivity();
			return false;
		}
	});
}
function updateDistrictSelector(ctrl_pref, region_eid, district_eid, locality_eid){
	/*
	if (ctrl_pref!='') {
		region_eid=ctrl_pref+region_eid;
		district_eid=ctrl_pref+district_eid;
		locality_eid=ctrl_pref+locality_eid;
	} 
	*/
	var parent_id=$("#"+ctrl_pref+region_eid).val(); 
	ajaxShowActivity();
	$.ajax({
		url : siteConfig['siteUrl'] + "index.php",
		data : ({
			type : 'module',
			option : 'ajax',
			module : 'user',
			ctrl_pref: ctrl_pref,
			view : 'panel',
			task : 'getDistrictSelector',
			psid: parent_id
		}),
		dataType : 'html',
		success : function(data, textStatus) {
			elem="#"+ctrl_pref+district_eid;
			is_required=$(elem).hasClass('required');
			elem_parent=$(elem).parent();
			data_onchange = $(elem).attr('data-onchange');
			$(elem).remove();
			elem_parent.append(data);
			if (is_required && !$(elem).hasClass('required')) $(elem).addClass('required');
			$(elem).attr('data-onchange', data_onchange);
			$(elem).attr('onchange', data_onchange);
			ajaxHideActivity();
			updateLocalitySelector(ctrl_pref, district_eid, locality_eid);
		},
		error : function() {
			alert('Error occured');
			ajaxHideActivity();
			return false;
		}
	});
}

function updateLocalitySelector(ctrl_pref, district_eid, locality_eid){
	/*
	if (ctrl_pref!='') {
		district_eid=ctrl_pref+district_eid;
		locality_eid=ctrl_pref+locality_eid;
	} 
	*/
	var parent_id=$("#"+ctrl_pref+district_eid).val(); 
	ajaxShowActivity();
	$.ajax({
		url : siteConfig['siteUrl'] + "index.php",
		data : ({
			type : 'module',
			option : 'ajax',
			module : 'user',
			ctrl_pref: ctrl_pref,
			view : 'panel',
			task : 'getLocalitySelector',
			psid: parent_id
		}),
		dataType : 'html',
		success : function(data, textStatus) {
			elem="#"+ctrl_pref+locality_eid;
			is_required=$(elem).hasClass('required');
			elem_parent=$(elem).parent();
			data_onchange = $(elem).attr('data-onchange');
			$(elem).remove();
			elem_parent.append(data);
			if (is_required && !$(elem).hasClass('required')) $(elem).addClass('required');
			$(elem).attr('data-onchange', data_onchange);
			$(elem).attr('onchange', data_onchange);
			ajaxHideActivity();
			return true;
		},
		error : function() {
			alert('Error occured');
			ajaxHideActivity();
			return false;
		}
	});
}

function toggleEnabled(elm,module,view,layout,psid,controller,multy_code){
	ajaxShowActivity();
	if(controller==undefined) controller="default";
	$.ajax({
		url : siteConfig['siteUrl'] + "index.php",
		data : ({
			option : 'ajax',
			module : module,
			view : view,
			layout : layout,
			controller : controller,
			task : 'ToggleEnabled',
			psid: psid,
			multy_code: multy_code
		}),
		dataType : 'html',
		success : function(data, textStatus) {
			if (data=='OK') {
				if ($(elm).hasClass('switcher_on')){
					$(elm).removeClass('switcher_on');
					$(elm).addClass('switcher_off');
					$("#sprav-tree #multycode_"+psid).addClass('disabled');
				} else if ($(elm).hasClass('switcher_off')){
					$(elm).removeClass('switcher_off');
					$(elm).addClass('switcher_on');
					$("#sprav-tree #multycode_"+psid).removeClass('disabled');
				}
			}
			ajaxHideActivity();
		},
		error : function() {
			alert('Error occured');
			ajaxHideActivity();
			return false;
		}
	});		
}
function toggleSpravTree() {
	if ($('#sprav-panel').hasClass('collapsedPanel')){
		$('#sprav-panel').removeClass('collapsedPanel');
	} else {
		$('#sprav-panel').addClass('collapsedPanel');
	}
}
function sprToggleTree(cdirection) {
	if ($('#collapse_button').hasClass('tree_collapse_left')){
		$('#sprav-panel').addClass('collapsedPanel');
		$('#collapse_button').removeClass('tree_collapse_left').addClass('tree_collapse_right');
	} else {
		$('#sprav-panel').removeClass('collapsedPanel');
		$('#collapse_button').removeClass('tree_collapse_right').addClass('tree_collapse_left');
	}
}

function cleanTrash(query_status,module,view,layout,multy_code,first_rec,controller) {
	if(controller==undefined) controller="default";
	if (query_status==='start') ajaxShowActivity();
	var url = siteConfig['siteUrl'] + 'index.php?option=ajax&task=cleanTrash&module='+module+'&view='+view+'&layout='+layout+'&multy_code='+multy_code+'&first_rec='+first_rec+'&controller='+controller;
	if (ajaxQueryFinished===false) {
		$.getJSON(url, {}, function(json) {
			ajaxUpdateActivity(json.message);
			if (json.status==='processing') {
				setTimeout('cleanTrash(\''+json.status+'\',\''+module+'\',\''+view+'\',\''+layout+'\',\''+multy_code+'\',\''+json.first_rec+'\',\''+controller+'\')',1000);
			} else {
				$('#BARMAZ-loading').unbind("click");
				$('#BARMAZ-loading').bind("click", function() { window.location.reload(); });
			}
		});
	}
}
/* Comments */
function clearCommentAnswerTo() {
	$('#parent_id').val(0);
	$('#commentLabel').html($('#cleanCommentLabel').val());
	$('#com_selectors').show();
}
function setCommentAnswerTo(comm_id) {
	comm_text=$('#comment_author_'+comm_id).html()+' ('+$('#comment_date_'+comm_id).html()+')';
	html_text = '<img width=\"1\" height=\"1\" onclick=\"clearCommentAnswerTo()\" src=\"/images/blank.gif\" alt=\"\" title=\"\" class=\"clearAnswerTo\" />';
	html_text +=	$('#answerCommentLabel').val();
	html_text += '<'+'a href=\"#comment' + comm_id + '\"> ' + comm_text + '<\/a>:';
	$('#commentLabel').html(html_text);
	$('#parent_id').val(comm_id);
	$('#com_selectors select').val(0);
	$('#com_selectors').hide();
}

function toggleCommentEnabled(elem,module,view,psid,comm_id){
	var published=$('#comm_published_'+comm_id).val();
	ajaxShowActivity();
	$.ajax({
		url : siteConfig['siteUrl'] + "index.php",
		data : ({
			option : 'ajax',
			module : module,
			view : view,
			psid: psid,
			task : 'ToggleCommentEnabled',
			published: published,
			comm_id: comm_id
		}),
		dataType : 'json',
		success : function(data, textStatus) {
			if (data.status=='OK'){
				if (published==1) {
					$('#comm_published_'+comm_id).val(0);
					$('#cm_body_'+comm_id).addClass('unpublished');
				} else {
					$('#comm_published_'+comm_id).val(1);
					$('#cm_body_'+comm_id).removeClass('unpublished');
				}	
				$(elem).html(data.message);
			} else {
				if(data.message != "") alert(data.message);
			}
			ajaxHideActivity();
		},
		error : function() {
			alert('Error occured');
			ajaxHideActivity();
			return false;
		}
	});		
}
function toggleCommentDeleted(elem,module,view,psid,comm_id){
	var deleted=$('#comm_deleted_'+comm_id).val();
	ajaxShowActivity();
	$.ajax({
		url : siteConfig['siteUrl'] + "index.php",
		data : ({
			option : 'ajax',
			module : module,
			view : view,
			psid: psid,
			task : 'ToggleCommentDeleted',
			deleted: deleted,
			comm_id: comm_id
		}),
		dataType : 'json',
		success : function(data, textStatus) {
			if (data.status=='OK'){
				if (deleted==1) {
					$('#comm_deleted_'+comm_id).val(0);
					$('#cm_body_'+comm_id).removeClass('deleted');
				} else {
					$('#comm_deleted_'+comm_id).val(1);
					$('#cm_body_'+comm_id).addClass('deleted');	
				}
				$(elem).html(data.message);
			} else {
				if(data.message != "") alert(data.message);
			}
			ajaxHideActivity();
		},
		error : function() {
			alert('Error occured');
			ajaxHideActivity();
			return false;
		}
	});		
}
function getMoreComments(elem,module,view,psid,parent_id,start){
	ajaxShowActivity();
	$.ajax({
		url : siteConfig['siteUrl'] + "index.php",
		data : ({
			option : 'ajax',
			module : module,
			view : view,
			psid: psid,
			task : 'getComments',
			parent_id: parent_id,
			start: start
		}),
		dataType : 'html',
		success : function(data, textStatus) {
			comm_list=$('#morecomments_'+parent_id).parent();
			$('#morecomments_'+parent_id).remove();
			comm_list.append(data);
			ajaxHideActivity();
		},
		error : function() {
			alert('Error occured');
			ajaxHideActivity();
			return false;
		}
	});		
}
function getCommentChildren(elem,module,view,psid,parent_id){
	if ($('#subcomments_'+parent_id).html()) {
		$('#subcomments_'+parent_id).toggle(300);
		return;
	}
	ajaxShowActivity();
	$.ajax({
		url : siteConfig['siteUrl'] + "index.php",
		data : ({
			option : 'ajax',
			module : module,
			view : view,
			psid: psid,
			task : 'getComments',
			parent_id: parent_id
		}),
		dataType : 'html',
		success : function(data, textStatus) {
			$('#subcomments_'+parent_id).append(data);
			$('#subcomments_'+parent_id).toggle(300);
			ajaxHideActivity();
		},
		error : function() {
			alert('Error occured');
			ajaxHideActivity();
			return false;
		}
	});		
}
function acrmStatUpdate(){
	var item_ids="";
	$('.itms_ident').each(function() {
		if (item_ids==="") item_ids=$(this).val(); else item_ids=item_ids+","+$(this).val();
	});
	if (checkCookies(1)==1){
		if (item_ids!=""){
			$.ajax({
				url : siteConfig['siteUrl'] + "index.php",
				data : ({
					option : 'ajax',
					module : 'acrm',
					psid: item_ids,
					task : 'displayExecuted'
				}),
				dataType : 'html',
				success : function(data, textStatus) {	},
				error : function() { }
			});		
		}
	}
}
function voteRating(obj,mod,view,elem,psid,dir){
	ajaxShowActivity();
	$.ajax({
		url : siteConfig['siteUrl']+"index.php",
		data:({type:'module',
			   option:'ajax',
			   module:mod,
			   view:view,
			   element:elem,
			   psid:psid,
			   dir:dir,
			   task:'vote'
			   }),
		dataType:'html',
		success : function(data, textStatus) {
			$($(obj).parent().get(0)).html(data);
			ajaxHideActivity();
		},
		error : function() {
			alert('Error in data.');
			ajaxHideActivity();
			return false;
		}
	});
}
function toggleVisiblePan(elm,id){
  if($(elm).val()>0) $("#"+id).hide(); else $("#"+id).show();    
}
function hidePopup() {
	$('#fancybox-inner').html('');
	$('#fancybox-overlay').hide();
	$('#fancybox-wrap').hide();
}
function appendDTPicker(el, picker_time, picker_date){
	// jquery ui
	if( typeof( picker_date ) == 'undefined' ) picker_date=true;
	if( typeof( picker_time ) == 'undefined' ) picker_time=false;
	if($(el).next().hasClass("date_selector")) $(el).next().bind("click",function(e){  $(el).focus();} );
	if (!picker_time) $(el).datepicker({changeYear: true, yearRange: '-100:+50', dateFormat: 'dd.mm.yy'});
	else if(!picker_date) $(el).timepicker({timeFormat: 'HH:mm'});
	else $(el).datetimepicker({	changeYear: true, yearRange: '-100:+50', timeFormat: 'HH:mm', separator: ' '});
}
function addAfterContentLoadHandler(handlerName){
	if(afterContentLoadHandlers.indexOf(handlerName, 0) < 0){
		afterContentLoadHandlers.push(handlerName);
		// console.log('Added handler: '+handlerName);
		return true;
	} else return false;
}
function applyAfterContentLoadHandlers(parent_prefix){
	afterContentLoadHandlers.forEach(function(item, i, arr) {
		var fn = window[item]; 
		if ($.isFunction(fn)) {
			window[item](parent_prefix);
		} else {
			// console.log('Ooops, function absent '+item);
		}
	});
}
function afterAjaxUpdate(el_id){
	if (el_id) {
		parent_prefix = "#" + el_id; 
		afterContentLoad(parent_prefix);
		applyAfterContentLoadHandlers(parent_prefix); 
	}
}
function afterContentLoad(parent_prefix){ 
	if(parent_prefix) _current_prefix_ws = parent_prefix + " "; else _current_prefix_ws = "";
	$(_current_prefix_ws+'input.numeric').numeric();
	$(_current_prefix_ws+'input.decimal').numeric({decimal : "."});
	$(_current_prefix_ws+'a.relpopup').fancybox({
		'hideOnOverlayClick': true,
		'hideOnContentClick': false,
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	200,
		'speedOut'		:	100,
		'autoDimensions' : true,
		'scrolling': 'no',
		'centerOnScroll': true,
		'titleShow'		:	false,
		'enableNavArrows' : false,
		'showNavArrows'	:	false,
		'onComplete' : function() { 
			applyHTML5Required('#fancybox-content');
			applyJSFormats('#fancybox-content');
		}
	});
	$(_current_prefix_ws+'a.relpopupwt').fancybox({
		'hideOnOverlayClick': true,
		'hideOnContentClick': false,
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	200,
		'speedOut'		:	100,
		'titleShow'		:	true,
		'enableNavArrows' : false,
		'showNavArrows'	:	false,
		'onComplete': function() {
			applyHTML5Required('#fancybox-content');
			applyJSFormats('#fancybox-content');
		}
	});	
	$(_current_prefix_ws+'a.relpopuptext').fancybox({
		'hideOnOverlayClick': true,
		'hideOnContentClick': false,
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	200,
		'speedOut'		:	100,
		'autoDimensions' : true,
		'centerOnScroll': true,
		'titleShow'		:	false,
		'enableNavArrows' : false,
		'showNavArrows'	:	false,
		'type'		:	'inline',
		'onComplete' : function() { 
			applyHTML5Required('#fancybox-content');
			applyJSFormats('#fancybox-content');
		}
	});
	$(_current_prefix_ws+'a.relpopuptext80').fancybox({
		'hideOnOverlayClick': true,
		'hideOnContentClick': false,
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	200,
		'speedOut'		:	100,
		'autoDimensions' : false,
		'centerOnScroll': true,
		'titleShow'		:	false,
		'enableNavArrows' : false,
		'showNavArrows'	:	false,
		'type'			:	'inline',
		'autoScale'     :true,
		'height'        : '80%',
		'width'			: '80%',
		'onComplete' : function() { 
			applyHTML5Required('#fancybox-content');
			applyJSFormats('#fancybox-content');
		}
	});
	$(_current_prefix_ws+'a.relpopupdate').fancybox({
		'hideOnOverlayClick': true,
		'hideOnContentClick': false,
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	200,
		'speedOut'		:	100,
		'titleShow'	:	false,
		'showNavArrows'	:	false,
		'enableNavArrows' : false,
		'onComplete' : function(){ 
			applyHTML5Required('#fancybox-content');
			applyJSFormats('#fancybox-content');
		}
	});
	$(_current_prefix_ws+'a.spravselector').fancybox({
		'hideOnOverlayClick': true,
		'hideOnContentClick': false,
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	200,
		'speedOut'		:	100,
		'autoDimensions' : true,
		'centerOnScroll': true,
		'titleShow'		:	false,
		'enableNavArrows' : false,
		'showNavArrows'	:	false,
		'type'		:	'inline',
		'onComplete' : function(){ 
			applyHTML5Required('#fancybox-content');
			applyJSFormats('#fancybox-content');
		}
	});
	applyHTML5Required(parent_prefix);
	if (typeof templateEveryLoad == 'function') templateEveryLoad(parent_prefix); 
	$(".singleclick").each(function(){
		$(this).bind("click",function(){
			$(this).unbind('click').bind('click', false).addClass("disabled");
			$(this).siblings('a, button, input[type=button], input[type=submit], input[type=reset]').addClass("disabled");
			return true;
		})
	})
}
function setMaxLength(textareaID,maxLength){
//	maxLength = $("#"+textareaID).attr("maxlength");
    $("#"+textareaID).after("<div><span id='remainingLength"+textareaID+"'>" + maxLength + "<\/span> remaining<\/div>");
    $("#"+textareaID).bind("keyup change", function(){checkMaxLength(textareaID,  maxLength); } )
}
function checkMaxLength(textareaID, maxLength){
    currentLengthInTextarea = $("#"+textareaID).val().length;
    var remainingLengthTempId='#remainingLength'+textareaID;
    $(remainingLengthTempId).text(parseInt(maxLength) - parseInt(currentLengthInTextarea));
	if (currentLengthInTextarea > (maxLength)) { 
		// Trim the field current length over the maxlength.
		$("#"+textareaID).val($("#"+textareaID).val().slice(0, maxLength));
		$(remainingLengthTempId).text(0);
	}
}	
function equalizeDivHeight(div4eq){
	var currentTallest = 0, currentRowStart = 0, rowDivs = new Array(), current_el, topPosition = 0;
	if(equalizedHeightElements.indexOf(div4eq, 0) < 0){
		equalizedHeightElementsCounter++;
		equalizedHeightElements[equalizedHeightElementsCounter]=div4eq;
	}
	$(div4eq).height("auto");
	$(div4eq).each(
		function() {
			current_el = $(this);
			topPostion = current_el.offset().top;
			if (currentRowStart != topPostion) {
				for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {
					rowDivs[currentDiv].height(currentTallest);
				}
				rowDivs.length = 0;
				currentRowStart = topPostion;
				currentTallest = current_el.height();
				rowDivs.push(current_el);
			} else {
				rowDivs.push(current_el);
				currentTallest = (currentTallest < current_el.height()) ? (current_el.height()) : (currentTallest);
			}
			for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {
				rowDivs[currentDiv].height(currentTallest);
			}
		}
	);
}
function getErrMsg(jqXHR, exception) {
	var msg = '';
	if(siteConfig['debugMode']!=0){
		if (jqXHR.status === 0) {msg = 'Not connect.Verify Network.';} 
		else if (jqXHR.status == 404) {msg = 'Requested page not found. [404]';} 
		else if (jqXHR.status == 500) {msg = 'Internal Server Error [500].';} 
		else if (exception === 'parsererror') {msg = 'Requested JSON parse failed.';} 
		else if (exception === 'timeout') {msg = 'Time out error.';} 
		else if (exception === 'abort') {msg = 'Ajax request aborted.';} 
		else {msg = 'Uncaught Error.\n' + jqXHR.responseText;}
	}
	return msg;
}
function object_key_exists (key, search) {
	if (!search || (search.constructor !== Array && search.constructor !== Object)) {
		return false;
	}
	return key in search;
}

function decodeHTMLEntities(text) {
	  var textArea = document.createElement('textarea');
	  textArea.innerHTML = text;
	  return textArea.value;
}
function encodeHTMLEntities(text) {
	  var textArea = document.createElement('textarea');
	  textArea.innerText = text;
	  return textArea.innerHTML;
}
/* SUPPORT FOR DONKEY */
var JSON = JSON || {};
JSON.stringify = JSON.stringify || function (obj) {
	var t = typeof (obj);
	if (t != "object" || obj === null) { // simple data type
		if (t == "string") obj = '"'+obj+'"';
		return String(obj);
	} else { // recurse array or object
		var n, v, json = [], arr = (obj && obj.constructor == Array);
		for (n in obj) {
			v = obj[n]; t = typeof(v);
			if (t == "string") v = '"'+v+'"';
			else if (t == "object" && v !== null) v = JSON.stringify(v);
			json.push((arr ? "" : '"' + n + '":') + String(v));
		}
		return (arr ? "[" : "{") + String(json) + (arr ? "]" : "}");
	}
};
