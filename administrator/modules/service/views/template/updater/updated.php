<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

?>
<div class="container"><div class="row"><div class="col-md-12"><div class="service-manager rounded-pan rounded-pan-mini">
	<?php if(property_exists("backofficeConfig", "updatesBetaChannel") && backofficeConfig::$updatesBetaChannel) { ?>
		<h4 class="title"><span class="red"><?php echo Text::_("Beta updates channel") ?></span></h4>
		<p align="center" class="red"><?php echo Text::_("Beta updates channel is for testing ONLY").". ".Text::_("We dont quarantee the correct working of your CMS while using it")."."; ?></p>
	<?php } else { ?>
		<h4 class="title"><?php echo Text::_("Main updates channel") ?></h4>
	<?php } ?>
	<h4 class="title"><?php echo Text::_("Automatic update") ?></h4>
	<div class="row">
		<div class="col-md-12"><?php echo HTMLControls::renderLabelField(false, Text::_("Operations log")); ?></div>
	</div>
	<div class="row"><div class="col-md-12">
		<?php if(count($this->packagelog)>0){
			foreach($this->packagelog as $msg){
				echo "<p class=\"log_message\">".$msg."</p>";
			}
		} ?>
	</div></div>
	<div class="buttons">
		<a class="linkButton btn btn-info" href="index.php?module=service&amp;view=updater"><?php echo Text::_("Continue"); ?></a>
	</div>
</div></div></div></div>