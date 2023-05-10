<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_WIDGET_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class blogcalendarWidget extends Widget {
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("blog_id", "table_select", 0, false, "SELECT b_id AS fld_id, b_name AS fld_name FROM #__blogs ORDER BY fld_name");
		$this->addParam("menu_id", "table_select", 0, false, "SELECT mi_id AS fld_id, CONCAT('(',mi_id,') ',mi_name) AS fld_name FROM #__menus ORDER BY fld_name");
	}
	public function render() {
		$blog_id = $this->getParam('blog_id');
		if (!$blog_id) return Text::_("Undefined blog");
		if ($this->getParam('menu_id') && seoConfig::$useMidInMenuLinks) $mid="&mid=".$this->getParam('menu_id'); else $mid="";
		$route_vars=Router::getInstance()->getVarsArr();
		$body="";
		$psid=0;
		$postDates=array("postStartDate"=>false,"postEndDate"=>false);
		if (isset($route_vars["module"]) && $route_vars["module"]=="blog" && isset($route_vars["view"]) && $route_vars["view"]=="list" && isset($route_vars["psid"]) && $route_vars["psid"]){
			$psid=$route_vars["psid"];
			$postDates = Module::getHelper("post","blog")->getDates($route_vars["psid"],$postDates);
		}
		if(!$postDates["postStartDate"] && !$postDates["postEndDate"]){
			$postDates["postStartDate"]=date("Y-m-01")." 00:00:00";
			$postDates["postEndDate"]=date("Y-m-t")." 23:59:59";
		}
		$time=Date::mysqldatetime_to_timestamp($postDates["postStartDate"]);
		if(!$psid) $psid = $blog_id;
/*
		if ($postDates["postStartDate"] && $psid){
			$time=Date::mysqldatetime_to_timestamp($postDates["postStartDate"]);
		} else {
			$time=time();
			$psid = $blog_id;
		}
*/		
		$starYear = date("Y",$time);
		$starMonth = date("m",Date::mysqldatetime_to_timestamp($postDates["postStartDate"]));
		
		$blog_alias = Module::getHelper("blog","blog")->getAliasByID("list",$blog_id);
		$link=Router::_("index.php?module=blog&view=list&psid=".$blog_id."&alias=".$blog_alias.$mid,false,false);
		$dates_arr=Module::getHelper("post","blog")->getPostsDates($blog_id, $starYear, $starMonth);
		$script="var blogEnabledDates=".json_encode($dates_arr).";
				$(document).ready(function() {
					$('#blogCalendar').datepicker({
						beforeShow: getPostDatesForMonth(".$psid.",'".$starYear."','".$starMonth."'),
						dateFormat: 'yy-mm-dd',
						beforeShowDay:  disableBlogCalendarDates, 
						onChangeMonthYear:  function(year, month, inst) { getPostDatesForMonth(".$psid.",year,month); },
						onSelect: function(date){
							setBlogCalendarCookie(".$psid.",date,date);
							window.location.href = '".$link."';
						}
					});";
		if(Date::fromSQL($postDates["postStartDate"],true)==Date::fromSQL($postDates["postEndDate"],true)) $script.=	"$('#blogCalendar').datepicker('setDate','".$postDates["postStartDate"]."');";				
		$script.="}); ";
		Portal::getInstance()->addScriptDeclaration($script);
		$body.="<div id=\"blogCalendar\"></div>";
		$body.="<p class=\"calendar_show_all\"><a onclick=\"resetBlogCalendarCookie(".$psid.")\" href=\"".$link."\">".Text::_("Show all")."</a></p>";
		return $body;
	}
}
?>