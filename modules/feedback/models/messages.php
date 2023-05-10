<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class feedbackModelmessages extends Model {
	public function getListMessages() {
		$sqltxt="SELECT * FROM #__feedback WHERE f_uid=".User::getInstance()->getID()." ORDER BY f_date DESC";
		$this->_db->setQuery($sqltxt);
		return $this->_db->loadObjectList();		
	}
}
?>