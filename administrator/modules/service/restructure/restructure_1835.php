<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Restructure_1835{
	private static $field="field_value";
	private static $debug=false;
	public static function proceed(&$messages, &$errors) {
		// Let's make something with database
		$tables = SpravStatic::getCKArray("extendable_tables");
		if(self::prepareDatabase($messages, $errors, $tables)){
			$fields_choices =  self::getFieldsChoices($messages, $errors, $tables);
			if($fields_choices===false) return false;
			$update_data_sql_arr = self::prepareData($tables, $fields_choices);
			if(count($update_data_sql_arr)){
				$update_data_sql = implode("", $update_data_sql_arr);
//				Util::pre($update_data_sql_arr); Util::pre($update_data_sql); return true;
				Database::getInstance()->setQuery($update_data_sql);
				if(!Database::getInstance()->query_batch()){
					$errors[]=Text::_("Error applying restructure")." (x01): ".__CLASS__.": ".__FUNCTION__;
					$errors[]=Database::getInstance()->getLastError()." (x01): ".__CLASS__.": ".__FUNCTION__;
					return false;
				}
				if(!self::$debug) {
					$sql_drop_old="ALTER TABLE `#__fields_list` DROP COLUMN `f_choices`".Database::getInstance()->getDelimiter();
					Database::getInstance()->setQuery($sql_drop_old);
					if(!Database::getInstance()->query()){
						$errors[]=Text::_("Error applying restructure")." (x02): ".__CLASS__.": ".__FUNCTION__;
						$errors[]=Database::getInstance()->getLastError()." (x02): ".__CLASS__.": ".__FUNCTION__;
						return false;
					}
				}
			}
		}
		// Returning message if successed
		$messages[]=__CLASS__.": ".__FUNCTION__;
		return true;
	}
	private static function prepareDatabase(&$messages, &$errors, $tables){
		$main_sql = "";
		$main_sql.= "DELETE FROM `#__fields_choices`".Database::getInstance()->getDelimiter();
		foreach ($tables as $table) {
			if(self::$debug) {
				self::$field="df_temp";
				$main_sql.= "ALTER TABLE `#__".$table."_data` DROP COLUMN `df_temp`".Database::getInstance()->getDelimiter();
				$main_sql.= "ALTER TABLE `#__".$table."_data` ADD COLUMN `df_temp` TEXT NOT NULL".Database::getInstance()->getDelimiter();
			}
		}
		Database::getInstance()->setQuery($main_sql);
		if(!Database::getInstance()->query_batch()){
			// Returning error if failed
			$errors[]=Text::_("Error applying restructure")." (x03): ".__CLASS__.": ".__FUNCTION__;
			$errors[]=Database::getInstance()->getLastError()." (x03): ".__CLASS__.": ".__FUNCTION__;
			return false;
		}
		return true;
	}
	private static function getFieldsChoices(&$messages, &$errors, $tables){
		$fc_ordering=0;
		$fields_choices=array();
		foreach ($tables as $table) {
			$fields_list_sql="SELECT * FROM `#__fields_list` WHERE f_table='".$table."' AND `f_type` IN (SELECT `t_id` FROM `#__fields_type` WHERE `t_input_type`='select' OR `t_input_type`='multiselect')".Database::getInstance()->getDelimiter();
			Database::getInstance()->setQuery($fields_list_sql);
//			Util::pre(Database::getInstance()->getQuery());
			$fields_list=Database::getInstance()->loadObjectList("f_id");
//			Util::pre($table);
//			Util::pre($fields_list);
			if(count($fields_list)){
				foreach($fields_list as $f){
					$fields_choices[$f->f_name]=array();
//					Util::pre(htmlspecialchars_decode($f->f_choices, ENT_QUOTES));
					$current_field_vals=array_unique(explode(";", trim(htmlspecialchars_decode($f->f_choices, ENT_QUOTES), ";")));
					if(count($current_field_vals)){
						foreach($current_field_vals as $cf_key=>$cf_val){
							$cf_val = htmlspecialchars($cf_val, ENT_QUOTES, 'UTF-8');
							$fc_ordering++;
							$insert_choice_sql = "INSERT INTO `#__fields_choices` (`fc_id`, `fc_field_id`, `fc_value`, `fc_ordering`, `fc_enabled`, `fc_deleted` )";
							$insert_choice_sql.= " VALUES (NULL, '".$f->f_id."', '".$cf_val."', ".$fc_ordering.", 1, 0)".Database::getInstance()->getDelimiter();
							Database::getInstance()->setQuery($insert_choice_sql);
							if(!Database::getInstance()->query()){
								// Returning error if failed
								$errors[]=Text::_("Error applying restructure")." (x04): ".__CLASS__.": ".__FUNCTION__;
								$errors[]=Database::getInstance()->getLastError()." (x04): ".__CLASS__.": ".__FUNCTION__;
								return false;
							}
							$choice_id=Database::getInstance()->insertid();
							if(!$choice_id){
								// Returning error if failed
								$errors[]=Text::_("Error applying restructure")." (x05): ".__CLASS__.": ".__FUNCTION__;
								$errors[]=Database::getInstance()->getLastError()." (x05): ".__CLASS__.": ".__FUNCTION__;
								return false;
							}
							$fields_choices[$f->f_name][$choice_id]=$cf_val;
						}
					}
				}
			}
		}
		return $fields_choices;
	}
	private static function prepareData($tables, $fields_choices){
//		Util::pre($fields_choices);
		$update_data_sql_arr=array();
		if(count($fields_choices)) {
			foreach ($tables as $table) {
				$fields_list_sql="SELECT * FROM `#__fields_list` WHERE f_table='".$table."' AND `f_type` IN (SELECT `t_id` FROM `#__fields_type` WHERE `t_input_type`='select' OR `t_input_type`='multiselect')".Database::getInstance()->getDelimiter();
				Database::getInstance()->setQuery($fields_list_sql);
//				Util::pre(Database::getInstance()->getQuery());
				$fields_list=Database::getInstance()->loadObjectList("f_id");
//				Util::pre($table);
//				Util::pre($fields_list);
				if(count($fields_list)){
					foreach($fields_list as $fld){
						$data_sql="SELECT * FROM #__".$table."_data WHERE `field_id`='".$fld->f_id."' AND `field_name`='".$fld->f_name."'".Database::getInstance()->getDelimiter();
						Database::getInstance()->setQuery($data_sql);
						$data=Database::getInstance()->loadObjectList();
						if(count($data)){
							foreach($data as $row){
								if($row->field_value){
									$source_val_array = array_unique(explode(";", trim(htmlspecialchars_decode($row->field_value, ENT_QUOTES), ";")));
									$new_val_array=self::transpose($source_val_array, $fields_choices[$fld->f_name]);
									if(count($new_val_array)){
										$new_val = implode(";", $new_val_array);
										if($fld->f_type==8) $new_val = ";".$new_val.";";
										$update_data_sql_arr[]="UPDATE #__".$table."_data SET `".self::$field."`='".$new_val."' WHERE `obj_id`='".$row->obj_id."' AND `field_id`='".$row->field_id."'".Database::getInstance()->getDelimiter();
									}
								}
							}
						}
					}
				}
			}
		}
		return $update_data_sql_arr;
	}
	private static function transpose($source_val_array, $fields_choices){
		$new_val_array=array();
		if(count($source_val_array)){
//			Util::pre($fields_choices); Util::pre($source_val_array);
			foreach ($source_val_array as $sv_key=>$sv_val){
				$sv_val = htmlspecialchars($sv_val, ENT_QUOTES, 'UTF-8');
				$key=array_search($sv_val, $fields_choices);
				if($key) {
					$new_val_array[]=$key;
//					Util::pre($key);
				}
			}
		}
		return $new_val_array;
	}
}
?>