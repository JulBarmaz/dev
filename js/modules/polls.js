//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO


function ajaxvotePoll(pollid){
	ajaxShowActivity();
	$.ajax({
			url : siteConfig['siteUrl']+"index.php",
			  data:({type:'module',
					 option:'ajax',
				     module:'polls',
					 psid:pollid,
					 task:'getVotePanel'
					}),
				dataType:'html',
				success : function(data, textStatus) {
					$.fancybox({'content':data,
						'autoDimensions' : true,
						'centerOnScroll': true,
						'autoScale':true,
						'width':600
					});
					ajaxHideActivity();
				},
				error : function() {
					alert('Data error');
					return false;
				}
			});
}