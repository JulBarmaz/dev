<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class serviceModelmailerlog extends SpravModel {
	public function cleanMailerlog(){
		$sql = "DELETE FROM #__mailer_log";
		$this->_db->setQuery($sql);
		return $this->_db->query();
	}
}
?>