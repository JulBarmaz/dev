<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class videosetModule extends Module {
	public function prepare() {
		/* Not need here if is set in module settings */ $this->setDefaultView('groups');
	}
	public function getSitemapHTML() {
		$db=Database::getInstance();
		$module=$this->getName();
		$result = array("html"=>"","links"=>array());
		$sql = "SELECT gr.vgr_id, gr.vgr_title, gr.vgr_alias,g.vg_id,g.vg_title,g.vg_alias
				FROM #__videoset_galleries as g, #__videoset_groups as gr
				WHERE gr.vgr_id = g.vg_group_id AND gr.vgr_deleted=0 AND gr.vgr_published=1 AND gr.vgr_show_in_list=1
				AND g.vg_deleted=0 AND g.vg_published=1 AND g.vg_show_in_list=1";
		$db->setQuery($sql);
		$res=$db->loadObjectList();
		$html='';
		$current_category=NULL;
		$html='';
		$ul_started=0;
		if(count($res))
		{
			$html.="<ul>";
			foreach($res as $gal)
			{
				if($current_category!=$gal->vgr_id&&!is_null($gal->vgr_id))
				{
					if($ul_started) $html.="</ul></li>";
					$html.="<li><a href=\"".Router::_("index.php?module=videoset&view=items&psid=".$gal->vgr_id."&alias=".$gal->vgr_alias)."\">".$gal->vgr_title."</a>";
					$html.="<ul>";
					$html.="<li><a href=\"".Router::_("index.php?module=videoset&view=videos&psid=".$gal->vg_id."&alias=".$gal->vg_alias)."\">".$gal->vg_title."</a></li>";
					$ul_started++;
					$current_category=$gal->vgr_id;
				}
				else {
					$html.="<li><a href=\"".Router::_("index.php?module=videoset&view=videos&psid=".$gal->vg_id."&alias=".$gal->vg_alias)."\">".$gal->vg_title."</a></li>";
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