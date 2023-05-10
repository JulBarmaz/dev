<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_PLUGIN_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class searchPlugintags extends Plugin {
	protected $_events=array("search.renderForm","search.renderResult");
	protected function setParamsMask(){
		parent::setParamsMask();
//		$this->addParam("enableTagSearch", "boolean", 0);
		$this->addParam("cutTextSuffix", "string", "");
	}
	protected function onRaise($event, &$data) {
		switch($event){
			case "search.renderForm":
				$whereInputs=array("tags"=>Text::_("Search by tags"));
				SearchMachine::getInstance()->addWhereInputs($whereInputs);
				SearchMachine::getInstance()->addSorting(array("tags"=>array("ttl","ttl-desc","cdate","cdate-desc")));
				SearchMachine::getInstance()->addDefaultSorting(array("tags"=>array("cdate-desc")));
				break;
			case "search.renderResult":
				switch(SearchMachine::getInstance()->getWhere()){
					case "tags":
						$data=$this->processTagsData();
						break;
				}
				break;
		}
	}
	private function processTagsData(){
		$res=array(); $i=0; $where_arr1=array(); $where_arr2=array();
		$cutTextSuffix = $this->getParam("cutTextSuffix");
		$kwds = SearchMachine::getInstance()->getWords();
		$orderby=" ORDER BY ".SearchMachine::getInstance()->getOrderBy();
		$availableBlogs=Module::getInstance("blog")->getModel("rights")->getBlogIdsForUser(User::getInstance()->getId(),User::getInstance()->getRole());
		if (count($availableBlogs)) $abstring=implode(",",$availableBlogs); else $abstring=0;
		$sql_txt_1="SELECT p_id AS id FROM #__blogs_posts";
		$sql_txt_2="SELECT t_id AS id FROM #__forum_themes";
		$where_sql_1=" WHERE p_deleted=0 AND p_enabled=1 AND p_blog_id IN (".$abstring.")";
		$where_sql_2=" WHERE t_deleted=0 AND t_enabled=1";
		switch (SearchMachine::getInstance()->getType()){
			case "exact":
				$where_sql_1.=" AND (p_tags LIKE '%,".implode(" ", $kwds).",%')";
				$where_sql_2.=" AND (t_tags LIKE '%,".implode(" ", $kwds).",%')";
				break;
			case "any":
				foreach($kwds as $kwd){
					$where_arr1[]="p_tags LIKE '%".$kwd."%'";
					$where_arr2[]="t_tags LIKE '%".$kwd."%'";
				}
				$where_sql_1.=" AND (".implode(" OR ", $where_arr1).")";
				$where_sql_2.=" AND (".implode(" OR ", $where_arr2).")";
				break;
			case "all":
				foreach($kwds as $kwd){
					$where_arr1[]="p_tags LIKE '%".$kwd."%'";
					$where_arr2[]="t_tags LIKE '%".$kwd."%'";
				}
				$where_sql_1.=" AND (".implode(" AND ", $where_arr1).")";
				$where_sql_2.=" AND (".implode(" AND ", $where_arr2).")";
				break;
		}
		$sql="SELECT COUNT(id) FROM (".$sql_txt_1.$where_sql_1." UNION ".$sql_txt_2.$where_sql_2.") AS posts";
		Database::getInstance()->setQuery($sql);
		$resultsCount=Database::getInstance()->loadResult();
		$appendix=SearchMachine::getInstance()->alterPaginator($resultsCount);
		$sql_txt_1="SELECT p_id AS id, p_blog_id AS pid, p_alias AS alias, p_date AS cdate, p_theme AS ttl, p_text AS txt, '' AS img, 'blog' AS module FROM #__blogs_posts";
		$sql_txt_2="SELECT t_id AS id, t_forum_id AS pid, t_alias AS alias, t_date AS cdate, t_theme AS ttl, t_text AS txt, '' AS img, 'forum' AS module FROM #__forum_themes";
		$sql="SELECT * FROM (".$sql_txt_1.$where_sql_1." UNION ".$sql_txt_2.$where_sql_2.") AS posts".$orderby.$appendix;
		Database::getInstance()->setQuery($sql);
		$data=Database::getInstance()->loadObjectList();
		if (count($data)){
			foreach ($data as $row){
				$i++;
				$res[$i]["ttl"]=$row->ttl;
				if ($row->module=='blog'){
					$res[$i]["link"]=Router::_("index.php?module=blog&view=post&psid=".$row->id."&alias=".$row->alias);
				} elseif($row->module=='forum'){
					$res[$i]["link"]=Router::_("index.php?module=forum&view=theme&psid=".$row->id."&alias=".$row->alias);
				} else {
					$res[$i]["link"]=Router::_("index.php");
				}
				$res[$i]["cdate"]=Date::fromSQL($row->cdate,false,true);
				$res[$i]["img"]=$row->img;
				$first_hr=mb_strpos($row->txt,'<hr id="system-readmore"',0,DEF_CP);
				if ($first_hr) { $res[$i]["txt"] = mb_substr($row->txt,0,$first_hr,DEF_CP);	}
				// else { $res[$i]["txt"] = Text::toHtml(Text::fromHtml($row->txt,siteConfig::$shortTextLength,$cutTextSuffix));	}
				else { $res[$i]["txt"] = Text::cutHtml($row->txt, siteConfig::$shortTextLength, $cutTextSuffix); }
			}
		}
		return $res;
	}
}

?>