<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_WIDGET_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class blogpostWidget extends Widget {
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("blog_Id", "table_multiselect", 0, false, "SELECT b_id AS fld_id, CONCAT('(',b_id,') ',b_name) AS fld_name FROM #__blogs WHERE b_deleted=0 ORDER BY fld_name");
		$this->addParam("blog_Id_ex", "table_multiselect", 0, false, "SELECT b_id AS fld_id, CONCAT('(',b_id,') ',b_name) AS fld_name FROM #__blogs WHERE b_deleted=0 ORDER BY fld_name");
		$this->addParam("post_count", "integer", 5);
		$this->addParam("show_date", "boolean", 0);
		$this->addParam("show_time_in_date", "boolean", 0);
		$this->addParam("show_post_title", "boolean", 1);
		$this->addParam("brief_length", "integer", 0);
		$this->addParam("show_readmore", "boolean", 1);
		$this->addParam("show_thumb", "boolean", 1);
		$this->addParam("sort_by_touchdate", "boolean", 0);
		$this->addParam("show_all_blog_id", "integer", 0);
		$this->addParam("show_all_blog_text", "string", "");
		$this->addParam("data_absent_text", "string", "");
		$this->addParam("menu_id", "table_select", 0, false, "SELECT mi_id AS fld_id, CONCAT('(',mi_id,') ',mi_name) AS fld_name FROM #__menus ORDER BY fld_name");
	}
	public function render() {
		$post_count = $this->getParam('post_count');
		$arrpsid = array_filter(explode(";",$this->getParam('blog_Id')));
		$arrexpsid = array_filter(explode(";",$this->getParam('blog_Id_ex')));
		$show_date = $this->getParam('show_date');
		if($this->getParam('show_time_in_date')) $hide_time_in_date = 0; else $hide_time_in_date = 1;
		$show_post_title	= $this->getParam('show_post_title');
		$brief_length	= $this->getParam('brief_length');
		$show_readmore	= $this->getParam('show_readmore');
		$show_thumb	= $this->getParam('show_thumb');
		$show_all_blog_id = $this->getParam('show_all_blog_id');
		if ($this->getParam('menu_id') && seoConfig::$useMidInMenuLinks) $mid="&amp;mid=".$this->getParam('menu_id'); else $mid="";
		if(count($arrpsid)==1 && empty($arrpsid[0]) ) $arrpsid=array();
		if(count($arrexpsid)==1 && empty($arrexpsid[0]) ) $arrexpsid=array();
		$body="";
		$list = $this->getListPost($arrpsid,$arrexpsid,$post_count);
		if (count($list)) {
			foreach($list as $val) {
				$link=Router::_("index.php?module=blog&amp;view=post&amp;psid=".$val->p_id."&amp;alias=".$val->p_alias.$mid);
				$link_aut=Router::_("index.php?module=user&view=info&psid=".$val->p_author_id);
				$body.='<div class="wblog float-fix"><div class="wbTheme">';
				if ($show_date) $body.='<span class="post_date">'.Date::fromSQL($val->p_touch_date,$hide_time_in_date,true).'</span>';
				if($show_post_title) $body.='<a href="'.$link.'">'.$val->p_theme.'</a>';
				$body.='</div>';
				if ($show_thumb && $val->p_thumb) {
					$filename=BARMAZ_UF_PATH."blog".DS."thumbs".DS.Files::splitAppendix($val->p_thumb,true);
					if (Files::isImage($filename))	{
						$filelink=BARMAZ_UF."/blog/thumbs/".Files::splitAppendix($val->p_thumb);
						$body.= "<div class=\"wbThumb\"><img title=\"".$val->p_title_thm."\" alt=\"".$val->p_alt_thm."\" src=\"".$filelink."\" /></div>";
					}
				}
				if($brief_length){
					$body.='<div class="wbText float-fix">';
					$body.=mb_substr(strip_tags($val->p_text), 0, $brief_length,DEF_CP)."...";
					if($show_readmore){
						$body.='<div class="readmore"><a href="'.$link.'">'.Text::_("Read more").'</a></div>';
					}
					$body.='</div>';
				}
				$body.='</div>';
			}
			if($show_all_blog_id){
				$blog_alias = Module::getHelper("blog","blog")->getAliasByID("list",$show_all_blog_id);
				$show_all_blog_text = $this->getParam('show_all_blog_text');
				$body .= "<div class=\"view_archive\"><a href=\"".Router::_("index.php?module=blog&view=list&psid=".$show_all_blog_id."&alias=".$blog_alias)."\">".($show_all_blog_text ? $show_all_blog_text : Text::_('Show all'))."</a></div>";
			}
		}	else {	// Render failure
			$data_absent_text = $this->getParam('data_absent_text');
			$body .= "<div class=\"messages_absent\">".($data_absent_text ? $data_absent_text : Text::_('Posts not found'))."</div>";
		}
		return $body;
	}
	protected function getListPost($arrblogId,$arrblogIdEx,$post_count=5) {
		$db = Database::getInstance();
		$rights=Module::getInstance('blog')->getModel('rights');
		$res=array();  	$res_ex=array();
		if(!count($arrblogId)) {
			$sql_blog="SELECT b_id FROM #__blogs WHERE b_deleted=0 AND b_enabled=1";
			$db->setQuery($sql_blog);
			$arrblogId=$db->LoadResultArray();
		}
		
		if(count($arrblogId)) {
			$res=array_keys($rights->getBlogsWithAction($arrblogId,'read'));
		}
		if(count($arrblogIdEx)) {
			foreach($arrblogIdEx as $blogId) {
				$res_ex[]=intval($blogId);
			}
		}
		$query = "SELECT b.p_id,b.p_blog_id,b.p_theme,b.p_alias,b.p_text, b.p_thumb,
				b.p_title_thm, b.p_alt_thm,b.p_touch_date,b.p_author_id,
				u.u_nickname as author 	FROM `#__blogs_posts` as b
				LEFT JOIN #__users AS u  ON u.u_id=b.p_author_id
				WHERE b.p_deleted=0 AND b.p_enabled=1";
		if (count($res)) {
			$query .= " AND (b.p_blog_id IN (".implode(",",$res)."))";
		} else return array();
		if (count($res_ex)) {
			$query .= " AND (b.p_blog_id NOT IN (".implode(",",$res_ex)."))";
		}
		if($this->getParam('sort_by_touchdate')) $query .= " ORDER BY b.p_touch_date DESC";
		else  $query .= " ORDER BY b.p_date DESC";
		$query .= " LIMIT ".(int)$post_count;
		$db->setQuery($query);
		$posts = $db->loadObjectList();
		if (is_array($posts) && count($posts) > 0) {
			if(defined("_BARMAZ_TRANSLATE")){
				// преобразуем сведения на тот язык который сейчас выбран, если он отличается от дефолтного
				if(siteConfig::$defaultLanguage!=Text::getLanguage()){
					if(is_array ( $posts ) && count ( $posts ) > 0){
						$translator = new Translator();
						$arr_tables=array('blogs_posts');
						$arr_data=array('blogs_posts'=>$posts);
						foreach($posts as $post){
							$arr_psid['blogs_posts'][$post->p_id]=$post->p_id;
							$arr_psid['users'][$post->p_author_id]=$post->p_author_id;
						}
						$arr_key['blogs_posts']='p_id';
						//Util::showArray($posts,'before');
						$post_data= $translator->updateReturnData($arr_tables,$arr_data,$arr_psid,$arr_key,Text::getLanguage());
						$posts=$post_data['blogs_posts'];
						//Util::showArray($posts,'after');
					}
				}
			}
			
			return $posts;
		}	else 	{
			return array();
		}
	}
}
?>