//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_TEMPLATE_INFO

$(window).load(function(){
	$("input[type=file]").nicefileinput({
		label : '...'
	});
	navyButtons();
});
function applyHeights(){
	equalizeDivHeight('.row-cells-autoheight .row-cell-wrapper');
	/*************************************************************/
	equalizeDivHeight('.quadro-wrapper .g_thumb_link');
	equalizeDivHeight('.quadro-wrapper .g_thumb');
	equalizeDivHeight('.quadro-wrapper .g_title');
	equalizeDivHeight('.quadro-wrapper .g_custom_properties');
	equalizeDivHeight('.quadro-wrapper .g_price');
	equalizeDivHeight('.quadro-wrapper .g_quantity');
	
	equalizeDivHeight('.quadro-wrapper .group-img');
	equalizeDivHeight('.quadro-wrapper .group-link');
	
	equalizeDivHeight('.quadro-wrapper');
	/*************************************************************/
	// equalizeDivHeight('.g_mini_thumbs .g_mini_thumb'); // Replaced with ".row-cells-autoheight .row-cell-wrapper"
	/*************************************************************/
	equalizeDivHeight('.galleryModule .gallery-image .gallery-image-wrapper');
	equalizeDivHeight('.galleryModule .gallery-image .image-title');
	equalizeDivHeight('.galleryModule .gallery-image');
	/*************************************************************/
	// equalizeDivHeight('#footer_wrapper.autoheight .footer-block-inner'); // Replaced with ".row-cells-autoheight .row-cell-wrapper"
}
function applyFooterBlocksHeights(){
	var footer_height = $('#footer_wrapper.autoheight').height();
	if(typeof(footer_height) == 'undefined') return
	$('#bufer_wrapper').height(footer_height)
	$('#footer_wrapper').css("margin-top", "-"+footer_height+"px")
}
$(window).load(function () {
	applyHeights();
	addAfterContentLoadHandler('applyHeights');
	applyFooterBlocksHeights();
});
$(window).resize(function(){
	equalizedHeightElements.forEach(function(item, i, arr) {
		equalizeDivHeight(item);
	});
	applyFooterBlocksHeights();
});
function navyButtons(){
	$("a#go_top").hide().removeAttr("href");
	if ($(window).scrollTop() >= "250")	$("a#go_top").fadeIn("slow");
	$(window).scroll(function() {
		if ($(window).scrollTop() <= "250") $("a#go_top").fadeOut("slow");
		else $("a#go_top").fadeIn("slow");
	});
	$("a#go_bottom").hide().removeAttr("href");
	if ($(window).scrollTop() <= $(document).height() - "999") $("a#go_bottom").fadeIn("slow");
	$(window).scroll(function() {
		if ($(window).scrollTop() >= $(document).height() - "999") $("a#go_bottom").fadeOut("slow");
		else $("a#go_bottom").fadeIn("slow");
	});
	$("a#go_top").click(function() {
		$("html, body").animate({ scrollTop : 0 }, "slow");
	})
	$("a#go_bottom").click(function() {
		$("html, body").animate({scrollTop : $(document).height()}, "slow");
	})
}
function templateEveryLoad(parent_prefix){
	if( typeof( parent_prefix ) != 'undefined' && parent_prefix) _current_prefix_ws = parent_prefix + ' ';  else _current_prefix_ws='';
	$(_current_prefix_ws + ".phone").each(function(){ $(this).mask("+9(999) 999-9999"); });
	if(typeof(siteConfig['debugMode']) != 'undefined' && parseInt(siteConfig['debugMode']) > 0){
		console.log("function templateEveryLoad executed");
	}
}