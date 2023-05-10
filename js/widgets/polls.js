//BARMAZ_COPYRIGHT_TEMPLATE

function votePoll(pollid){
	var voteid = $("#wpoll_"+pollid+" form input[name='voteid[]']:checked").val();
	var color=$("#barcolor_"+pollid).val();
	if (voteid==undefined) { voteid=0; }
	else {
		  $.ajax({
				url : siteConfig['siteUrl']+"index.php",
				  data:({type:'module',
						 option:'ajax',
					     module:'polls',
						 psid:voteid,
						 task:'PollVote'
						}),
					dataType:'html',
					success : function(data, textStatus) {
						$('#wpoll_'+pollid).html(data);
						$('#wpoll_'+pollid+' div.percentage').each(function(i) {
							$(this).css('background',color);
						});
					},
					error : function() {
						alert('Data error');
						return false;
					}
				});
	}
}