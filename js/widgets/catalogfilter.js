//BARMAZ_COPYRIGHT_TEMPLATE

function catalog_ext_flt_reset_key(div_id, frmname, cur_key, but_el){
	ajaxShowActivity();
	setTimeout(function() {
		el=$("#"+div_id+" *[name='"+cur_key+"[]']").each(function(){
			$(this).removeAttr("name");	
		});
		el=$("#"+div_id+" *[name='"+cur_key+"']").each(function(){
		$(this).removeAttr("name");	
		});
		el=$("#"+div_id+" *[name='fake1_"+cur_key+"']").each(function(){
			$(this).removeAttr("name");	
		});
		el=$("#"+div_id+" *[name='fake2_"+cur_key+"']").each(function(){
		$(this).removeAttr("name");	
		});
		el=$("#"+div_id+" *[name='fake3_"+cur_key+"']").each(function(){
			$(this).removeAttr("name");	
		});
		el=$("#"+div_id+" *[name='fake4_"+cur_key+"']").each(function(){
			$(this).removeAttr("name");	
		});
		$("#"+div_id+" form[name="+frmname+"]").append('<input type="hidden" value="1" name="save_filter">');
		$("#"+div_id+" form[name="+frmname+"]").submit();
	}, 500);
}
function catalog_ext_flt_onsubmit(){
	var flds = ['c_g_price_1','c_g_price_2','c_g_price_3','c_g_price_4','c_g_price_5'];
	for (var i in flds) {
		catalog_ext_flt_reset_field(flds[i]);
	}
}
function catalog_ext_flt_reset_field(fld){
	if(typeof($('div.catalog-extended-filter #fake2_'+fld).val())=='undefined') return;
	if($('div.catalog-extended-filter #fake2_max_'+fld).val()==$('div.catalog-extended-filter #fake2_'+fld).val()){
		$('div.catalog-extended-filter #fake2_'+fld).val(0);
	}
}
function catalog_ext_flt_set_mode(ef_mode, div_id, frmname){
	if (ef_mode==0){
		$("#"+div_id+" form[name="+frmname+"]").append('<input type="hidden" value="1" name="reset_filter">');
		$("#"+div_id+" form[name="+frmname+"]").append('<input type="hidden" value="0" name="filter_ext_mode">');
	} else {
		$("#"+div_id+" form[name="+frmname+"]").append('<input type="hidden" value="1" name="filter_ext_mode">');
	}
	$("#"+div_id+" form[name="+frmname+"]").submit();
}