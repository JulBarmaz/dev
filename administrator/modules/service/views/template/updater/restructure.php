<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

if ($this->current_version > $this->version) $class="red";
else $class="blue";
?>
<div class="container"><div class="row"><div class="col-md-12"><div class="service-manager rounded-pan rounded-pan-mini">
	<h4 class="title"><?php echo Text::_("Database restructuring") ?></h4>
	<?php  if ($this->current_version && ($this->current_version > $this->version)) { ?>
		<div class="row">
			<div class="col-md-12 red"><?php 
				echo "<h3 style=\"font-weight:bold; text-align:center;\">".Text::_("Attention")."!</h3>"; 
				echo Text::_("You must make full backup of your site and database before you continue").".<br />"; 
				echo Text::_("Database restructuring process may cause harm to your site and database")."!<br /><br />"; 
				echo Text::_("You start it at own risk")."!<br /><br />"; 
				echo Text::_("We do not incur responsibility for")." "; 
				echo Text::_("any moral costs or commercial losses connected with")." <br />"; 
				echo Text::_("use of automatic updating")." !!!<br /><br />"; 
				echo Text::_("By pressing CONTINUE button you completely agree with the above-stated conditions")." "; 
				echo Text::_("and take all risks up")." !!!<br />"; 
			?></div>
		</div>
	<?php } ?>
	<div class="row">
		<div class="col-sm-8"><?php echo HTMLControls::renderLabelField(false, Text::_("Current version").":"); ?></div>
		<div class="col-sm-4"><?php echo Portal::getVersionMajor().".".Portal::getVersionMinor().".".$this->current_version; ?></div>
	</div>
	<div class="row">
		<div class="col-sm-8"><?php echo HTMLControls::renderLabelField(false, Text::_("Your version").":"); ?></div>
		<div class="col-sm-4 <?php echo $class; ?>"><?php echo Portal::getVersionMajor().".".Portal::getVersionMinor().".".$this->version; ?></div>
	</div>
	<?php if ($this->current_version > $this->version) { ?>
		<h4 class="title"><span class="<?php echo $class; ?>"><?php echo Text::_("Your database must be restructured"); ?></span></h4>
	<?php }	else { ?>
		<h4 class="title"><span class="<?php echo $class; ?>"><?php echo Text::_("Your database does not need restructuring"); ?></span></h4>
	<?php }	?>
	<div class="buttons" style="margin-top:5px;">
		<?php  if ($this->current_version && ($this->current_version > $this->version)) { ?>
			<a class="linkButton singleclick btn btn-info" href="index.php?module=service&amp;task=restructureDB"><?php echo Text::_("Continue"); ?></a>
		<?php 	}	?>
		<a class="linkButton btn btn-info" href="index.php"><?php echo Text::_("Close"); ?></a>
	</div>
</div></div></div></div>	