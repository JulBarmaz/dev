//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO


function updateRoleRule(el, blog_id, role_id, act, flag) {
	var newflag=1;
	ajaxShowActivity();
	$.ajax({
		url : "index.php",
		data:({type:'module',
			option:'ajax',
			module:'blog',
			task:'updateRoleRule',
			psid:blog_id,
			role:role_id,
			act:act,
			flag:flag
		}),
		dataType:'html',
		success : function(data, textStatus) {
			if (data=='OK'){
				if (flag=='1') newflag='0'; 
				$htmlcode='<a onclick="updateRoleRule(this,\''+blog_id+'\',\''+role_id+'\',\''+act+'\',\''+newflag+'\')"><img width="1" height="1" src="/images/blank.gif" alt="" class="enabled_'+flag+'" /></a>';
				$(el).parent().html($htmlcode);
				ajaxHideActivity();
			} else {
				alert('Server answered ERROR');
				ajaxHideActivity();
				return false;
			}
		},
		error : function() {
			alert('Server ERROR');
			ajaxHideActivity();
			return false;
		}
	});
}
function updateUserRule(el, blog_id, user_id, act, flag) {
	var newflag=1;
	ajaxShowActivity();
	$.ajax({
		url : "index.php",
		data:({type:'module',
			option:'ajax',
			module:'blog',
			task:'updateUserRule',
			psid:blog_id,
			user:user_id,
			act:act,
			flag:flag
		}),
		dataType:'html',
		success : function(data, textStatus) {
			if (data=='OK'){
				if (flag=='1') newflag='2'; 
				$htmlcode='<a onclick="updateUserRule(this,\''+blog_id+'\',\''+user_id+'\',\''+act+'\',\''+newflag+'\')"><img width="1" height="1" src="/images/blank.gif" alt="" class="enabled_'+flag+'" /></a>';
				$(el).parent().html($htmlcode);
				ajaxHideActivity();
			} else {
				alert('Server answered ERROR');
				ajaxHideActivity();
				return false;
			}
		},
		error : function() {
			alert('Server ERROR');
			ajaxHideActivity();
			return false;
		}
	});
}