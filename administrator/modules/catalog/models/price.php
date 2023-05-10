<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogModelprice extends Model {
	public function getGoods($psid,$params){
		$sql="SELECT g.* FROM #__goods AS g LEFT JOIN #__goods_links AS l ON l.g_id=g.g_id";
		if ($params["break_by_groups"]) {
			if ($psid){
				$sql.=" WHERE l.parent_id=".(int)$psid;				
				if ($params["enabled_only"]) $sql.=" AND g.g_enabled=1";
			} else {
				if ($params["enabled_only"]) $sql.=" WHERE g.g_enabled=1";
			}
			$sql.=" ORDER BY l.ordering,g.g_name";
		} else {
			if ($psid){
				$psid_arr=$this->getBranchArr($psid,$params["enabled_only"]);
				$sql.=" WHERE l.parent_id IN (".implode(",",$psid_arr).")";
				if ($params["enabled_only"]) $sql.=" AND g.g_enabled=1";
			} else {
				if ($params["enabled_only"]) $sql.=" WHERE g.g_enabled=1";
			}
			$sql.=" ORDER BY g.g_name";
		}
		$this->_db->setQuery($sql);
		$res=$this->_db->loadObjectList();
		return $res;
	}
	public function getBranchArr($psid, $enabled_only) {
		$psid_arr[]=$psid;
		$params["parent_group"]=$psid;
		$params["enabled_only"]=$enabled_only;
		$arr=$this->getTreeArr($params);
		if (count($arr)){
			foreach($arr as $element){
				$psid_arr[]=$element->id;
			}
		}
		return $psid_arr;
	}
	public function getTreeArr($params) {
		$tree = new simpleTreeTable;
		$tree->table="goods_group";
    $tree->fld_id="ggr_id";
    $tree->fld_parent_id="ggr_id_parent";
    $tree->fld_title="ggr_name";
    $tree->fld_deleted="ggr_deleted";
    $tree->fld_enabled="ggr_enabled";
    $tree->fld_orderby="ggr_ordering, ggr_name";
    $tree->element_link="";
		$tree->buildTreeArrays('',0,1,$params["enabled_only"]);
  	return $tree->getTreeArr($params["parent_group"]);
  }
	
	public function getGroupTitle($params){
		if (!$params["parent_group"])	return "";
		$sql="SELECT ggr_name FROM #__goods_group WHERE ggr_id=".(int)$params["parent_group"]." AND ggr_deleted=0";
		if($params["enabled_only"]) $sql.=" AND ggr_enabled=1";
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}
	public function getPriceSetsNames()
	{
		$sql="select p_id as id,p_name as title,p_comment from #__goods_priceset where p_deleted=0 and p_enabled=1";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
	public function getPriceSetsID($id=0)
	{
		$sql="select * from #__goods_priceset where p_id=".(int)$id;
		$this->_db->setQuery($sql);
		$res=false;
		$this->_db->loadObject($res);
		return $res;
	}
	public function savePriceset($data)
	{
	
		$sql="insert into `#__goods_priceset`
		(`p_id`,`p_name`,`p_head_colon`,`p_foot_colon`,`p_checkbox`,`p_template`,
		`p_price`,`p_discont`,`p_deleted`,`p_enabled`,`p_datecreate`,
		`p_comment`)
		values(".$data['p_id'].",
		'".$data['p_name']."',
		'".$data['p_head_colon']."',
		'".$data['p_foot_colon']."',
	    '".$data['p_checkbox']."',
        '".$data['p_template']."',
	    ".$data['p_price'].",
	    ".$data['p_discont'].",0,1,NOW(),
	    '".$data['p_comment']."') ON DUPLICATE KEY UPDATE
        `p_name`='".$data['p_name']."',
        `p_head_colon`='".$data['p_head_colon']."',
        `p_foot_colon`='".$data['p_foot_colon']."',
        `p_checkbox`='".$data['p_checkbox']."',
		`p_template`='".$data['p_template']."',
		`p_price`=".$data['p_price'].",
		`p_discont`=".$data['p_discont'].",
		`p_datecreate`=NOW(),
		`p_comment`='".$data['p_comment']."'";
		$this->_db->setQuery($sql);
		return $this->_db->query();
	}
	
}

?>