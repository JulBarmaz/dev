<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class Restructure_2360{
	public static function proceed(&$messages, &$errors) {
		// Let's make something with database

		$query = "";

		$widgetName = "blogpost";
		$sql = "SELECT * FROM `#__widgets_active` WHERE `aw_name`='".$widgetName."'".Database::getInstance()->getDelimiter();
		Database::getInstance()->setQuery($sql);
		$widgets = Database::getInstance()->loadObjectList("aw_id");
		$w_params = Widget::getInstance($widgetName)->getParamsMask();
		if(count($widgets)){
			foreach($widgets as $w_id=>$wdata){
				$params = Params::parse($wdata->aw_config);
				$params["blog_Id"] = explode(",", $params["blog_Id"]);
				$params["blog_Id_ex"] = explode(",", $params["blog_Id_ex"]);
				$aw_config = Params::intersect($params, $w_params);
				$query.= "UPDATE #__widgets_active SET aw_config='".$aw_config."' WHERE aw_id=".$w_id.Database::getInstance()->getDelimiter();
			}
		}

		$widgetName = "forumpost";
		$sql = "SELECT * FROM `#__widgets_active` WHERE `aw_name`='".$widgetName."'".Database::getInstance()->getDelimiter();
		Database::getInstance()->setQuery($sql);
		$widgets = Database::getInstance()->loadObjectList("aw_id");
		$w_params = Widget::getInstance($widgetName)->getParamsMask();
		if(count($widgets)){
			foreach($widgets as $w_id=>$wdata){
				$params = Params::parse($wdata->aw_config);
				$params["forum_Id"] = explode(",", $params["forum_Id"]);
				$params["forum_Id_ex"] = explode(",", $params["forum_Id_ex"]);
				$aw_config = Params::intersect($params, $w_params);
				$query.= "UPDATE #__widgets_active SET aw_config='".$aw_config."' WHERE aw_id=".$w_id.Database::getInstance()->getDelimiter();
			}
		}
		
		Database::getInstance()->setQuery($query);
/*
		Util::pre(Database::getInstance()->getQuery());
		$errors[]=Text::_("Error applying restructure")." (x01): ".__CLASS__.": ".__FUNCTION__;
		return false;
*/
		if(!Database::getInstance()->query_batch()){
			$errors[]=Text::_("Error applying restructure")." (x01): ".__CLASS__.": ".__FUNCTION__;
			$errors[]=Database::getInstance()->getLastError()." (x01): ".__CLASS__.": ".__FUNCTION__;
			return false;
		}

		// Returning message if successed
		$messages[]=__CLASS__;
		return true;
		
	}
}
?>