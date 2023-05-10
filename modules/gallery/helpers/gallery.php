<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class galleryHelperGallery {
	public function getIdByAlias($view,$alias){
		switch($view){
			case "items":
				$sql="SELECT gr_id FROM #__gallery_groups WHERE gr_alias='".$alias."'";
			break;
			case "images":
				$sql="SELECT g_id FROM #__galleries WHERE g_alias='".$alias."'";
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
