<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Menus extends BaseObject{
	private $max_levels = 5;
	private $all_parents = "";
	private $all_res = "";
	private $tree_level = 0;
	private $html = "";
	private $root_id = ""; // ID of root menu item
	private $ul_id = ""; // id for main ul of tree
	private $translate = true;
	private $activemenu = 0;
	private $_unique_id = "";
	private $ul_class="";
	private $ul_li_class="";
	private $ul_a_class="";
	private $ul_ul_class="";
	private $data_toggle="";
	private $data_toggle_separate=0;
	private $data_id_attr = "data-id";
	private $data_canonical_attr = "data-canonical-id";
	
	public function __construct($ul_class="", $ul_li_class="", $ul_a_class="", $ul_ul_class="", $data_toggle="", $data_toggle_separate = 0){
		$this->ul_class=$ul_class;
		$this->ul_li_class=$ul_li_class;
		$this->ul_ul_class=$ul_ul_class;
		$this->ul_a_class=$ul_a_class;
		$this->data_toggle=$data_toggle;
		$this->data_toggle_separate=$data_toggle_separate;
	}
	public function render($root_id="", $ul_id="", $translate=true, $max_levels=0) {
		$this->_unique_id=$ul_id;
		if(!$translate) $this->translate=false;
		if (!$root_id) return "";
		$this->root_id=$root_id; 
		$this->ul_id=$ul_id; 
		$this->activemenu=$_SESSION['active_menu_id'];
		$this->buildTreeArrays($max_levels);
		$this->html=""; $this->tree_level=0;
		$parents_arr=preg_split("/(\,)/", $this->root_id);
		// Тут можно обработать пункты меню
		Event::raise('system.menus.prepare_all_res', array(), $this->all_res);
		$this->drawTreeLevelUL ($parents_arr, $ul_id); 
		$menuHTML = $this->html; 
		return $menuHTML;
	}
	private function makeParents($res) {
		if (count($res)>0) {
			foreach($res as $row) {	$result[]=$row->mi_parent_id; }
		} else return false;
		return $result;
	}
	private function drawTreeLevelUL($parent_id, $ul_id) {
		$item_drawed=0; $this->tree_level++;
		if (count($this->all_res)) {
			foreach($this->all_res as $res_row) {
				if (in_array($res_row->mi_parent_id,$parent_id)) {
					$data_attr = " ".$this->data_id_attr."=\"".$res_row->mi_id."\"";
					if ($res_row->mi_canonical_id && $res_row->mi_canonical_id != $res_row->mi_id) {
						if(isset($this->all_res[$res_row->mi_canonical_id])){
							$data_attr.= " ".$this->data_canonical_attr."=\"".$res_row->mi_canonical_id."\"";
							// Let's update data from canonical menu
							$_row = $this->all_res[$res_row->mi_canonical_id];
							$res_row->mi_module = $_row->mi_module;
							$res_row->mi_view = $_row->mi_view;
							$res_row->mi_layout = $_row->mi_layout;
							$res_row->mi_alias = $_row->mi_alias;
							$res_row->mi_psid = $_row->mi_psid;
							$res_row->mi_controller = $_row->mi_controller;
							$res_row->mi_task = $_row->mi_task;
							$res_row->mi_link = $_row->mi_link;
							$res_row->mi_type = $_row->mi_type;
							// Util::pre($res_row);
						}
					}
					$parents_arr=preg_split("/(\,)/", $res_row->mi_id);
					if($this->tree_level==1) 
						$ul_class=$this->ul_class; 
					else
						$ul_class=$this->ul_ul_class;
					if (!$item_drawed) {
						if ($ul_id) 
							$this->html.= "<ul id=\"".$ul_id."\"".($ul_class ? " class=\"".$ul_class."\"" : "").">"; 
						else 
							$this->html.= "<ul".($ul_class ? " class=\"".$ul_class."\"" : "").">";
						$item_drawed=1;
					}
					if ($res_row->mi_access != "all") {
						$roles=preg_split("/(\;)/", trim($res_row->mi_access)); $roles=array_flip($roles);
						if (!array_key_exists(User::getInstance()->getRole(), $roles)) continue;
					}
					if(defined("_BARMAZ_TRANSLATE")){
						if ($res_row->mi_forlang != "all") {
							// @FIXME this storage method will be deprecated
							$lang=preg_split("/(\;)/", trim($res_row->mi_forlang)); $lang=array_flip($lang);
							if (!array_key_exists(Text::getLanguage(), $lang)) continue;
						}
					}
					if (in_array($res_row->mi_id, $this->all_parents) && $this->tree_level<$this->max_levels) {
						$ul_li_class=$this->ul_li_class;
						$ul_a_class=$this->ul_a_class;
						$data_toggle=$this->data_toggle;
					} else {
						$ul_li_class="";
						$ul_a_class="";
						$data_toggle="";
					}
					$this->html.= "<li id=\"".$this->ul_id."_item_".$res_row->mi_id."\"".($ul_li_class ? " class=\"".$ul_li_class."\"" : "").$data_attr.">";
					if ($res_row->mi_link && !Router::isAnchor($res_row->mi_link)) {
						$href=$res_row->mi_link;
						if(!Router::isFullLink($href) && !Router::isJavaScript($href) && seoConfig::$useMidInMenuLinks && $res_row->mi_target!="popup") $href.=Router::getSeparator($href)."mid=".$res_row->mi_id;
					} else {
						$anchor='';
						if(Router::isAnchor($res_row->mi_link)) $anchor=$res_row->mi_link;
						$href="";
						if ($res_row->mi_module) $href.="module=".$res_row->mi_module."&amp;";
						if ($res_row->mi_view) $href.="view=".$res_row->mi_view."&amp;";
						if ($res_row->mi_layout) $href.="layout=".$res_row->mi_layout."&amp;";
						if ($res_row->mi_alias) $href.="alias=".$res_row->mi_alias."&amp;";
						if ($res_row->mi_psid) $href.="psid=".$res_row->mi_psid."&amp;";
						if ($res_row->mi_controller) $href.="controller=".$res_row->mi_controller."&amp;";
						if ($res_row->mi_task) $href.="task=".$res_row->mi_task."&amp;";
						if (seoConfig::$useMidInMenuLinks && $res_row->mi_id!=siteConfig::$defaultMenuID && $res_row->mi_target!="popup" && !$anchor) {
							if ($res_row->mi_canonical_id) {
								$href.="mid=".$res_row->mi_canonical_id;
							} else {
								$href.="mid=".$res_row->mi_id;
							}
						}
						if ($href) $href="index.php?".$href;
						$href.=$anchor;
					}
					$target="";
					switch  ($res_row->mi_target) {
						case "_blank":
							$target=" target=\"_blank\""; 
							break;
						case "popup":
							$ul_a_class.=" relpopuptext"; 
							break;
					}
					if($ul_a_class) $ul_a_class=" class=\"".$ul_a_class."\"";
					if($res_row->mi_nofollow) $rel=" rel=\"nofollow\"";	else $rel="";
//					if ($this->translate) $cur_name =	Text::_($res_row->mi_name);
//					else $cur_name = $res_row->mi_name;
					if ($this->translate) $cur_name = html_entity_decode(Text::_($res_row->mi_name), ENT_QUOTES);
					else $cur_name = html_entity_decode($res_row->mi_name, ENT_QUOTES);
					if($res_row->mi_thumb){
						$_thumb_path=BARMAZ_UF_PATH."menus".DS."thumbs".DS.Files::splitAppendix($res_row->mi_thumb, true);
						if ((file_exists($_thumb_path)) && (is_file($_thumb_path))) {
							$_thumb=BARMAZ_UF."/menus/thumbs/".Files::splitAppendix($res_row->mi_thumb);
						} else $_thumb ="";
					} else $_thumb ="";
					if($res_row->mi_image){
						$_image_path=BARMAZ_UF_PATH."menus".DS."i".DS.Files::splitAppendix($res_row->mi_image, true);
						if ((file_exists($_image_path)) && (is_file($_image_path))) {
							$_image=BARMAZ_UF."/menus/i/".Files::splitAppendix($res_row->mi_image);
						} else $_image ="";
					} else $_image ="";
					if($this->data_toggle_separate){
						$carret = '<span class="mnu-toggler-caret mnu-toggler-caret-active"></span>';
						if($data_toggle) $this->html.= '<span class="mnu-toggler" data-toggle="'.$data_toggle.'"></span>';
					} elseif($data_toggle) {
						$carret = '<span class="mnu-toggler-caret"></span>';
					} else{
						$carret = "";
					}
					$this->html.= '<a'.(!$this->data_toggle_separate && $data_toggle ? ' data-toggle="'.$data_toggle.'"' : '').' href="'.Router::_($href).'"'.$rel.$target.$ul_a_class.$data_attr.'>'.($_image ? '<span class="mnu_thumb"><img src="'.$_image.'" alt="" title="'.$cur_name.'" /></span>': '').'<span class="mnu_title">'.$carret.$cur_name.'</span></a>';
					if ($this->tree_level<$this->max_levels) $this->drawTreeLevelUL ( $parents_arr, ($ul_id ? $ul_id.'_tree_'.$res_row->mi_id : ""));
					$this->html.= '</li>';
				}
			}
			$this->tree_level--;
			if ($item_drawed) $this->html.= '</ul>';
		}	
	}
	private function buildTreeArrays( $max_levels=0) {
		if ($max_levels) $this->max_levels=(int)$max_levels;
		elseif (siteConfig::$treeDepth) $this->max_levels=(int)siteConfig::$treeDepth;
		$sql = "SELECT * FROM #__menus WHERE mi_id>0 AND mi_enabled=1 AND mi_deleted=0 ORDER BY mi_ordering";
		Database::getInstance()->setQuery($sql);
		$this->all_res=Database::getInstance()->loadObjectList("mi_id");
		$this->all_parents=$this->makeParents($this->all_res);
	}
	
	public function getAllMenusItems() {
		$query = "SELECT mi.mi_id,0 as mi_level, mi.mi_parent_id,mi.mi_name";
		$query .= " FROM #__menus AS mi";
		$query .= " WHERE mi.mi_deleted=0";
		$query .= " ORDER BY mi.mi_ordering";
		Database::getInstance()->setQuery($query);
		$items=null;
		$this->tree_level=0;	$this->all_res=Database::getInstance()->loadObjectList();
		$parents_arr=preg_split("/(\,)/", 0);
		$this->markLevels($items,$parents_arr);
		return $items;
	}
	public function getMenusItems($parent_id) {
		$query = "SELECT mi_id,0 as mi_level,mi_parent_id,mi_name FROM #__menus WHERE mi_enabled=1 AND mi_deleted=0";
		Database::getInstance()->setQuery($query);
		$items=null;
		$this->tree_level=0;	$this->all_res=Database::getInstance()->loadObjectList();
		$parents_arr=preg_split("/(\,)/", $parent_id);
		$this->markLevels($items,$parents_arr);
		return $items;
	}
	public static function getMenusTemplate($psid) {
		$query = "SELECT mi_custom_template FROM #__menus WHERE mi_id=".$psid;
		Database::getInstance()->setQuery($query);
		return Database::getInstance()->loadResult();
	}
	private function markLevels(&$items,$parent_id){
		$this->tree_level++;
		if (count($this->all_res)) {
			foreach($this->all_res as $res_row) {
				if (in_array($res_row->mi_parent_id,$parent_id)) {
					$res_row->mi_level=$this->tree_level;
					$items[]=$res_row;
					$parents_arr=preg_split("/(\,)/", $res_row->mi_id);
					if ($this->tree_level<$this->max_levels) $this->markLevels ($items, $parents_arr);
				}
			}
			$this->tree_level--;
		}	
	}
	public static function searchSuitableMID(){
		// ссылки в пунктах меню отрабатывае только по модулю
		// остальное надо забивать не ссылками а параметрами
		$mid=0; $possible=array();
		$view=""; $layout="default"; $psid=""; $alias="";
		$req = Router::getInstance()->getVarsArr(); 
		if (isset($req["module"])) $module=$req["module"]; 
		elseif(Request::get("module","")) $module=Request::get("module","");
		else {
			return siteConfig::$defaultMenuID;
		}
		// Отработка не sef ссылки при включенном sef 
		$view=Request::getSafe("view","");
		$layout=Request::getSafe("layout","default");
		$psid=Request::getInt("psid","");
		$alias=Request::getSafe("alias","");
		// сузим поиск до модуля
		$sql ="SELECT mi.* FROM #__menus as mi";
		$sql.=" LEFT JOIN #__menus as p ON p.mi_id=mi.mi_parent_id";
		$sql.=" WHERE mi.mi_parent_id>0 AND mi.mi_enabled=1 AND mi.mi_deleted=0 AND p.mi_enabled=1 AND p.mi_deleted=0";
		$sql.=" AND mi.mi_module='".$module."'";
		$sql.=" ORDER BY p.mi_ordering, mi.mi_ordering"; // ??????????????
		Database::getInstance()->setQuery($sql);
		$nearer=Database::getInstance()->loadObjectList();
		if (count($nearer)){
			$data=Router::getInstance($module)->getTreeUp($view, $layout, $psid, $alias);
			foreach($data as $_view=>$_data){
				if(count($_data)){
					foreach($_data as $_psid=>$_alias){
						// Variants of equality
						// 0 - equal alias
						// 1 - equal view, equal layout, equal psid
						// 2 - equal view, equal psid
						// 3 - equal view, equal layout
						// 4 - equal view, empty row->mi_layout
						// 5 - equal view
						// 6 - equal module only :(
						foreach($nearer as $key=>$row){
							if ($_alias && $row->mi_alias==$_alias){
								$possible[0][]=$row->mi_id;
							} elseif ($row->mi_view==$_view && $row->mi_layout==$layout && $_psid && intval($row->mi_psid)==$_psid){
								$possible[1][]=$row->mi_id;
							} elseif (($row->mi_view==$_view) && $_psid && intval($row->mi_psid)==$_psid){
								$possible[2][]=$row->mi_id;
							} elseif ($row->mi_view==$_view && $row->mi_layout==$layout){
								$possible[3][]=$row->mi_id;
							} elseif ($row->mi_view==$_view && $row->mi_layout==""){
								$possible[4][]=$row->mi_id;
							} elseif ($row->mi_view==$_view){
								$possible[5][]=$row->mi_id;
							} else {
								$possible[6][]=$row->mi_id;
							}
						}
					}
				}
			}
			for ($i=0; $i<7; $i++){	
				if (isset($possible[$i]) && count($possible[$i])) return $possible[$i][0];
			}
		}
		return $mid;
	}
}
?>