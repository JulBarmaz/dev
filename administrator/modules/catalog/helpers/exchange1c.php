<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogHelperExchange1c extends BaseObject{
	private $_db = null;
	private $_module = "catalog";
	private $_meta = array();
	private $_el_templates = array();
	private $_el_files_templates = array();
	private $_error = false;
	private $_error_descr = array();
	private $changes_only = null;
	private $log_level=0;
	private $log_file="exchange1c.log";
	private $autoexchange=false;
	private $offers_mode=0;
	private $export_system="";
	
	public function __construct() {
		$this->initObj();
		$this->_db = Database::getInstance();
	}
	public function dump2screen($var, $stop_executing=false){
		// https://BARMAZ-cms.web/administrator/index.php?dump=screen&option=ajax&task=processImport1C&module=catalog&view=exchange1c&field_id=files_0&filename=import.xml
		// https://BARMAZ-cms.web/administrator/index.php?dump=screen&option=ajax&task=processImport1C&module=catalog&view=exchange1c&field_id=files_1&filename=offers.xml
		// https://BARMAZ-cms.web/administrator/index.php?dump=screen&option=ajax&task=processImport1C&module=catalog&view=exchange1c&field_id=files_0&filename=orders-import.xml
		if (defined("_BARMAZ_DEVELOPER_EXCHANGE1C") 
			&& 
			(
				(!$this->autoexchange && Request::getSafe("option")!="ajax") 
				|| 
				Request::getSafe("dump")=="screen"
			)
		) {
			Util::pre($var);
			if($stop_executing) {
				if($this->isError()) Util::pre("HELPERS ERRORS: ".CR_LF.$this->getErrorText(false));
				Util::halt("Halted in dump2screen. Memory usage (".Debugger::getInstance()->getMemoryString(true).") Time: ".Debugger::getInstance()->getTime()."sec");
			}
		}
	}
	public function setExportSystem($mode=""){
		$this->export_system = $mode;
	}
	public function setOffersMode($mode=0){
		$this->offers_mode = $mode;
	}
	public function setAutoexchange($autoexchange=false){
		$this->autoexchange = $autoexchange;
	}
	public function setChangesOnly($val){
		$this->changes_only = $val;
	}
	public function setLogLevel($val){
		$this->log_level=$val;
	}
	public function setLogFile($val){
		$this->log_file=$val;
	}
	private function getParam($param_name, $subs_default_value=true, $custom_default_value=null){
		return Module::getInstance($this->_module)->getParam($param_name, $subs_default_value, $custom_default_value);
	}
	/******** Logs are the same as in model. Start ********/
	private function log($message, $level=0, $with_date=true){
		if($level<=$this->log_level){
			Util::writeLog($message, $this->log_file, $with_date);
		}
	}
	private function logTitle($message, $level=1){
		$this->log(str_repeat("#", 100), $level, false);
		$this->log(mb_strtoupper($message, DEF_CP), $level);
		$this->log(str_repeat("#", 100), $level, false);
	}
	private function logError($message, $level=1){
		$this->log("[ERROR] ".$message, $level);
	}
	private function logWarning($message, $level=1){
		$this->log("[WARNING] ".$message, $level);
	}
	private function logInfo($message, $level=2){
		$this->log("[INFO] ".$message, $level);
	}
	private function logDebug($message, $level=3){
		$this->log("[DEBUG] ".$message, $level);
	}
	/******** Logs are the same as in model. Stop ********/
	private function setError($message){
		$this->_error=true;
		$this->_error_descr[] = $message;
		$this->logError($message);
	}
	public function isError(){
		return $this->_error;
	}
	public function getErrorText($as_array=true, $br=false){
		if($as_array) return $this->_error_descr;
		elseif($br) return implode("<br />", $this->_error_descr);
		else return implode(CR_LF, $this->_error_descr);
	}
	private function getMetaVars($meta_index=""){
		$metavars=array();
		switch ($meta_index){
			case "fields_list":
				$metavars["module"]="conf";
				$metavars["view"]="dopfields";
				$metavars["layout"]="default";
				$metavars["extcode"]="f_extcode";
				break;
			case "fields_choices":
				$metavars["module"]="conf";
				$metavars["view"]="dopfields_choices";
				$metavars["layout"]="default";
				$metavars["extcode"]="fc_extcode";
				break;
			case "goods":
				$metavars["module"]="catalog";
				$metavars["view"]="goods";
				$metavars["layout"]="default";
				$metavars["extcode"]="g_extcode";
				break;
			case "goodsgroup":
				$metavars["module"]="catalog";
				$metavars["view"]="goodsgroup";
				$metavars["layout"]="default";
				$metavars["extcode"]="ggr_extcode";
				break;
			case "manufacturers":
				$metavars["module"]="catalog";
				$metavars["view"]="manufacturers";
				$metavars["layout"]="default";
				$metavars["extcode"]="mf_extcode";
				break;
			case "measures":
				$metavars["module"]="catalog";
				$metavars["view"]="measures";
				$metavars["layout"]="default";
				$metavars["extcode"]="meas_extcode";
				break;
			case "options":
				$metavars["module"]="catalog";
				$metavars["view"]="options";
				$metavars["layout"]="default";
				$metavars["extcode"]="o_extcode";
				break;
			case "options_data":
				$metavars["module"]="catalog";
				$metavars["view"]="options_data";
				$metavars["layout"]="default";
				$metavars["extcode"]="od_extcode";
				break;
			case "optionvals":
				$metavars["module"]="catalog";
				$metavars["view"]="optionvals";
				$metavars["layout"]="default";
				$metavars["extcode"]="ov_extcode";
				break;
			case "optionvals_data":
				$metavars["module"]="catalog";
				$metavars["view"]="optionvals_data";
				$metavars["layout"]="default";
				$metavars["extcode"]="ovd_extcode";
				break;
			case "taxes":
				$metavars["module"]="catalog";
				$metavars["view"]="taxes";
				$metavars["layout"]="default";
				$metavars["extcode"]="t_extcode";
				break;
			case "vendors":
				$metavars["module"]="catalog";
				$metavars["view"]="vendors";
				$metavars["layout"]="default";
				$metavars["extcode"]="v_extcode";
				break;
			default:
				$metavars["module"]="";
				$metavars["view"]="";
				$metavars["layout"]="";
				$metavars["extcode"]="";
			break;
		}
		return $metavars;
	}
	private function getGuidField($meta_index=""){
		$metavars=$this->getMetaVars($meta_index);
		if(isset($metavars["extcode"])) return $metavars["extcode"];
		return false;
	}
	private function getElementFilesTemplate($meta_index, $enable=true){
		if(!$meta_index) return false;
		if(!isset($this->_el_files_templates[$meta_index])) $this->getElementTemplate($meta_index, $enable);
		return $this->_el_files_templates[$meta_index];
	}
	private function getElementTemplate($meta_index, $enable=true){
		if(!$meta_index) return false;
		if(!isset($this->_el_templates[$meta_index])){
			$meta=$this->getMeta($meta_index);
			if($meta){
				$tmpl=array(); $files=array();
				foreach ($meta->field as $ind=>$fld){
					if($meta->field_is_method[$ind]) continue;
					if($fld==$meta->enabled){
						$tmpl[$fld]=intval($enable);
						continue;
					}
					if($fld==$meta->deleted){
						$tmpl[$fld]=0;
						continue;
					}
					if($meta->default_value[$ind]!="") {
						$default_value=$meta->default_value[$ind];
						if(isset($meta->constants[$default_value])) {
							$default_value=$meta->constants[$default_value];
						}
					} else $default_value=null;
					switch($meta->val_type[$ind]) {
						case "int":
						case "float":
						case "currency":
							$tmpl[$fld]=($default_value==null ? 0 : $default_value);
							break;
						case "boolean":
							$tmpl[$fld]=($default_value==null ? 0 : $default_value);
							break;
						case "timestamp":
							$tmpl[$fld]=($default_value==null ? 0 : $default_value);
							break;
						case "date":
							$tmpl[$fld]=($default_value==null ? "" : Date::toSQL($default_value, true));
							break;
						case "datetime":
							$tmpl[$fld]=($default_value==null ? "" : Date::toSQL($default_value));
							break;
						default:
							$tmpl[$fld]=($default_value==null ? "" : $default_value);
							break;
					}
					if(!$tmpl[$fld] && $meta->ck_reestr[$ind]){
						if (is_array($meta->ck_reestr[$ind])) $key_arr=$meta->ck_reestr[$ind];
						else $key_arr=SpravStatic::getCKArray($meta->ck_reestr[$ind]);
						if($meta->check_value[$ind]) $tmpl[$fld]=Util::array_key_first($key_arr);
					}
					// Let's make images template
					if($meta->input_type[$ind]=="image" || $meta->input_type[$ind]=="file"){
						$files[$fld]["type"]=$meta->input_type[$ind];
						$files[$fld]["upload_path"]=$meta->upload_path[$ind];
						$files[$fld]["is_add"]=$meta->is_add[$ind];
					}
				}
				$this->_el_templates[$meta_index] = $tmpl;
				$this->_el_files_templates[$meta_index] = $files;
			} else return false;
		}
		return $this->_el_templates[$meta_index];
	}
	private function getMeta($meta_index){
		if(!$meta_index) return false;
		if(!isset($this->_meta[$meta_index])){
			$metavars=$this->getMetaVars($meta_index);
			$this->_meta[$meta_index]=new SpravMetadata($metavars["module"], $metavars["view"], $metavars["layout"], false, false);
			if(!$this->_meta[$meta_index]->success) {
				$this->setError(Text::_("Metadata file not found").": ".implode(".", $metavars));
				unset($this->_meta[$meta_index]);
				return false;
			}
		}
		return $this->_meta[$meta_index];
	}
	private function getElementIdByField($fieldname="", $fieldvalue="", $meta_index="", $enable=false, $restore_deleted=false){
		return $this->getElementIdByFieldAndParent($fieldname, $fieldvalue, false, false, $meta_index, $enable, $restore_deleted);
	}
	private function getElementIdByFieldAndParent($fieldname="", $fieldvalue="", $parent_field=false, $parent_field_val=false, $meta_index="", $enable=false, $restore_deleted=false){
		// Search with empty GUID field, if GUID field exists
		if(!$fieldname || $fieldvalue==="" || !$meta_index) return 0; // Check field value only for empty string
		$meta=$this->getMeta($meta_index);
		$element=false; $psid=0;
		if($meta){
			$guid_field = $this->getGuidField($meta_index);
			$sql = "SELECT `".$meta->keystring."` AS `psid`";
			if($meta->enabled) $sql.= ", `".$meta->enabled."` AS `enabled`";
			if($meta->deleted) $sql.= ", `".$meta->deleted."` AS `deleted`";
			$sql.= " FROM `#__".$meta->tablename."` WHERE `".$fieldname."`='".$fieldvalue."'";
			if($parent_field) $sql.= " AND `".$parent_field."`='".$parent_field_val."'";
			if($fieldname != $guid_field) $sql.=" AND `".$guid_field."`=''";
			$sql.= " LIMIT 1";
			$this->_db->setQuery($sql);
			$this->logDebug($this->_db->getQuery());
// $this->dump2screen($this->_db->getQuery());
			$this->_db->loadObject($element);
			if(!$element) return $psid;
// $this->dump2screen($element);
			$psid=$element->psid;
			if($element->psid && $meta->deleted && $element->deleted){
				if($restore_deleted){ // Marked as deleted. Let's restore ???
					if(!$this->restoreDeletedElement($element->psid, $meta_index)) {
						$this->setError(Text::_("Failed to restore element")." ".$meta_index.":".$psid);
						$psid=0;
					}
				} else {
					if($this->updateElementFieldById($psid, $this->getGuidField($meta_index), "", $meta_index)){
						$this->setError(Text::_("Failed to reset GUID for deleted element")." ".$meta_index.":".$psid);
						$psid=0;
					}
				}
			}
			if($element->psid && $meta->enabled && !$element->enabled){
				if($enable){ // Disabled. Let's enable ???
					if(!$this->enableElement($element->psid, $meta_index)) {
						$this->setError(Text::_("Failed to enable element")." ".$meta_index.":".$psid);
						$psid=0;
					}
				}
			}
		}
		return $psid;
	}
	private function getElementFieldsByFieldAndParent($fields_list=array(), $fieldname="", $fieldvalue="", $parent_field=false, $parent_field_val=false, $meta_index="", $enabled_only=false, $skip_deleted=true){
		// Ignoring GUID field
		if(!count($fields_list) || !$fieldname || $fieldvalue==="" || !$meta_index) return false; // Check field value only for empty string
		$meta=$this->getMeta($meta_index);
		$element=false; $psid=0;
		if($meta){
//			$guid_field = $this->getGuidField($meta_index);
			$sql = "SELECT `".implode("`, `", $fields_list)."`, `".$meta->keystring."` AS `psid`";
			if($meta->enabled) $sql.= ", `".$meta->enabled."` AS `enabled`";
			if($meta->deleted) $sql.= ", `".$meta->deleted."` AS `deleted`";
			$sql.= " FROM `#__".$meta->tablename."` WHERE `".$fieldname."`='".$fieldvalue."'";
			if($enabled_only && $meta->enabled) $sql.= " AND `".$meta->enabled."`='1'";
			if($skip_deleted && $meta->deleted) $sql.= " AND `".$meta->deleted."`='0'";
			if($parent_field) $sql.= " AND `".$parent_field."`='".$parent_field_val."'";
//			if($fieldname != $guid_field) $sql.=" AND `".$guid_field."`=''";
			$sql.= " LIMIT 1";
			$this->_db->setQuery($sql);
			$this->logDebug($this->_db->getQuery());
// $this->dump2screen($this->_db->getQuery());
			$this->_db->loadObject($element);
			if(!$element) return false;
// $this->dump2screen($element);
		}
		return $element;
	}
	private function restoreDeletedElement($psid=0, $meta_index=""){
		if(!$psid || !$meta_index) return false;
		$meta=$this->getMeta($meta_index);
		if($meta && $meta->deleted) return $this->updateElementFieldById($psid, $meta->deleted, 0, $meta_index);
		return false;
	}
	private function enableElement($psid=0, $meta_index=""){
		if(!$psid || !$meta_index) return false;
		$meta=$this->getMeta($meta_index);
		if($meta && $meta->enabled) return $this->updateElementFieldById($psid, $meta->enabled, 1, $meta_index);
		return false;
	}
	private function updateElementFieldById($psid=0, $fieldname="", $fieldvalue="", $meta_index=""){
		$result=false;
		if(!$psid || !$fieldname || !$meta_index) return $result;
		$meta=$this->getMeta($meta_index);
		if($meta){
			$sql = " UPDATE `#__".$meta->tablename."` SET `".$fieldname."`='".$fieldvalue."' WHERE `".$meta->keystring."`=".$psid;
			$this->_db->setQuery($sql);
			$this->logDebug($this->_db->getQuery());
// $this->dump2screen($this->_db->getQuery());
			$result=$this->_db->query();
			if(!$result){
				$this->setError(Text::_("Field update failed")." ".$meta_index.":".$fieldname.":".$result);
			}
		}
		return $result;
	}
	private function getElementIdByGUID($guid="", $meta_index="", $enable=true, $restore_deleted=true){
		if(!$guid) return 0;
		$guid_field=$this->getGuidField($meta_index);
		if($guid_field){
			return $this->getElementIdByField($guid_field, $guid, $meta_index, $enable, $restore_deleted);
		}
		return 0;
	}
	private function updateElementGuidById($psid=0, $guid="", $meta_index=""){
		if($guid===false) return true; // We don't need update, we have no guid in xml files 
		if(!$guid) return false;
		$guid_field=$this->getGuidField($meta_index);
		if($guid_field){
			return $this->updateElementFieldById($psid, $guid_field, $guid, $meta_index);
		}
		return false;
	}
	private function tryToGetElement($guid="", $fld_2="", $fld_2_val="", $meta_index="", $enable=true, $restore_deleted=true){
		return $this->tryToGetElementWithParent($guid, $fld_2, $fld_2_val, false, false, $meta_index, $enable, $restore_deleted);
	}
	private function tryToGetElementWithParent($guid="", $fld_2="", $fld_2_val="", $parent_fld=false, $parent_fld_val=false, $meta_index="", $enable=true, $restore_deleted=true){
		$psid = $this->getElementIdByGUID($guid, $meta_index, $enable, $restore_deleted);
		if(!$psid && $fld_2_val){ // Not found, let's try by field
			$psid = $this->getElementIdByFieldAndParent($fld_2, $fld_2_val, $parent_fld, $parent_fld_val, $meta_index, $enable, $restore_deleted);
			if($psid && !$this->updateElementGuidById($psid, $guid, $meta_index)) return 0; // Found, but failed update GUID
		}
		return intval($psid);
	}
	private function getMetaFieldIndex($fieldname, $meta_index) {
		$meta=$this->getMeta($meta_index);
		if ($meta && count($meta->field)>0) {
			foreach ($meta->field as $key=>$val) {
				if ($val==$fieldname) return $key;
			}
		}
		$this->setError(Text::_("Field index absent")." ".$meta_index.":".$fieldname);
		return 0;
	}
	private function getFirstPossibleParentId($meta_index){
		$meta=$this->getMeta($meta_index);
		$psid=0; $field_index=0;
		if($meta->parent_code && $meta->multy_field){
			$field_index = $this->getMetaFieldIndex($meta->multy_field, $meta_index);
			$sql = "SELECT `".$meta->ch_id[$field_index]."` AS `psid`";
			$sql.= " FROM `#__".$meta->ch_table[$field_index]."`";
			$where_sql = array();
			if($meta->ch_enabled[$field_index] && $meta->ch_skip_disabled[$field_index]) $where_sql[]= "`".$meta->ch_enabled[$field_index]."`=1";
			if($meta->ch_deleted[$field_index] && $meta->ch_skip_deleted[$field_index]) $where_sql[]= "`".$meta->ch_deleted[$field_index]."`=0";
			if(count($where_sql)) $sql.= " WHERE ".implode("AND", $where_sql);
			$sql.= " LIMIT 1";
			$this->_db->setQuery($sql);
			$this->logDebug($this->_db->getQuery());
// $this->dump2screen($this->_db->getQuery());
			$psid=intval($this->_db->loadResult());
		}
		return $psid;
	}
	private function reductionOfType($index, $field, &$meta, $data) {
		$val_type=$meta->val_type[$index];
		switch ($val_type) {
			case "int":
				$temp=(isset($data[$field]) ? intval($data[$field]) : 0);
				break;
			case "boolean":
				$temp=(isset($data[$field]) ? intval($data[$field]) : 0);
				if ($temp) $temp=1;
				break;
			case "string":
				// May be it is file ???
				if(($meta->input_type[$index]=="image") ||($meta->input_type[$index]=="file")) {
					// Let's skip. Must be alredy copied 
					$temp=(isset($data[$field]) ? $data[$field] : "");
				} else {
					$temp=(isset($data[$field]) ? Request::makeSafe($data[$field]) : "");
				}
				break;
			case "text":
				if ($meta->input_type[$index]=="texteditor") {
					$temp=(isset($data[$field]) ? $data[$field] : "");
				} else {
					$temp=(isset($data[$field]) ? Request::makeSafe($data[$field]) : "");
				}
				break;
			case "float":
				$temp_d=(isset($data[$field]) ? Request::makeSafe($data[$field]) : "");
				$temp=floatval(str_replace(",", ".", strval($temp_d)));
				break;
			case "currency":
				$temp_d=(isset($data[$field]) ? Request::makeSafe($data[$field]) : "");
				$temp=floatval(str_replace(",", ".", strval($temp_d)));
				break;
			case "date":
				$temp=(isset($data[$field]) && $data[$field] ? Request::makeSafe($data[$field]) : null);
				$temp=Request::getSafe($field,null);
				if (!is_null($temp)) $temp = Date::toSQL($temp, true);
				break;
			case "datetime":
				$temp=(isset($data[$field]) && $data[$field] ? Request::makeSafe($data[$field]) : null);
				if (!is_null($temp)) $temp = Date::toSQL($temp);
				break;
			case "timestamp":
				$temp=(isset($data[$field]) && $data[$field] ? Request::makeSafe($data[$field]) : false);
				if($temp) $temp = strval($temp);
				break;
			default:
				$this->logError("Undefined metadata value type : -".$val_type."- for ".$meta->tablename.":".$field);
				$temp="";
			break;
		}
		return $temp;
	}
	private function encodeAddress($addr_arr){
		return base64_encode(json_encode($this->parseAddress($addr_arr)));
	}
	private function save($data, $guid, $meta_index){
// $this->dump2screen($data);
		$new_psid=false; $autoinc=false;
		$meta = $this->getMeta($meta_index);
		$col_count=count($meta->field);
		$keystring=$meta->keystring;
		$sql_txt="";
		$sql_ad_del=""; $sql_ad="";
		if(isset($data[$keystring])){
			$psid = $data[$keystring];
			if ($psid)	{ // Updating
				$sql_txt="UPDATE #__".$meta->tablename." SET ";
				$sql_ad_del="DELETE FROM #__".$meta->tablename."_data WHERE obj_id=".$psid.$this->_db->getDelimiter()."\n";
				for ($i = 1; $i <= $col_count; $i++)	{
					$field=$meta->field[$i];
					if(!isset($data[$field])) continue; // Only fields in data array
					if ($keystring==$meta->field[$i]) {
						$new_psid=$psid; continue;
					}
					if ($meta->field_no_update[$i]) continue;
					if ($meta->field_is_method[$i]) continue;
					// if ($meta->input_type[$i]=="label" || $meta->input_type[$i]=="label_sel") continue; // Don't skip this. Change uid and change date will be failed
					// $val_type=$meta->val_type[$i];
					$temp=$this->reductionOfType($i, $field, $meta, $data);
					$field=$meta->field[$i];
// $this->dump2screen($field."=".$temp);
					if (($meta->input_type[$i]=="image") ||($meta->input_type[$i]=="file")) {
						if (!$temp) continue;
					}
					if ($meta->input_type[$i]=="multiselect") {
						if (is_array($temp)) $temp = implode(";", $temp);
						$temp = ";".$temp.";";
					}
					if($meta->is_add[$i])	{ // генерим дополнительные строки запроса
						$obj_id="###keystring###";
						$field_id=$meta->is_add[$i];
						$sql_ad.="INSERT INTO #__".$meta->tablename."_data";
						$sql_ad.=" VALUES('".$obj_id."','".$field_id."','".$field."','".$temp."')";
						$sql_ad.=" ON DUPLICATE KEY UPDATE `field_value`='".$temp."'".$this->_db->getDelimiter()."\n";
					} else {
						$sql_txt.="`".$field."`='".$temp."'";	$sql_txt.=",";
					}
				} // End of for
				$sql_txt=mb_substr($sql_txt, 0, mb_strrpos($sql_txt, ",", 0, DEF_CP), DEF_CP);
				$sql_txt.=" WHERE ".$keystring."='".$psid."';\n";
			}	else { // New
				$sql_txt="INSERT INTO #__".$meta->tablename." (";
				for ($i = 1; $i <= $col_count; $i++)	{
					$field=$meta->field[$i];
					if(!isset($data[$field])) continue; // Only fields in data array
					if($meta->field_is_method[$i]) continue;
					if($meta->is_add[$i]) continue; //пропускаем если поле является дополнительным
					$sql_txt.="`$field`";
					$sql_txt.=",";
				}
				$l=mb_strlen($sql_txt,DEF_CP); $l=($l-1); $sql_txt[$l]=" "; // change the last comma to space
				$sql_txt.=" ) VALUES (";
				for ($i = 1; $i <= $col_count; $i++) {
					$field=$meta->field[$i];
					if(!isset($data[$field])) continue; // Only fields in data array
					if ($keystring==$field) {
						if ($meta->val_type[$i]=="int") $autoinc=true;
					}
					if($meta->field_is_method[$i]) continue; // skip if this is a method
					// $val_type=$meta->val_type[$i];
					$temp = $this->reductionOfType($i, $field, $meta, $data);
					/*
					// Must be set earlier
					if ($meta->input_type[$i]=="label_sel") {
						if ($meta->default_value[$i]) {
							$param_default=$meta->default_value[$i];
							if(isset($meta->constants[$param_default])) {
								$temp=$meta->constants[$param_default];
							} else {
								$temp=$param_default;
							}
						} else {
							if ($meta->val_type[$i]=="int") $temp=Request::getInt("p_".$field."_select",0);
							else {
								$temp=Request::getSafe("p_".$field."_select","");
								if ($temp=="0"||$temp=="")	$temp="";
							}
						}
					}
					*/
					if ($meta->input_type[$i]=="multiselect") {
						if (is_array($temp)) $temp = implode(";", $temp);
						$temp = ";".$temp.";";
					}
					if($meta->is_add[$i]) {
						// генерим дополнительные строки запроса
						$obj_id="###keystring###";
						$field_id=$meta->is_add[$i];
						$sql_ad.="INSERT INTO #__".$meta->tablename."_data";
						$sql_ad.="\n VALUES('".$obj_id."','".$field_id."','".$field."','".$temp."')";
						$sql_ad.="ON DUPLICATE KEY UPDATE `field_value`='".$temp."'".$this->_db->getDelimiter();
					} else {
						$sql_txt.="'".$temp."'";
						$sql_txt.=",";
					}
				}
				$sql_txt=mb_substr($sql_txt, 0, mb_strrpos($sql_txt, ",", 0, DEF_CP), DEF_CP);
				$sql_txt.=" )";
			}
			$this->_db->setQuery($sql_txt);
			$c_sql = $this->_db->query();
// $this->dump2screen($this->_db->getQuery());
			if (!$c_sql) {
				$this->setError(Text::_("Element save error"));
				$this->logDebug($this->_db->getQuery());
				$this->logError($this->_db->getLastError());
				return false;
			}
			if (!$new_psid) {
				if ($autoinc) $new_psid=$this->_db->insertid();
				else {
					$this->setError(Text::_("Element insert id error"));
					$this->logError($this->_db->getLastError());
					return false;
				}
			}
			if($new_psid && !$psid) $this->updateElementGuidById($new_psid, $guid, $meta_index);
			if ($meta->multy_field==$meta->keystring) { // links update
				if ($meta->parent_table && $meta->linktable && $meta->parent_code) {
					if(isset($data["links"])) $links=$data["links"]; else $links=array();
					$sql_links="";
					if($links !== false){
						if (count($links)) {
							foreach($links as $lnk){
								$sql_links.="INSERT INTO #__".$meta->linktable."(".$meta->keystring.", parent_id) VALUES ('".$new_psid."', '".$lnk."') ON DUPLICATE KEY UPDATE parent_id='".$lnk."'".$this->_db->getDelimiter()." \n";
							}
							$in_str=implode("','", $links);
							$sql_links.="DELETE FROM  #__".$meta->linktable." WHERE ".$meta->keystring."='".$new_psid."' AND parent_id NOT IN('".$in_str."')".$this->_db->getDelimiter()." \n";
						} else {
							$sql_links.="DELETE FROM  #__".$meta->linktable." WHERE ".$meta->keystring."='".$new_psid."'".$this->_db->getDelimiter()." \n";
						}
					}
					$this->_db->setQuery($sql_links);
					$lsql = $this->_db->query_batch(true,true);
					$this->logDebug($this->_db->getQuery());
					if (!$lsql){
						$this->setError(Text::_("Element links save error"));
						$this->logDebug($this->_db->getQuery());
						$this->logError($this->_db->getLastError());
						return false;
					}
				}
			}
			if($sql_ad) {
				if($sql_ad_del) $sql_ad = $sql_ad_del.$sql_ad;
				$sql_ad=str_replace("###keystring###", $new_psid, $sql_ad);
				$this->_db->setQuery($sql_ad);
				$c_sql = $this->_db->query_batch(true,true);
				if (!$c_sql){
					$this->setError(Text::_("Element fields save error"));
					$this->logDebug($this->_db->getQuery());
					$this->logError($this->_db->getLastError());
					return false;
				}
			}
		} else {
			$this->setError(Text::_("Save failed")."^ ".$meta_index);
		}
		return $new_psid;
	}
	private function parseAddress($addr_arr){
		$address=Address::getTmpl();
		if(siteConfig::$useTextAddress){
			foreach($address as $a_k=>$a_val){
				if(isset($addr_arr[$a_k])) $address[$a_k]=$addr_arr[$a_k];
			}
		} else {
			// @TODO parse address as selector
			if(isset($addr_arr["fullinfo"])) $address["fullinfo"]=$addr_arr["fullinfo"];
		}
 // $this->dump2screen($addr_arr);
 // $this->dump2screen($address);
		return $address;
	}
	private function updateAlias($psid, $meta_index, $name){
		$meta = $this->getMeta($meta_index);
		if (!$meta->alias_field) {
			$this->setError(Text::_("Error updating alias")." (x01) ".$meta_index.":".$psid);
			return false;
		}
		$alias=mb_substr(Translit::_($name), 0, 255);
		if ($alias==Module::getInstance()->getName()) $alias=mb_substr($psid."-".Translit::_($name), 0, 255);
		$sql="SELECT COUNT(*) FROM #__".$meta->tablename." WHERE ".$meta->alias_field."='".$alias."' AND ".$meta->keystring."<>".$psid;
		$this->_db->setQuery($sql);
		if ($this->_db->loadResult()>0) {
			$alias=mb_substr($psid."-".$alias, 0, 255);
		}
		$sql="UPDATE #__".$meta->tablename." SET ".$meta->alias_field."='".$alias."' WHERE ".$meta->keystring."=".$psid;
		$this->_db->setQuery($sql);
		if(!$this->_db->query()){
			$this->setError(Text::_("Error updating alias")." (x02) ".$meta_index.":".$psid);
			$this->logDebug($this->_db->getQuery());
			$this->logError($this->_db->getLastError());
			return false;
		}
		return true;
	}
	private function fieldValueIsUnique($meta_index, $psid, $field, $value){
		$meta = $this->getMeta($meta_index);
		$sql = "SELECT COUNT(*) FROM `#__".$meta->tablename."` WHERE `".$field."`='".$value."'";
		if($psid) $sql.= " AND `".$meta->keystring."`<>".$psid;
		$this->_db->setQuery($sql);
//$this->dump2screen($this->_db->getQuery());
		if ($this->_db->loadResult()>0) {
			$this->logDebug("Field ".$field." not unique: ".$value." [".$meta_index."]");
			return false;
		}
		return true;
	}
	// Get from main fields
	private function getOldFilesFromMeta($psid, $meta_index){
		$result=array(); $element=false;
		if(!$psid || !$meta_index) return $result;
		$meta=$this->getMeta($meta_index);
		if($meta){
			$file_fields_arr=array();
			foreach ($meta->field as $ind=>$fld){
				if (($meta->input_type[$ind]=="image") ||($meta->input_type[$ind]=="file")) {
					$file_fields_arr[]=$fld;
				}
			}
			if(count($file_fields_arr)){
				$sql = "SELECT `".implode("`, `", $file_fields_arr)."`";
				$sql.= " FROM `#__".$meta->tablename."` WHERE `".$meta->keystring."`='".$psid."'";
				$sql.= " LIMIT 1";
				$this->_db->setQuery($sql);
				$this->logDebug($this->_db->getQuery());
				$this->_db->loadObject($element);
				if($element){
					foreach($file_fields_arr as $fld){
						$result[$fld]["old_path"]=$this->getFilePathFromMeta($fld, $element->{$fld}, $meta_index, true);
					}
				}
			}
		}
		return $result;
	}
	private function getFilePathFromMeta($fieldname, $field_val, $meta_index, $check_exists=false){
		$result="";
		if(!$fieldname || !$field_val || !$meta_index) return $result;
		$meta=$this->getMeta($meta_index);
		if($meta){
			$metavars=$this->getMetaVars($meta_index);
			$ind=$this->getMetaFieldIndex($fieldname, $meta_index);
			if($ind){
				$result=BARMAZ_UF_PATH.$metavars["module"].DS.str_replace("/", DS, $meta->upload_path[$ind]).DS.Files::getAppendix($field_val).DS.$field_val;
				if($check_exists && !is_file($result)) $result=false;
			}
		}
		return $result;
	}
	// For external calls
	public function transformTreeToLineArray($source_arr, &$dest_arr=array()){
		foreach($source_arr as $key=>$var){
			if(isset($var["guid"]) && $var["guid"] && isset($var["psid"]) && $var["psid"]){
				$dest_arr[$var["guid"]] = $var["psid"];
				if(isset($var["children"]) && count($var["children"])) $this->transformTreeToLineArray($var["children"], $dest_arr);
			}
		}
		return $dest_arr;
	}
	/***********************************************************************************/
	public function disableAllGoods(){
		$meta_index="goods";
		$meta = $this->getMeta($meta_index);
		if (!$meta->enabled) {
			$this->setError(Text::_("Error updating enable status")." (x01) ".$meta_index);
			return false;
		}
		$sql="UPDATE #__".$meta->tablename." SET ".$meta->enabled."='0'".$this->_db->getDelimiter()." \n";
		$this->_db->setQuery($sql);
		if(!$this->_db->query()){
			$this->setError(Text::_("Error updating enable status")." (x02) ".$meta_index);
			$this->logDebug($this->_db->getQuery());
			$this->logError($this->_db->getLastError());
			return false;
		}
		return true;
	}
	private function updateSKU($psid){
		$sku_prefix = Module::getInstance()->getParam("automatic_sku_prefix");
		$new_sku = $sku_prefix.$psid;
		$sql="SELECT COUNT(*) FROM #__goods WHERE g_sku='".$new_sku."' AND g_id<>'".$psid."'".$this->_db->getDelimiter()." \n";
		$this->_db->setQuery($sql);
		if ($this->_db->loadResult()>0){
			$new_sku=$sku_prefix.$psid."-".md5($psid);
		}
		$sql="UPDATE #__goods SET g_sku='".$new_sku."' WHERE g_id='".$psid."'".$this->_db->getDelimiter()." \n";
		$this->_db->setQuery($sql);
		return $this->_db->query();
	}
	// For external calls
	public function getGoodsFields($fields_list=array(), $fieldname="", $fieldvalue=""){
		return $this->getElementFieldsByFieldAndParent($fields_list, $fieldname, $fieldvalue, false, false, "goods");
	}
	public function addGoods(&$data=array(), $create_new=true, $update_found=true, $enable=true, $restore_deleted=true){
		if(!isset($data["guid"]) || !$data["guid"]) return 0;
		/*** defenitions start ***/
		$meta_index="goods"; $guid=$data["guid"]; $fld_2="g_sku"; $fld_2_val=$data["sku"];
		/*** defenitions stop ***/
		$psid = $this->tryToGetElement($guid, $fld_2, $fld_2_val, $meta_index, $enable, $restore_deleted);
		
		if($data["sku"] && !$this->fieldValueIsUnique($meta_index, $psid, "g_sku", $data["sku"])) {
			if($this->getParam("1c_goods_duplicate_sku")==1){
				$messages[]=Text::_("Repeated SKU").": ".$data["sku"];
				$this->logError("Repeated SKU: ".$data["sku"]." for goods: ".$data["guid"]." (".__CLASS__."->".__FUNCTION__.")");
				return false;
			} else {
				$this->logDebug("Repeated SKU: ".$data["sku"]." for goods: ".$data["guid"]." (".__CLASS__."->".__FUNCTION__.")");
				$data["sku"]="";
			}
		}
		
		$old_files=array(); $new_files=array();
		$el=array();
		if(!$psid && $create_new){
			// Let's create element! Why not ?
			$el=$this->getElementTemplate($meta_index, $enable);
			// Let's set known values
			$el["g_id"]=0;
			$el["g_sku"]=$data["sku"];
			$el["g_name"]=$data["title"];
			$el["g_title"]=$data["full_title"] ? $data["full_title"] : $data["title"];
			$el["g_comments"]=$data["description"];
			$el["g_alias"]=""; // Later
			if(isset($data["type"]) && $data["type"]) $el["g_type"]=$data["type"];
			if(isset($data["measure"]) && $data["measure"]) $el["g_measure"]=$data["measure"];
			$el["g_pack_measure"] = $el["g_measure"];
			$el["g_pack_koeff"] = 1;
			if(isset($data["weight"]) && $data["weight"]) $el["g_weight"]=$data["weight"];
			if(isset($data["width"]) && $data["width"]) $el["g_width"]=$data["width"];
			if(isset($data["length"]) && $data["length"]) $el["g_length"]=$data["length"];
			if(isset($data["height"]) && $data["height"]) $el["g_height"]=$data["height"];
			if(isset($data["manufacturer"]) && $data["manufacturer"]) $el["g_manufacturer"]=$data["manufacturer"];
			if(isset($data["vendor"]) && $data["vendor"]) $el["g_vendor"]=$data["vendor"];
			if(count($data["links"])) {
				$el["links"]=$data["links"];
				$el["g_main_grp"]=$data["links"][0];
			}
			if(isset($data["tax"]) && $data["tax"]) $el["g_tax"]=$data["tax"];
			if(count($data["df_fields"])){
				foreach($data["df_fields"] as $dfkey=>$dfval){
					if(array_key_exists($dfkey, $el)) {
						$el[$dfkey]=$dfval;
					}
				}
			}
			$el["g_ordering"]=0;
			$el["g_enabled"]=1;
			$el["g_deleted"]=0;
			if($data["image_path"] && is_file($data["image_path"])){
				$filename=md5(time().User::getInstance()->getID()."catalog".basename($data["image_path"])).time();
				$rash=strtolower(strrchr(strval(basename($data["image_path"])), "."));
				$el["g_thumb"]=$filename.$rash;
				$el["g_title_thm"] = $data["image_title"];
				$el["g_alt_thm"] = $data["image_title"];
				$new_files["g_thumb"]["source"]=$data["image_path"];
				$new_files["g_thumb"]["dest"]=$this->getFilePathFromMeta("g_thumb", $el["g_thumb"], $meta_index);
				$el["g_medium_image"]=$filename.$rash;
				$el["g_title_med"] = $data["image_title"];
				$el["g_alt_med"] = $data["image_title"];
				$new_files["g_medium_image"]["source"]=$data["image_path"];
				$new_files["g_medium_image"]["dest"]=$this->getFilePathFromMeta("g_medium_image", $el["g_medium_image"], $meta_index);
				$el["g_image"]=$filename.$rash;
				$el["g_title_img"] = $data["image_title"];
				$el["g_alt_img"] = $data["image_title"];
				$new_files["g_image"]["source"]=$data["image_path"];
				$new_files["g_image"]["dest"]=$this->getFilePathFromMeta("g_image", $el["g_image"], $meta_index);
			}
		} elseif($psid && $update_found){
			// Let's update element! Why not ?
			$el["g_id"]=$psid;
			$el["g_sku"]=$data["sku"];
			$el["g_name"]=$data["title"];
			$el["g_title"]=$data["full_title"] ? $data["full_title"] : $data["title"];
			$el["g_comments"]=$data["description"];
			$el["g_alias"]=""; // Later
			if(isset($data["type"]) && $data["type"]) $el["g_type"]=$data["type"];
			if(isset($data["measure"]) && $data["measure"]) $el["g_measure"]=$data["measure"];
			$el["g_pack_measure"] = $el["g_measure"];
			$el["g_pack_koeff"] = 1;
			if(isset($data["weight"]) && $data["weight"]) $el["g_weight"]=$data["weight"];
			if(isset($data["width"]) && $data["width"]) $el["g_width"]=$data["width"];
			if(isset($data["length"]) && $data["length"]) $el["g_length"]=$data["length"];
			if(isset($data["height"]) && $data["height"]) $el["g_height"]=$data["height"];
			if(isset($data["manufacturer"]) && $data["manufacturer"]) $el["g_manufacturer"]=$data["manufacturer"];
			if(isset($data["vendor"]) && $data["vendor"]) $el["g_vendor"]=$data["vendor"];
			if($this->getParam("1c_goods_update_groups")){
				if(count($data["links"])) {
					$el["links"]=$data["links"];
					$el["g_main_grp"]=$data["links"][0];
				}
			} else {
				$el["links"]=false;
			}
			if(isset($data["tax"]) && $data["tax"]) $el["g_tax"]=$data["tax"];
			if(count($data["df_fields"])){
				foreach($data["df_fields"] as $dfkey=>$dfval){
					if(array_key_exists($dfkey, $el)) {
						$el[$dfkey]=$dfval;
					}
				}
			}
			$el["g_ordering"]=0;
			$el["g_enabled"]=1;
			$el["g_deleted"]=0;
			$el["g_change_date"]=$this->getMeta($meta_index)->getDefaultConstant("NOW");
			$el["g_change_uid"]=$this->getMeta($meta_index)->getDefaultConstant("AUTHOR");
			if($data["image_path"] && is_file($data["image_path"]) && $this->getParam("1c_goods_update_images")){
				$filename=md5(time().User::getInstance()->getID()."catalog".basename($data["image_path"])).time();
				$rash=strtolower(strrchr(strval(basename($data["image_path"])), "."));
				$el["g_thumb"]=$filename.$rash;
				$el["g_title_thm"] = $data["image_title"];
				$el["g_alt_thm"] = $data["image_title"];
				$new_files["g_thumb"]["source"]=$data["image_path"];
				$new_files["g_thumb"]["dest"]=$this->getFilePathFromMeta("g_thumb", $el["g_thumb"], $meta_index);
				$el["g_medium_image"]=$filename.$rash;
				$el["g_title_med"] = $data["image_title"];
				$el["g_alt_med"] = $data["image_title"];
				$new_files["g_medium_image"]["source"]=$data["image_path"];
				$new_files["g_medium_image"]["dest"]=$this->getFilePathFromMeta("g_medium_image", $el["g_medium_image"], $meta_index);
				$el["g_image"]=$filename.$rash;
				$el["g_title_img"] = $data["image_title"];
				$el["g_alt_img"] = $data["image_title"];
				$new_files["g_image"]["source"]=$data["image_path"];
				$new_files["g_image"]["dest"]=$this->getFilePathFromMeta("g_image", $el["g_image"], $meta_index);
				/********************************************************/
				$old_files=$this->getOldFilesFromMeta($psid, $meta_index);
			}
		}
// $this->dump2screen($el);
// $this->dump2screen($data, true);
		if(is_array($el) && count($el)){
			$psid = $this->save($el, $guid, $meta_index);
		}
		if($psid && ($create_new || $update_found)){
			if(!$this->updateAlias($psid, $meta_index, $el["g_name"])) {
				return false;
			}
			if(!$el["g_sku"] && $this->getParam("generate_sku_automaticly")){
				if(!$this->updateSKU($psid)) {
					return false;
				}
			}
		}
		if($psid && ($create_new || $update_found)){
			if(count($this->getElementFilesTemplate($meta_index))){
				foreach($this->getElementFilesTemplate($meta_index) as $file_ind=>$file_val){
					if(isset($el[$file_ind])){
						// Let's delete old
						if(isset($old_files[$file_ind]["old_path"]) && $old_files[$file_ind]["old_path"] && is_file($old_files[$file_ind]["old_path"])){
							Files::delete($old_files[$file_ind]["old_path"], true);
						}
						// Let's copy files
						if(isset($new_files[$file_ind]) && $new_files[$file_ind]["source"] && $new_files[$file_ind]["dest"]){
							if(!Files::checkFolder(pathinfo($new_files[$file_ind]["dest"], PATHINFO_DIRNAME), true) || !copy($new_files[$file_ind]["source"], $new_files[$file_ind]["dest"])) {
								$this->logError(Text::_("File copy failed").": ".$new_files[$file_ind]["source"]." => ".$new_files[$file_ind]["dest"]);
							}
							// Let's resize
							if ($file_ind=="g_thumb" && catalogConfig::$thumbAutoResize && catalogConfig::$thumb_width && catalogConfig::$thumb_height && $new_files[$file_ind]["dest"]) {
								if(!Files::resizeImage($new_files[$file_ind]["source"], $new_files[$file_ind]["dest"], catalogConfig::$thumb_width, catalogConfig::$thumb_height)){
									$this->logError(Text::_("Thumb resize failed").": ".$new_files[$file_ind]["dest"]);
								}
							} elseif ($file_ind=="g_medium_image" && catalogConfig::$mediumImgAutoResize && catalogConfig::$mediumImgWidth && catalogConfig::$mediumImgHeight && $new_files[$file_ind]["dest"]) {
								if(!Files::resizeImage($new_files[$file_ind]["source"], $new_files[$file_ind]["dest"], catalogConfig::$mediumImgWidth, catalogConfig::$mediumImgHeight)){
									$this->logError(Text::_("Medium image resize failed").": ".$new_files[$file_ind]["dest"]);
								}
							}
						}
					}
				}
			}
		}
		if(!$psid) $this->setError(Text::_("Element absent").": ".__CLASS__."->".__FUNCTION__."(".$guid.")");
		return $psid;
	}
	public function addGoodsArr(&$data=array(), $create_new=true, $update_found=true, $enable=true, $restore_deleted=true){
		foreach ($data as $_key=>&$_data){
			$_data["psid"]=$this->addGoods($_data, $create_new, $update_found, $enable, $restore_deleted);
			if(!$_data["psid"]) return false;
		}
// $this->dump2screen($data, true);
		return true;
	}
	public function addGoodsGroup(&$data=array(), $parent_field_val=0, $create_new=true, $update_found=true, $enable=true, $restore_deleted=true){
		if(!isset($data["guid"]) || !$data["guid"]) return 0;
		/*** defenitions start ***/
		$meta_index="goodsgroup"; $guid=$data["guid"]; $fld_2="ggr_name"; $fld_2_val=$data["title"]; $parent_field="ggr_id_parent";
		/*** defenitions stop ***/
		$psid = $this->tryToGetElementWithParent($guid, $fld_2, $fld_2_val, $parent_field, $parent_field_val, $meta_index, $enable, $restore_deleted);
		
		$old_files=array(); $new_files=array();
		$el=array();
		if(!$psid && $create_new){
			// Let's create element! Why not ?
			$el=$this->getElementTemplate($meta_index, $enable);
			// Let's set known values
			$el["ggr_id"]=0;
			$el["ggr_id_parent"]=$parent_field_val;
			$el["ggr_name"]=$data["title"];
			$el["ggr_alias"]=""; // Later
			$el["ggr_ordering"]=0;
			$el["ggr_comment"]=$data["description"];
			$el["ggr_enabled"]=1;
			$el["ggr_deleted"]=0;
			if($data["image_path"] && is_file($data["image_path"])){
				$filename=md5(time().User::getInstance()->getID()."catalog".basename($data["image_path"])).time();
				$rash=strtolower(strrchr(strval(basename($data["image_path"])), "."));
				$el["ggr_thumb"]=$filename.$rash;
				$new_files["ggr_thumb"]["source"]=$data["image_path"];
				$new_files["ggr_title_thm"] = $data["image_title"];
				$new_files["ggr_alt_thm"] = $data["image_title"];
				$new_files["ggr_thumb"]["dest"]=$this->getFilePathFromMeta("ggr_thumb", $el["ggr_thumb"], $meta_index);
				$el["ggr_image"]=$filename.$rash;
				$new_files["ggr_image"]["source"]=$data["image_path"];
				$new_files["ggr_title_img"] = $data["image_title"];
				$new_files["ggr_alt_img"] = $data["image_title"];
				$new_files["ggr_image"]["dest"]=$this->getFilePathFromMeta("ggr_image", $el["ggr_image"], $meta_index);
			}
		} elseif($psid && $update_found){
			// Let's update element! Why not ?
			$el["ggr_id"]=$psid;
			$el["ggr_id_parent"]=$parent_field_val;
			$el["ggr_name"]=$data["title"];
			$el["ggr_alias"]=""; // Later
			$el["ggr_ordering"]=0;
			$el["ggr_comment"]=$data["description"];
			$el["ggr_enabled"]=1;
			$el["ggr_deleted"]=0;
			$el["ggr_change_date"]=$this->getMeta($meta_index)->getDefaultConstant("NOW");
			$el["ggr_change_uid"]=$this->getMeta($meta_index)->getDefaultConstant("AUTHOR");
			if($data["image_path"] && is_file($data["image_path"]) && $this->getParam("1c_groups_update_images")){
				$filename=md5(time().User::getInstance()->getID()."catalog".basename($data["image_path"])).time();
				$rash=strtolower(strrchr(strval(basename($data["image_path"])), "."));
				$el["ggr_thumb"]=$filename.$rash;
				$new_files["ggr_thumb"]["source"]=$data["image_path"];
				$new_files["ggr_title_thm"] = $data["image_title"];
				$new_files["ggr_alt_thm"] = $data["image_title"];
				$new_files["ggr_thumb"]["dest"]=$this->getFilePathFromMeta("ggr_thumb", $el["ggr_thumb"], $meta_index);
				$el["ggr_image"]=$filename.$rash;
				$new_files["ggr_image"]["source"]=$data["image_path"];
				$new_files["ggr_title_img"] = $data["image_title"];
				$new_files["ggr_alt_img"] = $data["image_title"];
				$new_files["ggr_image"]["dest"]=$this->getFilePathFromMeta("ggr_image", $el["ggr_image"], $meta_index);
				/********************************************************/
				$old_files=$this->getOldFilesFromMeta($psid, $meta_index);
			}
		}
		if(is_array($el) && count($el)){
			$psid = $this->save($el, $guid, $meta_index);
		}
		if($psid && ($create_new || $update_found)){
			if(!$this->updateAlias($psid, $meta_index, $el["ggr_name"])) {
				return false;
			}
		}
		if($psid && ($create_new || $update_found)){
			if(count($this->getElementFilesTemplate($meta_index))){
				foreach($this->getElementFilesTemplate($meta_index) as $file_ind=>$file_val){
					if(isset($el[$file_ind])){
						// Let's delete old
						if(isset($old_files[$file_ind]["old_path"]) && $old_files[$file_ind]["old_path"] && is_file($old_files[$file_ind]["old_path"])){
							Files::delete($old_files[$file_ind]["old_path"], true);
						}
						// Let's copy files
						if(isset($new_files[$file_ind]) && $new_files[$file_ind]["source"] && $new_files[$file_ind]["dest"]){
							if(!Files::checkFolder(pathinfo($new_files[$file_ind]["dest"], PATHINFO_DIRNAME), true) || !copy($new_files[$file_ind]["source"], $new_files[$file_ind]["dest"])) {
								$this->logError(Text::_("File copy failed").": ".$new_files[$file_ind]["source"]." => ".$new_files[$file_ind]["dest"]);
							}
							// Let's resize
							if ($file_ind=="ggr_thumb" && catalogConfig::$ggr_thumb_AutoResize && catalogConfig::$ggr_thumb_width && catalogConfig::$ggr_thumb_height && $new_files[$file_ind]["dest"]) {
								if(!Files::resizeImage($new_files[$file_ind]["source"], $new_files[$file_ind]["dest"], catalogConfig::$ggr_thumb_width, catalogConfig::$ggr_thumb_height)){
									$this->logError(Text::_("Thumb resize failed").": ".$new_files[$file_ind]["dest"]);
								}
							}
						}
					}
				}
			}
		}
		if($psid && count($data["children"])) {
			foreach($data["children"] as $ggr_child_key=>&$ggr_child_val){
				$ggr_child_val["psid"]=$this->addGoodsGroup($ggr_child_val, $psid, $create_new, $update_found, $enable, $restore_deleted);
				if(!$ggr_child_val["psid"]) return false;
			}
		}
		if(!$psid) $this->setError(Text::_("Element absent").": ".__CLASS__."->".__FUNCTION__."(".$guid.")");
		return $psid;
	}
	public function addGoodsGroups(&$data=array(), $create_new=true, $update_found=true, $enable=true, $restore_deleted=true){
		foreach ($data as $_key=>&$_data){
			$_data["psid"]=$this->addGoodsGroup($_data, 0, $create_new, $update_found, $enable, $restore_deleted);
			if(!$_data["psid"]) return false;
		}
// $this->dump2screen($data, true);
		return true;
	}
	public function addPropertyChoice(&$data=array(), $parent_field_val=0, $create_new=true, $update_found=true, $enable=true, $restore_deleted=true){
		if(!$parent_field_val) return 0;
		if(!isset($data["guid"]) || !$data["guid"]) return 0;
		/*** defenitions start ***/
		$meta_index="fields_choices"; $guid=$data["guid"]; $fld_2="fc_value"; $fld_2_val=strtolower($data["title"]); $parent_field="fc_field_id";
		/*** defenitions stop ***/
		$psid = $this->tryToGetElementWithParent($guid, $fld_2, $fld_2_val, $parent_field, $parent_field_val, $meta_index, $enable, $restore_deleted);
		
		$el=array();
		if(!$psid && $create_new){
			// Let's create element! Why not ?
			$el=$this->getElementTemplate($meta_index, $enable);
			// Let's set known values
			$el["fc_id"]=0;
			$el["fc_field_id"]=$parent_field_val;
			$el["fc_value"]=$data["title"];
			$el["fc_ordering"]=0;
			$el["fc_enabled"]=1;
			$el["fc_deleted"]=0;
		} elseif($psid && $update_found){
			// Let's update element! Why not ?
			// Nothing for update
		}
		if(is_array($el) && count($el)){
			$psid = $this->save($el, $guid, $meta_index);
		}
		if(!$psid) $this->setError(Text::_("Element absent").": ".__CLASS__."->".__FUNCTION__."(".$guid.")");
		return $psid;
	}
	public function addTaxWoGUID(&$data=array(), $create_new=true, $update_found=true, $enable=true, $restore_deleted=true){
		// if(!isset($data["guid"]) || !$data["guid"]) return 0; // CHECK WITHOUT CODE
		/*** defenitions start ***/
		$meta_index="taxes"; $guid=false; $fld_2="t_value"; $fld_2_val=$data["value"];
		/*** defenitions stop ***/
		$psid = $this->tryToGetElement($guid, $fld_2, $fld_2_val, $meta_index, $enable, $restore_deleted);
		
		$el=array();
		if(!$psid && $create_new){
			// Let's create element! Why not ?
			$el=$this->getElementTemplate($meta_index, $enable);
			// Let's set known values
			$el["t_id"]=0;
			$el["t_name"]=$data["title"];
			$el["t_value"]=$data["value"];
		} elseif($psid && $update_found){
			// Let's update element! Why not ?
			// Nothing for update
		}
		if(is_array($el) && count($el)){
			$psid = $this->save($el, $guid, $meta_index);
		}
		if(!$psid) $this->setError(Text::_("Element absent").": ".__CLASS__."->".__FUNCTION__."(".$fld_2.":".$fld_2_val.")");
		return $psid;
	}
	public function addManufacturerWoGUID(&$data=array(), $create_new=true, $update_found=true, $enable=true, $restore_deleted=true){
		// if(!isset($data["guid"]) || !$data["guid"]) return 0; // CHECK WITHOUT CODE
		/*** defenitions start ***/
		$meta_index="manufacturers"; $guid=false; $fld_2="mf_name"; $fld_2_val=$data["title"];
		/*** defenitions stop ***/
		$psid = $this->tryToGetElement($guid, $fld_2, $fld_2_val, $meta_index, $enable, $restore_deleted);
		
		$el=array();
		if(!$psid && $create_new){
			// Let's create element! Why not ?
			$el=$this->getElementTemplate($meta_index, $enable);
			// Let's set known values
			$el["mf_id"]=0;
			$el["mf_cat_id"]=$this->getFirstPossibleParentId($meta_index);
			$el["mf_name"]=$data["title"];
		} elseif($psid && $update_found){
			// Let's update element! Why not ?
			// Nothing for update
		}
		if(is_array($el) && count($el)){
			$psid = $this->save($el, $guid, $meta_index);
		}
		if(!$psid) $this->setError(Text::_("Element absent").": ".__CLASS__."->".__FUNCTION__."(".$fld_2.":".$fld_2_val.")");
		return $psid;
	}
	public function addManufacturer(&$data=array(), $create_new=true, $update_found=true, $enable=true, $restore_deleted=true){
		if(!isset($data["guid"]) || !$data["guid"]) return 0;
		/*** defenitions start ***/
		$meta_index="manufacturers"; $guid=$data["guid"]; $fld_2="mf_name"; $fld_2_val=$data["title"];
		/*** defenitions stop ***/
		$psid = $this->tryToGetElement($guid, $fld_2, $fld_2_val, $meta_index, $enable, $restore_deleted);
		
		$el=array();
		if(!$psid && $create_new){
			// Let's create element! Why not ?
			$el=$this->getElementTemplate($meta_index, $enable);
			// Let's set known values
			$el["mf_id"]=0;
			$el["mf_cat_id"]=$this->getFirstPossibleParentId($meta_index);
			$el["mf_name"]=$data["title"];
		} elseif($psid && $update_found){
			// Let's update element! Why not ?
			$el["mf_id"]=$psid;
			$el["mf_name"]=$data["title"];
			// Nothing for update
		}
		if(is_array($el) && count($el)){
			$psid = $this->save($el, $guid, $meta_index);
		}
		if(!$psid) $this->setError(Text::_("Element absent").": ".__CLASS__."->".__FUNCTION__."(".$guid.")");
		return $psid;
	}
	public function addManufacturers(&$data=array(), $create_new=true, $update_found=true, $enable=true, $restore_deleted=true){
		foreach ($data as $_key=>&$_data){
			$_data["psid"]=$this->addManufacturer($_data, $create_new, $update_found, $enable, $restore_deleted);
			if(!$_data["psid"]) return false;
		}
		return true;
	}
	public function addProperty(&$data=array(), $create_new=true, $update_found=true, $enable=true, $restore_deleted=true){
		if(!isset($data["guid"]) || !$data["guid"]) return 0;
		/*** defenitions start ***/
		$meta_index="fields_list"; $guid=$data["guid"]; $fld_2="f_name"; $fld_2_val="df_".DBUtil::cleanNameForDB($data["guid"]); // $fld_2_val="df_".strtolower(Translit::_($data["title"]));  // Not working, because of duplicates in 1C !!!
		/*** defenitions stop ***/
		$psid = $this->tryToGetElement($guid, $fld_2, $fld_2_val, $meta_index, $enable, $restore_deleted);

		$el=array();
		if(!$psid && $create_new){
			// Let's create element! Why not ?
			$el=$this->getElementTemplate($meta_index, $enable);
			// Let's set known values
			$el["f_id"]=0;
			$el["f_name"]="df_".DBUtil::cleanNameForDB($data["guid"]); // "df_".strtolower(Translit::_($data["title"])); // Not working, because of duplicates in 1C !!!
			$el["f_descr"]=$data["title"];
			$el["f_default"]="";
			$el["f_type"]=$data["type"];
			$el["f_writeable"]=1;
			$el["f_required"]=intval($data["required"]);
			$el["f_deleted"]=0;
			$el["f_table"]=($data["table"] ? $data["table"] : "goods");
			$el["f_custom"]=0;
		} elseif($psid && $update_found){
			// Let's update element! Why not ?
			$el["f_id"]=$psid;
			$el["f_descr"]=$data["title"];
			$el["f_required"]=intval($data["required"]);
			// Nothing for update
		}
		if(is_array($el) && count($el)){
			$psid = $this->save($el, $guid, $meta_index);
		}
		// Let's add children if need
		if($psid && $data["type"]==5){
			foreach($data["choices"] as $fc_key=>&$fc_val){
				$fc_val["psid"]=$this->addPropertyChoice($fc_val, $psid, $create_new, $update_found, $enable, $restore_deleted);
				if(!$fc_val["psid"]) return false;
			}
		}
		if(!$psid) $this->setError(Text::_("Element absent").": ".__CLASS__."->".__FUNCTION__."(".$guid.")");
		return $psid;
	}
	public function addProperties(&$data=array(), $create_new=true, $update_found=true, $enable=true, $restore_deleted=true){
		foreach ($data as $_key=>&$_data){
			if($_data["skip_on_add"]) continue;
			$_data["psid"]=$this->addProperty($_data, $create_new, $update_found, $enable, $restore_deleted);
			if(!$_data["psid"]) return false;
		}
		return true;
	}
	public function addMeasureWoGUID($data=array(), $create_new=true, $update_found=true, $enable=true, $restore_deleted=true){
		// if(!isset($data["guid"]) || !$data["guid"]) return 0; // CHECK WITHOUT GUID 
		/*** defenitions start ***/
		$meta_index="measures"; $guid=false; $fld_2="meas_code"; $fld_2_val=$data["code"];
		if(!$fld_2_val){ // Only if no meas_code specified 
			if($this->getParam("1c_measures_search")==2) { $fld_2="meas_short_name"; $fld_2_val=$data["short_name"]; }
			elseif($this->getParam("1c_measures_search")==3) { $fld_2="meas_full_name"; $fld_2_val=$data["full_name"]; }
		}
		/*** defenitions stop ***/
		$psid = $this->tryToGetElement($guid, $fld_2, $fld_2_val, $meta_index, $enable, $restore_deleted);

		$el=array();
		if(!$psid && $create_new){
			// Let's create element! Why not ?
			$el=$this->getElementTemplate($meta_index, $enable);
			// Let's set known values
			/*
			["base_coeff"]=>string(1) "1"
			*/
			// @TODO meas_kf always 1, ["base_coeff"] not used, change to real
			$el["meas_id"]=0;
			$el["meas_code"]=$data["code"];
			$el["meas_short_name"]=isset($data["short_name"]) ? $data["short_name"] : $data["full_name"];
			$el["meas_full_name"]=$data["full_name"];
			if(isset($data["base_code"])){
				$base_type = Measure::getInstance()->getType($data["base_code"]);
				if($base_type){
					$el["meas_type"]=$base_type;
					$el["meas_kf"]=1;
				} else {
					$el["meas_type"]=$this->getParam("1c_measures_default_type");
					$el["meas_kf"]=1;
				}
			} else {
				$el["meas_type"]=$this->getParam("1c_measures_default_type");
				$el["meas_kf"]=1;
			}
			$el["meas_comment"]="";
			$el["meas_enabled"]=1;
			$el["meas_deleted"]=0;
		} elseif($psid && $update_found){
			// Let's update element! Why not ?
			// Nothing for update
		}
		if(is_array($el) && count($el)){
			$psid = $this->save($el, $guid, $meta_index);
		}
		if(!$psid) $this->setError(Text::_("Element absent").": ".__CLASS__."->".__FUNCTION__."(".$fld_2.":".$fld_2_val.")");
		return $psid;
	}
	private function updateGoodsPricesAndQuantity($data){
		if(!isset($data["quantity"])) {
			$this->setError(Text::_("Goods quantity absent").": ".__CLASS__."->".__FUNCTION__);
			return false;
		} else {
			$quantity=$data["quantity"];
		}
		if(!isset($data["prices"]) || !count($data["prices"])) {
			$this->setError(Text::_("Goods prices absent").": ".__CLASS__."->".__FUNCTION__);
			return false;
		} else {
			$prices=$data["prices"];
			if(!isset($prices["price_1"]) || !isset($prices["price_2"]) || !isset($prices["price_3"]) || !isset($prices["price_4"]) || !isset($prices["price_5"])){
				$this->setError(Text::_("One of goods prices absent").": ".__CLASS__."->".__FUNCTION__);
				return false;
			}
		}
		if(!isset($data["psid"])) {
			$this->setError(Text::_("Goods psid absent").": ".__CLASS__."->".__FUNCTION__);
			return false;
		} else {
			$psid=$data["psid"];
		}
		
		$sql="UPDATE #__goods SET g_stock='".$quantity."', g_price_1='".$prices["price_1"]."', g_price_2='".$prices["price_2"]."', g_price_3='".$prices["price_3"]."', g_price_4='".$prices["price_4"]."', g_price_5='".$prices["price_5"]."' WHERE g_id='".$psid."'".$this->_db->getDelimiter()." \n";;
		$this->_db->setQuery($sql);
		$this->logDebug($this->_db->getQuery());
		return $this->_db->query();
	}
	private function addCharacteristicsVals(&$data){
		$znak="+";
		$check_stock=0;
		$sql="";
// $this->dump2screen($data);
		if(isset($data["options"]) && count($data["options"])){
			if(!isset($data["base"])) return false;
			if(!isset($data["base"]["psid"]) || !$data["base"]["psid"]) return false;
			$object_id=$data["base"]["psid"];
			if(!isset($data["base"]["prices"]) || !is_array($data["base"]["prices"])) return false;
			$base_prices=$data["base"]["prices"];
			if(!isset($base_prices["price_1"]) || !isset($base_prices["price_2"]) || !isset($base_prices["price_3"]) || !isset($base_prices["price_4"]) || !isset($base_prices["price_5"])) return false;
			$base_price_1=$base_prices["price_1"]; $base_price_2=$base_prices["price_2"]; $base_price_3=$base_prices["price_3"]; $base_price_4=$base_prices["price_4"]; $base_price_5=$base_prices["price_5"];
			if(!isset($data["option_data"]) || !isset($data["option_data"]["psid"]) || !$data["option_data"]["psid"]) return false;
			$option_data_id=$data["option_data"]["psid"];
			$ordering=0;
			foreach($data["options"] as $opt_key=>$opt_val){
				$ordering = $ordering + 10;
				if(!isset($opt_val["psid"]) || !$opt_val["psid"]) return false;
				$option_val_id=$opt_val["psid"];
				if(!isset($opt_val["prices"]) || !is_array($opt_val)) return false;
				$prices=$opt_val["prices"];
				if(!isset($prices["price_1"]) || !isset($prices["price_2"]) || !isset($prices["price_3"]) || !isset($prices["price_4"]) || !isset($prices["price_5"])) return false;
				$price_1=$prices["price_1"]-$base_price_1; $price_2=$prices["price_2"]-$base_price_2; $price_3=$prices["price_3"]-$base_price_3; $price_4=$prices["price_4"]-$base_price_4; $price_5=$prices["price_5"]-$base_price_5;
				if(!isset($opt_val["quantity"])) return false;
				$quantity=$opt_val["quantity"];
				if(!isset($opt_val["offer_key"])) return false; // @TODO May be not return ??? May be just insert empty ??? 
				$offer_key = $opt_val["offer_key"];
				$sql.="INSERT INTO `#__goods_opt_vals_data` (`ovd_id`, `ovd_od_id`, `ovd_val_id`, `ovd_price_sign`, `ovd_price_1`, `ovd_price_2`, `ovd_price_3`, `ovd_price_4`, `ovd_price_5`, `ovd_check_stock`, `ovd_stock`, `ovd_thumb`, `ovd_ordering`, `ovd_enabled`, `ovd_extcode`)";
				$sql.=" VALUES (NULL, '".$option_data_id."', '".$option_val_id."', '".$znak."', '".$price_1."', '".$price_2."', '".$price_3."', '".$price_4."', '".$price_5."', '".$check_stock."', '".$quantity."', '', '".$ordering."', '1', '".$offer_key."')".$this->_db->getDelimiter();
			}
		}
		if(!$sql) return false;
		$this->_db->setQuery($sql);
		if(!$this->_db->query_batch(true,true)){
$this->dump2screen($this->_db->getQuery($sql));
			$this->logDebug($this->_db->getQuery());
			$this->logError($this->_db->getLastError());
			return false;
		}
		return true;
	}
	public function addCharacteristics(&$data){
		$result=true;
		if(count($data)){
			foreach ($data as $gk=>&$goods){
// $this->dump2screen("KEY=".$gk." (".__CLASS__."->".__FUNCTION__.")");
				if(!isset($goods["base"]) || !isset($goods["base"]["psid"]) || !$goods["base"]["psid"]){
					$this->setError(Text::_("Goods base data absent").": ".__CLASS__."->".__FUNCTION__."(".$gk.")");
					return false;
				}
				if(!$this->updateGoodsPricesAndQuantity($goods["base"])){
					$this->setError(Text::_("Failed to update goods base data").": ".__CLASS__."->".__FUNCTION__."(".$gk.")");
					return false;
				}
				if(!isset($goods["options"]) || !is_array($goods["options"]) || $goods["options"]===false){
					$this->setError(Text::_("Goods options data absent").": ".__CLASS__."->".__FUNCTION__."(".$gk.")");
					return false;
				}
// $this->dump2screen($goods["options"]);
				if(count($goods["options"]) && ($this->offers_mode==1 || $this->offers_mode==2)){
					$option=array();
					$option["title"] = Text::_("Characteristics")." [".$goods["base"]["psid"]."]";
					$option["type"] = 5;
					$option["required"] = 0;
					$option["psid"] = $this->addOptionWoGUID($option);
					if(!$option["psid"]){
						$this->setError(Text::_("Option adding failed").": ".__CLASS__."->".__FUNCTION__."(".$gk.")");
						return false;
					}
					if(!$this->cleanOptionVals($option["psid"], $goods["base"]["psid"])){
						$this->setError(Text::_("Option values cleaning failed").": ".$option["title"]." [".__CLASS__."->".__FUNCTION__."] (".$gk.")");
						return false;
					}
					$ordering=0;
					foreach($goods["options"] as $go_key=>&$go_val){
						$ordering = $ordering + 10;
						$option_vals=array();
						$option_vals["title"] = $go_val["name"];
						$option_vals["parent_id"] = $option["psid"];
						$option_vals["ordering"] = $ordering;
						$go_val["psid"] = $this->addOptionValWoGUID($option_vals);
						if(!$go_val["psid"]){
							$this->setError(Text::_("Option value adding failed").": ".$option_vals["title"]." [".__CLASS__."->".__FUNCTION__."] (".$gk.")");
							return false;
						}
					}
					$option_data=array();
					$option_data["object_id"] = $goods["base"]["psid"];
					$option_data["option_id"] = $option["psid"];
					$option_data["psid"] = $this->addOptionDataWoGUID($option_data);
					if(!$option_data["psid"]){
						$this->setError(Text::_("Option to goods adding failed").": ".$option["title"]." (".__CLASS__."->".__FUNCTION__.") (".$gk.")");
						return false;
					}
					$goods["option"] = $option;
					$goods["option_data"] = $option_data;
					if(!$this->addCharacteristicsVals($goods)){
						$this->setError(Text::_("Option values to goods adding failed").": ".$option["title"]." (".__CLASS__."->".__FUNCTION__.") (".$gk.")");
						return false;
					}
				}
// $this->dump2screen($option);
// $this->dump2screen($goods["options"], true);
// $this->dump2screen($goods, true);
			}
// $this->dump2screen($data);
		}
		return $result;
	}
	private function cleanOptionVals($psid, $obj_id){
		$sql = "DELETE FROM `#__goods_opt_vals` WHERE `ov_opt_id`='".$psid."'".$this->_db->getDelimiter();
		$sql.= "DELETE FROM `#__goods_options_data` WHERE `od_opt_id`='".$psid."'".$this->_db->getDelimiter();
		$sql.= "DELETE FROM `#__goods_options_data` WHERE `od_obj_id`='".$obj_id."'".$this->_db->getDelimiter();
		$sql.= "DELETE FROM `#__goods_options_data` WHERE `od_opt_id` NOT IN (SELECT o_id FROM `#__goods_options`)".$this->_db->getDelimiter();
		$sql.= "DELETE FROM `#__goods_opt_vals_data` WHERE `ovd_od_id` NOT IN (SELECT od_id FROM `#__goods_options_data`)".$this->_db->getDelimiter();
		$this->_db->setQuery($sql);
		if(!$this->_db->query_batch(true,true)){
$this->dump2screen($this->_db->getQuery($sql));
			$this->logDebug($this->_db->getQuery());
			$this->logError($this->_db->getLastError());
			return false;
		}
		return true;
	}
	private function addOptionWoGUID(&$data=array(), $create_new=true, $update_found=true, $enable=true, $restore_deleted=true){
		// if(!isset($data["guid"]) || !$data["guid"]) return 0; // CHECK WITHOUT CODE
		/*** defenitions start ***/
		$meta_index="options"; $guid=false; $fld_2="o_title"; $fld_2_val=$data["title"];
		/*** defenitions stop ***/
		$psid = $this->tryToGetElement($guid, $fld_2, $fld_2_val, $meta_index, $enable, $restore_deleted);
		
		$el=array();
		if(!$psid && $create_new){
			// Let's create element! Why not ?
			$el=$this->getElementTemplate($meta_index, $enable);
			// Let's set known values
			$el["o_id"]=0;
			$el["o_title"]=$data["title"];
			$el["o_type"]=$data["type"];
			$el["o_required"]=$data["required"];
		} elseif($psid && $update_found){
			// Let's update element! Why not ?
			// Nothing for update
		}
		if(is_array($el) && count($el)){
			$psid = $this->save($el, $guid, $meta_index);
		}
		if(!$psid) $this->setError(Text::_("Element absent").": ".__CLASS__."->".__FUNCTION__."(".$fld_2.":".$fld_2_val.")");
		return $psid;
	}
	private function addOptionDataWoGUID(&$data=array(), $create_new=true, $update_found=true, $enable=true, $restore_deleted=true){
		// if(!isset($data["guid"]) || !$data["guid"]) return 0; // CHECK WITHOUT CODE
		/*** defenitions start ***/
		$meta_index="options_data"; $guid=false; $fld_2=false; $fld_2_val="";
		/*** defenitions stop ***/
		$psid = $this->tryToGetElement($guid, $fld_2, $fld_2_val, $meta_index, $enable, $restore_deleted);
		
		$el=array();
		if(!$psid && $create_new){
			// Let's create element! Why not ?
			$el=$this->getElementTemplate($meta_index, $enable);
			// Let's set known values
			$el["o_id"]=0;
			$el["od_obj_id"]=$data["object_id"];
			$el["od_opt_id"]=$data["option_id"];
		} elseif($psid && $update_found){
			// Let's update element! Why not ?
			// Nothing for update
		}
		if(is_array($el) && count($el)){
			$psid = $this->save($el, $guid, $meta_index);
		}
		if(!$psid) $this->setError(Text::_("Element absent").": ".__CLASS__."->".__FUNCTION__."(".$fld_2.":".$fld_2_val.")");
		return $psid;
	}
	private function addOptionValWoGUID(&$data=array(), $create_new=true, $update_found=true, $enable=true, $restore_deleted=true){
		// if(!isset($data["guid"]) || !$data["guid"]) return 0; // CHECK WITHOUT CODE
		/*** defenitions start ***/
		$meta_index="optionvals"; $guid=false; $fld_2="ov_name"; $fld_2_val=$data["title"]; $parent_field="ov_opt_id"; $parent_field_val=$data["parent_id"];
		/*** defenitions stop ***/
		
		$psid = $this->tryToGetElementWithParent($guid, $fld_2, $fld_2_val, $parent_field, $parent_field_val, $meta_index, $enable, $restore_deleted);
		
		$el=array();
		if(!$psid && $create_new){
			// Let's create element! Why not ?
			$el=$this->getElementTemplate($meta_index, $enable);
			// Let's set known values
			$el["ov_id"]=0;
			$el["ov_name"]=$data["title"];
			$el["ov_opt_id"]=$data["parent_id"];
			$el["ov_ordering"]=$data["ordering"];
		} elseif($psid && $update_found){
			// Let's update element! Why not ?
			// Nothing for update
		}
		if(is_array($el) && count($el)){
			$psid = $this->save($el, $guid, $meta_index);
		}
		if(!$psid) $this->setError(Text::_("Element absent").": ".__CLASS__."->".__FUNCTION__."(".$fld_2.":".$fld_2_val.")");
		return $psid;
	}
	public function addMeasure($data=array(), $create_new=true, $update_found=true, $enable=true, $restore_deleted=true){
		if(!isset($data["guid"]) || !$data["guid"]) return 0;
		/*** defenitions start ***/
		$meta_index="measures"; $guid=$data["guid"]; $fld_2="meas_code"; $fld_2_val=$data["code"];
		if($this->getParam("1c_measures_search")==2) { $fld_2="meas_short_name"; $fld_2_val=$data["short_name"]; }
		elseif($this->getParam("1c_measures_search")==3) { $fld_2="meas_full_name"; $fld_2_val=$data["full_name"]; }
		/*** defenitions stop ***/
		$psid = $this->tryToGetElement($guid, $fld_2, $fld_2_val, $meta_index, $enable, $restore_deleted);
		
		$el=array();
		if(!$psid && $create_new){
			// Let's create element! Why not ?
			$el=$this->getElementTemplate($meta_index, $enable);
			// Let's set known values
			/*
			 ["base_coeff"]=>string(1) "1"
			 */
			// @TODO meas_kf always 1, ["base_coeff"] not used, change to real
			$el["meas_id"]=0;
			$el["meas_code"]=$data["code"];
			$el["meas_short_name"]=$data["short_name"];
			$el["meas_full_name"]=$data["full_name"];
			if(isset($data["base_code"])){
				$base_type = Measure::getInstance()->getType($data["base_code"]);
				if($base_type){
					$el["meas_type"]=$base_type;
					$el["meas_kf"]=1;
				} else {
					$el["meas_type"]=$this->getParam("1c_measures_default_type");
					$el["meas_kf"]=1;
				}
			} else {
				$el["meas_type"]=$this->getParam("1c_measures_default_type");
				$el["meas_kf"]=1;
			}
			$el["meas_comment"]="";
			$el["meas_enabled"]=1;
			$el["meas_deleted"]=0;
		} elseif($psid && $update_found){
			// Let's update element! Why not ?
			// Nothing for update
		}
		if(is_array($el) && count($el)){
			$psid = $this->save($el, $guid, $meta_index);
		}
		if(!$psid) $this->setError(Text::_("Element absent").": ".__CLASS__."->".__FUNCTION__."(".$guid.")");
		return $psid;
	}
	public function addVendor($data=array(), $create_new=true, $update_found=true, $enable=true, $restore_deleted=true){
		if(!isset($data["guid"]) || !$data["guid"]) return 0;
		/*** defenitions start ***/
		if(!catalogConfig::$multy_vendor) return intval(catalogConfig::$default_vendor);
		$meta_index="vendors"; $guid=$data["guid"]; $fld_2="v_inn"; $fld_2_val=$data["inn"];
		/*** defenitions stop ***/
		$psid = $this->tryToGetElement($guid, $fld_2, $fld_2_val, $meta_index, $enable, $restore_deleted);
		
		$el=array();
		if(!$psid && $create_new){
			// Let's create element! Why not ?
			$el=$this->getElementTemplate($meta_index, $enable);
			// Let's set known values
			$el["v_id"]=0;
			$el["v_cat_id"]=$this->getFirstPossibleParentId($meta_index);
			$el["v_name"] = $data["title"];
			if($data["full_title"]) $el["v_store_name"]=$data["full_title"];
			elseif($data["official_title"]) $el["v_store_name"]=$data["official_title"];
			else $el["v_store_name"]=$el["v_name"];
			$el["v_inn"]=$data["inn"];
			$el["v_kpp"]=$data["kpp"];
			if(isset($data["account"][0]) && is_array($data["account"][0]) && isset($data["account"][0]["bank"]) && is_array($data["account"][0]["bank"])){
				$el["v_bank_acc"] = $data["account"][0]["number"];
				$el["v_bank"] = $data["account"][0]["bank"]["title"];
				$el["v_sett_acc"] = $data["account"][0]["bank"]["corr_account"];
				$el["v_bik"] = $data["account"][0]["bank"]["bik"];
				// $el[""] = $data["account"][0]["bank"]["address"]; // адрес банка строкой
			}
			// $el[""] = $data["okpo"]; // ОКПО строкой
			$el["v_address_u"]=$this->encodeAddress($data["legal_address"]);
			$el["v_address_p"]=$this->encodeAddress($data["post_address"]);
		} elseif($psid && $update_found){
			// Let's update element! Why not ?
			// Let's set known values
			$el["v_id"]=$psid;
			if($data["title"]) $el["v_name"] = $data["title"];
			if($data["full_title"]) $el["v_store_name"]=$data["full_title"];
			elseif($data["official_title"]) $el["v_store_name"]=$data["official_title"];
			
			if($data["inn"]) $el["v_inn"]=$data["inn"];
			if($data["kpp"]) $el["v_kpp"]=$data["kpp"];
			if(isset($data["account"][0]) && is_array($data["account"][0]) && isset($data["account"][0]["bank"]) && is_array($data["account"][0]["bank"])){
				if($data["account"][0]["number"]) $el["v_bank_acc"] = $data["account"][0]["number"];
				if($data["account"][0]["bank"]["title"]) $el["v_bank"] = $data["account"][0]["bank"]["title"];
				if($data["account"][0]["bank"]["corr_account"]) $el["v_sett_acc"] = $data["account"][0]["bank"]["corr_account"];
				if($data["account"][0]["bank"]["bik"]) $el["v_bik"] = $data["account"][0]["bank"]["bik"];
				// $el[""] = $data["account"][0]["bank"]["address"]; // адрес банка строкой
			}
			// $el[""] = $data["okpo"]; // ОКПО строкой
			if($data["legal_address"]) $el["v_address_u"]=$this->encodeAddress($data["legal_address"]);
			if($data["post_address"]) $el["v_address_p"]=$this->encodeAddress($data["post_address"]);
		}
		if(is_array($el) && count($el)){
			$psid = $this->save($el, $guid, $meta_index);
		}
		if(!$psid) $this->setError(Text::_("Element absent").": ".__CLASS__."->".__FUNCTION__."(".$guid.")");
		return $psid;
	}
	/****************************** ORDERS ****************************/
	private function makeOrderCustomerInfo(&$order) {
		$customer = array();
		if(isset($order->o_userdata["userdata_person"])) $person = $order->o_userdata["userdata_person"]; else $person = "";
		if(isset($order->o_userdata["userdata_email"])) $email = $order->o_userdata["userdata_email"]; else $email = "";
		if(isset($order->o_userdata["userdata_phone"])) $phone = $order->o_userdata["userdata_phone"]; else $phone = "";
		if(isset($order->o_dt_data["fullinfo"])) $delivery_address = $order->o_dt_data["fullinfo"]; else $delivery_address = "";
		
		// For future purposes
		if($order->o_uid && ACLObject::getInstance('maintenanceUserdata')->canAccess()){
			$userdata = Userdata::getInstance($order->o_uid);
// $this->dump2screen($userdata->getAddresses());
		}
		
		if(!$person) $person = BaseCML::_("testCML_Private_person");
		$fio_arr = explode(" ", trim($person));
		if (count($fio_arr) == 1) {
			$lastname = Text::ucfirst($fio_arr[0]); $firstname = ""; $middlename = "";
		} else if (count($fio_arr) == 2) {
			$lastname = Text::ucfirst($fio_arr[0]); $firstname = Text::ucfirst($fio_arr[1]); $middlename = "";
		} else if (count($fio_arr) > 2) {
			$lastname = Text::ucfirst($fio_arr[0]); $firstname = Text::ucfirst($fio_arr[1]); $middlename = Text::ucfirst($fio_arr[2]);
		}
		// Required fields for trading system
		$customer = array(
				BaseCML::_("Id") => $order->o_uid . '#' . $email,
				BaseCML::_("testCML_Role") => BaseCML::_("testCML_RoleCustomerDefault"),
				BaseCML::_("Title") => $person,
				BaseCML::_("FullTitle") => $person,
				BaseCML::_("testCML_LastName") => $lastname,
				BaseCML::_("testCML_FirstName") => $firstname,
				BaseCML::_("testCML_Phone") => array(BaseCML::_("View") => $phone),
				BaseCML::_("testCML_Email") => array(BaseCML::_("View") => $email),
				BaseCML::_("testCML_RegistrationAddress") => $delivery_address
		);
		// For future purposes
		switch($this->export_system){
			case "ut103":
			case "ut11":
			case "ip":
				// Private person
				$customer[BaseCML::_("Address")] = "";
				$customer[BaseCML::_("Contacts")] = array(
						BaseCML::_("Contact")."1" => array( BaseCML::_("Type") => BaseCML::_("testCML_Work_phone"), BaseCML::_("Value") => $phone, BaseCML::_("Comment") => BaseCML::_("testCML_Loaded_from_site") ),
						BaseCML::_("Contact")."2" => array( BaseCML::_("Type") => BaseCML::_("testCML_Mail"), BaseCML::_("Value") => $email, BaseCML::_("Comment") => BaseCML::_("testCML_Loaded_from_site") ),
						BaseCML::_("Contact")."3" => array( BaseCML::_("Type") => BaseCML::_("testCML_Actual_address"), BaseCML::_("Value") => $delivery_address, BaseCML::_("Comment") => BaseCML::_("testCML_Loaded_from_site") )
				);
				// Company
				$customer[BaseCML::_("OfficialTitle")]	= "";
				$customer[BaseCML::_("testCML_Delegates")] = array(
						BaseCML::_("testCML_Delegate") => array( BaseCML::_("testCML_Relationship") => BaseCML::_("testCML_Contact_person"), BaseCML::_("Title") => "" )
				);
				// $customer[BaseCML::_("testCML_INN")] = $order['payment_company_inn'];
				// $customer[BaseCML::_("testCML_KPP")] = $order['payment_company_kpp'];
				break;
			case "unf16":
				// Company
				$customer[BaseCML::_("testCML_LegalAddress")] = "";
				$customer[BaseCML::_("FullTitle")] = "";
				break;
			default:
				break;
		}
		$this->logDebug(print_r($customer, true));
		return $customer;
	}
	private function makeOrderRequisites(&$order) {
		$data = array();
		$requisites = array();
		$req_counter = 0;
		$orders_model = Module::getInstance("catalog")->getModel("orders");
		$requisites[BaseCML::_("testCML_Shipping_date")] = date("Y-m-d", strtotime($order->o_date));
		$requisites[BaseCML::_("testCML_Order_status")] = $orders_model->getStatusName($order->o_status);
		foreach ($requisites as $name=>$value) {
			if (!$value) continue;
			$requisite_index = BaseCML::_("testCML_TraitValue").$req_counter;
			$data[$requisite_index]	= array( BaseCML::_("Title") => $name, BaseCML::_("Value") => $value );
			$req_counter ++;
		}
		return $data;
	}
	private function getOrderGoodsFeatureGuid($item){
		$result = "";
		if(($this->offers_mode==1 || $this->offers_mode==2)){
			$found=0;
			$options_data = json_decode($item->i_g_options, true);
			if(is_array($options_data) && count($options_data)){
				foreach($options_data as $opt_key=>$opt_val){
					if(is_array($opt_val) && count($opt_val)){
						foreach($opt_val as $o_key=>$option){
							if($option["ovd_extcode"] && $option["val_id"]) {
								$result = $option["ovd_extcode"];
								$found++;
							}
						}
					}
					
				}
			}
			if($found>1){
				$result = "";
				$this->logWarning("Multiply offers for order position: ".__CLASS__."->".__FUNCTION__."(OrderID=".$item->i_order_id.", ItemID=".$item->i_id.")");
			}
		}
		return $result;
	}
	private function makeOrderTaxes($order, &$_element){
		$data=array();
		switch ($this->export_system){
			case "ut103": // OK
			case "ut11":
			case "unf16": // OK
			case "ip":
			default:	
				if($order->o_taxes_sum>0){
					$data[BaseCML::_("Tax")][BaseCML::_("Title")] = BaseCML::_("testCML_TaxNDS");
					$data[BaseCML::_("Tax")][BaseCML::_("InSum")] = "true";
					$data[BaseCML::_("Tax")][BaseCML::_("testCML_Sum")] = $order->o_taxes_sum;
				} else {
					$data[BaseCML::_("Tax")][BaseCML::_("Title")] = BaseCML::_("testCML_Without_tax");
					// $data[BaseCML::_("Tax")][BaseCML::_("InSum")] = "true";
				}
				if(count($data)) $_element[BaseCML::_("testCML_Taxes")] = $data; 
				break;
		}
	}
	private function makeOrderGoodsTaxes($item, &$_element){
		switch ($this->export_system){
			case "ut103": // OK
			case "ut11":
			case "unf16": // OK
			case "ip":
			default:
				$data=array();
				$data[BaseCML::_("Tax")][BaseCML::_("Title")] = ($item->i_g_tax_val > 0 ? BaseCML::_("testCML_TaxNDS") : BaseCML::_("testCML_Without_tax"));
				$data[BaseCML::_("Tax")][BaseCML::_("InSum")] = "true";
				$data[BaseCML::_("Tax")][BaseCML::_("testCML_Sum")] = $item->i_g_tax;
				$data[BaseCML::_("Tax")][BaseCML::_("testCML_Rate")] = ($item->i_g_tax_val > 0 ? $item->i_g_tax_val : BaseCML::_("testCML_Without_tax"));
				if(count($data)) $_element[BaseCML::_("testCML_Taxes")] = $data;
				$data=array();
				$data[BaseCML::_("testCML_TaxRate")][BaseCML::_("Title")] = ($item->i_g_tax_val > 0 ? BaseCML::_("testCML_TaxNDS") : BaseCML::_("testCML_Without_tax"));
				$data[BaseCML::_("testCML_TaxRate")][BaseCML::_("testCML_Rate")] = intval($item->i_g_tax_val);
				if(count($data)) $_element[BaseCML::_("testCML_TaxRates")] = $data;
				break;
		}
		return $data;
	}
	public function prepareOrders($orders){
		$docs=array(); $docs_counter = 0;
		$orders_model = Module::getInstance("catalog")->getModel("orders");
		if($orders && count($orders)){
			$orders = $orders_model->decodeOrdersData($orders);
			foreach ($orders as $order){
				$items = $orders_model->getOrderItems($order->o_id);
				if($items && count($items)){
					$docs_index = BaseCML::_("testCML_Document") . $docs_counter;
					$docs[$docs_index] = array();
					$docs[$docs_index][BaseCML::_("Id")] = $order->o_id;
					$docs[$docs_index][BaseCML::_("testCML_Nomer")] = $order->o_id;
					$docs[$docs_index][BaseCML::_("testCML_Date")] = date("Y-m-d", strtotime($order->o_date));
					$docs[$docs_index][BaseCML::_("testCML_Time")] = date("H:i:s", strtotime($order->o_date));
					$docs[$docs_index][BaseCML::_("Currency")] = Currency::getCode($order->o_currency);
					$docs[$docs_index][BaseCML::_("testCML_CurrencyRate")] = 1; // For now, put 1
					$docs[$docs_index][BaseCML::_("testCML_BusinessOperation")] = BaseCML::_("testCML_BusinessOperationDefault");
					$docs[$docs_index][BaseCML::_("testCML_Role")] = BaseCML::_("testCML_RoleVendorDefault");
					$docs[$docs_index][BaseCML::_("testCML_Sum")] = $order->o_total_sum;
					$docs[$docs_index][BaseCML::_("Comment")] = $order->o_comments;
					$this->makeOrderTaxes($order, $docs[$docs_index]);
					$docs[$docs_index][BaseCML::_("testCML_Contractors")][BaseCML::_("testCML_Contractor")] = $this->makeOrderCustomerInfo($order);
					$docs[$docs_index][BaseCML::_("TraitsValues")] = $this->makeOrderRequisites($order);
					$goods_counter=0;
					$document[$docs_index][BaseCML::_("Elements")] =array();
					foreach ($items as $item){
						$goods_index = BaseCML::_("Element") . $goods_counter;
						$docs[$docs_index][BaseCML::_("Elements")][$goods_index] = array();
						$docs[$docs_index][BaseCML::_("Elements")][$goods_index][BaseCML::_("Id")] = ($item->i_g_extcode ? $item->i_g_extcode : $item->i_g_id);
						$docs[$docs_index][BaseCML::_("Elements")][$goods_index][BaseCML::_("Article")] = $item->i_g_sku;
						$docs[$docs_index][BaseCML::_("Elements")][$goods_index][BaseCML::_("Title")] = $item->i_g_name;
						$docs[$docs_index][BaseCML::_("Elements")][$goods_index][BaseCML::_("PriceForOne")] = $item->i_g_price;
						$docs[$docs_index][BaseCML::_("Elements")][$goods_index][BaseCML::_("Amount")] = $item->i_g_quantity;
						$docs[$docs_index][BaseCML::_("Elements")][$goods_index][BaseCML::_("testCML_Sum")] = $item->i_g_sum;
						$this->makeOrderGoodsTaxes($item, $docs[$docs_index][BaseCML::_("Elements")][$goods_index]);
						$measure_id = $item->i_g_measure;
						if(Measure::getInstance()->getMeasure($measure_id) ===false){
							$measure_id = catalogConfig::$default_measure;
						}
						switch ($this->export_system){
							case "ut103":
							case "ut11":
							case "unf16":
							case "ip":
								$docs[$docs_index][BaseCML::_("Elements")][$goods_index][BaseCML::_("BaseUnit")]=array(
									"@attributes" => array(
										BaseCML::_("Code") => Measure::getInstance()->getCode($measure_id),
										BaseCML::_("FullName") => Measure::getInstance()->getTitle($measure_id)
									),
									"@value" => Measure::getInstance()->getShortName($measure_id)
									);
								break;
							default:
								// May be sometimes will be usefull
								$docs[$docs_index][BaseCML::_("Elements")][$goods_index][BaseCML::_("BaseUnit")]=array(
									BaseCML::_("Code") => Measure::getInstance()->getCode($measure_id),
									BaseCML::_("FullName") => Measure::getInstance()->getTitle($measure_id)
								);
								break;
						}
						$docs[$docs_index][BaseCML::_("Elements")][$goods_index][BaseCML::_("Measure")]=Measure::getInstance()->getShortName($measure_id);
						// We have discounts now only for full order
						$docs[$docs_index][BaseCML::_("Elements")][$goods_index][BaseCML::_("testCML_Discounts")]=array(
								BaseCML::_("testCML_Discount") => array( BaseCML::_("InSum") => 'false', BaseCML::_("testCML_Sum") => 0 )
						);
						$goods_type=$item->i_g_type;
						$goods_type_str = SpravStatic::getValueFromCKArray("goods_type", $goods_type);
						if(!$goods_type_str) $goods_type_str = SpravStatic::getValueFromCKArray("goods_type", 1);
						$docs[$docs_index][BaseCML::_("Elements")][$goods_index][BaseCML::_("TraitsValues")]=array(
								BaseCML::_("testCML_TraitValue") => array(
										BaseCML::_("Title") => BaseCML::_("testCML_TypeOfNomenclature"),
										BaseCML::_("Value") => $goods_type_str
								)
						);
						if($this->getParam("1c_orders_reserve")){
							$docs[$docs_index][BaseCML::_("Elements")][$goods_index][BaseCML::_("testCML_Reserve")] = $item->i_g_quantity;
						}
						if($item->i_g_extcode){
							$feature_guid = $this->getOrderGoodsFeatureGuid($item);
							if($feature_guid) $docs[$docs_index][BaseCML::_("Elements")][$goods_index][BaseCML::_("Id")].= "#".$feature_guid;
						}
						$goods_counter++;
					}
					$extended_comment = " ".Text::_("Delivery type").": ".$order->o_dt_name.", ";
					$extended_comment.= catalogDelivery::getDeliveryClass($order->o_dt_id)->renderInfo($order->o_dt_data);
					$docs[$docs_index][BaseCML::_("Comment")].= strip_tags($extended_comment);
				}
				$docs_counter++;
// $this->dump2screen($order);
			}
		}
		return $docs;
	}
}
?>