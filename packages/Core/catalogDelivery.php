<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogDelivery extends catalogUserdata{
	protected $delivery=null;
	protected $tax_name="";
	protected $tax_sum=0;
	protected $need_recalc=1;
	
	public function needRecalc(){
		return $this->need_recalc;
	}
	public function isLoaded() {
		return is_object($this->delivery);
	}
	public function __construct($delivery_type,$_data=false) {
		parent::__construct($delivery_type);
		$this->delivery=$delivery_type;
		if ($_data) $this->data=$_data;
		if ($this->delivery) {
			Text::parse("catalog.delivery.".$this->delivery->dt_file,"custom");
			$this->params=Params::parse($this->delivery->dt_params);
		}
	}
	public static function getOrderId($mode){
		return 0;
	}
	public static function getOrderIdByAbstractDeliveryClass($dt_name, $mode){
		$db=Database::getInstance();
		$sql="SELECT count(dt_id) FROM #__goods_dts WHERE dt_deleted=0 AND dt_enabled=1 AND dt_file='".$dt_name."'";
		$db->setQuery($sql);
		if ($db->loadResult()>0){
			$dt_file=PATH_MODULES."catalog".DS."deliveries".DS.$dt_name.DS."default.php";
			if (is_file($dt_file)){
				require_once $dt_file;
				$dt_class=$dt_name."DeliveryClass";
				if (class_exists($dt_class)) {
					return $dt_class::getOrderId($mode);
				}
			}
			
		}
		return 0;
	}
	public static function getDeliveryClass($dt_id, $dt_data=false) {
		$delivery=self::getDeliveryType($dt_id);
		if ($delivery){
			if (defined("_ADMIN_MODE")) $modules_path=PATH_FRONT_MODULES; else $modules_path=PATH_MODULES;
			$dt_file=$modules_path."catalog".DS."deliveries".DS.$delivery->dt_file.DS."default.php";
			$dt_class=$delivery->dt_file."DeliveryClass";
			if (is_file($dt_file)){
				require_once $dt_file;
				if (class_exists($dt_class)) return new $dt_class($delivery,$dt_data);
			}
		}
		return new self($delivery);
	}
	public static function getDeliveryType($dt_id) {
		$db=Database::getInstance();
		if (!defined("_ADMIN_MODE")) $enabled = " AND  dt_enabled=1"; else $enabled="";
		$sql="SELECT * FROM #__goods_dts WHERE dt_deleted=0 AND dt_id=".$dt_id.$enabled;
		$db->setQuery($sql);
		if ($db->loadObject($res)) return $res;
		else return false;
	}
	/**
	 * @param integer $mode : 0-controller (list in new order); 1-ajax; 2-basket (calculate order on save)
	 * @param array $userdata : from order->save() 
	 * @return float
	 */
	public function calculate($mode, $userdata=array()){
		$this->calculateTaxSum($this->delivery->dt_price);
		return $this->delivery->dt_price;
	}
	public function getId() {
		return $this->delivery->dt_id;
	}
	public function getTitle() {
		return $this->delivery->dt_name;
	}
	public function getTaxId() {
		return $this->delivery->dt_tax;
	}
	public function getCurrency() {
		return $this->delivery->dt_currency;
	}
	public function getTaxSum() {
		return $this->tax_sum;
	}
	public function getTaxName() {
		return $this->tax_name;
	}
	public function calculateTaxSum($current_sum=0){
		if(!$current_sum) return 0;
		if(!$this->getTaxId()) return 0;
		$tax=Taxes::getTax($this->getTaxId());
		if(is_object($tax)){
			$this->tax_sum=round($current_sum/(100 + $tax->t_value) * $tax->t_value, 2);
			$this->tax_name=$tax->t_name;
		} else {
			$this->is_error=1;
			$this->error_text=Text::_("Failed initializing delivery tax");
			$this->tax_sum=0;
			$this->tax_name="Error tax";
		}		
	}
	public function executeDataQuery($query) {
		return "";
	}
}
?>