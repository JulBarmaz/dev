<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Taxes {
	public static function getAllTaxes() {
		$db = Database::getInstance();
		$sql = "SELECT * FROM #__taxes WHERE t_enabled=1 AND t_deleted=0";
		$db->setQuery($sql);
		return $db->loadObjectList("t_id");
	}
	public static function getTax($tax_id, $enabled_only=true) {
		$tax=false;
		$db = Database::getInstance();
		$sql = "SELECT * FROM #__taxes WHERE t_id=".$tax_id;
		if($enabled_only) $sql.= " AND t_enabled=1 AND t_deleted=0";
		$db->setQuery($sql);
		$db->loadObject($tax);
		return $tax;
	}
}
?>