<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogModelOptions_data extends SpravModel {
	public function countOptionVals($opt_id){
		$sql="SELECT COUNT(*) FROM #__goods_opt_vals_data WHERE ovd_od_id=".intval($opt_id);
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}
	public function valsPossible($opt_id){
		$sql = "SELECT t.t_have_values FROM #__goods_opt_types as t, #__goods_options AS o, #__goods_options_data AS d";
		$sql.=" WHERE t.t_id=o.o_type AND o.o_id=d.od_opt_id AND d.od_id=".(int)$opt_id;
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}
	
}
?>