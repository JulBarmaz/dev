<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");
class robokassaPaymentClass extends catalogPayment {
	/* ROBOKASSA API */
	/* index.php?module=catalog&view=orders&layout=payment&mode=success&order_id_alias=InvId */
	private $_errors = array();
	private $_payPoint = "http://localhost/";
	private $_allowed_currencies=array("USD","EUR","RUB");
	public function __construct($payment_type,$_data=false) {
		parent::__construct($payment_type,$_data);
	}
	public static function getOrderId($mode){
		return Request::getSafe("InvId", 0);
	}
	public function getParamsMask(){
		$params = parent::getParamsMask();
		
		$params['Display_type']["vtype"] = "select";
		$display_type = array(
				"logo" => Text::_('Logo'),
				"button" => Text::_('Button'),
				"both" => Text::_('Logo and button')
		);
		$params['Display_type']["source"]=Params::transformParamsSource($display_type);
		$params['Display_type']["vdefault"]="logo";
		
		$params["TechnicData"]["vtype"]="tab"; $params["TechnicData"]["vdefault"]=Text::_("Technics Data");
		
		$params["IdentShop"]["vtype"]="string";	$params["IdentShop"]["vdefault"]=Portal::getURI(1,1);
		$params["IdentShop"]["descr"]="обозначение магазина ТОЛЬКО для использования интерфейсом инициализации оплаты, то есть для понимания системой ROBOKASSA в адрес какого магазина будет проводиться платеж. Идентификатор может содержать латинские буквы, цифры и символы: . - _.";
		
		
		$params['rk_user']["vtype"]="string";		 	$params['rk_user']["vdefault"]="magazin";
		$params['rk_password_1']["vtype"]="string"; 	$params['rk_password_1']["vdefault"]="password_1";
		$params['rk_password_1']["descr"]="Используется интерфейсом инициализации оплаты в боевой среде. Длина не менее 8 символов, должен содержать хотя бы одну букву и хотя бы одну цифр.";
		$params['rk_password_2']["vtype"]="string"; 	$params['rk_password_2']["vdefault"]="password_2";
		$params['rk_password_2']["descr"]="Используется интерфейсом оповещения о платеже, XML-интерфейсами в боевой среде. Длина не менее 8 символов, должен содержать хотя бы один символ и хотя бы одну цифру.";
		// method of send data
		$params["ResultURL"]["vtype"]="ro_string";	$params["ResultURL"]["vdefault"]=Portal::getSSLURI(1, 1)."index.php?module=catalog&view=orders&layout=payment&mode=recieve";
		$params["ResultURL"]["descr"]="";
		
		$params['ResultURL_method']["vtype"]="select";
		$params['ResultURL_method']["source"]=Params::transformParamsSource(array("GET"=>"GET","POST"=>"POST"));
		$params['ResultURL_method']["vdefault"]="POST";
		
		$params["SuccessURL"]["vtype"]="ro_string";	$params["SuccessURL"]["vdefault"]=Portal::getSSLURI(1, 1)."index.php?module=catalog&view=orders&layout=payment&payment=robokassa&mode=success";
		$params['SuccessURL_method']["vtype"]="select";
		$params['SuccessURL_method']["source"]=Params::transformParamsSource(array("GET"=>"GET","POST"=>"POST"));
		$params['SuccessURL_method']["vdefault"]="POST";
		
		$params["FailURL"]["vtype"]="ro_string";	$params["FailURL"]["vdefault"]=Portal::getSSLURI(1, 1)."index.php?module=catalog&view=orders&layout=payment&payment=robokassa&mode=cancel";
		$params['FailURL_method']["vtype"]="select";
		$params['FailURL_method']["source"]=Params::transformParamsSource(array("GET"=>"GET","POST"=>"POST"));
		$params['FailURL_method']["vdefault"]="POST";
		
		// fiskalisation
		$params["FiskalisationData"]["vtype"]="tab"; $params["FiskalisationData"]["vdefault"]=Text::_("Fiskalisation data");
		
		$params['rk_fiskalisation']["vtype"]="boolean";		$params['rk_fiskalisation']["vdefault"]=true;
		$params['rk_sno']["vtype"]="select";
		$list_sn=array("osn"=>Text::_('rk_osn'),
				"usn_income"=>Text::_('rk_usn_income'),
				"usn_income_outcome"=>Text::_('rk_usn_income_outcome'),
				"envd"=>Text::_('rk_envd'),
				"esn"=>Text::_('rk_esn'),
				"patent"=>Text::_('rk_patent')
		);
		$params['rk_sno']["source"]=Params::transformParamsSource($list_sn);
		$params['rk_sno']["vdefault"]="usn_income";
		
		
		$params['rk_payment_method']["vtype"]="select";
		$pm=$this->fs_payment_method();
		$params['rk_payment_method']["source"]=Params::transformParamsSource($pm['list']);
		$params['rk_payment_method']["vjs"]="toggleParamElementDescripion(this);";
		$params['rk_payment_method']["vdefault"]="full_payment";
		$params['rk_payment_method']["descr"]="Признак способа расчёта. Будет подставлен в позицию чека по умолчанию.";
		$params['rk_payment_method']["js_descr"]=$pm['descr_ru'];
		
		
		$params['rk_payment_object']["vtype"]="select";
		$po=$this->fs_payment_thing();
		$params['rk_payment_object']["source"]=Params::transformParamsSource($po['list']);
		$params['rk_payment_object']["vdefault"]="commodity";
		$params['rk_payment_object']["vjs"]="toggleParamElementDescripion(this);";
		$params['rk_payment_object']["descr"]="Признак предмета расчёта.Будет подставлен в позицию чека по умолчанию.";
		$params['rk_payment_object']["js_descr"]=$po['descr_ru'];
		
		$params['rk_tax']["vtype"]="select";
		$pt=$this->fs_payment_tax();
		$params['rk_tax']["source"]=Params::transformParamsSource($pt['list']);
		$params['rk_tax']["vdefault"]="none";
		$params["rk_tax"]["descr"]="Это поле устанавливает налоговую ставку в ККТ. Определяется для каждого вида товара по отдельности, но за все единицы конкретного товара вместе.";
		
		
		
		$params["TestData"]["vtype"]="tab"; $params["TestData"]["vdefault"]=Text::_("Data for testing");
		
		// debug and test
		$params['rk_sandbox']["vtype"]="boolean";		$params['rk_sandbox']["vdefault"]=true;
		$params['rk_testpassword_1']["vtype"]="string"; 	$params['rk_testpassword_1']["vdefault"]="test_password_1";
		$params['rk_testpassword_1']["descr"]="Используется интерфейсом инициализации оплаты в тестовой среде. Длина не менее 8 символов, должен содержать хотя бы одну букву и хотя бы одну цифр.";
		$params['rk_testpassword_2']["vtype"]="string"; 	$params['rk_testpassword_2']["vdefault"]="test_password_2";
		$params['rk_testpassword_2']["descr"]="Используется интерфейсом оповещения о платеже, XML-интерфейсами в тестовой среде. Длина не менее 8 символов, должен содержать хотя бы один символ и хотя бы одну цифру.";
		
		return $params;
	}
	private function initData(){
		// путь теперь один
		$this->_payPoint = "https://auth.robokassa.ru/Merchant/Index.aspx";
		//$this->getConfigValue("rk_sandbox");
		/*if ($this->getConfigValue("rk_sandbox")){
		 $this->_payPoint = "http://test.robokassa.ru/Index.aspx";
		 } else {
		 $this->_payPoint = "https://auth.robokassa.ru/Merchant/Index.aspx";
		 }*/
		if(!in_array(Currency::getCode($this->payment->pt_currency), $this->_allowed_currencies)) {
			echo "<p class=\"error\">".Text::_("Unsupported currency")."</p	>";
			return false;
		}
		return true;
	}
	public function show() {
		if ($this->initData()) {
			$this->showPanel();
		}
	}
	public function recieve(){
		@ob_end_clean();
		//Util::showArray($_REQUEST);
		Portal::getInstance()->disableTemplate();
		if ($this->getConfigValue("rk_sandbox")){
			$mrh_pass2=$this->getConfigValue("rk_testpassword_2","");
			$TestPl=1;
		}else{
			$mrh_pass2=$this->getConfigValue("rk_password_2","");
			$TestPl=0;
		}
		$rk_invoice=Request::getInt("inv_id",0);// shop's invoice number
		$inv_id = $this->order->o_id;	// shop's invoice number
		$order_summ  = $this->getOrderTotalSumForPayment();   // invoice summ
		$out_summ = Request::getSafe("OutSum","");
		$crc= Request::getSafe("SignatureValue","");
		$my_crc = md5($order_summ.":".$inv_id.":".$mrh_pass2.":shp_order_id=".$inv_id);
		if(strtoupper($crc)==strtoupper($my_crc)) {
			echo "OK".$inv_id;
			$this->setPaid();
			Util::halt();
		}
	}
	public function success(){
		if ($this->getConfigValue("rk_sandbox")){
			$mrh_pass1=$this->getConfigValue("rk_testpassword_1","");
			$TestPl=1;
		}else{
			$mrh_pass1=$this->getConfigValue("rk_password_1","");
			$TestPl=0;
		}
		$inv_id = $this->order->o_id;	// shop's invoice number
		$order_summ  = $this->getOrderTotalSumForPayment();   // invoice summ
		$out_summ = Request::getSafe("OutSum","");
		$crc= Request::getSafe("SignatureValue","");
		$my_crc = md5($out_summ.":".$inv_id.":".$mrh_pass1.":shp_order_id=".$inv_id);
		if(strtoupper($crc)==strtoupper($my_crc)) {
			if (floatval($order_summ)==floatval($out_summ)){
				$data["OutSum"]=$out_summ;
				$data["OutDate"]=Date::GetdateRus(time(),1,false);
				$this->setPaymentResultData($data);
				echo "<p class=\"ok\">".Text::_("Payment successful")."</p	>";
			} else {
				echo "<p class=\"error\">".Text::_("Incorrect payment sum")."</p	>";
				echo "<p class=\"error\">".Text::_("Please, contact administrator to resolve this problem")."</p	>";
			}
		} else {
			echo "<p class=\"error\">".Text::_("Signature failure")."</p	>";
			echo "<p class=\"error\">".Text::_("Please, contact administrator to resolve this problem")."</p	>";
		}
	}
	public function fail(){
		$this->cancel();
	}
	public function cancel(){
		echo $this->getPaymentTitle();
		echo "<p class=\"error\">".Text::_("Payment canceled")."</p	>";
		echo $this->getPaymentButton();
	}
	private function showPanel() {
		echo $this->getPaymentTitle();
		if ($this->order->o_paid){
			echo "<p class=\"ok\">".Text::_("Payment already done")."</p	>";
		} else {
			echo $this->getPaymentButton();
		}
	}
	private function getPaymentTitle(){
		$html = "<h3 class=\"title\">".Text::_("Order payment")." № ".$this->order->o_id." ".Text::_("from")." ".Date::GetdateRus($this->order->o_date)."</h3>";
		$html.= "<h4 class=\"sub-title\">".Text::_("Your payment type")." : ".$this->payment->pt_name."</h4>";
		return $html;
	}
	private function getPaymentButton(){
		$this->initData();
		$mrh_login=$this->getConfigValue("rk_user","");
		if ($this->getConfigValue("rk_sandbox")){
			$mrh_pass1=$this->getConfigValue("rk_testpassword_1","");
			$TestPl=1;
		}else{
			$mrh_pass1=$this->getConfigValue("rk_password_1","");
			$TestPl=0;
		}
		$type_request=$this->getConfigValue("ResultURL_method","");
		$inv_id    = $this->order->o_id;	// shop's invoice number
		$inv_desc  = Text::_("Order payment")." № ".$this->order->o_id." ".Text::_("from")." ".Date::GetdateRus($this->order->o_date);   // invoice desc
		$out_summ  = $this->getOrderTotalSumForPayment();   // invoice summ
		$currency = Currency::getCode($this->order->o_currency);
		
		$Receipt=""; // состав чека
		$demand_fiscal=$this->getConfigValue("rk_fiskalisation",false);
		// если у нас требуется фискализация
		if($demand_fiscal){
			$order_list= new stdClass();
			// сначала обязательные параметры
			$order_list->sno=$this->getConfigValue("rk_sno");
			// теперь состав чека
			$im_list=array();
			if(count($this->items)){
				foreach($this->items as $item)
				{
					$elem=new stdClass();
					
					$elem->name=$item->i_g_name;
					$elem->quantity=$item->i_g_quantity;
					$elem->sum=$item->i_g_sum;
					if(isset($item->rk_payment_method))	$elem->payment_method=$item->rk_payment_method;
					else $elem->payment_method=$this->getConfigValue("rk_payment_method");
					if(isset($item->rk_payment_object))	$elem->payment_object=$item->rk_payment_object;
					else	$elem->payment_object=$this->getConfigValue("rk_payment_object");
					if(isset($item->rk_tax))	$elem->tax=$item->rk_tax;
					else $elem->tax=$this->getConfigValue("rk_tax");
					$im_list[]=$elem;
					if(isset($item->rk_nomenclature_code))	$elem->nomenclature_code=$item->rk_nomenclature_code;
				}
				$order_list->items=$im_list;
				$Receipt=urlencode(json_encode($order_list));
			}
			
			
			$crc  = md5($mrh_login.":".$out_summ.":".$inv_id.":".$mrh_pass1.":shp_order_id=".$inv_id);
		}else{
			$crc  = md5($mrh_login.":".$out_summ.":".$inv_id.":".$mrh_pass1.":shp_order_id=".$inv_id);
		}
		
		
		$html="";
		if($type_request=='POST'){
			$html.="<div class=\"payment_button row\"><div class=\"col-sm-12\">";
			$html.="<form id=\"rk_pay\" method=\"POST\" action=\"".$this->_payPoint."\">";
			$html.=HTMLControls::renderHiddenField("MrchLogin",$mrh_login);
			$html.=HTMLControls::renderHiddenField("OutSum",$out_summ);
			$html.=HTMLControls::renderHiddenField("InvId",$inv_id);
			$html.=HTMLControls::renderHiddenField("Description",$inv_desc);
			$html.=HTMLControls::renderHiddenField("SignatureValue",$crc);
			$html.=HTMLControls::renderHiddenField("sIncCurrLabel",$currency);
			$html.=HTMLControls::renderHiddenField("IsTest",$this->getConfigValue("rk_sandbox"));
			$html.=HTMLControls::renderHiddenField("shp_order_id",$inv_id);
			$html_1 = '<button class="linkButton btn" onclick="go_form(\'rk_pay\');"><img src="/images/payments/robokassa.jpg" align="left" style="margin-right:7px;" alt="ROBOKASSA" /></button>';
			$html_2 = '<button class="linkButton btn btn-info" onclick="go_form(\'rk_pay\');">'.Text::_("Pay").'</button>';
			switch($this->getConfigValue("Display_type","logo")){
				case "both":
					$html.= $html_1."<span class=\"button_separator\" style=\"display:inline-block;\"></span>".$html_2;
					break;
				case "button":
					$html.= $html_2;
					break;
				case "logo":
				default:
					$html.= $html_1;
					break;
			}
			$html.="</div></div>";
			
		} else {
			$url = $this->_payPoint."?MrchLogin=".$mrh_login."&OutSum=".$out_summ."&InvId=".$inv_id."&Desc=".$inv_desc."&SignatureValue=".$crc."&shp_order_id=".$inv_id."&sIncCurrLabel=".$currency."&IsTest=".$TestPl;
			$html.="<div class=\"payment_button row\"><div class=\"col-sm-12\">";
			$html_1 = "<a class=\"linkButton btn\" href=\"".$url."\"><img src=\"/images/payments/robokassa.jpg\" align=\"left\" style=\"margin-right:7px;\" alt=\"ROBOKASSA\" /></a> \n";
			$html_2 = "<a class=\"linkButton btn btn-info\" href=\"".$url."\">".Text::_("Pay")."</a> \n";
			switch($this->getConfigValue("Display_type","logo")){
				case "both":
					$html.= $html_1."<span class=\"button_separator\" style=\"display:inline-block;\"></span>".$html_2;
					break;
				case "button":
					$html.= $html_2;
					break;
				case "logo":
				default:
					$html.= $html_1;
					break;
			}
			$html.= "</div></div>";
		}
		return $html;
	}
	public function renderInfo($data="") {
		if (!$data) $data=$this->getData();
		$html="<br />";
		if (is_array($data)){
			if (isset($data["OutSum"])&&isset($data["OutDate"])) $html.="<b>".Text::_("Payment sum")." : </b>".$data["OutSum"]."<br /><b>".Text::_("Payment date")." : </b>".$data["OutDate"]."<br />";
		}
		return $html;
	}
	// Признак способа расчёта.
	public function fs_payment_method()
	{
		$arr_list=array(
				"full_prepayment"=>Text::_("rk_full_prepayment"),
				"prepayment"=>Text::_("rk_prepayment"),
				"advance"=>Text::_("rk_advance"),
				"full_payment"=>Text::_("rk_full_payment"),
				"partial_payment"=>Text::_("rk_partial_payment"),
				"credit"=>Text::_("rk_credit"),
				"credit_payment"=>Text::_("rk_credit_payment")
		);
		// русский
		$arr_descr_ru=array(
				"full_prepayment"=>Text::_("rk_full_prepayment_descr"),
				"prepayment"=>Text::_("rk_prepayment_descr"),
				"advance"=>Text::_("rk_advance_descr"),
				"full_payment"=>Text::_("rk_full_payment_descr"),
				"partial_payment"=>Text::_("rk_partial_payment_descr"),
				"credit"=>Text::_("rk_credit_descr"),
				"credit_payment"=>Text::_("rk_credit_payment_descr")
		);
		$arr['list']=$arr_list;
		$arr['descr_ru']=$arr_descr_ru;
		return $arr;
	}
	// Признак способа расчёта.
	public function fs_payment_thing()
	{
		$arr_list=array(
				"commodity"=>Text::_("rk_commodity"),
				"excise"=>Text::_("rk_excise"),
				"job"=>Text::_("rk_job"),
				"service"=>Text::_("rk_service"),
				"gambling_bet"=>Text::_("rk_gambling_bet"),
				"gambling_prize"=>Text::_("rk_gambling_prize"),
				"lottery"=>Text::_("rk_lottery"),
				"lottery_prize"=>Text::_("rk_lottery_prize"),
				"intellectual_activity"=>Text::_("rk_intellectual_activity"),
				"payment"=>Text::_("rk_payment"),
				"agent_commission"=>Text::_("rk_agent_commission"),
				"composite"=>Text::_("rk_composite"),
				"another"=>Text::_("rk_another"),
				"property_right"=>Text::_("rk_property_right"),
				"non-operating_gain"=>Text::_("rk_non-operating_gain"),
				"insurance_premium"=>Text::_("rk_insurance_premium"),
				"sales_tax"=>Text::_("rk_sales_tax"),
				"resort_fee"=>Text::_("rk_resort_fee")
		);
		$arr_descr_ru=array(
				"commodity"=>Text::_("rk_commodity_descr"),
				"excise"=>Text::_("rk_excise_descr"),
				"job"=>Text::_("rk_job_descr"),
				"service"=>Text::_("rk_service_descr"),
				"gambling_bet"=>Text::_("rk_gambling_bet_descr"),
				"gambling_prize"=>Text::_("rk_gambling_prize_descr"),
				"lottery"=>Text::_("rk_lottery_descr"),
				"lottery_prize"=>Text::_("rk_lottery_prize_descr"),
				"intellectual_activity"=>Text::_("rk_intellectual_activity_descr"),
				"payment"=>Text::_("rk_payment_descr"),
				"agent_commission"=>Text::_("rk_agent_commission_descr"),
				"composite"=>Text::_("rk_composite_descr"),
				"another"=>Text::_("rk_another_descr"),
				"property_right"=>Text::_("rk_property_right_descr"),
				"non-operating_gain"=>Text::_("rk_non-operating_gain_descr"),
				"insurance_premium"=>Text::_("rk_insurance_premium_descr"),
				"sales_tax"=>Text::_("rk_sales_tax_descr"),
				"resort_fee"=>Text::_("rk_resort_fee_descr")
		);
		$arr['list']=$arr_list;
		$arr['descr_ru']=$arr_descr_ru;
		return $arr;
	}
	public function fs_payment_tax()
	{
		$arr_list=array(
				"none"=>Text::_("rk_none"),
				"vat0"=>Text::_("rk_vat0"),
				"vat10"=>Text::_("rk_vat10"),
				"vat110"=>Text::_("rk_vat110"),
				"vat20"=>Text::_("rk_vat20"),
				"vat120"=>Text::_("rk_vat120")
				
		);
		$arr_descr_ru=array(
		);
		$arr['list']=$arr_list;
		$arr['descr_ru']=$arr_descr_ru;
		return $arr;
	}
}
?>