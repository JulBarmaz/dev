<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class simpleDeliveryClass extends catalogDelivery{
	protected $exc_required=array("psid","type_id","use_as_default","fullinfo","apartment");
	protected $need_recalc=0;

	public function getParamsMask(){
		$params = parent::getParamsMask();
		$params['require_address']["vtype"]="boolean"; 		$params['require_address']["vdefault"]=0;
		return $params;
	}
	public function save() {
		if (User::getInstance()->isLoggedIn()){
			$savedAddrId=Request::getInt('listAddr');
			$userdata=Userdata::getInstance(User::getInstance()->u_id);
			if(Request::getBool('save_adress'))	$userdata->saveAddress($savedAddrId);
		}
		$address = Address::getTmpl();
		foreach($address as $key=>$val) $address[$key]=stripslashes(Request::getSafe($key,""));
		$this->data=$address;
		return $this->data;
	}
	public function renderForm(){
		if (User::getInstance()->isLoggedIn()){
			$list_address=Userdata::getInstance(User::getInstance()->u_id)->getAddresses();
		} else {
			$list_address=array();
		}
		$required=$this->intersectRequired(Address::getRequiredTmpl());
		$address=Address::getTmpl();
		if(is_array($list_address)&&count($list_address))	{
			foreach($list_address as $key=>$val)	{
				$list_addr[$key]=$val['fullinfo'];
			}
		} else $list_addr=false;
		if(!isset($address['country_id'])) $address['country_id']=0;
		if(!isset($address['region_id'])) $address['region_id']=0;
		if(!isset($address['district_id'])) $address['district_id']=0;
		if(!isset($address['locality_id'])) $address['locality_id']=0;
		$require_country=(in_array("country",$required)||in_array("country_id",$required));
		$require_locality=(in_array("locality",$required)||in_array("locality_id",$required));
		$require_district=(in_array("district",$required)||in_array("district_id",$required));
		$require_region=(in_array("region",$required)||in_array("region_id",$required));
		$html="<div class=\"delivery_form\">";
		if ($this->getConfigValue("require_address")) {
			$html.="<fieldset><legend>".Text::_("Delivery address")."</legend>";
			$html.="<div id=\"useraddrselector\">";
			if($list_addr) {
				$selected_addr=Request::getInt("listAddr",0);
				if ($selected_addr) Portal::getInstance()->addScriptDeclaration("$(document).ready(function() {fillAddressPanel('#listAddr')});");
				$html.="<div class=\"row\"><div class=\"col-sm-5\">".HTMLControls::renderLabelField("listAddr", Text::_("Saved addresses"))."</div><div class=\"col-sm-7\">".HTMLControls::renderSelect('listAddr', 'listAddr', '', '', $list_addr, $selected_addr, 1, 'fillAddressPanel(this); orderUserdataChanged(this);')."</div></div>";
			}
			$html.= "</div>";
			$isLoggedIn = User::getInstance()->isLoggedIn();
			$html.= Address::renderEditor($address, (!siteConfig::$useTextAddress && $isLoggedIn), $isLoggedIn);
			$html.="<div class=\"addressPanel addressSubPanel\">";
			if($isLoggedIn) $html.="	<div class=\"row addr-save_adress\"><div class=\"col-sm-5 col-xs-9\">".HTMLControls::renderLabelField("save_adress", Text::_("Save address in list"))."</div><div class=\"col-sm-7 col-xs-3\">".HTMLControls::renderCheckbox("save_adress",1)."</div></div>";
			$html.= "</div>";
			$html.="</fieldset>";
		}
		$html.="</div>";
		return $html;
	}
	public function renderInfo($data=""){
		$html="";
		if ($this->getConfigValue("require_address")) {
			$html.="<br/>".HTMLControls::renderLabelField(false,"Delivery address",true).": ";
			if (isset($data["zipcode"])) $html.=$data["zipcode"].", ";
			if (siteConfig::$useTextAddress){
			  	if (isset($data["country"])) $html.=$data["country"].", ";
				if (isset($data["region"])) $html.=$data["region"].", ";
				if (isset($data["district"])) $html.=$data["district"].", ";
				if (isset($data["locality"])) $html.=$data["locality"].", ";
			} else {
				if (isset($data["country_id"])) $html.=Address::getCountryName($data["country_id"]).", ";
				if (isset($data["region_id"])) $html.=Address::getRegionName($data["region_id"]).", ";
				if (isset($data["district_id"])) $html.=Address::getDistrictName($data["district_id"]).", ";
				if (isset($data["locality_id"])) $html.=Address::getLocalityName($data["locality_id"]).", ";
			}
			if (isset($data["street"])&&$data["street"]) $html.=$data["street"].", ";
			if (isset($data["house"])&&$data["house"]) $html.=$data["house"].", ";
			if (isset($data["apartment"])&&$data["apartment"]) $html.=$data["apartment"];
		}
		return $html;
	}
	public function checkData(&$err_message){
		$no_errors=true;
		$address = Address::getTmpl();
		if ($this->getConfigValue("require_address")) {
			$required=$this->intersectRequired(Address::getRequiredTmpl());
			foreach($required as $key) {
				if(!Request::getSafe($key,"")||Request::getSafe($key,"")=="0") {
					$err_message[]=Text::_("Some fields not filled");
					$no_errors=false;
					break;
				}
			}
		}
		return $no_errors;
	}
}
?>