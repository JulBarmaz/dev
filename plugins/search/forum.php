<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_PLUGIN_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class searchPluginforum extends Plugin {
	protected $_events=array("search.renderForm","search.renderResult");
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("enableTagSearch", "boolean", 0);
		$this->addParam("cutTextSuffix", "string", "");
	}
	protected function onRaise($event, &$data) {
		switch($event){
			case "search.renderForm":
				$whereInputs=array("forum_posts"=>Text::_("Search forum posts"));
				if($this->getParam("enableTagSearch")) $whereInputs["forum_tags"]=Text::_("Search forum posts by tags");
				SearchMachine::getInstance()->addWhereInputs($whereInputs);
				SearchMachine::getInstance()->addSorting(array("forum_posts"=>array("ttl","ttl-desc","cdate","cdate-desc"),"forum_tags"=>array("cdate","cdate-desc")));
				SearchMachine::getInstance()->addDefaultSorting(array("forum_posts"=>array("cdate-desc"),"forum_tags"=>array("cdate-desc")));
				break;
			case "search.renderResult":
				switch(SearchMachine::getInstance()->getWhere()){
					case "forum_posts":
						$data=$this->processData();
						break;
					case "forum_tags":
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
		$availableForums=Module::getInstance("forum")->getModel("rights")->getForumIdsForUser(User::getInstance()->getId(),User::getInstance()->getRole());
		if (count($availableForums)) $abstring=implode(",",$availableForums); else $abstring=0;

		/***************** START *****************/
		 
		$sql_txt="SELECT count(a.id) FROM (";
		$sql_txt.="SELECT ft.t_id AS id, ft.t_forum_id AS pid, ft.t_date AS cdate, ft.t_theme AS ttl, ft.t_text AS txt, '' AS img, ft.t_deleted AS deleted, ft.t_enabled AS enabled, 1 AS is_main FROM #__forum_themes AS ft WHERE ft.t_deleted=0 AND ft.t_enabled=1 AND ft.t_forum_id IN (".$abstring.")";
		$sql_txt.=" UNION SELECT fp.p_id AS id, fp.p_theme_id AS pid, fp.p_date AS cdate, fp.p_theme AS ttl, fp.p_text AS txt, '' AS img, fp.p_deleted AS deleted, fp.p_enabled AS enabled, 0 AS is_main FROM #__forum_posts AS fp WHERE fp.p_deleted=0 AND fp.p_enabled=1 AND fp.p_theme_id IN (SELECT ftt.t_id FROM #__forum_themes AS ftt WHERE ftt.t_deleted=0 AND ftt.t_enabled=1 AND ftt.t_forum_id IN (".$abstring."))";
		$sql_txt.=") AS a";
		$where_sql="";
		switch (SearchMachine::getInstance()->getType()){
			case "exact":
				$where_sql.=" WHERE (a.ttl LIKE '%".implode(" ", $kwds)."%' OR a.txt LIKE '%".implode(" ", $kwds)."%')";
				break;
			case "any":
				foreach($kwds as $kwd){
					$where_arr1[]="a.ttl LIKE '%".$kwd."%'";
					$where_arr2[]="a.txt LIKE '%".$kwd."%'";
				}
				$where_sql.=" WHERE (".implode(" OR ", $where_arr1)." OR ".implode(" OR ", $where_arr2).")";
				break;
			case "all":
				foreach($kwds as $kwd){
					$where_arr1[]="a.ttl LIKE '%".$kwd."%'";
					$where_arr2[]="a.txt LIKE '%".$kwd."%'";
				}
				$where_sql.=" WHERE ((".implode(" AND ", $where_arr1).") OR (".implode(" AND ", $where_arr2)."))";
				break;
		}
		$sql=$sql_txt.$where_sql;
		Database::getInstance()->setQuery($sql);
		$resultsCount=Database::getInstance()->loadResult();
		$appendix=SearchMachine::getInstance()->alterPaginator($resultsCount);
		$sql_txt="SELECT a.id, a.pid, p.alias, a.cdate, a.ttl, a.txt, a.img, a.is_main FROM (";
		$sql_txt.="SELECT ft.t_id AS id, ft.t_alias AS alias, ft.t_forum_id AS pid, ft.t_date AS cdate, ft.t_theme AS ttl, ft.t_text AS txt, '' AS img, ft.t_deleted AS deleted, ft.t_enabled AS enabled, 1 AS is_main FROM #__forum_themes AS ft WHERE ft.t_deleted=0 AND ft.t_enabled=1 AND ft.t_forum_id IN (".$abstring.")";
		$sql_txt.=" UNION SELECT fp.p_id AS id, '' AS alias, fp.p_theme_id AS pid, fp.p_date AS cdate, fp.p_theme AS ttl, fp.p_text AS txt, '' AS img, fp.p_deleted AS deleted, fp.p_enabled AS enabled, 0 AS is_main FROM #__forum_posts AS fp WHERE fp.p_deleted=0 AND fp.p_enabled=1 AND fp.p_theme_id IN (SELECT ftt.t_id FROM #__forum_themes AS ftt WHERE ftt.t_deleted=0 AND ftt.t_enabled=1 AND ftt.t_forum_id IN (".$abstring."))";
		$sql_txt.=") AS a";
		/***************** END *****************/
		 
		$sql=$sql_txt.$where_sql.$orderby.$appendix;
		Database::getInstance()->setQuery($sql);
		$data=Database::getInstance()->loadObjectList();
		if (count($data)){
			foreach ($data as $row){
				$i++;
				$res[$i]["ttl"]=$row->ttl;
				if($row->is_main) $res[$i]["link"]=Router::_("index.php?module=forum&view=theme&psid=".$row->id."&alias=".$row->alias);
				else $res[$i]["link"]=Router::_("index.php?module=forum&view=theme&psid=".$row->pid."&alias=".$row->alias."#post".$row->id);
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
		$availableForums=Module::getInstance("forum")->getModel("rights")->getForumIdsForUser(User::getInstance()->getId(),User::getInstance()->getRole());
		if (count($availableForums)) $abstring=implode(",",$availableForums); else $abstring=0;
		$sql_txt="SELECT count(t_id) FROM #__forum_themes";
		$where_sql=" WHERE t_deleted=0 AND t_enabled=1 AND t_forum_id IN (".$abstring.")";
		switch (SearchMachine::getInstance()->getType()){
			case "exact":
				$where_sql.=" AND (t_tags LIKE '%,".implode(" ", $kwds).",%')";
				break;
			case "any":
				foreach($kwds as $kwd){
					$where_arr1[]="t_tags LIKE '%,".$kwd.",%'";
				}
				$where_sql.=" AND (".implode(" OR ", $where_arr1).")";
				break;
			case "all":
				foreach($kwds as $kwd){
					$where_arr1[]="t_tags LIKE '%,".$kwd.",%'";
				}
				$where_sql.=" AND (".implode(" AND ", $where_arr1).")";
				break;
		}
		$sql=$sql_txt.$where_sql;
		Database::getInstance()->setQuery($sql);
		$resultsCount=Database::getInstance()->loadResult();
		$appendix=SearchMachine::getInstance()->alterPaginator($resultsCount);
		$sql_txt="SELECT t_id AS id, t_forum_id AS pid, t_date AS cdate, t_theme AS ttl, t_text AS txt, '' AS img FROM #__forum_themes";
		$sql=$sql_txt.$where_sql.$orderby.$appendix;
		Database::getInstance()->setQuery($sql);
		$data=Database::getInstance()->loadObjectList();
		if (count($data)){
			foreach ($data as $row){
				$i++;
				$res[$i]["ttl"]=$row->ttl;
				$res[$i]["link"]=Router::_("index.php?module=forum&view=theme&psid=".$row->id."&alias=".$row->alias);
				$res[$i]["cdate"]=Date::fromSQL($row->cdate,false,true);
				$res[$i]["img"]=$row->img;
				// $res[$i]["txt"] = Text::toHtml(Text::fromHtml($row->txt,siteConfig::$shortTextLength,$cutTextSuffix));
				$res[$i]["txt"] = Text::cutHtml($row->txt, siteConfig::$shortTextLength, $cutTextSuffix);
			}
		}
		return $res;
	}
}

?>