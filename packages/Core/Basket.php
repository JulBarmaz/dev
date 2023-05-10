<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

// @TODO this->quantity и basket->quantity в orders это бред. Пока ЗАРЕМИЛ все кроме свойства.

class Basket extends BaseObject {
	public static $cookie_name = "BARMAZ_basket";
	private $basket_id = null;
	private $basket_hash = null;
	private $taxes = null;
	private $taxesInOrder = array();
	private $temp_files_folder="orders";
	private $g_stocks = array();
	private $multOptQuant = true;
	public $keepTime = 1209600; // 14 дней
	public $goods = null; // id, optArr и количество товаров
	public $items = null; // рассчитанная таблица товаров и услуг
	public $deliverySum = 0;
	public $paymentSum = 0;
	public $discountSum = 0;
	public $points_enabled=false;

	public $taxesSum = 0;
	public $points = 0;
	public $summa = 0;
	public $total = 0;
	public $weight = 0;
	public $not_enough_quantity = false; // it is set in calculateVendor()
	
	public $quantity = 0; // пока оставлено, есть обращения
	public $order_complete = 0;
	public $order_id = 0;
	public $basket_message = "";
	public $order_message = "";
	public $order_vendor = 0;

	//---------- Singleton implementation ------------
	private static $_instance = null;

	public static function createInstance() {
		if (self::$_instance == null) {
			self::$_instance = new self();
		}
	}

	public static function getInstance() {
		self::createInstance();
		return self::$_instance;
	}
	//------------------------------------------------

	private function __construct() {
		$this->initObj();
		$basket_id = Request::getSafe("basket_id","","cookie");
		if(!$basket_id) {
			$basket_id=base64_encode(md5(session_id()).md5(time()));
			Session::getInstance()->setcookie("basket_id", $basket_id, time() + 60*siteConfig::$cookieLifeTime,"/");
		}
		$this->basket_id=$basket_id;
		$this->basket_hash=md5(Session::getInstance()->getKey());
		$this->temp_files_folder="orders".DS.$this->basket_hash;
		$this->loadBasket();
		$this->points_enabled=siteConfig::$use_points_system;
		if (!isset($_SESSION["basket_vendor"])) Session::setVar("basket_vendor",0);
	}

	public function cleanBasket() {
		if($this->order_complete) {
			if(catalogConfig::$multy_vendor && $this->order_vendor){
				foreach ($this->goods as $k=>$g){
					if($g['g_vendor']==$this->order_vendor){
						$this->deleteBasketPosition($k);
					}
				}
			} else {
				$sql = "DELETE FROM #__goods_basket WHERE basket_id='".$this->basket_id."'";
				Database::getInstance()->setQuery($sql);
				Database::getInstance()->query();
			}
			Session::unsetVar("basket_vendor");
			$this->cleanOldBaskets();
		}
	}
	public function cleanOldBaskets(){
		$sql = "DELETE FROM #__goods_basket WHERE basket_touch<".(time()-$this->keepTime);
		Database::getInstance()->setQuery($sql);
		return Database::getInstance()->query();
	}
	private function loadBasket() {
		$sql = "SELECT basket_data FROM #__goods_basket WHERE basket_id='".$this->basket_id."'";
		Database::getInstance()->setQuery($sql);
		$this->goods = json_decode(base64_decode(Database::getInstance()->loadResult()), true);
	}
	private function saveBasket() {
		$basket_data = base64_encode(json_encode($this->goods));
		$sql = "INSERT INTO #__goods_basket  VALUES ('".$this->basket_id."', ".time().", '".$basket_data."') ON DUPLICATE KEY UPDATE basket_touch=".time().", basket_data='".$basket_data."'";
		Database::getInstance()->setQuery($sql);
		Database::getInstance()->query();
		if(!count($this->goods)) $this->deleteTempFolder();
	}
	public function updateStockArray($gid){
		$gids = array();
		if(count($this->goods)) {
			foreach($this->goods as $gs){
				$gids[$gs["g_id"]]=$gs["g_id"];
			}
		} 
		$gids[$gid]=$gid;
		$sql="SELECT g_id, 0 AS g_quantity, g_stock FROM #__goods WHERE g_id IN(".implode(",", array_keys($gids)).")";
		Database::getInstance()->setQuery($sql);
		$this->g_stocks = Database::getInstance()->loadAssocList("g_id");
		if(count($this->g_stocks)){
			$sql="SELECT a.ovd_id, a.ovd_od_id, a.ovd_check_stock, a.ovd_stock, b.od_obj_id FROM `#__goods_opt_vals_data` AS a, `#__goods_options_data` AS b WHERE a.ovd_od_id=b.od_id AND b.od_obj_id IN(".implode(",", array_keys($gids)).")";
			Database::getInstance()->setQuery($sql);
			$opt_stocks = Database::getInstance()->loadAssocList("ovd_id");
			if(count($opt_stocks)){
				foreach($this->g_stocks as $gk=>$gv){
					$this->g_stocks[$gk]["g_options"]=Array();
					foreach($opt_stocks as $osk=>$osv){
						if($osv["od_obj_id"]==$gk){
							$this->g_stocks[$gk]["g_options"][$osv["ovd_od_id"]][$osv["ovd_id"]]=Array();
							$this->g_stocks[$gk]["g_options"][$osv["ovd_od_id"]][$osv["ovd_id"]]["ovd_id"]=$osv["ovd_id"];
							$this->g_stocks[$gk]["g_options"][$osv["ovd_od_id"]][$osv["ovd_id"]]["ovd_check_stock"]=$osv["ovd_check_stock"];
							$this->g_stocks[$gk]["g_options"][$osv["ovd_od_id"]][$osv["ovd_id"]]["ovd_quantity"]=0;
							$this->g_stocks[$gk]["g_options"][$osv["ovd_od_id"]][$osv["ovd_id"]]["ovd_stock"]=$osv["ovd_stock"];
						}
					}
				}
			}
		}
		if(count($this->goods)){
			foreach ($this->goods as $key=>$val){
				if(isset($this->g_stocks[$val["g_id"]])){
					$this->g_stocks[$val["g_id"]]["g_quantity"]=$this->g_stocks[$val["g_id"]]["g_quantity"]+$val["quantity"];
					if(count($val["options_data"])){
						foreach ($val["options_data"] as $k_opt=>$v_opt){
							if(count($v_opt)){
								foreach ($v_opt as $kk_opt=>$vv_opt){
									if(isset($this->g_stocks[$val["g_id"]]["g_options"][$k_opt][$kk_opt])){
										$this->g_stocks[$val["g_id"]]["g_options"][$k_opt][$kk_opt]["ovd_quantity"]=$this->g_stocks[$val["g_id"]]["g_options"][$k_opt][$kk_opt]["ovd_quantity"]+($vv_opt["quantity"]*($this->multOptQuant ? $val["quantity"] : 1)); 
									}
								}
							}
						}
					} 
				}
			}
		}
	}
	public function checkQuantity($id, $g_arr, $quantity){
		$check = true; $old_quantity=0;
		if(catalogConfig::$check_stock){
			$this->updateStockArray($g_arr["g_id"]);
			if($id)	$old_quantity=$this->goods[$id]["quantity"];
			if(isset($this->g_stocks[$g_arr["g_id"]])){
				$g=$this->g_stocks[$g_arr["g_id"]];
				if(($g["g_quantity"] + $quantity - $old_quantity)>floatval($g["g_stock"])){ // проверка общего количества заказываемого товара от базового количества
					//$this->basket_message=Text::_("Not enough quantity in stock");
					$check=false;
				} else {
					if(count($g_arr["options_data"])){ // проверяем количество опций
						foreach($g_arr["options_data"] as $ga_key=>$ga_opt){
							if(count($ga_opt)){
								foreach($ga_opt as $ga_ov_key=>$ga_ov_val){
									if(isset($g["g_options"][$ga_key][$ga_ov_key])){
										$go=$g["g_options"][$ga_key][$ga_ov_key];
										if($go["ovd_check_stock"]){
											$new_opt_quantity=$go["ovd_quantity"] + ($this->multOptQuant ? $quantity : 1)*$ga_ov_val["quantity"] - ($this->multOptQuant ? $old_quantity : 1)*$ga_ov_val["quantity"];
											if($new_opt_quantity>floatval($go["ovd_stock"])){
												//$this->basket_message=Text::_("Not enough quantity in stock");
												$check=false;
											}
										}
									}
								}
							}
						}
					}
				}
			} else $check=false;
		}
		return $check;
	}
	public function reduceStocks($key){
		if(catalogConfig::$check_stock && isset($this->items[$key])){
			$sql="UPDATE #__goods SET g_stock=g_stock-".$this->items[$key]->g_quantity." WHERE g_id=".intval($this->items[$key]->g_id);
			Database::getInstance()->setQuery($sql);
			Database::getInstance()->query();
			if(count($this->items[$key]->options_data)){
				foreach ($this->items[$key]->options_data as $k=>$v){
					if(count($v)){
						foreach($v as $k2=>$v2){
							if(isset($this->items[$key]->options_template[$k]->optionsData[$k2]->ovd_check_stock) && intval($this->items[$key]->options_template[$k]->optionsData[$k2]->ovd_check_stock)){
								$sql="UPDATE #__goods_opt_vals_data SET ovd_stock=ovd_stock-".($this->multOptQuant ? $this->items[$key]->g_quantity : 1)*$v2["quantity"]." WHERE ovd_check_stock=1 AND ovd_id=".$k2;
								Database::getInstance()->setQuery($sql);
								Database::getInstance()->query();
							}
						}
					}
				}
			}
		}
	}
	public function addBasketPosition($g_id, $quantity, $optArr) {
		// @TODO May be deny adding goods with zero price, or restrict ordering later.
		$check = false;
		$new_id= 0 ;
		if($g_id && $quantity){
			$g=$this->getGoodsElement((int)$g_id);
			if(is_object($g)){
//				$g->quantity=$quantity;
				$check = $this->setIntersection($g, $optArr);
				$g->options_hash=md5(json_encode($g->options_data));
				if($check) {
					if(is_array($this->goods) && count($this->goods)){
						foreach($this->goods as $i=>$j){
							if($g->options_hash==$j['options_hash'] && $g->g_id==$j['g_id']){
								return $this->updateBasketPosition ($i, $this->goods[$i]['quantity'] + $quantity);
							}
						}
						$new_id=max(array_keys($this->goods)) + 1;
					} else {
						$new_id=1;
					}
					
					if($new_id){
						// @TODO проверить g_is_single если рассматривать параметр как единственно возможный для заказа, а не просто переходить в корзину
						$new_goods_array = Array();
						$new_goods_array['g_id']=$g->g_id;
						$new_goods_array['g_vendor']=$g->g_vendor;
						$new_goods_array['g_stock']=$g->g_stock;
						$new_goods_array['options_hash']=$g->options_hash;
						$new_goods_array['options_data']=$g->options_data;
						$new_goods_array['quantity']=$quantity;
						if($this->checkQuantity(0, $new_goods_array, $quantity)){
							$this->goods[$new_id]=$new_goods_array;
							$this->saveBasket();
							$check=true;
						} else {
							$check=false;
						}
					} else {
						$check=false;
					}
				}
			}
		}
		return $check;
	}
	private function setIntersection(&$g, &$optArr){
		$result=true;
		$g->options_text=array();
		$g->options_files=array();
		$g->options_data=array();
		if(count($g->options_template)){ // есть шаблон опций
			foreach ($g->options_template as $topt_key=>$topt_val){ //  бежим по шаблону для каждой опции
				if(count($topt_val->optionsData)){ // есть значения опций в шаблоне опций
					$found=false;
					$g_options_text_temp=array();
					foreach($topt_val->optionsData as $odk=>$odv){ // бежим по щаблону и проверяем пришло или нет
						if(isset($optArr[$topt_key][$odk])){
							$quantity = $optArr[$topt_key][$odk]['quantity'];
							$val_id = $optArr[$topt_key][$odk]['val_id'];
							if($quantity && $val_id){
								$found=true;
								$g->options_data[$topt_key][$odk]['quantity']=$quantity;
								$g->options_data[$topt_key][$odk]['val_id']=$val_id;
								$g->options_data[$topt_key][$odk]['o_extcode']=$topt_val->o_extcode;
								$g->options_data[$topt_key][$odk]['od_extcode']=$topt_val->od_extcode;
								$g->options_data[$topt_key][$odk]['ov_extcode']=$odv->ov_extcode;
								$g->options_data[$topt_key][$odk]['ovd_extcode']=$odv->ovd_extcode;
								$g_options_text_temp[]=$odv->ov_name.($quantity>1 ? " (".$quantity.")" : "");
							}
						} else {
							$quantity=0;
							$val_id=0;
						}
					}
					if($found) $g->options_text[]=$topt_val->o_title.": ".implode(", ", $g_options_text_temp);
					// ПОЧЕМУ ТО РАНЬШЕ ИСКЛЮЧАЛИ CHECKBOX
					// elseif($topt_val->o_required && $topt_val->t_input_type!="checkbox"){
					elseif($topt_val->o_required){
						// ошибка, поле нужно, но значения или количества нет
						$this->basket_message.=Text::_("Some fields not filled")." ".Text::_("Code")." 0x0001";
						$result=false;
					}
				} else { // единичное значение типа текст и т.д.
					if(isset($optArr[$topt_key][0])){
						$quantity = $optArr[$topt_key][0]['quantity'];
						$value = $optArr[$topt_key][0]['value'];
					} else {
						$quantity=0;
						$value=false;
					}
					if($topt_val->o_required && (!$quantity || !$value)){
						// ошибка, поле нужно, но значения или количества нет
						$this->basket_message.=Text::_("Some fields not filled")." ".Text::_("Code")." 0x0002";
						$result=false;
					} else {
						if($value){
							if($topt_val->t_input_type=="file") {
								if(is_file($this->getTempFile($value))){
									$g->options_data[$topt_key][0]['quantity']=$quantity;
									$g->options_data[$topt_key][0]['value']=$value;
									$g->options_data[$topt_key][0]['o_extcode']=$topt_val->o_extcode;
									$g->options_data[$topt_key][0]['od_extcode']=$topt_val->od_extcode;
									$g->options_data[$topt_key][0]['ov_extcode']=false;
									$g->options_data[$topt_key][0]['ovd_extcode']=false;
									$g->options_text[]=$topt_val->o_title.": ".Text::_("Y");
									$g->options_files[$topt_key][0]['title']=$topt_val->o_title;
									$g->options_files[$topt_key][0]['value']=$value;
								} else {
									$this->basket_message.=Text::_("Failed to upload file");
									$result=false;
								}
							} else {
								$g->options_data[$topt_key][0]['quantity']=$quantity;
								$g->options_data[$topt_key][0]['value']=$value;
								$g->options_data[$topt_key][0]['o_extcode']=$topt_val->o_extcode;
								$g->options_data[$topt_key][0]['od_extcode']=$topt_val->od_extcode;
								$g->options_data[$topt_key][0]['ov_extcode']=false;
								$g->options_data[$topt_key][0]['ovd_extcode']=false;
								$g->options_text[]=$topt_val->o_title.": ".$value.($quantity>1 ? " (".$quantity.")" : "");
							}
						}
					}
				}
			}
		}
		return $result;
	}
	public function getBasketHash(){
		return $this->basket_hash;
	}
	public function getTempFile($value, $path=true){
		if($path) return PATH_TMP.$this->temp_files_folder.DS.Files::splitAppendix($value, $path);
		else return "/tmp/".str_replace(DS, "/", $this->temp_files_folder)."/".Files::splitAppendix($value, $path);
	}
	public function updateBasketPosition ($psid, $quantity) {
		$result=false;
		if ($this->goods && count($this->goods) && array_key_exists($psid, $this->goods)) {
			if ($this->checkQuantity($psid, $this->goods[$psid], $quantity)){
				$this->goods[$psid]['quantity']=$quantity;
				$this->saveBasket();
				$result=true;
			}
		}
		return $result;
	}
	public function deleteBasketPosition ($psid) {
		if ($this->goods && count($this->goods) && array_key_exists($psid, $this->goods)) {
			unset($this->goods[$psid]);
			$this->saveBasket();
		}
	}
	public function deleteTempFolder(){
		$basket_folder=PATH_TMP.$this->temp_files_folder.DS;
		// FIXME: Надо чистить временные файлы
	}
	private function showEmptyBasket() {
		$_html='';
		$lPath = PATH_TEMPLATES.Portal::getInstance()->getTemplate().DS.'html'.DS.'basket_empty.php';
		if (is_file($lPath)) {
			ob_start();
			include $lPath;
			$_html.= ob_get_contents();
			ob_end_clean();
		} else {
			// ###ВЕРСТКА НАЧАТА###
			$_html = "<div id=\"mybasket\" class=\"empty_basket\">".Text::_('Basket is empty')."</div>";
			// ###ВЕРСТКА ЗАКОНЧЕНА###
		}
		return $_html;
	}
	private function showEmptyBasketMini() {
		$_html='';
		$lPath = PATH_TEMPLATES.Portal::getInstance()->getTemplate().DS.'html'.DS.'minibasket_empty.php';
		if (is_file($lPath)) {
			ob_start();
			include $lPath;
			$_html.= ob_get_contents();
			ob_end_clean();
		} else {
			// ###ВЕРСТКА НАЧАТА###
			$_html = "<div id=\"mybasket\" class=\"empty_basket\">".Text::_('Basket is empty')."</div>";
			// ###ВЕРСТКА ЗАКОНЧЕНА###
		}
		return $_html;
	}
	public function showMini() {
		$_html="";
		$fullview=catalogConfig::$basket_fullview;		
		$this->calculateGoods();
		$summa=0; $this->points=0; 
		if (catalogConfig::$multy_vendor) {
			$vendor_ids=$this->getGoodsVendorsFromDB();
		} else {
			$vendor_ids=array(catalogConfig::$default_vendor);
		}
		$tmp_items=array();
		$all_quantity=0;
		if (($this->goods)&&(count($this->goods)>0)){
			foreach($this->items as $g){
				$current_price = $g->g_price;
				$current_sum= $g->g_price * $g->g_quantity;
				$summa=$summa + $current_sum;
				$all_quantity+=$g->g_quantity;
				if(isset($tmp_items[$g->g_id])){
					$tmp_items[$g->g_id]->g_quantity=$tmp_items[$g->g_id]->g_quantity + $g->g_quantity;
				} else {
					$tmp_items[$g->g_id] = new stdClass();
					$tmp_items[$g->g_id]->g_name = $g->g_name;
					$tmp_items[$g->g_id]->g_quantity = $g->g_quantity;
					$tmp_items[$g->g_id]->g_sell_measure = $g->g_sell_measure;
				}
			}
			$lPath = PATH_TEMPLATES.Portal::getInstance()->getTemplate().DS.'html'.DS.'minibasket.php';
			if (is_file($lPath)) {
				ob_start();
				require $lPath;
				$_html .= ob_get_contents();
				ob_end_clean();
			} else {
				// ###ВЕРСТКА НАЧАТА###
				if($fullview){
					foreach($tmp_items as $ti)	$_html .= "<p class=\"goods_row\">".$ti->g_name." x ".number_format($ti->g_quantity, catalogConfig::$quantity_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)." ".Measure::getInstance()->getShortName($ti->g_sell_measure)."</p>";
				} else {
					$_html .= "<p class=\"goods_row\">".Text::_('Quantity')." : ".$all_quantity."</p>";
				}
				if(!catalogConfig::$hide_prices) $_html .= "<p class=\"subtotal\">".Text::_('Sum')." : ".number_format($summa, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."&nbsp;".Currency::getShortName(DEFAULT_CURRENCY)."</p> \n";
				$_html .= "<p class=\"buttons\"><a rel=\"nofollow\" class=\"linkButton btn btn-info\" href=\"".Router::_("index.php?module=catalog&amp;view=orders&amp;layout=basket"
						.(User::getInstance()->isLoggedIn() && count($vendor_ids)==1 ? "&amp;vendor=".$vendor_ids[0] : ""))
						."\">".Text::_('Modify')."/".Text::_('Make order')."</a></p> \n";
				// ###ВЕРСТКА ЗАКОНЧЕНА###
			}
		} else $_html=$this->showEmptyBasketMini();
		return $_html;
	}
	private function getGoodsElement($g_id) {
		$result = array();
		$sql = "SELECT * FROM #__goods WHERE g_id=".$g_id." ORDER BY g_vendor";
		Database::getInstance()->setQuery($sql);
		Database::getInstance()->loadObject($result);
		$result->options_template = Module::getHelper("goods","catalog")->getOptions($g_id);
		return $result;
	}
	private function getGoodsIds(){
		$result=array();
		if(count($this->goods)){
			foreach($this->goods as $g){
				$result[]=$g['g_id'];
			}
		}
		return $result;
	}
	private function getGoodsFromDB() {
		$result = array();
		if (is_array($this->goods)&&(count($this->goods)>0)){
			$spisok=implode(",", $this->getGoodsIds());
			$sql = "SELECT * FROM #__goods WHERE g_id IN (".$spisok.")";
			$sql.=" ORDER BY g_vendor";
			Database::getInstance()->setQuery($sql);
			return Database::getInstance()->loadObjectList('g_id');
		}
		return $result;
	}
	private function getGoodsVendorsFromDB() {
		$db = Database::getInstance();
		$result = array();
		if (is_array($this->goods)&&(count($this->goods)>0))	{
			$spisok=implode(",", $this->getGoodsIds());
			$sql = "SELECT DISTINCT(g_vendor) FROM #__goods WHERE g_id IN (".$spisok.") and g_vendor>0 ORDER BY g_vendor";
			$db->setQuery($sql);
			$result = array_keys($db->loadObjectList("g_vendor"));
		}
		return $result;
	}
	public function calculateOrder($pt, $dt, $vendor_id=0, $userdata=array()){
		$_goods = $this->calculateVendor($vendor_id);
		
		$this->deliverySum=$this->calculateDeliverySum($dt, false, $_goods, $userdata);
		$this->calculateTaxSum($dt->getTaxId(), $this->deliverySum, true);
		$this->total=$this->total + $this->deliverySum;
		
		$this->paymentSum=$this->calculatePaymentSum($pt, false, $_goods, $userdata);
		$this->total=$this->total + $this->paymentSum;
		
		$this->discountSum=$this->calculateDiscountSum();
		$this->total=$this->total - $this->discountSum;
		
		return $_goods;
	}
	
	public function calculateVendor($vendor_id=0){
		if ($this->items==null) $this->calculateGoods();
		$_goods=array();
		// Заново нулим все и считаем от вендора
		$this->summa=0; $this->total=0; $this->taxesSum=0; $this->taxesInOrder=array();$this->points=0; $this->weight = 0; $this->not_enough_quantity = false;
		// $this->quantity = 0;
		if(!$vendor_id) $vendor_id=Session::getVar("basket_vendor");
		if(is_array($this->items) && count($this->items)){
			foreach($this->items as $k=>$g) {
				if ($g->g_quantity && (($g->g_vendor==$vendor_id && catalogConfig::$multy_vendor) || !catalogConfig::$multy_vendor)) {
					$_goods[$k]=$g;
					if($g->not_enough_quantity) $this->not_enough_quantity = true;
					$this->calculateTaxSum($g->g_tax_id, $g->g_sum, true);
					$current_weight = Measure::getInstance()->convert($g->g_total_weight, $g->g_wmeasure, catalogConfig::$default_wmeasure);
					$this->weight = $this->weight + $current_weight;
					$this->summa=$this->summa + $g->g_sum;
					$this->points=$this->points + $g->g_points;
				}
			}
		}
		$this->total=$this->summa;
		$this->discountSum=$this->calculateDiscountSum();
		$this->total=$this->total-$this->discountSum;
		return $_goods;
	}
	
	public function getGoods(){
		if ($this->items==null) $this->calculateGoods();
		return $this->items;
	}
	private function updateItemValues(&$g){
		if (Event::raise("basket.items.options.calculate", array(), $g)==true) return;
		$curPrice=$g->g_base_price;
		$curPoints=$g->g_points;
		$curLength=$g->g_length;
		$curWidth=$g->g_width;
		$curHeight=$g->g_height;
		$curWeight=$g->g_weight;
		
		//$tp = User::getInstance()->u_pricetype;
		//$price_name="ovd_price_".$tp;
		$price_name="ovd_price_".User::getInstance()->u_pricetype;
		foreach ($g->options_data as $opt_key=>$opt_data){
			if(count($opt_data)){
				foreach($opt_data as $val_key=>$val_data){
					if(isset($val_data['val_id'])){
						if(isset($g->options_template[$opt_key]) && isset($g->options_template[$opt_key]->optionsData[$val_data['val_id']])){
							$cur_opt=$g->options_template[$opt_key]->optionsData[$val_data['val_id']];
							$cur_quantity = $val_data['quantity'];
							// Price
							if($cur_opt->ovd_price_sign=='+')	$curPrice = $curPrice + (Currency::getInstance()->convert($cur_opt->{$price_name}, $g->g_currency) * $cur_quantity);
							elseif($cur_opt->ovd_price_sign=='-') $curPrice = $curPrice - (Currency::getInstance()->convert($cur_opt->{$price_name}, $g->g_currency) * $cur_quantity);
							elseif($cur_opt->ovd_price_sign=='*') $curPrice = $curPrice * pow($cur_opt->{$price_name}, $cur_quantity);
							// Points
							if($cur_opt->ovd_points_sign=='+')	$curPoints = $curPoints + ($cur_opt->ovd_points * $cur_quantity);
							elseif($cur_opt->ovd_points_sign=='-') $curPoints = $curPoints - ($cur_opt->ovd_points * $cur_quantity);
							elseif($cur_opt->ovd_points_sign=='*') $curPoints = $curPoints * pow($cur_opt->ovd_points, $cur_quantity);
							// Length
							if($cur_opt->ovd_length_sign=='+')	$curLength = $curLength + ($cur_opt->ovd_length * $cur_quantity);
							elseif($cur_opt->ovd_length_sign=='-') $curLength = $curLength - ($cur_opt->ovd_length * $cur_quantity);
							elseif($cur_opt->ovd_length_sign=='*') $curLength = $curLength * pow($cur_opt->ovd_length, $cur_quantity);
							// Width
							if($cur_opt->ovd_width_sign=='+')	$curWidth = $curWidth + ($cur_opt->ovd_width * $cur_quantity);
							elseif($cur_opt->ovd_width_sign=='-') $curWidth = $curWidth - ($cur_opt->ovd_width * $cur_quantity);
							elseif($cur_opt->ovd_width_sign=='*') $curWidth = $curWidth * pow($cur_opt->ovd_width, $cur_quantity);
							// Height
							if($cur_opt->ovd_height_sign=='+')	$curHeight = $curHeight + ($cur_opt->ovd_height * $cur_quantity);
							elseif($cur_opt->ovd_height_sign=='-') $curHeight = $curHeight - ($cur_opt->ovd_height * $cur_quantity);
							elseif($cur_opt->ovd_height_sign=='*') $curHeight = $curHeight * pow($cur_opt->ovd_height, $cur_quantity);
							// Weight
							if($cur_opt->ovd_weight_sign=='+')	$curWeight = $curWeight + ($cur_opt->ovd_weight * $cur_quantity);
							elseif($cur_opt->ovd_weight_sign=='-') $curWeight = $curWeight - ($cur_opt->ovd_weight * $cur_quantity);
							elseif($cur_opt->ovd_weight_sign=='*') $curWeight = $curWeight * pow($cur_opt->ovd_weight, $cur_quantity);
								
						}
					}
				}
			}
		}
		$g->g_price = round($curPrice, 2);
		$g->g_points = round($curPoints, 0);
		$g->g_length = round($curLength, 3);
		$g->g_width = round($curWidth, 3);
		$g->g_height = round($curHeight, 3);
		$g->g_weight = round($curWeight, 3);
		$g->g_sum = round($g->g_price * $g->g_quantity, 2);
	}
	private function calculateGoods(){ // попробуем вариант чтобы не пересчитывать с нуля
		$this->items=null; $this->summa=0; $this->taxesInOrder=array(); $this->taxesSum=0; $this->total=0; $this->points=0;  $this->weight = 0;  
		// $this->quantity = 0;
		$this->prepareTaxes();
		$garr=$this->getGoodsFromDB();
		if (is_array($this->goods)&&(count($this->goods)>0)){
			$discounts=Module::getHelper("goods","catalog")->getDiscounts(array_keys($garr));
			$extPrices=Module::getHelper("goods","catalog")->getExtendedPrices(array_keys($garr));
			foreach($this->goods as $gk=>$gv){
				$i=$gv['g_id'];
				if(array_key_exists($i, $garr)){
					if($this->goods[$gk]['quantity']>0){
						$this->items[$gk]=new stdClass();
						$this->items[$gk]->g_id = $i;
						$this->items[$gk]->g_quantity = $this->goods[$gk]['quantity'];
						/*
						if($this->checkQuantity($gk, $this->goods[$gk], $this->items[$gk]->g_quantity)){
							$this->items[$gk]->not_enough_quantity = false;
						} else {
							$this->items[$gk]->not_enough_quantity = true;
						}
						*/
						$this->items[$gk]->not_enough_quantity = !$this->checkQuantity($gk, $this->goods[$gk], $this->items[$gk]->g_quantity);
						$this->items[$gk]->g_old_price = $this->getBasePrice($garr[$i]);
						$this->items[$gk]->g_base_price = Module::getHelper("goods","catalog")->applyExtendedPrices($i, $this->items[$gk]->g_old_price, $this->items[$gk]->g_quantity, $extPrices);
						$this->items[$gk]->g_base_price = Module::getHelper("goods","catalog")->applyDiscounts($i, $this->items[$gk]->g_base_price, $discounts);
						$this->items[$gk]->options_hash = $this->goods[$gk]['options_hash'];
						$this->items[$gk]->options_data = $this->goods[$gk]['options_data'];
						$this->items[$gk]->options_text = array();
						$garr[$i]->g_total_weight = 0;
						switch($garr[$i]->g_selltype){
							case 1:
								$garr[$i]->g_sell_measure=$garr[$i]->g_pack_measure;
								$garr[$i]->g_total_weight = $garr[$i]->g_weight * $this->goods[$gk]['quantity'] * $garr[$i]->g_pack_koeff;
								break;
							case 2:
								$garr[$i]->g_sell_measure=$garr[$i]->g_wmeasure;
								$garr[$i]->g_total_weight = $this->goods[$gk]['quantity'];
								break;
							case 3:
								$garr[$i]->g_sell_measure=$garr[$i]->g_vmeasure;
								$volume = 0 ;
								$height = Measure::getInstance()->convert($garr[$i]->g_height, $garr[$i]->g_size_measure, catalogConfig::$size4volume_measure);
								$width = Measure::getInstance()->convert($garr[$i]->g_width, $garr[$i]->g_size_measure, catalogConfig::$size4volume_measure);
								$length = Measure::getInstance()->convert($garr[$i]->g_length, $garr[$i]->g_size_measure, catalogConfig::$size4volume_measure);
								$_volume = $height * $width * $length;
								if($_volume){
									$base_volume = Measure::getInstance()->convert($_volume, catalogConfig::$default_vol_measure, $garr[$i]->g_vmeasure);
									if($base_volume){
										$garr[$i]->g_total_weight = ($garr[$i]->g_weight/$base_volume) * $this->goods[$gk]['quantity'];
									}
								}
								break;
							case 4:	// break;
							case 5:	// break;
							case 0:
							default:
								$garr[$i]->g_sell_measure=$garr[$i]->g_measure;
								$garr[$i]->g_total_weight = $garr[$i]->g_weight * $this->goods[$gk]['quantity'];
								break;
						}
						$this->items[$gk]->options_template = Module::getHelper("goods","catalog")->getOptions($i);
						if($this->setIntersection($this->items[$gk], $this->goods[$gk]['options_data'])){
							$this->items[$gk]->g_width = $garr[$i]->g_width;
							$this->items[$gk]->g_length = $garr[$i]->g_length;
							$this->items[$gk]->g_height = $garr[$i]->g_height;
//							$this->items[$gk]->g_price = $this->items[$gk]->g_base_price;
							$this->items[$gk]->g_points = $garr[$i]->g_points;
							$this->items[$gk]->g_weight = $garr[$i]->g_weight;
							$this->items[$gk]->g_total_weight = $garr[$i]->g_total_weight;
							$this->items[$gk]->g_sum = 0;
							$this->items[$gk]->g_currency = $garr[$i]->g_currency;
							$this->updateItemValues($this->items[$gk]);
							$this->items[$gk]->g_selltype = $garr[$i]->g_selltype;
							$this->items[$gk]->g_sell_measure = $garr[$i]->g_sell_measure;
							$this->items[$gk]->g_id = $garr[$i]->g_id;
							$this->items[$gk]->g_sku = $garr[$i]->g_sku;
							$this->items[$gk]->g_name = $garr[$i]->g_name;
							$this->items[$gk]->g_alias = $garr[$i]->g_alias;
							$this->items[$gk]->g_thumb = Module::getHelper("goods","catalog")->getThumbImage($garr[$i]->g_thumb);
							$this->items[$gk]->g_measure = $garr[$i]->g_measure;
							$this->items[$gk]->g_tax_id = $garr[$i]->g_tax;
							$this->items[$gk]->g_tax_val = $this->getTaxValue($garr[$i]->g_tax);
							$this->items[$gk]->g_tax = $this->calculateTaxSum($garr[$i]->g_tax, $this->items[$gk]->g_sum, false);
							$this->items[$gk]->g_tax_name = $this->getTaxName($garr[$i]->g_tax);
							$this->items[$gk]->g_wmeasure = $garr[$i]->g_wmeasure;
							$this->items[$gk]->g_vendor = $garr[$i]->g_vendor;
							$this->items[$gk]->g_type = $garr[$i]->g_type;
							$this->items[$gk]->g_extcode = $garr[$i]->g_extcode;
//							$this->weight = $this->weight + Measure::getInstance()->convert($this->items[$gk]->g_weight, $this->items[$gk]->g_sell_measure, catalogConfig::$default_wmeasure);
//							$this->summa=$this->summa + $this->items[$gk]->g_sum;
//							$this->points=$this->points + $this->items[$gk]->g_points;
//							$this->total = $this->summa;
						} else{ // ошибка в опциях, надо удалить из корзины
							$this->deleteBasketPosition($gk);
						}
					} else{ // количество=0, надо удалить из корзины
						$this->deleteBasketPosition($gk);
					}
				} else{ // нет в базе, надо удалить из корзины
					$this->deleteBasketPosition($gk);
				}
			}
		}
		return $this->items;
	}
	private function calculateDeliverySum($dt, $order, $items, $userdata) {
		$dt->assignOrder($order,$items);
		$price=$dt->calculate(2, $userdata);
		$val=Currency::getInstance()->convert($price,$dt->getCurrency());
		return $val;
	}
	private function calculatePaymentSum($pt, $order, $items, $userdata) {
		$pt->assignOrder($order,$items);
		$price=$pt->calculate($userdata);
		$val=Currency::getInstance()->convert($price,$pt->getCurrency());
		return $val;
	}
	public function getTaxName($tax_id){
		if(!$this->taxes) return "";
		if(!array_key_exists($tax_id, $this->taxes)) return "";
		return $this->taxes[$tax_id]->t_name;

	}
	public function getTaxValue($tax_id){
		if(!$this->taxes) return 0;
		if(!array_key_exists($tax_id, $this->taxes)) return 0;
		return $this->taxes[$tax_id]->t_value;
	}
	public function getTaxSum($tax_id, $current_sum){
		if(!$this->taxes||!$current_sum) return 0;
		if(!array_key_exists($tax_id, $this->taxes)) return 0;
		$sumtax=round($current_sum/(100+$this->taxes[$tax_id]->t_value)*$this->taxes[$tax_id]->t_value,2);
		return $sumtax;
	}
	private function calculateTaxSum($tax_id, $current_sum, $add2order=false){
		if(!$this->taxes||!$current_sum) return 0;
		if(!array_key_exists($tax_id, $this->taxes)) return 0;
		$sumtax=round($current_sum/(100+$this->taxes[$tax_id]->t_value)*$this->taxes[$tax_id]->t_value,2);
		if($add2order && $sumtax>0) {
			if(isset($this->taxesInOrder[$tax_id])) $this->taxesInOrder[$tax_id]+=$sumtax;
			else $this->taxesInOrder[$tax_id]=$sumtax;
			$this->taxesSum+=$sumtax;
		}
		return $sumtax;
	}
	private function calculateDiscountSum(){ // здесь считается только персональная скидка юзера
		return 0; // НЕ УЧИТЫВАЕМ ЕЕ ЗДЕСЬ, ПРИМЕНЯТЬ ЕЕ НАДО В ОБЩИХ СКИДКАХ, А СЧИТАТЬ ВСЕ СУММАРНО, И ТОГДА НЕ ВЫЧИТАТЬ ЕЕ В МЕСТЕ ВЫЗОВА ДАНОЙ ФУНКЦИИ
		return round($this->total*(User::getInstance()->u_discount/100),2);
	}
	private function prepareTaxes(){
		$this->taxes=Taxes::getAllTaxes();
	}
	private function getBasePrice($g) {
		$tp = User::getInstance()->u_pricetype;
		$price_name="g_price_".$tp;
		if ($g->g_type==5 && catalogConfig::$complectPriceAsGoodsSum){
			$price_val=$this->getBasePriceAsSum($g->g_id, $tp);
		} else {
			$price_val=$g->{$price_name};
		}
		$_val=Currency::getInstance()->convert($price_val, $g->g_currency);
		
		return $_val;
	}
	private function getBasePriceAsSum($gid, $tp){
		$db = Database::getInstance();
		$g_price=0;
		$sql="SELECT g.g_id, g.g_price_".$tp." AS g_price, s.s_quantity";
		$sql.=" FROM #__goods_sets AS s, #__goods AS g";
		$sql.=" WHERE s.s_id=g.g_id AND s.g_id=".$gid;
		$db->setQuery($sql);
		$gsets = $db->loadObjectList();
		if(count($gsets)){
			foreach($gsets as $gset) {
				$g_price=$g_price+$gset->g_price*$gset->s_quantity;
			}
		}
		return $g_price;
	}
	public function modifyBasket($ajax=false, $read_only=true, $no_buttons=true, $subtotals=false) {
		$basket_div="mybasket";
		if ($this->items==null) $this->calculateGoods();
		if ($ajax) $basket_div="mybasket_ajax";
		$colspan=6;
		if ($this->points_enabled) $colspan=$colspan+1;
		if (catalogConfig::$multy_vendor) {
			if ($ajax || Session::getVar("basket_vendor")==0) {
				$vendor_ids=$this->getGoodsVendorsFromDB();
				$read_only=false;
				$no_buttons=false;
			} else {
				$vendor_ids=array(Session::getVar("basket_vendor"));
			}
		} else {
			if ($ajax || Session::getVar("basket_vendor")==0) {
				$read_only=false;
				$no_buttons=false;
			}
			$vendor_ids=array(catalogConfig::$default_vendor);
		}
		$lPath = PATH_TEMPLATES.Portal::getInstance()->getTemplate().DS.'html'.DS.'basket.php';
		
		if ($this->items && count($this->items)){
			$_html = "";
			foreach($vendor_ids as $vendor_id) {
				$summa=0;
				$not_enough_quantity=false; $not_enough_sum=false; $not_enough_sum_text = "";
				$vendor=Vendor::getInstance()->getVendor($vendor_id);
				$this->points=0;
				if(is_object($vendor)){
					if(count($this->calculateVendor($vendor_id))){
						if($vendor->v_minimum_basket && $vendor->v_minimum_basket>$this->total){
							$not_enough_sum = true;
							if(count($vendor_ids)>1) $not_enough_sum_text = Text::_("Minimum basket sum for selected vendor");
							else $not_enough_sum_text = Text::_("Minimum basket sum");
						}
						if (is_file($lPath)) {
							ob_start();
							require $lPath;
							$_html .= ob_get_contents();
							ob_end_clean();
						} else {
							ob_start();
							/* ###ВЕРСТКА НАЧАТА### */
							?>
								<?php 
								$cols4sku = 1;
								$cols4thumb = 1;
								$cols4name = 3;
								$cols4points = 1;
								$cols4price = 2;
								$cols4sum = 2;
								$cols4measure = ($read_only ? 0 : 1);
								$col4delete = ($read_only ? 0 : 1); 
								if(!$this->points_enabled) $cols4name = $cols4name + $cols4points;
								if(catalogConfig::$hide_prices) $cols4name = $cols4name + $cols4price + $cols4sum - $col4delete;
								$cols4name = $cols4name - $cols4measure;
								?>
								<h4 class="basket-vendor"><?php echo Text::_("Vendor")." : ".$vendor->v_name; ?></h4>
								<?php if($not_enough_sum){ ?>
									<h5 class="basket-min-sum-caution"><?php echo $not_enough_sum_text; ?>: <?php echo number_format($vendor->v_minimum_basket, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."&nbsp;".Currency::getShortName(DEFAULT_CURRENCY);?></h5>
								<?php } ?>
								<div class="basket-header hidden-xs">
									<div class="row midi-gutter">
										<div class="basket-header-cell basket-header-sku col-sm-<?php echo $cols4sku; ?>">
											<?php echo Text::_('Articul'); ?>
										</div>
										<div class="basket-header-cell basket-header-thumb col-sm-<?php echo $cols4thumb; ?>"></div>
										<div class="basket-header-cell basket-header-name col-sm-<?php echo $cols4name; ?>">
											<?php echo Text::_('Goods title'); ?>
										</div>
										<?php if ($this->points_enabled) { ?>
											<div class="basket-header-cell basket-header-points col-sm-<?php echo $cols4points; ?>">
												<?php echo Text::_('Points'); ?>
											</div>
										<?php } ?>
										<?php if(!catalogConfig::$hide_prices) { ?>
											<div class="basket-header-cell basket-header-price col-sm-<?php echo $cols4price; ?>">
												<?php echo Text::_('Price'); ?>
											</div>
										<?php } ?>
										<div class="basket-header-cell basket-header-quantity col-sm-2">
											<?php echo Text::_('Quantity'); ?>
										</div>
										<?php if (!$read_only) { ?>
											<div class="basket-header-cell basket-header-measure col-sm-<?php echo $cols4measure; ?>">
												<?php echo Text::_('Measure'); ?>
											</div>
										<?php } ?>
										<?php if(!catalogConfig::$hide_prices) { ?>
											<div class="basket-header-cell basket-header-sum col-sm-<?php echo $cols4sum;?>">
												<?php echo Text::_('Sum'); ?>
											</div>
										<?php } ?>
									</div>
								</div>
								<div class="basket-body">
									<?php
									foreach($this->items as $k=>$g) { ?>
										<?php if ($g->g_quantity && (($g->g_vendor==$vendor_id && catalogConfig::$multy_vendor) || !catalogConfig::$multy_vendor)) { ?>
										<?php 
										if($g->not_enough_quantity) {
											$err_class = " error";
											$not_enough_quantity = true;
											$this->basket_message=Text::_("Not enough quantity in stock");
										} else {
											$err_class = "";
										} ?>
										<div class="basket-row<?php echo $err_class; ?>">
											<div class="row row-cells-autoheight midi-gutter">
												<?php 
												$current_price=$g->g_price;
												$current_sum=$current_price * $g->g_quantity;
												$summa=$summa+$current_sum;
												if(count($g->options_text)) {
													$options_title = implode("; ", $g->options_text);
													$options_text =  "<span title=\"".$options_title."\" class=\"basket-row-options\">".mb_substr($options_title, 0, siteConfig::$shortTextLength)."...</span>";
													if(count($g->options_files)) {
														$options_text.="<span class=\"basket-row-options\">".Text::_("Files").":";
														foreach($g->options_files as$fk=>$fv){
															$options_text.=" [<a target=\"_blank\" href=\"".$this->getTempFile($fv[0]['value'], false)."\">".$fv[0]['title']."</a>]";
														}
														$options_text.="</span>";
													}
												} else {
													$options_text="";
													$options_title="";
												}
												$href = Router::_("index.php?module=catalog&view=goods&layout=info&psid=".$g->g_id."&amp;alias=".$g->g_alias);
												?>
												<div class="basket-row-cell basket-row-sku col-xs-4 col-sm-<?php echo $cols4sku; ?>">
													<div class="row-cell-wrapper"><div class="row-cell-wrapper-inner">
														<span class="hidden-sm hidden-md hidden-lg"><?php echo Text::_('Articul'); ?>: </span><?php echo $g->g_sku; ?>
													</div></div>
												</div>
												<div class="basket-row-cell basket-row-thumb hidden-xs col-sm-<?php echo $cols4thumb; ?>">
													<div class="row-cell-wrapper"><div class="row-cell-wrapper-inner">
														<img alt="" src="<?php echo ($g->g_thumb ? $g->g_thumb : Module::getHelper("goods", "catalog")->getEmptyImage()); ?>" />
													</div>
												</div></div>
												<div class="basket-row-cell basket-row-name col-xs-8 col-sm-<?php echo $cols4name; ?>">
													<div class="row-cell-wrapper"><div class="row-cell-wrapper-inner">
														<a href="<?php echo $href; ?>"><?php echo $g->g_name; ?></a><?php echo $options_text; ?>
													</div></div>
												</div>
												<?php if ($this->points_enabled) { ?>
													<?php $current_points=$g->g_points * $g->g_quantity; ?>
													<?php $this->points=$this->points + $current_points; ?>
													<div class="basket-row-cell basket-row-points col-xs-6 col-sm-<?php echo $cols4points; ?>">
														<div class="row-cell-wrapper"><div class="row-cell-wrapper-inner">
															<span class="hidden-sm hidden-md hidden-lg"><?php echo Text::_('Points'); ?>: </span><?php echo $current_points; ?>
														</div></div>
													</div>
												<?php } ?>
												<?php if(!catalogConfig::$hide_prices){?>
													<div class="basket-row-cell basket-row-price col-xs-6 col-sm-<?php echo $cols4price; ?>">
														<div class="row-cell-wrapper"><div class="row-cell-wrapper-inner">
															<span class="hidden-sm hidden-md hidden-lg"><?php echo Text::_('Price'); ?>: </span><?php echo number_format($current_price, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR); ?>
														</div></div>
													</div>
												<?php } ?>	
												<div class="basket-row-cell basket-row-quantity col-xs-6 col-sm-2<?php echo ($read_only ? " read-only" : ""); ?>">
													<div class="row-cell-wrapper"><div class="row-cell-wrapper-inner">
													<?php if ($read_only) { ?>
														<span class="hidden-sm hidden-md hidden-lg"><?php echo Text::_('Quantity'); ?>: </span>
														<?php echo number_format($g->g_quantity, catalogConfig::$quantity_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR); ?>
														<?php echo Measure::getInstance()->getShortName($g->g_sell_measure); ?>
													<?php } else { ?>
														<input class="form-control numeric quantity-field" size="10" id="gq_<?php echo $k; ?>" name="gq_<?php echo $k; ?>" type="text" value="<?php echo number_format($g->g_quantity, catalogConfig::$quantity_digits, DEFAULT_DECIMAL_SEPARATOR , ""); ?>" />
														<a class="b_link_save" onclick="javascript:updateBasketPosition('<?php echo $k; ?>','gq_<?php echo $k; ?>','<?php echo $basket_div; ?>');" title="<?php echo Text::_('Update'); ?>"><img src="/images/blank.gif" alt="U" width="16" height="16" /></a>
													<?php } ?>
													</div></div>
												</div>
												<?php if (!$read_only) { ?>
													<div class="basket-row-cell basket-row-measure col-xs-6 col-sm-<?php echo $cols4measure; ?>">
														<div class="row-cell-wrapper"><div class="row-cell-wrapper-inner">
															<span class="hidden-sm hidden-md hidden-lg"><?php echo Text::_('Measure'); ?>: </span><?php echo Measure::getInstance()->getShortName($g->g_sell_measure); ?>
														</div></div>
													</div>
												<?php } ?>
												<?php if(!catalogConfig::$hide_prices){?>
													<div id="sum_<?php echo $g->g_id;?>" class="basket-row-cell basket-row-sum col-xs-12 col-sm-<?php echo $cols4sum; ?><?php echo ($read_only ? " read-only" : ""); ?>">
														<div class="row-cell-wrapper"><div class="row-cell-wrapper-inner">
															<span class="hidden-sm hidden-md hidden-lg"><?php echo Text::_('Sum'); ?>: </span><?php echo number_format($current_sum, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR); ?>
															<?php if (!$read_only) { ?>
																<a class="b_link_del" onclick="javascript:if (confirm('<?php echo Text::_("Are you sure"); ?> ?')) deleteBasketPosition('<?php echo $k;?>','<?php echo $basket_div;?>');" title="<?php echo Text::_('Delete');?>"><img src="/images/blank.gif" alt="D" width="16" height="16" /></a>
															<?php } ?>
														</div></div>
													</div>
												<?php } else { ?>
													<?php if (!$read_only) { ?>
														<div id="sum_<?php echo $g->g_id;?>" class="basket-row-cell basket-row-del col-xs-12 col-sm-<?php echo $col4delete; ?>">
															<div class="row-cell-wrapper"><div class="row-cell-wrapper-inner">
																<a class="b_link_del pull-right" onclick="javascript:if (confirm('<?php echo Text::_("Are you sure"); ?> ?')) deleteBasketPosition('<?php echo $k;?>','<?php echo $basket_div;?>');" title="<?php echo Text::_('Delete');?>"><img src="/images/blank.gif" alt="D" width="16" height="16" /></a>
															</div></div>
														</div>
													<?php } ?>
												<?php } ?>
											</div>
										</div>
										<?php } ?>
									<?php } ?>
								</div>
								<div class="basket-footer">
									<div class="row">
										<?php if ($this->points_enabled) { ?>
											<div class="basket-footer-cell basket-footer-points col-xs-6 col-sm-4 text-right">
												<?php echo Text::_('Total points'); ?>
											</div>
											<div class="basket-footer-cell basket-footer-points col-xs-6 col-sm-1 text-right">
												<?php echo $this->points; ?>
											</div>
										<?php } else { ?>
											<div class="basket-footer-cell hidden-xs col-xs-12 col-sm-5 text-right"></div>
										<?php } ?>
										
										<?php if(!catalogConfig::$hide_prices){?>
											<div class="basket-footer-cell col-xs-6 col-sm-4 text-right">
												<?php echo Text::_('Sum'); ?>
											</div>
											<div class="basket-footer-cell col-xs-6 col-sm-3 text-right">
												<?php echo number_format($summa, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."&nbsp;".Currency::getShortName(DEFAULT_CURRENCY); ?>
											</div>
										<?php } else { ?>
											<div class="basket-footer-cell hidden-xs col-xs-12 col-sm-7 text-right"></div>
										<?php } ?>
									</div>
									<?php if($subtotals && !catalogConfig::$hide_prices) { ?>
										<div class="subtotals">
											<?php if (($this->deliverySum)&&(($this->deliverySum)>0)) { ?>
											<div class="row">
												<div class="basket-footer-cell col-xs-6 col-sm-9 text-right">
													<?php echo Text::_("Delivery sum"); ?>
												</div>
												<div class="basket-footer-cell col-xs-6 col-sm-3 text-right">
													<?php echo number_format($this->deliverySum, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."&nbsp;".Currency::getShortName(DEFAULT_CURRENCY); ?>
												</div>
											</div>
											<?php } ?>
											<?php if (($this->paymentSum)&&($this->paymentSum>0)) { ?>
											<div class="row">
												<div class="basket-footer-cell col-xs-6 col-sm-9 text-right">
													<?php echo Text::_("Payment commission"); ?>
												</div>
												<div class="basket-footer-cell col-xs-6 col-sm-3 text-right">
													<?php echo number_format($this->paymentSum, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."&nbsp;".Currency::getShortName(DEFAULT_CURRENCY); ?>
												</div>
											</div>
											<?php } ?>
											<?php if ($this->discountSum) { ?>
											<div class="row">
												<?php if ($this->discountSum>0) { ?>
													<div class="basket-footer-cell col-xs-6 col-sm-9 text-right">
														<?php echo Text::_("Discount"); ?>
													</div>
												<?php } else { ?>
													<div class="basket-footer-cell col-xs-6 col-sm-9 text-right">
														<?php echo Text::_("Fee"); ?>
													</div>
												<?php } ?>
												<div class="basket-footer-cell col-xs-6 col-sm-3 text-right">
													<?php echo number_format($this->discountSum, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."&nbsp;".Currency::getShortName(DEFAULT_CURRENCY); ?>
												</div>
											</div>
											<?php } ?>
											<?php if($this->total!=$this->summa){ ?>
											<div class="row">
												<div class="basket-footer-cell col-xs-6 col-sm-9 text-right">
													<?php echo Text::_("Total"); ?>
												</div>
												<div class="basket-footer-cell col-xs-6 col-sm-3 text-right">
													<?php echo number_format($this->total, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."&nbsp;".Currency::getShortName(DEFAULT_CURRENCY); ?>
												</div>
											</div>
											<?php } ?>
											<?php if (count($this->taxesInOrder)&&$this->taxesSum) { ?>
												<div class="row">
													<div class="basket-footer-cell col-xs-6 col-sm-9 text-right">
														<?php echo Text::_("Taxes in order"); ?>
													</div>
													<div class="basket-footer-cell col-xs-6 col-sm-3 text-right">
														<?php echo number_format($this->taxesSum, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."&nbsp;".Currency::getShortName(DEFAULT_CURRENCY); ?>
													</div>
												</div>
												<?php foreach($this->taxesInOrder as $kt=>$vt){ ?>
													<div class="row">
														<div class="basket-footer-cell col-xs-6 col-sm-9 text-right">
															<?php echo $this->taxes[$kt]->t_name; ?>
														</div>
														<div class="basket-footer-cell col-xs-6 col-sm-3 text-right">
															<?php echo number_format($vt, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."&nbsp;".Currency::getShortName(DEFAULT_CURRENCY); ?>
														</div>
													</div>
												<?php } ?>
											<?php } ?>
											<?php if($this->total!=$this->summa || $this->taxesSum){ ?>
											<div class="row">
												<div class="basket-footer-cell col-xs-6 col-sm-9 text-right">
													<?php echo Text::_("Grand total"); ?>
												</div>
												<div class="basket-footer-cell col-xs-6 col-sm-3 text-right">
													<?php echo number_format($this->total, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."&nbsp;".Currency::getShortName(DEFAULT_CURRENCY); ?>
												</div>
											</div>
											<?php } ?>
										</div>
									<?php } ?>
									<?php if (!$no_buttons && !$read_only && !$not_enough_sum && !$not_enough_quantity) { ?>
										<?php if(catalogConfig::$ordersWithoutRegistration && !backofficeConfig::$cryptoUserData) { ?>
										<form action="<?php echo Router::_("index.php?module=catalog&amp;view=orders&amp;layout=basket&amp;vendor=".$vendor_id); ?>" method="post">
											<?php if($ajax){ ?>
											<div class="buttons">
												<a id="submit" class="linkButton btn btn-info" href="<?php echo Router::_("index.php?module=catalog&amp;view=orders&amp;layout=basket&amp;vendor=".$vendor_id); ?>"><?php echo Text::_('Proceed order'); ?></a>
												<?php if(!User::getInstance()->isLoggedIn()) { ?><input class="linkButton btn btn-info" type="submit" value="<?php echo Text::_("Proceed order without registration"); ?>" /><?php } ?>
											</div>
											<?php } else { ?>
												<?php if(!User::getInstance()->isLoggedIn()) { ?>
												<div class="buttons">
													<a rel="nofollow" class="linkButton relpopup btn btn-info" href="<?php echo Router::_("index.php?module=user&amp;view=login"); ?>"><?php echo Text::_("Log in");?></a>
													<?php if (!backofficeConfig::$noRegistration) { ?><a rel="nofollow" class="linkButton btn btn-info" href="<?php echo Router::_("index.php?module=user&amp;view=register"); ?>"><?php echo Text::_("Register");?></a><?php } ?>
												</div>
												<div class="buttons">
													<input class="linkButton btn btn-info" type="submit" value="<?php echo Text::_("Proceed order without registration"); ?>" />
												</div>
												<?php } else { ?>
												<div class="buttons">
													<a id="submit" class="linkButton btn btn-info" href="<?php echo Router::_("index.php?module=catalog&amp;view=orders&amp;layout=basket&amp;vendor=".$vendor_id); ?>"><?php echo Text::_('Proceed order'); ?></a>
												</div>
												<?php } ?>
											<?php } ?>
											<input type="hidden" name="without_registration" value="1" />
										</form>
										<?php }  else { ?>
										<div class="buttons">
											<a id="submit" class="linkButton btn btn-info" href="<?php echo Router::_("index.php?module=catalog&amp;view=orders&amp;layout=basket&amp;vendor=".$vendor_id); ?>"><?php echo Text::_('Proceed order'); ?></a>
										</div>
										<?php } ?>
									<?php } elseif(!$no_buttons && $read_only) {?>
									<div class="buttons">
										<a class="linkButton btn btn-info" href="<?php echo Router::_("index.php?module=catalog&amp;view=orders&amp;layout=basket"); ?>"><?php echo Text::_('Modify'); ?></a>
									</div>
									<?php } ?>
								</div>
							<?php 
							/* ###ВЕРСТКА ЗАКОНЧЕНА### */
							$_html .= ob_get_contents();
							ob_end_clean();
						}
						$_html .="<input type=\"hidden\" id=\"order_results_weight\" value=\"".$this->weight."\" />";
						$_html .="<input type=\"hidden\" id=\"order_results_summa\" value=\"".(catalogConfig::$hide_prices ? 0 : $this->summa)."\" />";
						$_html .="<input type=\"hidden\" id=\"order_results_summa_text\" value=\"".number_format($this->summa, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."\" />";
						$_html .="<input type=\"hidden\" id=\"order_results_total\" value=\"".(catalogConfig::$hide_prices ? 0 : $this->total)."\" />";
						$_html .="<input type=\"hidden\" id=\"order_results_total_text\" value=\"".number_format($this->total, catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)."\" />";
						$_html .="<input type=\"hidden\" id=\"order_results_currency\" value=\"".Currency::getShortName(DEFAULT_CURRENCY)."\" />";
					}
				}
			} // Vendor cycle
			$_html_pre = "<div id=\"".$basket_div."\" class=\"".($read_only ? "read-only-basket" : "modify-basket")."\">";
			if(Basket::getInstance()->basket_message) $_html_pre .= "<h4 class=\"message_error\">".Basket::getInstance()->basket_message."</h4>";
			if (count($vendor_ids)>1) {
				$_html_pre .= "<h4 class=\"basket_vendor_caution title\">".Text::_("You selected goods from several vendors").". ".Text::_("They are splitting to several orders").".</h4>";
			}
			$_html = $_html_pre.$_html."</div>";
		} else $_html=$this->showEmptyBasket();
		return $_html;
	}
}
?>
