<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

Portal::getInstance()->AddScriptDeclaration("$(document).ready(function() { csv_import_proceed(0); });");
?>
<div class="container"><div class="row"><div class="col-md-12"><div class="catalog-manager rounded-pan rounded-pan-medium">
	<h4 class="title"><?php echo Text::_("Import catalog data");?></h4>
	<div class="row"><div class="col-md-12"><?php echo Text::_("Data upload started"); ?></div></div>
	<div class="row"><div class="col-md-12"><div id="import_log"></div></div></div>
</div></div></div></div>