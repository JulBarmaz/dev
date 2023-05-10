<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogCustomRouter extends Router {
	private $_views=array("goods", "page", "manufacturers", "orders", "payments", "price", "reports", "sales", "shopwindow", "vendors");
	public function parseRoute($request) {
		Debugger::getInstance()->message("Router (catalog) parsed URI ".$request);
		$_result=preg_split('/[\/]/',$request);
		$module=""; $view=""; $layout=""; $psid=""; $ggr_id=""; $alias="";$page404=false;
		// Callbacks for payments and deliveries
		$payment = ""; $delivery = ""; $mode = "";
		$poshtml=strpos($_result[count($_result)-1],'.html');
		$last_element = preg_split('/(\.html)/',$_result[count($_result)-1]);
		if (preg_match('/(index.php)/',$last_element[0])) $last_element[0]="";
		if (preg_match('/(default)/',$last_element[0])) $last_element[0]="";
		if ($last_element[0]) $_result[count($_result)-1] = $last_element[0];
		else unset($_result[count($_result)-1]);
		switch (count($_result)) {
			case 2:
				$module = $_result[1];
				break;
			case 3:
				$module = $_result[1]; $view   = $_result[2];
				if (!in_array($view, $this->_views)){
					$alias=$view;
					$psid = Module::getHelper("goods","catalog",true)->getGroupIdByAlias($view);
					if (!$psid) $page404=true;
					$view="goods";
				}
				break;
			case 4:
				$module = $_result[1]; 	$view   = $_result[2];
				if($poshtml===false) { $page404=true; break; }
				if (!in_array($view, $this->_views)){
					$alias=$_result[3];
					$psid = Module::getHelper("goods","catalog",true)->getGoodsIdByAlias($alias);
					if (!$psid) $page404=true;
					else $ggr_id = Module::getHelper("goods","catalog",true)->getGroupIdByAlias($view);
					$view="goods"; $layout="info";
				} else {
					if ($view=="orders" || $view=="page") $layout = $_result[3];
					else $psid = $_result[3];
				}
				break;
			case 5:
				$module = $_result[1]; $view = $_result[2];				
				$layout = $_result[3];
				if($poshtml===false) { $page404=true; break; }
				if (!in_array($view,$this->_views)){
					$alias=$view;
					$psid = Module::getHelper("goods","catalog",true)->getGoodsIdByAlias($view);
					if (!$psid) $page404=true;
					$view="goods"; 
				} else {
					switch($view){
						case "manufacturers":
							$alias = $layout;
							$layout = $_result[4];
							$psid = Module::getHelper("goods","catalog",true)->getManufacturerIdByAlias($alias);
							if(!$psid) {
								// JUST FOR COMPATIBILITY. DELETE LATER.
								$alias="";
								$psid = intval($_result[4]);
							}
							break;
						case "vendors":
							$alias = $layout;
							$layout = $_result[4];
							$psid = Module::getHelper("goods","catalog",true)->getVendorIdByAlias($alias);
							if(!$psid) {
								// JUST FOR COMPATIBILITY. DELETE LATER.
								$alias="";
								$psid = intval($_result[4]);
							}
							break;
						default:
							$psid = intval($_result[4]);
							break;
					}
					if (!$psid) $page404=true;
				}
			break;
			case 6:
				// Callbacks for payments and deliveries
				$module = $_result[1]; $view = $_result[2];
				$layout = $_result[3];
				if($view=="orders" && $layout=="payment"){
					$payment = $_result[4];
					$mode = $_result[5];
				}
				if($view=="orders" && $layout=="delivery"){
					$delivery = $_result[4];
					$mode = $_result[5];
				}
				break;
			default:
				$page404=true;
				break;
					
		}
		$_vars = array();
		if ($module) $_vars['module']=$module;
		if ($view) $_vars['view']=$view;
		if ($layout) $_vars['layout']=$layout;
		if ($psid) $_vars['psid']=$psid;
		if ($ggr_id) $_vars['ggr_id']=$ggr_id;
		if ($alias) $_vars['alias']=$alias;
		if ($page404) $_vars['page404'] = $page404;
		// Callbacks for payments and deliveries
		if ($payment) $_vars['payment'] = $payment;
		if ($delivery) $_vars['delivery'] = $delivery;
		if ($mode) $_vars['mode'] = $mode;
		return $_vars;
	}
	public function buildRoute($options, $mode="sitelink", $force_fronte=0, $absolute_link=0, $force_protocol=0) {
		$appendix=""; 
		$url = $options['module']; unset($options['module']);
		$alias = (array_key_exists('alias', $options) ? $options['alias'] : "");
		$view = (array_key_exists('view', $options) ? $view=$options['view'] : "");
		$layout = (array_key_exists('layout', $options) ? $options['layout'] : "");
		if ($alias && $view=="goods" && $layout == "info"){ // это товар
			$group_alias = $this->getMainGroupAlias($options, $alias);
			if(!$group_alias) $group_alias = "info";
		} else $group_alias = "";
		if ($alias && $view=="goods" && $layout == "info" && $group_alias){
			$url.= "/".$group_alias;
			$url.= "/".$alias;
			$url.=".html";
			unset($options['view']);
			unset($options['layout']);
			unset($options['psid']);
			unset($options['alias']);
			unset($options['ggr_id']);
			unset($options['ggr_alias']);
		} elseif ($alias && $view=="goods" && $layout != "info"){
			$url.= "/".$alias."/";
			unset($options['view']);
			unset($options['layout']);
			unset($options['psid']);
			unset($options['alias']);
		} elseif ($alias && ($view=="manufacturers" || $view=="vendors")){
			$url.= "/".$options['view'];
			$url.= "/".$alias;
			$url.= "/".$layout.".html";
			unset($options['view']);
			unset($options['layout']);
			unset($options['psid']);
			unset($options['alias']);
		} else {
			if (array_key_exists('alias', $options)) unset($options['alias']);
			if (array_key_exists('view', $options) && !$options['view']) unset($options['view']);
			if (array_key_exists('view', $options) && $options['view']) {
				$url_1 = "";
				if (array_key_exists('layout', $options) && $options['layout']) {
					if($options['layout'] != "default") $url_1.= "/".$options['layout']; 
					unset($options['layout']);
				}
				if (array_key_exists('psid', $options)) {
					if($options['psid']) $url_1.= "/".$options['psid']; 
					unset($options['psid']);
				}
				if (array_key_exists('payment', $options)) {
					if($options['payment']) $url_1.= "/".$options['payment'];
					unset($options['payment']);
					if (array_key_exists('mode', $options)) {
						if($options['mode']) $url_1.= "/".$options['mode'];
						unset($options['mode']);
					}
				}
				if (array_key_exists('delivery', $options)) {
					if($options['delivery']) $url_1.= "/".$options['delivery'];
					unset($options['delivery']);
					if (array_key_exists('mode', $options)) {
						if($options['mode']) $url_1.= "/".$options['mode'];
						unset($options['mode']);
					}
				}
				if($url_1 || $options['view'] !="goods") {
					$url.= "/".$options['view'].$url_1;
				} 
				unset($options['view']);
			}
			$url.=".html";
		}
		// Let's clean default layout
		if (array_key_exists('layout', $options) && $options['layout'] == "default") {
			unset($options['layout']);
		}
		// Let's clean default controller
		if (array_key_exists('controller', $options) && $options['controller'] == "default") {
			unset($options['controller']);
		}
		
		if (count($options)>0) {
			foreach($options as $key=>$val) {	$appendix.="&".$key."=".$val;	}
		}
		$url=Portal::getURI($force_fronte, $absolute_link, $force_protocol).$url;
		if ($appendix) $url=$url."?".substr($appendix,1);
		return $url;
	}
	public function getMainGroupAlias($options, $alias){
		$ggr_alias = (array_key_exists('ggr_alias', $options) ? $options['ggr_alias'] : "");
		if($ggr_alias) return $ggr_alias;
		
		$helper = Module::getHelper("goods","catalog",true);
		$ggr_id = (array_key_exists('ggr_id', $options) ? intval($options['ggr_id']) : 0);
		if($ggr_id) $ggr_alias=$helper->getAliasByGroupId($ggr_id);
		if($ggr_alias) return $ggr_alias;
		
		if(!isset($options['psid']) || !$options['psid']) $psid = $helper->getGoodsIdByAlias($alias);
		else $psid = $options['psid'];
		$ggr_alias=$helper->getMainGroupAliasByGoodsId($psid);
		return $ggr_alias;
	}
	public function getAlias($_vars){
		// используется например в плагине id2alias
		$alias='';
		$helper=Module::getHelper("goods","catalog",true);
		if($_vars['psid']){
			if(isset($_vars['layout'])){ // товар
				$alias=$helper->getAliasByGoodsId($_vars['psid']);
			} else {  // группа
				$alias=$helper->getAliasByGroupId($_vars['psid']);
			}
		}
		return $alias;
	}
}
?>