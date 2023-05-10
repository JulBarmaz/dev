//BARMAZ_COPYRIGHT_TEMPLATE

function rgDisplayed(item_id){
	if (checkCookies(1)==1){
		if (item_id!=""){
			$.ajax({
				url : siteConfig['siteUrl'] + "index.php",
				data : ({
					option : 'ajax',
					module : 'acrm',
					psid: item_id,
					task : 'displayExecuted'
				}),
				dataType : 'html',
				success : function(data, textStatus) {	},
				error : function() { }
			});		
		}
	}
}
function clickRGACRM($str){
	if (checkCookies(1)==1){ $.cookie($str, $str,{expires: 7 , domain: siteConfig['siteDomain'],	secure: siteConfig['siteSecure '] }); }
}