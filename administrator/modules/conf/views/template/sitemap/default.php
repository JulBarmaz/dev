<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

?>
<div class="container"><div class="row"><div class="col-md-12"><div class="install-manager rounded-pan rounded-pan-mini">
	<h4 class="title"><?php echo Text::_('Created maps')?></h4>
	<div class="sitemap">
		<ul class="sitemap-selector">
			<li><a href="<?php echo Portal::getURI(true); ?>sitemap.html" target="_blank"><?php echo Text::_("HTML format"); ?></a></li>
			<li><a href="<?php echo Portal::getURI(true); ?>sitemap.xml" target="_blank"><?php echo Text::_("XML format"); ?></a></li>
		</ul>
	</div>
</div></div></div></div>