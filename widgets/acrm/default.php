<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_WIDGET_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class acrmWidget extends Widget {
	protected $_requiredModules = array("acrm");
	
	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("Client_ID", "table_select", 0, false, "SELECT bcl_id AS fld_id, bcl_name AS fld_name FROM #__banners_clients ORDER BY fld_name");
		$this->addParam("Category_ID", "table_select", 0, false, "SELECT bc_id AS fld_id, bc_name AS fld_name FROM #__banners_categories ORDER BY fld_name");
		$this->addParam("Quantity", "integer", 1);
		$this->addParam("Show_titles", "boolean", 0);
		$this->addParam("Show_descriptions", "boolean", 0);
		$this->addParam("Randomize", "boolean", 0);
	}
	public function render() {
		$widgetHTML="";
		$client = intval($this->getParam('Client_ID'));
		$cat = intval($this->getParam('Category_ID'));
		$quantity = intval($this->getParam('Quantity'));
		$randomize = intval($this->getParam('Randomize'));
		$model = new ACRM();
		$items = $model->getItems($client, $cat, $quantity, $randomize);
		if (is_array($items)&&(count($items))) {
			foreach($items as $item){
				$widgetHTML.=$this->renderItem($item);
				$arr_ids[]=$item->b_id;
			}
			$items_ids=implode(",",$arr_ids);
			$widgetHTML.= HTMLControls::renderHiddenField("itms_ident",$items_ids,false,"itms_ident");
		}
		return $widgetHTML;
	}
	public function renderItem($item){
		$_html="<div class=\"itms\">";
		$width=intval($item->b_width);
		$height=intval($item->b_height);
		$name=$item->b_name;
		$target=$item->b_target;
		if($target) {
			if (Router::isFullLink($target)) $_blank=" target=\"_blank\""; else $_blank="";
			$_html.="<a rel=\"nofollow\" onclick=\"javascript:clickACRM('".$item->b_id."')\" ".$_blank." href=\"".$target."\">";
		}
		if ($item->b_image) {
			$imageurl=BARMAZ_UF."/acrm/i/".Files::splitAppendix($item->b_image);
			$imagefile=BARMAZ_UF_PATH."acrm".DS."i".DS.Files::getAppendix($item->b_image,true).DS.$item->b_image;
			if (Files::isFlash($imagefile)) {
				$_html.=HTMLControls::renderFlash($imageurl, $width, $height); 
			} elseif(Files::isImage($imagefile)) {
				$_html.=HTMLControls::renderImage($imageurl, false, $width, $height, $name, $name, false);
			}
		} elseif ($item->b_custom_code) {
			$_html.=html_entity_decode($item->b_custom_code);
		} else return '';
		if(intval($this->getParam('Show_titles')) && $name){
			$_html.="<div class=\"itms_title\">".$name."</div>";
		}
		if(intval($this->getParam('Show_descriptions')) && $item->b_descr){
			$_html.="<div class=\"itms_descr\">".$item->b_descr."</div>";
		}
		if($target) $_html.="</a>";
		$_html.="</div>";
		return $_html;
	}
}
?>

