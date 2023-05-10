<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

?>
<div class="container"><div class="row"><div class="col-md-12"><div class="service-manager rounded-pan rounded-pan-medium">
	<h4 class="title"><?php echo Text::_("Current backups"); ?></h4>
	<div class="buttons row"><div class="col-md-12">
		<a class="linkButton btn btn-info" href="index.php?module=service&amp;view=db&amp;layout=export"><?php echo Text::_("Create DB backup"); ?></a>
	</div></div>
	<div class="row"><div class="col-md-12">
		<?php echo Text::_("Path"); ?>: <?php echo $this->backup_path?>
	</div></div>
	<?php 
	if ($this->files) {
		echo "<table id=\"backups\" class=\"table table-bordered table-hover table-condensed sprav-table\">";
		echo "<tr>";
		echo "<th>".Text::_("Name")."</th>";
		echo "<th>".Text::_("File size")."</th>";
		echo "<th>".Text::_("Date")."</th>";
		echo "<th>&nbsp;</th>";
		echo "</tr>";
		
		foreach($this->files as $file){
			echo "<tr>";
			echo "<td><a href=\"index.php?module=service&amp;task=downloadBackup&amp;file=".$file["filename"]."\">".$file["filename"]."</a></td>";
			echo "<td>".$file["filesize"]."</td>";
			echo "<td>".$file["filedate"]."</td>";
			echo "<td align=\"center\"><a href=\"index.php?module=service&amp;task=deleteBackup&amp;file=".$file["filename"]."\" onclick=\"javascript:if(confirm('".Text::_("Do you want to delete")." ".$file["filename"]." ?'))return true; else return false;\" title=\"".Text::_("Delete")."\">
					<img width=\"1\" height=\"1\" src=\"/images/blank.gif\" class=\"delete\" alt=\"".Text::_("Delete")."\" />
				</a></td>";
			echo "</tr>";
		}
		echo "</table>";
	}
		?>
	</table>
	</div>
</div></div></div></div>