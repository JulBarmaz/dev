<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class webmoneyPaymentClass extends catalogPayment {
	private $_file='webmoney';

	public function getParamsMask(){
		$params = parent::getParamsMask();
		$params['wm_payee_purse']["vtype"]="string"; 		$params['wm_payee_purse']["vdefault"]="";
		$params['wm_link']["vtype"]="string"; 				$params['wm_link']["vdefault"]="https://merchant.webmoney.ru/lmi/payment.asp";
		$params['wm_signature']["vtype"]="string"; 			$params['wm_signature']["vdefault"]="de34rffgg231";
		return $params;
	}

	private function initConstants(){

	}
	/* Собственные функции*/
	private function showPanel() {
		echo "<h3 class=\"title\">".Text::_("Order payment")." № ".$this->order->o_id." ".Text::_("from")." ".Date::GetdateRus($this->order->o_date)."</h3>";
		echo "<h4>".Text::_("Your payment type")." : ".$this->payment->pt_name."</h4>";
		echo "<div class=\"payment_button row\"><div class=\"col-sm-12\">";
		// генерим форму запроса платежа
		// номер платежа пользователя - где то хранить и формировать
		$numplat="098".$this->order->o_id."000".rand(1,1000);
		// опускапем номер платежа в таблицу
		$this->setPaymentId($this->order->o_id,$numplat);
		// кошелек продавца - загнать в настройки
		$wmnum=$this->getConfigValue("wm_payee_purse"); //"R770880296983";
		$result_url=Router::_("index.php?module=catalog&view=orders&layout=payment&docname=result&order_id=".$this->order->o_id,false,false);
		$success_url=Router::_("index.php?module=catalog&view=orders&layout=payment&docname=success&order_id=".$this->order->o_id,false,false);
		$fail_url=Router::_("index.php?module=catalog&view=orders&layout=payment&docname=fail&order_id=".$this->order->o_id,false,false);
		$frm=new aForm('wm', 'POST', 'https://merchant.webmoney.ru/lmi/payment.asp',false);
		$frm->addInput(array(	'NAME'=>'LMI_PAYMENT_AMOUNT','TYPE'=>"hidden",'ID'=>'LMI_PAYMENT_AMOUNT','VAL'=>$this->getOrderTotalSumForPayment()));
		$frm->addInput(array(	'NAME'=>'LMI_PAYMENT_DESC','TYPE'=>"hidden",'ID'=>'LMI_PAYMENT_DESC','VAL'=>'paying for order '.$this->order->o_id));
		$frm->addInput(array(	'NAME'=>'LMI_PAYMENT_NO','TYPE'=>"hidden",'ID'=>'LMI_PAYMENT_NO','VAL'=>$numplat));
		$frm->addInput(array(	'NAME'=>'LMI_PAYEE_PURSE','TYPE'=>"hidden",'ID'=>'LMI_PAYEE_PURSE','VAL'=>$wmnum));
		$frm->addInput(array(	'NAME'=>'LMI_SIM_MODE','TYPE'=>"hidden",'ID'=>'LMI_SIM_MODE','VAL'=>0));
		$frm->addInput(array(	'NAME'=>'LMI_RESULT_URL','TYPE'=>"hidden",'ID'=>'LMI_RESULT_URL','VAL'=>$result_url));
		$frm->addInput(array(	'NAME'=>'LMI_SUCCESS_URL','TYPE'=>"hidden",'ID'=>'LMI_SUCCESS_URL','VAL'=>$success_url));
		$frm->addInput(array(	'NAME'=>'LMI_SUCCESS_METHOD','TYPE'=>"hidden",'ID'=>'LMI_SUCCESS_METHOD','VAL'=>2));
		$frm->addInput(array(	'NAME'=>'LMI_FAIL_URL','TYPE'=>"hidden",'ID'=>'LMI_FAIL_URL','VAL'=>$fail_url));
		$frm->addInput(array(	'NAME'=>'LMI_FAIL_METHOD','TYPE'=>"hidden",'ID'=>'LMI_FAIL_METHOD','VAL'=>2));
		$frm->addInput(array(	'NAME'=>'BARMAZ_TOKEN','TYPE'=>"hidden",'ID'=>'BARMAZ_TOKEN','VAL'=>Session::getToken()));
		$frm->addInput(array(	'NAME'=>'PHPSESSID','TYPE'=>"hidden",'ID'=>'PHPSESSID','VAL'=>Request::getSafe("PHPSESSID")));
		$frm->addInput(array( "TYPE"=>"submit","CLASS"=>"commonButton btn btn-info", "VAL"=>Text::_("Pay"),	"NAME"=>"save", "ID"=>"save"));
		$frm->startLayout();
		$frm->renderInputPart('save');
		$frm->endLayout();
		echo "</div></div>";
	}
	private function setPaymentId($ordid,$tmpid) {
		if($tmpid){
			$db=Database::getInstance();
			$db->setQuery("UPDATE orders SET o_pt_tmpid=".(int)$tmpid." WHERE o_id=".(int)$ordid);
			return $db->query();
		}
		return false;
	}
	public function show() {
		$docname=Request::getSafe('docname','');
		$this->initConstants();
		switch($docname){
			case 'fail':
				$this->showFail();
				Session::unsetVar('wm_data');
				break;
			case 'success':
				$this->showSuccess();
				//Session::unsetVar('wm_data');
				break;
			case 'result':
				Session::unsetVar('wm_data');
				$this->proceedPayment();
				break;
			default:
				Session::unsetVar('wm_data');
				$this->showPanel();
				break;
		}
	}
	public function showFail(){
		echo "<p class=\"error\">".Text::_("Payment canceled")."</p	>";
	}
	public function showSuccess() {
		$data=Session::getVar('wm_data');
		$str='';
		if(isset($data["LMI_PAYEE_PURSE"])) $str.=$data["LMI_PAYEE_PURSE"];
			
		if(isset($data["LMI_PAYMENT_AMOUNT"])) $str.=$data["LMI_PAYMENT_AMOUNT"];
		if(isset($data["LMI_PAYMENT_NO"])) $str.=$data["LMI_PAYMENT_NO"];
		if(isset($data["LMI_MODE"])) $str.=$data["LMI_MODE"];
		if(isset($data["LMI_SYS_INVS_NO"])) $str.=$data["LMI_SYS_INVS_NO"];
		if(isset($data["LMI_SYS_TRANS_NO"])) $str.=$data["LMI_SYS_TRANS_NO"];
		if(isset($data["LMI_SYS_TRANS_DATE"])) $str.=$data["LMI_SYS_TRANS_DATE"];
		$str.=$this->getConfigValue("wm_signature");
		if(isset($data["LMI_PAYER_PURSE"])) $str.=$data["LMI_PAYER_PURSE"];
		if(isset($data["LMI_PAYER_WM"])) $str.=$data["LMI_PAYER_WM"];
		$result=strtoupper(md5($str));
		$hash=$data["LMI_HASH"];
		if($result!=$hash) {
			echo "<p class=\"error\">".Text::_("Payment final confirm error")."</p	>";
			echo "<p class=\"error\">".Text::_("Please, contact administrator to resolve this problem")."</p	>";

		} else if(floatval($data["LMI_PAYMENT_AMOUNT"])!=$this->getOrderTotalSumForPayment()) {
			echo "<p class=\"error\">".Text::_("Incorrect payment sum")."</p	>";
			echo "<p class=\"error\">".Text::_("Please, contact administrator to resolve this problem")."</p	>";
		} else if(floatval($data["LMI_PAYEE_PURSE"])!=$this->getConfigValue("wm_payee_purse")) {
			echo "<p class=\"error\">".Text::_("Incorrect payment purse")."</p	>";
			echo "<p class=\"error\">".Text::_("Please, contact administrator to resolve this problem")."</p	>";
		} else {
			$this->setPaid();
			$this->setPaymentResultData($data);
			echo "<p class=\"ok\">".Text::_("Payment successful")."</p	>";
		}
	}
	public function proceedPayment(){
		Portal::getInstance()->disableTemplate();
		ob_clean();
		if( isset($_REQUEST['LMI_PREREQUEST']) && $_REQUEST['LMI_PREREQUEST'] == 1){
			Util::halt("YES");
		} else {
			$data["LMI_PAYEE_PURSE"]=Request::getSafe("LMI_PAYEE_PURSE");
			$data["LMI_PAYMENT_AMOUNT"]=Request::getSafe("LMI_PAYMENT_AMOUNT");
			$data["LMI_PAYMENT_NO"]=Request::getSafe("LMI_PAYMENT_NO");
			$data["LMI_MODE"]=Request::getSafe("LMI_MODE");
			$data["LMI_SYS_INVS_NO"]=Request::getSafe("LMI_SYS_INVS_NO");
			$data["LMI_SYS_TRANS_NO"]=Request::getSafe("LMI_SYS_TRANS_NO");
			$data["LMI_SYS_TRANS_DATE"]=Request::getSafe("LMI_SYS_TRANS_DATE");
			$data["LMI_PAYER_PURSE"]=Request::getSafe("LMI_PAYER_PURSE");
			$data["LMI_PAYER_WM"]=Request::getSafe("LMI_PAYER_WM");
			$data["LMI_HASH"]=Request::getSafe("LMI_HASH");
			Session::getInstance()->setVar('wm_data',$data);
		}
	}
	public function renderInfo($data="") {
		if (!$data) $data=$this->getData();
		$html="<br />";
		if (is_array($data)){
			if (isset($data["LMI_PAYMENT_AMOUNT"])) $html.="<b>".Text::_("Payment sum")." : </b>".$data["LMI_PAYMENT_AMOUNT"]."<br />";
			if (isset($data["LMI_SYS_TRANS_DATE"])) $html.="<b>".Text::_("Payment date")." : </b>".$data["LMI_SYS_TRANS_DATE"]."<br />";
			if (isset($data["LMI_PAYER_PURSE"])) $html.="<b>".Text::_("Payer purse")." : </b>".$data["LMI_PAYER_PURSE"]."<br />";
			if (isset($data["LMI_SYS_TRANS_NO"])) $html.="<b>".Text::_("Transaction ID")." : </b>".$data["LMI_SYS_TRANS_NO"]."<br />";
		}
		return $html;
	}
}
?>