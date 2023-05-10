<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_PLUGIN_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class systemPluginkcaptcha extends Plugin {
	protected $_events=array("captcha.renderPicture","captcha.renderForm","captcha.checkResult");	
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("length", "integer", 7, true);
		$this->addParam("height", "integer", 60, true);
		$this->addParam("width", "integer", 120, true);
	}
	protected function onRaise($event, &$data) {
		switch($event){
			case "captcha.renderPicture":
				include_once PATH_INCLUDES.DS.'kcaptcha'.DS.'kcaptcha.php';
				$params=array();
				if(($this->getParam("length")) && ($this->getParam("length")>2) && ($this->getParam("length")<8)) $params["length"]=$this->getParam("length");
				if(($this->getParam("height")) && ($this->getParam("height")>0)) $params["height"]=$this->getParam("height");
				if(($this->getParam("width")) && ($this->getParam("width")>0)) $params["width"]=$this->getParam("width");
				$captcha = new KCAPTCHA($params);
				if($_REQUEST[session_name()]){ $_SESSION['captcha_string'] = $captcha->getKeyString();	}
			break;
			case "captcha.renderForm":
				$html = "<div class=\"captcha\">";
				$html .= "<table class=\"captcha\"><tr><td rowspan=\"2\" class=\"captcha\" width=\"".($this->getParam("width")+20)."\">";
				$html.= "	<img src=\"".Portal::getInstance()->getURI()."index.php?option=captcha&amp;".session_name()."=".session_id()."\" alt=\"\" />";
				$html.= "</td>";
				$html.= "<td class=\"labelcaptcha\">".Text::_("Enter code")."</td></tr>";
				$html.= "<tr><td class=\"captchainput\">";
				$html.= "	<input id=\"rCaptchaText\" class=\"required form-control\" name=\"rCaptchaText\" type=\"text\" />";
				$html.= "</td></tr></table>";
				$html.= "</div>";
				return $html;
			break;
			case "captcha.checkResult":
				$sess = Session::getInstance();
				$captcha	= Request::get('rCaptchaText','');
				if(isset($_SESSION['captcha_string'])) $captchaValid= $_SESSION['captcha_string']; else $captchaValid="";
				if (($captchaValid)&&($captcha == $captchaValid)) $msg="OK"; else $msg =Text::_("Wrong captcha code");
				$_SESSION['captcha_string']=$msg;
			break;
		}
	}
}

?>