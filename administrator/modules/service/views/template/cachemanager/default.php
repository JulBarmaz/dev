<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

?>
<div class="container"><div class="row"><div class="col-md-12"><div class="service-manager rounded-pan rounded-pan-medium">
	<h4 class="title"><?php echo Text::_("Cache manager"); ?></h4>
	<div class="cache-list">	
		<table class="table table-bordered table-hover table-condensed sprav-table">
		<?php 
		if ($this->folders) {
			foreach($this->folders as $folder){
				echo "<tr>";
				echo "<th colspan=\"3\">".Text::_(ucfirst($folder["filename"]))."</th>";
				echo "<td width=\"5\">
						<a class=\"deleteButton\" href=\"index.php?module=service&amp;task=deleteCacheFolder&amp;folder=".$folder["filename"]."\" onclick=\"javascript:if(confirm('".Text::_("Do you want to delete all files")." ".Text::_(ucfirst($folder["filename"]))." ?'))return true; else return false;\" title=\"".Text::_("Delete all")."\">
							<img width=\"1\" height=\"1\" src=\"/images/blank.gif\" class=\"delete\" alt=\"".Text::_("Delete")."\" />
						</a>
					 </td>";
				echo "</tr>";
				$filelist=$folder["filename"]."_files";
				if ($this->{$filelist}){
					echo "<tr>";
					echo "<th>".Text::_("Name")."</th>";
					echo "<th>".Text::_("File size")."</th>";
					echo "<th>".Text::_("Date")."</th>";
					echo "<th>&nbsp;</th>";
					echo "</tr>";
					foreach ($this->{$filelist} as $file){
						echo "<tr class=\"rows\">";
						echo "<td>".$file["filename"]."</td>";
						echo "<td>".$file["filesize"]."</td>";
						echo "<td>".$file["filedate"]."</td>";
						echo "<td align=\"center\">
								<a class=\"deleteButton\" href=\"index.php?module=service&amp;task=deleteCacheFile&amp;file=".$file["filename"]."&amp;folder=".$folder["filename"]."\" onclick=\"javascript:if(confirm('".Text::_("Do you want to delete")." ".$file["filename"]." ?'))return true; else return false;\" title=\"".Text::_("Delete")."\">
									<img width=\"1\" height=\"1\" src=\"/images/blank.gif\" class=\"delete\" alt=\"".Text::_("Delete")."\" />
								</a>
							</td>";
						echo "</tr>";
					}
				}
			}
		} else {
			echo "<tr><th class=\"cache_message\" colspan=\"3\">".Text::_("Cache absent")."</th></tr>";
		}
		?>
		</table>
	</div>
</div></div></div></div>