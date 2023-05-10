//BARMAZ_COPYRIGHT_TEMPLATE

$(document).ready(function() { 
	$('.inner-grid-array').each(function(){
		$(this).bind('click',function(){
			 $(this).toggleClass('expanded');
		});
	});	
});
function modifySelector(parent_prefix){
	if(parent_prefix) _current_prefix_ws = parent_prefix + " "; else _current_prefix_ws = "";
	// console.log('started modifySelector with parent_prefix="' + parent_prefix + '"');
	lol = $(_current_prefix_ws + '#sprav-tree-selector-ul').parent('.sprav-tree-selector-data').children('input[type=hidden].sprav_list_lol').val();
	if(typeof(lol) !='undefined'){
		$(_current_prefix_ws + '#sprav-tree-selector-ul').find('li>a').each(function(){
			var psid = $(this).closest('li').attr('data-row-id');
			var title = $(this).text();
			var _html = '<div class="selector  selector-'+psid+'"  onclick="addFromSelector(this, \''+lol+'\',\''+psid+'\',\''+title+'\');"><span class="selector-data" data-container="'+lol+'" data-element="'+psid+'"><img width="1" height="1" src="/images/blank.gif" alt="" title="'+'" /></span></div>';
			$(this).wrap('<div class="selector-a"></div>').closest('div').append(_html);
			
		});
	}
	$(_current_prefix_ws + '#sprav-tree-selector-ul').treeview({
		animated: 100,
		unique: false,
		persist: "cookie",
		collapsed : true,
		cookieId: "sprav_selector_navigator"
	});
	/*******************************************************/
	$(_current_prefix_ws + '.selector-data').each(function(){
		container = $(this).attr('data-container');
		elem_id = $(this).attr('data-element');
		elem = '#' + container  + ' #' + container + "_" + elem_id
		if(typeof($(elem).val()) !='undefined'){
			$(this).parent('.selector').addClass('selected').attr("onclick", "").unbind("click");
			$(this).find('img').attr("title", "");
		} else {
			// console.log('not found ' + elem);			
		}
	});
}
function updateLabel(elem, module, view, task, psid, single_event) {
	ajaxShowActivity();
	$.ajax({
		url : siteConfig['siteUrl'] + "index.php",
		data : ({
			type : 'module',
			option : 'ajax',
			psid : psid,
			module : module,
			view : view,
			layout : 'selector',
			task : task
		}),
		dataType : 'html',
		success : function(data, textStatus) {
			elem.innerHTML = data;
			if (single_event === 1) {
				$(elem).css('cursor', 'default');
				$(elem).removeAttr('title');
				$(elem).removeAttr('onclick');
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
function modifyLinkOrdering(elem, module, view, layout, psid, multy_code) {
	ordering=prompt('',elem.innerHTML);
	if (ordering===null) return;
	if (ordering===elem.innerHTML) return;
	ajaxShowActivity();
	$.ajax({
		url : siteConfig['siteUrl'] + "index.php",
		data : ({
			type : 'module',
			option : 'ajax',
			module : module,
			view : view,
			layout : layout,
			psid : psid,
			multy_code: multy_code,
			ordering: ordering,
			task : 'UpdateLinkOrdering'
		}),
		dataType : 'html',
		success : function(data, textStatus) {
			if (data==="OK") elem.innerHTML = ordering;
			ajaxHideActivity();
		},
		error : function() {
			alert('Error occured');
			ajaxHideActivity();
			return false;
		}
	});
}
function modifySpravOrdering(elem, module, view, layout, psid, multy_code,controller) {
	ordering=prompt('',elem.innerHTML);
	if (ordering===null) return;
	if (ordering===elem.innerHTML) return;
	if (controller===undefined) controller="default"; 
	ajaxShowActivity();
	$.ajax({
		url : siteConfig['siteUrl'] + "index.php",
		data : ({
			type : 'module',
			option : 'ajax',
			module : module,
			view : view,
			controller :controller,
			layout : layout,
			psid : psid,
			multy_code: multy_code,
			ordering: ordering,
			task : 'UpdateOrdering'
		}),
		dataType : 'html',
		success : function(data, textStatus) {
			if (data==="OK") elem.innerHTML = ordering;
			ajaxHideActivity();
		},
		error : function() {
			alert('Error occured');
			ajaxHideActivity();
			return false;
		}
	});
}
function toggleCheckboxes(cb_class, mess){
	if (confirm(mess+' ?')) {
		$('input.'+cb_class).each(function(){ 
			if ($(this).is(':checked')==false) { $(this).attr('checked','checked');	} 
			else { $(this).removeAttr('checked'); }
			$(this).trigger('change');
		});
	}
}
function csv_import_proceed(row2start){
	if (row2start===0) ajaxShowActivity();
	var url = siteConfig['siteUrl'] + 'index.php?module=catalog&view=import&step=4&row2start='+row2start;
	if (ajaxQueryFinished===false) {
		$.getJSON(url, {}, function(json) {
			if (json.log_message!='') $('#import_log').append(json.log_message);
			if (json.status_message!='') ajaxUpdateActivity(json.status_message);
			if (json.status==='processing') {
				setTimeout('csv_import_proceed(\''+json.row2start+'\')',1000);
			} else {
				ajaxHideActivity();
			}
		});
	} else {
		ajaxHideActivity();
	}
}
