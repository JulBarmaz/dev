<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class feedbackModule extends Module {
	public function prepare() {
		/* Not need here if is set in module settings */ $this->setDefaultView('message');
	}
	public function getSitemapHTML() {
		$result = array("html"=>"","links"=>array());
		$html="<ul>";
		$html.="<li><a href=\"".Router::_("index.php?module=feedback&view=message")."\">".Text::_("You may write us from here")."</a>";
		$html.="<li><a href=\"".Router::_("index.php?module=feedback&view=backcall")."\">".Text::_("Order backcall")."</a>";
		$html.="</ul>";
		$result["title_link"]=true;
		$result['html']=$html;
		return $result;
	}
}
?>