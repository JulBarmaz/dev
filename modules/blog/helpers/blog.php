<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class blogHelperBlog {
	public function getIdByAlias($view,$alias){
		switch($view){
			case "category":
				$sql="SELECT bc_id FROM #__blogs_cats WHERE bc_alias='".$alias."'";
			break;
			case "list":
				$sql="SELECT b_id FROM #__blogs WHERE b_alias='".$alias."'";
			break;
			case "post":
				$sql="SELECT p_id FROM #__blogs_posts WHERE p_alias='".$alias."'";
			break;
			default:
				return 0;
			break;
		}
		Database::getInstance()->setQuery($sql);
		return intval(Database::getInstance()->loadResult());
	}
	public function getAliasByID($view,$psid){
		switch($view){
			case "category":
				$sql="SELECT bc_alias FROM #__blogs_cats WHERE bc_id='".$psid."'";
			break;
			case "list":
				$sql="SELECT b_alias FROM #__blogs WHERE b_id='".$psid."'";
			break;
			case "post":
				$sql="SELECT p_alias FROM #__blogs_posts WHERE p_id='".$psid."'";
			break;
			default:
				return "";
			break;
		}
		Database::getInstance()->setQuery($sql);
		return Database::getInstance()->loadResult();
	}
} 
?>
