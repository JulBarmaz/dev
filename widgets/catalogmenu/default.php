<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_WIDGET_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogmenuWidget extends Widget {
	protected $_hide_content_param=true;
	protected $_requiredModules = array("catalog");
	
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("Collapse_widget", "select", 0, false, array(1=>Text::_("Accordeon"), 2=>Text::_("Full screen")), Text::_("Mobile devices"));
		$this->addParam("Collapsed_widget_title", "string", "", false, null, Text::_("Mobile devices"));
		$this->addParam("Hide_main_title_on_mobiles", "boolean", 1);
		$this->addParam("Menu_ID", "string", "");
		$this->addParam("UL_class", "string", "", false, null, Text::_("Variants").":<br />1) ".Text::_("Empty")."<br />2) navbar-nav<br />3) nav-pills nav-stacked<br />4) nav nav-pills nav-stacked");
		$this->addParam("Order_by", "select", "ggr_name", false, array(array("id"=>"ggr_name","name"=>Text::_("Name")),array("id"=>"ggr_ordering","name"=>Text::_("Ordering"))));
		$this->addParam("Render_type", "select", 0, false, array("1"=>Text::_("Javascript tree")." (".Text::_("Deprecated").")", "2"=>Text::_("Dropdown"), "3"=>Text::_("Static menu"), "4"=>Text::_("Accordeon")));
		$this->addParam("Hover_dropdown", "boolean", 0, false, null, Text::_("Variants").":<br />".Text::_("Dropdown")." (".Text::_("Except mobiles").")");
		$this->addParam("Menu_data_toggle_separate", "boolean", 0, false, null, Text::_("Variants").":<br />".Text::_("Dropdown")."<br />".Text::_("Accordeon"));
		$this->addParam("Expand_active", "boolean", 0, false, null, Text::_("Variants").":<br />".Text::_("Dropdown")."(".Text::_("Except mobiles").")");
		// $this->addParam("Expand_behaviour", "select", "location", false, array(array("id"=>"cookie","name"=>Text::_("By last expanded")),array("id"=>"location","name"=>Text::_("Current location"))));
		$this->addParam("Exclude_groups_ids", "multiselect", "0", false, $this->getGroupsItems());
		// Next line is for example of using another source for "Exclude_groups_ids"
		//$this->addParam("Exclude_groups_ids", "table_multiselect", 0, false, "SELECT ggr_id AS fld_id, CONCAT(ggr_name, ' (',ggr_id,')') AS fld_name FROM c_goods_group ORDER BY fld_name");
		$this->addParam("Max_levels", "integer", 5);
		$this->addParam("Root_id", "select", "0", false, $this->getGroupsItems());
		// $this->addParam("Reset_filter_on_click", "boolean", 0);
		$this->addParam("Use_controller", "select", "default", false, $this->getControllers());
		$this->addParam("Title", "title", Text::_("Buttons"));
		$this->addParam("Cart_button", "boolean", 0);
		$this->addParam("Filter_button", "boolean", 0);
		$this->addParam("Vendors_button", "boolean", 0);
		$this->addParam("Manufacturers_button", "boolean", 0);
		$this->addParam("Favourites_button", "boolean", 0);
		$this->addParam("Compare_button", "boolean", 0);
	}
	public function prepare() {
		$aw_id = $this->getParam('aw_id');
		$widget_id = $this->getParam('Widget_ID', false, "widget_".$this->getParam("aw_id"));
		$ul_id = $this->getParam('Menu_ID', false, ($widget_id ? $widget_id."_mnu" : ""));
		$render_type = intval($this->getParam('Render_type'));
		$toggle_separate = intval($this->getParam('Menu_data_toggle_separate'));
		
		if(!$render_type) $render_type=1;
		if($ul_id) {
			$script="$(document).ready(function(){";
			switch ($render_type){
				case "3":
					if(Request::getSafe("module")=="catalog"){
						$script.="
						if ($.cookie('BARMAZ_catalog_group')!=null){
							var curr_catalog_group_".$aw_id." = '".$ul_id."_item_' + $.cookie('BARMAZ_catalog_group');
							$('#".$ul_id." li').removeClass('active');
							$('#".$ul_id." li#' + curr_catalog_group_".$aw_id.").addClass('active');
							$('#".$ul_id." li#' + curr_catalog_group_".$aw_id." + '>a').addClass('active').addClass('selected').parents('ul, li').addClass('active');
						}
						";
					}
					break;
				case "2":
				case "4":
					$script.="$('#".$ul_id.".nav li>ul').parent('li').addClass('has_childs');";
					$script.="$('#".$ul_id.".nav li.has_childs>ul').addClass('dropdown-menu');";
					if($toggle_separate){
						$script.= "$('#".$ul_id.".nav li.has_childs').addClass('dropdown');";
						$script.= "$('#".$ul_id.".nav li.has_childs>a').addClass('dropdown-toggle');";
						$script.= "$('#".$ul_id.".nav li.has_childs').prepend('<div class=\"mnu-toggler\"></div>');";
						$script.= "$('#".$ul_id.".nav li.has_childs .mnu-toggler').attr('data-toggle', 'dropdown');";
						$script.= "$('#".$ul_id.".nav li.has_childs>a').prepend('<span class=\"mnu-toggler-caret mnu-toggler-caret-active\"></span>');";
						/****************************************************************************/
						$script.= '$("#'.$ul_id.' .mnu-toggler-caret-active").click(function(){';
						if($this->getParam('Hover_dropdown') && $render_type == 2){
							$script.= '    if (window.matchMedia("(min-width: 768px)").matches){ return true; }';
						}
						$script.= '    $(this).parents("a").siblings(".mnu-toggler").trigger("click");';
						$script.= '    return false;';
						$script.= '});';
						//}
					} else {
						$script.= "$('#".$ul_id.".nav li.has_childs').addClass('dropdown');";
						$script.= "$('#".$ul_id.".nav li.has_childs>a').addClass('dropdown-toggle');";
						$script.= "$('#".$ul_id.".nav li.has_childs>a').attr('data-toggle', 'dropdown');";
						$script.= "$('#".$ul_id.".nav li.has_childs>a').prepend('<span class=\"mnu-toggler-caret\"></span>');";
					}
					$script.= "$('#".$ul_id.".nav ul.dropdown-menu [data-toggle=dropdown]').on('click', function(event) {";
					$script.= "    event.preventDefault();";
					$script.= "    event.stopPropagation();";
					$script.= "    if($(this).parent().hasClass('open')) {";
					$script.= "        $(this).parent().removeClass('open');";
					$script.= "        $(this).closest('ul').find('li').removeClass('open');";
					$script.= "    } else {";
					$script.= "        $(this).parent().siblings().removeClass('open');";
					$script.= "        $(this).parent().addClass('open');";
					$script.= "    }";
					$script.= "});";
					if(Request::getSafe("module")=="catalog"){
						$script.="
							if ($.cookie('BARMAZ_catalog_group')!=null){
								var curr_catalog_group_".$aw_id." = '".$ul_id."_item_' + $.cookie('BARMAZ_catalog_group');
								$('#".$ul_id." li').removeClass('active');
								$('#".$ul_id." li#' + curr_catalog_group_".$aw_id.").addClass('active');
								$('#".$ul_id." li#' + curr_catalog_group_".$aw_id." + '>a').addClass('active').addClass('selected').parents('ul, li').addClass('active');
							}
							";
					}
					if($this->getParam('Expand_active') && $this->getParam('Render_type') == 4){
						$script.= '    $("#'.$ul_id.' .active").addClass("open");';
					}
					break;
				case "1":
				default:	
					Portal::getInstance()->addScript("/redistribution/jquery.plugins/jquery.treeview.js");
					// $persist = $this->getParam('Expand_behaviour');
					$persist = "location";
					Portal::getInstance()->AddScriptDeclaration('$(document).ready(function(){ $("#'.$ul_id.'").treeview({ animated: "fast", collapsed: true, unique: true, persist: "'.$persist.'" }); });');
					if(Request::getSafe("module")=="catalog"){
					//if(Request::getSafe("module")=="catalog" && $persist=="location"){
						$script.="
						if ($.cookie('BARMAZ_catalog_group')!=null){
							var curr_catalog_group_".$aw_id." = '".$ul_id."_item_'+$.cookie('BARMAZ_catalog_group');
							$('#".$ul_id." li').removeClass('active');
							$('#".$ul_id." li#' + curr_catalog_group_".$aw_id.").addClass('active');
							$('#".$ul_id." li#' + curr_catalog_group_".$aw_id." + '>a').addClass('active').addClass('selected').parents('ul, li').show();
						}
						";
					}
					break;
			}
			/*
			if($this->getParam('Reset_filter_on_click')){
				$script.="
					$('#".$ul_id." li:not(.has_childs)>a').on('click', function(e){
						var href = $(this).attr('href');
						e.preventDefault();
						e.stopPropagation();
						ajaxShowActivity();
						$.ajax({
							url : siteConfig['siteUrl']+'index.php',
							data:({
								type:'module',
								module:'catalog',
								view: 'goods',
								option:'ajax',
								full_reset: 1,
								task:'resetfilter'
							}),
							dataType:'json',
							success: function (data, textStatus) {
								if (data.result=='OK') document.location=href;
								ajaxHideActivity();
							},
							error: function (qq) { 
								ajaxHideActivity(); 
								return false;
							}
						});
					});
				";
			}
			*/
			$script.="});";
			Portal::getInstance()->AddScriptDeclaration($script);
			switch($this->getParam('Collapse_widget')){
				case "1":
					break;
				case "2":
					Portal::getInstance()->AddScriptDeclaration('
						$(document).ready(function(){
							$("#'.$widget_id.' .navbar-header button").bind("click", function(){
								$("body").addClass("overlaid");
								$("#'.$widget_id.'_wrapper").removeClass("navbar-popup-fullscreen-off").addClass("navbar-popup-fullscreen-on");
							});
							$("#'.$widget_id.' .navbar-popup-fullscreen button").bind("click", function(){
								$("body").removeClass("overlaid");
								$("#'.$widget_id.'_wrapper").removeClass("navbar-popup-fullscreen-on").addClass("navbar-popup-fullscreen-off");
							});
						});
					');
					break;
				default:
					break;
			}
			if($this->getParam('Hide_main_title_on_mobiles')){
				Portal::getInstance()->addStyle("@media (max-width: 767px){#".$widget_id." .wTitle {display:none !important;}}");
			}
		}
	}
	public function render() {
		if (catalogConfig::$catalogDisabled) {
			$menuHTML=""; return $menuHTML;
		}
		$widget_id = $this->getParam('Widget_ID', false, "widget_".$this->getParam("aw_id"));
		$ul_id = $this->getParam('Menu_ID', false, ($widget_id ? $widget_id."_mnu" : ""));
		$render_type = intval($this->getParam('Render_type'));
		if(!$render_type) $render_type=1;
		$_ul_class = $this->getParam('UL_class');
		if($render_type == 2 || $render_type == 4){
			$_ul_class.= ($_ul_class ? " " : "")."nav"; 
		}
		if($render_type == 2){
			$_ul_class.= ($this->getParam('Hover_dropdown') ? " hover-dropdown" : "");
		}
		$exc_grp_ids = $this->getParam('Exclude_groups_ids');
		$exc_grp_ids=implode(",", explode(";", $exc_grp_ids));
		$max_levels = $this->getParam('Max_levels');
		$root_id = $this->getParam('Root_id');
		$filter_button=$this->getParam('Filter_button');
		$cart_button=$this->getParam('Cart_button');
		$vendors_button=$this->getParam('Vendors_button');
		$manufacturers_button=$this->getParam('Manufacturers_button');
		$favourites_button=$this->getParam('Favourites_button');
		$compare_button=$this->getParam('Compare_button');
		$ordering=$this->getParam('Order_by');
		$model = $this->getGroupsTree($ordering,$max_levels,$exc_grp_ids);
		$widgetHTML = "";
		$menuHTML = "<div class=\"catalog-tree-wrapper tree-wrapper menu-render-type-".$render_type."\">".$model->getTreeHTML($root_id, 'ul', $ul_id, "treeview", 0, $ul_id."_item", $_ul_class)."</div>";
		if ($filter_button||$cart_button||$manufacturers_button){
			if(Module::getInstance()->getName()=="catalog") $controller = Module::getInstance("catalog")->get("controller");
			else $controller = "";
			$menuHTML.= "<div class=\"catalog-tree-buttons\">";
			if ($filter_button) $menuHTML.="<div class=\"filter_button".($cart_button ? "" : " width-100")."\"><a class=\"linkButton btn btn-info\" onclick=\"javascript:showFilter('catalog','goods','','0', '".Text::_("Filter")."', 0, '".$controller."'); return false;\" title=\"".Text::_("Filter")."\">".Text::_("Filter")."</a></div>";
			if ($cart_button) $menuHTML.="<div class=\"cart_button".($filter_button ? "" : " width-100")."\"><a rel=\"nofollow\" class=\"relpopupwt linkButton btn btn-info\" href=\"".Router::_("index.php?module=catalog&amp;task=ShowBasket&amp;option=ajax")."\" title=\"".Text::_("Basket")."\">".Text::_("Basket")."</a></div>";
			if ($vendors_button) $menuHTML.="<div class=\"vendors_button\"><a class=\"linkButton btn btn-info\" href=\"".Router::_("index.php?module=catalog&amp;view=vendors")."\">".Text::_("Vendors")."</a></div>";
			if ($manufacturers_button) $menuHTML.="<div class=\"manufacturers_button\"><a class=\"linkButton btn btn-info\" href=\"".Router::_("index.php?module=catalog&amp;view=manufacturers")."\">".Text::_("Manufacturers")."</a></div>";
			if ($favourites_button && Module::getInstance("catalog")->getParam("enable_favourites_goods")) $menuHTML.="<div class=\"favourites_button\"><a rel=\"nofollow\" class=\"linkButton btn btn-info\" href=\"".Router::_("index.php?module=catalog&amp;task=favourites")."\">".Text::_("Favourites list")."</a></div>";
			if ($compare_button && Module::getInstance("catalog")->getParam("enable_compare_goods")) $menuHTML.="<div class=\"compare_button\"><a rel=\"nofollow\" class=\"linkButton btn btn-info\" href=\"".Router::_("index.php?module=catalog&amp;task=compare")."\">".Text::_("Compare list")."</a></div>";
			$menuHTML.= "</div>";
		}
		switch($this->getParam('Collapse_widget')){
			case "1":
				$adaptive_menu_title = $this->getParam('Collapsed_widget_title');
				$widgetHTML.= "<div class=\"navbar-header\"><span class=\"topmenu_label visible-xs\">".$adaptive_menu_title."</span>";
				$widgetHTML.= "<button type=\"button\" class=\"btn btn-navbar navbar-toggle\" data-toggle=\"collapse\" data-target=\"#".$widget_id."_wrapper\"><i class=\"glyphicon glyphicon-menu-hamburger\"></i></button>";
				$widgetHTML.= "</div>";
				$widgetHTML.= "<div id=\"".$widget_id."_wrapper\" class=\"collapse navbar-collapse\">".$menuHTML."</div>";
				break;
			case "2":
				$adaptive_menu_title = $this->getParam('Collapsed_widget_title');
				$widgetHTML.= "<div class=\"navbar-header\"><span class=\"topmenu_label visible-xs\">".$adaptive_menu_title."</span>";
				$widgetHTML.= "<button type=\"button\" class=\"btn btn-navbar navbar-toggle\" data-toggle=\"popup\" data-target=\"#".$widget_id."_wrapper\"><i class=\"glyphicon glyphicon-menu-hamburger\"></i></button>";
				$widgetHTML.= "</div>";
				$widgetHTML.= "<div id=\"".$widget_id."_wrapper\" class=\"navbar-popup-fullscreen navbar-popup-fullscreen-off\">";
				$widgetHTML.= "<button type=\"button\" class=\"btn close\" data-toggle=\"popup\" data-target=\"#".$widget_id."_wrapper\"><i class=\"glyphicon glyphicon-remove\"></i></button>";
				$widgetHTML.= $menuHTML;
				$widgetHTML.= "</div>";
				break;
			default:
				$widgetHTML = $menuHTML;
				break;
		}
		return $widgetHTML;
	}
	protected function getGroupsItems(){
		$data = array();
		$model = $this->getGroupsTree("ggr_ordering", 3, "");
		foreach($model->getTreeArr() as $key=>$val){
			$data[]=array("id"=>$val->id, "name"=>str_repeat("- ", $val->level-1).$val->title);
		}
		return $data;
	}
	protected function getGroupsTree($ordering,$max_levels,$exc_grp_ids){
		$model = new simpleTreeTable();
		// $controller	= Request::get('controller','default');
		$controller	= $this->getParam('Use_controller');
		$model->table="goods_group";
		$model->fld_id="ggr_id";
		$model->fld_parent_id="ggr_id_parent";
		$model->fld_title="ggr_name";
		$model->fld_alias="ggr_alias";
		$model->fld_enabled="ggr_enabled";
		$model->fld_deleted="ggr_deleted";
		$model->fld_orderby=$ordering;
		$model->element_link="index.php?module=catalog".($controller ? "&amp;controller=".$controller : "")."&amp;view=goods&amp;psid=";
		$model->buildTreeArrays($exc_grp_ids, 0, 1, 1, $max_levels);
		return $model;
	}
	protected function getControllers(){
		$files = array();
		if(defined("_ADMIN_MODE")) $path=PATH_FRONT_MODULES."catalog".DS."controllers";
		else $path=PATH_MODULES."catalog".DS."controllers";
		$vfiles=Files::getFiles($path,false,false);
		foreach ($vfiles as $value) {
			$filename = basename($value["filename"],".php");
			$files[$filename]=$filename;
		}
		return $files;
	}
}
?>