<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class confModelMetadata extends Model {
	public function getHeaders(){
		$sql="SELECT * FROM #__md_hdrs ORDER BY h_side, h_module, h_view, h_layout";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
	public function getHeader($psid){
		$sql="SELECT * FROM #__md_hdrs WHERE h_id=".$psid;
		$this->_db->setQuery($sql);
		$this->_db->loadObject($res);
		return $res;
	}
	public function deleteMetadata($psid){
		$sql="DELETE FROM #__md_hdrs WHERE h_id=".$psid.$this->_db->getDelimiter();
		$sql.="DELETE FROM #__md_flds WHERE f_hid=".$psid.$this->_db->getDelimiter();
		$sql.="DELETE FROM #__md_btns WHERE b_id=".$psid.$this->_db->getDelimiter();
		$this->_db->setQuery($sql);
		return $this->_db->query_batch(true,true);
	}
	public function getHeaderFromRequest(){
		$hdr=new stdClass();
		$hdr->h_id=Request::getInt("psid");
		$hdr->h_side=Request::getSafe("h_side");
		$hdr->h_module=Request::getSafe("h_module");
		$hdr->h_view=Request::getSafe("h_view");
		$hdr->h_layout=Request::getSafe("h_layout");
		$hdr->h_title=Request::getSafe("h_title");
		$hdr->h_table=Request::getSafe("h_table");
		$hdr->h_is_tree=Request::getInt("h_is_tree");
		$hdr->h_keystring=Request::getSafe("h_keystring");
		$hdr->h_namestring=Request::getSafe("h_namestring");
		$hdr->h_keycurrency=Request::getSafe("h_keycurrency");
		$hdr->h_enabled=Request::getSafe("h_enabled");
		$hdr->h_deleted=Request::getSafe("h_deleted");
		$hdr->h_keysort=Request::getSafe("h_keysort");
		$hdr->h_ordering_fld=Request::getSafe("h_ordering_fld");
//		$hdr->h_ordering_parent=Request::getSafe("h_ordering_parent");
		$hdr->h_show_cb=Request::getInt("h_show_cb");
		$hdr->h_selector=Request::getInt("h_selector");
		$hdr->h_multy_field=Request::getSafe("h_multy_field");
		$hdr->h_l_tablename=Request::getSafe("h_l_tablename");
		$hdr->h_p_tablename=Request::getSafe("h_p_tablename");
		$hdr->h_p_keystring=Request::getSafe("h_p_keystring");
		$hdr->h_p_namestring=Request::getSafe("h_p_namestring");
		$hdr->h_p_view=Request::getSafe("h_p_view");
		$hdr->h_tmpl_new=Request::getSafe("h_tmpl_new");
		$hdr->h_tmpl_modify=Request::getSafe("h_tmpl_modify");
		$hdr->h_custom_sql=Request::getSafe("h_custom_sql");
		return $hdr;
	}
	public function saveHeader($hdr){
		if ($hdr->h_id) {
			$sql="UPDATE #__md_hdrs SET ";
			$sql.="h_side='".$hdr->h_side."',"
				."h_module='".$hdr->h_module."',"
				."h_view='".$hdr->h_view."',"
				."h_layout='".$hdr->h_layout."',"
				."h_title='".$hdr->h_title."',"
				."h_table='".$hdr->h_table."',"
				."h_is_tree='".$hdr->h_is_tree."',"
				."h_keystring='".$hdr->h_keystring."',"
				."h_namestring='".$hdr->h_namestring."',"
				."h_keycurrency='".$hdr->h_keycurrency."',"
				."h_enabled='".$hdr->h_enabled."',"
				."h_deleted='".$hdr->h_deleted."',"
				."h_keysort='".$hdr->h_keysort."',"
				."h_ordering_fld='".$hdr->h_ordering_fld."',"
//				."h_ordering_parent='".$hdr->h_ordering_parent."',"
				."h_show_cb='".$hdr->h_show_cb."',"
				."h_selector='".$hdr->h_selector."',"
				."h_multy_field='".$hdr->h_multy_field."',"
				."h_l_tablename='".$hdr->h_l_tablename."',"
				."h_p_tablename='".$hdr->h_p_tablename."',"
				."h_p_keystring='".$hdr->h_p_keystring."',"
				."h_p_namestring='".$hdr->h_p_namestring."',"
				."h_p_view='".$hdr->h_p_view."',"
				."h_tmpl_new='".$hdr->h_tmpl_new."',"
				."h_tmpl_modify='".$hdr->h_tmpl_modify."',"
				."h_custom_sql='".$hdr->h_custom_sql."'";
			$sql.=" WHERE h_id=".$hdr->h_id;
		} else {
			$sql="INSERT INTO #__md_hdrs VALUES (";
			$sql.="NULL,'"
				.$hdr->h_side."','"
				.$hdr->h_module."','"
				.$hdr->h_view."','"
				.$hdr->h_layout."','"
				.$hdr->h_title."','"
				.$hdr->h_table."','"
				.$hdr->h_is_tree."','"
				.$hdr->h_keystring."','"
				.$hdr->h_namestring."','"
				.$hdr->h_keycurrency."','"
				.$hdr->h_enabled."','"
				.$hdr->h_deleted."','"
				.$hdr->h_keysort."','"
				.$hdr->h_ordering_fld."','"
//				.$hdr->h_ordering_parent."','"
				.$hdr->h_show_cb."','"
				.$hdr->h_selector."','"
				.$hdr->h_multy_field."','"
				.$hdr->h_l_tablename."','"
				.$hdr->h_p_tablename."','"
				.$hdr->h_p_keystring."','"
				.$hdr->h_p_namestring."','"
				.$hdr->h_p_view."','"
				.$hdr->h_tmpl_new."','"
				.$hdr->h_tmpl_modify."','"
				.$hdr->h_custom_sql;
			$sql.=")";
		}
		$this->_db->setQuery($sql);
		return $this->_db->query();
	}
	public function getFields($psid){
		$sql="SELECT * FROM #__md_flds WHERE f_hid=".(int)$psid." ORDER BY f_ordering";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
	public function getField($psid){
		$sql="SELECT * FROM #__md_flds WHERE f_id=".(int)$psid;
		$this->_db->setQuery($sql);
		$this->_db->loadObject($res);
		return $res;
	}
}
?>