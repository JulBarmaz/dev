<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class pollsModule extends Module {
	public function prepare() {
		/* Not need here if is set in module settings */ $this->setDefaultView('polls');
	}
	public function getSitemapHTML() {
		$db=Database::getInstance();
		$module=$this->getName();
		$result = array("html"=>"","links"=>array());
		$sql = "SELECT * FROM #__polls WHERE p_deleted=0 AND p_enabled=1";
		$db->setQuery($sql);
		$res=$db->loadObjectList();
		$html='';
		if(count($res)) {
			$html.="<ul>";
			foreach($res as $al) {
				$html.="<li><a href=\"".Router::_("index.php?module=polls&view=poll&psid=".$al->p_id."&alias=".$al->p_alias)."\">".$al->p_title."</a></li>";
			}
			$html.="</ul>";
		}
		$result["title_link"]=true;
		$result['html']=$html;
		return $result;
	}
}
?>