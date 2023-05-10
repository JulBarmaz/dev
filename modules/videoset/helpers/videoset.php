<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class videosetHelperVideoset {
	public function getIdByAlias($view,$alias,$layout=''){
		switch($view){
			case "items":
				$sql="SELECT vgr_id FROM #__videoset_groups WHERE vgr_alias='".$alias."'";
			break;
			case "video":
				$sql="SELECT v_id FROM #__videoset_videos WHERE v_alias='".$alias."'";
			break;
			case "videos":
				$sql="SELECT vg_id FROM #__videoset_galleries WHERE vg_alias='".$alias."'";				
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
