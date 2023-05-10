<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class simplePaymentClass extends catalogPayment {
	public function getParamsMask(){
		$params = parent::getParamsMask();
		return $params;
	}
	private function initConstants(){
		$vendor = Vendor::getInstance()->getVendor($this->order->o_vendor);
		$vendor_addr=Vendor::getInstance()->getPostAddress($this->order->o_vendor);
		$stamp_path=BARMAZ_UF_PATH.DS.'catalog'.DS.'vendors'.DS;
		$stamp_path.='stamp'.DS.Files::splitAppendix($vendor->v_pechat,true);
		if((file_exists($stamp_path))&&(is_file($stamp_path))) {
			$vendor_pechat=BARMAZ_UF.'/catalog/vendors/';
			$vendor_pechat.='stamp/'.Files::splitAppendix($vendor->v_pechat);
		}	else 	$vendor_pechat="/images/blank.gif";
		define ('PAYMENT_DEFAULT_FIRM_NAME', $vendor->v_name);
		define ('PAYMENT_DEFAULT_FIRM_ADDRESS', $vendor_addr);
		define ('PAYMENT_DEFAULT_FIRM_PHONE', $vendor->v_phone.(($vendor->v_phone && $vendor->v_fax)?"/":"").$vendor->v_fax);
		define ('PAYMENT_DEFAULT_OGRN', $vendor->v_ogrn);
		define ('PAYMENT_DEFAULT_INN', $vendor->v_inn);
		define ('PAYMENT_DEFAULT_KPP', $vendor->v_kpp);
		define ('PAYMENT_DEFAULT_DIRECTOR', $vendor->v_boss);
		define ('PAYMENT_DEFAULT_GLAVBUH', $vendor->v_ca_name);
		define ('PAYMENT_DEFAULT_BANK_NAME', $vendor->v_bank);
		define ('PAYMENT_DEFAULT_ACCOUNT_NUMBER', $vendor->v_sett_acc);
		define ('PAYMENT_DEFAULT_BIK', $vendor->v_bik);
		define ('PAYMENT_DEFAULT_BANK_NUMBER', $vendor->v_bank_acc);
		define ('PAYMENT_PECHAT_IMAGE', $vendor_pechat);
	}
	/* Собственные функции*/
	private function showPanel() {
		if(User::getInstance()->isLoggedIn()) $CompanyInfo=Userdata::getInstance(User::getInstance()->getID())->getCompany();
		else $CompanyInfo=false;
		if($CompanyInfo) {
			if($CompanyInfo['org_type']){
				$address=Userdata::getInstance(User::getInstance()->getID())->getDefaultAddress(2); // Адрес регистрации
			} else {
				$address=Userdata::getInstance(User::getInstance()->getID())->getDefaultAddress(2); // Адрес регистрации
			}
			$payment_user = $CompanyInfo["surname"]." ".$CompanyInfo["firstname"]." ".$CompanyInfo["patronymic"];
			$payment_user_address = $address['fullinfo'];
			$payment_user_inn = $CompanyInfo["inn"];
		} else {
			$payment_user = "";
			$payment_user_address = "";
			$payment_user_inn = "";
		}
		
		echo "<form name=\"simple_payment\" target=\"_blank\" action=\"/index.php\" method=\"post\">";
		echo "<h3 class=\"title\">".Text::_("Order payment")." № ".$this->order->o_id." ".Text::_("from")." ".Date::GetdateRus($this->order->o_date)."</h3>";
		echo "<h4>".Text::_("Your payment type")." : ".$this->payment->pt_name."</h4>";
		echo "<div class=\"row\"><div class=\"col-sm-4\">";
		echo "<label for\"payment_user\">".Text::_("Payer")."</label>";
		echo "</div><div class=\"col-sm-8\">";
		echo "<input type=\"text\" class=\"form-control\" name=\"payment_user\" value=\"".$payment_user."\" />";
		echo "</div></div>";
		echo "<div class=\"row\"><div class=\"col-sm-4\">";
		echo "<label for\"payment_user_inn\">".Text::_("INN")."</label>";
		echo "</div><div class=\"col-sm-8\">";
		echo "<input type=\"text\" class=\"form-control\" name=\"payment_user_inn\" value=\"".$payment_user_inn."\" />";
		echo "</div></div>";
		echo "<div class=\"row\"><div class=\"col-sm-4\">";
		echo "<label for\"payment_user\">".Text::_("Address")."</label>";
		echo "</div><div class=\"col-sm-8\">";
		echo "<input type=\"text\" class=\"form-control\" name=\"payment_user_address\" value=\"".$payment_user_address."\" />";
		echo "</div></div>";
		echo "<div class=\"row\"><div class=\"col-sm-4\">";
		echo "<label for\"payment_user\">".Text::_("What to print")."</label>";
		echo "</div><div class=\"col-sm-8\">";
		echo "<select name=\"docname\" class=\"form-control\">";
		echo "	<option selected=\"selected\" value=\"receipt\">".Text::_('Receipt')."</option>";
		echo "	<option value=\"account\">".Text::_('Account')."</option>";
		echo "</select>";
		echo "</div></div>";
		echo "<div class=\"buttons row\"><div class=\"col-sm-12\">";
		echo "<input type=\"hidden\" name=\"option\" value=\"print\" />";
		echo "<input type=\"hidden\" name=\"module\" value=\"catalog\" />";
		echo "<input type=\"hidden\" name=\"view\" value=\"orders\" />";
		echo "<input type=\"hidden\" name=\"layout\" value=\"payment\" />";
		echo "<input type=\"hidden\" name=\"order_id\" value=\"".$this->order->o_id."\" />";
		echo "<input type=\"submit\" class=\"btn btn-info\" name=\"submit\" value=\"".Text::_("Print")."\" />";
//		echo "<a id=\"schet\" target=\"_blank\" class=\"linkButton btn btn-info\" href=\"".Router::_("index.php?option=print&module=catalog&view=orders&layout=payment&docname=account&order_id=".$this->order->o_id)."\">".Text::_('Account')."</a> \n";
//		echo "<a id=\"kvit\" target=\"_blank\" class=\"linkButton btn btn-info\" href=\"".Router::_("index.php?option=print&module=catalog&view=orders&layout=payment&docname=receipt&order_id=".$this->order->o_id)."\">".Text::_('Receipt')."</a> \n";
		echo "</div></div></form>";
	}
	public function show() {
		$docname=Request::getSafe('docname','');
		if ($docname) {
			$templPath = PATH_TEMPLATES.Portal::getInstance()->getTemplate().DS.'html'.DS."catalog".DS."payments".DS.$this->payment->pt_file.DS;
			$form_name=$templPath.$docname.".php";
				
			$form_path=PATH_MODULES."catalog".DS."payments".DS.$this->payment->pt_file.DS;
			if(!file_exists($form_name)) $form_name=$form_path.$docname.".php";
			
			if (file_exists($form_name)) {
				$this->initConstants();
				require_once($form_name);
			} else { $this->showPanel();
			}
		} else { $this->showPanel();
		}
	}
}
?>