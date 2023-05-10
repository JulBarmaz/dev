<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_WIDGET_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class forumpostWidget extends Widget {
	protected $_requiredModules = array("forum");
	
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("forum_Id", "table_multiselect", 0, false, "SELECT f_id AS fld_id, CONCAT('(',f_id,') ',f_name) AS fld_name FROM #__forum_sections WHERE f_deleted=0 ORDER BY fld_name");
		$this->addParam("forum_Id_ex", "table_multiselect", 0, false, "SELECT f_id AS fld_id, CONCAT('(',f_id,') ',f_name) AS fld_name FROM #__forum_sections WHERE f_deleted=0 ORDER BY fld_name");
		$this->addParam("post_count", "integer", 5);
		$this->addParam("show_author", "boolean", 1);
		$this->addParam("show_date", "boolean", 1);
		$this->addParam("add_nofollow", "boolean", 1);
		$this->addParam("menu_id", "table_select", 0, false, "SELECT mi_id AS fld_id, CONCAT('(',mi_id,') ',mi_name) AS fld_name FROM #__menus ORDER BY fld_name");
	}
	public function render() {
		$post_count = $this->getParam('post_count');
		$arrpsid = array_filter(explode(";", $this->getParam('forum_Id')));
		$arrexpsid = array_filter(explode(";", $this->getParam('forum_Id_ex')));
		$show_date = $this->getParam('show_date');
		$show_author = $this->getParam('show_author');
		$add_nofollow = $this->getParam('add_nofollow');
		if ($this->getParam('menu_id')) $mid="&amp;mid=".$this->getParam('menu_id'); else $mid="";
		if(count($arrpsid)==1 && empty($arrpsid[0]) ) $arrpsid=array();
		if(count($arrexpsid)==1 && empty($arrexpsid[0]) ) $arrexpsid=array();
		$body="";
		$list = $this->getMessages($arrpsid,$arrexpsid,$post_count);
		if (count($list)) {
			foreach($list as $val) {
				$link=Router::_("index.php?module=forum&view=theme&layout=lastpage&psid=".$val->t_id.($val->t_alias ? "&alias=".$val->t_alias : "").$mid);
				$link_author=Router::_("index.php?module=user&view=info&psid=".$val->p_author_id.$mid);
				$body.='<div class="wforum float-fix">';
				if ($show_date) $body.='<span class="wfDate">'.Date::fromSQL($val->p_date,0,true).'</span>';
				if ($show_author) $body.=Text::_("from").'&nbsp;<a class="wfAuthor" title="'.Text::_("Profile").'" href="'.$link_author.'">'.$val->u_nickname.'</a>';
				$body.='<a'.($add_nofollow ? ' rel="nofollow"' : '').' class="wfTheme" href="'.$link.'">'.$val->t_theme.'</a>';
				$body.='</div>';
			}
		}	else {	// Render failure
			$body .= "<p class=\"messages_absent\">".Text::_('Posts not found')."</p>";
		}
		return $body;
	}
	protected function getMessages($arrpsid, $arrexpsid, $post_count=5){
		$db = Database::getInstance();
		$rights=Module::getInstance('forum')->getModel('rights');
		$allowed_ids = $rights->getForumIdsForUser(User::getInstance()->getId(),User::getInstance()->getRole());
		if (count($arrpsid)){
			$ids_arr=array();
			foreach ($arrpsid as $id){
				if(in_array($id, $allowed_ids)) $ids_arr[]=$id;
			}
			
		} else {
			$ids_arr=$allowed_ids;
		}
		if (count($ids_arr)) $ids=implode(",",$arrpsid); else $ids=0;
		if (count($arrexpsid)) $ex_ids=implode(",",$arrexpsid); else $ex_ids=0;
		$sql="SELECT a.* FROM (";
		$sql.="SELECT p.p_author_id,p.p_date,t.*,u.u_nickname,pr.pf_img";
		$sql.=" FROM #__forum_posts AS p";
		$sql.=" LEFT JOIN #__forum_themes AS t ON t.t_id=p.p_theme_id";
		$sql.=" LEFT JOIN #__users AS u ON u.u_id=p.p_author_id";
		$sql.=" LEFT JOIN #__profiles AS pr ON pr.pf_id=p.p_author_id";
		$sql.=" WHERE p.p_enabled=1 AND p.p_deleted=0";
		if($ids) $sql.=" AND t.t_forum_id IN(".$ids.")";
		if($ex_ids) $sql.=" AND t.t_forum_id NOT IN(".$ex_ids.")";
		$sql.=" UNION SELECT t.t_author_id as p_author_id,t.t_date as p_date,t.*,u.u_nickname,pr.pf_img";
		$sql.=" FROM #__forum_themes AS t";
		$sql.=" LEFT JOIN #__users AS u ON u.u_id=t.t_author_id";
		$sql.=" LEFT JOIN #__profiles AS pr ON pr.pf_id=t.t_author_id";
		$sql.=" WHERE t.t_enabled=1";
		if($ids) $sql.=" AND t.t_forum_id IN(".$ids.")";
		if($ex_ids) $sql.=" AND t.t_forum_id NOT IN(".$ex_ids.")";
		$sql.=") AS a";
		$sql.=" GROUP BY a.t_id";
		$sql.=" ORDER BY a.p_date DESC";
		$sql .= " LIMIT ".(int)$post_count;
		$db->setQuery($sql);
		//		echo $this->_db->getQuery();
		return $db->loadObjectList();
	}
	
}
?>