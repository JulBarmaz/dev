<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class userModeluser extends SpravModel {

	public function activateUser($affCode) {
		$query = "UPDATE `#__users` SET `u_activated`=1 WHERE `u_affiliate_code`='".$affCode."'";
		$this->_db->setQuery($query);
		$this->_db->query($query);
	}

	public function getAffiliateCode($uid) {
		$query = "SELECT `u_affiliate_code` FROM `#__users` WHERE `u_id`=".intval($uid);
		$this->_db->setQuery($query);
		return strval($this->_db->loadResult());
	}

	public function getUserByEmail($email) {
		$query = "SELECT * FROM `#__users` WHERE `u_email`='".$email."' LIMIT 1";
		$this->_db->setQuery($query);
		if ($this->_db->loadObject($obj)) return $obj;
		else return false;
	}
}

?>