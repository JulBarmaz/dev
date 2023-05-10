<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogModelOptions extends SpravModel {
	public function countOptionVals($opt_id){
		$sql="SELECT COUNT(*) FROM #__goods_opt_vals WHERE ov_opt_id=".intval($opt_id);
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}
	public function valsPossible($opt_id){
		$sql = "SELECT t.t_have_values FROM #__goods_opt_types as t, #__goods_options AS o";
		$sql.=" WHERE t.t_id=o.o_type AND o.o_deleted=0 AND o.o_id=".(int)$opt_id;
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}
	public function getOptionsTypes(){
		$sql = "SELECT * FROM #__goods_opt_types";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList('t_id'); 
	}
}
?>