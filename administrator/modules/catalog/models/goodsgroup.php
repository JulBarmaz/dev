<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogModelgoodsgroup extends SpravModel {
	

	public function getGroupFields($psid){
		$sql = "SELECT f.*,fg.*, ggf.parent_id  FROM #__fields_list AS f";
		$sql.=" LEFT JOIN #__goods_group_fields AS ggf ON ggf.f_id=f.f_id AND ggf.parent_id=".$psid;
		$sql.=" LEFT JOIN #__fields_groups AS fg ON fg.fg_id=f.f_group";
		$sql.=" WHERE f.f_custom=1";
		$sql.=" ORDER BY fg.fg_name, f.f_descr";
		$this->_db->setQuery($sql); 
		return $this->_db->loadObjectList();
	}
	public function saveGroupFields($psid,$fields){
		$sql="DELETE FROM #__goods_group_fields WHERE parent_id=".$psid.$this->_db->getDelimiter();
		if (count($fields)){
			foreach($fields as $fld){
				$sql.="INSERT INTO  #__goods_group_fields VALUES (".$fld.",".$psid.",0)".$this->_db->getDelimiter();
			}
		}			
		$this->_db->setQuery($sql);
		return $this->_db->query_batch(true,true);
	}
	public function cleanLinkFromGroup(){
		// удаление ссылок
		$query = "DELETE FROM #__goods_links WHERE parent_id NOT IN (SELECT ggr_id FROM #__goods_group)".$this->_db->getDelimiter();
		$query.= "DELETE FROM #__goods_group_data WHERE obj_id NOT IN (SELECT ggr_id FROM #__goods_group)".$this->_db->getDelimiter();
		$query.= "DELETE FROM #__goods_group_fields WHERE parent_id NOT IN (SELECT ggr_id FROM #__goods_group)".$this->_db->getDelimiter();
		$query.= "UPDATE `#__goods` SET `#__goods`.`g_main_grp` =";
		$query.= " (SELECT `#__goods_links`.`parent_id` FROM `#__goods_links` WHERE `#__goods_links`.`g_id` = `#__goods`.`g_id` LIMIT 1)";
		$query.= " WHERE g_main_grp NOT IN (SELECT ggr_id FROM #__goods_group)".$this->_db->getDelimiter();
		$this->_db->setQuery($query); 
		if(!$this->_db->query_batch(true,true)) return false;

		$query = "UPDATE #__goods SET g_main_grp=0 WHERE g_main_grp>0 AND g_main_grp NOT IN (SELECT ggr_id FROM #__goods_group)";
		$this->_db->setQuery($query);
		if(!$this->_db->query()) return false;

		return true;
	}
	public function cleanRecords()	{
		if(parent::cleanRecords()) return $this->cleanLinkFromGroup();	else return false;
	}
	public function save(){
		$psid=parent::save(); 
		if ($psid) { 
			if (catalogConfig::$ggr_thumb_AutoResize && catalogConfig::$ggr_thumb_width && catalogConfig::$ggr_thumb_height && $_FILES["ggr_thumb"]["name"]) {
				$res=$this->getElement($psid);
				if (!$res) return 0;
				$filename=Files::splitAppendix($res->ggr_thumb, true);
				if ($filename) {
					$_src=BARMAZ_UF_PATH."catalog".DS."ggr".DS.$filename;
					$_dest=$_src;
					if(Files::isImage($_src)) {
						$res=Files::resizeImage($_src, $_dest, catalogConfig::$ggr_thumb_width, catalogConfig::$ggr_thumb_height);
					 	if (!$res) return 0;
					}
				} else return 0;
			}
			$sql="UPDATE #__goods_group SET ggr_change_date=NOW(), ggr_change_uid=".User::getInstance()->getID()." WHERE ggr_id=".$psid;
			$this->_db->setQuery($sql);
			$this->_db->query();
			
		} else return 0;
		return $psid;
	}
}
?>