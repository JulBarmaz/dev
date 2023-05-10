<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class defaultViewdopfields_groups extends SpravView {
	public function countFieldVals(&$psid, &$row, $hide_impossible=false){
		$count=Module::getInstance()->getModel()->countFieldVals($psid);
		if($count)	return Text::_("Total").": ".$count;
		else return Text::_("Values absent");
	}
}

?>