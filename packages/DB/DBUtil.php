<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class DBUtil {
	private static $_nameQuote		= null;

	public static function quote($text) {
		return '\''.Database::getInstance()->getEscaped($text).'\'';
	}
	public static function nameQuote( $s ) {
		if (strpos( $s, '.' ) === false) {
			$q = self::$_nameQuote;
			if (strlen( $q ) == 1) return $q . $s . $q;
			else return $q[0] . $s . $q[1];
		}	else return $s;
	}
	public static function truncate($table=false) {
		if(!$table) return false;
		Database::getInstance()->setQuery("truncate table #__$table");
		return Database::getInstance()->query();
	}
	public static function now() {
		$query = "SELECT NOW()";
		Database::getInstance()->setQuery($query);
		return Database::getInstance()->loadResult();
	}
	public static function resetCounter($tablename=""){
		if (!$tablename) return;
		$sql="ALTER TABLE #__".$tablename." AUTO_INCREMENT=1";
		Database::getInstance()->setQuery($sql);
		return Database::getInstance()->query();
	}
	public static function sql_table_exist($table = "") {
		Database::getInstance()->setQuery("SHOW TABLES");
		$rows=Database::getInstance()->loadResultArray();
		foreach ($rows as $key=>$line) {
			if($line[0]==$table) return true;
		}
		return false;
	}
	public static function explain() {
		$db=Database::getInstance();
		$temp = $db->getQuery(false);
		$db->setQuery("EXPLAIN ".$temp);
		if (!$db->query()) return null;
		$first = true;
		$buf = '<pre>'.htmlspecialchars($temp).'</pre>';
		$buf .= "<table cellspacing=\"1\" cellpadding=\"2\" border=\"0\" bgcolor=\"#000000\" align=\"center\">";
		$rows=$db->loadAssocList();
		foreach ($rows as $row) {
			if ($first) {
				$buf .= "<tr>";
				foreach ($row as $k=>$v) $buf .= "<th bgcolor=\"#ffffff\">$k</th>";
				$buf .= "</tr>";
				$first = false;
			}
			$buf .= "<tr>";
			foreach ($row as $k=>$v) $buf .= "<td bgcolor=\"#ffffff\">$v</td>";
			$buf .= "</tr>";
		}
		$buf .= "</table><br />&nbsp;";
		$db->setQuery($temp);
		return "<div style=\"background-color:#FFFFCC\" align=\"left\">$buf</div>";
	}
	public static function cleanNameForDB($name){
		return strtolower(str_replace("-", "_", Translit::_($name)));
	}
}
?>