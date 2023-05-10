<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$data=$this->company;
$title=Text::_("Changing data");
?>
<form action="<?php echo Router::_("index.php"); ?>" method="post" name="companyForm" id="companyForm">
<input type="hidden"  name="task" value="saveCompany" />
<input type="hidden"  name="module" value="user" />
<input type="hidden"  name="view" value="panel" />
<div class="userAddress">
<h3 class="title"><?php echo $title;?></h3>
<?php foreach($data as $key=>$val){
	echo "<div class=\"row\">";
	echo "<div class=\"col-md-6\">".HTMLControls::renderLabelField($key,Text::_($key).":")."</div>";
	if ($key=="doc_date") {
		echo "<div class=\"col-md-6\">".HTMLControls::renderDateTimeSelector($key, $val)."</div>";
	} elseif ($key=="org_type"){
		echo "<div class=\"col-md-6\">".Userdata::renderOrgTypeSelector($val)."</div>";
	} elseif (in_array($key, array("phone", "fax", "phone_mobile", "phone_mobile_2"))){
		echo "<div class=\"col-md-6\"><input type=\"text\" class=\"form-control phone\" name=\"".$key."\"  id=\"".$key."\" value=\"".$val."\" /></div>";
	} else { 
		echo "<div class=\"col-md-6\"><input type=\"text\" class=\"form-control\" name=\"".$key."\"  id=\"".$key."\" value=\"".$val."\" /></div>";
	} 
	echo "</div>";
} ?>

	<div class="buttons">
		<input class="commonButton btn btn-info" type="submit" name="submit"  value="<?php echo Text::_("Save"); ?>" />
	</div>
</div>
</form>

