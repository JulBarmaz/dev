<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_PLUGIN_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class userPluginagreeCookie extends Plugin {
	protected $_events = array("system.fetchFooterAfter");
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("button_text", "string", Text::_("I agree"), true);
		$this->addParam("warning_text", "text", Text::_("I agree to the collection of statistics"), true);
		$this->addParam("warning_template_text", "ro_string", Text::_("Template ###BUTTON_TEXT### will be replaced"));
		$this->addParam("warning_theme", "select", "blue", true, array("red"=>Text::_("Red"), "blue"=>Text::_("Blue"), "green"=>Text::_("Green")));
	}
	protected function onRaise($event, &$data) {
		$html="";
		if(defined('_ADMIN_MODE')) return true;
		$pressed = Request::getBool("consent4statistics",false,"cookie");
		if($pressed) return true;
		switch($event){
			case "system.fetchFooterAfter":
				$warning_text=$this->getParam("warning_text");
				$button_text=$this->getParam("button_text");
				$warning_text = str_replace("###BUTTON_TEXT###", $button_text, $warning_text);
				$theme = $this->getParam("warning_theme");
				switch($theme){
					case "red":
						$btn_class = "btn-warning";
					break;
					case "green":
						$btn_class = "btn-primary";
					break;
					case "blue":
					default:
						$theme = "blue";
						$btn_class = "btn-success";
					break;
				}
				
				$html.="<div class=\"consent4statistics theme-".$theme."\">";
				$html.="	<div class=\"container\">";
				$html.="		<div class=\"row\">";
				$html.="			<div class=\"col-sm-9\">";
				$html.="				<div class=\"warning_text\">";
				$html.="					<!--noindex-->";
				$html.=nl2br($warning_text);
				$html.="					<!--/noindex-->";
				$html.="				</div>";
				$html.="			</div>";
				$html.="			<div class=\"col-sm-3\">";
				$html.="				<div class=\"warning_button\">";
				$html.="					<a class=\"linkButton btn ".$btn_class."\" onclick=\"setConsent4statistics();\">".$button_text."</a>";
				$html.="				</div>";
				$html.="			</div>";
				$html.="		</div>";
				$html.="	</div>";
				$html.="</div>";
				break;
		}
		$data.=$html;
		return true;
	}
}
?>