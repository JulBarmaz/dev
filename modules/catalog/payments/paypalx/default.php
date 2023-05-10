<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class paypalxPaymentClass extends catalogPayment {
	/* PayPal API */
	/**
	 * Последние сообщения об ошибках
	 * @var array
	 */
	private $_errors = array();

	/**
	 * Данные API, для песочницы нужно использовать соответствующие данные
	 * @var array
	*/
	private $_credentials = array(); //array( 'USER' => '', 'PWD' => '', 'SIGNATURE' => '');

	/**
	 * Указываем, куда будет отправляться запрос - https://api-3t.paypal.com/nvp
	 * Песочница - https://api-3t.sandbox.paypal.com/nvp
	 * @var string
	*/
	private $_endPoint = "http://localhost/";

	/**
	 * Версия API
	 * @var string
	 */
	private $_version = '82.0';

	private $_allowed_currencies=array("USD","GBP","RUB");
	private $_cacert = 'cacert.pem';
	private $_payPoint = 'http://localhost/';

	/**
	 * Формируем запрос
	 *
	 * @param string $method Данные о вызываемом методе перевода
	 * @param array $params Дополнительные параметры
	 * @return array / boolean Response array / boolean false on failure
	 */
	public function paypalRequest($method,$params = array()) {
		$this->_errors = array();
		if( empty($method) ) {
			// Проверяем, указан ли способ платежа
			$this->_errors = array('Paypal method absent');
			return false;
		}
		$requestParams = array( 'METHOD' => $method, 'VERSION' => $this -> _version ) + $this -> _credentials;
		// Сформировываем данные для NVP
		$request = http_build_query($requestParams + $params);
		//Util::showArray($request, "paypalRequest-request");
		// Настраиваем cURL
		if ($this->_cacert && is_file($this->_cacert)) {
			$curlOptions = array (
					CURLOPT_URL => $this -> _endPoint,
					CURLOPT_VERBOSE => 1,
					CURLOPT_SSL_VERIFYPEER => true,
					CURLOPT_SSL_VERIFYHOST => 2,
					CURLOPT_CAINFO => $this->_cacert,
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_POST => 1,
					CURLOPT_POSTFIELDS => $request
			);
		} else {
			$curlOptions = array (
					CURLOPT_URL => $this -> _endPoint,
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_SSL_VERIFYHOST => false,
					CURLOPT_VERBOSE => 1,
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_POST => 1,
					CURLOPT_POSTFIELDS => $request
			);
		}

		$ch = curl_init();
		curl_setopt_array($ch,$curlOptions);
		// Отправляем наш запрос, $response будет содержать ответ от API
		$response = curl_exec($ch);
		// Проверяем, нету ли ошибок в инициализации cURL
		if (curl_errno($ch)) {
			$this -> _errors = curl_error($ch);
			curl_close($ch);
			return false;
		} else  {
			curl_close($ch);
			$responseArray = array();
			parse_str($response,$responseArray); // Разбиваем данные, полученные от NVP в массив
			return $responseArray;
		}
	}
	/* Standart API */
	public function __construct($payment_type,$_data=false) {
		parent::__construct($payment_type,$_data);
	}
	public function getParamsMask(){
		$params = parent::getParamsMask();
		$params['pp_user']["vtype"]="string"; 			$params['pp_user']["vdefault"]="sdk-three_api1.sdk.com";
		$params['pp_password']["vtype"]="string"; 	$params['pp_password']["vdefault"]="QFZCWN5HZM8VBG7Q";
		$params['pp_signature']["vtype"]="string";	$params['pp_signature']["vdefault"]="A-IzJhZZjhg29XQ2qnhapuwxIDzyAZQ92FRP5dqBzVesOkzbdUONzmOU";
		$params['pp_cacert']["vtype"]="string";			$params['pp_cacert']["vdefault"]=""; 				$params['pp_cacert']["descr"]="pp cacert description";
		$params['pp_api_ver']["vtype"]="string";		$params['pp_api_ver']["vdefault"]="82.0";
		$params['pp_sandbox']["vtype"]="boolean";		$params['pp_sandbox']["vdefault"]=true;
		return $params;
	}
	private function initData(){
		$this->_cacert=$this->getConfigValue("pp_cacert");
		$this->_version=$this->getConfigValue("pp_api_ver");
		if ($this->getConfigValue("pp_sandbox")){
			$this->_endPoint = "https://api-3t.sandbox.paypal.com/nvp";
			$this->_payPoint = "https://www.sandbox.paypal.com/webscr?cmd=_express-checkout";
		} else {
			$this->_endPoint = 'https://api.paypal.com/nvp';
			$this->_payPoint = "https://api-3t.paypal.com/webscr?cmd=_express-checkout";
		}
		$this->_credentials = array('USER' => $this->getConfigValue("pp_user"), 'PWD' => $this->getConfigValue("pp_password"), 'SIGNATURE' => $this->getConfigValue("pp_signature"));
		if (!in_array(Currency::getCode($this->payment->pt_currency), $this->_allowed_currencies)) {
			echo "<p class=\"error\">".Text::_("Unsupported currency")."</p	>";
			return false;
		}
		return true;
	}
	public function show() {
		if ($this->initData()) {
			$do=Request::getSafe('do','');
			if ($do) {
				switch ($do){
					case "send":
						$this->send();
						break;
					default:
						$this->showPanel();
						break;
				}
			} else { $this->showPanel();
			}
		}
	}
	public function cancel(){
		Session::unsetVar("PayPalExpressToken");
		echo "<h3 class=\"title\">".Text::_("Order payment")." № ".$this->order->o_id." ".Text::_("from")." ".Date::GetdateRus($this->order->o_date)."</h3>";
		echo "<h4>".Text::_("Your payment type")." : ".$this->payment->pt_name."</h4>";
		echo "<p class=\"error\">".Text::_("Payment canceled")."</p	>";
	}
	public function send(){
		$requestParams = array(
				'RETURNURL' => Router::_("index.php?module=catalog&view=orders&layout=payment&mode=recieve&order_id=".$this->order->o_id,false,false),
				'CANCELURL' => Router::_("index.php?module=catalog&view=orders&layout=payment&mode=cancel&order_id=".$this->order->o_id,false,false)
		);
		$item = array(); $total_summa=0; $ship_summa=0; $tax_summa=0; $summa=0;  $quantity=0;
		 
		// берем расширенный метод, со списком заказа
		$currency_code=Currency::getCode($this->payment->pt_currency);
		if(count($this->items)) {
			$ccc=0;
			foreach ($this->items as $itm){
				$item["L_PAYMENTREQUEST_0_NAME".$ccc]=$itm->i_g_name;
				$item["L_PAYMENTREQUEST_0_QTY".$ccc]=$itm->i_g_quantity;
				$curr_price = Currency::getInstance()->convert($itm->i_g_price,$this->order->o_currency, $currency_code);
				$item["L_PAYMENTREQUEST_0_AMT".$ccc]=number_format(floatval($curr_price), 2);
				$summa=$summa+$curr_price*$itm->i_g_quantity;
				$quantity=$quantity+$itm->i_g_quantity;
				$ccc++;
			}
		}
		$ship_summa = Currency::getInstance()->convert($this->order->o_dt_sum,$this->order->o_currency, $currency_code);
		$tax_summa = Currency::getInstance()->convert($this->order->o_taxes_sum,$this->order->o_currency, $currency_code);
		$total_summa = $this->getOrderTotalSumForPayment();
		$disc_summa = Currency::getInstance()->convert($this->order->o_discount_sum,$this->order->o_currency, $currency_code);
		//  	$total_summa=$ship_summa+$summa;
		$disc_summa=$total_summa-$ship_summa-$summa;
		$order_desc=Text::_("Order number")." ".$this->order->o_id." ".Text::_("from")." ".$this->order->o_date;
		$orderParams = array(
				'PAYMENTREQUEST_0_AMT' => number_format(floatval($total_summa), 2),
				'PAYMENTREQUEST_0_SHIPPINGAMT' => number_format(floatval($ship_summa), 2),
				'PAYMENTREQUEST_0_SHIPDISCAMT' => number_format(floatval($disc_summa), 2),
				'PAYMENTREQUEST_n_TAXAMT' =>$tax_summa,
				'PAYMENTREQUEST_0_CURRENCYCODE' => $currency_code,
				'PAYMENTREQUEST_0_ITEMAMT' => number_format(floatval($summa), 2),
				'PAYMENTREQUEST_0_DESC' => $order_desc
		);
		// закончили формирование расширенным методом

		$response = $this->paypalRequest('SetExpressCheckout',$requestParams + $orderParams + $item);
		if(is_array($response) && (strtoupper($response['ACK']) == 'SUCCESS' || strtoupper($response['ACK']) == 'SUCCESSWITHWARNING')) {
			$token = $response['TOKEN'];
			Session::setVar("PayPalExpressToken",$token);
			header( "Location: ".$this->_payPoint."&token=" . urlencode($token) );
		} else {
			if (is_array($response)){
				foreach($response as $mk=>$mv){
					if (preg_match('/^(L_)/',$mk)) Debugger::getInstance()->message($mk."=".$mv);
				}
			}
			echo "<p class=\"error\">".Text::_("ExpressCheckout API call failed")."</p	>";
		}
	}
	public function recieve(){
		if (!$this->initData()) return false;
		echo $this->getPaymentTitle();
		$token=Request::getSafe("token","");
		if( $token && Session::getVar("PayPalExpressToken") && $token===Session::getVar("PayPalExpressToken")) {
			// Получаем детали оплаты, включая информацию о покупателе
			$checkoutDetails = $this->paypalRequest("GetExpressCheckoutDetails", array("TOKEN" => $token));
			// Завершаем транзакцию
			$currency_code=$this->payment->pt_currency;
			$summa = number_format(floatval($this->getOrderTotalSumForPayment()), 2);
			$requestParams = array( "PAYMENTREQUEST_0_PAYMENTACTION" => "Sale",
					"TOKEN" => $token,
					"PAYERID" => Request::getSafe("PayerID"),
					"PAYMENTREQUEST_0_CURRENCYCODE"=>$currency_code,
					"PAYMENTREQUEST_0_AMT"=>$summa
			);
			$response = $this->paypalRequest("DoExpressCheckoutPayment",$requestParams);
			if( is_array($response) && $response["ACK"] == "Success") {
				// Оплата успешно проведена Здесь мы сохраняем ID транзакции, может пригодиться во внутреннем учете
				$transactionId = $response["PAYMENTINFO_0_TRANSACTIONID"];
				if (is_array($checkoutDetails)) $data= array_merge($checkoutDetails,$response);
				else $data=$response;
				$this->setPaid();
				$this->setPaymentResultData($data);
				echo "<p class=\"ok\">".Text::_("Payment successful")."</p	>";
			} else echo "<p class=\"error\">".Text::_("Payment final confirm error")."</p	>";
		} else echo "<p class=\"error\">".Text::_("Invalid server answer")."</p	>";
		Session::unsetVar("PayPalExpressToken");
	}
	/* Собственные функции*/
	private function getPaymentTitle(){
		$html = "<h3 class=\"title\">".Text::_("Order payment")." № ".$this->order->o_id." ".Text::_("from")." ".Date::GetdateRus($this->order->o_date)."</h3>";
		$html.= "<h4 class=\"sub-title\">".Text::_("Your payment type")." : ".$this->payment->pt_name."</h4>";
		return $html;
	}
	private function showPanel() {
		echo $this->getPaymentTitle();
		if ($this->order->o_paid){
			echo "<p class=\"ok\">".Text::_("Payment already done")."</p	>";
		} else {
			echo "<div class=\"payment_button row\"><div class=\"col-sm-12\">";
			echo "<a class=\"linkButton btn\" href=\"".Router::_("index.php?module=catalog&view=orders&layout=payment&do=send&order_id=".$this->order->o_id)."\">
					<img src=\"https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif\" align=\"left\" style=\"margin-right:7px;\" alt=\"PayPal\" />
					</a> \n";
			echo "</div></div>";
		}
	}
	public function renderInfo($data="") {
		if (!$data) $data=$this->getData();
		$html="<br />";
		if (is_array($data)){
			if (isset($data["PAYMENTINFO_0_TRANSACTIONID"])) $html.="<b>".Text::_("Transaction ID")." : </b>".$data["PAYMENTINFO_0_TRANSACTIONID"]."<br />";
		}
		return $html;
	}
}
?>