<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class HTMLControls extends BaseObject {
	private static $unificator=0;
	private static $elid=0;

	public static function renderBalloonButton($text,$translate=true){
		if ($translate) $text=Text::_($text);
		$html="<div title=\"".$text."\" class=\"balloon_button\"></div>";
		return $html;
	}
	public static function renderPopupMultySelect($name, $data, $key_fld="", $name_fld="", $link_add="", $js_add="",$js_inf='',$isNewLink=false) {
		return self::renderPopupMultySelectWQ($name, $data, false, $key_fld, $name_fld, "", $link_add, $js_add, $js_inf,$isNewLink);
	}
	public static function renderPopupMultySelectWQ($name, $data, $withQuantity=false, $key_fld="", $name_fld="", $quant_fld="", $link_add="", $js_add="",$js_inf='',$isNewLink=false) {
		if (!$key_fld) $key_fld="id";
		if (!$name_fld) $name_fld="title";
		if (!$quant_fld) $quant_fld="quantity";
		$html="<div id=\"".$name."\" class=\"multyselector\">";

		$html.="<input type=\"hidden\"  id=\"".$name."_delete\" name=\"".$name."_delete\" value=\"".Text::_("Delete")."\" />";
		$html.="<input type=\"hidden\" id=\"".$name."_exists\" name=\"".$name."_exists\" value=\"".Text::_("Already added")."\" />";
		$html.="<input type=\"hidden\" id=\"".$name."_wq\" name=\"".$name."_wq\" value=\"".($withQuantity ? 1 : 0)."\" />";
		if($link_add) {
			$html.="<p class=\"add-but\"><a class=\"spravselector\" href=\"".$link_add."\">".Text::_("Add")."<img alt=\"+\" title=\"".Text::_("Add")."\" class=\"add-but\" src=\"/images/blank.gif\" width=\"1\" height=\"1\" /></a></p>";
		}	elseif ($js_add) {
			$html.="<p class=\"add-but\"><a onclick=\"".$js_add."\">".Text::_("Add")."<img alt=\"+\" title=\"".Text::_("Add")."\" class=\"add-but\" src=\"/images/blank.gif\" width=\"1\" height=\"1\" /></a></p>";
		}
		if (is_array($data) && count($data)) {
			foreach($data as $elem){
				$current_id=$name."_".htmlspecialchars($elem[$key_fld]);
				if ($isNewLink) $html.="<p class=\"newline float-fix\">"; else $html.="<p class=\"float-fix\">";
				$html.="<img width=\"1\" height=\"1\" alt=\"-\" title=\"".Text::_("Delete")."\" onclick=\"deleteMultyRow('".$current_id."','".Text::_("Delete")."')\" class=\"delete-but\" src=\"/images/blank.gif\" />";
				$html.=self::renderHiddenField($name."[]",$elem[$key_fld],$current_id);
				$cur_js_inf=sprintf($js_inf,$elem[$key_fld]);
				$html.=self::renderLabelField($current_id,$elem[$name_fld],0,$cur_js_inf);
				//if($withQuantity) $html.=self::renderInputText($name."_quantity[]", $val="",$size="",$length="", $id="", $class='',$readonly=false,$required=false,$title='');
				if($withQuantity) $html.=self::renderInputText($name."_quantity[".$elem[$key_fld]."]", $elem[$quant_fld], "", "", $name."_quantity_".$elem[$key_fld], 'quantity numeric form-control');
				$html.="</p>";
			}
		}
		$html.="</div>";
		return $html;
	}
	public static function renderAddressPanel($name, $data, $id, $class, $readonly, $module){
		$html="<div class=\"addressselector\">";
		$html.=self::renderImageButton(false, "", false, 'addressbutton', "showAddressEditor('".$id."'".($module ? ",'".$module."'" : "").")"); 
		$html.=self::renderHiddenField($name,base64_encode(json_encode($data)),$id);
		$html.=self::renderLabelField($id,$data["fullinfo"]);
		$html.="</div>";
		return $html;
	}
	public static function renderCheckbox($name, $checked_val="", $val="1", $id="", $class="", $js="") {
		if(!$id) $id=$name;
		$html="<input type=\"checkbox\" id=\"".$id."\" name=\"".$name."\" value=\"".$val."\"";
		if ($class) $html.= " class=\"".$class."\"";
		if($checked_val==$val) $html.=" checked=\"checked\"";
		if($js) $html.=" onclick=\"javascript:".$js.";\"";
		$html.=" />";
		return $html;
	}

	public static function renderInputText($name, $val="",$size="",$length="", $id="", $class='form-control',$readonly=false,$required=false,$title='',$js_arr=array(),$placeholder='') {
		if($id===false)
			$id_text='';
			else {
				if(!$id) $id=$name;
				$id_text="id=\"".$id."\"";
			}
			if($class!==false && !$class) $class='form-control';
			if ($readonly) $readonly=" readonly=\"readonly\""; else $readonly="";
			$html="<input type=\"text\" ".$id_text.$readonly." value=\"".htmlspecialchars(htmlspecialchars_decode($val))."\" name=\"".$name."\"";
			if ($size) $html.= " size=\"".$size."\"";
			if ($class) $html.= " class=\"".$class."\"";
			if ($length) $html.=" maxlength=\"".$length."\"";
			if ($title) $html.=" title=\"".$title."\"";
			if ($required) $html.=" required=\"required\"";
			if ($placeholder) $html.=" placeholder=\"".$placeholder."\"";
			
			if(count($js_arr)){
				foreach ($js_arr as $act=>$func) $html.=" ".$act."=\"".$func."\"";
			}
			$html.=" />";
			return $html;
	}
	
	public static function renderFileSelector($name, $val="",$size="",$length="", $id="", $class='',$readonly=false,$required=false,$title='') {
		if($id===false)
			$id_text='';
		else {
			if(!$id) $id=$name;
			$id_text="id=\"".$id."\"";
		}
		if ($readonly) $readonly=" readonly=\"readonly\""; else $readonly="";
		$html="<div class=\"wrapper-filepath\"><input type=\"text\" ".$id_text.$readonly." value=\"".htmlspecialchars(htmlspecialchars_decode($val))."\" name=\"".$name."\"";
		if ($size) $html.= " size=\"".$size."\"";
		if ($class) $html.= " class=\"".$class."\"";
		if ($length) $html.=" maxlength=\"".$length."\"";
		if ($title) $html.=" title=\"".$title."\"";
		if ($required) $html.=" required=\"required\"";
		$html.=" />";
		$html.="<a class=\"relpopupwt filepath_selector\" href=\"index.php?module=service&amp;task=startMediamanager&amp;nfl=-1&amp;ret_elem=".$id."\">".self::renderImageButton("", "", "", 'file_selector', "")."</a></div>";
		return $html;
	}
	
	public static function renderInputFile($name, $val="",$size=20, $id="", $class='', $required=false) {
		if($id===false)
			$id_text='';
		else {
			if(!$id) $id=$name;
			$id_text="id=\"".$id."\"";
		}
		$html="<input type=\"file\" ".$id_text." size=\"".$size."\" value=\"".$val."\" name=\"".$name."\"";
		if ($required) $html.=" required=\"required\"";
		if ($class) $html.= " class=\"".$class."\"";
		$html.=" />";
		return $html;
	}

	public static function renderDateSelector($name, $val="", $with_time=false, $id="", $class='form-control') {
		Debugger::getInstance()->warning("Function renderDateSelector is deprecated");
		if($id===false) $id_text='';
		else {
			if(!$id) $id=$name;
			$id_text="id=\"".$id."\"";
		}
		$html="<div class=\"wrapper-datetimeselector\"><input type=\"text\" ".$id_text." maxlength=\"".($with_time ? "16" : "10")."\" size=\"".($with_time ? "16" : "10")."\" value=\"".$val."\" name=\"".$name."\"";
		if ($class) $html.= " class=\"".($with_time ? "datetimeselector" : "dateselector")." ".$class."\"";
		else $html.=  " class=\"".($with_time ? "datetimeselector" : "dateselector")."\"";
		$html.=" />";
		$html.="<img width=\"1\" height=\"1\" class=\"date_selector\" src=\"/images/blank.gif\" title=\"".Text::_("Select date")."\" alt=\"D\" /></div>";
		return $html;
	}

	public static function renderDateTimeSelector($name, $val="", $with_time=false, $with_date=false, $required=false, $id="", $class='form-control') {
		if($id===false) $id_text='';
		else {
			if(!$id) $id=$name;
			$id_text="id=\"".$id."\"";
		}
		if($with_date && $with_time) $class.=($class ? " " : "")."datetimeselector";
		elseif($with_date && !$with_time) $class.=($class ? " " : "")."dateselector";
		elseif(!$with_date && $with_time) $class.=($class ? " " : "")."timeselector";
		$html="<div class=\"wrapper-datetimeselector\"><input type=\"text\" ".$id_text." maxlength=\"".($with_time ? "16" : "10")."\" size=\"".($with_time ? "16" : "10")."\" value=\"".$val."\" name=\"".$name."\" class=\"".$class."\"".($required ? " required=\"required\"" : "")." />";
		$html.="<img width=\"1\" height=\"1\" class=\"date_selector\" src=\"/images/blank.gif\" title=\"".Text::_("Select date")."\" alt=\"D\" /></div>";
		return $html;
	}
	public static function renderButton($name, $val="",  $type="button",  $id="", $class='commonButton btn btn-info',$js='',$title="") {
		if($id===false) $id_text='';
		else {
			if(!$id) $id=$name;
			$id_text="id=\"".$id."\"";
		}
		if($js) $js=" onclick=\"javascript:".$js.";\"";
		if($title) $title=" title=\"".$title."\"";
		$html="<input type=\"".$type."\" ".$id_text." value=\"".htmlspecialchars(htmlspecialchars_decode($val))."\" name=\"".$name."\"";
		if ($class) $html.= " class=\"".$class."\"";
		$html.=" ".$js.$title." />";
		return $html;
	}

	public static function renderImageButton($name, $title="", $id="", $class='button',$js='') {
		if($id===false)  $id="";
		else {
			if(!$id) {
				if($name===false) $id=""; else $id=$name;
			}
		}
		$html="<img width=\"1\" height=\"1\" src=\"/images/blank.gif\"";
		if($name) $html.= " name=\"".$name."\"";
		if ($id) $html.= " id=\"".$id."\"";
		if ($class) $html.= " class=\"".$class."\"";
		if($js) $html.=" onclick=\"javascript:".$js.";\"";
		if($title) $html.=" title =\"".$title."\"";
		$html.=" alt=\"\"  />";
		return $html;
	}

	public static function renderIcon($class, $size=16) {
		$html="<img width=\"".$size."\" height=\"".$size."\" src=\"/images/blank.gif\" class=\"".$class."\" alt=\"\" />";
		return $html;
	}
	public static function renderRadio($name, $id, $val="", $label="",$class="", $checked=true, $js='',  $required=0){
		$html="<input type=\"radio\" value=\"".$val."\" name=\"".$name."\"";
		if($id) $html.= " id=\"".$id."\"";
		if ($checked) $html.= " checked=\"checked\"";
		if ($required) $required_txt=" required=\"required\""; else $required_txt="";
		if ($class) $html.= " class=\"".$class."\"";
		if ($js) $html.= " onchange=\"".$js."\"";
		$html.=$required_txt." />";
		if($label) $html.=self::renderLabelField($id, $label);
		return $html;
	}
	public static function renderYesNoRadioButton($name,$id,$value=1,$label_yes='',$label_no='', $class='',$js="") {

		if(!$label_yes) $label_yes=Text::_("Yes");
		if(!$label_no) $label_no=Text::_("No");
		$checked_yes=$value; $checked_no=!$value;
		$html=self::renderRadio($name, $name."_yes", 1, $label_yes ,$class,$checked_yes,$js);
		$html.=self::renderRadio($name, $name."_no", 0, $label_no ,$class,$checked_no,$js);
		return $html;
	}

	public static function renderLabelField($id="", $value="", $forceTranslation=0, $js="", $class="") {
		if($js) $js=" title=\"".Text::_("Update")."\" style=\"cursor:pointer;\" onclick=\"javascript:".$js.";\"";
		if($forceTranslation) $value=Text::_($value);
		if($id)	return "<label class=\"label".($class ? " ".$class : "")."\"".$js." for=\"".$id."\">".$value."</label>";
		else return "<span class=\"fake_label".($class ? " ".$class : "")."\"".$js.">".$value."</span>";
	}

	public static function renderHiddenField($name="", $val="", $id="", $class="") {
		if($id===false) $id_text='';
		else {
			if(!$id) $id=$name;
			$id_text="id=\"".$id."\"";
		}
		if ($class) $class="class=\"".$class."\"";
		return "<input type=\"hidden\" ".$id_text." value=\"".htmlspecialchars(htmlspecialchars_decode($val))."\" name=\"".$name."\" ".$class." />";
	}
    // микроразметка добавлена
	public static function renderImage($link="", $noimage=false, $width=0, $height=0, $title="", $alt="", $default_dims=false, $props=false) {
		if ($link) {
			$src=" src=\"".$link."\"";
		} else {
			if ($noimage===false) $src="";
			elseif ($noimage) $src=" src=\"".$noimage."\"";
			else $src=" src=\"/images/nophoto.png\"";
		}
		if (!$width && !$height && $default_dims) {	// размеры из галереи, она ближе всех к картинкам
			$width=galleryConfig::$thumbWidth;
			//$height=galleryConfig::$thumbHeight;
		}
		if ($width) $width=" width=\"".$width."\""; else $width="";
		if ($height) $height=" height=\"".$height."\""; else $height="";
		if ($title) $title=" title=\"".htmlspecialchars(htmlspecialchars_decode($title))."\"";
		if ($props) $props=" itemprop=\"image\"";
		$_html="<img".$title.$width.$height.$src.$props." alt=\"".$alt."\" />";
		return $_html;
	}
	public static function renderFlash($link="", $width=0, $height=0) {
		if ($link&&$width&&$height) {
			$_html='<object type="application/x-shockwave-flash" data="'.$link.'"';
			if ($width) $_html.=' width ="'. $width.'"';
			if ($height) $_html.=' height ="'. $height.'"';
			$_html.='>';
			$_html.='<param name="movie" value="'.$link.'" />';
			$_html.='<param name="quality" value="high" />';
			$_html.='<param name="wmode" value="transparent" />';
			$_html.='</object>';
		} else $_html="";
		return $_html;
	}
	public static function renderRating($rating=0,$max_raiting=5) {
		$html="<div class=\"stars\">";
		for ($i = 1; $i <= $max_raiting; $i++) {
			if (($rating<$i)&&($rating>($i-1))) {
				$img="rating_half.gif";
			}
			elseif($rating<$i) {
				$img="rating_off.gif";
			}
			else {$img="rating_on.gif";
			}
			$imgpath='/templates/'.Portal::getInstance()->getTemplate().'/images/'.$img;
			$html.="<img src=\"".$imgpath."\" alt=\"\" />";
		}
		$html.="</div>";
		return $html;
	}

	/**
	 *
	 * Формирование списка по массивам разных типов. Определяется передаваемыми данными.
	 * @param _name - имя поля
	 * @param _id - ид поля
	 * @param _key_fld - имя поля (value option) в элементе массива( в объекте) если пусто - работает по обычному варианту ассоциативного массива
	 * @param _val_fld - имя поля (title option) в элементе массива( в объекте) если пусто - работает по обычному варианту ассоциативного массива
	 * @param _arr - массив данных формата массив объектов
	 * @param _sel_val - выбранное значение(ключ)
	 * @param _zero_fill -  добавлять базовое (пустое значение)
	 * @param _js - функиця обработки
	 * @param multiple - ждет значения одновременно видимых элементов, 0 - отключен
	 * @param class - класс css
	 */
	public static function renderSelectColored($_name, $_id, $_key_fld, $_val_fld, $_arr, $_arr_classes, $_sel_val="", $_zero_fill=true, $_js="", $multiple=0, $class="", $required=false) {
		if($_id===false) $id_text='';
		else {
			if(!$_id) $_id=$_name;
			$id_text="id=\"".$_id."\"";
		}
		$selected=" selected=\"selected\"";
		if ($multiple) {
			if(!is_array($_sel_val)) $_sel_val= preg_split('/[\;]/', $_sel_val);
			$multiple = " multiple=\"multiple\" size=\"".$multiple."\""; $_name=$_name."[]";
			$_class = "multiSelect form-control";
		} else {
			if(!is_array($_sel_val)) $_sel_val= array((string)$_sel_val);
			$multiple = "";
			$_class = "singleSelect form-control";
		}
		if ($class) $_class.= " ".$class;
		if ($required) $required_txt=" required=\"required\""; else $required_txt="";
		
		$_html ="<select class=\"".$_class."\" ".$multiple." name=\"".$_name."\" ".$id_text." ".$required_txt;
		if(is_array($_js) && count($_js)){
			foreach ($_js as $act=>$func) $_html.=" ".$act."=\"".$func."\"";
		} else if($_js){
			$_html.=($_js ? "onchange=\"".$_js."\"" : "");
		}
		$_html.=">";
		$_html_options="";
		if (count($_arr)>0) {
			foreach($_arr as $key=>$val) {
				if (is_object($val)) {
					if(strval($val->{$_key_fld})==="0") $_zero_fill=false;
					$_html_options.="<option".(isset($_arr_classes[$key]) ? " class=\"".$_arr_classes[$key]."\"" : "")." value=\"".$val->{$_key_fld}."\"".(in_array($val->{$_key_fld}, $_sel_val) ? $selected : "").">".$val->{$_val_fld}."</option>";
				} else {
					if ($_key_fld && $_val_fld) {
						if(strval($val[$_key_fld])==="0") $_zero_fill=false;
						$_html_options.="<option".(isset($_arr_classes[$key]) ? " class=\"".$_arr_classes[$key]."\"" : "")." value=\"".$val[$_key_fld]."\"".(in_array($val[$_key_fld], $_sel_val) ? $selected : "").">".$val[$_val_fld]."</option>";
					} elseif ($_val_fld) {
						if(strval($key)==="0") $_zero_fill=false;
						$_html_options.="<option".(isset($_arr_classes[$key]) ? " class=\"".$_arr_classes[$key]."\"" : "")." value=\"".$key."\"".(in_array($key, $_sel_val) ? $selected : "").">".$val[$_val_fld]."</option>";
					} else {
						if(strval($key)==="0") $_zero_fill=false;
						$_html_options.="<option".(isset($_arr_classes[$key]) ? " class=\"".$_arr_classes[$key]."\"" : "")." value=\"".$key."\"".(in_array($key, $_sel_val) ? $selected : "").">".$val."</option>";
					}
				}
			}
		}
		if($_zero_fill) $_html.="<option class=\"no_selection\" value=\"0\"".($_sel_val==0 ? $selected : "")."> --- ".Text::_("Not selected")." --- </option>";
		$_html.=$_html_options;
		$_html.="</select>";
		return $_html;
	}
	public static function renderSelect($_name, $_id, $_key_fld, $_val_fld, $_arr, $_sel_val="", $_zero_fill=true, $_js="", $multiple=0, $class="", $required=false) {
		return self::renderSelectColored($_name, $_id, $_key_fld, $_val_fld, $_arr, array(), $_sel_val, $_zero_fill, $_js, $multiple, $class, $required);
	}

	public static function renderRadioGroup($_name, $_id, $_key_fld, $_val_fld, $_arr, $_sel_val=0, $_js="", $class="radio", $required=false) {
		if($_id===false) $id_text='';
		else {
			if(!$_id) $_id=$_name;
			$id_text="id=\"".$_id."\"";
		}
		$_selection=preg_split('/[\;]/',$_sel_val);
		$_class = "radio";
		if ($class) $_class.= " ".$class;
		$_html="";
		if (count($_arr)>0) {
			$i=0;
			foreach($_arr as $key=>$val) {
				$i++;
				if (is_object($val)) {
					$_html.="<div class=\"radio\">".self::renderRadio($_name, $_name."_".$i, $val->{$_key_fld}, $val->{$_val_fld}, $_class, (in_array($val->{$_key_fld}, $_selection) ? true : false), $_js, $required)."</div>";
				} else {
					if ($_key_fld && $_val_fld) {
						$_html.="<div class=\"radio\">".self::renderRadio($_name, $_name."_".$i, $val[$_key_fld], $val[$_val_fld], $_class, (in_array($val[$_key_fld], $_selection) ? true : false), $_js, $required)."</div>";
					} elseif ($_val_fld) {
						$_html.="<div class=\"radio\">".self::renderRadio($_name, $_name."_".$i, $key, $val[$_val_fld], $_class, (in_array($key, $_selection) ? true : false), $_js, $required)."</div>";
					} else {
						$_html.="<div class=\"radio\">".self::renderRadio($_name, $_name."_".$i, $key, $val, $_class, (in_array($key, $_selection) ? true : false), $_js, $required)."</div>";
					}
				}
			}
		}
//		$_html.="</select>";
		return $_html;
	}
	
	public static function renderStaticHeader($title='', $keywords='', $description='',$stylesheets=null,$scripts=null,$metanames=null,$lang=null) {
		if(!$lang) $lang=Text::getLanguage();
		$_html='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$lang.'-'.$lang.'" lang="'.$lang.'-'.$lang.'">
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<meta http-equiv="Pragma" content="no-cache" />
				<meta http-equiv="cache-control" content="no-cache" />
				<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
				<meta name="description" content="'.$description.'" />
				<meta name="keywords" content="'.$keywords.'" />'.CR_LF;
		$array_default=array("viewport"=>"width=device-width, initial-scale=1.0, maximum-scale=1.0",
				"description"=>"'.$description.'",
				"keywords"=>"'.$keywords.'"
		);
		if (is_array($metanames)){
			foreach($metanames as $key_meta=>$val_meta) {
				if(!array_key_exists($key_meta, $array_default))
					$_html.='<meta name="'.$key_meta.'" content="'.$val_meta.'" />'.CR_LF;
			}
		}
		if (siteConfig::$enableGeneratorMetaTag) $_html.='<meta name="generator" content="Barmaz erp" />'.CR_LF;
		$_html.='<title>'.$title.'</title>'.CR_LF;
		if (is_array($stylesheets)){
			foreach($stylesheets as $style) {
				$_html.=	'<link rel="stylesheet" href="'.$style.'" type="text/css" />'.CR_LF;
			}
		}
		if (is_array($scripts)){
			foreach($scripts as $script) {
				$_html.=	'<script  src="'.$script.'"></script>'.CR_LF;
			}
		}
		$_html.=	'</head>
				<body>
				';
		return $_html;
	}
	public static function renderStaticFooter() {
		$_html='</body></html>';
		return $_html;
	}

	public static function renderEditor($id,$taName='',$editorText='',$toolbar='Default',$height=0,$width=0,$params=array(), $class='editorArea',$taTitle='',$placeholder='') {
		if(!$taName) $taName=$id;
		if (array_key_exists("cols",$params)) $cols=$params["cols"]; else $cols=80;
		if (array_key_exists("rows",$params)) $rows=$params["rows"]; else $rows=10;
		$params["edName"]=$taName;
		if($height) $params["height"]=$height;
		if($width) $params["width"]=$width;
		$params["toolbar"]=$toolbar;
		Event::raise("editor.render",$params);
		$editorHTML = "<textarea name=\"".$taName."\" title=\"".$taTitle."\"  placeholder=\"".$placeholder."\"   cols=\"".$cols."\" class=\"".$class."\" rows=\"".$rows."\" id=\"".$id."\">".htmlentities($editorText,ENT_COMPAT,DEF_CP)."</textarea>";
		return $editorHTML;
	}
	
	public static function renderBBCodeEditor($id, $name='',$val='', $cols=35, $rows=7, $class='form-control',$readonly=false,$required=false,$title='',$placeholder='') {
		if(!$name) $name=$id;
		$html = "<textarea name=\"".$name."\" cols=\"".$cols."\"";
		if ($class) $html.= " class=\"".$class."\"";
		if ($required) $html.=" required=\"required\"";
		if ($title) $html.=" title=\"".$title."\"";
		if ($readonly) $html.=" readonly=\"readonly\"";
		if ($placeholder) $html.=" placeholder=\"".$placeholder."\"";
		$html.= "rows=\"".$rows."\" id=\"".$id."\">".$val."</textarea>";
		return $html;
	}
	public static function hideEmail($email,$class="",$replace_with_link=true){
		if ($class) $class=" class=\"".$class."\"";
		if (rand(0,1)) $el="span"; else $el="p";
		self::$unificator=self::$unificator+1;
		if (!self::$elid) self::$elid=md5(time());
		$elid=self::$elid.self::$unificator;
		$rand=rand(0,3);
		switch ($rand) {
			case 0: $dot=" tutadot ";		$at=" predostavlen ";	break;
			case 1: $dot=" zdesenot ";	$at=" na servere "; 	break;
			case 2: $dot=" chtoto ";		$at=" sponsor ";			break;
			case 3: $dot=" tochka ";		$at=" sobaka "; 			break;
		}

		$email=str_replace(".", $dot, $email);
		$email=str_replace("@", $at, $email);
		$newmail = "<".$el." id=\"".$elid."\">".$email."</".$el.">";
		$js="$(document).ready(function(){
				var chego = $('".$el."#".$elid."');
						var gdemy = /".$at."/;
								var razdelili = /".$dot."/g;
										var zamenit = $(chego).text().replace(gdemy,'@').replace(razdelili,'.');";
		if ($replace_with_link)	$js.="$(chego).after('<a".$class." href=\"mailto:'+zamenit+'\">'+ zamenit +'</a>');";
		else 	$js.="$(chego).after(zamenit);";
		$js.="$(chego).remove();
	});";
		Portal::getInstance()->addScriptDeclaration($js);
		return $newmail;
	}
	public static function renderParamsPanel($name, $title, $mask, $params, $active_tab=1){
		$tabs_count = 1; $tab=1; $new_tab=1; $html=array(); $html_tabs=array(); $_html="";
		foreach ($mask as $prmName=>$prmType) {
			if($prmType["vtype"]=="tab") $tabs_count++;
		}
		if($tabs_count < $active_tab) $active_tab=1;
		$html[$tab]="";
		if(!$title) $title = "Main settings";
		$html_tabs[$tab] = Text::_($title);
		if (count($mask)) {
			foreach ($mask as $prmName=>$prmType) {
				if($new_tab != $tab) {
					$tab=$new_tab;
					$html[$tab]="";
				}
				if ($prmName) {
					if($prmType["vtype"]=="title"){
						$html[$tab].="<div class=\"row params-".$prmType["vtype"]."\"><div class=\"col-sm-12\">";
					} elseif($prmType["vtype"]!="tab") {
						$html[$tab].="<div class=\"row params-".$prmType["vtype"]."\"><div class=\"col-sm-5\">";
						$html[$tab].="<label for=\"".$prmName."\">".Text::_(str_replace("_"," ",$prmName))."</label>";
						if(isset($prmType["descr"])&&$prmType["descr"]) $html[$tab].=HTMLControls::renderBalloonButton($prmType["descr"], false);
						$html[$tab].="</div><div class=\"col-sm-7\">";
					}
					if ((is_array($params))&&(array_key_exists($prmName,$params))) {
						$val = $params[$prmName];
						if(!$val && isset($prmType["fill_default"]) && $prmType["fill_default"]) $val = $prmType["vdefault"];
					} else $val = $prmType["vdefault"];
					switch ($prmType["vtype"]) {
						case "boolean":
							if ($val) $checked = "checked=\"checked\" ";
							else $checked = "";
							$html[$tab].="<input id=\"".$prmName."\" name=\"".$name."[".$prmName."]\" type=\"checkbox\" ".$checked." value=\"1\" />";
							break;
						case "table_select":
						case "table_multiselect":
							if(isset($prmType["source"])) {
								Database::getInstance()->setQuery($prmType["source"]);
								$_source=Database::getInstance()->loadObjectList();
								$html[$tab].=HTMLControls::renderSelect($name."[".$prmName."]", $prmName, "fld_id", "fld_name", $_source, $val, !($prmType["vdefault"] || $prmType["vtype"]=="table_multiselect"), (isset($prmType["vjs"])&&$prmType["vjs"] ? $prmType["vjs"]:""), ($prmType["vtype"]=="table_multiselect" ? 10 : 0));
							}	else $html[$tab].="<input class=\"form-control\" id=\"".$prmName."\" name=\"".$name."[".$prmName."]\" type=\"text\" value=\"".$val."\" />";
							break;
						case "select":
						case "multiselect":
						case "select_method":
						case "multiselect_method":
							if(isset($prmType["source"]) && is_array($prmType["source"])) {
								reset($prmType["source"]);
								if(is_array(current($prmType["source"])) || is_object(current($prmType["source"]))) $html[$tab].=HTMLControls::renderSelect($name."[".$prmName."]", $prmName, "id", "name", $prmType["source"], $val, !($prmType["vdefault"] || $prmType["vtype"]=="multiselect" || $prmType["vtype"]=="multiselect_method"), (isset($prmType["vjs"])&&$prmType["vjs"] ? $prmType["vjs"]:""), ($prmType["vtype"]=="multiselect" ? 10 : 0));
								else $html[$tab].=HTMLControls::renderSelect($name."[".$prmName."]", $prmName, false, false, $prmType["source"], $val,  !($prmType["vdefault"] || $prmType["vtype"]=="multiselect" || $prmType["vtype"]=="multiselect_method"), (isset($prmType["vjs"])&&$prmType["vjs"] ? $prmType["vjs"]:""), ($prmType["vtype"]=="multiselect" ? 10 : 0));
							} else $html[$tab].="<input class=\"form-control\" id=\"".$prmName."\" name=\"".$name."[".$prmName."]\" type=\"text\" value=\"".$val."\" />";
							break;
						case "integer":
							$html[$tab].="<input class=\"form-control numeric\" id=\"".$prmName."\" name=\"".$name."[".$prmName."]\" type=\"text\" value=\"".intval($val)."\" />";
							break;
						case "float":
							$html[$tab].="<input class=\"form-control decimal\" id=\"".$prmName."\" name=\"".$name."[".$prmName."]\" type=\"text\" value=\"".floatval($val)."\" />";
							break;
						case "string":
							$html[$tab].="<input class=\"form-control\" id=\"".$prmName."\" name=\"".$name."[".$prmName."]\" type=\"text\" value=\"".$val."\" />";
							break;
						case "text":
							$html[$tab].="<textarea class=\"form-control\" id=\"".$prmName."\" name=\"".$name."[".$prmName."]\">".$val."</textarea>";
							break;
						case "ro_string":
							$html[$tab].="<p class=\"ro_string\">".$val."</p>";
							break;
						case "title":
							$html[$tab].="<h4>".$prmType["vdefault"]."</h4>";
							break;
						case "filepath":
							$html[$tab].=HTMLControls::renderFileSelector($name."[".$prmName."]", $val, "", "", $name."_".$prmName, "form-control");
							break;
						case "tab":
							$new_tab++;
							$html_tabs[$new_tab]=$prmType["vdefault"];
							break;
						default:
							$html[$tab].=Text::_("Wrong parameter type").": ".$prmType["vtype"];
							break;
					}
					switch ($prmType["vtype"]) {
						case "table_select":
						// case "table_multiselect":
						case "select":
						// case "multiselect":
						case "select_method":
						// case "multiselect_method":
							if(isset($prmType["js_descr"])){
								if(is_array($prmType["js_descr"]) && count($prmType["js_descr"])) {
									$html[$tab].="<div class=\"prm_descr\" id=\"".$prmName."_descr\">";
									foreach($prmType["js_descr"] as $kd=>$vd) {
										$html[$tab].="<div".($kd != $val || !trim($vd) ? " style=\"display:none;\"" : "")." class=\"prm_descr_elem\" id=\"".$kd."_descr_elem\">".$vd."</div>";
									}
									$html[$tab].="</div>";
								}
							}
							break;
						default:
							break;
					}
					if($prmType["vtype"]!="tab") $html[$tab].="</div></div>";
				}
			}
		}
		if(count($html)>1) {
			$_html.="<ul class=\"nav nav-tabs\" id=\"".$name."_tabs\">";
			foreach($html_tabs as $ind=>$html_tab_header){
				$_html.="<li class=\"switcher".($active_tab==$ind ? " active" : "")."\"><a aria-expanded=\"".($active_tab==$ind ? "true" : "false")."\" href=\"#".$name."_tab_".$ind."\" data-key=\"".$ind."\" data-toggle=\"tab\">".$html_tab_header."</a></li>";
			}
			$_html.="</ul>";
			$_html.="<div class=\"tab-content float-fix\">";
			foreach($html as $ind=>$html_tab_body){
				$_html.="<div class=\"tab-pane".($active_tab==$ind ? " active" : "")."\" id=\"".$name."_tab_".$ind."\">";
				$_html.="<div class=\"params\">".$html_tab_body."</div>";
				$_html.="</div>";
			}
			$_html.="</div>";
			$_html.=HTMLControls::renderHiddenField("activeTab",$active_tab);
		} else {
			$_html.="<fieldset>";
			$_html.="<legend>";
			$_html.=self::renderLabelField(false, Text::_($title));
			$_html.="</legend>";
			$_html.="<div class=\"params\">".$html[1]."</div>";
			$_html.="</fieldset>";
		}
		return $_html;
	}
}
?>