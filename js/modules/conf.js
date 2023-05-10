//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO


function setListVisioModules() {
	var m_admin_side = $("#m_admin_side option:selected").val();
	ajaxShowActivity();
	$.ajax({
		url : siteConfig['siteUrl'] + "index.php",
		data : ({
			type : 'module',
			option : 'ajax',
			module : 'conf',
			task : 'listModules',
			m_admin_side : m_admin_side
		}),
		dataType : 'html',
		success : function(data, textStatus) {
			$('#m_module').html(data);
			ajaxHideActivity();
			setListVisioViews();
		},
		error : function() {
			alert('Error occured');
			ajaxHideActivity();
			return false;
		}
	});
}

function setListVisioViews() {
	var m_admin_side = $("#m_admin_side option:selected").val();
	var m_module = $('#m_module option:selected').val();
	ajaxShowActivity();
	$.ajax({
		url : siteConfig['siteUrl'] + "index.php",
		data : ({
			type : 'module',
			option : 'ajax',
			module : 'conf',
			task : 'listViews',
			m_module : m_module,
			m_admin_side : m_admin_side
		}),
		dataType : 'html',
		success : function(data, textStatus) {
			$('#m_view').html(data);
			ajaxHideActivity();
			setListVisioLayout()
		},
		error : function() {
			alert('Error occured');
			ajaxHideActivity();
			return false;
		}
	});
}
function setListVisioLayout() {
	var m_admin_side = $("#m_admin_side option:selected").val();
	var m_module = $('#m_module option:selected').val();
	var m_view = $('#m_view option:selected').val();
	ajaxShowActivity();
	$.ajax({
		url : siteConfig['siteUrl'] + "index.php",
		data : ({
			type : 'module',
			option : 'ajax',
			module : 'conf',
			task : 'listLayouts',
			m_admin_side : m_admin_side,
			m_module : m_module,
			m_view : m_view
		}),
		dataType : 'html',
		success : function(data, textStatus) {
			$('#m_layout').html(data);
			afterAjaxUpdate("selectVisio");
			ajaxHideActivity();
		},
		error : function() {
			alert('Error occured');
			afterAjaxUpdate("selectVisio");
			ajaxHideActivity();
			return false;
		}
	});
}
function dopFieldTypeOnChange(el){
	var el_val=parseInt($(el).val());
	if(typeof(el)!= 'undefined'){
		if(typeof($(el).val())!= 'undefined' && !isNaN(el_val)){
			if(el_val>0){
				if($(el).val()==5 || $(el).val()==8){
					$("#wrapper-f_vals_count").show();
				} else {
					$("#wrapper-f_vals_count").hide();
				}
			}
		}
	}
}