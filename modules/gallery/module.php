<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class galleryModule extends Module {
	public function prepare() {
		/* Not need here if is set in module settings */ $this->setDefaultView('groups');
	}
	public function getSitemapHTML() {
		$db=Database::getInstance();
		$module=$this->getName();
		$result = array("html"=>"","links"=>array());
		$sql = "SELECT gr.gr_id, gr.gr_title, gr.gr_alias,g.g_id,g.g_title,g.g_alias 
				FROM #__galleries as g, #__gallery_groups as gr 
				WHERE gr.gr_id = g.g_group_id 
				AND gr.gr_deleted=0 AND gr.gr_published=1 AND gr.gr_show_in_list=1 
				AND g.g_deleted=0 AND g.g_published=1 AND g.g_show_in_list=1
				ORDER BY gr.gr_title";
		$db->setQuery($sql);
		$res=$db->loadObjectList();
		$html='';
		$current_category=NULL;
		$html='';
		$ul_started=0;
		if(count($res)) {
			$html.="<ul>";
			foreach($res as $gal) {
				if($current_category!=$gal->gr_id&&!is_null($gal->gr_id)) {
					if($ul_started) $html.="</ul></li>";
					$html.="<li><a href=\"".Router::_("index.php?module=gallery&view=items&psid=".$gal->gr_id."&alias=".$gal->gr_alias)."\">".$gal->gr_title."</a>";
					$html.="<ul>";
					$html.="<li><a href=\"".Router::_("index.php?module=gallery&view=images&psid=".$gal->g_id."&alias=".$gal->g_alias)."\">".$gal->g_title."</a></li>";
					$ul_started++;
					$current_category=$gal->gr_id;
				} else {
					$html.="<li><a href=\"".Router::_("index.php?module=gallery&view=images&psid=".$gal->g_id."&alias=".$gal->g_alias)."\">".$gal->g_title."</a></li>";
				}
			}
			if($ul_started) $html.="</ul></li>";
			$html.="</ul>";
		}
		$result["title_link"]=true;
		$result['html']=$html;
		return $result;
	}	
}
?>