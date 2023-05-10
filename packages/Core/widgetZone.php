<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class widgetZone extends BaseObject {
	private static $_zones=array();
	private static $_instance = null;

	public static function createInstance() {
		if (self::$_instance == null) {
			Debugger::getInstance()->milestone("Creating widgetZone instance");
			self::$_instance = new self();
			Debugger::getInstance()->milestone("widgetZone instance created");
		}
	}
	public static function getInstance() {
		self::createInstance();
		return self::$_instance;
	}
	private function __construct() {
		$this->initObj();
		$this->prepare();
	}
	private function prepare() {
		$query = "SELECT aw.* FROM `#__widgets_active` as aw WHERE aw.aw_enabled=1 AND aw.aw_deleted=0 ORDER BY aw.aw_zone, aw.aw_ordering";
		$db = Database::getInstance();
		$db->setQuery($query);
		$awlist = $db->loadObjectList();
		if (count($awlist)){
			Event::raise('system.widget_zone.prepare_awlist', array(), $awlist); // Тут можно обработать пункты меню
			foreach ($awlist as $aw) {
				if(!Widget::getInstance($aw->aw_name)->checkAccess($aw->aw_access)) continue;
				if(!Widget::getInstance($aw->aw_name)->checkLanguage($aw->aw_forlang)) continue;
				if(!Widget::getInstance($aw->aw_name)->checkVisibilityByMenu($aw->aw_visible_in)) continue;
				$params = Widget::getInstance($aw->aw_name)->intersectParams($aw);
				self::$_zones[$aw->aw_zone][$aw->aw_id] = array("id"=>$aw->aw_id, "name"=>$aw->aw_name, "params"=>$params);
			}
		}
	}
	public function countZone($zoneName){
		if (array_key_exists($zoneName, self::$_zones)) return count(self::$_zones[$zoneName]); else return 0;
	}
	public function getZone($zoneName){
		if (array_key_exists($zoneName, self::$_zones)) return self::$_zones[$zoneName]; else return false;
	}
	public function getListWidget(){
		return self::$_zones;		
	}
	public function switchoff_allWidget(){
		foreach (self::$_zones as $zone=>$listWidget)
		{
			foreach ($listWidget as $widget_id=>$widget_body){
				$this->unsetWidgets($zone,$widget_id);
			}
		}
	}
	/**
	 * переброска всех(кроме исключаемых) виджетов из одной зоны в другую
	 * может понадобится в отдельных случаях при перестройке верстки 
	 * @param string $from_zone - зона источник
	 * @param string $to_zone - зона приемник ( для полного исключения - выбрать неиспользуемую в шаблоне зону)
	 * @param array $arr_from_zone_excl - ид виджетов которые надо оставить в зоне источнике
	 */
	public static function relocate_allWidget($from_zone, $to_zone, $arr_from_zone_excl=array()){
		if(count(self::$_zones[$from_zone])){
			foreach(self::$_zones[$from_zone] as $kw=>$wv){
				if(in_array($kw, $arr_from_zone_excl)) continue;
				unset(self::$_zones[$from_zone][$kw]);
				self::$_zones[$to_zone][$kw]=$wv;
			}
		}
	}
	// Удаляем виджеты из списка -временно
	public function unsetWidgets($aw_zone, $aw_id) {
		if(isset(self::$_zones[$aw_zone][$aw_id])) unset(self::$_zones[$aw_zone][$aw_id]);
	}
}
?>