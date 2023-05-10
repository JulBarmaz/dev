<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

?>
<div class="row"><div class="col-md-12"><div class="install-list">
	<h4 class="title"><?php echo Text::_("Package instalation and deleting"); ?></h4>
	<?php 
	echo "<table class=\"table-bordered table-hover table-condensed sprav-table\">";
	echo "<tr>";
	echo "<th>".Text::_("Package type")."</th>";
	echo "<th>".Text::_("Package name")."</th>";
	echo "<th>".Text::_("Version")."</th>";
	echo "<th>".Text::_("Description")."</th>";
	echo "<th>".Text::_("Author")."</th>";
	echo "<th>".Text::_("E-mail")."</th>";
	echo "<th>".Text::_("Site")."</th>";
	echo "<th>".Text::_("License")."</th>";
	echo "<th>".Text::_("Uninstall")."</th>";
	echo "</tr>";
	$crow = 0;
	if (count($this->packages)) {
		foreach($this->packages as $package){
			$crow=abs($crow-1);
			$class="crow_".$crow;
			echo "<tr class=\"".$class."\">";
			echo "<td>".Text::_($package->c_type)."</td>";
			echo "<td class=\"package_name\">".$package->c_name."</td>";
			echo "<td class=\"package\">".$package->c_version."</td>";
			echo "<td class=\"package\">".$package->c_description."</td>";
			echo "<td class=\"package\">".$package->c_author."</td>";
			if ($package->c_email) $link="<a href=\"mailto:".$package->c_email."\">".$package->c_email."</a>"; else $link="";
			echo "<td class=\"package\">".$link."</td>";
			if ($package->c_site) $link="<a target=\"_blank\" href=\"".$package->c_site."\">".$package->c_site."</a>"; else $link="";
			echo "<td class=\"package\">".$link."</td>";
			if ($package->c_license) $link="<a class=\"relpopupwt\" href=\"index.php?module=installer&option=ajax&task=readLicense&psid=".$package->c_id."\">".Text::_("Read")."</a>";	else $link=Text::_("Absent");
			echo "<td class=\"but\">".$link."</td>";
			$button="<a onclick=\"if (confirm('".Text::_("Do you want to delete")." ".Text::_($package->c_type)." ".$package->c_name." ?')) return true; else return false;\" href=\"index.php?module=installer&task=uninstall&psid=".$package->c_id."\">".Text::_("Uninstall")."</a>";
			echo "<td class=\"but\" width=\"10%\">".$button."</td>";
			echo "</tr>";
		}
	}
	echo "</table>";
	?>
</div></div></div>

<div class="container"><div class="row"><div class="col-md-12"><div class="install-manager rounded-pan rounded-pan-medium">
	<div class="install-form">
		<form action="index.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="option" value="module" />
			<input type="hidden" name="module" value="installer" />
			<input type="hidden" name="task" value="installFromFile" />
			<div class="row">
				<div class="col-sm-8">
					<div class="row">
						<div class="col-sm-4">
							<?php echo HTMLControls::renderLabelField("packageFile", Text::_("File").":"); ?>
						</div>
						<div class="col-sm-8">
							<div class="fileselector">
								<?php echo HTMLControls::renderInputFile("packageFile", "", 135, "packageFile"); ?>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-4">
					<input class="commonButton btn btn-info" type="submit" value="<?php echo Text::_("Upload and install"); ?>" />
				</div>
			</div>
		</form>
		<form action="index.php" method="post">
			<input type="hidden" name="option" value="module" />
			<input type="hidden" name="module" value="installer" />
			<input type="hidden" name="task" value="installFromFolder" />
			<div class="row">
				<div class="col-sm-8">
					<div class="row">
						<div class="col-sm-4">
							<?php echo HTMLControls::renderLabelField("packagePath", Text::_("Package path").":"); ?>
						</div>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="packagePath" name="packagePath" value="" /></td>
						</div>
					</div>
				</div>
				<div class="col-sm-4">
					<input class="commonButton btn btn-info" type="submit" value="<?php echo Text::_("Install"); ?>" />
				</div>
			</div>
		</form>
		<form action="index.php" method="post">
			<input type="hidden" name="option" value="module" />
			<input type="hidden" name="module" value="installer" />
			<input type="hidden" name="task" value="installFromURL" />
			<div class="row">
				<div class="col-sm-8">
					<div class="row">
						<div class="col-sm-4">
							<?php echo HTMLControls::renderLabelField("packagePath", Text::_("File").":"); ?>
						</div>
						<div class="col-sm-8">
							<input type="text" class="form-control" id="packageURL" name="packageURL" value="" />
						</div>
					</div>
				</div>
				<div class="col-sm-4">
					<input class="commonButton btn btn-info" type="submit" value="<?php echo Text::_("Download and install"); ?>" />
				</div>
			</div>
		</form>
	</div>
</div></div></div></div>
