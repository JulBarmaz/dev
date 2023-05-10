<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogModelgoods extends SpravModel {

	public $current_path = array();
	public $ParentArray=array(); // список родительских групп товара

	public function getAjaxFilter(){
		if (is_null($this->meta)) $this->loadMeta();
		$tp = User::getInstance()->u_pricetype;
		$col_count = count($this->meta->field);
		for ($i = 1; $i <= $col_count; $i++){
			if($this->meta->filter[$i] && $this->meta->field[$i]=='g_price_'.$tp) {
				if(!catalogConfig::$hide_prices) $this->meta->updateArrayField('filter','g_price_'.$tp,1);
			} elseif(in_array($this->meta->field[$i],array("g_price_1","g_price_2","g_price_3","g_price_4","g_price_5"))){
				$this->meta->updateArrayField('filter',$this->meta->field[$i],0);
			}
		}
		return parent::getAjaxFilter();
	}
	public function getImages($psid=0) {
		if ($psid) {
			$query = "SELECT * FROM #__goods_img WHERE i_deleted=0 AND i_enabled=1 AND i_gid='".$psid."' ORDER BY i_ordering";
			$this->_db->setQuery($query);
			return $this->_db->loadObjectList();
		} else return array();
	}
	public function getAnalogs($psid){
		$sql="SELECT DISTINCT a.a_id AS id, g.g_name AS title,g.g_thumb as thumb,g.g_sku as g_sku, 
				g.g_price_1, g.g_price_2, g.g_price_3, g.g_price_4, g.g_price_5,g_currency,
				g.g_alt_thm as seo_alt,g.g_title_thm as seo_title,g.g_alias as alias
				FROM #__goods_analogs AS a,#__goods AS g WHERE g.g_enabled=1 AND g.g_deleted=0 AND g.g_id=a.a_id AND a.g_id=".$psid;
		$this->_db->setQuery($sql);
		return $this->_db->loadAssocList();
	}
	public function getAdditional($psid){
		$sql="SELECT DISTINCT a.ad_id AS id, g.g_name AS title,g.g_thumb as thumb,g.g_sku as g_sku,
				g.g_price_1, g.g_price_2, g.g_price_3, g.g_price_4, g.g_price_5,g_currency,
				g.g_alt_thm as seo_alt,g.g_title_thm as seo_title,g.g_alias as alias
				FROM #__goods_additionals AS a,#__goods AS g WHERE g.g_enabled=1 AND g.g_deleted=0 AND g.g_id=a.ad_id AND a.g_id=".$psid;
		$this->_db->setQuery($sql);
		return $this->_db->loadAssocList();
	}
	
	public function getVideos($psid=0) {
		if ($psid) {
			$query = "SELECT * FROM #__goods_videos WHERE v_deleted=0 AND v_published=1 AND v_gid='".$psid."' ORDER BY v_ordering";
			$this->_db->setQuery($query);
			return $this->_db->loadObjectList();
		} else return array();
	}
	public function getElement($psid=0,$fillempty=false,$enabled_only=true) {
		if (is_null($this->meta)) $this->loadMeta();
		$meta=$this->meta;
		$result=parent::getElement($psid,$fillempty,$enabled_only);
		return $result;
	}
	public function getFirstParent($psid){
		$sql="SELECT l.parent_id
				FROM #__goods_links AS l, #__goods_group AS g
				WHERE l.parent_id=g.ggr_id
				AND l.g_id='".$psid."'
				AND g.ggr_deleted=0
				AND g.ggr_enabled=1
				ORDER BY l.ordering";
		Database::getInstance()->setQuery($sql);
		return Database::getInstance()->loadResult();
	}
	public function getParentGroup($gr_id,$level=0){
		if(!$level) $this->current_path=array();
		$sql="SELECT `ggr_id`,`ggr_id_parent`,`ggr_name`,`ggr_alias`
				FROM #__goods_group
				WHERE ggr_deleted=0 AND ggr_id=".(int)$gr_id;
		Database::getInstance()->setQuery($sql);
		if(Database::getInstance()->LoadObject($res))	{
			$this->current_path[$res->ggr_id]["title"]=$res->ggr_name;
			$this->current_path[$res->ggr_id]["alias"]=$res->ggr_alias;
			if($res->ggr_id_parent)	{
				$level++;
				$this->getParentGroup($res->ggr_id_parent,$level);
			}
		}
		return $this->current_path;
	}
	public function loadGroup($ggr_id) {
		$query = "SELECT * FROM #__goods_group WHERE ggr_id=".intval($ggr_id)." AND ggr_enabled=1 AND ggr_deleted=0";
		$this->_db->setQuery($query);
		$this->_db->loadObject($grp);
		return $grp;
	}
	public function loadChildGroups($parent_id) {
		$query = "SELECT * FROM #__goods_group WHERE ggr_id_parent=".intval($parent_id)." AND ggr_enabled=1 AND ggr_deleted=0 ORDER BY ggr_ordering,ggr_name";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	public function getNameGroup($ggr_id) {
		$query = "SELECT ggr_name FROM #__goods_group WHERE ggr_id=".intval($ggr_id)." AND ggr_enabled=1 AND ggr_deleted=0";
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Возвращает массив групп товара
	 */
	public function getParentArray($id) {
		$this->_db->setQuery("SELECT ggr_name as title,ggr_id as id, ggr_id_parent as parent FROM #__goods_group
				WHERE ggr_id='".$id."'");
		$row=false;
		$this->_db->loadObject($row);
		if($row) {
			if(!key_exists($row->id, $this->ParentArray)) {
				$this->ParentArray[$row->id]['title']=$row->title;
				$this->ParentArray[$row->id]['parent']=$row->parent;
				if($row->parent>0)	{
					$this->getParentArray($row->parent);
				}
			}
			return true;
		}
	}
	public function getWholeTreeUp($psid){
		$arr[]=$psid;
		$ggrTree = new simpleTreeTable();
		$ggrTree->table="goods_group";
		$ggrTree->fld_id="ggr_id";
		$ggrTree->fld_parent_id="ggr_id_parent";
		$ggrTree->fld_title="ggr_name";
		$ggrTree->fld_deleted="ggr_deleted";
		$ggrTree->fld_enabled="ggr_enabled";
		$ggrTree->fld_orderby="ggr_name";
		$ggrTree->element_link="";
		$ggrTree->buildTreeArrays();
		return $ggrTree->getWholeTreeUp($arr);
	}
	public function getAllGroupChilds($psid, &$childs){
		if(is_array($psid)) $psid = implode("','", $psid);
		$this->_db->setQuery("SELECT ggr_id FROM #__goods_group WHERE ggr_id_parent IN ('".$psid."')");
		$res=$this->_db->loadResultArray();
		if(is_array($res) && count($res)){
			foreach($res as $val) $childs[$val]=$val;
			$this->getAllGroupChilds($res, $childs);
		}
	}
	public function getGroupsFieldsArray($psid, $with_childs = false, &$parents=array(), &$childs=array()){
		$fbgList = $this->getGroupsFields($psid, $with_childs, $parents, $childs);
		$fbgListNames=array();
		if (is_array($fbgList) && count($fbgList)) {
			foreach($fbgList as $k=>$v){
				$fbgListNames[$v->f_name]=1;
			}
		}
		return $fbgListNames;
	}
	public function getGroupsFields($psid, $with_childs = false, &$parents=array(), &$childs=array()){
		if (!$psid) return array();
		$parents=$this->getWholeTreeUp($psid);
		$_all=array();
		if(count($parents)){
			foreach ($parents as $key=>$val){
				$_all[$val] = $val;
			}
		}
		if($with_childs){
			$this->getAllGroupChilds($psid, $childs);
			if(count($childs)){
				foreach ($childs as $key=>$val){
					$_all[$val] = $val;
				}
			}
		}
		$linkArray = array_keys($_all);
		if (!is_array($linkArray) || !count($linkArray)) return array();
		$sql = "SELECT f.f_id,f.f_name FROM #__fields_list AS f";
		$sql.=" LEFT JOIN #__goods_group_fields AS ggf ON ggf.f_id=f.f_id";
		$sql.=" WHERE f.f_custom=1 AND f.f_table='goods'";
		$sql.=" AND ggf.parent_id IN ('".implode("','",$linkArray)."') ";
		$sql.=" ORDER BY f.f_descr";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList("f_id");
	}
	public function GetGroupTree($psid) {
		$listGroup=$this->GetGroupList($psid);
		if($listGroup) 	{
			foreach($listGroup as $val) {
				$group_list=$this->getParentArray($val);
			}
		}
		return $this->ParentArray;
	}
	public function GetGroupList($psid) {
		$sql = "select parent_id from #__goods_links where g_id='".$psid."'";
		$this->_db->setQuery($sql);
		$result=$this->_db->loadResultArray();
		return $result;
	}
	public function getVendorName($psid){
		$sql="SELECT v_store_name FROM #__vendors WHERE v_id=".(int)$psid;
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}
	public function getManufacturerName($psid){
		$sql="SELECT mf_name FROM #__manufacturers WHERE mf_id=".(int)$psid;
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}
	public function getVendor($psid){
		$sql="SELECT * FROM #__vendors WHERE v_id=".(int)$psid." AND v_enabled=1 AND v_deleted=0";
		$this->_db->setQuery($sql);
		$object=false;
		$this->_db->loadObject($object);
		return $object;
	}
	public function getManufacturer($psid){
		$sql="SELECT * FROM #__manufacturers WHERE mf_id=".(int)$psid." AND mf_enabled=1 AND mf_deleted=0";
		$this->_db->setQuery($sql);
		$object=false;
		$this->_db->loadObject($object);
		return $object;
	}
	public function getComplectSet($g){
		$complect = array();
		if (is_object($g) && $g->g_type==5){
			$sql="SELECT g.*, s.s_quantity FROM #__goods_sets AS s, #__goods AS g WHERE s.s_id=g.g_id AND s.g_id=".$g->g_id;
			$this->_db->setQuery($sql);
			$complect = $this->_db->loadObjectList();
		}
		return $complect;
	}
	public function updateComplectPrice($g, $complect=array()){
		if (is_object($g) && $g->g_type==5 && catalogConfig::$complectPriceAsGoodsSum){
			$g->g_price_1=0;
			$g->g_price_2=0;
			$g->g_price_3=0;
			$g->g_price_4=0;
			$g->g_price_5=0;
			if(!count($complect)){
				$sql="SELECT g.g_id, g.g_price_1, g.g_price_2, g.g_price_3, g.g_price_4, g.g_price_5, s.s_quantity";
				$sql.=" FROM #__goods_sets AS s, #__goods AS g";
				$sql.=" WHERE s.s_id=g.g_id AND s.g_id=".$g->g_id;
				$this->_db->setQuery($sql);
				$complect = $this->_db->loadObjectList();
			}
			if(count($complect)){
				foreach($complect as $gset) {
					$g->g_price_1=$g->g_price_1+$gset->g_price_1*$gset->s_quantity;
					$g->g_price_2=$g->g_price_2+$gset->g_price_2*$gset->s_quantity;
					$g->g_price_3=$g->g_price_3+$gset->g_price_3*$gset->s_quantity;
					$g->g_price_4=$g->g_price_4+$gset->g_price_4*$gset->s_quantity;
					$g->g_price_5=$g->g_price_5+$gset->g_price_5*$gset->s_quantity;
				}
			}
		}
		return $g;
	}
	public function getDiscounts($ids){
		return Module::getHelper("goods","catalog")->getDiscounts($ids);
	}
	public function applyDiscounts($gid, $price, $discounts){
		return Module::getHelper("goods","catalog")->applyDiscounts($gid, $price, $discounts);
	}
	public function getExtendedPrices($ids){
		return Module::getHelper("goods","catalog")->getExtendedPrices($ids);
	}
	public function applyExtendedPrices($gid, $price, $quantity, $extPrices){
		return Module::getHelper("goods","catalog")->applyExtendedPrices($gid, $price, $quantity, $extPrices);
	}
	public function getOptions($psid){
		return Module::getHelper("goods","catalog")->getOptions($psid);
	}
	public function getOptionsData($ids){
		return Module::getHelper("goods","catalog")->getOptionsData($ids);
	}
	public function haveOptions($ids){
		return Module::getHelper("goods","catalog")->haveOptions($ids);
	}
	public function getCategories4LiveSearch($kwds, $search_mode){
		$in_sql = "SELECT g.g_id FROM #__goods as g WHERE";
		switch($search_mode){
			case "1":
				$in_sql .= " g.g_sku LIKE '%".$kwds."%' OR g.g_name LIKE '%".$kwds."%'";
				break;
			case "2":
				$in_sql .= " g.g_sku LIKE '%".$kwds."%'";
				break;
			case "3":
				$in_sql .= " g.g_name LIKE '%".$kwds."%'";
				break;
		}
		$sql = "SELECT l.parent_id as cat_id, count(l.g_id) as count_goods FROM #__goods_links as l WHERE l.g_id IN (".$in_sql.") GROUP BY l.parent_id ORDER BY count_goods DESC";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
	
	public function getCountGoods4LiveSearch($kwds, $search_mode){
		$sql = "SELECT count(g.g_id) FROM #__goods as g WHERE";
		switch($search_mode){
			case "1":
				$sql .= " g.g_sku LIKE '%".$kwds."%' OR g.g_name LIKE '%".$kwds."%'";
				break;
			case "2":
				$sql .= " g.g_sku LIKE '%".$kwds."%'";
				break;
			case "3":
				$sql .= " g.g_name LIKE '%".$kwds."%'";
				break;
		}
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}
	
	public function getGoods4LiveSearch($kwds, $search_mode, $limit = 0){
		$sql = "SELECT g.* FROM #__goods as g WHERE";
		switch($search_mode){
			case "1":
				$sql .= " g.g_sku LIKE '%".$kwds."%' OR g.g_name LIKE '%".$kwds."%'";
				break;
			case "2":
				$sql .= " g.g_sku LIKE '%".$kwds."%'";
				break;
			case "3":
				$sql .= " g.g_name LIKE '%".$kwds."%'";
				break;
		}		
		$sql .= " LIMIT ".$limit;
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
}
?>