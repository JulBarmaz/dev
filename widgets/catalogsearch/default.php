<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_WIDGET_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogsearchWidget extends Widget {
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("show_label", "boolean", 0);
		$this->addParam("show_button", "boolean", 0);
		$this->addParam("controller_name", "select", "default", false, $this->getControllers());
		$this->addParam("text_in_field", "string", Text::_("Search in catalog"));
	}
	public function prepare() {
		$minimum_search_length = Module::getInstance("catalog")->getParam("minimum_search_length");
		$widget_id = $this->getParam('Widget_ID');
		$form_id = $widget_id."_CatalogSearchFrm";
		$field_id = $widget_id."_CatalogSearchFld";
		$result_id = $widget_id."_CatalogSearchResults";
		$controller_name = $this->getParam('controller_name');
		
		$script="
			$(window).on('load',function() {
				$(document).mouseup(function (e){
					var div = $('#".$result_id."');
					var input = $('#".$field_id."');
					if (!div.is(e.target) // not our result block
						&& !input.is(e.target) // not our input
						&& div.has(e.target).length === 0) { // not our result block choildren
						div.hide();
					}
				});
				$('#".$field_id."').focus(function() {
					$('#".$result_id."').show(); // показываем его
				});
			});
			$(document).ready(function() {
				$('#".$form_id."').bind('submit', function() { if ($('#".$field_id."').val().length<".intval($minimum_search_length).") return false; });
				$('#".$field_id."').bind('keyup', function(e) {
					console.log(e.keyCode);
					var keyword = $('#".$field_id."').val()
					if (keyword.length >= ".$minimum_search_length.") {
						//ajaxShowActivity();
						$.ajax({
							url: siteConfig['siteUrl'] + 'index.php',
							data: ({
								type: 'module',
								option: 'ajax',
								module: 'catalog',
								controller: '".$controller_name."',
								task: 'liveSearch',
								kwds: keyword
							}),
							dataType: 'html',
							success: function(data, textStatus) {
								$('#".$result_id."').show();
								$('#".$result_id."').html(data);
								//ajaxHideActivity();
							},
							error: function() {
								ajaxHideActivity();
								return false;
							}
						});
					} else {
						$('#".$result_id."').html('');
					}
				});
			});";
		Portal::getInstance()->addScriptDeclaration($script);
	}
	public function render() {
		$show_label = $this->getParam('show_label');
		$show_button = $this->getParam('show_button');
		$text_in_field = $this->getParam('text_in_field');
		$controller_name = $this->getParam('controller_name');
		
		$widget_id = $this->getParam('Widget_ID');
		$form_id = $widget_id."_CatalogSearchFrm";
		$field_id = $widget_id."_CatalogSearchFld";
		$result_id = $widget_id."_CatalogSearchResults";
		$html= "<form action=\"".Router::_("index.php")."\" method=\"get\" id=\"".$form_id."\" name=\"wFrmCatalogSearch\">";
		$html.= "<div class=\"w_catalogsearchform".($show_button ? " search-with-button" : "")."\">";
		if ($show_label) $html.= HTMLControls::renderLabelField("kwds","Search phrase",1);
		$html.= HTMLControls::renderInputText("kwds","",30,"", $field_id, "form-control", false, false, "", [], $text_in_field);
		$html.= "<div class=\"w_catalogsearchresult\" id=\"".$result_id."\"></div>";
		if($show_button) {
			$html.= "<div class=\"w_catalogsearchform_button\">";
			$html.= HTMLControls::renderButton("submit", "", "submit", "submit", "");
			$html.= "</div>";
		}
		if($controller_name && $controller_name != "default") $html.= HTMLControls::renderHiddenField("controller", $controller_name);
		$html.= HTMLControls::renderHiddenField("module", "catalog");
		$html.= HTMLControls::renderHiddenField("view", "goods");
		$html.= "</div>";
		$html.= "</form>";
		return $html;
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