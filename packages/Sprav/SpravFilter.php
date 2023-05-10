<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class SpravFilter	{
	protected	$_db			= null;
	private $equis=array("none","eq","lt","gt");
	private $null_date='0000-00-00';

	public function __construct() {
		$this->_db = Database::getInstance();
	}
	
	public function getFilteredStrForm(&$_arr,  $filter_arr){
		$uid = User::getInstance()->getID(true);
		$side = $_arr["side"];
		$module = $_arr["module"];
		$view = $_arr["view"];
		$layout = $_arr["layout"];
		$multy_code = $_arr["multy_code"];
		$psid = $_arr["psid"];
		$orderby = $_arr["orderby"];
		$sort = $_arr["sort"];
		$page = $_arr["page"];
		$controller = $_arr["controller"];
		$trash = $_arr["trash"];

		if(!$_arr["is_selector"]) $reset_js = "resetSpravFilterKey(this, '".$module."', '".$view."', '".$layout."', '".$multy_code."', '".$psid."', '".$orderby."', '".$sort."', '".$page."', '".$controller."', '".$trash."')";
		else $reset_js = "alterAjaxFilterReset();";

		$without_parents = isset($_SESSION['filter_wp'][$module][$uid][$view.".".$layout.".".$side]) && $_SESSION['filter_wp'][$module][$uid][$view.".".$layout.".".$side]==1;
		$no_pages = isset($_SESSION['filter_np'][$module][$uid][$view.".".$layout.".1"]) && $_SESSION['filter_np'][$module][$uid][$view.".".$layout.".1"]==1;
		$filter = $this->getFilter($side,$module, $view,$layout);
		$filtered = "";
		$flt_obj = $this->inFilter($side, $module, $view, $layout, true);
		if($filter_arr && count($filter_arr)) {
			foreach ($filter_arr as $key => $value) {
				$cur_key=str_replace(".", "_", $key);
				if ($filter) {
					if (array_key_exists($key, $filter)) $cur_val=$filter[$key];
					else $cur_val="";
				} else $cur_val="";
				$filtered_tmp = "";
				if($value['ck_reestr']) {
					if (is_array($value['ck_reestr'])) $ck_array=$value['ck_reestr'];
					else $ck_array=SpravStatic::getCKArray($value['ck_reestr']);
					$cur_ck_array=explode("&&", $cur_val);
					$_arr[$cur_key] = array($cur_key=>$cur_ck_array);
					if (count($cur_ck_array)){
						$new_ck_array=array();
						foreach($cur_ck_array as $ck_key){
							if (is_array($filter_arr[$key]['ck_reestr'])) {
								if(isset($filter_arr[$key]['ck_reestr'][$ck_key])) $new_ck_array[]=$filter_arr[$key]['ck_reestr'][$ck_key];
								else $new_ck_array[]=$ck_key;
							} else $new_ck_array[]=SpravView::getValueFromCKArray($filter_arr[$key]['ck_reestr'], $ck_key);
						}
						$filtered_tmp = implode(" && ",$new_ck_array);
					}
				} elseif ($value['type']=="boolean") {
					$_arr[$cur_key] = $cur_val;
					if (!isset($bool_array)) $bool_array=array(0=>Text::_('N'), 1=>Text::_('Y'));
					if(isset($bool_array[$cur_val])) $filtered_tmp = $bool_array[$cur_val];
				} elseif (($value['type']=="date")||($value['type']=="datetime")) {
					$fakes=$this->parseDateFilter($cur_val);
					$_arr[$cur_key] = array();
					$_arr[$cur_key]["fake1_".$cur_key] = $fakes[0];
					$_arr[$cur_key]["fake2_".$cur_key] = $fakes[1]==$this->null_date ? "" : Date::fromSQL($fakes[1],true,true);
					$_arr[$cur_key]["fake3_".$cur_key] = $fakes[2];
					$_arr[$cur_key]["fake4_".$cur_key] = $fakes[3]==$this->null_date ? "" : Date::fromSQL($fakes[3],true,true);
					$filtered_tmp = $this->parseDateFilterToStr($cur_val);
				} elseif ($value['type']=="currency") {
					$fakes=$this->parseCurrencyFilter($cur_val);
					$_arr[$cur_key] = array();
					$_arr[$cur_key]["fake1_".$cur_key] = floatval($fakes[0]);
					$_arr[$cur_key]["fake2_".$cur_key] = floatval($fakes[1]);
					$filtered_tmp = $this->parseCurrencyFilterToStr($cur_val);
				} elseif (($value['type']=="int")||($value['type']=="float")) {
					$fakes=$this->parseFloatFilter($cur_val);
					$_arr[$cur_key] = array();
					if (siteConfig::$intervalNumFilter) $_arr[$cur_key]["fake1_".$cur_key] = floatval($fakes[0]);
					else $_arr[$cur_key]["fake1_".$cur_key] = $fakes[0];
					$_arr[$cur_key]["fake2_".$cur_key] = floatval($fakes[1]);
					$filtered_tmp = $this->parseFloatFilterToStr($cur_val);
				} else {
					$_arr[$cur_key] = $cur_val;
					$filtered_tmp = $cur_val;
				}
				if($filtered_tmp) {
					$filtered.= "<span class=\"flt_wrapper\"><span class=\"flt_title\">".Text::_($filter_arr[$key]['title']).":</span><span class=\"flt_value\">".$filtered_tmp."</span>".($reset_js ? "<span class=\"flt_reset_wrapper\"><span class=\"flt_reset_key\" onclick=\"".$reset_js."\" data-key=\"".$key."\"></span></span>" : "")."</span>";
				}
			}
			if (!defined("_ADMIN_MODE")){
				$no_pages = 0;
				// $without_parents = 1;
			}
			$_arr["no_pages"] = $no_pages;
			$_arr["without_parents"] = $without_parents;
		}
		if(!$filtered) $_arr = array();
		// @TODO Check if NEW VARIANT works fine on frontend
		/*
		// OLD VARIANT START
		if (defined("_ADMIN_MODE") && $without_parents) {
			$filtered.= "<span class=\"flt_wrapper\"><span class=\"flt_title\">".Text::_("Without parents")."</span></span>";
		}
		// OLD VARIANT STOP
		*/
		// NEW VARIANT START
		if ($without_parents) {
			if(defined("_ADMIN_MODE")){
				$filtered.= "<span class=\"flt_wrapper\"><span class=\"flt_title\">".Text::_("Without parents")."</span></span>";
			} elseif(!$filtered) {
				$reset_all_js = "resetSpravFilterAll(this, '".$module."', '".$view."', '".$layout."', '".$multy_code."', '".$psid."', '".$orderby."', '".$sort."', '".$page."', '".$controller."', '".$trash."')";
				$filtered.= "<span class=\"flt_wrapper\"><span class=\"flt_title\">".Text::_("Without parents")."</span>".($reset_all_js ? "<span class=\"flt_reset_wrapper\"><span class=\"flt_reset_key\" onclick=\"".$reset_all_js."\"></span></span>" : "")."</span>";
			}
		}
		// NEW VARIANT STOP
		return $filtered;
	}
/*
	public function getFilteredStr($module, $view, $layout, $side, $filter_arr, $uid) {
		$filtered = "";
		$without_parents = isset($_SESSION['filter_wp'][$module][$uid][$view.".".$layout.".".$side]) && $_SESSION['filter_wp'][$module][$uid][$view.".".$layout.".".$side]==1;
		$flt_obj = $this->inFilter($side, $module, $view, $layout, true);
		if ($flt_obj){
			foreach($flt_obj as $flt_row) {
				$filtered_tmp = "";
				//var_dump($flt_row->f_key, $flt_row->f_val); echo "<br/>";
				if (!array_key_exists($flt_row->f_key, $filter_arr)) continue;
				if ($filter_arr[$flt_row->f_key]['type']=="boolean") {
					if (!isset($bool_array)) $bool_array=array(0=>Text::_('N'), 1=>Text::_('Y'));
					$filtered_tmp = $bool_array[$flt_row->f_val];
				} elseif ($filter_arr[$flt_row->f_key]['ck_reestr']){
					$cur_ck_array=explode("&&", $flt_row->f_val);
					if (count($cur_ck_array)){
						$new_ck_array=array();
						foreach($cur_ck_array as $ck_key){
							if (is_array($filter_arr[$flt_row->f_key]['ck_reestr'])) {
								if(isset($filter_arr[$flt_row->f_key]['ck_reestr'][$ck_key])) $new_ck_array[]=$filter_arr[$flt_row->f_key]['ck_reestr'][$ck_key];
								else $new_ck_array[]=$ck_key;
							} else $new_ck_array[]=SpravView::getValueFromCKArray($filter_arr[$flt_row->f_key]['ck_reestr'], $ck_key);
						}
						$filtered_tmp = implode(" && ",$new_ck_array);
					}
				} elseif ($filter_arr[$flt_row->f_key]['type']=="currency") {
					$filtered_tmp = $this->parseCurrencyFilterToStr($flt_row->f_val);
				} elseif (($filter_arr[$flt_row->f_key]['type']=="date")||($filter_arr[$flt_row->f_key]['type']=="datetime")) {
					$filtered_tmp = $this->parseDateFilterToStr($flt_row->f_val);
				} elseif (($filter_arr[$flt_row->f_key]['type']=="int")||($filter_arr[$flt_row->f_key]['type']=="float")) {
					$filtered_tmp = $this->parseFloatFilterToStr($flt_row->f_val);
				} else {
					$filtered_tmp = $flt_row->f_val;
				}
				if($filtered_tmp) {
					$filtered.= "<span class=\"flt_wrapper\"><span class=\"flt_title\">".Text::_($filter_arr[$flt_row->f_key]['title']).":</span><span class=\"flt_value\">".$filtered_tmp."</span></span>";
				}
			}
		}
		if (defined("_ADMIN_MODE") && $without_parents) {
			$filtered.= "<span class=\"flt_wrapper\"><span class=\"flt_title\">".Text::_("Without parents")."</span></span>";
		}
		return $filtered;
	}
*/
	public function getForm($module,$view,$layout,$multy_code=0,$trash=0,$controller='default') {
		$_selected="selected=\"selected\"";
		if(defined("_ADMIN_MODE")) $side=1; else $side=0;
		if(isset($_SESSION[$module][$view.".".$layout.".".$side]['filt_arr'])) $filter_arr = $_SESSION[$module][$view.".".$layout.".".$side]['filt_arr'];
		else  $filter_arr=false;
		if(isset($_SESSION[$module][$view.".".$layout.".".$side]['show_woparents'])) $show_woparents= $_SESSION[$module][$view.".".$layout.".".$side]['show_woparents'];
		else  $show_woparents=true;
		if(isset($_SESSION[$module][$view.".".$layout.".".$side]['show_nopages'])) $show_nopages= $_SESSION[$module][$view.".".$layout.".".$side]['show_nopages'];
		else  $show_nopages=true;
		
		if(isset($_SESSION[$module][$view.".".$layout.".".$side]['add_filter_hidden_fields'])) $add_filter_hidden_fields = $_SESSION[$module][$view.".".$layout.".".$side]['add_filter_hidden_fields'];
		else $add_filter_hidden_fields = array();
		
		$filter=$this->getFilter($side,$module, $view,$layout);
		if ($filter) { foreach ($filter as $key => $value) { $fkey=$key; $fval=$value; } }
		else {$fkey=''; $fval='';}
		$_html = "<div class=\"filter container-fluid\"><form name=\"frmfilterext\" method=\"post\" action=\"".(defined("_ADMIN_MODE") ? "index.php" : Router::_("index.php"))."\" >";
		if($controller && $controller != "default") $_html.= "<input type=\"hidden\" name=\"controller\" value=\"".$controller."\"  />";
		$_html.= "<input type=\"hidden\" name=\"module\" value=\"".$module."\"  />";
		$_html.= "<input type=\"hidden\" name=\"view\" value=\"".$view."\"  />";
		$_html.= "<input type=\"hidden\" name=\"layout\" value=\"".$layout."\"  />";
		$_html.= "<input type=\"hidden\" name=\"multy_code\" value=\"".$multy_code."\"  />";
		$_html.= "<input type=\"hidden\" name=\"trash\" value=\"".$trash."\"  />";
		if(is_array($add_filter_hidden_fields)){
			foreach($add_filter_hidden_fields as $ahf_key=>$ahf_val){
				$_html.= "<input type=\"hidden\" name=\"".$ahf_key."\" value=\"".$ahf_val."\"  />";
			}
		}
		if($filter_arr&&count($filter_arr)) {
			foreach ($filter_arr as $key => $value) { 
				if ($key==$fkey) {$selected=" selected=\"selected\"";} else {$selected="";}
				$cur_key=str_replace(".","_",$key);
				if ($filter) {
					if (array_key_exists($key,$filter)) {$cur_val=$filter[$key];} else {$cur_val="";}
				}	else $cur_val="";
				if($value['ck_reestr']) {
					if (is_array($value['ck_reestr'])) $ck_array=$value['ck_reestr'];
					else $ck_array=SpravStatic::getCKArray($value['ck_reestr']);
					$_html.= "<div class=\"singleRow row\">";
					$_html.= "	<div class=\"col-sm-5\">".HTMLControls::renderLabelField($cur_key, Text::_($value['title']).":")."</div>";
					$_html.= "	<div class=\"col-sm-7\">".HTMLControls::renderSelect($cur_key, $cur_key, false, false, $ck_array, str_replace("&&", ";",$cur_val), false, false, "auto", "multi form-control")."</div>";
					$_html.= "</div>";
				} elseif ($value['type']=="boolean") {
					$cur_sel_0="";$cur_sel_1="";$cur_sel="";
					if ($cur_val=="0") $cur_sel_0=$_selected;
					elseif ($cur_val=="1") $cur_sel_1=$_selected;
					else $cur_sel=$_selected;
					$_html.= "<div class=\"singleRow row\">";
					$_html.= "	<div class=\"col-sm-5\">".HTMLControls::renderLabelField($cur_key, Text::_($value['title']).":")."</div>";
					$_html.= "	<div class=\"col-sm-7\"><select class=\"bln singleSelect form-control\" id=\"".$cur_key."\" name=\"".$cur_key."\" size=\"1\">";
					$_html.= "		<option ".$cur_sel." value=\"-1\">".Text::_("All")."</option><option ".$cur_sel_0." value=\"0\">".Text::_("N")."</option><option ".$cur_sel_1." value=\"1\">".Text::_("Y")."</option>";
					$_html.= "	</select></div>";
					$_html.= "</div>";
				} elseif (($value['type']=="date")||($value['type']=="datetime")) {
					$fakes=$this->parseDateFilter($cur_val);
					if ($fakes[1]==$this->null_date) $cur_val=""; else $cur_val=Date::fromSQL($fakes[1],true,true);
					$cur_sel_eq=""; $cur_sel_lt=""; $cur_sel_gt="";
					if ($fakes[0]==$this->equis[1]) $cur_sel_eq=$_selected;
					elseif ($fakes[0]==$this->equis[2]) $cur_sel_lt=$_selected;
					elseif ($fakes[0]==$this->equis[3]) $cur_sel_gt=$_selected;
					$_html.= "<div class=\"singleRow row\">";
					$_html.= "	<div class=\"col-sm-5\">".HTMLControls::renderLabelField($cur_key, Text::_($value['title']).":")."</div>";
					$_html.= "	<div class=\"col-sm-7\">";
					$_html.= "		<div class=\"wrapper-filter-datetime\">";
					$_html.= "			<select class=\"eq singleSelect form-control\" id=\"fake1_".$cur_key."\" name=\"fake1_".$cur_key."\" size=\"1\">";
					$_html.= "				<option value=\"".$this->equis[0]."\">*</option><option ".$cur_sel_eq." value=\"".$this->equis[1]."\">=</option><option ".$cur_sel_lt." value=\"".$this->equis[2]."\">&lt;</option><option ".$cur_sel_gt." value=\"".$this->equis[3]."\">&gt;</option>";
					$_html.= "			</select>";
					$_html.= "			<input name=\"fake2_".$cur_key."\" id=\"fake2_".$cur_key."\" value=\"".$cur_val."\" class=\"datepicker form-control\" readonly=\"readonly\" maxlength=\"10\" size=\"10\" type=\"text\" />";
					$_html.= '			<img width="1" height="1" class="date_selector form-control" src="/images/blank.gif" title="'.Text::_("Select date").'"	alt="D" />';
					$_html.= "		</div>";
					$_html.= "	</div>";
					$_html.= "</div>";
					if ($fakes[3]==$this->null_date) $cur_val=""; else $cur_val=Date::fromSQL($fakes[3],true,true);
					$cur_sel_eq=""; $cur_sel_lt=""; $cur_sel_gt="";
					if ($fakes[2]==$this->equis[1]) $cur_sel_eq=$_selected;
					elseif ($fakes[2]==$this->equis[2]) $cur_sel_lt=$_selected;
					elseif ($fakes[2]==$this->equis[3]) $cur_sel_gt=$_selected;
					$_html.= "<div class=\"singleRow row\">";
					$_html.= "	<div class=\"col-sm-5\">".HTMLControls::renderLabelField($cur_key, Text::_($value['title']).":")."</div>";
					$_html.= "	<div class=\"col-sm-7\">";
					$_html.= "		<div class=\"wrapper-filter-datetime\">";
					$_html.= "			<select class=\"eq singleSelect form-control\" id=\"fake3_".$cur_key."\" name=\"fake3_".$cur_key."\" size=\"1\">";
					$_html.= "				<option value=\"".$this->equis[0]."\">*</option><option ".$cur_sel_eq." value=\"".$this->equis[1]."\">=</option><option ".$cur_sel_lt." value=\"".$this->equis[2]."\">&lt;</option><option ".$cur_sel_gt." value=\"".$this->equis[3]."\">&gt;</option>";
					$_html.= "			</select>";
					$_html.= "			<input name=\"fake4_".$cur_key."\" id=\"fake4_".$cur_key."\" value=\"".$cur_val."\" class=\"datepicker form-control\" readonly=\"readonly\" maxlength=\"10\" size=\"10\" type=\"text\" />";
					$_html.= '			<img width="1" height="1" class="date_selector" src="/images/blank.gif" title="'.Text::_("Select date").'"	alt="D" />';
					$_html.= "		</div>";
					$_html.= "	</div>";
					$_html.= "</div>";
				} elseif ($value['type']=="currency") {
					$fakes=$this->parseCurrencyFilter($cur_val);
					$_html.= "<div class=\"singleRow row\">";
					$_html.= "	<div class=\"col-sm-5\">".HTMLControls::renderLabelField($cur_key, Text::_($value['title']).":")."</div>";
					$_html.= "	<div class=\"col-sm-7\">";
					$_html.= "		<div class=\"row filtercurrency\">";
					$cur_val=floatval($fakes[0]);
					$_html.= "			<div class=\"col-xs-2\">".HTMLControls::renderLabelField(false, Text::_("from"))."</div>";
					$_html.= "			<div class=\"col-xs-4\"><input name=\"fake1_".$cur_key."\" id=\"fake1_".$cur_key."\" value=\"".$cur_val."\" class=\"form-control numeric\" maxlength=\"10\" size=\"10\" type=\"text\" /></div>";
					$cur_val=floatval($fakes[1]);
					$_html.= "			<div class=\"col-xs-2\">".HTMLControls::renderLabelField(false, Text::_("till"))."</div>";
					$_html.= "			<div class=\"col-xs-4\"><input name=\"fake2_".$cur_key."\" id=\"fake2_".$cur_key."\" value=\"".$cur_val."\" class=\"form-control numeric\" maxlength=\"10\" size=\"10\" type=\"text\" /></div>";
					$_html.= "		</div>";
					$_html.= "	</div>";
					$_html.= "</div>";
				} elseif (($value['type']=="int")||($value['type']=="float")) {
					$fakes=$this->parseFloatFilter($cur_val);
					if (siteConfig::$intervalNumFilter){
						$_html.= "<div class=\"singleRow row\">";
						$_html.= "	<div class=\"col-sm-5\">".HTMLControls::renderLabelField($cur_key, Text::_($value['title']).":")."</div>";
						$_html.= "	<div class=\"col-sm-7\">";
						$_html.= "		<div class=\"row filterfloat filterint filterinterval\">";
						$cur_val=floatval($fakes[0]);
						$_html.= "			<div class=\"col-xs-2\">".HTMLControls::renderLabelField(false, Text::_("from"))."</div>";
						$_html.= "			<div class=\"col-xs-4\"><input name=\"fake1_".$cur_key."\" id=\"fake1_".$cur_key."\" value=\"".$cur_val."\" class=\"form-control numeric\" maxlength=\"20\" size=\"10\" type=\"text\" /></div>";
						$cur_val=floatval($fakes[1]);
						$_html.= "			<div class=\"col-xs-2\">".HTMLControls::renderLabelField(false, Text::_("till"))."</div>";
						$_html.= "			<div class=\"col-xs-4\"><input name=\"fake2_".$cur_key."\" id=\"fake2_".$cur_key."\" value=\"".$cur_val."\" class=\"form-control numeric\" maxlength=\"20\" size=\"10\" type=\"text\" /></div>";
						$_html.= "		</div>";
						$_html.= "	</div>";
						$_html.= "</div>";
					} else {
						$cur_sel_eq=""; $cur_sel_lt=""; $cur_sel_gt="";
						if ($fakes[0]==$this->equis[1]) $cur_sel_eq=$_selected;
						elseif ($fakes[0]==$this->equis[2]) $cur_sel_lt=$_selected;
						elseif ($fakes[0]==$this->equis[3]) $cur_sel_gt=$_selected;
						$cur_val=$fakes[1];
						$_html.= "<div class=\"singleRow row filterfloat filterint\">";
						$_html.= "	<div class=\"col-sm-5\">".HTMLControls::renderLabelField($cur_key, Text::_($value['title']).":")."</div>";
						$_html.= "	<div class=\"col-sm-7\">";
						$_html.= "		<div class=\"wrapper-filter-numeric\">";
						$_html.= "			<select class=\"eq singleSelect form-control\" id=\"fake1_".$cur_key."\" name=\"fake1_".$cur_key."\" size=\"1\">";
						$_html.= "				<option value=\"".$this->equis[0]."\">*</option><option ".$cur_sel_eq." value=\"".$this->equis[1]."\">=</option><option ".$cur_sel_lt." value=\"".$this->equis[2]."\">&lt;</option><option ".$cur_sel_gt." value=\"".$this->equis[3]."\">&gt;</option>";
						$_html.= "			</select>";
						$_html.= "			<input type=\"text\" class=\"form-control numeric\"  name=\"fake2_".$cur_key."\" id=\"fake2_".$cur_key."\" value=\"".$cur_val."\" />";
						$_html.= "		</div>";
						$_html.= "	</div>";
						$_html.= "</div>";
					}
				} else {
					$_html.= "<div class=\"singleRow row\">";
					$_html.= "	<div class=\"col-sm-5\">".HTMLControls::renderLabelField($cur_key, Text::_($value['title']))."</div>";
					$_html.= "	<div class=\"col-sm-7\"><input type=\"text\" class=\"form-control\" id=\"".$cur_key."\" name=\"".$cur_key."\" value=\"".$cur_val."\" /></div>";
					$_html.= "</div>";
				}
			}
			if (defined("_ADMIN_MODE")){
				$uid = User::getInstance()->getID(); // It's admin mode, session id not need
				if (isset($_SESSION['filter_np'][$module][$uid][$view.".".$layout.".1"])
						&&($_SESSION['filter_np'][$module][$uid][$view.".".$layout.".1"]==1)) $no_pages_status=" checked=\"checked\"";
				else $no_pages_status="";
				if($show_nopages) {
					$_html.= "<div class=\"singleRow row\">";
					$_html.= "	<div class=\"col-sm-7\"><label for=\"no_pages\">".Text::_("Without pagination")."</label></div>";
					$_html.= "	<div class=\"col-sm-5\"><input id=\"no_pages\" name=\"no_pages\" type=\"checkbox\"".$no_pages_status." value=\"1\" /></div>";
					$_html.= "</div>";
				} else {
					if($no_pages_status) $_html.= HTMLControls::renderHiddenField("no_pages",1);
					else $_html.= HTMLControls::renderHiddenField("no_pages",0);
				}
				if (isset($_SESSION['filter_wp'][$module][$uid][$view.".".$layout.".".$side])
						&&($_SESSION['filter_wp'][$module][$uid][$view.".".$layout.".".$side]==1)) $without_parents_status=" checked=\"checked\"";
				else $without_parents_status="";
				if($trash || !$show_woparents) {
					if($without_parents_status) $_html.= HTMLControls::renderHiddenField("without_parents",1); 
					else $_html.= HTMLControls::renderHiddenField("without_parents",0);
				} else {
					$_html.= "<div class=\"singleRow row\">";
					$_html.= "	<div class=\"col-sm-7\"><label for=\"without_parents\">".Text::_("Without parents")."</label></div>";
					$_html.= "	<div class=\"col-sm-5\"><input id=\"without_parents\" name=\"without_parents\" type=\"checkbox\"".$without_parents_status." value=\"1\" /></div>";
					$_html.= "</div>";
				}
				
			} else {
				$_html.= HTMLControls::renderHiddenField("no_pages",0); //"<input type=\"hidden\" name=\"no_pages\" value=\"0\" />";
				$_html.= HTMLControls::renderHiddenField("without_parents",1);// "<input type=\"hidden\" name=\"without_parents\" value=\"1\" />";
			}
		}	else	{
			$_html.= "<div class=\"warning singleRow row\"><div class=\"col-sm-12\">";
			$_html.= Text::_("Filter fields undefined");
			$_html.= "</div>";
		}
		if (defined("_ADMIN_MODE")||($filter_arr && count($filter_arr))){
			$_html.= "<div class=\"buttons\">";
			$_html.= "<input name=\"save_filter\" type=\"submit\" class=\"commonButton btn btn-info\" value=\"".Text::_("Apply")."\" />";
			$_html.= "<input name=\"reset_filter\" type=\"submit\" class=\"commonButton btn btn-info\" value=\"".Text::_("Reset")."\" />";
			$_html.= "</div>";
		}
		$_html.= "</form></div>";
		return $_html;
	}

	public function inFilter($side,$module,$view=false,$layout='',$return_obj=false) {
		$result=false;
		$uid=User::getInstance()->getID(true);
		//if(!$uid) $uid=session_id();
		$f_sql_str="SELECT * FROM #__filters WHERE f_module='".$module."' AND f_uid='$uid'";
		if ($view&&$layout) $f_sql_str .= "  AND f_view='".$view."' AND f_layout='".$layout."' AND f_side='".$side."'";
		$this->_db->setQuery($f_sql_str);
		$sel_filt = $this->_db->loadObjectList();
		//if (!defined("_ADMIN_MODE")&&count($sel_filt)) $_SESSION['filter_wp'][$module][$uid][$view.".".$layout.".0"]=0;
		if($sel_filt) {$result = Text::_("Is filtered"); }
		if($return_obj) return $sel_filt;
		return $result;
	}

	public function getFilter($side,$module,$view=false,$layout='') {
		$uid=User::getInstance()->getID(true);
		//if(!$uid) $uid=session_id();
		$result=false;
		$f_sql_str="SELECT f_key, f_val from #__filters
					WHERE f_module='".$module."' AND f_side='".$side."' AND f_uid='$uid'";
		if ($view&&$layout) $f_sql_str .= "  AND f_view='".$view."' AND f_layout='".$layout."'";
		$this->_db->setQuery($f_sql_str);
		$res=$this->_db->loadObjectList();
		if(count($res)) {
			foreach($res as $value) {
				$fkey = $value->f_key;
				$fval = $value->f_val;
				if (!$result) {$result = array($fkey=>$fval);}
				else {$result=array_merge($result, array($fkey=>$fval));}
			}
		}
		return $result;
	}

	public function appendSQL($filter_arr,$module,$view=false,$layout='') {
		$result='';
		if(defined("_ADMIN_MODE")) $side=1; else $side=0;
		$filter=$this->getFilter($side,$module,$view,$layout);
		if ($filter)	{
			$sup_str = "";  $sup_str_sub = "";
			foreach ($filter as $key => $value) {
				$fkey=$key;	$fval=$value;
				if (($fkey<>'')&&($fval<>'')) {
					if(array_key_exists($fkey, $filter_arr)) {
						if(($filter_arr[$fkey]['type']=="date")||$filter_arr[$fkey]['type']=="datetime") {
							$fakes=$this->parseDateFilter($fval);
							if ($fakes[0]!=$this->equis[0])	{ // есть знак сравнения
								if ($fakes[1]!=$this->null_date)	{ // есть с чем сравнивать
									if ($sup_str) {$sup_str .= " AND ";}
									if ($fakes[0]==$this->equis[1]) $sup_str .= $fkey." LIKE '".$fakes[1]."%'"	;
									elseif ($fakes[0]==$this->equis[2]) $sup_str .= $fkey." < '".$fakes[1]."'"	;
									elseif ($fakes[0]==$this->equis[3]) $sup_str .= $fkey." > '".$fakes[1]."'"	;
								}
							}
							if ($fakes[2]!=$this->equis[0])	{ // есть знак сравнения
								if ($fakes[3]!=$this->null_date)	{ // есть с чем сравнивать
									if ($sup_str) {$sup_str .= " AND ";}
									if ($fakes[2]==$this->equis[1]) $sup_str .= $fkey." LIKE '".$fakes[3]."%'"	;
									elseif ($fakes[2]==$this->equis[2]) $sup_str .= $fkey." < '".$fakes[3]."'"	;
									elseif ($fakes[2]==$this->equis[3]) $sup_str .= $fkey." > '".$fakes[3]."'"	;
								}
							}
						} elseif($filter_arr[$fkey]['ck_reestr']) {
							if ($sup_str) {$sup_str .= " AND ";}
							// проверяем значение из базы на наличие разделителя для составного фильтра (&&)
							if (substr_count($fval, "&&")) {$values = explode("&&", $fval);} // если нашли разбиваем строку на массив
							else {$values = array($fval);} // иначе пихаем в массив единственное значение
							foreach ($values as $keys=>$vals) {
								if ($sup_str_sub) $sup_str_sub .= " OR ";
								if($filter_arr[$fkey]['input_type']=="multiselect") $sup_str_sub .= $fkey." LIKE '%;".$vals.";%'";
								else $sup_str_sub .= $fkey." = '".$vals."'";  
							}
							$sup_str .= "($sup_str_sub)";		$sup_str_sub = "";
						} elseif($filter_arr[$fkey]['type']=="currency") {
							$fakes=$this->parseCurrencyFilter($fval);
							if(floatval($fakes[0])==floatval($fakes[1])){
								if ($sup_str) { $sup_str .= " AND "; }
								$sup_str .= $fkey." = ".$fakes[0];
							} else {
								if ($sup_str) { $sup_str .= " AND "; }
								$sup_str .= $fkey." >= ".$fakes[0]	;
								if(floatval($fakes[0])<=floatval($fakes[1])){
									if ($sup_str) { $sup_str .= " AND "; }
									$sup_str .= $fkey." <= ".$fakes[1]	;
								}
							}
						} elseif($filter_arr[$fkey]['type']=="int" || $filter_arr[$fkey]['type']=="float") {
							$fakes=$this->parseFloatFilter($fval);
							if (siteConfig::$intervalNumFilter){
								if(floatval($fakes[0])==floatval($fakes[1])){
									if ($sup_str) { $sup_str .= " AND "; }
									$sup_str .= $fkey." = ".$fakes[0];
								} else {
									if ($sup_str) { $sup_str .= " AND "; }
									$sup_str .= $fkey." >= ".$fakes[0]	;
									if(floatval($fakes[0])<=floatval($fakes[1])){
										if ($sup_str) { $sup_str .= " AND "; }
										$sup_str .= $fkey." <= ".$fakes[1]	;
									}
								}
							} else {		
								if ($fakes[0]!=$this->equis[0])	{ // есть знак сравнения
									if ($sup_str) {$sup_str .= " AND ";}
									if ($fakes[0]==$this->equis[1]) $sup_str .= $fkey." = ".$fakes[1]	;
									elseif ($fakes[0]==$this->equis[2]) $sup_str .= $fkey." < ".$fakes[1]	;
									elseif ($fakes[0]==$this->equis[3]) $sup_str .= $fkey." > ".$fakes[1]	;
								}
							}
						}	else {
							if ($sup_str) {$sup_str .= " AND ";}
							// проверяем значение из базы на наличие разделителя для составного фильтра (&&)
							if (substr_count($fval, "&&")) {$values = explode("&&", $fval);} // если нашли разбиваем строку на массив
							else {$values = array($fval);} // иначе пихаем в массив единственное значение
							foreach ($values as $keys=>$vals){ 
								if ($sup_str_sub) {$sup_str_sub .= " OR ";}		
								$matches=array();
								// Определяем строгий фильтр по написанию, например [[Наш завод]]
								preg_match("/\[\[([\S|\s]+)\]\]/", $vals, $matches);
								if(count($matches)==2){ // Получили массив array(2) { [0]=> string(21) "[[Наш завод]]" [1]=> string(17) "Наш завод" } 
									$sup_str_sub .= $fkey."= '".$matches[1]."'";
								}else{	
									if($filter_arr[$fkey]['strict']) $sup_str_sub .= $fkey."='".$vals."'";
									else $sup_str_sub .= $fkey." LIKE '%".$vals."%'";
								}  								  
							}
							$sup_str .= "($sup_str_sub)";		$sup_str_sub = "";
						}
					}
				}
			}
			if ($sup_str) {$result.=" AND ($sup_str)";}
		}
		return $result;
	}
	// здесь $mode=0 это однозначный сброс
	public function saveFilterString($filter_arr,$module,$view=false,$layout='',$mode)  {
		if(defined("_ADMIN_MODE")) $side=1; else $side=0;
		$uid=User::getInstance()->getID(true);
		//if(!$uid) $uid=session_id();
		// выясняем надо ли обрубать лимиты
		if(Request::get('reset_filter',false)) {	
			$no_pages=0;
			$without_parents=0;
//			$this->resetFilterString($module,$view,$layout);
		} else {
			if (defined("_ADMIN_MODE")){
				$no_pages=Request::get('no_pages',0);
			} else {
				$no_pages = 0;
			}
			$without_parents=Request::get('without_parents',0);
		}
// echo "<br />save filter  ".$module."-".$uid."-".$view."-".$layout."-".$no_pages."-".$without_parents;
		if ($view) {
			$_SESSION['filter_np'][$module][$uid][$view.".".$layout.".".$side]=$no_pages;
			$_SESSION['filter_wp'][$module][$uid][$view.".".$layout.".".$side]=$without_parents;
			$keys_array=array();
			foreach ($filter_arr as $key => $value) {
				if ($mode==0)  {
					if ($value['type']=="boolean") $val=-1;
					else	$val="";
				} else {
					$cur_key=str_replace(".","_",$key);
					if ($value['type']=="boolean") {
						$cur_val=Request::getInt($cur_key,"-1");
						if ($cur_val == 0 || $cur_val == 1) $val = $cur_val; else $val = -1;
					} elseif ($value['ck_reestr']) {
						$cur_ck_select=Request::getSafe($cur_key,"");
						if (is_array($cur_ck_select)) $val=implode("&&", $cur_ck_select);
						else $val="";
					} elseif (($value['type']=="date")||($value['type']=="datetime")) {
						$fake_1=Request::getSafe('fake1_'.$cur_key,'');
						$fake_2=Request::getSafe('fake2_'.$cur_key,'');
						$fake_3=Request::getSafe('fake3_'.$cur_key,'');
						$fake_4=Request::getSafe('fake4_'.$cur_key,'');
						$val=$this->calcDateFilter($fake_1, $fake_2, $fake_3, $fake_4);
					} elseif ($value['type']=="currency") {
						$fake_1=Request::getFloat('fake1_'.$cur_key,'');
						$fake_2=Request::getFloat('fake2_'.$cur_key,'');
						$val=$this->calcCurrencyFilter($fake_1, $fake_2);
					} elseif ($value['type']=="float") {
						$fake_1=Request::getSafe('fake1_'.$cur_key,'');
						$fake_2=Request::getFloat('fake2_'.$cur_key,'');
						$val=$this->calcFloatFilter($fake_1, $fake_2);
					} elseif ($value['type']=="int") {
						$fake_1=Request::getSafe('fake1_'.$cur_key,'');
						$fake_2=Request::getInt('fake2_'.$cur_key,'');
						$val=$this->calcFloatFilter($fake_1, $fake_2);
					}	else {
						$cur_key=Request::getSafe($cur_key,"");
						if ($cur_key) {$val=$cur_key;} else {$val="";}
					}
				}
 				if ((($value['type']=="boolean")&&($val>=0))||(($value['type']!="boolean")&&($val))) {
					$f_sql_str="INSERT INTO #__filters (f_time,f_uid,f_module,f_view,f_layout,f_side,f_key,f_val)
								VALUES (".time().",'$uid','$module','".$view."','".$layout."',".$side.",'".$key."','".$val."')
								ON DUPLICATE KEY UPDATE f_val='".$val."'";
					$this->_db->setQuery($f_sql_str);
					$f_sql = $this->_db->query($f_sql_str);
				} elseif (($value['type']=="boolean" && $val<0)|| ($value['type']!="boolean" && !$val)){
//					$this->resetFilterKey($module, $view, $layout, $side, $key, $uid);
					$keys_array[]=$key;
				}
			}
			if(count($keys_array)) $this->resetFilterKeysArray($module, $view, $layout, $side, $keys_array, $uid);
		}
	}
	public function resetFilterKey($module, $view, $layout, $side, $key, $uid)  {
		$f_sql_str = "DELETE FROM #__filters WHERE (f_module='".$module."' AND f_view='".$view."' AND f_layout='".$layout."' AND f_side='".$side."' AND f_key='".$key."' AND f_uid='".$uid."')";
		$this->_db->setQuery($f_sql_str);
		return $this->_db->query($f_sql_str);
	}
	public function resetFilterKeysArray($module, $view, $layout, $side, $keys_array, $uid)  {
		$f_sql_str = "DELETE FROM #__filters WHERE (f_module='".$module."' AND f_view='".$view."' AND f_layout='".$layout."' AND f_side='".$side."' AND f_uid='".$uid."' AND f_key IN ('".implode("', '", $keys_array)."'))";
		$this->_db->setQuery($f_sql_str);
		return $this->_db->query($f_sql_str);
	}
	public function resetFilterString($module,$view, $layout)  {
		$uid=User::getInstance()->getID(true);
		//if(!$uid) $uid=session_id();
		if(defined("_ADMIN_MODE")) $side=1; else $side=0;
		$_SESSION['filter_np'][$module][$uid][$view.".".$layout.".".$side]=NULL;
		$_SESSION['filter_wp'][$module][$uid][$view.".".$layout.".".$side]=NULL;
		if ($view) {
			$timeback=time()-60*60*24*7;
			$f_sql_str = "DELETE FROM #__filters WHERE (f_module='".$module."' AND f_uid='".$uid."' AND f_view='".$view."' AND f_layout='".$layout."' AND f_side='".$side."') OR f_time<".$timeback;
			$this->_db->setQuery($f_sql_str);
			$f_sql = $this->_db->query($f_sql_str);
			unset($_SESSION[$module][$view.".".$layout.".".$side]['filt_arr']);
			Event::raise("filter.reset");
			return true;
		} else { return false; }
	}
	public function parseFloatFilter($val) {
		$fake=preg_split('/(#)/',$val);
		if (count($fake)==2) return $fake;
		elseif(siteConfig::$intervalNumFilter) return array(0, 0); 
		else return array($this->equis[0], 0);
	}
	public function parseFloatFilterToStr($val) {
		$fakes=$this->parseFloatFilter($val); $result="";
		if(siteConfig::$intervalNumFilter) {
			if ($fakes[0])	{
				$result .= " ".Text::_("from")." ".$fakes[0]	;
			}
			if ($fakes[1])	{
				$result .= " ".Text::_("till")." ".$fakes[1]	;
			}
		} else {
			if ($fakes[0]!=$this->equis[0])	{ // есть знак сравнения
				if ($fakes[0]==$this->equis[1]) 	$result .= " = ".$fakes[1]	;
				elseif ($fakes[0]==$this->equis[2]) $result .= " < ".$fakes[1]	;
				elseif ($fakes[0]==$this->equis[3]) $result .= " > ".$fakes[1]	;
			}
		}
		return $result;
	}
	public function parseCurrencyFilter($val) {
		$fake=preg_split('/(#)/',$val);
		if (count($fake)==2) return $fake;
		else return array(0, 0);
	}
	public function parseCurrencyFilterToStr($val) {
		$fakes=$this->parseCurrencyFilter($val); $result="";
		if($fakes[0]!=$fakes[1]){
			if ($fakes[0])	{ 
				$result .= " ".Text::_("from")." ".$fakes[0]	;
			}
			if ($fakes[1])	{ 
				$result .= " ".Text::_("till")." ".$fakes[1]	;
			}
		} else {
			$result .= $fakes[1]	;
		}
		return $result;
	}
	public function parseDateFilter($val) {
		$fake=preg_split('/(#)/',$val);
		if (count($fake)==4) return $fake;
		else return array($this->equis[0], $this->null_date,$this->equis[0], $this->null_date);
	}
	public function parseDateFilterToStr($val) {
		$fakes=$this->parseDateFilter($val); $result="";
		if ($fakes[0]!=$this->equis[0])	{ // есть знак сравнения
			if ($fakes[1]!=$this->null_date)	{ // есть с чем сравнивать
				$first_date=Date::fromSQL($fakes[1],true);
				if ($fakes[0]==$this->equis[1]) 		$result .= " = '".$first_date."'"	;
				elseif ($fakes[0]==$this->equis[2]) $result .= " < '".$first_date."'"	;
				elseif ($fakes[0]==$this->equis[3]) $result .= " > '".$first_date."'"	;
			}
		}
		if ($fakes[2]!=$this->equis[0])	{ // есть знак сравнения
			if ($fakes[3]!=$this->null_date)	{ // есть с чем сравнивать
				$second_date=Date::fromSQL($fakes[3],true);
				if ($result) {$result .= " ".Text::_("and")." ";}
				if ($fakes[2]==$this->equis[1]) 		$result .= " = '".$second_date."'"	;
				elseif ($fakes[2]==$this->equis[2]) $result .= " < '".$second_date."'"	;
				elseif ($fakes[2]==$this->equis[3]) $result .= " > '".$second_date."'"	;
			}
		}
		return $result;
	}
	public function calcDateFilter($fake_1, $fake_2, $fake_3, $fake_4) {
		$eq_1=$this->equis[0]; $date_1=$this->null_date; $eq_2=$this->equis[0]; $date_2=$this->null_date;
		if (!$fake_2) {$eq_1="none";}	if (!$fake_4) {$eq_2="none";}
		if (in_array($fake_1,$this->equis)) { $eq_1=$fake_1; }
		if (in_array($fake_3,$this->equis)) { $eq_2=$fake_3; }
		if ($eq_1!=$this->equis[0]) { $date_1=Date::toSQL($fake_2, true); }
		if ($eq_2!=$this->equis[0]) { $date_2=Date::toSQL($fake_4, true); }
		if (($eq_1==$this->equis[0])&&($eq_2==$this->equis[0])) return "";
		return $eq_1."#".$date_1."#".$eq_2."#".$date_2;
	}
	public function calcCurrencyFilter($fake_1, $fake_2) {
		if (!$fake_1 && !$fake_2) return "";
		if ($fake_1 > $fake_2 && $fake_2 != 0) return "";
		return $fake_1."#".$fake_2;
	}
	public function calcFloatFilter($fake_1, $fake_2) {
		if (siteConfig::$intervalNumFilter){
			if (!$fake_1 && !$fake_2) return "";
			if ($fake_1 > $fake_2 && $fake_2 != 0) return "";
			return $fake_1."#".$fake_2;
		} else {
			$eq_1=$this->equis[0]; $val_1=0;
			if (in_array($fake_1,$this->equis)) { $eq_1=$fake_1; }
			if ($eq_1!=$this->equis[0]) { $val_1=$fake_2; }
			if ($eq_1==$this->equis[0]) return "";
			return $eq_1."#".$val_1;
		}
	}
}
?>