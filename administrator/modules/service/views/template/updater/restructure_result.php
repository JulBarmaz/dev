<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

?>
<div class="container"><div class="row"><div class="col-md-12"><div class="service-manager rounded-pan rounded-pan-mini">
	<h4 class="title"><?php echo Text::_("Database restructuring") ?></h4>
	<div class="row">
		<div class="col-md-12"><?php echo HTMLControls::renderLabelField(false, Text::_("Operations log")); ?></div>
	</div>
	<div class="row"><div class="col-md-12">
	<?php if(count($this->packagelog)>0){
		foreach($this->packagelog as $msg){
			echo "<p class=\"log_message\">".$msg."</p>";
		}
	} else {
		echo "<p class=\"log_message\">".Text::_("Operation complete")."</p>";
	}?>
	</div></div>
	<div class="buttons">
		<a class="linkButton btn btn-info" href="index.php?module=service&amp;view=updater&amp;layout=restructure"><?php echo Text::_("Continue"); ?></a>
	</div>
</div></div></div></div>