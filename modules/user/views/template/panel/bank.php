<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$data=$this->bank;
if ($data["psid"]) $title=Text::_("Changing data"); else  $title=Text::_("Adding data"); 
?>
<form action="<?php echo Router::_("index.php"); ?>" method="post" name="bankForm" id="bankForm">
<input type="hidden"  name="task" value="saveBank" />
<input type="hidden"  name="module" value="user" />
<input type="hidden"  name="view" value="panel" />
<div class="userAddress">
<h3 class="title"><?php echo $title;?></h3>
<?php foreach($data as $key=>$val){ 
	echo "<div class=\"row\">";
	if ($key=="use_as_default") {
		echo "<div class=\"col-md-6\">".HTMLControls::renderLabelField($key, Text::_($key).":")."</div>";
		echo "<div class=\"col-md-6\">".HTMLControls::renderCheckbox("use_as_default",$data["use_as_default"])."</div>";
	} elseif ($key=="psid") {
		echo HTMLControls::renderHiddenField($key,$val);
	} else { 
		echo "<div class=\"col-md-6\">".HTMLControls::renderLabelField($key, Text::_("$key").":")."</div>";
		echo "<div class=\"col-md-6\"><input type=\"text\" class=\"form-control\" name=\"".$key."\"  id=\"".$key."\" value=\"".$val."\" /></div>";
	}
	echo "</div>";
} ?>

	<div class="buttons">
		<input class="commonButton btn btn-info" type="submit" name="submit"  value="<?php echo Text::_("Save"); ?>" />
	</div>
</div>
</form>