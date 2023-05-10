<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_PLUGIN_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class contentPluginyashare extends Plugin {
	protected $_events=array("share.article","share.blog","share.blogpost","share.goods","share.page");
	private $share_services=array("collections", "vkontakte", /* "facebook",*/ "odnoklassniki", "moimir", "gplus", "twitter", "blogger", "linkedin", "lj", "viber", "whatsapp", "skype", "telegram");
	private $enabled_services=array();

	protected function setParamsMask(){
		$this->enabled_services=$this->share_services;
		parent::setParamsMask();
		$this->addParam("share_page", "boolean", 1);
		$this->addParam("share_article", "boolean", 1);
		$this->addParam("share_blogpost", "boolean", 1);
		$this->addParam("share_blog", "boolean", 1);
		$this->addParam("share_goods", "boolean", 1);
		$this->addParam("srvstitle_1", "title", Text::_("Use services"));
		foreach ($this->share_services as $srvs){
			$this->addParam("srvs_".$srvs, "boolean", 1);
		}
	}

	protected function onRaise($event, &$data) {
		$html="";
		if(defined('_ADMIN_MODE')) return $html;
		$srvs=$this->getEnabledShareServices();
		if (!$this->getParam("share_article") && $event=="share.article") return $html;
		if (!$this->getParam("share_blogpost") && $event=="share.blogpost") return $html;
		if (!$this->getParam("share_blog") && $event=="share.blog") return $html;
		if (!$this->getParam("share_goods") && $event=="share.goods") return $html;
		if (!$this->getParam("share_page") && $event=="share.page") return $html;
		switch($event){
			case "share.article":
			case "share.blog":
			case "share.blogpost":
			case "share.goods":
			case "share.page":
				if (count($this->enabled_services)) {
					Portal::getInstance()->addScript("//yastatic.net/es5-shims/0.0.2/es5-shims.min.js", true);
					Portal::getInstance()->addScript("//yastatic.net/share2/share.js", true);
					$html='<div class="'.str_replace(".", "-", $event).' share-panel float-fix"><div class="ya-share2" data-services="'.$srvs.'"></div></div>';
				}
				break;
			default: break;
		}
		return $html;
	}
	private function getEnabledShareServices(){
		$this->enabled_services=array();
		foreach ($this->share_services as $srvs){
			if ($this->getParam("srvs_".$srvs)) $this->enabled_services[]=$srvs;
		}
		return implode(",", $this->enabled_services);
	}
}
?>