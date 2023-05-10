<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class defaultViewauth_providers extends SpravView {
	public function modify($row) {
		if(is_object($row)){
			if($row->sn_key && $row->sn_secret){
				$crypt = new Crypta ();
				$row->sn_key = $crypt->xxtea_decrypt(base64_decode($row->sn_key), backofficeConfig::$secretCode);
				$row->sn_secret = $crypt->xxtea_decrypt(base64_decode($row->sn_secret), backofficeConfig::$secretCode);
			} else {
				$row->sn_key = "";
				$row->sn_secret = "";
			}
		} else {
			$row = new stdClass();
			$row->sn_key = "";
			$row->sn_secret = "";
		}
		parent::modify($row);
	}
}
?>