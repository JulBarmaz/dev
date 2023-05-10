<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_WIDGET_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class randarticleWidget extends Widget {
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("ParentarticleId", "table_select", 0, false, "SELECT a_id AS fld_id, a_title AS fld_name FROM #__articles ORDER BY fld_name");
		$this->addParam("ShowDopTitle", "boolean", 1);
		$this->addParam("DopTitle", "string", "");
		$this->addParam("LinkDopTitle", "string", "");
		$this->addParam("shortTextLength", "integer", 200);
	}
	public function render() {
		$psid		= $this->getParam('ParentarticleId');
		$show_Doptitle = $this->getParam('ShowDopTitle');
		$Doptitle = $this->getParam('DopTitle');
		$LinkDoptitle = $this->getParam('LinkDopTitle');
		$shortTextLength=$this->getParam('shortTextLength');
		// получаем случайную статью, если указан псид, то ищем в дочерних, иначе по всей базе, исключая удаленные и выключенные
		$widgetHTML="";
		$art = $this->getRandArticle($psid);
		if ($art) {
			$widgetHTML .= "<div class=\"w_randarticle\">";
			if($show_Doptitle&&$Doptitle)	{
				$dop_href=Router::_($LinkDoptitle);		
				$widgetHTML.="<div class=\"toplabel\"><a href=\"".$dop_href."\">".$Doptitle."</a></div>";		 		
			}
			$img=$this->getImage($art->a_thumb);
			if($img){ $widgetHTML.= "<div class=\"a_thumb\"><img src=\"".$img."\" alt=\"\" /></div>";	}		
			$href=Router::_("index.php?module=article&view=read&psid=".$art->a_id."&alias=".$art->a_alias);
			$widgetHTML.="<div class=\"a_title\">";
			$widgetHTML.="<a href=\"".$href."\">".$art->a_title."</a>";
			$widgetHTML.="</div>";
			$widgetHTML.="<div class=\"a_text\">";
			Event::raise('content.prepare', array("clean_all"=>"1"), $art->a_text);
			$first_hr=mb_strpos($art->a_text,'<hr id="system-readmore"',0);
			if ($first_hr) $widgetHTML.=mb_substr($art->a_text,0,$first_hr);	
			// else $widgetHTML.=mb_substr(strip_tags($art->a_text), 0, $shortTextLength)."...";
			else $widgetHTML.=Text::cutHtml($art->a_text, $shortTextLength);
			
			$widgetHTML.="</div>";
			$widgetHTML.="<div class=\"readMore\"><a href=\"".$href."\">".Text::_('Read more')."</a></div>";
			$widgetHTML.="</div>";
		}
		else $widgetHTML.= "";
		return $widgetHTML;
	}
	public function getRandArticle($aid=0) {
		$query = "SELECT a.a_id FROM #__articles AS a WHERE a.a_published=1 AND a.a_deleted=0";
		if($aid) $query.= " AND a.a_parent_id=".intval($aid);
		Database::getInstance()->setQuery($query);
		if ($ids_res=Database::getInstance()->loadObjectList()) {
			$rand=rand(0,count($ids_res)-1);
			$a_id=$ids_res[$rand]->a_id;
			$query = "SELECT a.*, u.u_nickname AS author FROM #__articles AS a
					LEFT JOIN #__users AS u ON a.a_author_id=u.u_id
					WHERE a.a_id=".$a_id." AND a.a_deleted=0";
			Database::getInstance()->setQuery($query);
			Database::getInstance()->loadObject($art);
			if ($art->a_parent_id != 0) {
				$query = "SELECT a_title FROM #__articles WHERE a_id=".$art->a_parent_id;
				Database::getInstance()->setQuery($query);
				$art->parentTitle = strval(Database::getInstance()->loadResult());
			}
			return $art;
		}
		else return false;
	}
	public function getImage($img) {
		$imgpath=BARMAZ_UF_PATH."article".DS.'thumbs'.DS.Files::splitAppendix($img,true);
		if(file_exists($imgpath) && is_file($imgpath)) {
			$imgurl=BARMAZ_UF."/article/thumbs/".Files::splitAppendix($img);
		} else $imgurl="";
		return $imgurl;
	}
}
?>