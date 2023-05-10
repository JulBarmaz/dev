<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

?>
<div class="container"><div class="row"><div class="col-md-12"><div class="service-manager rounded-pan rounded-pan-mini">
	<h4 class="title"><?php echo Text::_("Clear users filters"); ?></h4>
	<div id="filters" class="row">
	<?php 
		echo "<div class=\"col-xs-10 col-sm-11\">".Text::_("Found filter records")." : ".$this->filtercount."</div>";
		echo "<div class=\"col-xs-2 col-sm-1\"><a href=\"index.php?module=service&amp;task=clearUserFilter\" onclick=\"javascript:if(confirm('".Text::_("Do you want to clear all filters")." ?'))return true; else return false;\" title=\"".Text::_("Clear")."\">
				<img width=\"1\" height=\"1\" src=\"/images/blank.gif\" class=\"delete\" alt=\"".Text::_("Clear")."\" /></a>";
		echo "</div>";
	?>
	</div>
</div></div></div></div>