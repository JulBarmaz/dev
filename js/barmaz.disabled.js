//BARMAZ_COPYRIGHT_TEMPLATE

var idmod=0; // May be just for example, not found in project

function checkCookies() {
	$.cookie('BARMAZ', 'dfgdhdfhdfghfdhdffhdfhdf', {expires:1, path: '/' ,domain: siteConfig['siteDomain'],	secure: siteConfig['siteSecure ']  });
	if ($.cookie('BARMAZ')!='dfgdhdfhdfghfdhdffhdfhdf') {
		// Failed
		$.cookie('BARMAZ', null, {expires: -1, path: '/' ,domain: siteConfig['siteDomain'],	secure: siteConfig['siteSecure '] });
		return 0;
	} else {
		// OK
		$.cookie('BARMAZ', null, {expires: -1, path: '/' ,domain: siteConfig['siteDomain'],	secure: siteConfig['siteSecure '] });
		return 1;
	}
}