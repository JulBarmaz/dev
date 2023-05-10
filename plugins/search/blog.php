<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_PLUGIN_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class searchPluginblog extends Plugin {
	protected $_events=array("search.renderForm","search.renderResult");
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("enableTagSearch", "boolean", 0);
		$this->addParam("cutTextSuffix", "string", "");
	}
	protected function onRaise($event, &$data) {
		switch($event){
			case "search.renderForm":
				$whereInputs=array("blogs_posts"=>Text::_("Search blogs posts"));
				if($this->getParam("enableTagSearch")) $whereInputs["blogs_tags"]=Text::_("Search blogs posts by tags");
				SearchMachine::getInstance()->addWhereInputs($whereInputs);
				SearchMachine::getInstance()->addSorting(array("blogs_posts"=>array("ttl","ttl-desc","cdate","cdate-desc"),"blogs_tags"=>array("cdate","cdate-desc")));
				SearchMachine::getInstance()->addDefaultSorting(array("blogs_posts"=>array("cdate-desc"),"blogs_tags"=>array("cdate-desc")));
				break;
			case "search.renderResult":
				switch(SearchMachine::getInstance()->getWhere()){
					case "blogs_posts":
						$data=$this->processData();
						break;
					case "blogs_tags":
						$data=$this->processTagsData();
						break;
				}
				break;
		}
	}
	private function processData(){
		$res=array(); $i=0;
		$cutTextSuffix = $this->getParam("cutTextSuffix");
		$kwds = SearchMachine::getInstance()->getWords();
		$orderby=" ORDER BY ".SearchMachine::getInstance()->getOrderBy();
		$availableBlogs=Module::getInstance("blog")->getModel("rights")->getBlogIdsForUser(User::getInstance()->getId(),User::getInstance()->getRole());
		if (count($availableBlogs)) $abstring=implode(",",$availableBlogs); else $abstring=0;
		$sql_txt="SELECT count(p_id) FROM #__blogs_posts";
		$where_sql=" WHERE p_deleted=0 AND p_enabled=1 AND p_blog_id IN (".$abstring.")";
		switch (SearchMachine::getInstance()->getType()){
			case "exact":
				$where_sql.=" AND (p_theme LIKE '%".implode(" ", $kwds)."%' OR p_text LIKE '%".implode(" ", $kwds)."%')";
				break;
			case "any":
				foreach($kwds as $kwd){
					$where_arr1[]="p_theme LIKE '%".$kwd."%'";
					$where_arr2[]="p_text LIKE '%".$kwd."%'";
				}
				$where_sql.=" AND (".implode(" OR ", $where_arr1)." OR ".implode(" OR ", $where_arr2).")";
				break;
			case "all":
				foreach($kwds as $kwd){
					$where_arr1[]="p_theme LIKE '%".$kwd."%'";
					$where_arr2[]="p_text LIKE '%".$kwd."%'";
				}
				$where_sql.=" AND ((".implode(" AND ", $where_arr1).") OR (".implode(" AND ", $where_arr2)."))";
				break;
		}
		$sql=$sql_txt.$where_sql;
		Database::getInstance()->setQuery($sql);
		$resultsCount=Database::getInstance()->loadResult();
		$appendix=SearchMachine::getInstance()->alterPaginator($resultsCount);
		$sql_txt="SELECT p_id AS id, p_alias AS alias, p_blog_id AS pid, p_date AS cdate, p_theme AS ttl, p_text AS txt, '' AS img FROM #__blogs_posts";
		$sql=$sql_txt.$where_sql.$orderby.$appendix;
		Database::getInstance()->setQuery($sql);
		$data=Database::getInstance()->loadObjectList();
		if (count($data)){
			foreach ($data as $row){
				$i++;
				$res[$i]["ttl"]=$row->ttl;
				$res[$i]["link"]=Router::_("index.php?module=blog&view=post&psid=".$row->id."&alias=".$row->alias);
				$res[$i]["cdate"]=Date::fromSQL($row->cdate,false,true);
				$res[$i]["img"]=$row->img;
				// $res[$i]["txt"] = Text::toHtml(Text::fromHtml($row->txt,siteConfig::$shortTextLength,$cutTextSuffix));
				$res[$i]["txt"] = Text::cutHtml($row->txt, siteConfig::$shortTextLength, $cutTextSuffix);
			}
		}
		return $res;
	}
	private function processTagsData(){
		$res=array(); $i=0;
		$cutTextSuffix = $this->getParam("cutTextSuffix");
		$kwds = SearchMachine::getInstance()->getWords();
		$orderby=" ORDER BY ".SearchMachine::getInstance()->getOrderBy();
		$availableBlogs=Module::getInstance("blog")->getModel("rights")->getBlogIdsForUser(User::getInstance()->getId(),User::getInstance()->getRole());
		if (count($availableBlogs)) $abstring=implode(",",$availableBlogs); else $abstring=0;
		$sql_txt="SELECT count(p_id) FROM #__blogs_posts";
		$where_sql=" WHERE p_deleted=0 AND p_blog_id IN (".$abstring.")";
		switch (SearchMachine::getInstance()->getType()){
			case "exact":
				$where_sql.=" AND (p_tags LIKE '%,".implode(" ", $kwds).",%')";
				break;
			case "any":
				foreach($kwds as $kwd){
					$where_arr1[]="p_tags LIKE '%,".$kwd.",%'";
				}
				$where_sql.=" AND (".implode(" OR ", $where_arr1).")";
				break;
			case "all":
				foreach($kwds as $kwd){
					$where_arr1[]="p_tags LIKE '%,".$kwd.",%'";
				}
				$where_sql.=" AND (".implode(" AND ", $where_arr1).")";
				break;
		}
		$sql=$sql_txt.$where_sql;
		Database::getInstance()->setQuery($sql);
		$resultsCount=Database::getInstance()->loadResult();
		$appendix=SearchMachine::getInstance()->alterPaginator($resultsCount);
		$sql_txt="SELECT p_id AS id, p_alias AS alias, p_blog_id AS pid, p_date AS cdate, p_theme AS ttl, p_text AS txt, '' AS img FROM #__blogs_posts";
		$sql=$sql_txt.$where_sql.$orderby.$appendix;
		Database::getInstance()->setQuery($sql);
		$data=Database::getInstance()->loadObjectList();
		if (count($data)){
			foreach ($data as $row){
				$i++;
				$res[$i]["ttl"]=$row->ttl;
				$res[$i]["link"]=Router::_("index.php?module=blog&view=post&psid=".$row->id."&alias=".$row->alias);
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