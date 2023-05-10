<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class confModelfields extends Model {
	private $_hiddenfiles=array('.','.svn','resources','index.php','index.html','.htaccess','.htpasswd','web.config');
	public $conf_msg='';// внутримодельное сообщение для передачи информации наружу
	/* заготовка
	 public function rereadMetadata(){
	$mod_arr=Module::getInstalledModules(); asort($mod_arr);
	}
	*/
	/* получить список доступных модулей в зависимости от стороны */
	public function getModulesList($type=0) {
		$db=Database::getInstance();
		$sql="select distinct m_module from #__metadata where m_admin_side=".(int)$type." and m_module IN (select distinct m_name from #__modules) order by m_module";
		$db->setquery($sql);
		return $db->loadResultArray();
	}
	/* получить список доступных вьюх в зависимости от стороны и модулей*/
	public function getViewsList($type=0,$module="") {
		$db=Database::getInstance();
		$sql="select distinct m_view from #__metadata where m_admin_side=".(int)$type." and m_module='".$module."'";
		$db->setquery($sql);
		return $db->loadResultArray();
	}
	/* получить список доступных лайоутов в зависмости от стороны вьюхи и модуля */
	public function getLayoutsList($type=0,$module="",$view="") {
		$db=Database::getInstance();
		$sql="select distinct m_layout from #__metadata where m_admin_side=".(int)$type." and m_module='".$module."' and m_view='".$view."'";
		$db->setquery($sql);
		return $db->loadResultArray();
	}

	public function getLayoutInfo($type=0,$module="",$view="",$layout="") {
		$db=Database::getInstance();
		$sql="select * from #__metadata where m_admin_side=".(int)$type." and m_module='".$module."' and m_view='".$view."' and m_layout='".$layout."' order by m_id";
		$db->setquery($sql);
		return $db->loadObjectList("m_field");
	}

	public function saveVisioData() {
		$db=Database::getInstance();
		$m_id=Request::get("m_id");
		$m_field_order=Request::get("m_field_order");
		$m_show=Request::get("m_show");
		$m_width=Request::get("m_width");
		$m_input_view=Request::get("m_input_view");
		$m_input_size=Request::get("m_input_size");
		$m_input_page=Request::get("m_input_page");
		$m_show_in_filter=Request::get("m_show_in_filter");
		$m_show_in_filter_ext=Request::get("m_show_in_filter_ext");
		$m_strict_filter=Request::get("m_strict_filter");
		$m_translate_value=Request::get("m_translate_value");
		$sql="";
		foreach($m_id as $key=>$val) {
			$sql.="update #__metadata set ";
			if(isset($m_show[$key])) $sql.=" m_show=1,"; else $sql.=" m_show=0,";
			$sql.=" m_width='".$db->doubleBaks($m_width[$key])."',";
			if(isset($m_input_view[$key])) $sql.=" m_input_view=1,"; else $sql.=" m_input_view=0,";
			if(isset($m_input_size[$key])) $sql.=" m_input_size='".$db->doubleBaks($m_input_size[$key])."',";
			if(isset($m_input_page[$key])) $sql.=" m_input_page=".intval($m_input_page[$key]).","; else $sql.=" m_input_page=0,";
			if(isset($m_show_in_filter[$key])) $sql.=" m_show_in_filter=1,"; else $sql.=" m_show_in_filter=0,";
			if(isset($m_show_in_filter_ext[$key])) $sql.=" m_show_in_filter_ext=1,"; else $sql.=" m_show_in_filter_ext=0,";
			if(isset($m_strict_filter[$key])) $sql.=" m_strict_filter=1,"; else $sql.=" m_strict_filter=0,";
                        if(isset($m_translate_value[$key])) $sql.=" m_translate_value=1,"; else $sql.=" m_translate_value=0,";
			$sql.=" m_field_order='".intval($m_field_order[$key])."'";
			$sql.=" where m_id=".(int)$val.$this->_db->getDelimiter();
		}
		if(!empty($sql)) {
			$db->setQuery($sql);
			return $db->query_batch(true,true);
		}
		return false;
	}
	public function getMsg() {
		return $this->conf_msg;
	}
	public function getTablesByFields($arr_psid=array()) {
		$tables=array();
		if(count($arr_psid)){
			$sql="select distinct f_table from #__fields_list where f_id IN (".implode(",", $arr_psid).")";
			$this->_db->setquery($sql);
			return $this->_db->loadResultArray();
		}
		return array();
	}
	public function getModulesByTables($tables=array()) {
		$result=array();
		$site_paths=array(PATH_SITE, PATH_FRONT);
		$modules=Module::getInstalledModules();
		foreach($modules as $module_name){
			foreach($site_paths as $site_path){
				$meta_path = $site_path.'modules'.DS.$module_name.DS.'metadata';
				$metafiles = Files::getFiles($meta_path,$this->_hiddenfiles, false);
				if ($metafiles && count($metafiles)){
					foreach($metafiles as $metafile=>$metainfo) {
						if(isset($tablname)) unset($tablname);
						if(is_file($meta_path.DS.$metafile)){
							include $meta_path.DS.$metafile;
							if(in_array($tablname, $tables)) $result[$module_name]=true;
						}
					}
				}
			}
		}
		return $result;
	}
	public function saveFields($zone,$mod_name) {
		$this->conf_msg="";
		$db=Database::getInstance();
		if ($zone==1) {
			$site_path = PATH_SITE;
			$admin_side=1;
		} else {
			$site_path = PATH_FRONT;
			$admin_side=0;
		}
		$meta_path = $site_path.'modules'.DS.$mod_name.DS.'metadata';
		if(!is_dir($meta_path)) {
			$this->conf_msg=Text::_("Metadata folder absent for module")." ".$mod_name;
			return false; 
		}
		$data_meta=array();
		$inch=0;
		$presentFields=array();
		$fld_arrayDop=array();
		$fld_array=array();
		$metafiles = Files::getFiles($meta_path,$this->_hiddenfiles, false);
		if ($metafiles && count($metafiles)){
			foreach($metafiles as $metafile=>$metainfo) {
				unset($fld_arrayDop);
				unset($fld_array);
				unset($presentFields);
				unset($meta_index);
				$meta_params = explode('.', $metafile);
				if ((count($meta_params)==3)&&($meta_params[2]=="php")) {
					include $meta_path.DS.$metafile;
					$view=$meta_params[0];
					$layout=$meta_params[1];
					//список полей, которые есть в таблице
					$fld_array = $db->getListFieldfromTable($tablname);
					//Доп.поля для goods
					$query="SELECT f_name FROM #__fields_list WHERE f_deleted=0 AND f_table='$tablname'";
					$db->setQuery($query);
					$fld_arrayDop = $db->loadAssocList();
					if ($fld_array) {
						foreach ($fld_array as $key=>$val) $presentFields[$key]=$val->Field;
					} else {
						Debugger::getInstance()->warning("Table absent"); 
						return false;
					}
					if ($fld_arrayDop) {
						foreach ($fld_arrayDop as $key=>$val) $presentFields[$val['f_name']]=$val['f_name'];
					}
					unset($fld_arrayDop);
					unset($fld_array);
					//получили в $presentFields список полей, которые есть в таблице
					//создадим из метадаты массив, с key=имя поля,val=индекс поля
					foreach ($cur_table_arr['field'] as  $key=>$val){
						$meta_index[$val]=$key;
					}
					//создадим из  $meta_index и $presentFields массив всех полей/ таблицы metadata (ключи - поля метадаты, значения - значения полей)
					//сначала со стороны $presentFields
					foreach ($presentFields  as  $key=>$val){
						$data_meta['m_view'][$inch]=$view;
						$data_meta['m_field'][$inch]=$val;
						$data_meta['m_layout'][$inch]=$layout;
						if (isset($meta_index[$val])){
							$ind=$meta_index[$val];
							if (isset($cur_table_arr['view'][$ind])) {
								$data_meta['m_show'][$inch]=$cur_table_arr['view'][$ind];
								$data_meta['m_input_view'][$inch]=$cur_table_arr['view'][$ind];
							} else {
								$data_meta['m_show'][$inch]=0;
								$data_meta['m_input_view'][$inch]=1;
							}
							if (isset($cur_table_arr['size'][$ind])) {
								$data_meta['m_width'][$inch]=$cur_table_arr['size'][$ind];
							} else {// вот тут типизацию проверим
								$data_meta['m_width'][$inch]='';
							}
							if (isset($cur_table_arr['input_size'][$ind])) {
								$data_meta['m_input_size'][$inch]=$cur_table_arr['input_size'][$ind];
							} else {
								if(isset($cur_table_arr['input_type'][$ind])&&$cur_table_arr['input_type'][$ind]!='select') $data_meta['m_input_size'][$inch]=0;
								else $data_meta['m_input_size'][$inch]=1;
							}
							$data_meta['m_show_in_filter'][$inch]=0;
							$data_meta['m_show_in_filter_ext'][$inch]=0;
							$data_meta['m_strict_filter'][$inch]=0;
							$data_meta['m_input_page'][$inch]=0;
						} else {
							$data_meta['m_show'][$inch]=0;
							$data_meta['m_width'][$inch]='';
							$data_meta['m_input_view'][$inch]=1;
							$data_meta['m_input_size'][$inch]=0;
							$data_meta['m_input_page'][$inch]=0;
							$data_meta['m_show_in_filter'][$inch]=0;
							$data_meta['m_show_in_filter_ext'][$inch]=0;
							$data_meta['m_strict_filter'][$inch]=0;
						}
						$inch++;
					}
					//теперь со стороны $cur_table_arr
					foreach ($meta_index as $key=>$val){
						if (!isset($presentFields[$key])){
							$data_meta['m_view'][$inch]=$view;
							$data_meta['m_field'][$inch]=$key;
							$data_meta['m_layout'][$inch]=$layout;
							$ind=$meta_index[$key];
							if (isset($cur_table_arr['view'][$ind])) {
								$data_meta['m_show'][$inch]=$cur_table_arr['view'][$ind];
								$data_meta['m_input_view'][$inch]=$cur_table_arr['view'][$ind];
							} else {
								$data_meta['m_show'][$inch]=0;
								$data_meta['m_input_view'][$inch]=1;
							}
							if (isset($cur_table_arr['size'][$ind])) {
								$data_meta['m_width'][$inch]=$cur_table_arr['size'][$ind];
							} else {
								$data_meta['m_width'][$inch]='';
							}

							if (isset($cur_table_arr['input_size'][$ind])) {
								$data_meta['m_input_size'][$inch]=$cur_table_arr['input_size'][$ind];
							}
							else {
								if(isset($cur_table_arr['input_type'][$ind])&&$cur_table_arr['input_type'][$ind]!='select') $data_meta['m_input_size'][$inch]=0;
								else $data_meta['m_input_size'][$inch]=1;
							}
							$data_meta['m_show_in_filter'][$inch]=0;
							$data_meta['m_show_in_filter_ext'][$inch]=0;
							$data_meta['m_strict_filter'][$inch]=0;
							$data_meta['m_input_page'][$inch]=0;
							$inch++;
						}
					}
				}
			}
		}
		$sql="UPDATE #__metadata SET m_field_loaded=0 WHERE m_module='".$mod_name."' AND m_admin_side=".$admin_side;
		$this->_db->setQuery($sql);
		$this->_db->query();
		$insert_sql="";
		$m_field_order=9999;
		for ($i=0; $i<$inch; $i++ ){
			$m_field_order++;
			$insert_sql.=" INSERT INTO #__metadata
						(`m_module`,`m_view`,`m_field`,`m_layout`,`m_show`,`m_width`,`m_input_view`,`m_input_size`,`m_input_page`,`m_show_in_filter`,`m_show_in_filter_ext`, `m_strict_filter`,`m_admin_side`,`m_field_loaded`, `m_field_order`)
						VALUES
						('".$mod_name."','".$data_meta['m_view'][$i]."','".$data_meta['m_field'][$i]."','".$data_meta['m_layout'][$i]."',".$data_meta['m_show'][$i].",'".$data_meta['m_width'][$i]."',".$data_meta['m_input_view'][$i].",'".$data_meta['m_input_size'][$i]."',".$data_meta['m_input_page'][$i].",".$data_meta['m_show_in_filter'][$i].",".$data_meta['m_show_in_filter_ext'][$i].",".$data_meta['m_strict_filter'][$i].",".$admin_side.", 1,".$m_field_order.")
						ON DUPLICATE KEY UPDATE m_field_loaded=1".$this->_db->getDelimiter();
		}
		$this->_db->setQuery($insert_sql);
		$res= $this->_db->query_batch(true,true);
		if ($res) {
			$query="DELETE FROM #__metadata WHERE m_module='".$mod_name."' AND m_admin_side=".$admin_side." AND m_field_loaded=0";
			$this->_db->setQuery($query);
			$res=$this->_db->query();
		}
		return $res;
	}
	public function isField($val,$psid){
		$db = Database::getInstance();
		$query = "SELECT COUNT(*) FROM #__fields_list WHERE f_name='".strval($val)."'";
		if ($psid) $query.=" AND f_id<>'".$psid."'";
		$db->setQuery($query);
		$cnt = $db->loadResult();
		return (intval($cnt) != 0);
	}
}
?>