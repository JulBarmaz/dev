<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_TEMPLATE_INFO

defined('_BARMAZ_VALID') or die("Access denied");
define("_BARMAZ_IE_DROP", 7);
define('_BARMAZ_HTML5',true);



//Portal::getInstance()->addScript("jquery.selectBoxIt.js");
//Portal::getInstance()->addStylesheet("jquery.selectBoxIt.css");
Portal::getInstance()->addScript("jquery.infoballoon.js");
Portal::getInstance()->addStylesheet("jquery.infoballoon.css");
Portal::getInstance()->addScript("jquery.beautifier.js");
Portal::getInstance()->addStylesheet("jquery.beautifier.css");
Portal::getInstance()->addScript("jquery.nicefileinput.min.js");
Portal::getInstance()->addStylesheet("jquery.nicefileinput.css");

$scroll_script="$(window).on('load',function() {
	$(window).scroll(function() { if ($(window).scrollTop() != 0) { $('#scrollToTop').fadeIn(); } else if ($(window).scrollTop() == 0) { $('#scrollToTop').fadeOut(); } });
	$('#scrollToTop').bind('click',function(){ $('html, body').animate({ scrollTop: 0  }, 200);	});
});";
Portal::getInstance()->addScriptDeclaration($scroll_script);
echo $this->renderSystemMessage(); ?>
<div id="outer_wrapper" class="float-fix">
	<div id="panel" class="printhidden">
		<div id="panel_line">
			<div class="container-fluid">
				<div class="row d-block d-sm-none">
					<div class="col-md-12">
						<div class="panel-logo"><a target="_blank" href="http://BARMAZ.ru">BARMAZ-ERP <small><?php echo Portal::getVersion()." ".Portal::getProjectName()." ".Portal::getLicenseType(); ?></small></a><?php echo " - ".Text::_("Administration"); ?></div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div id="panel_butt_exit" class="panel_button_r"><a title="<?php echo Text::_('Logout'); ?>" href="/index.php?option=logout"><img width="1" height="1" alt="" src="/images/blank.gif" /></a></div>
						<div id="panel_butt_help" class="panel_button_r"><a class="relpopuptext80"  title="<?php echo Text::_('Help'); ?>" href="index.php?module=help&amp;notmpl=1"><img width="1" height="1" alt="" src="/images/blank.gif" /></a></div>
						<div id="panel_butt_license" class="panel_button_r"><a class="relpopuptext80" title="<?php echo Text::_('License'); ?>" href="index.php?module=help&amp;layout=license&amp;notmpl=1"><img width="1" height="1" alt="" src="/images/blank.gif" /></a></div>
						<div id="panel_butt_site" class="panel_button_r"><a target="_blank" title="<?php echo Text::_('Back to frontend'); ?>" href="<?php echo Portal::getInstance()->getURI(1); ?>"><img width="1" height="1" alt="" src="/images/blank.gif" /></a></div>
						<div id="panel_butt_cpanel" class="panel_button_l"><a title="<?php echo Text::_('Main page'); ?>" href="index.php"><img width="1" height="1" alt="" src="/images/blank.gif" /></a></div>
						<div class="panel-logo hidden-xs"><a target="_blank" href="http://BARMAZ.ru">BARMAZ-ERP <small><?php echo Portal::getVersion()." ".Portal::getProjectName()." ".Portal::getLicenseType(); ?></small></a><?php echo " - ".Text::_("Administration"); ?></div>
					</div>
				</div>
			</div>
		</div>
		<nav class="navbar navbar-expand-lg bg-body-tertiary">		
		<?php $this->placeWidget('adminmenu',array('menuId'=>1,'menu_divId'=>'panel_line1'),0); ?>
		</nav>
	</div> <!-- panel -->
	<div class="clr"></div>
	<div id="inner_wrapper">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<?php echo $this->moduleHTML; ?>
				</div>
			</div>
		</div>
	</div>
	<div class="clear"></div>
	<div id="footer" class="printhidden">
		<div class="container-fluid">
			<div class="row">
				<div id="footer_l" class="col-sm-6"></div> <!-- footer_l -->
				<div id="footer_r" class="col-sm-6"><?php echo $this->getCopyright(); ?></div> <!-- footer_r -->
			</div>
		</div>
	</div> <!-- footer_m -->
</div> <!-- contentWrapper -->
<a id="scrollToTop" title="" class="scrollToTop printhidden" style="display:none;"><?php echo Text::_("Scroll up"); ?></a>
<div class="clr"></div>
