
$(window).on('load', function(){ spravPictoScroll()});
$(window).scroll(function(){
	spravPictoScroll();
});

$(window).resize(function(){
	spravPictoScroll();
});
function templateEveryLoad(parent_prefix){
	if(typeof(parent_prefix) != 'undefined' && parent_prefix) _current_prefix_ws = parent_prefix + ' ';  else _current_prefix_ws='';
	if(typeof($().infoballoon)==='function') $(_current_prefix_ws + '.balloon_button').infoballoon();
	if(typeof($().Beautifier)==='function') $(_current_prefix_ws + 'input[type=checkbox]').Beautifier();
	if(typeof($().nicefileinput)==='function') $(_current_prefix_ws + "input[type=file]").nicefileinput({ label : '...' });
	if(typeof(siteConfig['debugMode']) != 'undefined' && parseInt(siteConfig['debugMode']) > 0){
		console.log("function templateEveryLoad executed");
	}
}
function csv_import_check_fields(){
	var _fld=0;
	var _sku=0;
	var _name=0;
	
	$('.fcell select').each(function(e){
		if(this.value==='g_sku'){
			_sku++;
		} else if (this.value==='g_name'){
			_name++;
		} else {
			if(this.value!=0){
				_fld++;
			}
		}
	});
	if (_sku==0) { alert('SKU not selected'); return false;}
	else if (_sku>1) { alert('SKU must be single'); return false;}
	else if (_name==0) { alert('Name not selected'); return false;}
	else if (_name>1) { alert('Name must be single'); return false;}
	else {
		if(_fld==0){ alert('No fields selected'); return false;}
	}
	return true;
}
function csv_export_checkbox(id){
	$('#'+id+' a').each(function(e){
		new_id=$(this).parent().attr('id');
		ind=new_id.split('_')[3];
		$(this).html('<span class="checkboxContainer"><input type="checkbox"'+($(this).parents('li').hasClass('disabled') ? '' : ' checked="checked"')+' value="'+ind+'" name="ggr[]" id="a'+new_id+'" /><label for="a'+new_id+'">'+$(this).html()+'<\/label><\/span>');
	});
	$('#'+id+' a input[type=checkbox]').Beautifier();
	$('#'+id+'  a input[type=checkbox]').each(function(el){
		$(this).bind('change', function(ell){
			var ppp=$(this);
			$(this).closest('li').find('ul input[type=checkbox]').each(function(elem){
				if ($(ppp).is(':checked')) $(this).attr('checked','checked');
				else $(this).removeAttr('checked');
	   			if($(this).is(':checked')) { $(this).next('label').addClass('CheckboxChecked');	} 
	   			else { 	$(this).next("label").removeClass('CheckboxChecked');	}
			});
		});
	});
}
function csv_export_accordion(id){
	$('#'+id+' li>a').each(function(e){
		$(this).addClass('float-fix');
		if($(this).next('ul').length>0) { 
			$(this).addClass('opened'); 
			$(this).bind('click',function(){
				if($(this).hasClass('opened')) { $(this).removeClass('opened'); $(this).addClass('closed'); }
				else { $(this).addClass('opened'); $(this).removeClass('closed'); }
				$(this).next().toggle(700);
			});
		}
	});
}
function spravPictoScroll(){
	var windw = $(window).width();
	if (windw > 752){ /** Screen width - 16px of vertical scroll **/
		var fixed_top = $('#panel').height();
	}else{
		var fixed_top = ($('#panel').height())+($('#inner_wrapper #sprav-panel #tree-panel').height());
	}
	var elem = "#inner_wrapper .sprav-list .nmb";
	var up_top = $(elem).height();
	$(elem).removeClass('sprav_fixed');
	
	if (fixed_top > 0){
		if (checkBrowserIsIE()){
			var scrr = ($("html, body").scrollTop());
		} else {
			var scrr = ($(window).scrollTop());
		}
		if (checkBrowserIsIE()){
			if ($("html, body").scrollTop() > fixed_top){
				$(elem).addClass("sprav_fixed").css({'top':'0px'});
				$('#inner_wrapper').css({'padding-top':up_top+'px'});
			} else {
				$(elem).removeClass('sprav_fixed').css({'top':scrr+'px','left':'0'});
				$('#inner_wrapper').css({'padding-top':'0px'});
			}
		} else {
			if ($(window).scrollTop() > fixed_top){
				$(elem).addClass("sprav_fixed").css({'top':'0px'});
				$('#inner_wrapper').css({'padding-top':up_top+'px'});
			} else {
				$(elem).removeClass('sprav_fixed').css({'top':scrr+'px','left':'0'});
				$('#inner_wrapper').css({'padding-top':'0px'});
			}
		}
	} else {
		$(elem).addClass("sprav_fixed").css({'top':'0px'});
		$('#inner_wrapper').css({'padding-top':up_top+'px'});
	}
}
