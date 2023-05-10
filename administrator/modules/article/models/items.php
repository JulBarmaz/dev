<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class articleModelitems extends SpravModel {

	public function changeDateItems($psid,$new_date) {
		$sql="update #__articles set a_date='".Date::toSQL($new_date)."' where a_id=".$psid;
		$this->_db->setQuery($sql);
		return $this->_db->query();
	}
	public function getDataArticle($psid){
		$res=new stdClass();
		$sql="select a_date,a_title from #__articles where a_id=".$psid;
		$this->_db->setQuery($sql);
		$this->_db->LoadObject($res);
		return $res;
	}
}
?>