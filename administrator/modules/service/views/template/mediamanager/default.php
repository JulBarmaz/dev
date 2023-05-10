<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

?>
<div id="mediamanager">	<div class="container"><div class="row"><div class="col-md-12"><div class="media-manager rounded-pan">
	<h4 class="title"><?php echo Text::_("Media manager"); ?></h4>
	<?php if ($this->info_message)  echo "<div class=\"info_message ".$this->error_class."\">".$this->info_message."</div>"; ?>
	<div class="row">
		<div class="col-sm-6">
			<fieldset class="uploader">
				<legend><?php echo Text::_("Create folder"); ?></legend>
				<div class="row"><div class="col-md-12"><input class="form-control" type="text" id="newfolder" /></div></div>
				<div class="buttons"><input class="commonButton btn btn-info" type="button" onclick="mm_createFolder('<?php echo $this->folder; ?>')" value="<?php echo Text::_("Create"); ?>" /></div>
			</fieldset>
		</div>
		<div class="col-sm-6">
			<fieldset class="uploader">
				<legend><?php echo Text::_("Upload file"); ?></legend>
				<form id="mm_single_upload" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
					<div class="fileselector">
						<?php echo HTMLControls::renderInputFile("up_file", "", 38, "up_file"); ?>
					</div>
					<p><span class="warning"><?php echo Text::_("Max file size")." ".ini_get("upload_max_filesize"); ?></span></p>
					<input type="hidden" name="option" value="ajax" />
					<input type="hidden" name="task" value="createMedia" />
					<input type="hidden" name="module" value="service" />
					<input type="hidden" name="folder" value="<?php echo $this->folder; ?>" />
					<div class="upload-progress"><div class="bar"></div ><div class="percent">0%</div ></div><div id="status"></div>
					<div class="buttons"><input class="commonButton btn btn-info" type="submit" value="<?php echo Text::_("Upload"); ?>" /></div>
				</form>
			</fieldset>
		</div>
	</div>
	<div class="filemanager-wrapper">
	<?php
	echo "<table id=\"filemanager\" class=\"table table-bordered table-hover table-condensed sprav-table\">";
	echo "<tr>";
	echo "<td class=\"currentfolder\" colspan=\"6\"><b>".Text::_("Current folder").":</b> ".$this->filepath."</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<th width=\"5%\">&nbsp;</th>";
	echo "<th width=\"5%\">&nbsp;</th>";
	echo "<th>".Text::_("Name")."</th>";
	echo "<th width=\"15%\">".Text::_("File size")."</th>";
	echo "<th width=\"15%\">".Text::_("File date")."</th>";
	echo "<th width=\"10%\">".Text::_("Actions")."</th>";
	echo "</tr>";
	$files=$this->files;
	if ($files && count($files)){
		foreach ($files as $file) {
			$ext = strtolower(substr($file['filename'], strrpos($file['filename'], '.')+1));
			$filename = stripslashes($file['filename']);
			if (mb_strlen($filename,DEF_CP) > 43) {	$short_filename = mb_substr($file['filename'], 0, 40,DEF_CP) . '...';	}	 else $short_filename=$filename;
			echo "<tr>";
				$delete_js="javascript:if(confirm('".Text::_("Do you want to delete")." ".$filename." ?'))return true; else return false;";
				if ($file['folder']==1) {
					if ($filename=="..") echo "<td>&nbsp;</td>";
					else {
						echo "<td align=\"center\"><a title=\"".Text::_("Delete")."\" onclick=\"mm_deleteFolder('".$this->folder."','".$filename."')\">";
						echo "<img width=\"1\" height=\"1\" alt=\"Delete\" class=\"delete\" src=\"/images/blank.gif\" />";
						echo "</a></td>";
					}
					if ($filename=="..") {
						echo "<td>&nbsp;</td>";
						echo "<td><a title=\"".Text::_("Folder up")."\" onclick=\"mm_getContent('".$this->folder."','',1)\"><img class=\"folder_up\" width=\"1\" height=\"1\" alt=\"Folder Up\" src=\"/images/blank.gif\" /></a>&nbsp;&nbsp;&nbsp;&nbsp;<b>..</b></td>";
					}	else {
						echo "<td><img width=\"1\" height=\"1\" alt=\"Folder\" class=\"folder\" src=\"/images/blank.gif\" /></td>";
						echo "<td><a class=\"folder\" title=\"".Text::_("Open folder")."\" onclick=\"mm_getContent('".$this->folder."','".$filename."',0)\">".$short_filename."</a></td>";
					}
					echo "<td align=\"right\">".$file['filesize']." KB</td>";
					echo "<td align=\"center\">".$file['filedate']."</td>";
					echo "<td>&nbsp;</td>";
				} elseif ($file['folder']==0) {
					$href="index.php?module=service&amp;task=downloadFile&amp;folder=".rawurlencode($this->folder)."&amp;file=".rawurlencode($filename);
					echo "<td align=\"center\"><a title=\"".Text::_("Delete")."\"  onclick=\"mm_deleteFile('".$this->folder."','".$filename."')\">";
					echo "<img width=\"1\" height=\"1\" alt=\"Delete\" class=\"delete\" src=\"/images/blank.gif\" />";
					echo "</a></td>";
					echo "<td><img width=\"1\" height=\"1\" alt=\"File\" class=\"file\" src=\"/images/blank.gif\" /></td>";
					echo "<td><a title=\"".Text::_("Get link")."\" class=\"getlink\" onclick=\"mm_setLink('".$this->folder."','".$filename."')\">".$short_filename."</a></td>";
					echo "<td align=\"right\">".$file['filesize']." KB</td>";
					echo "<td align=\"center\">".$file['filedate']."</td>";
					echo "<td align=\"center\">";
					echo "<a title=\"".Text::_("Download")."\" target=\"_blank\" class=\"download\" href=\"".$href."\"><img width=\"1\" height=\"1\" alt=\"Get link\" class=\"getlink\" src=\"/images/blank.gif\" /></a>";
					if (Files::isImage($this->filepath.DS.$filename)) {
						$imagehref=BARMAZ_UF."/".Files::pathURLEncode($this->folder)."/".rawurlencode($filename);
						echo "&nbsp;<a target=\"_blank\" title=\"".Text::_("Preview")."\" class=\"preview relpopup\" href=\"".$imagehref."\"><img width=\"1\" height=\"1\" alt=\"Preview\" class=\"preview\" src=\"/images/blank.gif\" /></a>";
					} else echo "&nbsp;";
					echo "</td>";
				}
			echo "</tr>";
		}
	}
	echo "</table>";
	?>
	</div>
	<div id="medialink"></div>
</div></div></div></div></div>