//BARMAZ_COPYRIGHT_TEMPLATE

function getPostDatesForMonth(psid,year,month){
	$.ajax({
		url : siteConfig['siteUrl']+"index.php",
		async:false,
		data:({
			// async:false,  
			type:'module',
			option:'ajax',
			module:'blog',
			psid:psid,
			post_year:year,
			post_month:month,
			task:'getPostDatesForMonth'
		}),
		dataType: "json",
		success: function (data, textStatus) {
			blogEnabledDates = data;
			ajaxHideActivity();
		},
		error: function () { ajaxHideActivity(); return false; }
	});
}
function disableBlogCalendarDates(date) {
	date = $.datepicker.formatDate('yy-mm-dd', date);
	var todayDate = new Date();
	todayDate = $.datepicker.formatDate('yy-mm-dd', todayDate);
//	console.log(todayDate);
	if (date==todayDate) return [true];
//	console.log([$.inArray(date, blogEnabledDates) !=-1]);
	return [$.inArray(date, blogEnabledDates) !=-1];
}

function setBlogCalendarCookie(bid, myDate1, myDate2){
	if (checkCookies()==0) return false;
	newcoo=''+myDate1+'#'+myDate2;
	$.cookie('BARMAZ_blog_'+bid, newcoo, {expires: 30, path: '/', domain: siteConfig['siteDomain'],	secure: siteConfig['siteSecure '] });	
}
function resetBlogCalendarCookie(bid){
	$.cookie('BARMAZ_blog_'+bid, null, {path: '/' , domain: siteConfig['siteDomain'],	secure: siteConfig['siteSecure '] });	
}

