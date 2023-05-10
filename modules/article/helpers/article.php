<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class articleHelperArticle {
	public function getArticleByAlias($alias){
		$sql="SELECT a_id FROM #__articles WHERE a_alias='".$alias."'";	
		Database::getInstance()->setQuery($sql);
		return intval(Database::getInstance()->loadResult());	
	}
	public function getArticle($aid, $no_deleted=1, $enabled_only=1) {
		$query = "SELECT a.* FROM #__articles AS a WHERE  a.a_id=".intval($aid);
		if ($no_deleted) $query.= " AND a.a_deleted=0";
		if ($enabled_only)$query.= " AND a.a_published=1";
		Database::getInstance()->setQuery($query);
		if (Database::getInstance()->loadObject($art)) {
			return $art;
		} else return false;
	}
} 
?>
