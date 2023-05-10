<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogModelexport extends Model {
	public function proceedExport($hidden_fields){
		$hlp=Module::getHelper("groupsTree");
		$grp_arr=array();
		$_levels=$this->revertGroupsArray($grp_arr,$hlp->getTreeArr());
		$grp=Request::getSafe("ggr");
		if (is_array($grp)) $grp_id=implode(",",$grp);
		else $grp_id=0; 
		$meta = new SpravMetadata("catalog","goods");
		$this->_db->setQuery("SET SQL_BIG_SELECTS = 1;");
		$this->_db->query();
		$sql="SELECT c.g_id"; $join=""; $where="";
		$counter=0; 
		for($a=1;$a<=$_levels;$a++)	$headers["group_".$a]="group_".$a; 
		$headers["g_id"]="g_id"; 
		$rows=array();
		foreach($meta->field as $key=>$val){
			if (in_array($val,$hidden_fields)) continue;
			if (Request::getInt($val)){
				$headers[$val]=$val;
				if($meta->is_add[$key]) {
					$counter++;
					$sql.=", c".$counter.".field_value AS ".$val;
					$join.="\n LEFT JOIN #__goods_data AS c".$counter." ON c".$counter.".obj_id=c.g_id AND c".$counter.".field_id=".$meta->is_add[$key];
				} elseif($meta->ch_table[$key]) {
					$counter++;
					$sql.=", c".$counter.".".$meta->ch_field[$key]." AS ".$val;
					$join.="\n LEFT JOIN #__".$meta->ch_table[$key]." AS c".$counter." ON c".$counter.".".$meta->ch_id[$key]."=c.".$val;
				}	else {
					$sql.=",c.".$val;
				}
			}
		}
		$sql.=", ggr.parent_id as ggr_id";
		$join.="\n LEFT JOIN #__goods_links as ggr ON ggr.g_id=c.g_id";
		$where="\n WHERE c.g_deleted=0 AND c.g_id IN(SELECT g_id FROM #__goods_links WHERE parent_id IN (".$grp_id."))";
		$sql.="\n FROM #__goods AS c ".$join.$where;
		$this->_db->setQuery($sql);
		$rows=$this->_db->loadAssocList("g_id");
		foreach($rows as $ind=>$row){ 
			$_level=0;
			$tree_arr=array(); $index=$_levels; $_row=array();
			$this->getGroupTreeUp($row["ggr_id"],$grp_arr,$tree_arr,$index);
			ksort($tree_arr);
			if (count($tree_arr)){
				foreach($tree_arr as $cur_group){
					$_level++; $_row["ggr".$_level]=$cur_group;
				}
			}
			if($_level<$_levels){
				for($a=$_level;$a<$_levels;$a++) $_row["ggr".($a+1)]="";
			}
			$_row["g_id"]=$row["g_id"]; 
			foreach($meta->field as $i=>$field){
				if (in_array($field, $hidden_fields)) continue;
				if (Request::getInt($field)){
					$code=$rows[$ind][$field];
					if($meta->ck_reestr[$i]) {
						if (is_array($meta->ck_reestr[$i])) $key_arr=$meta->ck_reestr[$i];
						else $key_arr=SpravStatic::getCKArray($meta->ck_reestr[$i]);
						if(is_array($key_arr) && $meta->input_type[$i] == "multiselect"){
							$code = explode(";", trim($code, ";"));
							$ms_code = array();
							if(is_array($code)){
								foreach($code as $ms_key=>$ms_val){
									if(array_key_exists($ms_val, $key_arr)){
										$ms_code[]=htmlspecialchars_decode($key_arr[$ms_val],ENT_QUOTES);
									}
								}
								$_row[$field] = implode(", ", $ms_code);
							} else $_row[$field]="";
						} elseif(is_array($key_arr) && array_key_exists($code, $key_arr)) {
							$_row[$field]=htmlspecialchars_decode($key_arr[$code],ENT_QUOTES);
						} else {
							$_row[$field]="";
						}
					} else {
						$_row[$field]=htmlspecialchars_decode($rows[$ind][$field],ENT_QUOTES);
					}
				}
			}
			$rows[$ind]=$_row;
		}
		$filename="export_".time().".csv";
		$txt=BaseCSV::buildCSV($headers, $rows);
		if (file_put_contents(PATH_TMP.$filename,$txt)){
			$in='UTF-8';
			//$out='windows-1251';
			$out='windows-1251//IGNORE';
			Files::file_iconv(PATH_TMP.$filename, $in, $out);
			return $filename;
		}	else return false;
	}
	public function getFields(){
		$meta=new SpravMetadata("catalog","goods","default");
		foreach($meta->field as $key=>$val){
			$res[$val]=$meta->field_title[$key];
		}
		return $res;
	}
	public function revertGroupsArray(&$arr,$src){
		$result=0;
		foreach($src as $key=>$val){
			$arr[$val->id]["parent_id"]=$val->parent_id;
			$arr[$val->id]["title"]=$val->title;
			$arr[$val->id]["level"]=$val->level;
			if ($val->level>$result) $result=$val->level;
		}
		return $result;
	}
	public function getGroupTreeUp($ind,$grp_arr,&$tree_arr,&$level){
		if (array_key_exists($ind,$grp_arr)){
			$level--;
			$tree_arr[$level]=$grp_arr[$ind]["title"];
			if ($grp_arr[$ind]["parent_id"]) $this->getGroupTreeUp($grp_arr[$ind]["parent_id"],$grp_arr,$tree_arr,$level);
		}
	}
}
?>