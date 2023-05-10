<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class defaultViewoptionvals_data extends SpravView {
	//
	public function renderSprav(&$meta, &$rows, $column_class=""){
		$reestr = Module::getInstance()->get('reestr');
		$multy_code= $reestr->get('multy_code', 0);
		$title1=Module::getInstance()->getModel()->getOptionName($multy_code);
		$title2=Module::getInstance()->getModel()->getGoodsName($multy_code);
		$reestr->set('dop_head',"&quot;".$title1."&quot; ".Text::_("for")." ".$title2);
		parent::renderSprav($meta, $rows, $column_class);
	}
	public function getOptionName($psid,$row){
		$reestr = Module::getInstance()->get('reestr');
		$multy_code = $reestr->get('multy_code');
		return Module::getInstance()->getModel()->getOptionName($multy_code);
	}
	public function modify($row){
		parent::modify($row);
	}	
	
}

?>