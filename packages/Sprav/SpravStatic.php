<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class SpravStatic {
	public static function renderBaseTemplate($_form, $_html_pan, $_table_class, $filtered, $_table_header_arr, $_table_body_arr, $_table_body_settings_arr, $_html_footer) {
		echo $_form;
		echo $_html_pan;
		echo "<div class=\"sprav_telo\">";
		if (count($_table_header_arr)) {
			echo "<table class=\"table table-bordered table-hover table-condensed ".$_table_class."\"><thead>\n";
			if ($filtered) { echo "<tr><th class=\"filter_msg\" colspan=\"".(count($_table_header_arr))."\"><div class=\"filtered\">".Text::_("Is filtered").": ".$filtered."</div></th></tr>\n"; }
			echo "<tr>\n";
			foreach($_table_header_arr as $_table_header) {
				$_width=$_table_header['width'];
				if(trim($_width))	{
					if (trim($_width)=="no") $_style="";
					elseif (trim($_width)=="auto") $_style="style=\"width: auto;\"";
					elseif (mb_substr($_width,mb_strlen($_width)-1,1)=="%") $_style=" style=\"width:".$_width.";\"";
					else $_style=" style=\"width:".$_width."px;\"";
				}	else $_style="style=\"width: auto;\"";
				echo "<th class=\"".$_table_header['class']."\" ".$_style." ".$_table_header['onclick'].">";
				echo "<div class=\"inner-".$_table_header['class'].($_table_header['orderby_class'] ? " ".$_table_header['orderby_class'] : "")."\">";
				echo $_table_header['html'];
				echo "</div></th>\n";
			}
			echo "</tr>\n";
			echo "</thead><tbody>";
			$rowNum=0;
			if (is_array($_table_body_arr)) {
				foreach($_table_body_arr as $row_num=>$_table_body) {
					$rowNum++; if ($rowNum % 2) $tr_class="odd";  else $tr_class="even";
					if(isset($_table_body_settings_arr[$row_num]) && isset($_table_body_settings_arr[$row_num]["row_class"])) $tr_class.=" ".$_table_body_settings_arr[$row_num]["row_class"];
					echo "<tr class=\"".$tr_class."\">\n";
					foreach($_table_body as $_cell) {
						if ($_cell['hidden']) continue;
						$_width=$_cell['width'];
						if(trim($_width))	{
							if (trim($_width)=="no") $_style="";
							elseif (trim($_width)=="auto") $_style=" style=\"width: auto;\"";
							elseif (mb_substr($_width,mb_strlen($_width)-1,1)=="%") $_style=" style=\"width:".$_width."; white-space:nowrap;\"";
							else $_style=" style=\"width:".$_width."px; white-space:nowrap;\"";
						}	else $_style=" style=\"width: auto;\"";
						if(defined("_ADMIN_MODE")&&is_array($_cell['html'])) {
							// возможно и не стоит, понадобится наверное только для шифрованных данных
							echo "<td class=\"".$_cell['class']."\"".$_style."><div class=\"inner-grid-array ".$_cell['class']."\"".$_cell['onclick'].">";
							if (count($_cell['html'])){
								foreach($_cell['html'] as $k=>$v){
									if ($v)	echo "<b>".Text::_($k)." : </b>".$v."<br />";
								}
							}
							echo "</div></td>";
						} else {
							echo  "<td class=\"".$_cell['class']."\"".$_style."><div class=\"inner-grid ".$_cell['class']."\" ".$_cell['onclick'].">".$_cell['html']."</div></td>";
						}
					}
					echo "</tr>\n";
				}
			} else { echo "<tr><td align=\"center\" colspan=\"".(count($_table_header_arr))."\">".Text::_("Does not contain the data")."</td></tr>"; }
			echo "</tbody></table>";
		}
		echo "</div>";
		echo $_html_footer;
		if($_form) echo "</form>";
	}
	public static function getCKArray($ck_label="") {
		$emptyArr=array("",Text::_("Data absent"));
		switch($ck_label){
			case "sign_vals":
				return array("+"=>"+","-"=>"-");
				break;
			case "sign_vals_ext":
				return array("+"=>"+","-"=>"-","*"=>"*");
				break;
			case "sign_vals_full":
					return array("+"=>"+","-"=>"-","*"=>"*","="=>Text::_("Absolute value"));
					break;
			case "add_field_type":
				return array(0=>Text::_("Common field"),1=>Text::_("Custom field"));
				break;
			case "sex":
				return array(0=>Text::_("Hide sex"),1=>Text::_("Female"),2=>Text::_("Male"));
				break;
			case "price_type":
				return array(1=>Text::_("Price 1"),2=>Text::_("Price 2"),3=>Text::_("Price 3"),4=>Text::_("Price 4"),5=>Text::_("Price 5"));
				break;
			case "side":
				return array(0=>Text::_("Front"),1=>Text::_("Admin"));
				break;
			case "rating":
				return array(1=>"1",2=>"2",3=>"3",4=>"4",5=>"5");
				break;
			case "menu_type":
				return array(0=>Text::_("Module"), 1=>Text::_("Link"), 2=>Text::_("Menu link"));
				break;
			case "link_target":
				return array("window"=>Text::_("Same window"),"popup"=>Text::_("Popup window"),"_blank"=>Text::_("New window"));
				break;
			case "monthArray":
				return Date::getMonthArrayStatic();
				break;
			case "yearsArray":
				return Date::getYearArrayStatic();
				break;
			case "order_direction":
				return array("ASC"=>Text::_("Ascending"),"DESC"=>Text::_("Descending"));
				break;
			case "vote_vals":
				return array("up"=>Text::_("Good"),"down"=>Text::_("Bad"));
				break;
			case "inc_switcher":
				return array(1=>Text::_("Included"),2=>Text::_("Excluded"));
				break;
			case "file_source":
				return array("1"=>Text::_("Local file"),"2"=>Text::_("Server file"),"3"=>Text::_("Http file"));
				break;
			case "measure_type":
				return array("0"=>Text::_("Quantity"), "1"=>Text::_("Volume"), "2"=>Text::_("Lenght"), "3"=>Text::_("Time"), "4"=>Text::_("Weight"), 6=>Text::_("Square"));
				break;
			case "contra_type":
				return array("0"=>Text::_("PR"), "1"=>Text::_("IP"), "2"=>Text::_("UR"));
				break;
			case "goods_type":
				return array("1"=>Text::_("Product"), "2"=>Text::_("Service"), "3"=>Text::_("EGoods"), "4"=>Text::_("Demo material"), "5"=>Text::_("Complect set"),"6"=>Text::_("Material"),
							"101"=>Text::_('Service element')." (".Text::_("Product").")",
							"102"=>Text::_('Service element')." (".Text::_("Service").")",
							"105"=>Text::_('Service element')." (".Text::_("Complect set").")",
							"106"=>Text::_('Service element')." (".Text::_("Material").")",
				);
				break;
			case "goods_option_type":
				return array(11=>Text::_("Option field"));
				break;
			case "sell_type":
				return array("0"=>Text::_("Base measure"), "1"=>Text::_("Pack measure"), "2"=>Text::_("Weight measure"), "3"=>Text::_("Volume measure"),/* "4"=>Text::_("Square measure"), "5"=>Text::_("Length measure")*/);
				break;
			case "time_interval":
				return array("0"=>Text::_("Cache disabled"),"1"=>Text::_("1 min"),"5"=>Text::_("5 min"),"15"=>Text::_("15 min"),"30"=>Text::_("30 min"),"60"=>Text::_("1 hour"),"180"=>Text::_("3 hour"),"360"=>Text::_("6 hour"),"720"=>Text::_("12 hour"),"1440"=>Text::_("24 hour"),"4320"=>Text::_("3 days"),"10080"=>Text::_("1 week"));
				break;
			case "blacklist_type":
				return array("ip"=>Text::_("IP"),"email"=>Text::_("E-mail"),"login"=>Text::_("Login"),"nickname"=>Text::_("Nickname"));
				break;
			case "opengraph_types":
				return array(
				array("id"=>"article", "name"=>"Article"),
				array("id"=>"bog", "name"=>"Bog"),
				array("id"=>"website", "name"=>"Website"),
				array("id"=>"activity", "name"=>"Activity"),
				array("id"=>"sport", "name"=>"Sport"),
				array("id"=>"bar", "name"=>"Bar"),
				array("id"=>"company", "name"=>"Company"),
				array("id"=>"cafe", "name"=>"Cafe"),
				array("id"=>"hoel", "name"=>"Hoel"),
				array("id"=>"restaurant", "name"=>"Restaurant"),
				array("id"=>"cause", "name"=>"Cause"),
				array("id"=>"sport_leauge", "name"=>"Sport Leauge"),
				array("id"=>"sport_team", "name"=>"Sport Team"),
				array("id"=>"band", "name"=>"Band"),
				array("id"=>"governement", "name"=>"Governement"),
				array("id"=>"non_profit", "name"=>"Non Profit"),
				array("id"=>"school", "name"=>"School"),
				array("id"=>"university", "name"=>"University"),
				array("id"=>"actor", "name"=>"Actor"),
				array("id"=>"athlete", "name"=>"Athlete"),
				array("id"=>"author", "name"=>"Author"),
				array("id"=>"director", "name"=>"Director"),
				array("id"=>"musician", "name"=>"Musician"),
				array("id"=>"poitician", "name"=>"Poitician"),
				array("id"=>"public_figure", "name"=>"Public Figure"),
				array("id"=>"city", "name"=>"City"),
				array("id"=>"country", "name"=>"Country"),
				array("id"=>"landmark", "name"=>"Landmark"),
				array("id"=>"state_province", "name"=>"State Province"),
				array("id"=>"album", "name"=>"Album"),
				array("id"=>"book", "name"=>"Book"),
				array("id"=>"drink", "name"=>"Drink"),
				array("id"=>"food", "name"=>"Food"),
				array("id"=>"game", "name"=>"Game"),
				array("id"=>"product", "name"=>"Product"),
				array("id"=>"song", "name"=>"Song"),
				array("id"=>"movie", "name"=>"Movie"),
				array("id"=>"tv_show", "name"=>"TV Show"),
				);
				break;
			case "upload_result_codes":
				return array(
					0 => "There is no error, the file uploaded with success",
					1 => "The uploaded file exceeds the upload_max_filesize directive in php.ini",
					2 => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
					3 => "The uploaded file was only partially uploaded",
					4 => "No file was uploaded",
					6 => "Missing a temporary folder",
					7 => "Failed to write file to disk.",
					8 => "A PHP extension stopped the file upload."
				);
				break;
			case "list_lang":
				$list_lang=array_keys(Text::getAllLanguages());
				return array_combine($list_lang, $list_lang);
				break;
			default:
				$userMDConfig=PATH_CONFIG.'metadata.php';
				if(file_exists($userMDConfig)) {
					require_once($userMDConfig);
					if (class_exists("customCKArray",false)){
						$userCKArray=customCKArray::getArray($ck_label);
						if(is_array($userCKArray)) return $userCKArray;
					}
				}
				return $emptyArr;
				break;
		}
	}
	public static function getValueFromCKArray($ck_label='',$val='')	{
		$array=self::getCKArray($ck_label);
		if((is_array($array))&&(isset($array[$val]))) return $array[$val];
		else return '';
	}
	public static function makeCKArray($data, $delimiter="", $html_encode=true) {
		if ($delimiter) {
			if ($html_encode) $_arr=explode($delimiter, htmlspecialchars_decode($data,ENT_QUOTES));
			else $_arr=explode($delimiter, htmlspecialchars_decode($data,ENT_QUOTES));
		} else $_arr = $data;
		if (!is_array($_arr)) return array();
		if ($html_encode) {
			foreach($_arr as $arr_key=>$arr_val) {
				$_arr[$arr_key]=htmlspecialchars(htmlspecialchars_decode($arr_val,ENT_QUOTES),ENT_QUOTES,DEF_CP);
			}
		}
		$arr=array_flip(array_diff(array_unique($_arr),array('')));
		foreach($arr as $arr_key=>$arr_val) {
			$arr[$arr_key]=$arr_key;
		}
		return $arr;
	}
}
?>