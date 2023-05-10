<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_WIDGET_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class menuWidget extends Widget {
	protected $_hide_content_param = true;
	private $data_id_attr = "data-id";
	private $data_canonical_attr = "data-canonical-id";
	
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("Collapse_widget", "select", 0, false, array(1=>Text::_("Accordeon"), 2=>Text::_("Full screen")), Text::_("Mobile devices"));
		$this->addParam("Collapsed_widget_title", "string", "", false, null, Text::_("Mobile devices"));
		$this->addParam("Hide_main_title_on_mobiles", "boolean", 1);
		$this->addParam("Render_type", "select", 0, false, array("1"=>Text::_("Javascript tree")." (".Text::_("Deprecated").")", "2"=>Text::_("Dropdown"), "3"=>Text::_("Static menu"), "4"=>Text::_("Accordeon")));
		$this->addParam("Hover_dropdown", "boolean", 0, false, null, Text::_("Variants").":<br />".Text::_("Dropdown")." (".Text::_("Except mobiles").")");
		$this->addParam("Expand_active", "boolean", 0, false, null, Text::_("Variants").":<br />".Text::_("Dropdown")."(".Text::_("Except mobiles").")");
		$this->addParam("Menu_ID", "string", "");
		$this->addParam("Menu_class", "string", "", false, null, Text::_("Variants").":<br />1) ".Text::_("Empty")."<br />2) navbar-nav<br />3) nav-pills nav-stacked<br />4) nav nav-pills nav-stacked");
		$this->addParam("Menu_li_class", "string", "");
		$this->addParam("Menu_a_class", "string", "");
		$this->addParam("Menu_ul_class", "string", "");
		$this->addParam("Menu_data_toggle_separate", "boolean", 0, false, null, Text::_("Variants").":<br />".Text::_("Dropdown")."<br />".Text::_("Accordeon"));
		$this->addParam("Menu_root_id", "select", "", false, $this->getTopItems());
		$this->addParam("Menu_max_levels", "integer", 0);
		$this->addParam("Hide_menu_titles", "boolean", 0);
		$this->addParam("Translate", "boolean", 0);
	}
	public function prepare() {
		$widget_id = $this->getParam('Widget_ID');
		$ul_id = $this->getParam('Menu_ID', false, ($widget_id ? $widget_id."_mnu" : ""));
		$li = $ul_id."_item_".$_SESSION['active_menu_id'];
		$li_canonical = "li[".$this->data_canonical_attr."='".$_SESSION['active_menu_id']."']";
		if($ul_id) {
			switch($this->getParam('Render_type')){
				case "1":
					Portal::getInstance()->addScript("/redistribution/jquery.plugins/jquery.treeview.js");
					$persist = "location";
					Portal::getInstance()->AddScriptDeclaration('
						$(document).ready(function(){
							$("#'.$ul_id.'").addClass("tree_menu");
							$("#'.$ul_id.'").treeview({  animated: "fast", collapsed: true, unique: true, persist: "'.$persist.'" });
						});
					');
					break;
				case "2":
					$script = '$(document).ready(function(){';
					if($this->getParam('Menu_data_toggle_separate')){
						$script.= '    $("#'.$ul_id.' .mnu-toggler-caret-active").click(function(){';
						if($this->getParam('Hover_dropdown')){
							$script.= '    if (window.matchMedia("(min-width: 768px)").matches){ return true; }';
						}
						$script.= '        $(this).parents("a").siblings(".mnu-toggler").trigger("click");';
						$script.= '        return false;';
						$script.= '    });';
					}
					$script.= '    $("#'.$ul_id.'.nav ul.dropdown-menu [data-toggle=dropdown]").on("click", function(event) {';
					$script.= '        event.preventDefault();';
					$script.= '        event.stopPropagation();';
					$script.= '        if($(this).parent().hasClass("open")) {';
					$script.= '            $(this).parent().removeClass("open");';
					$script.= '            $(this).closest("ul").find("li").removeClass("open");';
					$script.= '        } else {';
					$script.= "            $(this).parent().siblings().removeClass('open');";
					$script.= '            $(this).parent().addClass("open");';
					$script.= '        }';
					$script.= '    });';
					$script.= '});';
					Portal::getInstance()->AddScriptDeclaration($script);
					break;
				case "3":
					break;
				case "4":
					$script = '$(document).ready(function(){';
					if($this->getParam('Menu_data_toggle_separate')){
						$script.= '    $("#'.$ul_id.' .mnu-toggler-caret-active").click(function(){';
						$script.= '        $(this).parents("a").siblings(".mnu-toggler").trigger("click");';
						$script.= '        return false;';
						$script.= '    });';
					}
					$script.= '    $("#'.$ul_id.'.nav ul.dropdown-menu [data-toggle=dropdown]").on("click", function(event) {';
					$script.= '        event.preventDefault();';
					$script.= '        event.stopPropagation();';
					$script.= '        if($(this).parent().hasClass("open")) {';
					$script.= '            $(this).parent().removeClass("open");';
					$script.= '            $(this).closest("ul").find("li").removeClass("open");';
					$script.= '        } else {';
					$script.= "            $(this).parent().siblings().removeClass('open');";
					$script.= '            $(this).parent().addClass("open");';
					$script.= '        }';
					$script.= '    });';
					$script.= '});';
					
					Portal::getInstance()->AddScriptDeclaration($script);
					break;
				default:
					break;
					
			}
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
			// Here we should consider mi_canonical_id (data-canonical-id)
			$script = '$(document).ready(function(){';
			$script.= '    $("#'.$ul_id.' li").removeClass("active");';
			$script.= '    $("#'.$ul_id.' li#'.$li.'").addClass("active");';
			$script.= '    $("#'.$ul_id.' li#'.$li.'").parents("li").addClass("active has_active_child");';
			
			$script.= '    $("#'.$ul_id.' '.$li_canonical.'").addClass("active");';
			$script.= '    $("#'.$ul_id.' '.$li_canonical.'").parents("li").addClass("active has_active_child");';
			
			if($this->getParam('Expand_active') && $this->getParam('Render_type') == 4){
				$script.= '    $("#'.$ul_id.' .active").addClass("open");';
			}
			$script.= '});';
			Portal::getInstance()->AddScriptDeclaration($script);
		}
		if($this->getParam('Hide_main_title_on_mobiles')){
			Portal::getInstance()->addStyle("@media (max-width: 767px){#".$widget_id." .wTitle {display:none !important;}}");
		}
	}
	public function render() {
		$widgetHTML="";
		$widget_id = $this->getParam('Widget_ID', false, "widget_".$this->getParam("aw_id"));
		$ul_id = $this->getParam('Menu_ID', false, ($widget_id ? $widget_id."_mnu" : ""));
		$ul_hide_menu_titles = $this->getParam('Hide_menu_titles');
		$ul_class = $this->getParam('Menu_class').($ul_hide_menu_titles ? " hide_titles" : "");
		$ul_li_class = $this->getParam('Menu_li_class');
		$ul_ul_class = $this->getParam('Menu_ul_class');
		$ul_a_class = $this->getParam('Menu_a_class');
		$data_toggle = "";
		$data_toggle_separate = $this->getParam('Menu_data_toggle_separate');
		$root_id = $this->getParam('Menu_root_id');
		$translate = $this->getParam('Translate');
		$max_levels= $this->getParam('Menu_max_levels');
		switch($this->getParam('Render_type')){
			case "1":

				break;
			case "2":
				$ul_class = "nav".($ul_class ? " " : "").$ul_class.($this->getParam('Hover_dropdown') ? " hover-dropdown" : "");
				$ul_li_class = "dropdown".($ul_li_class ? " " : "").$ul_li_class;
				$ul_a_class = "dropdown-toggle".($ul_a_class ? " " : "").$ul_a_class;
				$ul_ul_class = "dropdown-menu".($ul_ul_class ? " " : "").$ul_ul_class;
				$data_toggle = "dropdown".($data_toggle ? " " : "").$data_toggle;
				break;
			case "3":

				break;
			case "4":
				$ul_class = "nav".($ul_class ? " " : "").$ul_class;
				$ul_li_class = "dropdown".($ul_li_class ? " " : "").$ul_li_class;
				$ul_a_class = "dropdown-toggle".($ul_a_class ? " " : "").$ul_a_class;
				$ul_ul_class = "dropdown-menu".($ul_ul_class ? " " : "").$ul_ul_class;
				$data_toggle = "dropdown".($data_toggle ? " " : "").$data_toggle;
				/*
				$ul_class.= ($ul_class ? " " : "")."nav";
				$ul_li_class.= ($ul_li_class ? " " : "")."dropdown";
				$ul_a_class.= ($ul_a_class ? " " : "")."dropdown-toggle";
				$ul_ul_class.= ($ul_ul_class ? " " : "")."dropdown-menu";
				$data_toggle.= ($data_toggle ? " " : "")."dropdown";
				*/
				break;
			default:
				
				break;
				
		}
		if ($root_id) {
			$model = new Menus($ul_class, $ul_li_class, $ul_a_class, $ul_ul_class, $data_toggle, $data_toggle_separate);
			$menuHTML = $model->render($root_id, $ul_id, $translate, $max_levels);
			$menuHTML = "<div class=\"menu-tree-wrapper tree-wrapper menu-render-type-".$this->getParam('Render_type')."\">".$menuHTML."</div>";
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
					//$widgetHTML.= "<div class=\"float-fix\"><button type=\"button\" class=\"btn close\" data-toggle=\"popup\" data-target=\"#".$widget_id."_wrapper\"><i class=\"glyphicon glyphicon-remove\"></i></button></div>";
					$widgetHTML.= "<button type=\"button\" class=\"btn close\" data-toggle=\"popup\" data-target=\"#".$widget_id."_wrapper\"><i class=\"glyphicon glyphicon-remove\"></i></button>";
					$widgetHTML.= $menuHTML;
					$widgetHTML.= "</div>";
					break;
				default:
					$widgetHTML = $menuHTML;
					break;
			}
			
		}
		return $widgetHTML;
	}
	public function getTopItems(){
		if(!defined('_ADMIN_MODE')) return null;
		$sql="SELECT mi_id AS id, mi_name AS name FROM #__menus WHERE mi_parent_id=0 ORDER by mi_ordering";
		Database::getInstance()->setQuery($sql);
		return Database::getInstance()->loadObjectList();
	}
}

?>