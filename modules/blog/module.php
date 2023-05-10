<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class blogModule extends Module {
	public function prepare() {
		/* Not need here if is set in module settings */ $this->setDefaultView('list');
	}
	public function getSitemapHTML() {
		$arrCatsIdEx=explode(",", $this->getParam('exclude_cats_from_map'));
		$arrblogIdEx=explode(",", $this->getParam('exclude_blogs_from_map'));
		$db=Database::getInstance();
		$result = array("html"=>"","links"=>array());
		$rights=Module::getInstance('blog')->getModel('rights');
		$res=array(0);  	$res_ex=array();
		$sql_blog="SELECT b_id FROM #__blogs WHERE b_deleted=0 AND b_enabled=1";
		$db->setQuery($sql_blog);
		$arrblogId=$db->LoadResultArray();
		if(count($arrblogId)) {
			$res=array_keys($rights->getBlogsWithAction($arrblogId,'read'));
		}
		if(count($arrblogIdEx)) {
			foreach($arrblogIdEx as $blogId) {
				$res_ex[]=intval($blogId);
			}
		}
		$query="SELECT c.*,cats.bc_id,cats.bc_name,cats.bc_alias,links.ordering as ordering
				FROM #__blogs as c
				LEFT JOIN #__blogs_links AS links ON links.b_id=c.b_id
				LEFT JOIN #__blogs_cats AS cats ON cats.bc_id=links.parent_id AND cats.bc_enabled=1 AND cats.bc_deleted=0
				WHERE c.b_deleted=0 AND c.b_enabled=1 AND c.b_show_in_list=1";
		if (count($res)) {
			$query .= " AND (c.b_id IN (".implode(",",$res)."))";
		}
		if (count($res_ex)) {
			$query .= " AND (c.b_id NOT IN (".implode(",",$res_ex)."))";
		}
		$query .=" ORDER BY cats.bc_id, links.ordering ASC";
		$db->setQuery($query);
		$blogs = $db->loadObjectList();
		$current_category=NULL;
		$html='';
		$ul_started=0;
		if(count($blogs)) {
			$html.="<ul>";
			foreach($blogs as $blog) {
				if($current_category!=$blog->bc_id&&!is_null($blog->bc_id)) {
					if($ul_started) {
						$html.="</ul></li>";
						$ul_started--;
					}
					$html.="<li><a href=\"".Router::_("index.php?module=blog&view=category&psid=".$blog->bc_id."&alias=".$blog->bc_alias)."\">".$blog->bc_name."</a>";
					$html.="<ul>";
					$html.="<li><a href=\"".Router::_("index.php?module=blog&view=list&psid=".$blog->b_id."&alias=".$blog->b_alias)."\">".$blog->b_name."</a></li>";
					$ul_started++;
					$current_category=$blog->bc_id;
				} else {
					$html.="<li><a href=\"".Router::_("index.php?module=blog&view=list&psid=".$blog->b_id."&alias=".$blog->b_alias)."\">".$blog->b_name."</a></li>";
				}
			}
			if($ul_started) $html.="</ul></li>";
			$html.="</ul>";
		}
		$result["title_link"]=false;
		$result['html']=$html;
		return $result;
	}
}
?>