<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$spr_tmpl_overrided=1;
?>
<h1 class="title no_border"><?php echo Text::_("Compare list"); ?></h1>
<?php
//Util::showArray($this->goods_info_arr);
$js = "
function compare_check_if_table_is_empty(){
	if($('.compare-item-cell').length==0){
		$('.compare-table').remove();
	}
}
";
Portal::getInstance()->addScriptDeclaration($js);
$fields_arr = array();
$show_mode = 2;
$hidden_fields = array("g_id", "g_name", "g_thumb", "g_fullname", "g_comments", "g_medium_image", "g_image");
$hidden_types = array("filepath", "image");
if(count($this->goods_info_arr)){
	echo "<div class=\"compare-list\">";
	foreach ($this->goods_info_arr as $gk=>$gv){
		if(count($gv)){
			foreach($gv as $fk=>$fv){
				if(!$fv["hidden"] && $fv["html"]){
					//Util::ddump($fv);
					//if($fv["type"]=="float" && floatval($fv["value"])==0) continue;
					if($fv["type"]=="float" && floatval($fv["value"])==0){
						$this->goods_info_arr[$gk][$fk]["html"]="";
						continue;
					}
					$fields_arr[$fk]["title"]=$fv["title"];
					$fields_arr[$fk]["input_type"]=$fv["input_type"];
					$fields_arr[$fk]["type"]=$fv["type"];
				}
			}
		}
	}
	if(count($fields_arr)){
		switch($show_mode) {
			case "1": // goods as rows
				echo "<div class=\"table-responsive\">";
				echo "<table class=\"compare-table compare-table-horizontal table table-striped table-bordered table-hover\">";
				echo "<thead>";
				echo "<tr class=\"compare-header-row\">";
				echo "<th class=\"compare-header-cell\"></th>";
				echo "<th class=\"compare-header-cell\" colspan=\"2\">";
				echo $fields_arr["g_name"]["title"];
				echo "</th>";
				foreach ($fields_arr as $fld_key_h=>$fld_name_h){
					if(!in_array($fld_key_h, $hidden_fields) && !in_array($fld_name_h["input_type"], $hidden_types)){
						echo "<th class=\"compare-header-cell\">";
						echo $fld_name_h["title"]/*." (".$fld_key_h.")"*/;
						echo "</th>";
					}
				}
				echo "</tr>";
				echo "</thead>";
				echo "<tbody>";
				foreach ($this->goods_info_arr as $gk=>$gv){
					echo "<tr class=\"compare-item-row compare-item-row-".$gv["g_id"]["value"]."\" id=\"compare-item-row-".$gv["g_id"]["value"]."\">";
					echo "<td class=\"compare-item-cell compareButtons-wrapper-cell\">";
					echo "<div class=\"compareButtons inCompare\">";
					$onclick = "if(confirm('".Text::_("Are you sure")." ?')){removeFromCompare(this, '".$gv['g_id']['value']."', '.compare-item-row-".$gv['g_id']['value']."', 1);}";
					echo "<a title=\"".Text::_("Remove from compare")."\" onclick=\"".$onclick."\" class=\"removeFromCompare linkButton btn btn-info\"><i class=\"glyphicon glyphicon-remove\"></i></a>";
					echo "</div>";
					echo "</td>";
					echo "<td class=\"compare-item-cell\">";
					echo "<a href=\"".Router::_("index.php?module=catalog&view=goods&layout=info&psid=".$gv['g_id']['value']."&alias=".$gv['g_alias']['value'])."\">".$gv["g_name"]["html"]."</a>";
					echo "</td>";
					echo "<td class=\"compare-item-cell\">";
					echo $gv["g_thumb"]["html"];
					echo "</td>";
					foreach ($fields_arr as $fld_key=>$fld_name){
						if(!in_array($fld_key, $hidden_fields) && !in_array($fld_name["input_type"], $hidden_types)){
							// $cell_class = "";
							// if($fld_name["type"]=="boolean" || $fld_name["type"]=="currency" || $fld_name["type"]=="float") $cell_class = " text-center";
							echo "<td class=\"compare-item-cell text-center\">";
							if($gv[$fld_key]["html"]) echo $gv[$fld_key]["html"];
							echo "</td>";
						}
					}
					echo "</tr>";
				}
				echo "</tbody>";
				echo "</table>";
				echo "</div>";
				break;
			case "2": // goods as cols
				echo "<div class=\"table-responsive\">";
				echo "<table class=\"compare-table compare-table-vertical table table-striped table-bordered table-hover\">";
				echo "<tbody>";
				echo "<tr class=\"compare-item-row\" id=\"compare-item-row-g_name\">";
				echo "<th class=\"compare-header-cell\">";
				//echo $fields_arr["g_name"]["title"]." (g_name)";
				echo "</th>";
				foreach ($this->goods_info_arr as $gk=>$gv){
					echo "<td class=\"compare-item-cell compare-item-cell-".$gv['g_id']['value']."\">";
					echo "<a href=\"".Router::_("index.php?module=catalog&view=goods&layout=info&psid=".$gv['g_id']['value']."&alias=".$gv['g_alias']['value'])."\">";
					if(!$gv['g_name']['hidden']) {
						echo $gv["g_name"]["html"];
					} elseif(!$gv['g_fullname']['hidden']) {
						echo $gv["g_name"]["html"];
					}
					echo "</a>";
					echo "</td>";
				}
				echo "</tr>";
				echo "<tr class=\"compare-item-row\" id=\"compare-item-row-g_thumb\">";
				echo "<th class=\"compare-header-cell\">";
				//echo $fields_arr["g_thumb"]["title"]." (g_name)";
				echo "</th>";
				foreach ($this->goods_info_arr as $gk=>$gv){
					echo "<td class=\"compare-item-cell compare-item-cell-".$gv['g_id']['value']."\">";
					echo $gv["g_thumb"]["html"];
					echo "</td>";
				}
				echo "</tr>";
				foreach ($fields_arr as $fld_key=>$fld_name){
					if(!in_array($fld_key, $hidden_fields) && !in_array($fld_name["input_type"], $hidden_types)){
						echo "<tr class=\"compare-item-row\" id=\"compare-item-row-".$fld_key."\">";
						echo "<th class=\"compare-header-cell\">";
						echo $fld_name["title"]/*." (".$fld_key.")"*/;
						echo "</th>";
						foreach ($this->goods_info_arr as $gk=>$gv){
							echo "<td class=\"compare-item-cell compare-item-cell-".$gv['g_id']['value']."\">";
							//var_dump($fld_name["type"],$gv[$fld_key]["value"]);
							if($gv[$fld_key]["html"]) echo $gv[$fld_key]["html"];
							echo "</td>";
						}
						echo "</tr>";
					}
				}
				echo "<tr class=\"compare-item-row\" id=\"compare-item-row-g_thumb\">";
				echo "<th class=\"compare-header-cell\">";
				echo "</th>";
				foreach ($this->goods_info_arr as $gk=>$gv){
					echo "<td class=\"compare-item-cell-".$gv['g_id']['value']."\">";
					echo "<div class=\"compareButtons inCompare\">";
					$onclick = "if(confirm('".Text::_("Are you sure")." ?')){removeFromCompare(this, '".$gv['g_id']['value']."', '.compare-item-cell-".$gv['g_id']['value']."', 1);}";
					echo "<a title=\"".Text::_("Remove from compare")."\" onclick=\"".$onclick."\" class=\"removeFromCompare linkButton btn btn-info\">".Text::_("Remove from compare")."</a>";
					echo "</div>";
					echo "</td>";
				}
				echo "</tr>";
				echo "</tbody>";
				echo "</table>";
				echo "</div>";
				break;
		}
		//Util::showArray($fields_arr);
	}
	echo "</div>";
}


?>