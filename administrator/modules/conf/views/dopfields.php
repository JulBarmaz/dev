<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class defaultViewdopfields extends SpravView {
	//
	public function modify($row){
		$js="$(document).ready(function() {
			dopFieldTypeOnChange($('#f_type'));
			dopFieldTypeOnChange($('#p_f_type_select'));
		});";
		Portal::getInstance()->addScriptDeclaration($js);
		parent::modify($row);
	}	
	public function countFieldVals(&$psid, &$row, $hide_impossible=false){
		$vals_possible=($row && ($row->f_type==5 || $row->f_type==8));
		if($row && ($row->f_type==5 || $row->f_type==8)){
			$count=Module::getInstance()->getModel()->countFieldVals($psid);
			$link="index.php?module=conf&view=dopfields_choices&psid=".$psid;
			if($count)	return "<a href=\"".$link."\">".Text::_("Total").": ".$count."</a>";
			else return "<a href=\"".$link."\">".Text::_("Values absent")."</a>";
		} elseif($row) {
			return Text::_("Values impossible");
		} else {
			return Text::_("Values access will appear after save");
		}
	}
}

?>