<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogPayment extends catalogUserdata{
	protected $payment=null;
	public function __construct($payment_type,$_data=false) {
		parent::__construct($payment_type);
		$this->payment=$payment_type;
		if ($_data) $this->data=$_data;
		if ($this->payment) {
			Text::parse("catalog.payment.".$this->payment->pt_file,"custom");
			$this->params=Params::parse($this->payment->pt_params);
		}
	}
	public static function getOrderId($mode){
		return 0;
	}
	public static function getOrderIdByAbstractPaymentClass($pt_name, $mode){
		$db=Database::getInstance();
		$sql="SELECT count(pt_id) FROM #__goods_pts WHERE pt_deleted=0 AND pt_enabled=1 AND pt_file='".$pt_name."'";
		$db->setQuery($sql);
		if ($db->loadResult()>0){
			$pt_file=PATH_MODULES."catalog".DS."payments".DS.$pt_name.DS."default.php";
			if (is_file($pt_file)){
				require_once $pt_file;
				$pt_class=$pt_name."PaymentClass";
				if (class_exists($pt_class)) {
					return $pt_class::getOrderId($mode);
				}
			}
			
		}
		return 0;
	}
	public static function getPaymentClass($pt_id, $pt_data=false) {
		$payment=self::getPaymentType($pt_id);
		if ($payment){
			if (defined("_ADMIN_MODE")) $modules_path=PATH_FRONT_MODULES; else $modules_path=PATH_MODULES;
			$pt_file=$modules_path."catalog".DS."payments".DS.$payment->pt_file.DS."default.php";
			$pt_class=$payment->pt_file."PaymentClass";
			if (is_file($pt_file)){
				require_once $pt_file;
				if (class_exists($pt_class)) return new $pt_class($payment,$pt_data);
			}
		}
		return new self($payment);
	}
	public static function getPaymentType($pt_id) {
		$db=Database::getInstance();
		if (!defined("_ADMIN_MODE")) $enabled = " AND  pt_enabled=1"; else $enabled="";
		$sql="SELECT * FROM #__goods_pts WHERE pt_deleted=0 AND pt_id=".$pt_id.$enabled;
		$db->setQuery($sql);
		if ($db->loadObject($res)) return $res;
		else return false;
	}
	public function setPaid(){
		$sql = "UPDATE #__orders SET o_paid=1";
		if($this->payment->pt_set_status){
			$sql.= ", o_status=".$this->payment->pt_set_status;
		}
		$sql.= " WHERE o_id=".$this->order->o_id;
		Database::getInstance()->setQuery($sql);
		if (Database::getInstance()->query()) {
			Event::raise("catalog.order.paid", array("module"=>"catalog", "order"=>$this->order, "payment"=>$this->payment));
			return true;
		}
		return false;
	}
	public function setPaymentResultData($data){
		$enc_data=$this->getEncodedArray($data);
		$sql="UPDATE #__orders SET o_pt_result='".$enc_data."' WHERE o_id=".$this->order->o_id;
		Database::getInstance()->setQuery($sql);
		if (Database::getInstance()->query())	return true;
		else return false;
	}
	public function show() { 
		/* Just for override */
	}
	public function send(){
		return true;
	}
	public function recieve(){
		return true;
	}
	public function calculate($userdata=array()){
		return $this->payment->pt_price;
	}
	public function getId() {
		return $this->payment->pt_id;
	}
	public function getTitle() {
		return $this->payment->pt_name;
	}
	public function getCurrency() {
		return $this->payment->pt_currency;
	}
	public function getOrderTotalSumForPayment($currency_code = ""){
		if(!$currency_code) $currency_code = $this->getCurrency();
		$order_summ= Currency::getInstance()->convert($this->order->o_total_sum, $this->order->o_currency, $currency_code);
		return $order_summ;
	}
	public function executeDataQuery($query) {
		return "";
	}
}
?>