<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogHelperFavourites {
	public static $cookie_name = "BARMAZ_favourites";
	private $favourites_id = null;
	private $favourites_hash = null;
	public $goods = array(); // id, optArr и количество товаров
	public $keepTime = 1209600; // 14 дней
	
	public function __construct() {
		// $this->initObj();
		$favourites_id = Request::getSafe("favourites_id","","cookie");
		if(!$favourites_id) {
			$favourites_id=base64_encode(md5(session_id()).md5(time()));
			Session::getInstance()->setcookie("favourites_id", $favourites_id, time() + 60*siteConfig::$cookieLifeTime,"/");
		}
		$this->favourites_id=$favourites_id;
		$this->favourites_hash=md5(Session::getInstance()->getKey());
		$this->cleanOldFavourites();
		$this->loadFavourites();
	}
	public function cleanOldFavourites(){
		$sql = "DELETE FROM #__goods_favourites WHERE favourites_touch<".(time()-$this->keepTime);
		Database::getInstance()->setQuery($sql);
		return Database::getInstance()->query();
	}
	private function loadFavourites() {
		$sql = "SELECT favourites_data FROM #__goods_favourites WHERE favourites_id='".$this->favourites_id."'";
		Database::getInstance()->setQuery($sql);
		$this->goods = json_decode(base64_decode(Database::getInstance()->loadResult()), true);
		if(!is_array($this->goods)) $this->goods = array();
	}
	private function saveFavourites() {
		$favourites_data = base64_encode(json_encode($this->goods));
		$sql = "INSERT INTO #__goods_favourites  VALUES ('".$this->favourites_id."', ".time().", '".$favourites_data."') ON DUPLICATE KEY UPDATE favourites_touch=".time().", favourites_data='".$favourites_data."'";
		Database::getInstance()->setQuery($sql);
		return Database::getInstance()->query();
	}
	public function addFavouritesPosition($psid) {
		$this->goods[$psid] = $psid;
		return $this->saveFavourites();
	}
	public function getFavouritesHash(){
		return $this->favourites_hash;
	}
	public function getFavourites(){
		return $this->goods;
	}
	public function deleteFavouritesPosition($psid) {
		if ($this->goods && count($this->goods) && array_key_exists($psid, $this->goods)) {
			unset($this->goods[$psid]);
			return $this->saveFavourites();
		} else return true;
	}
}
?>
