<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogHelperCompare {
	public static $cookie_name = "BARMAZ_compare";
	private $compare_id = null;
	private $compare_hash = null;
	public $goods = array(); // id, optArr и количество товаров
	public $keepTime = 1209600; // 14 дней
	
	public function __construct() {
		// $this->initObj();
		$compare_id = Request::getSafe("compare_id","","cookie");
		if(!$compare_id) {
			$compare_id=base64_encode(md5(session_id()).md5(time()));
			Session::getInstance()->setcookie("compare_id", $compare_id, time() + 60*siteConfig::$cookieLifeTime,"/");
		}
		$this->compare_id=$compare_id;
		$this->compare_hash=md5(Session::getInstance()->getKey());
		$this->cleanOldCompare();
		$this->loadCompare();
	}
	public function cleanOldCompare(){
		$sql = "DELETE FROM #__goods_compare WHERE compare_touch<".(time()-$this->keepTime);
		Database::getInstance()->setQuery($sql);
		return Database::getInstance()->query();
	}
	private function loadCompare() {
		$sql = "SELECT compare_data FROM #__goods_compare WHERE compare_id='".$this->compare_id."'";
		Database::getInstance()->setQuery($sql);
		$this->goods = json_decode(base64_decode(Database::getInstance()->loadResult()), true);
		if(!is_array($this->goods)) $this->goods = array();
	}
	private function saveCompare() {
		$compare_data = base64_encode(json_encode($this->goods));
		$sql = "INSERT INTO #__goods_compare  VALUES ('".$this->compare_id."', ".time().", '".$compare_data."') ON DUPLICATE KEY UPDATE compare_touch=".time().", compare_data='".$compare_data."'";
		Database::getInstance()->setQuery($sql);
		return Database::getInstance()->query();
	}
	public function addComparePosition($psid) {
		$this->goods[$psid] = $psid;
		return $this->saveCompare();
	}
	public function getCompareHash(){
		return $this->compare_hash;
	}
	public function getCompare(){
		return $this->goods;
	}
	public function deleteComparePosition($psid) {
		if ($this->goods && count($this->goods) && array_key_exists($psid, $this->goods)) {
			unset($this->goods[$psid]);
			return $this->saveCompare();
		} else return true;
	}
}
?>
