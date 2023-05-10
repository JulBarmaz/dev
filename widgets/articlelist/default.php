<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_WIDGET_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class articlelistWidget extends Widget {
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("article_Id", "string", "");
		$this->addParam("article_Id_ex", "string", "");
		$this->addParam("article_count", "integer", 5);
		$this->addParam("show_date", "boolean", 0);
		$this->addParam("data_absent_text", "string", "");
		$this->addParam("menu_id", "table_select", 0, false, "SELECT mi_id AS fld_id, CONCAT('(',mi_id,') ',mi_name) AS fld_name FROM #__menus ORDER BY fld_name");
	}
	public function render() {
		$article_count	= $this->getParam('article_count');
		$arrpsid		= explode(",",$this->getParam('article_Id'));
		$arrexpsid	= explode(",",$this->getParam('article_Id_ex'));
		$show_date = $this->getParam('show_date');
		if ($this->getParam('menu_id')) $mid="&amp;mid=".$this->getParam('menu_id'); else $mid="";
		if(count($arrpsid)==1 && empty($arrpsid[0]) ) $arrpsid=array();
		if(count($arrexpsid)==1 && empty($arrexpsid[0]) ) $arrexpsid=array();
		$body = "";
		$list = $this->getListArticle($arrpsid,$arrexpsid,$article_count);
		if (count($list)) {
			foreach($list as $val) {
				$link=Router::_("index.php?module=article&amp;view=read&amp;psid=".$val->a_id."&amp;alias=".$val->a_alias.$mid);
				$link_aut=Router::_("index.php?module=user&view=info&psid=".$val->u_id);
				$body.='<div class="wArticleList row"><div class="wbTheme col-xs-12"><a href="'.$link.'">'.$val->title;
				if($show_date) $body.='&nbsp;<small>'.Date::fromSQL($val->date,true,true).'</small>';
				$body.='</a></div></div>';
			}
		}	else {	// Render failure
			$data_absent_text = $this->getParam('data_absent_text');
			$body .= "<div class=\"data-absent\">".($data_absent_text ? $data_absent_text : Text::_('Article not found'))."</div>";
		}
		return $body;
	}
	protected function getListArticle($arrArticleId,$arrArticleIdEx,$post_count=5) {
		$db = Database::getInstance();
		$res=array(0);  	$res_ex=array();
		if(!count($arrArticleId)) {
			$sql_article="SELECT a_id FROM #__articles WHERE a_deleted=0 AND a_published=1 and a_parent_id<>0";
			$db->setQuery($sql_article);
			$arrArticleId=$db->LoadResultArray();
		}
		
		if(count($arrArticleId)) {
			foreach($arrArticleId as $ArticleId) {
				$res[]=intval($ArticleId);
			}
		}
		if(count($arrArticleIdEx)) {
			foreach($arrArticleIdEx as $ArticleId) {
				$res_ex[]=intval($ArticleId);
			}
		}
		$query = "SELECT a.a_id, a.a_alias, a.a_parent_id,
				a.a_title as title,a.a_date as date,
				a.a_author_id as u_id,	u.u_nickname as author
				FROM `#__articles` as a,`#__users` as u
				WHERE a.a_deleted=0 AND a.a_published=1";
		$query .= " AND a.a_author_id=u.u_id";
		if (count($res)) {
			$query .= " AND (a.a_id IN (".implode(",",$res)."))";
		}
		if (count($res_ex)) {
			$query .= " AND (a.a_id NOT IN (".implode(",",$res_ex)."))";
		}
		$query .= " ORDER BY a.a_date DESC";
		$query .= " LIMIT ".(int)$post_count;
		$db->setQuery($query);
		$posts = $db->loadObjectList();
		if (is_array($posts) && count($posts) > 0) {
			return $posts;
		}	else 	{
			return array();
		}
	}
}
?>