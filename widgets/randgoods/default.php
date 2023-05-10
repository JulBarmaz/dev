<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_WIDGET_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class randgoodsWidget extends Widget {
	protected $_requiredModules = array("catalog");
	
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("ShowDopTitle", "boolean", 1);
		$this->addParam("DopTitle", "string", "");
		$this->addParam("LinkDopTitle", "string", "");
		$this->addParam("goodsGroupid", "integer", 0);
		$this->addParam("goodsid", "integer", 0);
		$this->addParam("onlyNew", "boolean", 0);
		$this->addParam("onlyHit", "boolean", 0);
		$this->addParam("showDescription", "boolean", 1);
		$this->addParam("shortTextLength", "integer", 200);
	}
	public function render() {
		$grpsid	= $this->getParam('goodsGroupid');
		$gid	= $this->getParam('goodsid');
		$new	= $this->getParam('onlyNew');
		$hit	= $this->getParam('onlyHit');

		$Doptitle = $this->getParam('DopTitle');
		$LinkDoptitle = $this->getParam('LinkDopTitle');
		$show_Doptitle = $this->getParam('ShowDopTitle');
		$showDescription = $this->getParam('showDescription');
		$shortTextLength=$this->getParam('shortTextLength');
		$widgetHTML="";
		// получаем случайный товар
		// если указан псид, то ищем в дочерних, иначе по всей базе исключая удаленные и выключенные
		$helper=Module::getHelper("goods","catalog");
		$goods = $helper->getRandomGoods($grpsid,$gid,$new,$hit);
		if (count($goods)) {
			$gids=array();
			foreach($goods as $gds)	$gids[$gds->g_id]=$gds->g_id;
//			$options_gids=Module::getHelper("goods","catalog")->haveOptions($gids);
//			$discounts=Module::getHelper("goods","catalog")->getDiscounts($gids);
			$widgetHTML .= "<div class=\"w_randgoods float-fix\">";
			if($show_Doptitle&&$Doptitle)	{
				if ($LinkDoptitle) $widgetHTML.="<div class=\"toplabel\"><a href=\"".Router::_($LinkDoptitle)."\">".$Doptitle."</a></div>";
				else $widgetHTML.="<div class=\"toplabel\"><span>".$Doptitle."</span></div>";
			}
			foreach($goods as $gds){
				if($gds->g_thumb_url){
					$widgetHTML.= "<div class=\"g_thumb\"><a href=\"".$gds->g_goods_url."\"><img width=\"".catalogConfig::$thumb_width."\" src=\"".$gds->g_thumb_url."\" alt='".$gds->g_name."' /></a></div>";
				}
				$widgetHTML.="<div class=\"g_title\">";
				$widgetHTML.="<a href=\"".$gds->g_goods_url."\">".$gds->g_name."</a>";
				$widgetHTML.="</div>";
				if($showDescription){
					$widgetHTML.="<div class=\"g_text\">";
					$first_hr=mb_strpos($gds->g_comments,'<hr id="system-readmore"',0);
					if ($first_hr) $widgetHTML.=mb_substr($gds->g_comments,0,$first_hr);
					// elseif(strip_tags($gds->g_comments)) $widgetHTML.=mb_substr(strip_tags($gds->g_comments), 0,$shortTextLength)."...";
					elseif(trim(strip_tags($gds->g_comments))) $widgetHTML.=Text::cutHtml($gds->g_comments, $shortTextLength);
					$widgetHTML.="</div>";
					$widgetHTML.="<div class=\"readMore\"><a href=\"".$gds->g_goods_url."\">".Text::_('Read more')."</a></div>";
				}
			}
			$widgetHTML.="</div>";
		}	else  $widgetHTML = "";
		return $widgetHTML;
	}
}
?>