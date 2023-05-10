<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_WIDGET_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class acrm_sliderWidget extends Widget {
	protected $_requiredModules = array("acrm");
	
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("Client_ID", "table_select", 0, false, "SELECT bcl_id AS fld_id, bcl_name AS fld_name FROM #__banners_clients ORDER BY fld_name");
		$this->addParam("Category_ID", "table_select", 0, false, "SELECT bc_id AS fld_id, bc_name AS fld_name FROM #__banners_categories ORDER BY fld_name");
		$this->addParam("Show_banner_description", "boolean", 0);
		$this->addParam("Show_navs", "select", "1", true, array("0"=>Text::_("N"), "1"=>Text::_("Circles"), "2"=>Text::_("Rectangles")));
		$this->addParam("Show_controls", "boolean", 1);
		$this->addParam("Slide_effect", "select", "scroll", true, array("scroll"=>"Scroll", "fade"=>"Fade"));
		$this->addParam("Interval", "integer", 5);
	}
	public function prepare() {
		Portal::getInstance()->addScript("/redistribution/jquery.plugins/jquery.bc_swipe.min.js"); 
	}
	public function render() {
		$widgetHTML = "";
		$widget_id = $this->getParam('Widget_ID');
		$slider_id = $widget_id."_acrm";
		$client = intval($this->getParam('Client_ID'));
		$cat = intval($this->getParam('Category_ID'));
		$show_navs = intval($this->getParam('Show_navs'));
		$show_controls = intval($this->getParam('Show_controls'));
		$interval = intval($this->getParam('Interval'))*1000;
		$model = new ACRM();
		
		$items = $model->getItems($client, $cat);
		if($slider_id){
			$indicators=""; $i=0; $_html="";
			if (is_array($items)&&(count($items))) {
				foreach($items as $item){
					//$widgetHTML.=$this->renderItem($item);
					$_html.= '<div class="item'.($i==0 ? '  active' : '').'">';
					if($item->b_target) {
						if (Router::isFullLink($item->b_target)) $_blank=" target=\"_blank\""; else $_blank="";
						$_html.="<a rel=\"nofollow\" onclick=\"javascript:clickACRM('".$item->b_id."')\" ".$_blank." href=\"".$item->b_target."\">";
					}
					if ($item->b_image) {
						$imageurl=BARMAZ_UF."/acrm/i/".Files::splitAppendix($item->b_image);
						$imagefile=BARMAZ_UF_PATH."acrm".DS."i".DS.Files::getAppendix($item->b_image,true).DS.$item->b_image;
						if(Files::isImage($imagefile)) {
							$_html.=HTMLControls::renderImage($imageurl, false, 0, 0, $item->b_name, $item->b_name, false);
						}
					}
					if($item->b_target) $_html.="</a>";
					if ($item->b_custom_code) {
						$_html.="<div class=\"slider-text slider-text-".$i."\">".html_entity_decode($item->b_custom_code)."</div>";
					}
					if(intval($this->getParam('Show_banner_description')) && $item->b_descr){
						$_html.="<div class=\"carousel-caption\">".$item->b_descr."</div>";
					}
					$indicators.='<li data-target="#'.$slider_id.'" data-slide-to="'.$i.'"'.($i==0 ? '  class="active"' : '').'></li>';
					$i++;
					$arr_ids[]=$item->b_id;
					$_html.= "</div>";
				}
				$items_ids=implode(",",$arr_ids);
				$widgetHTML.= HTMLControls::renderHiddenField("itms_ident",$items_ids,false,"itms_ident");
				
				$script="$(document).ready(function(){
						$('#".$slider_id."').carousel({ interval: ".$interval." });
						$('#".$slider_id.".carousel').bcSwipe({ threshold: 50 });
					});";
				Portal::getInstance()->addScriptDeclaration($script);
				$effect=$this->getParam("Slide_effect");
				$subclass="acrm-slider-".$effect;
				if($show_navs==2 && count($items)){
					$css="#".$slider_id." .carousel-indicators-2 li{ width:".(100/count($items))."%}";
					Portal::getInstance()->addStyle($css);
				}
				$widgetHTML .="<div id=\"".$slider_id."\" class=\"carousel slide acrm-slider ".$subclass."\" data-ride=\"carousel\">";
				if($show_navs) $widgetHTML .="<ol class=\"carousel-indicators carousel-indicators-".$show_navs."\">".$indicators."</ol>";
				$widgetHTML .="<div class=\"carousel-inner\">".$_html."</div>";
				if($show_controls) $widgetHTML .="<!-- Controls -->
												<a class=\"left carousel-control\" href=\"#".$slider_id."\" role=\"button\" data-slide=\"prev\">
													<span class=\"glyphicon glyphicon-chevron-left\"></span>
												</a>
												<a class=\"right carousel-control\" href=\"#".$slider_id."\" role=\"button\" data-slide=\"next\">
													<span class=\"glyphicon glyphicon-chevron-right\"></span>
												</a>";
				$widgetHTML .= "</div>";
			}
		}
		return $widgetHTML;
	}
}
?>

