<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogModelgoods extends SpravModel {

	private $arr_table_clean=array();

	public function cleanLinkFromGoods(){
		// удаление ссылок
		$query = "DELETE FROM #__goods_links WHERE g_id NOT IN (SELECT g_id FROM #__goods)".$this->_db->getDelimiter();
		$query.= "DELETE FROM #__goods_data WHERE obj_id NOT IN (SELECT g_id FROM #__goods)".$this->_db->getDelimiter();
		$query.= "DELETE FROM #__goods_discounts WHERE g_id NOT IN (SELECT g_id FROM #__goods)".$this->_db->getDelimiter();
		$query.= "DELETE FROM #__goods_analogs WHERE g_id NOT IN (SELECT g_id FROM #__goods)".$this->_db->getDelimiter();
		$query.= "DELETE FROM #__goods_analogs WHERE a_id NOT IN (SELECT g_id FROM #__goods)".$this->_db->getDelimiter();
		$query.= "DELETE FROM #__goods_sets WHERE g_id NOT IN (SELECT g_id FROM #__goods)".$this->_db->getDelimiter();
		$query.= "DELETE FROM #__goods_prices WHERE p_g_id NOT IN (SELECT g_id FROM #__goods)".$this->_db->getDelimiter();
		$query.= "DELETE FROM #__goods_stat WHERE gs_goods_id NOT IN (SELECT g_id FROM #__goods)".$this->_db->getDelimiter();
		$query.= "DELETE FROM #__goods_feedbacks WHERE gf_goods_id NOT IN (SELECT g_id FROM #__goods)".$this->_db->getDelimiter();
		$query.= "DELETE FROM #__goods_additionals WHERE g_id NOT IN (SELECT g_id FROM #__goods)".$this->_db->getDelimiter();
		$query.= "DELETE FROM #__goods_additionals WHERE ad_id NOT IN (SELECT g_id FROM #__goods)".$this->_db->getDelimiter();
		$query.= "DELETE FROM #__goods_options_data WHERE od_obj_id NOT IN (SELECT g_id FROM #__goods)".$this->_db->getDelimiter();
		$query.= "DELETE FROM #__goods_opt_vals_data WHERE ovd_od_id NOT IN (SELECT od_id FROM #__goods_options_data)".$this->_db->getDelimiter();
		$query.= "DELETE FROM #__goods_videos WHERE v_gid NOT IN (SELECT g_id FROM #__goods)".$this->_db->getDelimiter();
		$this->_db->setQuery($query); if(!$this->_db->query_batch(true,true)) return false;
		return true;
	}
	public function cleanRecords()	{
		// помечаем дочерние таблицвы
		$this->arr_table_clean['m_table']="goods";
		$this->arr_table_clean['m_key']="g_id";
		$this->arr_table_clean['m_deleted']="g_deleted";
		$this->arr_table_clean['ch_table']="goods_img";
		$this->arr_table_clean['ch_deleted']="i_deleted";
		$this->arr_table_clean['ch_key']="i_gid";
		parent::markDeleteTrashChildsTable($this->arr_table_clean);
		$this->arr_table_clean['ch_table']="goods_videos";
		$this->arr_table_clean['ch_deleted']="v_deleted";
		$this->arr_table_clean['ch_key']="v_gid";
		parent::markDeleteTrashChildsTable($this->arr_table_clean);
		if(parent::cleanRecords()) return $this->cleanLinkFromGoods();	else return false;
	}
	public function afterSaveLinks($current_group,$group_array,$arr_psid) {
		if(is_array($group_array) && in_array($current_group, $group_array)){
			return true;
		} elseif(!is_array($group_array) || (is_array($group_array) && !in_array($current_group,$group_array)))	{
			$sql = "UPDATE `#__goods` SET `#__goods`.`g_main_grp` =";
			$sql.= " (SELECT `#__goods_links`.`parent_id` FROM `#__goods_links` WHERE `#__goods_links`.`g_id` = `#__goods`.`g_id` LIMIT 1)";
			$sql.= " WHERE `#__goods`.`g_main_grp`=".$current_group;
			$sql.= " AND `#__goods`.`g_id` IN (".implode(",", $arr_psid).")";
			$this->_db->setQuery($sql);
			if($this->_db->query()){
				$sql = "UPDATE `#__goods` SET `#__goods`.`g_main_grp` = 0";
				$sql.= " WHERE `#__goods`.`g_main_grp`=".$current_group;
				$sql.= " AND `#__goods`.`g_id` IN (".implode(",", $arr_psid).")";
				$this->_db->setQuery($sql);
				return $this->_db->query();
			}
		}
		return false;
	}
	public function getGoods($shortdata=0,$ids=array()) {
		if($shortdata) $query = "SELECT g_id as id, g_name as title FROM `#__goods` WHERE `g_deleted`=0";
		else $query = "SELECT * FROM `#__goods` WHERE `g_deleted`=0";
		if(count($ids)>0) $query.=" AND g_id IN ('".implode("','",$ids)."')";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	public function updateThumb($psid,$thumb){
		$sql="UPDATE #__goods SET g_thumb='".$thumb."' WHERE g_id='".$psid."'";
		$this->_db->setQuery($sql);
		return $this->_db->query();
	}

	public function updateMediumImage($psid,$medium_image){
		$sql="UPDATE #__goods SET g_medium_image='".$medium_image."' WHERE g_id='".$psid."'";
		$this->_db->setQuery($sql);
		return $this->_db->query();
	}
	public function save()	{
		$mdl = Module::getInstance();
		$reestr = $mdl->get('reestr');
		$psid = $reestr->get('psid');
		$generate_sku_automaticly = Module::getInstance("catalog")->getParam("generate_sku_automaticly");
		if($generate_sku_automaticly){
			$sku=Request::getSafe("g_sku","EMPTY_VALUE");
		} else {
			$sku=Request::getSafe("g_sku","");
			if(!$sku) return false;
		}
		if (!$this->isGoods($sku,$psid)) {
			$new_psid=parent::save();
			if ($new_psid) {
				if (catalogConfig::$thumbAutoCreate && catalogConfig::$thumb_width && catalogConfig::$thumb_height && $_FILES["g_image"]["name"] && !$_FILES["g_thumb"]["name"]) {
					$res=$this->getElement($new_psid);
					if (!$res) return false;
					$_dest=BARMAZ_UF_PATH."catalog".DS."i".DS."thumbs".DS.Files::splitAppendix($res->g_thumb, true);
					if ((!$res->g_thumb)||(!file_exists($_dest))) {
						$filename=Files::splitAppendix($res->g_image, true);
						if ($filename) {
							Files::checkFolder(BARMAZ_UF_PATH."catalog".DS."i".DS."thumbs".DS.Files::getAppendix($filename), true);
							$_src=BARMAZ_UF_PATH."catalog".DS."i".DS."fullsize".DS.$filename;
							$_dest=BARMAZ_UF_PATH."catalog".DS."i".DS."thumbs".DS.$filename;
							if(Files::isImage($_src))	{
								$result=Files::resizeImage($_src, $_dest, catalogConfig::$thumb_width, catalogConfig::$thumb_height);
								if (!$result) return false;
								if (!$res->g_thumb) $result=$this->updateThumb($new_psid,$res->g_image);
								if (!$result) return false;
							}
						} else return false;
					}
				} elseif (catalogConfig::$thumbAutoResize && catalogConfig::$thumb_width && catalogConfig::$thumb_height && $_FILES["g_thumb"]["name"]) {
					$res=$this->getElement($new_psid);
					if (!$res) return false;
					$_dest=BARMAZ_UF_PATH."catalog".DS."i".DS."thumbs".DS.Files::splitAppendix($res->g_thumb, true);
					if ($res->g_thumb && is_file($_dest)) {
						$_src=$_dest;
						if(Files::isImage($_src))	{
							if (!Files::resizeImage($_src, $_dest, catalogConfig::$thumb_width, catalogConfig::$thumb_height)) return false;
						}
					}
					
				}
				if (catalogConfig::$mediumImgAutoCreate && $_FILES["g_image"]["name"] && catalogConfig::$mediumImgWidth && catalogConfig::$mediumImgHeight && !$_FILES["g_medium_image"]["name"]) {
					$res=$this->getElement($new_psid);
					if (!$res) return false;
					$_dest=BARMAZ_UF_PATH."catalog".DS."i".DS."medium".DS.Files::splitAppendix($res->g_medium_image, true);
					if ((!$res->g_medium_image)||(!file_exists($_dest))) {
						$filename=Files::splitAppendix($res->g_image, true);
						if ($filename) {
							Files::checkFolder(BARMAZ_UF_PATH."catalog".DS."i".DS."medium".DS.Files::getAppendix($filename), true);
							$_src=BARMAZ_UF_PATH."catalog".DS."i".DS."fullsize".DS.$filename;
							$_dest=BARMAZ_UF_PATH."catalog".DS."i".DS."medium".DS.$filename;
							if(Files::isImage($_src)) {
								$result=Files::resizeImage($_src, $_dest, catalogConfig::$mediumImgWidth, catalogConfig::$mediumImgHeight);
								if (!$result) return false;
								if (!$res->g_medium_image) $result=$this->updateMediumImage($new_psid,$res->g_image);
								if (!$result) return false;
							}
						} else return false;
					}
				} elseif (catalogConfig::$mediumImgAutoResize && catalogConfig::$mediumImgWidth && catalogConfig::$mediumImgHeight && $_FILES["g_medium_image"]["name"]) {
					$res=$this->getElement($new_psid);
					if (!$res) return false;
					$_dest=BARMAZ_UF_PATH."catalog".DS."i".DS."medium".DS.Files::splitAppendix($res->g_medium_image, true);
					if ($res->g_medium_image && file_exists($_dest)) {
						$_src=$_dest;
						if(Files::isImage($_src))	{
							if (!Files::resizeImage($_src, $_dest, catalogConfig::$mediumImgWidth, catalogConfig::$mediumImgHeight)) return false;
						}
					}
				}
				$sql="UPDATE #__goods SET g_change_date=NOW(), g_change_uid=".User::getInstance()->getID()." WHERE g_id=".$new_psid;
				$this->_db->setQuery($sql);
				$this->_db->query();
			}
		} else return false;
		if($generate_sku_automaticly && $sku=="EMPTY_VALUE") $this->updateSKU($new_psid);
		return $new_psid;
	}

	public function getGroupsFields($linkArray){
		if (!is_array($linkArray) || !count($linkArray)) return array();
		$sql = "SELECT f.*, ggf.parent_id  FROM #__fields_list AS f";
		$sql.=" LEFT JOIN #__goods_group_fields AS ggf ON ggf.f_id=f.f_id";
		$sql.=" WHERE f.f_custom=1 AND f.f_table='goods'";
		$sql.=" AND ggf.parent_id IN ('".implode("','",$linkArray)."') ";
		$sql.=" ORDER BY f.f_descr";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList("f_id");
	}
	public function getCommonFields(){
		$sql = "SELECT f.*  FROM #__fields_list AS f";
		$sql.=" WHERE f.f_custom=0 AND f.f_table='goods'";
		$sql.=" ORDER BY f.f_descr";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList("f_id");
	}
	
	public function cleanNonGroupsFields($psid,$linkArray){
		$fcList=$this->getCommonFields();
		$fbgList=$this->getGroupsFields($linkArray);
		if (is_array($fbgList) && count($fbgList)){
			$fbgList_IDS=array_keys($fbgList);
			if (is_array($fcList) && count($fcList)){
				$fcList_IDS = array_keys($fcList);
				$fList_IDS = array_merge($fbgList_IDS, $fcList_IDS);
			} else $fList_IDS=$fbgList_IDS;
			$sql = "DELETE FROM #__goods_data WHERE obj_id=".$psid." AND field_id NOT IN(".implode(",",$fList_IDS).")";
			$this->_db->setQuery($sql);
			$this->_db->query();
		}
	}
	public function getElementClone($psid=0,$fillempty=false) {
		$row = $this->getElement($psid, $fillempty);
		if (is_object($row)){
			$meta = $this->meta;
			foreach($meta->field as $ind=>$field)	{
				if ($field == $meta->keystring) $row->{$field}=0;
				if($meta->input_type[$ind]=="image" || $meta->input_type[$ind]=="file" || $field =="g_sku") $row->{$field}="";
			}
		}
		return $row;
	}
	// May be for remove ??? Check first !!! Currently used only in save()
	public function isGoods($val,$psid){
		$query = "SELECT COUNT(*) FROM #__goods WHERE g_sku='".strval($val)."'";
		if ($psid) $query.=" AND g_id<>'".$psid."'";
		$this->_db->setQuery($query);
		$cnt = $this->_db->loadResult();
		return (intval($cnt) != 0);
	}
	public function getAnalogs($psid){
		$sql="SELECT DISTINCT a.a_id AS id, CONCAT(g.g_sku,' : ', g.g_name) AS title FROM #__goods_analogs AS a,#__goods AS g WHERE g.g_id=a.a_id AND a.g_id=".$psid;
		$this->_db->setQuery($sql);
		return $this->_db->loadAssocList();
	}
	
	public function saveAnalogs($psid,$analogs,$reverse=true){
		if($reverse){
			$sql="DELETE FROM #__goods_analogs WHERE g_id=".$psid." OR a_id=".$psid.$this->_db->getDelimiter();
		}
		else
		{
			$sql="DELETE FROM #__goods_analogs WHERE g_id=".$psid.$this->_db->getDelimiter();
		}
		$res=true;
		if(count($analogs)){
			foreach($analogs as $aid){
				if ($aid!=$psid) {
					$sql.="INSERT INTO #__goods_analogs VALUES(".$psid.",".$aid.")".$this->_db->getDelimiter();
					if($reverse) $sql.="INSERT INTO #__goods_analogs VALUES(".$aid.",".$psid.")".$this->_db->getDelimiter();
				}
			}
		}
		$this->_db->setQuery($sql);
		return $this->_db->query_batch(true,true);		
	}
	public function getAdditionals($psid){
		$sql="SELECT DISTINCT a.ad_id AS id, CONCAT(g.g_sku,' : ', g.g_name) AS title FROM #__goods_additionals AS a,#__goods AS g WHERE g.g_id=a.ad_id AND a.g_id=".$psid;
		$this->_db->setQuery($sql);
		return $this->_db->loadAssocList();
	}
	public function saveAdditionals($psid,$additional){
 	   $sql="DELETE FROM #__goods_additionals WHERE g_id=".$psid.$this->_db->getDelimiter();		
	   $res=true;
		if(count($additional)){
			foreach($additional as $aid){
				if ($aid!=$psid) {
					$sql.="INSERT INTO #__goods_additionals VALUES(".$psid.",".$aid.")".$this->_db->getDelimiter();
				}
			}
		}
		$this->_db->setQuery($sql);
		return $this->_db->query_batch(true,true);
	}
	
	public function getDiscounts($psid){
		$sql="SELECT DISTINCT gd.d_id AS id, d.d_name AS title FROM #__goods_discounts AS gd, #__discounts AS d WHERE d.d_id=gd.d_id AND gd.g_id=".$psid;
		$this->_db->setQuery($sql);
		return $this->_db->loadAssocList();
	}
	public function saveDiscounts($psid, $discounts){
		$sql="DELETE FROM #__goods_discounts WHERE g_id=".$psid.$this->_db->getDelimiter();
		$res=true;
		if(count($discounts)){
			foreach($discounts as $did){
				$sql.="INSERT INTO #__goods_discounts VALUES(".$psid.",".$did.")".$this->_db->getDelimiter();
			}
		}
		$this->_db->setQuery($sql);
		return $this->_db->query_batch(true,true);
	}
	public function getSKUPath($psid){
		$sql="SELECT CONCAT(g_sku,' : ',g_name) FROM #__goods WHERE g_id=".$psid;
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}
	public function getComplectSet($psid){
		$sql="SELECT DISTINCT a.s_id AS id, CONCAT(g.g_sku,' : ', g.g_name) AS title, a.s_quantity AS quantity FROM #__goods_sets AS a,#__goods AS g WHERE g.g_id=a.s_id AND a.g_id=".$psid;
		$this->_db->setQuery($sql);
		return $this->_db->loadAssocList();
	}
	public function saveComplectSet($psid,$complects){
		$sql="DELETE FROM #__goods_sets WHERE g_id=".$psid.$this->_db->getDelimiter();
		$res=true;
		if(count($complects)){
			foreach($complects as $aid=>$quantity){
				if ($aid!=$psid && $quantity) {
					$sql.="INSERT INTO #__goods_sets VALUES(".$psid.",".$aid.",".$quantity.")".$this->_db->getDelimiter();
				}
			}
		}
		$this->_db->setQuery($sql);
		return $this->_db->query_batch(true,true);
	}
	public function updateSKU($psid){
		$sku_prefix = Module::getInstance()->getParam("automatic_sku_prefix");
		$new_sku = $sku_prefix.$psid;
		$sql="SELECT COUNT(*) FROM #__goods WHERE g_sku='".$new_sku."' AND g_id<>".$psid;
		$this->_db->setQuery($sql);
		if ($this->_db->loadResult()>0){
			$new_sku=$sku_prefix.$psid."-".md5($psid);
		}
		$sql="UPDATE #__goods SET g_sku='".$new_sku."' WHERE g_id=".$psid;
		$this->_db->setQuery($sql);
		return $this->_db->query();
	}
	/**********************************************************************************/
	public function updatePricesFromComplect($psid){
		$g_price_1=0;
		$g_price_2=0;
		$g_price_3=0;
		$g_price_4=0;
		$g_price_5=0;
		$sql="SELECT g.g_id, g.g_price_1, g.g_price_2, g.g_price_3, g.g_price_4, g.g_price_5, s.s_quantity";
		$sql.=" FROM #__goods_sets AS s, #__goods AS g";
		$sql.=" WHERE s.s_id=g.g_id AND s.g_id=".$psid;
		$this->_db->setQuery($sql);
		$complect = $this->_db->loadObjectList();
		if(count($complect)){
			foreach($complect as $gset) {
				$g_price_1 = $g_price_1 + $gset->g_price_1 * $gset->s_quantity;
				$g_price_2 = $g_price_2 + $gset->g_price_2 * $gset->s_quantity;
				$g_price_3 = $g_price_3 + $gset->g_price_3 * $gset->s_quantity;
				$g_price_4 = $g_price_4 + $gset->g_price_4 * $gset->s_quantity;
				$g_price_5 = $g_price_5 + $gset->g_price_5 * $gset->s_quantity;
			}
			$sql="UPDATE #__goods SET g_price_1='".$g_price_1."', g_price_2='".$g_price_2."', g_price_3='".$g_price_3."', g_price_4='".$g_price_4."', g_price_5='".$g_price_5."' WHERE g_id='".$psid."'";
			$this->_db->setQuery($sql);
			return $this->_db->query();
		}
		return false;
	}
	public function updateAllPricesFromComplects(){
		if(catalogConfig::$complectPriceAsGoodsSum){
			$sql = "UPDATE #__goods g
					INNER JOIN (
						SELECT `id`, SUM(`quantity`*`price_1`) AS sum_1, SUM(`quantity`*`price_2`) AS sum_2, SUM(`quantity`*`price_3`) AS sum_3, SUM(`quantity`*`price_4`) AS sum_4, SUM(`quantity`*`price_5`) AS sum_5 FROM(
							SELECT mgs.g_id AS id, mgs.s_quantity AS quantity, mg.g_price_1 AS price_1, mg.g_price_2 AS price_2, mg.g_price_3 AS price_3, mg.g_price_4 AS price_4, mg.g_price_5 AS price_5 FROM #__goods_sets mgs
							LEFT JOIN #__goods mg ON mg.g_id = mgs.s_id
							WHERE mg.g_deleted = 0 AND mg.g_enabled =1
						) as tmp GROUP BY tmp.`id`
					) x ON g.g_id = x.id
					SET g.g_price_1 = x.sum_1, g.g_price_2 = x.sum_2, g.g_price_3 = x.sum_3, g.g_price_4 = x.sum_4, g.g_price_5 = x.sum_5 WHERE g.g_type = 5";
			$this->_db->setQuery($sql);
			return $this->_db->query();
		} else {
			return true;
		}
	}
}
?>