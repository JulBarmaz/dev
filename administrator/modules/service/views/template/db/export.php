<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

?>
<div class="container"><div class="row"><div class="col-md-12"><div class="service-manager rounded-pan rounded-pan-medium">
	<h4 class="title"><?php echo Text::_("Backup tables"); ?></h4>
	<form action="index.php" method="post">
		<table class="table table-bordered table-hover table-condensed sprav-table">
		<tr><td>
		<?php if (count($this->tables)) {
			echo HTMLControls::renderButton("start",Text::_("Export now"),"submit"); 
			echo HTMLControls::renderHiddenField("module","service");
			echo HTMLControls::renderHiddenField("view","db");
			echo HTMLControls::renderHiddenField("file",$this->file);
			echo HTMLControls::renderHiddenField("task","exportTables");	
		?>
		</td>
		<td width="5">
			<?php echo HTMLControls::renderCheckbox("",1,1,"toggleAll","","toggleChecked(this,'checkup')"); ?>
		</td></tr>
		<tr><td colspan="2">
			<?php echo Text::_("Path"); ?>: <?php echo $this->backup_path.$this->file; ?>
		</td></tr>
		<?php foreach ($this->tables as $tbl) { ?>
			<tr><th><?php echo HTMLControls::renderLabelField($tbl."_checkup", $tbl); ?></th>
			<td><?php echo HTMLControls::renderCheckbox($tbl."_mode", 1, 1, $tbl."_checkup", "checkup"); ?></td></tr>
			<?php } 
		} ?>
		</table>
	</form>
</div></div></div></div>