<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class feedbackModelmessages extends SpravModel {

	public function setRead($psid){
		if (!$psid) return false;
		$sql_txt="UPDATE #__feedback SET f_read = 1 , f_readdate = NOW() WHERE f_read=0 AND f_id=".$psid;
		$this->_db->setQuery($sql_txt);
		if ($this->_db->query()) return true;
		else return false;
	}
}
?>