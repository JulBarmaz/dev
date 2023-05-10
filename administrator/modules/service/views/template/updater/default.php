<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

if ($this->current_version > $this->version) $class="red";
else $class="blue";
?>
<div class="container"><div class="row"><div class="col-md-12"><div class="service-manager rounded-pan rounded-pan-mini">
	<?php if(backofficeConfig::$updatesBetaChannel) { ?>
		<h4 class="title"><span class="red"><?php echo Text::_("Beta updates channel") ?></span></h4>
		<p align="center" class="red"><?php echo Text::_("Beta updates channel is for testing ONLY").". ".Text::_("We dont quarantee the correct working of your CMS while using it")."."; ?></p>
	<?php } else { ?>
		<h4 class="title"><?php echo Text::_("Main updates channel") ?></h4>
	<?php } ?>
	<h4 class="title"><?php echo Text::_("Version control") ?></h4>
	<div class="row">
		<div class="col-sm-8"><?php echo HTMLControls::renderLabelField(false, Text::_("Current version").":"); ?></div>
		<div class="col-sm-4"><?php echo Portal::getVersionMajor().".".Portal::getVersionMinor().".".$this->current_version; ?></div>
	</div>
	<div class="row">
		<div class="col-sm-8"><?php echo HTMLControls::renderLabelField(false, Text::_("Your version").":"); ?></div>
		<div class="col-sm-4 <?php echo $class; ?>"><?php echo Portal::getVersion(); ?></div>
	</div>
	<div class="row"><div class="col-sm-12 <?php echo $class; ?>">
		<?php if (!$this->current_version) { ?>
			<?php echo Text::_("Updates unavailable"); ?>
		<?php } elseif ($this->current_version > $this->version) { ?>
			<?php echo Text::_("Your version is out-of-date"); ?>
		<?php }	else { ?>
			<?php echo Text::_("Your version is up-to-date"); ?>
		<?php }	?>
	</div></div>
	<div class="buttons">
		<?php  if ($this->current_version && ($this->current_version > $this->version)) { ?>
			<a target="_blank" class="linkButton btn btn-info" href="http://BARMAZ.ru/updates/<?php echo Portal::getVersionMajor()."/".Portal::getVersionMinor().(backofficeConfig::$updatesBetaChannel ? "-beta" : ""); ?>/download.php"><?php echo Text::_("Download new version"); ?></a>
		<?php 	}	?>
		<a class="linkButton btn btn-info" href="index.php"><?php echo Text::_("Close"); ?></a>
	</div>
	<?php  if ($this->current_version && ($this->current_version > $this->version)) { ?>
	<div class="buttons" style="margin-top:5px;">
		<a class="linkButton singleclick btn btn-info" href="index.php?module=service&amp;view=updater&amp;layout=autoupdate"><?php echo Text::_("Automatic update"); ?></a>
	</div>
	<?php 	}	?>
</div></div></div></div>