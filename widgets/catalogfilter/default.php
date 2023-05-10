<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_WIDGET_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogfilterWidget extends Widget {
	protected $_requiredModules = array("catalog");
	
	protected $equis=array("none","eq","lt","gt");
	protected $null_date='0000-00-00';
	protected $module="catalog";
	protected $view= "goods";
	protected $layout= "default";
	protected $side=0;
	/***********************************************/
	protected $flt_ext_mode = 0;
	protected $str_sql_wf = "";
	protected $used_choices = false;
	protected $fields_add = array();
	protected $fields_base = array();
	protected $fields_types = array("select", "multiselect", "checkbox");
	protected $_canProceed = true;
	
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("Collapse_widget", "select", 0, false, array(1=>Text::_("Accordeon"), 2=>Text::_("Full screen")), Text::_("Mobile devices"));
		$this->addParam("Collapsed_widget_title", "string", "", false, null, Text::_("Mobile devices"));
		$this->addParam("Hide_main_title_on_mobiles", "boolean", 1);
		$this->addParam("Filter_ID", "string", "");
		$this->addParam("Hide_filter_panel", "boolean", 0);
		$this->addParam("Show_own_filter_panel", "boolean", 0);
		$this->addParam("Show_switch_filter_mode_button", "boolean", 1);
		$this->addParam("max_checkbox_per_parameter", "integer", 10);
		$this->addParam("Show_on_level", "select", 0, false, array("0"=>Text::_("All levels"), "1"=>Text::_("Last level"), "2"=>Text::_("Except top level"), "3"=>Text::_("Except top and first levels")));
		$this->addParam("Use_GET_form_method", "boolean", 0);
		$this->addParam("Use_counter_for_fields", "boolean", 0);
		/**************************************************************************/
		$this->addParam("Use_experimental_functions", "boolean", 0);
	}
	protected function canProceed(){
		if($this->_canProceed){
			if(Module::getInstance()->getName()!=$this->module) $this->_canProceed = false;
			if(Request::getSafe("task") || !in_array(Request::getSafe("view"),array("",$this->view))) $this->_canProceed = false;
			if($this->getParam('Show_on_level')==1){
				$psid = Request::getInt('psid', false);
				$multy_code = Request::getInt('multy_code', false);
				if(!$multy_code) $multy_code=$psid;
				//$childs = $this->getGroupChilds($psid);
				// Util::showArray($childs);
				if(count($this->getGroupChilds($psid))) {
					$this->_canProceed = false;
				}
			} elseif($this->getParam('Show_on_level')==2){
				$psid = Request::getInt('psid', false);
				$multy_code = Request::getInt('multy_code', false);
				if(!$multy_code) $multy_code=$psid;
				if(!$psid) {
					$this->_canProceed = false;
				}
			} elseif($this->getParam('Show_on_level')==3){
				$psid = Request::getInt('psid', false);
				$multy_code = Request::getInt('multy_code', false);
				if(!$multy_code) $multy_code=$psid;
				if(!$psid) {
					$this->_canProceed = false;
				} elseif(!$this->getParentID($psid)) {
					$this->_canProceed = false;
				}
			}
		}
		return $this->_canProceed;
	}
	/**************************************************************************/
	protected function getCounter($field, $val, $text){
		if(isset($this->used_choices[$field]) && isset($this->used_choices[$field][$val])){
			return $this->used_choices[$field][$val];
		}
		// Util::pre($field, $val, $text);
		return 0;	
	}
	protected function fillFieldsLists(){
		$_arr_fields = array();
		/**********************************/
		Debugger::getInstance()->milestone("Before loading metadata", __CLASS__."->".__FUNCTION__);
		$meta = new SpravMetadata($this->module, $this->view, $this->layout);
		Debugger::getInstance()->milestone("After loading metadata", __CLASS__."->".__FUNCTION__);
		$_arr_fields_add = array();
		$_arr_fields_base = array();
		if(count($meta->field)){
			foreach ($meta->field as $ind=>$field){
//				Util::pre("<br />ind = ".$ind."<br />field = ".$field."<br />is_add = ".$meta->is_add[$ind]."<br />is_add_custom = ".$meta->is_add_custom[$ind]."<br />view = ".$meta->view[$ind]."<br />filter = ".$meta->filter[$ind]."<br />filter_ext = ".$meta->filter_ext[$ind]."<br>filter=".$meta->filter[$ind]."<br />flt_ext_mode=".$this->flt_ext_mode."<br />filter_ext=".$meta->filter_ext[$ind]);
//				Util::pre($field."=".( (!$this->flt_ext_mode && $meta->filter[$ind]) || ($this->flt_ext_mode && $meta->filter_ext[$ind]) ) );
				if( in_array($meta->input_type[$ind], $this->fields_types) && ( (!$this->flt_ext_mode && $meta->filter[$ind]) || ($this->flt_ext_mode && $meta->filter_ext[$ind]) ) ){
//					Util::pre("<br />ind = ".$ind."<br />field = ".$field."<br />is_add = ".$meta->is_add[$ind]."<br />is_add_custom = ".$meta->is_add_custom[$ind]."<br />view = ".$meta->view[$ind]."<br />filter = ".$meta->filter[$ind]."<br />filter_ext = ".$meta->filter_ext[$ind]."<br>filter=".$meta->filter[$ind]."<br />flt_ext_mode=".$this->flt_ext_mode."<br />filter_ext=".$meta->filter_ext[$ind]);
					if($meta->is_add[$ind]){
						if(trim($field)) $_arr_fields_add[trim($field)] = $meta->input_type[$ind];
					} else {
						if(trim($field)) {
							$_arr_fields_base[trim($field)] = $meta->input_type[$ind];
							$this->fields_base[] = (object) array("id"=>$field, "name"=>Text::_($meta->field_title[$ind]), "input_type"=>$meta->input_type[$ind]);
						}
					}
				}
			}
		}
//		Util::showArray($_arr_fields_add, "_arr_fields_add"); Util::showArray($_arr_fields_base, "_arr_fields_base");
		/***************** ADD FIELDS *****************/
		$sql="SELECT `f_name` as `id`, `f_descr` as `name` FROM #__fields_list WHERE (`f_type`=5 OR `f_type`=8) AND f_deleted=0 AND f_name IN('".implode("','", array_keys($_arr_fields_add))."')";
		Database::getInstance()->setQuery($sql);
//		Util::pre(Database::getInstance()->getQuery());
		$data=Database::getInstance()->loadObjectList('id');
//		Util::showArray($data, "data");
		foreach($_arr_fields_add as $key=>$fld){
			if(array_key_exists($key, $data)) {
				$data[$key]->input_type=$fld;
				$this->fields_add[]=$data[$key];
			}
		}
		/***************** BASE FIELDS *****************/
		// $_arr_fields = array(); // NOT NEED
		/**********************************/
		//Util::showCollapsedArray($this->fields_add, "fields_add"); Util::showCollapsedArray($this->fields_base, "fields_base");
	}
	protected function prepareSpravSQL(){
		if($this->canProceed()){
			$reestr = Module::getInstance("catalog")->get('reestr');
			$str_sql_wf = $reestr->get("str_sql_wf");
			if($str_sql_wf && strpos($str_sql_wf, "FROM")){
				$this->str_sql_wf = mb_substr($str_sql_wf, mb_strpos($str_sql_wf, "FROM", 0, DEF_CP));
				if(mb_strpos($this->str_sql_wf, "ORDER BY", 0, DEF_CP)) $this->str_sql_wf = mb_substr($this->str_sql_wf, 0, mb_strrpos($this->str_sql_wf, "ORDER BY", 0, DEF_CP),DEF_CP);
			}
		}
	}
	protected function prepareChoices(){
		if($this->canProceed()){
			$_experimental = $this->getParam('Use_experimental_functions');

			/**** Add fields choices exclude multiselect ****/
			Debugger::getInstance()->milestone(__CLASS__."->".__FUNCTION__."() =&gt; Started add fields (without multiselect)");
			$fields_arr =array();
			$this->fillFieldsLists();
			if(count($this->fields_add)){
				foreach($this->fields_add as $fld){
					if($fld->input_type != "multiselect") $fields_arr[]=$fld->id;
				}
			}
			$sql = "SELECT DISTINCT `field_name`, `field_value`, COUNT(*) AS counter FROM `#__goods_data` WHERE `field_value`<>'' AND `field_name` IN ('".implode("', '", $fields_arr)."')";
			if($this->str_sql_wf) $sql.=" AND obj_id IN ("."SELECT DISTINCT c.g_id ".$this->str_sql_wf.")";
			$sql.= " GROUP BY `field_name`, `field_value`";
			Database::getInstance()->setQuery($sql);
//			Debugger::getInstance()->warning(__CLASS__."->".__FUNCTION__.": ".Database::getInstance()->getQuery());
			$choice_arrays = Database::getInstance()->loadObjectList();
//			Util::showArray($choice_arrays, "choice_arrays add");
			if (count($choice_arrays)) {
				foreach($choice_arrays as $choice_arr){
					$this->used_choices[$choice_arr->field_name][$choice_arr->field_value] = $choice_arr->counter;
				}
			}
			Debugger::getInstance()->milestone(__CLASS__."->".__FUNCTION__."() =&gt; Finished add fields (without multiselect)");
			/**** Add fields choices only multiselect ****/
			if($_experimental){
				Debugger::getInstance()->milestone(__CLASS__."->".__FUNCTION__."() =&gt; Started add fields (only multiselect)");
				$fields_arr =array();
				if(count($this->fields_add)){
					foreach($this->fields_add as $fld){
						if($fld->input_type == "multiselect") $fields_arr[]=$fld->id;
					}
				}
				if(count($fields_arr)){
					$sql = "SELECT * FROM `#__goods_data` WHERE `field_value`<>'' AND `field_name` IN ('".implode("', '", $fields_arr)."')";
					if($this->str_sql_wf) $sql.=" AND `obj_id` IN ("."SELECT DISTINCT c.g_id ".$this->str_sql_wf.")";
					Database::getInstance()->setQuery($sql);
//					Util::pre(Database::getInstance()->getQuery());
					$multiselect_arrays = Database::getInstance()->loadObjectList();
					if(count($multiselect_arrays)){
						foreach($multiselect_arrays as $mak=>$mav){
							//						Util::pre($mav);
							$codes = array();
							$code_str = trim($mav->field_value, ";");
							if($code_str) $codes = explode(";", $code_str);
							if(count($codes)){
								foreach($codes as $mk=>$mv){
									//								Util::pre($mv);
									if($mv){
										if(isset($this->used_choices[$mav->field_name][$mv])){
											$this->used_choices[$mav->field_name][$mv]++;
										} else {
											$this->used_choices[$mav->field_name][$mv] = 1;
										}
									}
								}
							}
						}
					}
//				Util::showCollapsedArray($multiselect_arrays, "multiselect_arrays");
				}
//			Util::showArray($this->used_choices, "this->used_choices");
				Debugger::getInstance()->milestone(__CLASS__."->".__FUNCTION__."() =&gt; Finished add fields (only multiselect)");
			}

			/**** Base fields choices exclude multiselect ****/
			Debugger::getInstance()->milestone(__CLASS__."->".__FUNCTION__."() =&gt; Started base fields");
			$sql_arr = array();
			if(count($this->fields_base)){
				foreach($this->fields_base as $ind=>$fld){
					if($fld->input_type != "multiselect") $sql_arr[] = "SELECT g".$ind.".`g_id` AS `gid`, '".$fld->id."' AS `field_name`, g".$ind.".`".$fld->id."` AS `field_value` FROM `#__goods` AS g".$ind;
				}
			}
			if(count($sql_arr)){
				$sql = implode(" UNION ", $sql_arr);
				$sql = "SELECT DISTINCT `field_name`, `field_value`, COUNT(*) AS counter FROM (".$sql.") AS u";
				if($this->str_sql_wf) $sql.=" WHERE `gid` IN ("."SELECT DISTINCT c.g_id ".$this->str_sql_wf.")";
				$sql.= " GROUP BY `field_name`, `field_value`";
				Database::getInstance()->setQuery($sql);
//				Debugger::getInstance()->warning(__CLASS__."->".__FUNCTION__.": ".Database::getInstance()->getQuery());
				$choice_arrays = Database::getInstance()->loadObjectList();
//				Util::showArray($choice_arrays, "choice_arrays base");
				if (count($choice_arrays)) {
					foreach($choice_arrays as $choice_arr){
						$this->used_choices[$choice_arr->field_name][$choice_arr->field_value] = $choice_arr->counter;
					}
				}
			}
			/**** Base fields choices only multiselect ****/
			// @TODO Multiselect in base fields
			Debugger::getInstance()->milestone(__CLASS__."->".__FUNCTION__."() =&gt; Finished base fields");
		}
	}
	/*
	protected function getAllGroupChilds($psid, &$childs){
		if(is_array($psid)) $psid = implode("','", $psid);
		Database::getInstance()->setQuery("SELECT ggr_id FROM #__goods_group WHERE ggr_id_parent IN ('".$psid."')");
		$res = Database::getInstance()->loadResultArray();
		if(is_array($res) && count($res)){
			foreach($res as $val) $childs[$val] = $val;
			$this->getAllGroupChilds($res, $childs);
		}
	}
	*/
	protected function getGroupChilds($psid){
		$childs = array();
		if(is_array($psid)) $psid = implode("','", $psid);
		Database::getInstance()->setQuery("SELECT ggr_id FROM #__goods_group WHERE ggr_id_parent IN ('".$psid."')");
		$res = Database::getInstance()->loadResultArray();
		if(is_array($res) && count($res)){
			foreach($res as $val) $childs[$val] = $val;
		}
		return $childs;
	}
	protected function getParentID($psid){
		Database::getInstance()->setQuery("SELECT ggr_id_parent FROM #__goods_group WHERE ggr_id='".$psid."'");
		return Database::getInstance()->loadResult();
	}
	public function prepare() {
		$_use_counter_for_fields = $this->getParam('Use_counter_for_fields');
		Debugger::getInstance()->milestone(__CLASS__."->".__FUNCTION__."() =&gt; Start preparing");
		/**************************************************************************/
		if(!$this->canProceed()) return "";
		/**************************************************************************/
		$uid = User::getInstance()->getID(true);
		//if(!$uid) $uid=session_id();
		if (isset($_SESSION['filter_ext_mode'][$this->module][$uid][$this->view.".".$this->layout.".".$this->side]) && ($_SESSION['filter_ext_mode'][$this->module][$uid][$this->view.".".$this->layout.".".$this->side]==1)) $this->flt_ext_mode=1;
		/**************************************************************************/
		$widget_id = $this->getParam('Widget_ID');
		$div_id = $this->getParam('Filter_ID', false, ($widget_id ? $widget_id."_flt" : ""));
		if ($this->getParam('Hide_filter_panel')){
			$css="div.catalogModule div#filterBlock{ display:none; }";
			Portal::getInstance()->addStyle($css);
		}
		Portal::getInstance()->AddScriptDeclaration('
			$(document).ready(function(){
				if ($.cookie("BARMAZ_catalog_group")!=null){
					var BARMAZ_catalog_group=$.cookie("BARMAZ_catalog_group");
					$("#'.$div_id.'_psid").val(BARMAZ_catalog_group);
					$("#'.$div_id.'_multy_code").val(BARMAZ_catalog_group);
				}
			});
		');
		/**************************************************************************/
		if($_use_counter_for_fields){
			$this->prepareSpravSQL();
			$this->prepareChoices();
		}
		
		switch($this->getParam('Collapse_widget')){
			case "1":
				break;
			case "2":
				Portal::getInstance()->AddScriptDeclaration('
						$(document).ready(function(){
							$("#'.$widget_id.' .navbar-header button").bind("click", function(){
								$("body").addClass("overlaid");
								$("#'.$widget_id.'_wrapper").removeClass("navbar-popup-fullscreen-off").addClass("navbar-popup-fullscreen-on");
							});
							$("#'.$widget_id.' .navbar-popup-fullscreen button").bind("click", function(){
								$("body").removeClass("overlaid");
								$("#'.$widget_id.'_wrapper").removeClass("navbar-popup-fullscreen-on").addClass("navbar-popup-fullscreen-off");
							});
						});
					');
				break;
			default:
				break;
		}
		
		if($this->getParam('Hide_main_title_on_mobiles')){
			Portal::getInstance()->addStyle("@media (max-width: 767px){#".$widget_id." .wTitle {display:none !important;}}");
		}
		
		Debugger::getInstance()->milestone(__CLASS__."->".__FUNCTION__."() =&gt; Stop preparing");
	}
	public function render() {
		if(!$this->canProceed()) return "";
		// Debugger::getInstance()->warning("catalogfilterWidget.render");
		$widget_id = $this->getParam('Widget_ID');
		$div_id = $this->getParam('Filter_ID', false, ($widget_id ? $widget_id."_flt" : ""));
		if (!isset($_SESSION[$this->module][$this->view.".".$this->layout.".".$this->side]['filt_arr'])) return "";
		$psid = Request::getInt('psid', false);
		
		$widgetHTML = "";
		
		$html="<div class=\"catalog-extended-filter\" id=\"".$div_id."\">";
		$html.=$this->getCatalogFilterForm();
		$html.="</div>";
		
		switch($this->getParam('Collapse_widget')){
			case "1":
				$adaptive_menu_title = $this->getParam('Collapsed_widget_title');
				$widgetHTML.= "<div class=\"navbar-header\"><span class=\"topmenu_label visible-xs\">".$adaptive_menu_title."</span>";
				$widgetHTML.= "<button type=\"button\" class=\"btn btn-navbar navbar-toggle\" data-toggle=\"collapse\" data-target=\"#".$widget_id."_wrapper\"><i class=\"glyphicon glyphicon-menu-hamburger\"></i></button>";
				$widgetHTML.= "</div>";
				$widgetHTML.= "<div id=\"".$widget_id."_wrapper\" class=\"collapse navbar-collapse\">".$html."</div>";
				break;
			case "2":
				$adaptive_menu_title = $this->getParam('Collapsed_widget_title');
				$widgetHTML.= "<div class=\"navbar-header\"><span class=\"topmenu_label visible-xs\">".$adaptive_menu_title."</span>";
				$widgetHTML.= "<button type=\"button\" class=\"btn btn-navbar navbar-toggle\" data-toggle=\"popup\" data-target=\"#".$widget_id."_wrapper\"><i class=\"glyphicon glyphicon-menu-hamburger\"></i></button>";
				$widgetHTML.= "</div>";
				$widgetHTML.= "<div id=\"".$widget_id."_wrapper\" class=\"navbar-popup-fullscreen navbar-popup-fullscreen-off\">";
				$widgetHTML.= "<button type=\"button\" class=\"btn close\" data-toggle=\"popup\" data-target=\"#".$widget_id."_wrapper\"><i class=\"glyphicon glyphicon-remove\"></i></button>";
				$widgetHTML.= $html;
				$widgetHTML.= "</div>";
				break;
			default:
				$widgetHTML = $html;
				break;
		}
		
		return $widgetHTML;
	}
	protected function getCatalogFilterForm(){
		$_use_counter_for_fields = $this->getParam('Use_counter_for_fields');
		$_experimental = $this->getParam('Use_experimental_functions');
		$module=$this->module;
		if(Module::getInstance()->getName()=="catalog") $controller = Module::getInstance("catalog")->get("controller");
		else $controller = "";
		$view= $this->view;
		$layout= $this->layout;
		$side=$this->side;
		$script="";
		$widget_id = $this->getParam('Widget_ID');
		$div_id = $this->getParam('Filter_ID', false, ($widget_id ? $widget_id."_flt" : ""));
		$without_parents = 0;
		$show_own_filter_panel=$this->getParam('Show_own_filter_panel');
		$show_switch_filter_mode_button=$this->getParam('Show_switch_filter_mode_button');
		$max_checkbox_per_parameter=$this->getParam('max_checkbox_per_parameter');
		$alias = Request::getSafe('alias', "");
		if(intval(Module::getInstance("catalog")->getParam('search_mode'))>0) $kwds = Request::getSafe("kwds");
		else $kwds = "";
		$psid = Request::getInt('psid', false); 					// ид строки
		$multy_code = Request::getInt('multy_code', false); 		// ид верхней группы
		if(!$multy_code) $multy_code=$psid;
		$filter_arr = $_SESSION[$module][$view.".".$layout.".".$side]['filt_arr'];
		$uid = User::getInstance()->getID(true);
		// if(!$uid) $uid=session_id();
//		if (isset($_SESSION['filter_ext_mode'][$module][$uid][$view.".".$layout.".".$side])&&($_SESSION['filter_ext_mode'][$module][$uid][$view.".".$layout.".".$side]==1)) $this->flt_ext_mode=1;
		$flt=new SpravFilter();
		$filter=$flt->getFilter($side,$module, $view,$layout);
		$_selected="selected=\"selected\"";
		$_checked="checked=\"checked\"";
		$_html=""; $filtered="";
		$helper = Module::getHelper('goods','catalog');
		if(Request::getSafe("view")=="goods" && Request::getSafe("layout")=="info"){
			$group_id=$helper->getParentGroup($psid);
			$alias = $helper->getAliasByGroupId($group_id);
		} else {
			$group_id=$psid;
		}
		/* new start */
		if($this->getParam('Use_GET_form_method')){
			$form_method="get";
		} else {
			$form_method="post";
		}
		$form_link=Router::_("index.php?module=".$module.($without_parents ? "" : "&view=".$view."&layout=".$layout."&psid=".$group_id.($group_id && $alias ? "&alias=".$alias : "")));
		$_html_full = "<form name=\"frmfilterext\" method=\"".$form_method."\" action=\"".$form_link."\" onsubmit=\"catalog_ext_flt_onsubmit();\" >";
		if($kwds) $_html_full.= "<input type=\"hidden\" name=\"kwds\" value=\"".$kwds."\"  />";
		if($controller && $controller != "default") $_html_full.= "<input type=\"hidden\" name=\"controller\" value=\"".$controller."\"  />";
		if($form_method=="post"){
			$_html_full.= "<input type=\"hidden\" name=\"module\" value=\"".$module."\"  />";
			$_html_full.= "<input type=\"hidden\" name=\"view\" value=\"".$view."\"  />";
			$_html_full.= "<input type=\"hidden\" name=\"layout\" value=\"".$layout."\"  />";
			if(!$without_parents) {
				$_html_full.= "<input type=\"hidden\" name=\"alias\" value=\"".$alias."\" id=\"".$div_id."_alias\"  />";
				$_html_full.= "<input type=\"hidden\" name=\"psid\" value=\"".$group_id."\" id=\"".$div_id."_psid\"  />";
				$_html_full.= "<input type=\"hidden\" name=\"multy_code\" value=\"".$multy_code."\"  id=\"".$div_id."_multy_code\"  />"; // May be unusefull ?
			}
		}
		/* new stop */
		/* old start */
		/*
		$form_link=Router::_("index.php");
		if($this->getParam('Use_GET_form_method')){
			if(seoConfig::$sefMode) $form_link=Router::_("index.php?module=".$module.($without_parents ? "" : "&view=".$view."&layout=".$layout."&psid=".$group_id.($group_id && $alias ? "&alias=".$alias : "")));
			$form_method="get";
		} else {
			$form_method="post";
		}
		$_html_full = "<form name=\"frmfilterext\" method=\"".$form_method."\" action=\"".$form_link."\" onsubmit=\"catalog_ext_flt_onsubmit();\" >";
		if($form_method=="post" || ($form_method=="get" && !seoConfig::$sefMode)){
			$_html_full.= "<input type=\"hidden\" name=\"module\" value=\"".$module."\"  />";
			$_html_full.= "<input type=\"hidden\" name=\"view\" value=\"".$view."\"  />";
			$_html_full.= "<input type=\"hidden\" name=\"layout\" value=\"".$layout."\"  />";
			if(!$without_parents) {
				$_html_full.= "<input type=\"hidden\" name=\"alias\" value=\"".$alias."\" id=\"".$div_id."_alias\"  />";
				$_html_full.= "<input type=\"hidden\" name=\"psid\" value=\"".$group_id."\" id=\"".$div_id."_psid\"  />";
				$_html_full.= "<input type=\"hidden\" name=\"multy_code\" value=\"".$multy_code."\"  id=\"".$div_id."_multy_code\"  />"; // May be unusefull ? 
			}
		}
		*/
		/* old stop */
		$filter_vendor_id=Session::getVar("filter.vendor");
		$filter_manufacturer_id=Session::getVar("filter.manufacturer");
		if($filter_arr && count($filter_arr)) {
//			Util::showCollapsedArray($this->used_choices, "used_choices");
			foreach ($filter_arr as $key => $value) {
//				Util::showCollapsedArray($value, $key);
				if (!is_null($filter_vendor_id) && $filter_vendor_id && $value["name"] == "g_vendor") {
					continue;
				}
				if (!is_null($filter_manufacturer_id) && $filter_manufacturer_id && $value["name"] == "g_manufacturer") {
					continue;
				}
				if($kwds){
					switch(Module::getInstance("catalog")->getParam('search_mode')){
						case "1":
							if($value["name"]=="g_sku" || $value["name"]=="g_name") continue 2;
							break;
						case "2":
							if($value["name"]=="g_sku") continue 2;
							break;
						case "3":
							if($value["name"]=="g_name") continue 2;
							break;
					}
				}
				$cur_key=str_replace(".","_",$key);
				if ($filter) {
					if (array_key_exists($key,$filter)) {$cur_val=$filter[$key];} else {$cur_val="";}
				} else $cur_val="";
				if ($value['type']=="boolean") {
					$cur_sel_0="";$cur_sel_1="";$cur_sel="";
					if ($cur_val=="0") $cur_sel_0=$_selected;
					elseif ($cur_val=="1") $cur_sel_1=$_selected;
					else $cur_sel=$_selected;
/*						
					$cur_sel_0="";$cur_sel_1="";$cur_sel="";
					if ($cur_val=="0") $cur_sel_0=$_checked;
					elseif ($cur_val=="1") $cur_sel_1=$_checked;
					else $cur_sel=$_checked;
*/					
					if (!isset($bool_array)) $bool_array=array(0=>Text::_('N'), 1=>Text::_('Y'));
					if($cur_val=="0" || $cur_val=="1") $filtered.= "<span class=\"w_filter_row\"><span class=\"w_reset_filter_key\" onclick=\"catalog_ext_flt_reset_key('".$div_id."','frmfilterext','".$cur_key."', this);\"></span><span class=\"w_filter_row_title\">".strval(Text::_($filter_arr[$key]['title'])."</span> : ".$bool_array[$cur_val]."</span>");
					$_html.= "<div class=\"singleRow row\"><div class=\"col-xs-7 formlabel\">".Text::_($value['title'])."</div>";
					$_html.= "<div class=\"col-xs-5 forminput forminput_radio\">";
					
					$_html.= "<select class=\"bln singleSelect form-control\" id=\"".$cur_key."\" name=\"".$cur_key."\" size=\"1\">";
					$current_count_text = "";
					$current_count_class = "";
					$_html.= "<option class=\"".$current_count_class."\" ".$cur_sel." value=\"-1\">".Text::_("All")."</option>";
					if($_use_counter_for_fields) {
						$current_count = $this->getCounter($value["name"], 0, Text::_("N"));
						$current_count_text = " (".$current_count.")";
						$current_count_class = "input-count-".$current_count;
					}
					$_html.= "<option class=\"".$current_count_class."\" ".$cur_sel_0." value=\"0\">".Text::_("N").$current_count_text."</option>";
					if($_use_counter_for_fields) {
						$current_count = $this->getCounter($value["name"], 1, Text::_("Y"));
						$current_count_text = " (".$current_count.")";
						$current_count_class = "input-count-".$current_count;
					}
					$_html.= "<option class=\"".$current_count_class."\" ".$cur_sel_1." value=\"1\">".Text::_("Y").$current_count_text."</option>";
					$_html.= "</select>";
/*						
					$_html.= "<div class=\"row\"><div class=\"col-sm-3\"><input type=\"radio\" name=\"".$cur_key."\" id=\"".$cur_key."_2\" ".$cur_sel_1." value=\"1\"><label for=\"".$cur_key."_2\">".Text::_("Y")."</label></div>";
					$_html.= "<div class=\"col-sm-3\"><input type=\"radio\" name=\"".$cur_key."\" id=\"".$cur_key."_1\" ".$cur_sel_0." value=\"0\"><label for=\"".$cur_key."_1\">".Text::_("N")."</label></div>";
					$_html.= "<div class=\"col-sm-6\"><input type=\"radio\" name=\"".$cur_key."\" id=\"".$cur_key."_0\" ".$cur_sel." value=\"-1\" \><label for=\"".$cur_key."_0\">".Text::_("Not matter")."</label></div></div>";
*/					
					$_html.= "</div></div>";
				} elseif($value['ck_reestr']) {
					if (is_array($value['ck_reestr'])) $ck_array=$value['ck_reestr'];
					else $ck_array=SpravStatic::getCKArray($value['ck_reestr']);
					if(count($ck_array)){
						$_html.= "<div class=\"singleRow row\"><div class=\"col-sm-12 formlabel\">".Text::_($value['title'])."</div></div>";
						$i=0;
						$new_ck_array=array();
						$cur_val_array=explode("&&",$cur_val);
						$_html.= "<div class=\"singleRow row\"><div class=\"col-sm-12\">";
						$_html.= "<div class=\"formcheckboxes-wrapper float-fix\">";
						$forminput_class = ""; $forminput_style = "";
						foreach ($ck_array as $ck_key=>$ck_val){
							if ($ck_key){
								$i++;
								if($i > $max_checkbox_per_parameter) {
									$forminput_class = " switchable-visible-row";
									$forminput_style = " style=\"display:none;\"";
								}
								if (in_array($ck_key, $cur_val_array)) {
									$checked_val=$ck_key;
									if (is_array($filter_arr[$key]['ck_reestr'])) {
										if(isset($filter_arr[$key]['ck_reestr'][$ck_key])) $new_ck_array[]=$filter_arr[$key]['ck_reestr'][$ck_key];
										else $new_ck_array[]=$ck_key;
									} else $new_ck_array[]=SpravView::getValueFromCKArray($filter_arr[$key]['ck_reestr'], $ck_key);
								} else $checked_val="";
								$current_count_text = "";
								$current_count_class = "";
								//if($_use_counter_for_fields) {
								//if($_experimental && $value["input_type"] != "multiselect") {
								if($_use_counter_for_fields) {
									if($value["input_type"] != "multiselect" || $_experimental){
										$current_count = $this->getCounter($value["name"], $ck_key, $ck_val);
										$current_count_text = " (".$current_count.")";
										$current_count_class = " input-count-".$current_count;
									}
								}
								$_html.= "<div class=\"forminput formcheckboxes".$forminput_class.$current_count_class."\"".$forminput_style.">";
								$_html.= "<div class=\"formcheckbox-input\">".HTMLControls::renderCheckbox($cur_key."[]", $checked_val, $ck_key, $cur_key."_".$i)."</div>";
								$_html.= "<div class=\"formcheckbox-label\">".HTMLControls::renderLabelField($cur_key."_".$i, $ck_val.$current_count_text)."</div>";
								$_html.= "</div>";
							}
						}
						$_html.= "</div>";
						if($forminput_class){
							$js = "$(this).parents('.singleRow').find('.switchable-visible-row').toggle();";
							// $js = "$(this).parents('.singleRow').removeClass('formcheckboxes-collapsed').addClass('formcheckboxes-opened');";
							$_html.= "<div class=\"forminput formcheckboxes".$forminput_class."\">";
							$_html.= "<div class=\"formcheckbox-show-more\"><a onclick=\"".$js."\">".Text::_("Show all")."<i class=\"glyphicon glyphicon-chevron-down\" aria-hidden=\"true\"></i></a></div>";
							$_html.= "</div>";
							// $js = "$(this).parents('.singleRow').removeClass('formcheckboxes-opened').addClass('formcheckboxes-collapsed');";
							$_html.= "<div class=\"forminput formcheckboxes".$forminput_class."\"".$forminput_style.">";
							$_html.= "<div class=\"formcheckbox-show-less\"><a onclick=\"".$js."\">".Text::_("Hide")."<i class=\"glyphicon glyphicon-chevron-up\" aria-hidden=\"true\"></i></a></div>";
							$_html.= "</div>";
						}
						$_html.= "</div></div>";
						if($cur_val) $filtered.= "<span class=\"w_filter_row\"><span class=\"w_reset_filter_key\" onclick=\"catalog_ext_flt_reset_key('".$div_id."','frmfilterext','".$cur_key."', this);\"></span><span class=\"w_filter_row_title\">".strval(Text::_($filter_arr[$key]['title'])."</span> : ".implode(" + ", $new_ck_array)."</span>");						
					}
				} elseif (($value['type']=="date")||($value['type']=="datetime")) {
					$fakes=$flt->parseDateFilter($cur_val);
					if ($fakes[1]==$this->null_date) $cur_val=""; else $cur_val=Date::fromSQL($fakes[1],true,true);
					$cur_sel_eq=""; $cur_sel_lt=""; $cur_sel_gt="";
					if ($fakes[0]==$this->equis[1]) $cur_sel_eq=$_selected;
					elseif ($fakes[0]==$this->equis[2]) $cur_sel_lt=$_selected;
					elseif ($fakes[0]==$this->equis[3]) $cur_sel_gt=$_selected;
					if($cur_val) $filtered.= "<span class=\"w_filter_row\"><span class=\"w_reset_filter_key\" onclick=\"catalog_ext_flt_reset_key('".$div_id."','frmfilterext','".$cur_key."', this);\"></span><span class=\"w_filter_row_title\">".strval(Text::_($filter_arr[$key]['title'])."</span> : ".$flt->parseDateFilterToStr($cur_val)."</span>");
					$_html.= "<div class=\"singleRow row\"><div class=\"col-sm-12 formlabel\">".Text::_($value['title'])."</div></div>";
					$_html.= "<div class=\"singleRow row\"><div class=\"col-sm-12 forminput\">";
					$_html.= "<div class=\"row\"><div class=\"col-xs-12\"><div class=\"wrapper-filter-datetime\">";
					$_html.= "<select class=\"eq singleSelect form-control\" id=\"fake1_".$cur_key."\" name=\"fake1_".$cur_key."\" size=\"1\">";
					$_html.= "<option value=\"".$this->equis[0]."\">*</option><option ".$cur_sel_eq." value=\"".$this->equis[1]."\">=</option><option ".$cur_sel_lt." value=\"".$this->equis[2]."\">&lt;</option><option ".$cur_sel_gt." value=\"".$this->equis[3]."\">&gt;</option>";
					$_html.= "</select>";
					$_html.= "<input name=\"fake2_".$cur_key."\" id=\"fake2_".$cur_key."\" value=\"".$cur_val."\" class=\"datepicker form-control\" readonly=\"readonly\" maxlength=\"10\" size=\"10\" type=\"text\" />";
					$_html.= '<img width="1" height="1" class="date_selector" src="/images/blank.gif" title="'.Text::_("Select date").'"	alt="D" />';
					$_html.= "</div></div></div>";
					if ($fakes[3]==$this->null_date) $cur_val=""; else $cur_val=Date::fromSQL($fakes[3],true,true);
					$cur_sel_eq=""; $cur_sel_lt=""; $cur_sel_gt="";
					if ($fakes[2]==$this->equis[1]) $cur_sel_eq=$_selected;
					elseif ($fakes[2]==$this->equis[2]) $cur_sel_lt=$_selected;
					elseif ($fakes[2]==$this->equis[3]) $cur_sel_gt=$_selected;
					$_html.= "<div class=\"row\"><div class=\"col-xs-12\"><div class=\"wrapper-filter-datetime\">";
					$_html.= "<select class=\"eq singleSelect form-control\" id=\"fake3_".$cur_key."\" name=\"fake3_".$cur_key."\" size=\"1\">";
					$_html.= "<option value=\"".$this->equis[0]."\">*</option><option ".$cur_sel_eq." value=\"".$this->equis[1]."\">=</option><option ".$cur_sel_lt." value=\"".$this->equis[2]."\">&lt;</option><option ".$cur_sel_gt." value=\"".$this->equis[3]."\">&gt;</option>";
					$_html.= "</select>";
					$_html.= "<input name=\"fake4_".$cur_key."\" id=\"fake4_".$cur_key."\" value=\"".$cur_val."\" class=\"datepicker form-control\" readonly=\"readonly\" maxlength=\"10\" size=\"10\" type=\"text\" />";
					$_html.= '<img width="1" height="1" class="date_selector" src="/images/blank.gif" title="'.Text::_("Select date").'"	alt="D" />';
					$_html.= "</div></div></div>";
					$_html.= "</div></div>";
				} elseif ($value['type']=="currency") {
					$fakes=$flt->parseCurrencyFilter($cur_val);
					$_html.= "<div class=\"singleRow row\"><div class=\"col-sm-12 formlabel\">".Text::_($value['title'])."</div></div>";
					$_html.= "<div class=\"singleRow row\"><div class=\"col-sm-12 forminput\">";
					$_html.= "<div class=\"row filtercurrency\">";
					$cur_val1=floatval($fakes[0]);
					$_html.= "<div class=\"col-xss-4 col-xs-2 formlabel\">".Text::_("from")."</div>"
							."<div class=\"col-xss-8 col-xs-4\"><input name=\"fake1_".$cur_key."\" id=\"fake1_".$cur_key."\" value=\"".$cur_val1."\" class=\"form-control numeric\" maxlength=\"10\" size=\"10\" type=\"text\" /></div>";
					$cur_val2=floatval($fakes[1]);
					/*
					if(Request::getSafe("view")=="goods" && Request::getSafe("layout")=="info"){
						$group_id=$helper->getParentGroup($psid);
					} else {
						$group_id=$psid;
					}
					*/
					$max_price = $helper->getMaxPriceInGroup($without_parents ? 0 : $group_id, substr($cur_key,strpos($cur_key, "_")+1));
					if (!$cur_val2) $cur_val2=$max_price;
					$_html.= "<div class=\"col-xss-4 col-xs-2 formlabel\">".Text::_("till")."</div>"
							."<div class=\"col-xss-8 col-xs-4\"><input name=\"fake2_".$cur_key."\" id=\"fake2_".$cur_key."\" value=\"".$cur_val2."\" class=\"form-control numeric\" maxlength=\"10\" size=\"10\" type=\"text\" /></div>";
					$_html.= "</div>";
					$_html.= '<div id="filterslider_'.$cur_key.'"></div>';
					$_html.= '<input type="hidden" class="filterslider_max_val" id="fake2_max_'.$cur_key.'" value="'.$max_price.'" />';
					$script.='$(window).on(\'load\',function(){
								$("#filterslider_'.$cur_key.'").slider({
									min: 0,
									max: '.$max_price.',
									values: ['.$cur_val1.','.$cur_val2.'],
									range: true,
									stop: function(event, ui) {
										$("#fake1_'.$cur_key.'").val(jQuery("#filterslider_'.$cur_key.'").slider("values",0));
										$("#fake2_'.$cur_key.'").val(jQuery("#filterslider_'.$cur_key.'").slider("values",1));
									},
									slide: function(event, ui){
										$("#fake1_'.$cur_key.'").val(jQuery("#filterslider_'.$cur_key.'").slider("values",0));
										$("#fake2_'.$cur_key.'").val(jQuery("#filterslider_'.$cur_key.'").slider("values",1));
									}
								});
							});
					';
					$_html.= '</div></div>';
					if($cur_val) $filtered.= "<span class=\"w_filter_row\"><span class=\"w_reset_filter_key\" onclick=\"catalog_ext_flt_reset_key('".$div_id."','frmfilterext','".$cur_key."', this);\"></span><span class=\"w_filter_row_title\">".strval(Text::_($filter_arr[$key]['title'])."</span> : ".$flt->parseCurrencyFilterToStr($cur_val)."</span>");
				} elseif (($value['type']=="int")||($value['type']=="float")) {
					$fakes=$flt->parseFloatFilter($cur_val);
					if($cur_val) $filtered.= "<span class=\"w_filter_row\"><span class=\"w_reset_filter_key\" onclick=\"catalog_ext_flt_reset_key('".$div_id."','frmfilterext','".$cur_key."', this);\"></span><span class=\"w_filter_row_title\">".strval(Text::_($filter_arr[$key]['title'])."</span> : ".$flt->parseFloatFilterToStr($cur_val)."</span>");
					if (siteConfig::$intervalNumFilter){
						$_html.= "<div class=\"singleRow row\"><div class=\"col-sm-12 formlabel\">".Text::_($value['title'])."</div></div>";
						$_html.= "<div class=\"singleRow row\"><div class=\"col-sm-12 forminput\">";
						$_html.= "<div class=\"row filterfloat filterint filterinterval\">";
						$cur_val=floatval($fakes[0]);
						$_html.= "<div class=\"col-xs-2 formlabel\">".Text::_("from")."</div>"
								."<div class=\"col-xs-4\"><input name=\"fake1_".$cur_key."\" id=\"fake1_".$cur_key."\" value=\"".$cur_val."\" class=\"form-control numeric\" maxlength=\"20\" size=\"10\" type=\"text\" /></div>";
						$cur_val=floatval($fakes[1]);
						$_html.= "<div class=\"col-xs-2 formlabel\">".Text::_("till")."</div>"
								."<div class=\"col-xs-4\"><input name=\"fake2_".$cur_key."\" id=\"fake2_".$cur_key."\" value=\"".$cur_val."\" class=\"form-control numeric\" maxlength=\"20\" size=\"10\" type=\"text\" /></div>";
						$_html.= '</div>';
						$_html.= '</div></div>';
					} else {
						$cur_sel_eq=""; $cur_sel_lt=""; $cur_sel_gt="";
						if ($fakes[0]==$this->equis[1]) $cur_sel_eq=$_selected;
						elseif ($fakes[0]==$this->equis[2]) $cur_sel_lt=$_selected;
						elseif ($fakes[0]==$this->equis[3]) $cur_sel_gt=$_selected;
						$cur_val=$fakes[1];
						$_html.= "<div class=\"singleRow row\">";
						$_html.= "	<div class=\"col-sm-12 formlabel\">".Text::_($value['title'])."</div>";
						$_html.= "	<div class=\"col-sm-12\">";
						$_html.= "		<div class=\"wrapper-filter-numeric\">";
						$_html.= "			<input type=\"text\" class=\"filterfloat filterint numeric form-control\"  name=\"fake2_".$cur_key."\" id=\"fake2_".$cur_key."\" value=\"".$cur_val."\" />";
						$_html.= "			<select class=\"eq singleSelect form-control\" id=\"fake1_".$cur_key."\" name=\"fake1_".$cur_key."\" size=\"1\">";
						$_html.= "				<option value=\"".$this->equis[0]."\">*</option><option ".$cur_sel_eq." value=\"".$this->equis[1]."\">=</option><option ".$cur_sel_lt." value=\"".$this->equis[2]."\">&lt;</option><option ".$cur_sel_gt." value=\"".$this->equis[3]."\">&gt;</option>";
						$_html.= "			</select>";
						$_html.= "		</div>";
						$_html.= "	</div>";
						$_html.= "</div>";
					}
				}	else {
					if($cur_val) $filtered.= "<span class=\"w_filter_row\"><span class=\"w_reset_filter_key\" onclick=\"catalog_ext_flt_reset_key('".$div_id."','frmfilterext','".$cur_key."', this);\"></span><span class=\"w_filter_row_title\">".strval(Text::_($filter_arr[$key]['title'])."</span> : ".$cur_val."</span>");
					$_html.= "<div class=\"singleRow row\"><div class=\"col-sm-12 formlabel\">".Text::_($value['title'])."</div>";
					$_html.= "<div class=\"col-sm-12 forminput\"><input type=\"text\" class=\"form-control\" id=\"".$cur_key."\" name=\"".$cur_key."\" value=\"".$cur_val."\" /></div></div>";
				}
			}
			$_html.= "<div class=\"buttons\">";
			$_html.= "<input type=\"hidden\" name=\"no_pages\" value=\"0\" />";
			$_html.= "<input type=\"hidden\" name=\"without_parents\" value=\"".$without_parents."\" />";
			$_html.= "<input name=\"save_filter\" type=\"submit\" class=\"commonButton btn btn-info\" value=\"".Text::_("Apply")."\" />";
			$_html.= "<input name=\"reset_filter\" type=\"submit\" class=\"commonButton btn btn-info\" value=\"".Text::_("Reset")."\" />";
			$_html.= "</div>";
		}	else	{
			$_html.= "<div class=\"singleRow row\"><div class=\"col-sm-12\">";
			$_html.= Text::_("Filter fields undefined");
			$_html.= "</divd></div>";
		}
		if($show_switch_filter_mode_button) {
			$_html.= "<div class=\"singleRow row flt-ext-button\"><div class=\"col-sm-12\">";
			if($this->flt_ext_mode){
				$_html.= "<a onclick=\"catalog_ext_flt_set_mode(0,'".$div_id."','frmfilterext');\">".Text::_("Simple filter")."</a>";
			} else {
				$_html.= "<a onclick=\"catalog_ext_flt_set_mode(1,'".$div_id."','frmfilterext');\">".Text::_("Extended filter")."</a>";
			}
			$_html.= "</div></div>";
		}
		if($show_own_filter_panel && $filtered) $_html_full.= "<div class=\"singleRow row\"><div class=\"col-sm-12  filter_text\">".$filtered."</div></div>";
		$_html_full.=$_html;
		$_html_full.= "</form>";
		if($script) Portal::getInstance()->addScriptDeclaration($script);
		return $_html_full;
	}
}
?>