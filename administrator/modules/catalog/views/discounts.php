<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class defaultViewDiscounts extends SpravView {
	public function modify($row) {
		$script="";
		$script.="function updateDiscountPeriodVisibility(checked){
					if(checked){
						$('#wrapper-d_start_date').hide();
						$('#wrapper-d_end_date').hide();
					} else {
						$('#wrapper-d_start_date').show();
						$('#wrapper-d_end_date').show();
					}
				}
				$(document).ready(function(){
					$('#d_period_unlimited').change(function() {
						updateDiscountPeriodVisibility($(this).attr('checked'));
					});
					updateDiscountPeriodVisibility($('#d_period_unlimited').attr('checked'));
				}); \n";
		if($script) Portal::getInstance()->addScriptDeclaration($script);
		parent::modify($row);
	
	}
}
?>