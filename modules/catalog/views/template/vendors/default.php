<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$spr_tmpl_overrided=1;
$r_class="col-sm-4"
?>
<h1 class="title no_border"><?php echo Text::_("Goods vendors"); ?></h1>
<?php 
echo "<div class=\"row\">";
if (is_array($_table_body_arr)) {
	foreach($_table_body_arr as $_table_body) {
		echo "<div class=\"".$r_class."\"><div class=\"vendor-wrapper quadro-wrapper\">";
		$key="v_logo";
		if(array_key_exists($key, $_table_body)){ 
			$_cell=$_table_body[$key];
			if (!$_cell['hidden']) echo  "<div class=\"vendor-logo\">".$_cell['html']."</div>";
		}
		$key="v_store_name";
		if(array_key_exists($key, $_table_body)){ 
			$_cell=$_table_body[$key];
			if (!$_cell['hidden']) echo  "<div class=\"vendor-link\">".$_cell['html']."</div>";
		}
		echo "</div></div>";
	}
} else echo "<div class=\"col-sm-12\">".Text::_("Does not contain the data")."</div>"; 
echo "</div>";
if($records_count_wf > Module::getInstance()->getParam("Page_size")) echo $_html_footer;
?>