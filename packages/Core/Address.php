<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Address extends BaseObject{
	public static $address_types = array("0" => "Address delivering", "1" => "Address living", "2" => "Address registration");
	public static $tmpl_id = array(
			'psid' => '',
			'type_id' => 0,
			'country_id' => '',
			'region_id' => '',
			'district_id' => '',
			'locality_id' => '',
			'zipcode' => '',
			'street' => '',
			'house' => '',
			'apartment' => '',
			'use_as_default' => 0,
			'fullinfo' => ''
	);
	private static $tmpl_id_required = array(
			'psid' => true,
			'type_id' => true,
			'country_id' => true,
			'region_id' => true,
			'district_id' => true,
			'locality_id' => true,
			'zipcode' => true,
			'street' => true,
			'house' => true,
			'apartment' => true,
			'use_as_default' => true,
			'fullinfo' => true
	);
	public static $tmpl_text = array(
			'psid' => '',
			'type_id' => 0,
			'country' => '',
			'region' => '',
			'district' => '',
			'locality' => '',
			'zipcode' => '',
			'street' => '',
			'house' => '',
			'apartment' => '',
			'use_as_default' => 0,
			'fullinfo' => ''
	);
	private static $tmpl_text_required = array(
			'psid' => true,
			'type_id' => true,
			'country' => true,
			'region' => true,
			'district' => true,
			'locality' => false,
			'zipcode' => true,
			'street' => true,
			'house' => true,
			'apartment' => true,
			'use_as_default' => true,
			'fullinfo' => true
	);

	public static function getTmpl($text = false){
		if($text) return self::$tmpl_text;
		if(siteConfig::$useTextAddress) return self::$tmpl_text; else return self::$tmpl_id;
	}
	public static function getRequiredTmpl(){
		if(siteConfig::$useTextAddress){
			foreach(self::$tmpl_text as $key=>$val){
				if (self::$tmpl_text_required[$key]) $result[]=$key;
			}
		} else {
			foreach($tmpl=self::$tmpl_id as $key=>$val){
				if (self::$tmpl_id_required[$key]) $result[]=$key;
			}
		}
		return $result;
	}
	public static function getTypeTitle($type){
		if (isset(self::$address_types[$type])) return Text::_(self::$address_types[$type]);
		else return "";
	}
	public static function getCountryName($id=0){
		if (!$id) return "";
		$country=self::getCountry($id,false);
		if ($country) return $country->c_name; else return "";
	}
	public static function getCountry($id=0, $enabled_only=true){
		if (!$id) return false;
		$sql="SELECT * FROM #__addr_countries WHERE c_id=".(int)$id;
		if ($enabled_only) $sql.=" AND c_enabled=1 AND c_deleted=0";
		Database::getInstance()->setQuery($sql);
		$country=false;
		Database::getInstance()->loadObject($country);
		return $country;
	}

	public static function getCountries($enabled_only=true){
		$sql="SELECT c_id as id, c_name as title FROM #__addr_countries";
		if ($enabled_only) $sql.=" WHERE c_enabled=1 AND c_deleted=0";
		$sql.=" ORDER BY c_ordering, title";
		Database::getInstance()->setQuery($sql);
		return Database::getInstance()->loadObjectList();
	}
	
	public static function getRegionName($id=0){
		if (!$id) return "";
		$region=self::getRegion($id,false);
		if ($region) return $region->r_name; else return "";
	}
	
	public static function getRegion($id=0,$enabled_only=true){
		if(!$id) return false;
		$sql="SELECT * FROM #__addr_regions WHERE r_id=".(int)$id;
		if ($enabled_only) $sql.=" AND r_enabled=1 AND r_deleted=0";
		Database::getInstance()->setQuery($sql);
		$region=false;
		Database::getInstance()->loadObject($region);
		return $region;
	}
	
	public static function getRegions($parent_id=0,$enabled_only=true){
		if(!$parent_id) return null;
		$sql="SELECT r_id as id, r_name as title FROM #__addr_regions WHERE r_parent_id=".(int)$parent_id;
		if ($enabled_only) $sql.=" AND r_enabled=1 AND r_deleted=0";
		$sql.=" ORDER BY r_ordering,title";
		Database::getInstance()->setQuery($sql);
		return Database::getInstance()->loadObjectList();
	}

	public static function getDistrictName($id=0){
		if (!$id) return "";
		$obj=self::getDistrict($id,false);
		if ($obj) return $obj->d_name; else return "";
	}
	public static function getDistrict($id=0, $enabled_only=true) {
		if (!$id) return false;
		$sql="SELECT * FROM #__addr_districts WHERE d_id=".(int)$id;
		if ($enabled_only) $sql.=" AND d_enabled=1 AND d_deleted=0";
		Database::getInstance()->setQuery($sql);
		$obj=false;
		Database::getInstance()->loadObject($obj);
		return $obj;
	}
	public static function getDistricts($parent_id=0,$enabled_only=true) {
		if (!$parent_id) return null;
		$sql="SELECT d_id as id, d_name as title FROM #__addr_districts WHERE d_parent_id=".(int)$parent_id;
		if ($enabled_only) $sql.=" AND d_enabled=1 AND d_deleted=0";
		$sql.=" ORDER BY d_ordering, title";
		Database::getInstance()->setQuery($sql);
		return Database::getInstance()->loadObjectList();
	}

	public static function getLocalityName($id=0){
		if (!$id) return "";
		$obj=self::getLocality($id,false);
		if ($obj) return $obj->l_name; else return "";
	}
	public static function getLocality($id=0, $enabled_only=true) {
		if (!$id) return false;
		$sql="SELECT * FROM #__addr_localities WHERE l_id=".(int)$id;
		if ($enabled_only) $sql.=" AND l_enabled=1 AND l_deleted=0";
		Database::getInstance()->setQuery($sql);
		$obj=false;
		Database::getInstance()->loadObject($obj);
		return $obj;
	}
	public static function getLocalities($parent_id=0,$enabled_only=true) {
		if (!$parent_id) return null;
		$sql="SELECT l_id as id, l_name as title FROM #__addr_localities WHERE l_parent_id=".(int)$parent_id;
		if ($enabled_only) $sql.=" AND l_enabled=1 AND l_deleted=0";
		$sql.=" ORDER BY l_ordering, title";
		Database::getInstance()->setQuery($sql);
		return Database::getInstance()->loadObjectList();
	}
	
	public static function renderCountrySelector($ctrl_prefix="",$address=array(), $required=false, $enabled_only=true, $_js=array()) {
		if(siteConfig::$useTextAddress) {
			if(!isset($address['country'])) $text=''; else $text=$address['country'];
			$control_name=$ctrl_prefix."country";
			$html=HTMLControls::renderInputText($control_name, $text, false, 150, "", "", false, $required, "", $_js);
		} else {
			if(count($address)) {
				$psid=$address['country_id'];
			} else $psid=0;
			$list=self::getCountries($enabled_only);
			$control_name=$ctrl_prefix."country_id";

			if(is_array($_js) && isset($_js["onchange"])) $js_string=$_js["onchange"]; else $js_string="";
			$js["onchange"]="updateRegionSelector('".$ctrl_prefix."','country_id','region_id','district_id','locality_id');".$js_string;
			$html=self::renderSelector($control_name, "", "id", "title", $list, $psid, true, $js, $required);
		}
		return $html;
	}

	public static function renderRegionSelector($parent_id, $ctrl_prefix="", $address=array(),$required=false, $enabled_only=true, $_js=array()) {
		if(siteConfig::$useTextAddress) {
			if(!isset($address['region'])) $text=''; else $text=$address['region'];
			$control_name=$ctrl_prefix."region";
			$html=HTMLControls::renderInputText($control_name, $text, false,150,"", "", false, $required, "", $_js);
		}	else {
			if(count($address)) {
				$parent_id=$address['country_id'];
				$psid=$address['region_id'];
			} else $psid=0;
			$list=self::getRegions($parent_id,$enabled_only);
			$control_name=$ctrl_prefix."region_id";

			if(is_array($_js) && isset($_js["onchange"])) $js_string=$_js["onchange"]; else $js_string="";
			$js["onchange"]="updateDistrictSelector('".$ctrl_prefix."','region_id','district_id','locality_id');".$js_string; 
			$html=self::renderSelector($control_name, "", "id", "title", $list, $psid, true, $js, $required);
		}
		return $html;
	}

	public static function renderDistrictSelector($parent_id, $ctrl_prefix="", $address=array(), $required=false, $enabled_only=true, $_js=array())	{
		if(siteConfig::$useTextAddress) {
			if(!isset($address['district'])) $text=''; else $text=$address['district'];
			$control_name=$ctrl_prefix."district";
			$html=HTMLControls::renderInputText($control_name, $text, false,150,"", "", false, $required, "", $_js);
		} else {
			if(count($address)) {
				$parent_id=$address['region_id'];
				$psid=$address['district_id'];
			} else $psid=0;
				
			$list=self::getDistricts($parent_id,$enabled_only);
			$control_name=$ctrl_prefix."district_id";

			if(is_array($_js) && isset($_js["onchange"])) $js_string=$_js["onchange"]; else $js_string="";
			$js["onchange"]="updateLocalitySelector('".$ctrl_prefix."', 'district_id','locality_id');".$js_string;
			$html=self::renderSelector($control_name, "", "id", "title", $list, $psid, true, $js, $required);
		}
		return $html;
	}

	public static function renderLocalitySelector($parent_id, $ctrl_prefix="", $address=array(), $required=false, $enabled_only=true, $_js=array())	{
		if(siteConfig::$useTextAddress) {
			if(!isset($address['locality'])) $text=''; else $text=$address['locality'];
			$control_name=$ctrl_prefix."locality";
			$html=HTMLControls::renderInputText($control_name, $text, false,150,"", "", false, $required, "", $_js);
		} else {
			if(count($address)) {
				$parent_id=$address['district_id'];
				$psid=$address['locality_id'];
			} else $psid=0;
	
			$list=self::getLocalities($parent_id,$enabled_only);
			$control_name=$ctrl_prefix."locality_id";
			$html=self::renderSelector($control_name, "", "id", "title", $list, $psid, true, $_js, $required);
		}
		return $html;
	}
	
	public static function renderTypeSelector($type_id, $_js=array()) {
		foreach (self::$address_types as $key=>$val){
			$address_types[$key]=Text::_($val);
		}
		$html=self::renderSelector("type_id", "", "", "", $address_types, $type_id, false, $_js, false);
		return $html;
	}
	public static function decode($data, $asString=false){
		$address=self::getTmpl();
		$data=json_decode(base64_decode($data),true);
		foreach($address as $key=>$val){
			if (isset($data[$key])) $address[$key]=$data[$key]; else $address[$key]=$val;
		}
		if ($asString) return $address['fullinfo'];
		else return $address;
	}
	public static function renderEditor($data, $show_fullinfo=true, $use_as_default=true, $submit=array(), $prefix=""){
		if(!is_array($data)) $address=self::decode($data);
		else $address = $data;
		$required = self::getRequiredTmpl();
		if(!isset($address['country_id'])) $address['country_id'] = 0;
		if(!isset($address['region_id'])) $address['region_id'] = 0;
		if(!isset($address['district_id'])) $address['district_id'] = 0;
		if(!isset($address['locality_id'])) $address['locality_id'] = 0;
		$require_country=(in_array("country",$required)||in_array("country_id",$required));
		$require_district=(in_array("district",$required)||in_array("district_id",$required));
		$require_locality=(in_array("locality",$required)||in_array("locality_id",$required));
		$require_region=(in_array("region",$required)||in_array("region_id",$required));
		$html="<div class=\"addressPanel\">";
		$html.="	<div class=\"row addr-zipcode\"><div class=\"col-sm-5\">".HTMLControls::renderLabelField($prefix."zipcode", Text::_("zip_code"))."</div><div class=\"col-sm-7\">".HTMLControls::renderInputText($prefix."zipcode", $address['zipcode'],6,6, "", (in_array('zipcode', $required) ? "required form-control" : "form-control"))."</div></div>";
		$html.="	<div class=\"row addr-country\"><div class=\"col-sm-5\">".HTMLControls::renderLabelField($prefix.(siteConfig::$useTextAddress ? "country" : "country_id"), Text::_("Country"))."</div><div class=\"col-sm-7\">".Address::renderCountrySelector($prefix, $address, $require_country)."</div></div>";
		$html.="	<div class=\"row addr-region\"><div class=\"col-sm-5\">".HTMLControls::renderLabelField($prefix.(siteConfig::$useTextAddress ? "region" : "region_id"), Text::_("Region"))."</div><div class=\"col-sm-7\">".Address::renderRegionSelector(0, $prefix, $address, $require_region)."</div></div>";
		$html.="	<div class=\"row addr-district\"><div class=\"col-sm-5\">".HTMLControls::renderLabelField($prefix.(siteConfig::$useTextAddress ? "district" : "district_id"), Text::_("District/Town"))."</div><div class=\"col-sm-7\">".Address::renderDistrictSelector(0, $prefix, $address, $require_district)."</div></div>";
		$html.="	<div class=\"row addr-locality\"><div class=\"col-sm-5\">".HTMLControls::renderLabelField($prefix.(siteConfig::$useTextAddress ? "locality" : "locality_id"), Text::_("Locality"))."</div><div class=\"col-sm-7\">".Address::renderLocalitySelector(0, $prefix, $address, $require_locality)."</div></div>";
		$html.="	<div class=\"row addr-street\"><div class=\"col-sm-5\">".HTMLControls::renderLabelField($prefix."street", Text::_("Street"))."</div><div class=\"col-sm-7\">".HTMLControls::renderInputText($prefix."street", $address['street'],false,100, "", (in_array('street', $required) ? "required form-control" : "form-control"))."</div></div>";
		$html.="	<div class=\"row addr-house\"><div class=\"col-sm-5\">".HTMLControls::renderLabelField($prefix."house", Text::_("House"))."</div><div class=\"col-sm-7\">".HTMLControls::renderInputText($prefix."house", $address['house'],10,10, "", (in_array('house', $required) ? "required form-control" : "form-control"))."</div></div>";
		$html.="	<div class=\"row addr-apartment\"><div class=\"col-sm-5\">".HTMLControls::renderLabelField($prefix."apartment", Text::_("Apartment"))."</div><div class=\"col-sm-7\">".HTMLControls::renderInputText($prefix."apartment", $address['apartment'],10,10, "", (in_array('apartment', $required) ? "required form-control" : "form-control"))."</div></div>";
		if($show_fullinfo){
			$html.="	<div class=\"row addr-fullinfo\"><div class=\"col-sm-5\">".HTMLControls::renderLabelField($prefix."fullinfo", Text::_("Manual input"))."</div><div class=\"col-sm-7\">".HTMLControls::renderInputText($prefix."fullinfo", $address['fullinfo'], false, 200, "", "form-control", false, (in_array('fullinfo', $required) ?  true : false))."</div></div>";
		} else {
			$html.=HTMLControls::renderHiddenField($prefix."fullinfo", "");
		}
		if($use_as_default){
			$html.="	<div class=\"row addr-use_as_default\"><div class=\"col-sm-5 col-xs-9\">".HTMLControls::renderLabelField($prefix."use_as_default", Text::_("Use as default"))."</div><div class=\"col-sm-7 col-xs-3\">".HTMLControls::renderCheckbox($prefix."use_as_default", $address["use_as_default"])."</div></div>";
		}
		if (is_array($submit) && count($submit)){
			$html.="	<div class=\"row buttons addr-buttons\"><div class=\"col-sm-12\">".HTMLControls::renderButton($prefix."submit_addr", (isset($submit[1]) ? $submit[1] : Text::_("OK")),  "button",  "", $class='btn btn-info commonButton', $submit[0])."</div></div>";
		} 
		$html.="</div>";
		return $html;
	}
	public static function renderSelector($_name, $_id, $_key_fld, $_val_fld, $_arr, $_sel_val=0, $_zero_fill=true, $_js="", $required=false) {
		if($_id===false) $id_text='';
		else {
			if(!$_id) $_id=$_name;
			$id_text="id=\"".$_id."\"";
		}
		$selected=" selected=\"selected\"";
		if(!is_array($_sel_val)) $_sel_val= preg_split('/[\,]/', $_sel_val);
		if ($required) $required_txt=" required=\"required\""; else $required_txt="";
		
		$_html ="<select class=\"singleSelect form-control\" name=\"".$_name."\" ".$id_text." ".$required_txt;
		if(is_array($_js) && count($_js)){
			foreach ($_js as $act=>$func) {
				$_html.=" ".$act."=\"".$func."\"";
				$_html.=" data-".$act."=\"".$func."\"";
			}
		}
		$_html.=">";
		if($_zero_fill) $_html.="<option value=\"0\"".($_sel_val==0 ? $selected : "")."> --- ".Text::_("Not selected")." --- </option>";
		if (is_array($_arr) && count($_arr)>0) {
			foreach($_arr as $key=>$val) {
				if (is_object($val)) {
					$_html.="<option value=\"".$val->{$_key_fld}."\"".(in_array($val->{$_key_fld}, $_sel_val) ? $selected : "").">".$val->{$_val_fld}."</option>";
				} else {
					if ($_key_fld && $_val_fld) {
						$_html.="<option value=\"".$val[$_key_fld]."\"".(in_array($val[$_key_fld], $_sel_val) ? $selected : "").">".$val[$_val_fld]."</option>";
					} elseif ($_val_fld) {
						$_html.="<option value=\"".$key."\"".(in_array($key, $_sel_val) ? $selected : "").">".$val[$_val_fld]."</option>";
					} else {
						$_html.="<option value=\"".$key."\"".(in_array($key, $_sel_val) ? $selected : "").">".$val."</option>";
					}
				}
			}
		}
		$_html.="</select>";
		return $_html;
	}
}
?>






