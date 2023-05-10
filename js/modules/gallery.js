//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO


var gallery_slideshow=null;
$(document).ready(function() {
	$("a.gallery").fancybox({
		'cyclic'		: true,
		'rel'			: 'data-gg-attr',
		'titlePosition' : 'over', // 'float', 'outside', 'inside' or 'over'
		'titleShow'		: true,
		'showNavArrows'	: true,
		'onComplete': function() {
			//
		},
		'onClosed': function() {
			stopslideshow();
		}
	});
});
function slideshow(){
	$("#first_image").trigger('click');
	gallery_slideshow=setTimeout("next_slide()", siteConfig['slideshow_timeout']*1000);
}
function next_slide(){
	if ($.fancybox) {
		$.fancybox.next();
		gallery_slideshow=setTimeout("next_slide()", siteConfig['slideshow_timeout']*1000);
	}
}
function stopslideshow(){
	clearTimeout(gallery_slideshow);
}