//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO


function updateRoleRuleFF(el, forum_id, role_id, act, flag) {
	var newflag=1;
	ajaxShowActivity();
	$.ajax({
		url : "index.php",
		data:({type:'module',
			option:'ajax',
			module:'forum',
			task:'updateRoleRule',
			psid:forum_id,
			role:role_id,
			act:act,
			flag:flag
		}),
		dataType:'html',
		success : function(data, textStatus) {
			if (data=='OK'){
				if (flag=='1') newflag='0'; 
				$htmlcode='<a onclick="updateRoleRuleFF(this,\''+forum_id+'\',\''+role_id+'\',\''+act+'\',\''+newflag+'\')"><img width="1" height="1" src="/images/blank.gif" alt="" class="enabled_'+flag+'" /></a>';
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
function updateUserRuleFF(el, forum_id, user_id, act, flag) {
	var newflag=1;
	ajaxShowActivity();
	$.ajax({
		url : "index.php",
		data:({type:'module',
			option:'ajax',
			module:'forum',
			task:'updateUserRule',
			psid:forum_id,
			user:user_id,
			act:act,
			flag:flag
		}),
		dataType:'html',
		success : function(data, textStatus) {
			if (data=='OK'){
				if (flag=='1') newflag='2'; 
				$htmlcode='<a onclick="updateUserRuleFF(this,\''+forum_id+'\',\''+user_id+'\',\''+act+'\',\''+newflag+'\')"><img width="1" height="1" src="/images/blank.gif" alt="" class="enabled_'+flag+'" /></a>';
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
function toggleForumSubscription(theme_id, subscribe){
	ajaxShowActivity();
	var url = siteConfig['siteUrl'] + 'index.php?option=ajax&module=forum&task=subscribeUserToTheme&theme_id='+theme_id+'&subscribe='+subscribe;
	$.getJSON(url, {}, function(json) {
		if (json.status==='OK') {
//			$("#subscribe_theme_button").unbind( "click" );
			$("#subscribe_theme_button").attr( "onclick" , json.event_text);
			$("#subscribe_theme_button").html(json.title);
			ajaxHideActivity();
		} else {
			alert('Error');
			ajaxHideActivity();
		}
	});
}