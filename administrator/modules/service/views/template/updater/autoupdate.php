<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

if ($this->current_version > $this->version) { 
	$class=" class=\"red\"";
} else 	{
	$class=" class=\"blue\"";
}
?>
<div class="container"><div class="row"><div class="col-md-12"><div class="service-manager rounded-pan rounded-pan-mini">
	<?php if(backofficeConfig::$updatesBetaChannel) { ?>
		<h4 class="title"><span class="red"><?php echo Text::_("Beta updates channel") ?></span></h4>
		<p align="center" class="red"><?php echo Text::_("Beta updates channel is for testing ONLY").". ".Text::_("We dont quarantee the correct working of your CMS while using it")."."; ?></p>
	<?php } else { ?>
		<h4 class="title"><?php echo Text::_("Main updates channel") ?></h4>
	<?php } ?>
	<h4 class="title"><?php echo Text::_("Automatic update") ?></h4>
	<div class="row">
		<div class="col-md-12 red"><?php 
			echo "<h3 style=\"font-weight:bold; text-align:center;\">".Text::_("Attention")."!</h3>"; 
			echo Text::_("You must make full backup of your site and database before you continue").".<br />"; 
			echo Text::_("Automatic update may cause harm to your site and database")."!<br /><br />"; 
			echo Text::_("You use it at own risk")."!<br /><br />"; 
			echo Text::_("We do not incur responsibility for")." "; 
			echo Text::_("any moral costs or commercial losses connected with")." <br />"; 
			echo Text::_("use of automatic updating")." !!!<br /><br />"; 
			echo Text::_("By pressing CONTINUE button you completely agree with the above-stated conditions")." "; 
			echo Text::_("and take all risks up")." !!!<br />"; 
		?></div>
	</div>
	<div class="buttons">
		<a target="_blank" class="linkButton btn btn-info" href="index.php?module=service&amp;view=db&amp;layout=export"><?php echo Text::_("Create DB backup"); ?></a> 
		<a class="linkButton btn btn-info singleclick" href="index.php?module=service&amp;task=downloadUpdates"><?php echo Text::_("Continue"); ?></a>
		<a class="linkButton btn btn-info" href="index.php?module=service&amp;view=updater"><?php echo Text::_("Close"); ?></a>
	</div>
</div></div></div></div>