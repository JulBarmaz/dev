<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogModelimport extends Model {
	private $_in_debug = false;
	
	private function log2file($message, $title=""){
		if(!$this->_in_debug) return;
		if(is_array($message) || is_object($message)) $message = print_r($message, true);
		Util::logFile($message, $title);
	}
	public function resetSessionVars(){
		Session::unsetVar("CATIMPHDR");
		Session::unsetVar("CATIMPSETTINGS");
		Session::unsetVar("CATIMPFLDS");
		Session::unsetVar("CATIMPFLDS_UPDATE");
		Session::unsetVar("CATIMPFLDS_UPDATE_LIST");
		Session::unsetVar("CATIMPDEF");
		Session::unsetVar("CATIMPERR");
		Session::unsetVar("CATIMPPROC");
		Session::setVar("CATIMPFAILED",false);
	}
	public function setImportVars($def_fields){
		$settings['clean_tables']=Request::getInt("clean_tables",0);
		$settings['overwrite_data']=Request::getInt("overwrite_data",1);
		$settings['insert_new_data']=Request::getInt("insert_new_data",1);
		$settings['disable_all_goods']=Request::getInt("disable_all_goods",0);
		$settings['portions']=Request::getInt("portions",500);
		$settings['parent_group_id']=Request::getInt("parent_group_id",0);
		if($settings['clean_tables']) $settings['parent_group_id']=0;
		Session::setVar("CATIMPSETTINGS", $settings);
		/***********************************************/
		$hdrs=Session::getVar("CATIMPHDR");
		$grp=array();
		if(count($hdrs)){
			foreach($hdrs as $key=>$val){
				$tmp=Request::getSafe($key,"");
				$tmp_update=Request::getSafe("update_field_".$key,0);
				$tmp_update_list=Request::getSafe("update_list_".$key,0);
				if($tmp=="0") continue;
				if ($tmp=="is_grp"){
					$grp[]=$key;
				} else {
					$f[$key]=$tmp;
					$f_update[$tmp]=$tmp_update;
					$f_update_list[$tmp]=$tmp_update_list;
				}
			}
			$fields=array_flip($f);
			$fields_update=array_flip($f_update);
//			$fields_update_list=array_flip($f_update_list);
			$fields["grp"]=$grp;
			Session::setVar("CATIMPFLDS", $fields);
			Session::setVar("CATIMPFLDS_UPDATE", $f_update);
			Session::setVar("CATIMPFLDS_UPDATE_LIST", $f_update_list);
			/***********************************************/
			foreach($def_fields as $fld){
				$defaults[$fld]=Request::getSafe($fld,"");
			}
			Session::setVar("CATIMPDEF", $defaults);
		} else Session::setVar("CATIMPFAILED",true);
	}
	public function setHeaders($hdr_array){
		foreach($hdr_array as $key=>$val){
			if($key>84) continue;
			$arr['f'.($key+1)]=$val;
		}
		Session::setVar("CATIMPHDR", $arr);
	}
	public function cleanTempTable(){
		$sql="DELETE FROM #__goods_import_tmp";
		$this->_db->setQuery($sql); 
		return $this->_db->query();
	}
	public function cleanTables(){
		$sql="DELETE FROM #__goods";
		$this->_db->setQuery($sql); $this->_db->query();
		$sql="DELETE FROM #__goods_opt_vals_data";
		$this->_db->setQuery($sql); $this->_db->query();
		$sql="DELETE FROM #__goods_options_data";
		$this->_db->setQuery($sql); $this->_db->query();
		$sql="DELETE FROM #__goods_data";
		$this->_db->setQuery($sql); $this->_db->query();
		$sql="DELETE FROM #__goods_links";
		$this->_db->setQuery($sql); $this->_db->query();
		$sql="DELETE FROM #__goods_group";
		$this->_db->setQuery($sql); $this->_db->query();
		$sql="DELETE FROM #__goods_group_fields";
		$this->_db->setQuery($sql); $this->_db->query();
	}
	public function importTempTable($file){
		$imported=0;
/*
		$readed = file($file);
		$csv_data=BaseCSV::parseCSV($readed);
*/
		$csv_data=BaseCSV::parseCSVFile($file);
		if(is_array($csv_data)) {
			$hdr_array = $csv_data[0];
			$this->setHeaders($hdr_array);
			if (count($csv_data)){
				foreach($csv_data as $_ind=>$row){
					if (!$_ind) continue; // It's headers
					if ($this->insertRow($_ind, $row)) $imported++;
				}
			}
		} else Session::setVar("CATIMPFAILED",true);
		return $imported;
	}
	public function insertRow($_ind, $row){
		$sql="INSERT INTO #__goods_import_tmp (";
		$sql1="id";
		$sql2=$_ind;
		foreach ($row as $key=>$val){
			if($key>64) continue;
			$sql1.=",f".($key+1);
			//$sql2.=",'".htmlspecialchars(trim($val),ENT_QUOTES,'UTF-8')."'";
			$sql2.=",'".$this->_db->getEscaped(trim($val))."'";
		}
		$sql.=$sql1.") VALUES (".$sql2.")";
		$this->_db->setQuery($sql);
		return $this->_db->query();
	}
	public function getAddFieldsWLists($hidden_fields){
		// From metadata
		$arr=array();
		$meta=new SpravMetadata("catalog","goods","default");
		foreach($meta->field as $key=>$val){
			if (in_array($val, $hidden_fields)) continue;
			if($meta->is_add[$key] && is_array($meta->ck_reestr[$key])){
				$arr[$val]=1;
			} else {
				$arr[$val]=0;
			}
		}
		return $arr;
	}
	public function getFields($hidden_fields){
		// From metadata
		$arr["is_grp"]=Text::_("Group of goods");
		$meta=new SpravMetadata("catalog","goods","default");
		foreach($meta->field as $key=>$val){
			if (in_array($val, $hidden_fields)) continue;
			$arr[$val]=Text::_($meta->field_title[$key]);
		}
		return $arr;
	}
	public function getFieldsData($def_fields){
		$meta=new SpravMetadata("catalog","goods","default");
		foreach($meta->field as $key=>$val){
			if (!array_key_exists($val, $def_fields)) continue;
			$arr[$val]["title"]=Text::_($meta->field_title[$key]);
			$arr[$val]["type"]=$meta->input_type[$key];
			$arr[$val]["data"]="";
			$arr[$val]["val"]=$def_fields[$val];
			if ($meta->input_type[$key]=="select"){
				if ($meta->ck_reestr[$key]) {
					$arr[$val]["type"]="ck_select";
					$arr[$val]["data"]=SpravStatic::getCKArray($meta->ck_reestr[$key]);
				} else if ($meta->ch_table[$key]){
					$arr[$val]["type"]="ch_select";
					$arr[$val]["data"]=$this->getChArray($meta->ch_table[$key], $meta->ch_id[$key], $meta->ch_field[$key], $meta->ch_deleted[$key], $meta->ch_enabled[$key], $meta->ch_sort[$key]);
				}
			}
		}
		return $arr;
	}
	public function getChArray($ch_table, $ch_id, $ch_field, $ch_deleted="", $ch_enabled="", $ch_sort=""){
		$sql="SELECT ".$ch_id." AS id,".$ch_field." AS name FROM #__".$ch_table;
		if ($ch_deleted&&$ch_enabled) $sql.=" WHERE ".$ch_deleted."=0 AND ".$ch_enabled."=1";
		elseif ($ch_deleted) $sql.=" WHERE ".$ch_deleted."=0";
		elseif ($ch_enabled) $sql.=" WHERE ".$ch_enabled."=1";
		if ($ch_sort) $sql.=" ORDER BY ".$ch_sort;
		$this->_db->setQuery($sql);
		return $this->_db->loadAssocList();
	}
	public function getAddFields($table='') {
		$join_sql_txt='';
		$where_sql_txt='';
		$query="select fm.* from #__fields_list as fm";
		$where_sql_txt.=" where fm.f_deleted=0 and fm.f_table='".$table."'";
		$this->_db->setQuery($query.$join_sql_txt.$where_sql_txt);
		return $this->_db->loadObjectList('f_name');
	}
	public function proceedImport($row2start, $hidden_fields, $def_fields){
		$rec_proceed=0;
		if (!$row2start) { Session::setVar("CATIMPPROC",array()); Session::setVar("CATIMPERR",0); }
		$processed = Session::getVar("CATIMPPROC");
		$errors = Session::getVar("CATIMPERR");
		$settings = Session::getVar("CATIMPSETTINGS");
		if (!$row2start && $settings['clean_tables']){ $this->cleanTables(); }
		if (!$row2start && $settings['disable_all_goods']){
			$sql="UPDATE #__goods SET g_enabled=0;";
			$this->_db->setQuery($sql);
			$this->_db->query();
		}
		$sql="SELECT * FROM #__goods_import_tmp LIMIT ".$row2start.",".$settings["portions"];
		$this->_db->setQuery($sql);
		$data=$this->_db->loadAssocList("id");
		$rec_count=count($data);
		$res["log_message"]="";
		$fields_update_list = Session::getVar("CATIMPFLDS_UPDATE_LIST");
		if ($rec_count){
			$fields = Session::getVar("CATIMPFLDS");
			$fields_update = Session::getVar("CATIMPFLDS_UPDATE");
			$defaults = Session::getVar("CATIMPDEF");
			$meta=new SpravMetadata("catalog","goods","default");
			foreach($data as $ind=>$row){
				if(!$row[$fields["g_sku"]]) { $res["log_message"].="<br /><span class=\"error\">".Text::_("Empty SKU")." : ".$row[$fields["g_name"]].". </span>".Text::_("Skipped"); $rec_proceed++; $errors++; continue;}
				if (array_key_exists($row[$fields["g_sku"]], $processed)){ $res["log_message"].="<br /><span class=\"error\">".Text::_("Duplicated SKU")." : ".$row[$fields["g_sku"]]." - ".$row[$fields["g_name"]].". </span>".Text::_("Skipped"); $rec_proceed++; $errors++; continue;}
				if(!$row[$fields["g_name"]]) { $res["log_message"].="<br /><span class=\"error\">".Text::_("Empty name")." : ".$row[$fields["g_sku"]].". </span>".Text::_("Skipped"); $rec_proceed++; $errors++; continue;}
				if (!$this->proceedRow($row, $meta, $fields, $fields_update, $defaults, $settings["overwrite_data"], $settings["insert_new_data"], $settings["parent_group_id"], $hidden_fields, $def_fields)) { 
					$res["log_message"].="<br /><span class=\"error\">".Text::_("Database error").": &quot;".$row[$fields["g_sku"]]."&quot; - &quot;".$row[$fields["g_name"]]."&quot;. </span>";
					$res["log_message"].="<br /><span class=\"error\">".Database::getInstance()->getLastError()."</span>";
					$rec_proceed++; 
					$errors++; 
					break;
				}
/* НЕ УБИВАТЬ !!! ОТРЕМИТЬ ПЕРЕД РАБОТОЙ !!!
 * 
 */
				$processed[$row[$fields["g_sku"]]]=1;
/*				
 */
				$rec_proceed++;
			}
		}
		$res["row2start"]=$row2start + $rec_proceed;
		$res["status_message"]=Text::_("Processed rows")." : ".$res["row2start"];
		if ($rec_count<$settings["portions"]) { 
			$res["status"]="finished";
			$res["log_message"].="<br />".Text::_("Processed rows")." : ".$res["row2start"];
			$res["log_message"].="<br />".Text::_("Error rows")." : ".$errors;
			$this->updateAddFields($fields_update_list);
		}	else $res["status"]="processing";
		Session::setVar("CATIMPPROC", $processed);
		Session::setVar("CATIMPERR", $errors);
		return $res;
	}
	public function proceedRow($row, $meta, $fields, $fields_update, $defaults, $overwrite_data, $insert_new_data, $parent_group_id, $hidden_fields, $def_fields){
		$sku=$row[$fields["g_sku"]];
		$result=1;
		$g_id=$this->getGoodsID($sku);
		$this->log2file("Goods id=".$g_id);
		if ($g_id){
			if ($overwrite_data){
				$ggr_id=$this->getGroupID($row, $fields["grp"], $parent_group_id);
				$default_values=$this->recalcDefaults($row, $fields, $defaults, $meta);
				$g_alias="";
				$g_name="";
				$upd_fld="UPDATE #__goods SET g_sku='".$sku."'";
				foreach($meta->field as $ind=>$fld){
					if ($fld=="g_sku") continue;
					if ($meta->is_add[$ind] || in_array($fld, $hidden_fields)) continue;
					if (!array_key_exists($fld, $fields)) continue;
					$val=$this->reductionOfType($meta, $ind, $fld, $default_values, $fields, $row);
					if ($val!=false && array_key_exists($fld, $fields_update) && $fields_update[$fld]) $upd_fld.=", ".$fld."=".$val;
					if($fld=="g_alias") $g_alias=$this->reductionOfType($meta, $ind, $fld, $default_values, $fields, $row, "");
					if($fld=="g_name") $g_name=$this->reductionOfType($meta, $ind, $fld, $default_values, $fields, $row,"");
				}
				$upd_fld.=", g_change_date=NOW(), g_change_uid=".User::getInstance()->getID().", g_enabled=1";
				$upd_fld.=" WHERE g_sku='".$sku."'";
				$this->_db->setQuery($upd_fld);
				$this->log2file("Goods update [".$g_id."]. Main query: ".$this->_db->getQuery());
				if ($this->_db->query()){
					$this->log2file("Goods updated.[".$g_id."]");
					$this->updateAlias($g_id, $g_alias, $g_name);
					foreach($meta->field as $ind=>$fld){
						if($meta->is_add[$ind]){
							if (!array_key_exists($fld, $fields)) continue;
							$val=$this->reductionOfType($meta, $ind, $fld, $default_values, $fields, $row);
							if ((($meta->val_type[$ind]=="string"||$meta->val_type[$ind]=="text") && $val=="''")) continue;
							if ($val !== false && array_key_exists($fld, $fields_update) && $fields_update[$fld]){
								$add_sql="INSERT INTO #__goods_data (obj_id, field_id, field_name, field_value) VALUES (".$g_id.", ".$meta->is_add[$ind].", '".$fld."', ".$val.") ON DUPLICATE KEY UPDATE field_value=".$val;
								$this->_db->setQuery($add_sql);
								$this->log2file("Goods update [".$g_id."]. Query for add fields: ".$this->_db->getQuery());
								if (!$this->_db->query()) {
									$this->log2file("Query error:".$this->_db->getLastError());
									$result=0;
								}
							}
						}
					}
					if($ggr_id) {
						$grp_sql="INSERT IGNORE INTO #__goods_links (`g_id`, `ordering`, `parent_id`) VALUES	(".$g_id.", 0, ".$ggr_id.")";
						$this->_db->setQuery($grp_sql); 
						$this->log2file("Goods update [".$g_id."]. Query for update link: ".$this->_db->getQuery());
						if (!$this->_db->query()) {
							$this->log2file("Query error:".$this->_db->getLastError());
							$result=0;
						}
					}
				} else {
					$this->log2file("Query error:".$this->_db->getLastError());
					$result=0;
				}
			}
		} else { // новый
			if ($insert_new_data){
				$ggr_id=$this->getGroupID($row, $fields["grp"], $parent_group_id);
				$default_values=$this->recalcDefaults($row, $fields, $defaults, $meta);
				$g_alias="";
				$g_name="";
				$ins_fld="INSERT INTO #__goods(g_id,g_deleted";
				$val_fld=" VALUES (NULL,0";
				foreach($meta->field as $ind=>$fld){
					if ($meta->is_add[$ind] || in_array($fld, $hidden_fields)) continue;
					$val=$this->reductionOfType($meta, $ind, $fld, $default_values, $fields, $row);
					if ($val!=false) { $ins_fld.=",".$fld; $val_fld.=",".$val; }
					if($fld=="g_alias") $g_alias=$this->reductionOfType($meta, $ind, $fld, $default_values, $fields, $row,"");
					if($fld=="g_name") $g_name=$this->reductionOfType($meta, $ind, $fld, $default_values, $fields, $row,"");
				}
				$ins_fld.=",g_change_date,g_change_uid, g_enabled)";
				$val_fld.=",NOW(),".User::getInstance()->getID().",1)";
				$goods_sql=$ins_fld.$val_fld;
				$this->_db->setQuery($goods_sql); 
				$this->log2file("Goods insert [".$g_name."]. Main query: ".$this->_db->getQuery());
				if ($this->_db->query()){
					$g_id=$this->_db->insertid();
					$this->updateAlias($g_id, $g_alias, $g_name);
					foreach($meta->field as $ind=>$fld){
						if($meta->is_add[$ind]){
							$val=$this->reductionOfType($meta, $ind, $fld, $default_values, $fields, $row);
							if ((($meta->val_type[$ind]=="string"||$meta->val_type[$ind]=="text") && $val=="''")) continue;
							if ($val!=false){
								$add_sql="INSERT INTO #__goods_data (obj_id, field_id, field_name, field_value) VALUES (".$g_id.", ".$meta->is_add[$ind].", '".$fld."', ".$val.")";
								$this->_db->setQuery($add_sql);
								$this->log2file("Goods insert [".$g_id."]. Query for add fields: ".$this->_db->getQuery());
								if (!$this->_db->query()) {
									$this->log2file("Query error:".$this->_db->getLastError());
									$result=0;
								}
							}
						}
					}
					if($ggr_id) {
						$grp_sql="INSERT INTO #__goods_links (`g_id`, `ordering`, `parent_id`) VALUES	(".$g_id.", 0, ".$ggr_id.")";
						$this->_db->setQuery($grp_sql); 
						$this->log2file("Goods update [".$g_id."]. Query for update link: ".$this->_db->getQuery());
						if (!$this->_db->query()) {
							$this->log2file("Query error:".$this->_db->getLastError());
							$result=0;
						}
					} else {
						$grp_sql="DELETE FROM #__goods_links WHERE g_id=".$g_id;
						$this->_db->setQuery($grp_sql);
						$this->_db->query();
					}
				} else {
					$this->log2file("Query error:".$this->_db->getLastError());
					$result=0;
				}
			}		
		}
		return $result;
	}
	public function recalcDefaults($row, $fields, $defaults, $meta){
		$default_values["g_manufacturer"]=$this->getManufacturerID($row, $fields, $defaults);
		$default_values["g_vendor"]=$this->getVendorID($row, $fields, $defaults);
		$default_values["g_tax"]=$this->getTaxID($row, $fields, $defaults);
		$default_values["g_currency"]=$this->getCurrencyID($row, $fields, $defaults);
		$default_values["g_selltype"]=$this->getCKValue($row,"g_selltype", $fields, $defaults, $meta);
		$default_values["g_type"]=$this->getCKValue($row,"g_selltype", $fields, $defaults, $meta);
		$default_values["g_pack_koeff"] = $this->getPackKoeff($row, $fields, $defaults, $meta);
		$default_values["g_size_measure"]=$this->getMeasureID($row,"g_size_measure",2, $fields, $defaults);
		$default_values["g_measure"]=$this->getMeasureID($row,"g_measure",0, $fields, $defaults);
		$default_values["g_pack_measure"]=$this->getMeasureID($row,"g_pack_measure",0, $fields, $defaults);
		$default_values["g_wmeasure"]=$this->getMeasureID($row,"g_wmeasure",4, $fields, $defaults);
		$default_values["g_vmeasure"]=$this->getMeasureID($row,"g_vmeasure",1, $fields, $defaults);
		return $default_values;
	}
	private function convertSelectValues($meta, $ind, $val){
		$result="";
		if($meta->ck_reestr[$ind]) {
			if (is_array($meta->ck_reestr[$ind])) $key_arr=$meta->ck_reestr[$ind];
			else $key_arr=SpravStatic::getCKArray($meta->ck_reestr[$ind]);
			if(is_array($key_arr) && $meta->input_type[$ind] == "multiselect"){
				$vals = explode(",", trim($val, ","));
				$ms_code = array();
				if(is_array($vals)){
					foreach($vals as $ms_key=>$ms_val){
						$ms_val = Request::makeSafe(trim($ms_val));
						if(in_array($ms_val, $key_arr)){
							$key_index=array_search($ms_val, $key_arr);
							$ms_code[]=$key_index;
						}
					}
					$result = ";".implode(";", $ms_code).";";
				} else {
					$result="";
				}
			} elseif(is_array($key_arr) && in_array(Request::makeSafe(trim($val)), $key_arr)) {
				$key_index=array_search(Request::makeSafe(trim($val)), $key_arr);
				$result=$key_index;
			} else {
				$result="";
			}
		} else {
			$result = $val;
		}
		return $result;
	}
	public function reductionOfType($meta, $ind, $fld, $default_values, $fields, $row, $escape_symbol="'"){
		$val_type = $meta->val_type[$ind];
		switch($val_type){
			case "int":
				if (array_key_exists($fld, $default_values)) $val = (int)$default_values[$fld];
				elseif(array_key_exists($fld, $fields)) $val = (int)$row[$fields[$fld]];
				else $val = false;
				break;
			case "currency":
			case "float":
				if (array_key_exists($fld, $default_values)) $val = $default_values[$fld];
				elseif(array_key_exists($fld, $fields)) $val = floatval(str_replace(",",".", $row[$fields[$fld]]));
				else $val = false;
				break;
			case "boolean":
				if (array_key_exists($fld, $default_values)) $val = $default_values[$fld];
				elseif(array_key_exists($fld, $fields)) $val = ((int)$row[$fields[$fld]] ? 1 : 0);
				else $val = false;
				break;
			case "string":
			case "text":
			default:
				if (array_key_exists($fld, $default_values)) {
					$val = $default_values[$fld];
				} elseif(array_key_exists($fld, $fields)) {
					$val = $row[$fields[$fld]];
					$val = $this->convertSelectValues($meta, $ind, $val);
					$val = Request::makeSafe($val);
				} else {
					$val = false;
				}
				if($val !== false){
					$val=$escape_symbol.$val.$escape_symbol;
				}
				break;
		}
		return $val;
	}
	// коэффициент упаковки
	public function getPackKoeff($row, $fields, $defaults, $meta){
		$_id=floatval(str_replace(",",".", $defaults["g_pack_koeff"]));
		if (isset($fields["g_pack_koeff"])){
			$id=floatval(str_replace(",",".", $row[$fields["g_pack_koeff"]]));
			if ($id==0 && $row[$fields["g_pack_koeff"]]!=0){ $id=$_id; }
		} else { $id=$_id; }
		return $id;
	}
	// Тип товара (услуги)
	// Тип отпуска товара
	public function getCKValue($row, $key, $fields, $defaults, $meta){
		$id=false;
		if (isset($fields[$key])){
			$_name=mb_substr($row[$fields[$key]],0,64,DEF_CP); 	//$_name = htmlspecialchars($_name,ENT_COMPAT,'UTF-8');
			if ($_name) {
				foreach($meta->field as $meta_key=>$val){
					if ($val==$key){
						$arr=SpravStatic::getCKArray($meta->ck_reestr[$meta_key]);
						foreach($arr as $k=>$v){
							if($v==$_name){	$id=$k; }
						}
					} 
				}
				if($id===false){ $id=$defaults[$key]; }
			} else { $id=$defaults[$key]; }
		} else { $id=$defaults[$key]; }
		return $id;
	}
	// валюта 
	public function getCurrencyID($row, $fields, $defaults){
		if (isset($fields["g_currency"])){
			$_name=mb_substr($row[$fields["g_currency"]],0,64,DEF_CP);
			//$_name = htmlspecialchars($_name,ENT_COMPAT,'UTF-8');
			if ($_name) {
				$_id=$this->checkCurrency($_name);
				if(!$_id){ 	$id=$defaults["g_currency"];	}
				else { $id=$_id; }
			} else { $id=$defaults["g_currency"];	}
		} else { $id=$defaults["g_currency"];	}
		return $id;
	}
	public function checkCurrency($_name){
		$sql="SELECT c_id FROM #__currency WHERE c_name='".$_name."'";
		$this->_db->setQuery($sql);
		$this->log2file("Check currency: ".$this->_db->getQuery());
		return $this->_db->loadResult();
	}
	// единицы измерения
	public function getMeasureID($row, $key, $type, $fields, $defaults){
		$id=0;
		if (isset($fields[$key])){
			$_name=mb_substr($row[$fields[$key]],0,50,DEF_CP);
			//$_name = htmlspecialchars($_name,ENT_COMPAT,'UTF-8');
			if ($_name) {
				$_id=$this->checkMeasure($_name, $type);
				if(!$_id){
					if ($this->addMeasure($_name, $type)){
						$id=$this->_db->insertid(); //						$id=checkMeasure($_name, $type);
					} else { $id=$defaults[$key]; }
				} else { $id=$_id; }
			} else { $id=$defaults[$key]; }
		} else { $id=$defaults[$key]; }
		return $id;
	}
	public function checkMeasure($_name, $type){
		$sql="SELECT meas_id FROM #__measure WHERE meas_full_name='".$_name."' AND meas_type=".$type;
		$this->_db->setQuery($sql);
		$this->log2file("Check measure: ".$this->_db->getQuery());
		return (int)$this->_db->loadResult();
	}
	public function addMeasure($_name, $type){
		$sql="INSERT INTO #__measure (meas_id, meas_short_name, meas_full_name, meas_type) VALUES (NULL, '".$_name."', '".$_name."', ".$type.")";
		$this->_db->setQuery($sql);
		return $this->_db->query();
	}
	// налоги
	public function getTaxID($row, $fields, $defaults){
		$id=0;
		if (isset($fields["g_tax"])){
			$_name=mb_substr($row[$fields["g_tax"]],0,50,DEF_CP);
			//$_name = htmlspecialchars($_name,ENT_COMPAT,'UTF-8');
			if ($_name) {
				$_id=$this->checkTax($_name);
				if(!$_id){
					if ($this->addTax($_name)){
						$id=$this->_db->insertid(); //						$id=checkTax($_name);
					} else { $id=$defaults["g_tax"]; }
				} else { $id=$_id; }
			} else { $id=$defaults["g_tax"]; }
		} else { $id=$defaults["g_tax"]; }
		return $id;
	}
	public function checkTax($_name){
		$sql="SELECT t_id FROM #__taxes WHERE t_name='".$_name."'";
		$this->_db->setQuery($sql);
		$this->log2file("Check tax: ".$this->_db->getQuery());
		return (int)$this->_db->loadResult();
	}
	public function addTax($_name){
		$sql="INSERT INTO #__taxes(t_id, t_name) VALUES (NULL, '".$_name."')";
		$this->_db->setQuery($sql);
		return $this->_db->query();
	}
	// производители
	public function getVendorID($row, $fields, $defaults){
		$id=0;
		if (isset($fields["g_vendor"])){
			$_name=mb_substr($row[$fields["g_vendor"]],0,150,DEF_CP);
			//$_name = htmlspecialchars($_name,ENT_COMPAT,'UTF-8');
			if ($_name) {
				$sql="SELECT v_cat_id FROM #__vendors WHERE v_id=".$defaults["g_vendor"];
				$this->_db->setQuery($sql);
				$parent_id = (int)$this->_db->loadResult();
				$_id=$this->checkVendor($parent_id, $_name);
				if(!$_id){
					if ($this->addVendor($parent_id, $_name)){
						$id=$this->_db->insertid(); //						$id=checkVendor($_name);
					} else { 	$id=$defaults["g_vendor"]; 	}
				} else {	$id=$_id; 	}
			} else { 	$id=$defaults["g_vendor"]; 	}
		} else { 	$id=$defaults["g_vendor"]; 	}
		return $id;
	}
	public function checkVendor($parent_id, $_name){
		$sql="SELECT v_id FROM #__vendors WHERE v_cat_id=".$parent_id." AND v_name='".$_name."'";
		$this->_db->setQuery($sql);
		$this->log2file("Check vendor: ".$this->_db->getQuery());
		return (int)$this->_db->loadResult();
	}
	public function addVendor($parent_id, $_name){
		$sql="INSERT INTO #__vendors(v_id, v_cat_id, v_name, v_store_name	) VALUES (NULL, ".$parent_id.", '".$_name."', '".$_name."')";
		$this->_db->setQuery($sql);
		return $this->_db->query();
	}
	// поставщики
	public function getManufacturerID($row, $fields, $defaults){
		$id=0;
		if (isset($fields["g_manufacturer"])){
			$_name=mb_substr($row[$fields["g_manufacturer"]],0,150,DEF_CP);
			//$_name = htmlspecialchars($_name,ENT_COMPAT,'UTF-8');
			if ($_name) {
				$sql="SELECT mf_cat_id FROM #__manufacturers WHERE mf_id=".$defaults["g_manufacturer"];
				$this->_db->setQuery($sql);
				$parent_id = (int)$this->_db->loadResult();
				$_id=$this->checkManufacturer($parent_id, $_name);
				if(!$_id){
					if ($this->addManufacturer($parent_id, $_name)){
						$id=$this->_db->insertid(); //						$id=checkManufacturer($_name);
					} else { 	$id=$defaults["g_manufacturer"]; 	}
				} else {	$id=$_id; 	}
			} else { 	$id=$defaults["g_manufacturer"]; 	}
		} else { 	$id=$defaults["g_manufacturer"]; 	}
		return $id;
	}
	public function checkManufacturer($parent_id, $_name){
		$sql="SELECT mf_id FROM #__manufacturers WHERE mf_cat_id=".$parent_id." AND mf_name='".$_name."'";
		$this->_db->setQuery($sql);
		$this->log2file("Check manufacturer: ".$this->_db->getQuery());
		return (int)$this->_db->loadResult();
	}
	public function addManufacturer($parent_id, $_name){
		$sql="INSERT INTO #__manufacturers(mf_id, mf_cat_id, mf_name, mf_desc	) VALUES (NULL, ".$parent_id.", '".$_name."', '".$_name."')";
		$this->_db->setQuery($sql);
		return $this->_db->query();
	}
	// группы товаров
	public function getGroupID($row, $grp_arr, $gid=0){
		if(count($grp_arr)){
			foreach($grp_arr as $key=>$val){
				$ggr_name=mb_substr($row[$val],0,150,DEF_CP);
//				$ggr_name = htmlspecialchars($ggr_name,ENT_COMPAT,'UTF-8');
				if ($ggr_name){
					$_id=$this->checkGroup($gid, $ggr_name);
					if(!$_id){
						if ($this->addGroup($gid, $ggr_name)){
							$gid=$this->_db->insertid(); //						$gid=$this->checkGroup($gid, $ggr_name);
							$this->updateGroupAlias($gid, $ggr_name, $ggr_name);
						}
					} else {
						$gid=$_id;
					}
				}				
			}
		}
		return $gid;
	}
	public function checkGroup($parent_id, $ggr_name){
		$sql="SELECT ggr_id FROM #__goods_group WHERE ggr_id_parent=".$parent_id." AND ggr_name='".$ggr_name."'";
		$this->_db->setQuery($sql);
		$this->log2file("Check group: ".$this->_db->getQuery());
		return (int)$this->_db->loadResult();
	}
	public function addGroup($parent_id, $ggr_name){
		$sql="INSERT INTO `#__goods_group`(`ggr_id`,`ggr_id_parent`, `ggr_name`, `ggr_thumb`, `ggr_comment`, `ggr_meta_title`, `ggr_meta_description`, `ggr_meta_keywords`, `ggr_ordering`, `ggr_list_tmpl`, `ggr_enabled`, `ggr_deleted`	)
			VALUES(NULL, ".$parent_id.", '".$ggr_name."', '', '', '".$ggr_name."', '".$ggr_name."', '', 0, '', 1, 0)";
		$this->_db->setQuery($sql);
		return $this->_db->query();
	}
	// товары
	public function getGoodsID($sku){
		$sql="SELECT g_id FROM #__goods WHERE g_sku='".$sku."'";
		$this->_db->setQuery($sql);
		$this->log2file("Check goods by SKU: ".$this->_db->getQuery());
		return (int)$this->_db->loadResult();
	}
	public function updateAlias($psid, $alias, $name){
		if($alias) $alias=mb_substr(Translit::_($alias, DEF_CP, false), 0, 255);
		if (!$alias) $alias=mb_substr(Translit::_($name), 0, 255);
		if ($alias=="goods") $alias=mb_substr($psid."-".Translit::_($name), 0, 255);
		$sql="SELECT COUNT(*) FROM #__goods WHERE g_alias='".$alias."' AND g_id<>".$psid;
		$this->_db->setQuery($sql);
		if ($this->_db->loadResult()>0){
			$alias=mb_substr($psid."-".$alias, 0, 255);
		}
		$sql="UPDATE #__goods SET g_alias='".$alias."' WHERE g_id=".$psid;
		$this->_db->setQuery($sql);
		return $this->_db->query();
	}
	public function updateGroupAlias($psid, $alias, $name){
		if($alias) $alias=mb_substr(Translit::_($alias), 0,255);
		if (!$alias) $alias=mb_substr(Translit::_($name), 0,255);
		if ($alias=="goods") $alias=mb_substr(Translit::_($psid."_".$name), 0,255);
		$sql="SELECT COUNT(*) FROM #__goods_group WHERE ggr_alias='".$alias."' AND ggr_id<>".$psid;
		$this->_db->setQuery($sql);
		if ($this->_db->loadResult()>0){
			$alias=mb_substr($psid."_".$alias,0,255);
		}
		$sql="UPDATE #__goods_group SET ggr_alias='".$alias."' WHERE ggr_id=".$psid;
		$this->_db->setQuery($sql);
		return $this->_db->query();
	}
	public function updateAddFields($fields_update_list){
		if(count($fields_update_list)){
			foreach($fields_update_list as $key=>$val){
				if($val){
					$text_list="";
					$sql="SELECT DISTINCT field_value FROM #__goods_data WHERE field_name='".$key."' ORDER BY field_value";
					$this->_db->setQuery($sql);
					$field_values=$this->_db->loadObjectList();
					if(count($field_values)){
						foreach($field_values as $field_val){
							$text_list.=$field_val->field_value.";".CR_LF;
						}
						$sql="UPDATE #__fields_list SET	f_choices = '".trim($text_list)."' WHERE f_name='".$key."'";
						$this->_db->setQuery($sql);
						$this->_db->query();
					}
				}
			}
		}
	}
	
}
?>