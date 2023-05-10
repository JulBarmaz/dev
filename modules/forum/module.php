<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class forumModule extends Module {
	public function prepare() {
		/* Not need here if is set in module settings */ $this->setDefaultView('section');
	}
	public function getSitemapHTML() {
		$arrIdEx=explode(",", $this->getParam('exclude_forums_from_map'));
		$db=Database::getInstance();
		$result = array("html"=>"","links"=>array());
		$rights=Module::getInstance('forum')->getModel('rights');
		$res=array(0); $res_ex=array();
		$sql_sections="SELECT f_id FROM #__forum_sections WHERE f_deleted=0 AND f_enabled=1";
		$db->setQuery($sql_sections);
		$arrId=$db->LoadResultArray();
		if(count($arrId)) $res=array_keys($rights->getForumsWithAction($arrId,'read'));
		if(count($arrIdEx)) {
			foreach($arrIdEx as $_eId) {
				$res_ex[]=intval($_eId);
			}
		}
		$query="SELECT f.* FROM #__forum_sections as f
				WHERE f.f_deleted=0 AND f.f_enabled=1 AND f.f_show_in_list=1";
		if (count($res)) {
			$query .= " AND (f.f_id IN (".implode(",",$res)."))";
		}
		if (count($res_ex)) {
			$query .= " AND (f.f_id NOT IN (".implode(",",$res_ex)."))";
		}
		$query .=" ORDER BY f.f_ordering";
		$db->setQuery($query);
		$forums = $db->loadObjectList();
		$html='';
		if(count($forums)) {
			$html.="<ul>";
			foreach($forums as $forum) {
				$html.="<li><a href=\"".Router::_("index.php?module=forum&view=section&psid=".$forum->f_id."&alias=".$forum->f_alias)."\">".$forum->f_name."</a>";
			}
			$html.="</ul>";
		}
		$result["title_link"]=true;
		$result['html']=$html;
		return $result;
	}
}
?>