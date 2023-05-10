<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogModelgoodsprices extends SpravModel {
	public function updatePriceChangerInfo($new_psid)	{
		if ($new_psid) {
			$sql="UPDATE #__goods_prices SET p_change_date=NOW(), p_change_uid=".User::getInstance()->getID()." WHERE p_id=".$new_psid;
			$this->_db->setQuery($sql);
			$this->_db->query();
		} else return false;
		return $new_psid;
	}
}

?>