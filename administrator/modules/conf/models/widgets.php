<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class confModelwidgets extends SpravModel {

	public function saveNewWidget($w_name){
		$db=Database::getInstance();
		$sql_txt="INSERT INTO #__widgets_active(aw_name,aw_title,aw_zone,aw_title_link,aw_class,aw_config,aw_access,aw_visible_in,aw_content) values ('".htmlspecialchars($w_name)."','".Text::_($w_name." widget")."','top-left','','','',0,'','')";
		$db->setQuery($sql_txt);
		if ($db->query()) $res=$db->insertid(); else $res=false;
		return $res;
	}
	public function getRequiredDisabledModules($widgetName){
		return Widget::getInstance($widgetName)->getRequiredDisabledModules();
	}
	public function getWidgetParams($widgetName){
		return Widget::getInstance($widgetName)->getParamsMask();
	}
	public function hideWidgetContentParam($widgetName){
		return Widget::getInstance($widgetName)->hideContentParam();
	}
	public function saveWidgetParams($w_id,$wp,$w_params,$w_access,$w_visibility) {
		if(isset($wp["Widget_ID"]) && !$wp["Widget_ID"]) $wp["Widget_ID"]="widget_".$w_id;
		$aw_config = Params::intersect($wp, $w_params);
		$query = "UPDATE #__widgets_active SET aw_config='".$aw_config
						."', aw_access='".$w_access
						."', aw_visible_in='".$w_visibility
						."' WHERE aw_id=".$w_id;
		$this->_db->setQuery($query);
		$this->_db->query();
	}
}

?>