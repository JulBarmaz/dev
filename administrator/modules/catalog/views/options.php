<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class defaultViewoptions extends SpravView {
	//
	public function modify($row) {
		$script="";
		$tps=Module::getInstance()->getModel()->getOptionsTypes();
		if(count($tps)) {
			$script.="function updateQMBVVisibility(id){ \n
						var tps = new Array(); \n
					
					";
			foreach($tps as $t=>$ps){
				$script.="tps[".$t."]=".$ps->t_mb_quantitative."\n";
			}
			$script.="
					if(tps[id]==1){
						$('#wrapper-o_is_quantitative').show();
					} else {
						$('#o_is_quantitative').attr('checked',false);
						$('#wrapper-o_is_quantitative .CheckBoxLabelClass').removeClass('CheckboxChecked');
						$('#wrapper-o_is_quantitative').hide();
					}";
			$script.="}";
		}
		if(is_object($row) && $row->o_type){
			$script.="
					$(document).ready(function(){ updateQMBVVisibility(".$row->o_type."); }); \n";
		} else {
			$script.="
				$(document).ready(function(){ 
					$('#o_type').change(function() {
						updateQMBVVisibility($(this).val());
					});
					updateQMBVVisibility($('#o_type').val());
				}); \n";
		}
		if($script) Portal::getInstance()->addScriptDeclaration($script);
		parent::modify($row);
		
	}
	public function countOptionsVals(&$psid, &$row){
		$count=Module::getInstance()->getModel()->countOptionVals($psid);
		$vals_possible=Module::getInstance()->getModel()->valsPossible($psid);
		$link="index.php?module=catalog&view=optionvals&psid=".$psid;
		if($vals_possible){
			if($count)	return "<a href=\"".$link."\">".Text::_("Total").": ".$count."</a>";
			else return "<a href=\"".$link."\">".Text::_("Values absent")."</a>";
		} else {
			return Text::_("Values impossible");
		}
	}
}

?>