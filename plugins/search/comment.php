<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_PLUGIN_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class searchPlugincomment extends Plugin {
	protected $_events=array("search.renderForm","search.renderResult");
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("cutTextSuffix", "string", "");
	}
	protected function onRaise($event, &$data) {
		switch($event){
			case "search.renderForm":
				SearchMachine::getInstance()->addWhereInputs(array("comments"=>Text::_("Search comments")));
				SearchMachine::getInstance()->addSorting(array("comments"=>array("cdate","cdate-desc")));
				SearchMachine::getInstance()->addDefaultSorting(array("comments"=>array("cdate-desc")));
				break;
			case "search.renderResult":
				switch(SearchMachine::getInstance()->getWhere()){
					case "comments":
						$data=$this->processData();
						break;
				}
				break;
		}
	}
	private function processData(){
		$res=array(); $i=0;
		$kwds = SearchMachine::getInstance()->getWords();
		$orderby=" ORDER BY ".SearchMachine::getInstance()->getOrderBy();
		$cutTextSuffix = $this->getParam("cutTextSuffix");
		$availableGroups=array_keys(BaseComments::getInstance()->getGIDsForUser("read"));
		if (count($availableGroups)) $abstring=implode(",",$availableGroups); else $abstring=0;
		//@FIXME Now only base tables
		$sql_txt="SELECT count(c.cm_id) FROM #__comms AS c, #__comms_grp AS g";
		$where_sql=" WHERE g.cg_id=c.cm_grp_id AND g.cg_enabled=1 AND g.cg_deleted=0 AND c.cm_deleted=0 AND c.cm_grp_id IN (".$abstring.")";
		switch (SearchMachine::getInstance()->getType()){
			case "exact":
				$where_sql.=" AND (cm_title LIKE '%".implode(" ", $kwds)."%' OR cm_text LIKE '%".implode(" ", $kwds)."%')";
				break;
			case "any":
				foreach($kwds as $kwd){
					$where_arr1[]="cm_title LIKE '%".$kwd."%'";
					$where_arr2[]="cm_text LIKE '%".$kwd."%'";
				}
				$where_sql.=" AND (".implode(" OR ", $where_arr1)." OR ".implode(" OR ", $where_arr2).")";
				break;
			case "all":
				foreach($kwds as $kwd){
					$where_arr1[]="cm_title LIKE '%".$kwd."%'";
					$where_arr2[]="cm_text LIKE '%".$kwd."%'";
				}
				$where_sql.=" AND ((".implode(" AND ", $where_arr1).") OR (".implode(" AND ", $where_arr2)."))";
				break;
		}
		$sql=$sql_txt.$where_sql;
		Database::getInstance()->setQuery($sql);
		$resultsCount=Database::getInstance()->loadResult();
		$appendix=SearchMachine::getInstance()->alterPaginator($resultsCount);
		//@FIXME Now only base tables
		$sql_txt="SELECT c.cm_id AS id, c.cm_obj_id AS obj_id, c.cm_nickname AS nickname, c.cm_date AS cdate, c.cm_title AS ttl, c.cm_text AS txt, '' AS img, g.cg_module as module, g.cg_view as viewname FROM #__comms AS c, #__comms_grp AS g";
		$sql=$sql_txt.$where_sql.$orderby.$appendix;
		Database::getInstance()->setQuery($sql);
		$data=Database::getInstance()->loadObjectList();
		if (count($data)){
			foreach ($data as $row){
				$i++;
				if ($row->ttl) $res[$i]["ttl"]=$row->ttl;
				else $res[$i]["ttl"]=Text::_("Comment by").": ".$row->nickname;
				$res[$i]["link"]=Router::_("index.php?module=".$row->module."&view=".$row->viewname."&task=getComment&psid=".$row->obj_id."&comm_id=".$row->id);
				$res[$i]["cdate"]=Date::fromSQL($row->cdate,false,true);
				$res[$i]["img"]=$row->img;
				//$res[$i]["txt"] = Text::toHtml(Text::fromHtml($row->txt,siteConfig::$shortTextLength,$cutTextSuffix));
				$res[$i]["txt"] = Text::cutHtml($row->txt, siteConfig::$shortTextLength, $cutTextSuffix);
			}
		}
		return $res;
	}
}

?>