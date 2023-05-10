<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_PLUGIN_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class searchPluginarticle extends Plugin {
	protected $_events=array("search.renderForm","search.renderResult");
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("cutTextSuffix", "string", "");
	}
	protected function onRaise($event, &$data) {
		switch($event){
			case "search.renderForm":
				SearchMachine::getInstance()->addWhereInputs(array("article"=>Text::_("Search articles")));
				SearchMachine::getInstance()->addSorting(array("article"=>array("ttl","ttl-desc","cdate","cdate-desc")));
				SearchMachine::getInstance()->addDefaultSorting(array("article"=>array("ttl")));
				break;
			case "search.renderResult":
				switch(SearchMachine::getInstance()->getWhere()){
					case "article":
						$data=$this->processData();
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
		$sql_txt="SELECT count(a_id) FROM #__articles";
		$where_sql=" WHERE a_deleted=0 AND a_published=1";
		switch (SearchMachine::getInstance()->getType()){
			case "exact":
				$where_sql.=" AND (a_title LIKE '%".implode(" ", $kwds)."%' OR a_text LIKE '%".implode(" ", $kwds)."%')";
				break;
			case "any":
				foreach($kwds as $kwd){
					$where_arr1[]="a_title LIKE '%".$kwd."%'";
					$where_arr2[]="a_text LIKE '%".$kwd."%'";
				}
				$where_sql.=" AND (".implode(" OR ", $where_arr1)." OR ".implode(" OR ", $where_arr2).")";
				break;
			case "all":
				foreach($kwds as $kwd){
					$where_arr1[]="a_title LIKE '%".$kwd."%'";
					$where_arr2[]="a_text LIKE '%".$kwd."%'";
				}
				$where_sql.=" AND ((".implode(" AND ", $where_arr1).") OR (".implode(" AND ", $where_arr2)."))";
				break;
		}
		$sql=$sql_txt.$where_sql;
		Database::getInstance()->setQuery($sql);
		$resultsCount=Database::getInstance()->loadResult();
		$appendix=SearchMachine::getInstance()->alterPaginator($resultsCount);
		$sql_txt="SELECT a_id AS id, a_alias AS alias, a_parent_id AS pid, a_title AS ttl, a_date AS cdate, a_text AS txt, '' AS img FROM #__articles";
		$sql=$sql_txt.$where_sql.$orderby.$appendix;
		Database::getInstance()->setQuery($sql);
		$data=Database::getInstance()->loadObjectList();
		if (count($data)){
			foreach ($data as $row){
				$i++;
				$res[$i]["ttl"]=$row->ttl;
				$res[$i]["link"]=Router::_("index.php?module=article&view=read&psid=".$row->id."&alias=".$row->alias);
				$res[$i]["cdate"]=Date::fromSQL($row->cdate, true);
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