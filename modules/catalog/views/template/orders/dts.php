<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

if($this->dt_list){
	$new_dts_ids=array();
	foreach ($this->dt_list as $k=>$obj){
		if($obj->dt_price>0){
			$this->dt_list[$k]->dt_name.=" (".number_format(Currency::getInstance()->convert($obj->dt_price, $obj->dt_currency), catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR, DEFAULT_THOUSAND_SEPARATOR)." ".Currency::getShortName(DEFAULT_CURRENCY).")";
		}
		if($obj->dt_comments){
			$this->dt_list[$k]->dt_name.="<span class=\"delivery_comment\">".$obj->dt_comments."</span>";
		}
		if($obj->dt_logo){
			$filename=BARMAZ_UF_PATH."catalog".DS."dts".DS.Files::splitAppendix($obj->dt_logo,true);
			if (Files::isImage($filename)) {
				$filelink=BARMAZ_UF."/catalog/dts/".Files::splitAppendix($obj->dt_logo);
				$this->dt_list[$k]->dt_name.="<img class=\"dts_logo\" src=\"".$filelink."\" alt=\"\">";
			}
		}
		$new_dts_ids[]=$obj->dt_id;
	}
	if(!in_array($this->dt_selected, $new_dts_ids)) $this->dt_selected = $this->dt_list[0]->dt_id;
	
	echo HTMLControls::renderLabelField("delivery_type",Text::_("Delivery type")." : ");
	if (count($this->dt_list)==1){
		echo HTMLControls::renderLabelField(false, $this->dt_list[0]->dt_name, false, "", "delivery");
		echo HTMLControls::renderHiddenField('delivery_type',$this->dt_list[0]->dt_id);
	} else {
		echo HTMLControls::renderRadioGroup('delivery_type', '', 'dt_id', 'dt_name', $this->dt_list, $this->dt_selected, "setOrderUserDataForm()","", true);
	}
}


?>