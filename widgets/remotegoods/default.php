<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_WIDGET_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class remotegoodsWidget extends Widget {
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("ShowDopTitle", "boolean", 1);
		$this->addParam("DopTitle", "string", "");
		$this->addParam("LinkDopTitle", "string", "");
		$this->addParam("RemoteURL", "string", "");
		$this->addParam("RemoteQuery", "string", "module=catalog&option=ajax&task=getRandomGoods");
		$this->addParam("BannerID", "integer", 0);
		$this->addParam("RequestTimeout", "integer", 5);
		$this->addParam("showDescription", "boolean", 1);
		$this->addParam("shortTextLength", "integer", 200);
	}
	public function render() {
		$Doptitle = $this->getParam('DopTitle');
		$LinkDoptitle = $this->getParam('LinkDopTitle');
		$show_Doptitle = $this->getParam('ShowDopTitle');
		$RemoteURL = $this->getParam('RemoteURL');
		$RequestTimeout = $this->getParam('RequestTimeout');
		$RemoteQuery = htmlspecialchars_decode($this->getParam('RemoteQuery'));
		if (!isset($_COOKIE[md5($RemoteURL)])) $RemoteQuery .= "&rg_referer=".base64_encode(Portal::getURI(1, 1));
		$banner_id = $this->getParam('BannerID');
		$showDescription = $this->getParam('showDescription');
		$shortTextLength=$this->getParam('shortTextLength');
		$widgetHTML="";
		if ($RemoteURL && $RemoteQuery) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$RemoteURL); // set url to post to
			curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			//			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
			curl_setopt($ch, CURLOPT_TIMEOUT, $RequestTimeout); // times out after 4s
			curl_setopt($ch, CURLOPT_POST, 1); // set POST method
			curl_setopt($ch, CURLOPT_POSTFIELDS, $RemoteQuery); // add POST fields
			$result = curl_exec($ch); // run the whole process
			curl_close($ch);
			$goods[0]=json_decode($result);
			if (count($goods)) {
				$widgetHTML .= "<div class=\"w_remotegoods float-fix\">";
				if($show_Doptitle&&$Doptitle)	{
					if ($LinkDoptitle) $widgetHTML.="<div class=\"toplabel\"><a href=\"".Router::_($LinkDoptitle)."\">".$Doptitle."</a></div>";
				}
				if ($banner_id) {
					$js="$(document).ready(function() {	rgDisplayed(".$banner_id.") });";
					Portal::getInstance()->addScriptDeclaration($js);
					$onclick=" onclick=\"clickACRM(".$banner_id.");clickRGACRM('".md5($RemoteURL)."');\"";
				} else {
					$onclick=" onclick=\"clickRGACRM('".md5($RemoteURL)."');\"";
				}
				foreach($goods as $gds){
					if (is_object($gds)&&isset($gds->g_id)){
						if($gds->g_thumb_url){ $widgetHTML.= "<div class=\"g_thumb\"><a".$onclick." target=\"_blank\" href=\"".$gds->g_goods_url."\"><img width=\"".catalogConfig::$thumb_width."\" src=\"".$gds->g_thumb_url."\" alt='".$gds->g_name."' /></a></div>";	}
						$widgetHTML.="<div class=\"g_title\"><a".$onclick." target=\"_blank\" href=\"".$gds->g_goods_url."\">".$gds->g_name."</a></div>";
						if($showDescription){
							$widgetHTML.="<div class=\"g_text\">";
							$first_hr=mb_strpos($gds->g_comments,'<hr id="system-readmore"',0);
							if ($first_hr) $widgetHTML.=mb_substr($gds->g_comments,0,$first_hr);
							// elseif(strip_tags($gds->g_comments)) $widgetHTML.=mb_substr(strip_tags($gds->g_comments), 0, $shortTextLength)."...";
							elseif(trim(strip_tags($gds->g_comments))) $widgetHTML.=Text::cutHtml($gds->g_comments, $shortTextLength);
							$widgetHTML.="<div class=\"readMore\"><a".$onclick." target=\"_blank\" href=\"".$gds->g_goods_url."\">".Text::_('Read more')."</a></div>";
						}
						$widgetHTML.="</div>";
					}
				}
				$widgetHTML.="</div>";
			}	else  $widgetHTML = "";
		}
		return $widgetHTML;
	}
}
?>