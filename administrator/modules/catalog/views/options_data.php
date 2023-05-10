<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class defaultViewoptions_data extends SpravView {
	public function countOptionsVals(&$psid, &$row){
		$count=Module::getInstance()->getModel()->countOptionVals($psid);
		$vals_possible=Module::getInstance()->getModel()->valsPossible($psid);
		$link="index.php?module=catalog&view=optionvals_data&psid=".$psid;
		if($vals_possible){
			if($count)	return "<a href=\"".$link."\">".Text::_("Total").": ".$count."</a>";
			else return "<a href=\"".$link."\">".Text::_("Values absent")."</a>";
		} else {
			return Text::_("Values impossible");
		}
	}
}

?>