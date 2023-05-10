<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class pollsHelperPolls {
	public function getIdByAlias($view,$alias){
		switch($view){
			case "poll":
				$sql="SELECT p_id FROM #__polls WHERE p_alias='".$alias."'";
			break;
			default:
				return 0;
			break;
		}
		Database::getInstance()->setQuery($sql);
		return intval(Database::getInstance()->loadResult());
	}
} 
?>
