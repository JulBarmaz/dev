<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class userModelauth_providers extends SpravModel {
	public function save(){
		$psid=parent::save();
		if ($psid) {
			$res = $this->getElement($psid);
			if (!$res) return 0;
			$sn_key = Request::getSafe("sn_key");
			$sn_secret = Request::getSafe("sn_secret");
			$crypt = new Crypta ();
			$sn_key_encoded = base64_encode($crypt->xxtea_encrypt($sn_key, backofficeConfig::$secretCode));
			$sn_secret_encoded = base64_encode($crypt->xxtea_encrypt($sn_secret, backofficeConfig::$secretCode));
			$sql = "UPDATE #__auth_providers SET sn_key='".$sn_key_encoded."', sn_secret='".$sn_secret_encoded."' WHERE sn_id=".$psid;
			$this->_db->setQuery($sql);
			$this->_db->setQuery($sql);
			if (!$this->_db->query()) return 0;
		}
		return $psid;
	}
	public function getAuthProvidersCKArray(){
		$providers = array();
		$files = Files::getFiles(PATH_FRONT."packages".DS."tools".DS."SNProviders", "", false);
		$mask = "SNProvider.php";
		if(is_array($files)){
			foreach ($files as $filename=>$data){
				$found = mb_stristr($filename, $mask, true);
				if($found) $providers[mb_strtolower($found)]=$found;
			}
			
		}
		return $providers;
	}
}
?>