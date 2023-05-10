<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogControllerdefault extends SpravController {
	private $memory_limit=192;
	private $max_execution_time=120;
	public function __construct($name, $module){
		parent::__construct($name, $module);
		Text::parseCustom("catalog.model.exchange1c");
	}
	public function ajaxgetSKU() {
		$psid = $this->getPsid();
		$model=$this->getModel();
		echo $model->getSKUPath($psid);
	}
	public function ajaxgetGoodsInfo() {
		$psid = $this->getPsid();
		$model = $this->getModel('goods');
		$result=$model->getElement($psid);
		echo json_encode($result);
	}
	public function showExport()  {
		if(intval(adminConfig::$adminMemoryLimit) < $this->memory_limit) ini_set('memory_limit', $this->memory_limit."M");
		if(intval(adminConfig::$adminTimeLimit) < $this->max_execution_time) ini_set('max_execution_time', $this->max_execution_time);
		$hidden_fields=array("g_id", "g_deleted", "g_thumb", "g_medium_image", "g_thumb");
		$mdl = Module::getInstance();
		$model = $this->getModel();
		$view = $this->getView();
		$this->checkACL("view".ucfirst($mdl->getName()).ucfirst($view->getName()));
		$step=Request::getInt('step',0);
		if ($step){
			$res=$model->proceedExport($hidden_fields);
			$view->assign("res", Router::_("index.php?module=catalog&task=downloadResultCSV&filename=".$res));
		} else {
			$fields=$model->getFields();
			$helper=$this->getHelper("groupsTree");
			$helper->buildTreeArrays( "", 0, 1, 0);
			$tree=$helper->getTreeHTML(0, "ul", "export_ggr");
			$view->assign("tree", $tree );
			$view->assign("hidden_fields", $hidden_fields );
			$view->assign("fields", $fields );
			$view->setLayout('settings');
		}
	}
	public function showImport()  {
		if(intval(adminConfig::$adminMemoryLimit) < $this->memory_limit) ini_set('memory_limit', $this->memory_limit."M");
		if(intval(adminConfig::$adminTimeLimit) < $this->max_execution_time) ini_set('max_execution_time', $this->max_execution_time);
		$mdl = Module::getInstance();
		$model = $this->getModel();
		$view = $this->getView();
		$this->checkACL("view".ucfirst($mdl->getName()).ucfirst($view->getName()));
		$hidden_fields=array("g_id", "g_deleted", "g_change_date", "g_change_uid", "g_enabled");
		$def_fields = array(
				"g_type"=>1,
				"g_measure"=>catalogConfig::$default_measure,
				"g_pack_measure"=>catalogConfig::$default_measure,
				"g_pack_koeff"=>1,
				"g_vmeasure"=>catalogConfig::$default_vol_measure,
				"g_size_measure"=>catalogConfig::$default_size_measure,
				"g_wmeasure"=>catalogConfig::$default_wmeasure,
				"g_currency"=>catalogConfig::$default_currency,
				"g_selltype"=>0,
				"g_tax"=>catalogConfig::$default_order_taxes,
				"g_vendor"=>catalogConfig::$default_vendor,
				"g_manufacturer"=>catalogConfig::$default_manufacturer
				);
		$step=Request::getInt('step',0);
		$codepage=Request::getSafe('codepage',"");
		if ($codepage=="windows-1251") $convert=true; else $convert=false;
		switch ($step){
			case 1:
				$count_records=0;
				/* НЕ УБИВАТЬ !!! ОТРЕМИТЬ ПЕРЕД РАБОТОЙ !!! */
				$model->resetSessionVars();
				$model->cleanTempTable();
				$result=Files::uploadDataFile("CSV","filecsv", "import", PATH_TMP, true, $convert);
				if ($result) $count_records = $model->importTempTable($result["file"]);
				$fields=$model->getFields($hidden_fields);
				$fields_data=$model->getFieldsData($def_fields);
				$add_fields_w_lists=$model->getAddFieldsWLists($hidden_fields);
				$groupsTree=$this->getHelper("groupsTree");
				$view->assign("groupsTree", $groupsTree);
				$view->assign("fields", $fields);
				$view->assign("hidden_fields", $hidden_fields);
				$view->assign("fields_data", $fields_data);
				$view->assign("af_wlists", $add_fields_w_lists);
				$view->assign("count_records", $count_records);
				$view->setLayout('settings');
			break;
			case 2:
				// сложим всё в сессию
				$model->setImportVars(array_keys($def_fields));
				$view->setLayout('confirm');
			break;
			case 3: 
				$view->setLayout('results');
			break;
			case 4:
				Portal::getInstance()->disableTemplate();
				$row2start=Request::getInt("row2start");
				$res=$model->proceedImport($row2start, $hidden_fields, array_keys($def_fields));
				echo json_encode($res);
				Util::halt();
			break;
			default:
				$view->setLayout('fileselector');
			break;
		}
	}
	public function ajaxviewOrder() {
		$this->printOrders();
	}
	public function printOrders() {
		Portal::getInstance()->changeTemplateFile('print');
		$mdl = Module::getInstance();
		$moduleName	= $mdl->getName();
		$reestr = $mdl->get('reestr');
		$model = $this->getModel();
		$view = $this->getView();
		$viewname = $view->getName();
		$arr_psid     = Request::get('cps_id', false);				// массив отмеченных галочкой элементов
		if(!$arr_psid) $arr_psid=$this->getPsid();
		$multy_code   = Request::getInt('multy_code', false); 		// ид верхней группы
		$reestr->set('view',$viewname);
		$reestr->set('model',$model);
		$reestr->set("multy_code",$multy_code);
		$reestr->set('arr_psid',$arr_psid);

		$orders=$model->getOrders();
		if ($orders) {
			$orders=$model->decodeOrdersData($orders);
			$orders_items=$model->getOrdersItems();
		}
		else {$orders=false;$orders_items=false;}

		$view->setLayout('print');
		$view->assign("orders", $orders );
		$view->assign("orders_items", $orders_items );
		$view->render();
	}
	function showGoodsgroup() {
		$this->showData();
	}
	public function showGoods() {
		$this->showData('ggr_name','goodsgroup');
	}
	function showCurrency() {
		$this->showData();
	}
	function showCurrency_rate(){
		$this->showData('c_name','currency');
	}
	public function showImages() {
		$this->showData('g_name','goods');
	}
	public function showVideos() {
		$this->showData('g_name','goods');
	}
	public function showUsers() {
		$this->showData();
	}
	public function showGoodsprices() {
		$this->showData('g_name','goods');
	}
	public function showVendors() {
		$this->showData('vc_name','vendor_categories');
	}
	public function showVendor_cats() {
		$this->showData();
	}
	public function showManufacturers() {
		$this->showData('mfc_name','manufacturer_categories');
	}
	public function showManufacturer_cats() {
		$this->showData();
	}
	public function showOrders() {
		switch ($this->get('layout','')) 	{
			case "order":
				$this->showData("o_id","orders");
				break;
			default:
				$this->showOrdersData();
				break;
		}
	}
	public function showOrdersData() {
		$viewname 	= $this->getView()->getName();
		$mdl = Module::getInstance();
		$this->checkACL("view".ucfirst($mdl->getName()).ucfirst($viewname));
		$reestr = $mdl->get('reestr');
		$model = $this->getModel('orders');
		$model->loadMeta();

		$multy_code	= Request::getInt('multy_code', false); 		// ид верхней группы
		$psid		= $this->getPsid();
		if(!$multy_code)	$multy_code=$psid;
		elseif(!$psid) $psid=$multy_code;
		$reestr->set('psid',$psid);
		$reestr->set('canModify',true);
		$reestr->set('page',Request::getInt("page", 1));
		$reestr->set('sort',Request::getSafe("sort"));
		$reestr->set('orderby',Request::getSafe("orderby"));
		$reestr->set("multy_code",$multy_code);

		$trash=Request::getInt('trash',0);
		$reestr->set("trash",$trash);
		$view = $this->getView();
		Request::getSafe('option')=="ajax" ? $is_ajax=1 : $is_ajax=0;
		$reestr->set("is_ajax",$is_ajax);
		$result = $model->getData();
		$result=$model->decodeOrdersData($result);

		$view->renderSprav($model->meta,$result);
	}
	public function showDeliverytypes() {
		$reestr = Module::getInstance()->get('reestr');
		$reestr->set('consider_parents',false);
		$this->showData();
	}
	public function showPaymenttypes() {
		$this->showData();
	}
	public function showMeasures() {
		$this->showData();
	}
	public function showTaxes() {
		$this->showData();
	}

	public function modifyElement($ajaxModify=false, $clone_mode=false){
		$mdl		= Module::getInstance();
		$reestr 	= $mdl->get('reestr');
		$view 		= $this->getView();
		$model 		= $this->getModel();
		$viewname 	= $view->getName();
		switch($viewname){
			case "goods":
				$multy_code   = Request::getInt('multy_code', false); 		// ид верхней группы
				$psid = $this->getPsid();
				$links=$model->getLinkArray($psid);
				if(count($links)==0) {
					$prow=$model->getParentElement($multy_code);
					if(isset($prow->{$model->meta->parent_name})) $links=array(0=>array('id'=>$multy_code,'title'=>$prow->{$model->meta->parent_name}));
				}
				$groups=$this->getModule()->getHelper('groupsTree');
				$parents=$groups->getWholeTreeUp($links);
				$fbgList=$model->getGroupsFields(array_keys($parents));
				foreach($model->meta->is_add as $key=>$is_add){
					if ($is_add){
						if ($model->meta->is_add_custom[$key]==1){
							if (!array_key_exists($is_add, $fbgList)) {
								$model->meta->input_view[$key]=0;
							}
						}
					}
				}
				$analogList=$model->getAnalogs($psid);
				$view->assign('analogList',$analogList);
				
				$additionalList=$model->getAdditionals($psid);
				$view->assign('additionalList',$additionalList);
				
				$discountsList=$model->getDiscounts($psid);
				$view->assign('discountsList',$discountsList);
				$complectList=$model->getComplectSet($psid);
				$view->assign('complectList',$complectList);
				if(!$psid && $multy_code){
					$model->loadMeta();
					$model->meta->updateArrayField("default_value", "g_main_grp", $multy_code);
				}
				parent::modifyElement($ajaxModify, $clone_mode);
			break;
			case "goodsgroup":
				$multy_code   = Request::getInt('multy_code', false); 		// ид верхней группы
				$psid = $this->getPsid();
				$group_fields=$model->getGroupFields($psid);
				$view->assign('group_fields',$group_fields);
				parent::modifyElement($ajaxModify, $clone_mode);
			break;
			case "discounts":
				$multy_code   = Request::getInt('multy_code', false); 		// ид верхней группы
				$psid = $this->getPsid();				$goodsList=$model->getGoods($psid);
				$view->assign('goodsList',$goodsList);
				parent::modifyElement($ajaxModify, $clone_mode);
				break;
			default:
				parent::modifyElement($ajaxModify, $clone_mode);
			break;
		}
	}
	public function save() {
		$mdl				= Module::getInstance();
		$moduleName	= $mdl->getName();
		$reestr 		= $mdl->get('reestr');
		$model 			= $this->getModel();
		$viewname 	= $this->getView()->getName();
		$model->loadMeta();
		if($model->meta->use_view_rights){
			if (defined("_ADMIN_MODE")) $this->checkACL("view".ucfirst($mdl->getName()).ucfirst($model->meta->use_view_rights));
			else $this->checkACL("modify".ucfirst($mdl->getName()).ucfirst($model->meta->use_view_rights));
		} else {
			if (defined("_ADMIN_MODE")) $this->checkACL("view".ucfirst($mdl->getName()).ucfirst($viewname));
			else $this->checkACL("modify".ucfirst($mdl->getName()).ucfirst($viewname));
		}
		$psid			= $this->getPsid();
		$multy_code		= Request::getSafe('multy_code', 0);
		$layout			= Request::getSafe('layout');
		$page			= Request::getInt('page', 1);
		$sort			= Request::getSafe('sort');
		$orderby		= Request::getSafe('orderby');
		$is_apply		= Request::getSafe('apply');
		$is_add_clone	= Request::getSafe('add_clone',0);
		$is_add_new		= Request::getSafe('add_new',0);
		$reestr->set('multy_code',$multy_code);
		$reestr->set('view',$viewname);
		$reestr->set('psid',$psid);
		$reverse=$this->getConfigVal('reverse_analog_link');
		$new_psid=$model->save();
		if($new_psid) {
			$model->garbageCollector($new_psid);
			$msg=Text::_("Save successfull");
		} else { $msg=Text::_("Save unsuccessfull"); $new_psid=$psid; }
		switch($viewname){
			case "goods":
				if($new_psid) {
					$group = $this->getModel('goodsgroup');
					$links=$model->getLinkArray($new_psid);
					$groups=$this->getModule()->getHelper('groupsTree');
					$parents=$groups->getWholeTreeUp($links);
					$model->cleanNonGroupsFields($new_psid, array_keys($parents));
					$analogs=Request::get("analogEditor",array());
					if (!$model->saveAnalogs($new_psid,$analogs,$reverse)) $msg.="<br />".Text::_("Save analogs unsuccessfull");
					$additionals=Request::get("additionalEditor",array());
					if (!$model->saveAdditionals($new_psid,$additionals)) $msg.="<br />".Text::_("Save analogs unsuccessfull");
						
					$discounts=Request::get("discountEditor",array());
					if (!$model->saveDiscounts($new_psid,$discounts)) $msg.="<br />".Text::_("Save discounts unsuccessfull");
					$g_type	= Request::getInt('g_type',0);
					$complectsets=array();
					if ($g_type==5) { // это комплект
						$complect_ids=Request::get("complectEditor",array());
						$complect_quantities=Request::get("complectEditor_quantity",array());
						if (count($complect_ids)){
							foreach($complect_ids as $complect_gid){
								if (array_key_exists($complect_gid, $complect_quantities)){
									$cquantity=$complect_quantities[$complect_gid];
									if ($cquantity) $complectsets[$complect_gid]=$cquantity;
								}
							}
						}
					}
					if (!$model->saveComplectSet($new_psid, $complectsets)) {
						$msg.="<br />".Text::_("Save complectset unsuccessfull");
					} else {
						if(catalogConfig::$complectPriceAsGoodsSum && $g_type==5 && count($complectsets)){
							if (!$model->updatePricesFromComplect($new_psid)) {
								$msg.="<br />".Text::_("Update prices from complectset unsuccessfull");
							}
						}
					}
					$name	= Request::get('g_name',"");
					$alias	= Request::get('g_alias',"");
					$model->updateAlias($new_psid, $alias, $name);
				}
			break;
			case "goodsgroup":
					$name	= Request::get('ggr_name',"");
					$alias	= Request::get('ggr_alias',"");
					if($new_psid) {
						$fields = Request::get('group_field', array());
						$model->saveGroupFields($new_psid,$fields);
						$model->updateAlias($new_psid,$alias,$name);
					}
			break;
			case "discounts":
				if($new_psid) {
					$goods=Request::get("goodsEditor",array());
					if (!$model->saveGoods($new_psid,$goods)) $msg.="<br />".Text::_("Save goods unsuccessfull");
				}
			break;
			case "goodsprices":
				if($new_psid) {
					$model->updatePriceChangerInfo($new_psid);
				}
			break;
			case "manufacturers":
				if($new_psid) {
					$name	= Request::get('mf_name',"");
					$alias	= Request::get('mf_alias',"");
					$model->updateAlias($new_psid, $alias, $name);
				}
			break;
			case "vendors":
				if($new_psid) {
					$name	= Request::get('v_name',"");
					$alias	= Request::get('v_alias',"");
					$model->updateAlias($new_psid, $alias, $name);
				}
				break;
			default:
			break;
		}
		$activeTab=Request::getInt("activeTab",1);
		if ($is_apply) $url='index.php?module='.$moduleName.'&view='.$viewname.'&layout='.$layout.'&task=modify&psid='.$new_psid.'&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.$multy_code.'&activeTab='.$activeTab;
		elseif($is_add_clone) $url='index.php?module='.$moduleName.'&view='.$viewname.'&layout='.$layout.'&task=make_clone&psid='.$new_psid.'&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.$multy_code;
		elseif($is_add_new) $url='index.php?module='.$moduleName.'&view='.$viewname.'&layout='.$layout.'&task=modify&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.$multy_code;
		else $url='index.php?module='.$moduleName.'&view='.$viewname.'&layout='.$layout.'&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.$multy_code;
		$this->setRedirect($url,$msg);
	}
	public function newDTS() {
		$this->checkACL("viewCatalogDeliverytypes");
		$model = Module::getInstance()->getModel();
		$view = $this->getView();
		$templates=$model->getTemplates();
		$view->assign('selector',HTMLControls::renderSelect('tmpl_name','tmpl_name', 'filename', 'filename', $templates,0,0));
		$view->set('layout', 'new', true);
		$view->render();
	}
	public function saveNewDTS() {
		$this->checkACL("viewCatalogDeliverytypes");
		$tmpl_name = Request::getSafe("tmpl_name","");
		if (!$tmpl_name) $this->newDTS();
		else {
			$model = Module::getInstance()->getModel();
			$psid=$model->saveNewDTS($tmpl_name);
			if($psid) {
				$this->setRedirect("index.php?module=catalog&view=deliverytypes&task=modifyDTS&psid=".$psid);
			}	else {
				$this->setRedirect("index.php?module=catalog&view=deliverytypes","Save unsuccessfull");
			}
		}
	}
	public function newPTS() {
		$this->checkACL("viewCatalogPaymenttypes");
		$model = Module::getInstance()->getModel();
		$view = $this->getView();
		$templates=$model->getTemplates();
		$view->assign('selector',HTMLControls::renderSelect('tmpl_name','tmpl_name', 'filename', 'filename', $templates,0,0));
		$view->set('layout', 'new', true);
		$view->render();
	}
	public function saveNewPTS() {
		$this->checkACL("viewCatalogPaymenttypes");
		$tmpl_name = Request::getSafe("tmpl_name","");
		if (!$tmpl_name) $this->newPTS();
		else {
			$model = Module::getInstance()->getModel();
			$psid=$model->saveNewPTS($tmpl_name);
			if($psid) {
				$this->setRedirect("index.php?module=catalog&view=paymenttypes&task=modifyPTS&psid=".$psid);
			}	else {
				$this->setRedirect("index.php?module=catalog&view=paymenttypes","Save unsuccessfull");
			}
		}
	}
	public function cloneDTS() {
		$this->checkACL("viewCatalogDeliverytypes");
		$mdl = Module::getInstance();
		$reestr = $mdl->get('reestr');
		$model = $this->getModel();
		$psid = $this->getPsid();
		$reestr->set('task','saveDTS');
		$reestr->set('view','deliverytypes');
		$page = Request::getInt('page', 1);
		$reestr->set('page', $page);
		$sort = Request::getSafe('sort');
		$reestr->set('sort', $sort);
		$orderby = Request::getSafe('orderby');
		$reestr->set('orderby', $orderby);
		$reestr->set('model',$model);
		$reestr->set('linkArray',$model->getLinkArray($psid));
		// $elem = $model->getElement();
		$elem = $model->getElementClone($psid);
		$reestr->set('psid',0);
		$reestr->set('clone_mode',1);
		$view = $this->getView();
		$reestr->set("onCancelURL","index.php?module=catalog&view=deliverytypes".'&sort='.$sort.'&page='.$page.'&orderby='.$orderby);
		$params = Params::parse($elem->dt_params);
		$def_params = $model->getParamsMask($elem);
		$view->assign("params",$params);
		$view->assign("def_params",$def_params);
		$view->modify($elem);
	}
	public function modifyDTS() {
		$mdl = Module::getInstance();
		$reestr = $mdl->get('reestr');
		$model = $this->getModel();
		$psid = $this->getPsid();
		$reestr->set('psid',$psid);
		$reestr->set('task','saveDTS');
		$reestr->set('view','deliverytypes');
		$page = Request::getInt('page', 1);
		$reestr->set('page', $page);
		$sort = Request::getSafe('sort');
		$reestr->set('sort', $sort);
		$orderby = Request::getSafe('orderby');
		$reestr->set('orderby', $orderby);
		$reestr->set('model',$model);
		$reestr->set('clone_mode',0);
		$elem = $model->getElement();
		$view = $this->getView();
		$reestr->set("onCancelURL","index.php?module=catalog&view=deliverytypes".'&sort='.$sort.'&page='.$page.'&orderby='.$orderby);
		$params = Params::parse($elem->dt_params);
		$def_params = $model->getParamsMask($elem);
		$view->assign("params",$params);
		$view->assign("def_params",$def_params);
		$view->modify($elem);
	}
	public function saveDTS() {
		$mdl = Module::getInstance();
		$moduleName	= $mdl->getName();
		$reestr = $mdl->get('reestr');
		$psid      = Request::get('psid', false);
		$multy_code = Request::get('multy_code',0);
		$layout		= Request::get('layout');
		$page		= Request::getInt('page', 1);
		$sort		= Request::get('sort');
		$orderby	= Request::get('orderby');
		$is_apply	= Request::getSafe('apply');
		$is_add_clone	= Request::getSafe('add_clone',0);

		$model = $this->getModel();
		$view = $this->getView();
		$viewname = $view->getName();

		$reestr->set('multy_code',$multy_code);
		$reestr->set('view',$viewname);
		$reestr->set('psid',$psid);
		
		$params = Request::get("dt_params",array());
		$_REQUEST["dt_params"]=""; // обнулили для клонирования

		$new_psid = $model->save();
		if($new_psid) {
			$elem = $model->getElement($new_psid);
			$def_params = $model->getParamsMask($elem);
			if($model->saveParams($new_psid,$def_params,$params)) {
				$msg = Text::_("Save successfull");
			} else {
				$msg = Text::_("Save unsuccessfull");
			}
			$new_psid = urlencode($new_psid);
		}
		else {
			$new_psid = urlencode($psid); $msg = Text::_("Save unsuccessfull");
		}

		if ($is_apply) $url='index.php?module='.$moduleName.'&view='.$viewname.'&layout='.$layout.'&task=modifyDTS&psid='.$new_psid.'&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.$multy_code;
		else $url='index.php?module='.$moduleName.'&view='.$viewname.'&layout='.$layout.'&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.$multy_code;
		$this->setRedirect($url,$msg);
	}
	public function modifyPTS() {
		$mdl = Module::getInstance();
		$reestr = $mdl->get('reestr');
		$model = $this->getModel();
		$psid = $this->getPsid();
		$reestr->set('psid',$psid);
		$reestr->set('task','savePTS');
		$reestr->set('view','paymenttypes');
		$page = Request::getInt('page', 1);
		$reestr->set('page', $page);
		$sort = Request::getSafe('sort');
		$reestr->set('sort', $sort);
		$orderby = Request::getSafe('orderby');
		$reestr->set('orderby', $orderby);
		$reestr->set('model',$model);
		$elem = $model->getElement();
		$view = $this->getView();
		$reestr->set("onCancelURL","index.php?module=catalog&view=paymenttypes".'&sort='.$sort.'&page='.$page.'&orderby='.$orderby);
		$params = Params::parse($elem->pt_params);
		$def_params = $model->getParamsMask($elem);
		$view->assign("params",$params);
		$view->assign("def_params",$def_params);
		$view->modify($elem);
	}
	public function saveGoodsgroup() {
		$model = $this->getModel();
		$new_psid = $model->save();
		if($new_psid) {

		}	else {
			$new_psid = urlencode($psid); $msg = Text::_("Save unsuccessfull");
		}

		if ($is_apply) $url='index.php?module='.$moduleName.'&view='.$viewname.'&layout='.$layout.'&task=modifyPTS&psid='.$new_psid.'&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.$multy_code;
		else $url='index.php?module='.$moduleName.'&view='.$viewname.'&layout='.$layout.'&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.$multy_code;
		$this->setRedirect($url,$msg);
	}
	public function savePTS() {
		$mdl = Module::getInstance();
		$moduleName	= $mdl->getName();
		$reestr = $mdl->get('reestr');

		$psid		= $this->getPsid();
		$multy_code = Request::getInt('multy_code',0);
		$layout		= Request::getSafe('layout');
		$page		= Request::getInt('page', 1);
		$sort		= Request::getSafe('sort');
		$orderby	= Request::getSafe('orderby');
		$is_apply	= Request::getSafe('apply');

		$model = $this->getModel();
		$view = $this->getView();
		$viewname = $view->getName();

		$reestr->set('multy_code',$multy_code);
		$reestr->set('view',$viewname);
		$reestr->set('psid',$psid);

		$new_psid = $model->save();
		if($new_psid) {
			$params = Request::get("pt_params",array());
			$elem = $model->getElement($new_psid);
			$def_params = $model->getParamsMask($elem);
			if($model->saveParams($new_psid,$def_params,$params)) {
				$msg = Text::_("Save successfull");
			} else {
				$msg = Text::_("Save unsuccessfull");
			}
			$new_psid = urlencode($new_psid);
		}
		else {
			$new_psid = urlencode($psid); $msg = Text::_("Save unsuccessfull");
		}

		if ($is_apply) $url='index.php?module='.$moduleName.'&view='.$viewname.'&layout='.$layout.'&task=modifyPTS&psid='.$new_psid.'&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.$multy_code;
		else $url='index.php?module='.$moduleName.'&view='.$viewname.'&layout='.$layout.'&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.$multy_code;
		$this->setRedirect($url,$msg);
	}
	public function showPrice() {
		$view=$this->getView();
		$model=$this->getModel();
		switch ($this->get('layout','')) 	{
			case "list":
				$pr_layout=Request::getSafe("p_template","");
				if($pr_layout&&$pr_layout!='default')
				{
					$this->set("layout",$pr_layout,true);
				}				
				$reestr = Module::getInstance()->get('reestr');
				$reestr->set('needpan',false);
				$params["parent_group"]=Request::getInt("parent_group",0);
				$params["break_by_groups"]=Request::getInt("break_by_groups",0);
				$params["show_pack_price"]=Request::getInt("show_pack_price",0);
				$params["show_weight_price"]=Request::getInt("show_weight_price",0);
				$params["show_volume_price"]=Request::getInt("show_volume_price",0);
				$params["enabled_only"]=Request::getInt("enabled_only",0);
				$params["show_company_info"]=Request::getInt("show_company_info",0);
				$params["price_type"]=Request::getInt("price_type",1);
				$params["show_thumbs"]=Request::getInt("show_thumbs",0);
				$params["show_dimensions"]=Request::getInt("show_dimensions",0);
				$params["show_weight"]=Request::getInt("show_weight",0);
				$params["add_header"]=Request::getSafe("p_head_colon","");
				$params["add_footer"]=Request::getSafe("p_foot_colon","");
				$params["discount"]=Request::getSafe("p_discount","");

				$mainGrp=$model->getGroupTitle($params);
				$view->assign("mainGrp",$mainGrp);
				if($params["break_by_groups"]){
					$grpArr=$model->getTreeArr($params);
					$view->assign("grpArr",$grpArr);
				}
				$view->assign("params",$params);
				break;
			default:
				$grpTree=$this->getModule()->getHelper('groupsTree');
				$listsets=$model->getPriceSetsNames();
				$view->assign("listsets",$listsets);
				$view->assign("grpTree",$grpTree);
				break;
		}
	}
	public function ajaxsavePriceSettings(){
		$model=$this->getModel("price");
		if(!Request::getBool("p_new"))  $data['p_id']=Request::getInt('p_id');
		else $data['p_id']=0; 
		$data['p_name']=Request::getSafe('p_name');
		$data['p_head_colon']=Request::getSafe('p_head_colon');
		$data['p_foot_colon']=Request::getSafe('p_foot_colon');
		$data['p_checkbox']=Request::getSafe('p_checkbox');
		$data['p_price']=Request::getInt('p_price');
		$data['p_discont']=Request::getFloat('p_price');
		$data['p_comment']=Request::getSafe('p_comment');
		$data['p_template']=Request::getSafe('p_template');
		if($model->savePriceset($data)) return 'OK';
		else return 'False saving';
	}
	public function ajaxgetPriceSettings()	{
		$model=$this->getModel("price");
		$p_id=Request::getInt('p_id');
		$result='';
		
		if($p_id) {
			$inf=$model->getPriceSetsID($p_id);
			foreach (json_decode(htmlspecialchars_decode($inf->p_checkbox)) as $key=>$val)
			{
				$arrck[$key]=html_entity_decode($val);
			}		
			
			$inf->p_checkbox=json_encode($arrck);
			$result=json_encode($inf);
		}
		echo $result;
	}
	public function showGoods_stat() {
		$this->showData();
	}
	public function showDiscounts() {
		$this->showData();
	}
	public function showOptionvals() {
		$this->showData();
	}
	public function modifyOptionval() {
		Module::getInstance()->get('reestr')->set('task','saveOptionval');
		parent::modify();
	}
	public function saveOptionval(){
		parent::save();
	}
	public function showOptions() {
		$this->showData();
	}
	public function modifyOption() {
		Module::getInstance()->get('reestr')->set('task','saveOption');
		parent::modify();
	}
	public function saveOption(){
		parent::save();
	}
	public function showOptions_data() {
		$this->showData('g_name','goods');
	}
	public function modifyOption_data() {
		Module::getInstance()->get('reestr')->set('task','saveOption_data');
		parent::modify();
	}
	public function saveOption_data(){
		parent::save();
	}
	public function showOptionvals_data() {
		$this->showData();
	}
	public function modifyOptionvals_data() {
		$reestr = Module::getInstance()->get('reestr');
		$psid   = $this->getPsid();
		$multy_code   = Request::getInt('multy_code', 0);
		$opt_vals_parent = $this->getModel()->getOptValsParent($multy_code); 
		$reestr->set("opt_vals_parent", $opt_vals_parent);
		$reestr->set('psid', $psid);
		$reestr->set('task','saveOptionval_data');
		parent::modify();
	}
	public function saveOptionval_data(){
		parent::save();
	}
	public function downloadFile(){
		$folder=Request::getSafe("folder","");
		$file=Request::getSafe("filename","");
		$absolute=Request::getInt("abs",0);
		if ($this->getModule() && $file){
			$filepath=BARMAZ_UF_PATH;
			if (!$absolute)  $filepath.=$this->getModule()->getName().DS; 
			if ($folder) $filepath.=Util::dsPath($folder).DS; 
			Util::download($filepath, $file, Util::getRefererUrl(false));
		}
	}
	public function downloadResultCSV() {
		$this->set("view", "export", true);
		$this->checkACL("view".ucfirst(Module::getInstance()->getName()).ucfirst($this->getView()->getName()));
		$file=Request::getSafe("filename","");
		if($file){
			Util::download(PATH_TMP, $file);
		} else {
			$this->setRedirect(Router::_("index.php?module=catalog&view=export"), Text::_("File absent"));
		}
	}
	/**
	 * полное удаление всей базы товаров и групп
	 * используется для ускорения в исключительных случаях
	 * копирование системы от другого клиента с другой товарной матрицей
	 * очстики демоданных системы
	 * возможно, конечно этому место в сервисе
	 * явное управление через админку не предусматривается во избежание проблем
	 * запускается только ручным набором - имя специально усложнено, чтобы случайно нельяз было набрать
	 *
	 */
	/*
	// *********************************************************************************
	//@FIXME Пока заремил. Слишком опасно.
	// Решается выделением всех товаров, в фильтре без учета родителей, и их удалением.
	// С группами аналогично.
	// Если делать очистку то через сервис, с проверкой прав и на все модули.
	// *********************************************************************************
	public function clear42Goods42base42() {
		$m_goods=$this->getModel('goods');
		$m_ggr=$this->getModel('goodsgroup');
		$db=Database::getInstance();
		$sql_g="delete from #__goods";
		$sql_ggr="delete from #__goods_group";
		
		$db->setQuery($sql_g);
		$db->query();
		
		$db->setQuery($sql_ggr);
		$db->query();
		
		$result_g=$m_goods->cleanLinkFromGoods();
		$result_ggr=$m_ggr->cleanLinkFromGroup();
		echo 'clear catalog base done';
	}
	*/
	/********************************** EXCHANGE WITH 1C START ***********************************/
	public function echoMessage($status=null, $message=null, $halt=true){
		while (ob_get_level()) { ob_end_clean(); }	// clear output buffer
		if(siteConfig::$debugMode) ob_start();
		if(!is_null($status)){
			if($status===true) echo "success\n";
			elseif($status===false) echo "failure\n";
			elseif($status===2) echo "progress\n"; // @TODO For future purposes
		}
		if(!is_null($message)) {
			if(is_array($message) && count($message)) $message = implode("\n", $message);
			if($message) {
				if($this->getModule()->getParam("1c_messages_cp1251")) $message = mb_convert_encoding($message, "CP1251", DEF_CP);
				echo $message."\n";
			}
		}
		if(siteConfig::$debugMode) {
			$output = ob_get_contents();
			ob_end_clean();
			echo $output;
			Util::logFile($output, "Answer for TS (echoMessage".($this->getModule()->getParam("1c_messages_cp1251") ? ", CP1251" : "")."): ");
		}
		if($halt) Util::halt();
	}
	public function autoexchange1c() {
		if(siteConfig::$debugMode) Util::logFile("In controller : ".Request::getSafe("type").":".Request::getSafe("mode"));
		if(intval(adminConfig::$adminMemoryLimit) < $this->memory_limit) ini_set('memory_limit', $this->memory_limit."M");
		if(intval(adminConfig::$adminTimeLimit) < $this->max_execution_time) ini_set('max_execution_time', $this->max_execution_time);
		$messages = array();
		if(defined("_BARMAZ_EXCHANGE") && $this->checkACL("viewCatalogExchange1c") ){
			Portal::getInstance()->disableTemplate();
			switch(Request::getSafe("type")){
				case "sale":
					switch(Request::getSafe("mode")){
						case "checkauth":
							// Autologin in Portal
							break;
						case "init":
							$result = $this->getModel("exchange1c")->initRemote($messages);
							$this->echoMessage(null, $messages);
							break;
						case "query":
							// Let's return orders.xml
							$result=true;
							// Next check is specially for those alternatively gifted boys and girls, who don't request init (For examole: 1C)
							if(Util::toBool(Session::getInstance()->getUserVar("exchange1c_init_ok"))!==true) {
								$result = $this->getModel("exchange1c")->initRemote($messages);
								$messages = array();
							}
							if($result){
								$result = $this->getModel("exchange1c")->processExport($messages, true);
								if($result && isset($result["filename"]) && $result["filename"]){
									$this->getModel("exchange1c")->processDownload($result["filename"]);
								} else {
									$this->echoMessage(false, $messages);
								}
							} else {
								$messages[]="Init failed";
								$this->echoMessage(false, $messages);
							}
							break;
						case "success":
							// Let'change order status
							$result = $this->getModel("exchange1c")->processOrdersChangeStatus($messages, true);
							$this->echoMessage($result, $messages);
							break;
						case "file":
							// Possible the same as catalog:file
							$result = $this->getModel("exchange1c")->autoUpload($messages);
							$this->echoMessage($result, $messages);
							break;
						case "import":
							// Possible the same as catalog:import
							$filename = Request::getSafe("filename");
							$result = $this->getModel("exchange1c")->autoUploadUnzip($messages); // Every time
							if($result) $result = $this->getModel("exchange1c")->processImport($filename, $messages, true);
							$this->echoMessage($result, $messages);
							break;
						default:
							$this->echoMessage(true);
							break;
					}
					break;
				case "catalog":
					switch(Request::getSafe("mode")){
						case "checkauth":
							// Autologin in Portal
							break;
						case "init":
							$result = $this->getModel("exchange1c")->initRemote($messages);
							$this->echoMessage(null, $messages);
							break;
						case "file":
							$result = $this->getModel("exchange1c")->autoUpload($messages);
							$this->echoMessage($result, $messages);
							break;
						case "import":
							$filename = Request::getSafe("filename");
							$result = $this->getModel("exchange1c")->autoUploadUnzip($messages); // Every time
							if($result) $result = $this->getModel("exchange1c")->processImport($filename, $messages, true);
							$this->echoMessage($result, $messages);
							break;
						default:
							$this->echoMessage(true);
							break;
					}
					break;
				default:
					break;
			}
		} else {
			$this->echoMessage(false, "401 Unauthorized");
		}
		Util::logFile("Response not send on ".Request::getSafe("type").":".Request::getSafe("mode"));
		Util::halt();
	}
	public function ajaxExchange1C_log()  {
		$this->set("view", "exchange1c", true);
		if(intval(adminConfig::$adminMemoryLimit) < $this->memory_limit) ini_set('memory_limit', $this->memory_limit."M");
		if(intval(adminConfig::$adminTimeLimit) < $this->max_execution_time) ini_set('max_execution_time', $this->max_execution_time);
		if($this->checkACL("view".ucfirst(Module::getInstance()->getName()).ucfirst($this->getView()->getName()), false)){
			$log = Request::getSafe("log");
			switch($log){
				case "catalog":
				case "sale":
					$filename=PATH_LOGS."exchange1c-".$log.".log";
					$this->set("layout", "log", true);
					$view = $this->getView("exchange1c");
					$view->assign("log", @file_get_contents($filename));
					$view->render();
				break;
				default:
					echo Text::_("Access denied");
				break;
			}
		} else {
			echo Text::_("Access denied");
		}
	}
	public function showExchange1c() {
		if(intval(adminConfig::$adminMemoryLimit) < $this->memory_limit) ini_set('memory_limit', $this->memory_limit."M");
		if(intval(adminConfig::$adminTimeLimit) < $this->max_execution_time) ini_set('max_execution_time', $this->max_execution_time);
		$this->checkACL("view".ucfirst(Module::getInstance()->getName()).ucfirst($this->getView()->getName())); // May be already done in parent
		$view = $this->getView();
		$success=array(1=>false, 2=>true);
		$messages=array(1=>array(), 2=>array());
		$messages[1][]=Text::_("Make backup first")."!!!";
		$start_date = Settings::getVar("1c_exchange_orders_success_date");
		if(!$start_date) $start_date = Date::fromSQL(Date::todaySQL()." 00:00:00");
		$start_date = Date::fromSQL(Date::toSQL($start_date), false, true);
		$end_timestamp = Date::mysqldatetime_to_timestamp(Date::todaySQL()) + 60*60*24;
		$end_date = Date::formatTimestamp($end_timestamp, true, false);
		$view->assign("end_date", $end_date);
		$view->assign("start_date", $start_date);
		$view->assign("success_1", $success);
		$view->assign("messages_1", $messages);
		$view->assign("activeTab", 1);
	}
	public function ajaxprocessImport1C() {
		$this->set("view", "exchange1c", true);
		if(intval(adminConfig::$adminMemoryLimit) < $this->memory_limit) ini_set('memory_limit', $this->memory_limit."M");
		if(intval(adminConfig::$adminTimeLimit) < $this->max_execution_time) ini_set('max_execution_time', $this->max_execution_time);
		$model = $this->getModel("exchange1c");
		$model->setLogfile("catalog");
		$result=array();
		$messages=array();
		$result["field_id"]="";
		if($this->checkACL("view".ucfirst(Module::getInstance()->getName()).ucfirst($this->getView()->getName()), false)){
			$filename=Request::getSafe("filename");
			$field_id=Request::getSafe("field_id");
			if($filename && $field_id){
				$res=$model->processImport($filename, $messages);
				if($res===2){
					$result["status"]="progress";
				} elseif($res){
					$result["status"]="success";
					$result["field_id"]=$field_id;
				} else {
					$result["status"]="failure";
				}
			} else {
				$result["status"]="failure";
				$messages[]=Text::_("Wrong file name");
			}
		} else {
			$result["status"]="failure";
			$messages[]=Text::_("Access denied");
		}
		$result["messages"]=$messages;
		echo json_encode($result);
	}
	public function processImport1C() {
		$this->set("view", "exchange1c", true);
		if(intval(adminConfig::$adminMemoryLimit) < $this->memory_limit) ini_set('memory_limit', $this->memory_limit."M");
		if(intval(adminConfig::$adminTimeLimit) < $this->max_execution_time) ini_set('max_execution_time', $this->max_execution_time);
		$this->checkACL("view".ucfirst(Module::getInstance()->getName()).ucfirst($this->getView()->getName()));
		$model = $this->getModel();
		$model->setLogfile("catalog");
		$model->resetVars();
		if($this->getModule()->getParam("1c_log_always_clean")) $model->clearLog();
		$view = $this->getView();
		$view->assign("activeTab", 1);
		$files=array();
		$success=array(1=>true, 2=>true);
		$messages=array(1=>array(), 2=>array());
		$success[1] = $model->manualUpload($messages[1], $files);
		// if($success[1] && count($files)){ }
		$view->assign("files_1", $files);
		$view->assign("success_1", $success);
		$view->assign("messages_1", $messages);
		
		$start_date = Settings::getVar("1c_exchange_orders_success_date");
		if(!$start_date) $start_date = Date::fromSQL(Date::todaySQL()." 00:00:00");
		$start_date = Date::fromSQL(Date::toSQL($start_date), false, true);
		$end_timestamp = Date::mysqldatetime_to_timestamp(Date::todaySQL()) + 60*60*24;
		$end_date = Date::formatTimestamp($end_timestamp, true, false);
		$view->assign("end_date", $end_date);
		$view->assign("start_date", $start_date);
		
		$view->render();
	}
	public function processExport1C() {
		$this->set("view", "exchange1c", true);
		if(intval(adminConfig::$adminMemoryLimit) < $this->memory_limit) ini_set('memory_limit', $this->memory_limit."M");
		if(intval(adminConfig::$adminTimeLimit) < $this->max_execution_time) ini_set('max_execution_time', $this->max_execution_time);
		$this->checkACL("view".ucfirst(Module::getInstance()->getName()).ucfirst($this->getView()->getName()));
		$start_date = Request::getDateTime("start_date");
		$end_date = Request::getDateTime("end_date");
		$model = $this->getModel();
		$model->setLogfile("sale");
		$model->resetVars();
		if($this->getModule()->getParam("1c_log_always_clean")) $model->clearLog();
		$success=array(1=>true, 2=>true);
		$messages=array(1=>array(), 2=>array());
		$success[2] = $model->processExport($messages[2], false, $start_date, $end_date);
		$view = $this->getView();
		if(isset($success[2]["filename"]) && $success[2]["filename"]){
			$view->assign("filelink", Router::_("index.php?module=catalog&task=downloadResult1C&filename=".$success[2]["filename"]));
			$success[2]=true;
			$messages[2][]=Text::_("Export finished");
			$messages[2][]=Text::_("Everything OK");
		} else {
			$view->assign("filelink", "");
			$messages[2][]=Text::_("Export failed");
			$success[2]=false;
		}
		$view->assign("end_date", Date::fromSQL($end_date, true));
		$view->assign("start_date", Date::fromSQL($start_date, true));
		$view->assign("activeTab", 2);
		$view->assign("success_2", $success);
		$view->assign("messages_2", $messages);
		$view->render();
	}
	public function downloadResult1C() {
		$this->set("view", "exchange1c", true);
		$this->checkACL("view".ucfirst(Module::getInstance()->getName()).ucfirst($this->getView()->getName()));
		$file=Request::getSafe("filename","");
		if($file){
			$model = $this->getModel("exchange1c");
			$model->processDownload($file);
		} else {
			$this->setRedirect(Router::_("index.php?module=catalog&view=exchange1c"), Text::_("File absent"));
		}
	}
	/********************************** EXCHANGE WITH 1C STOP ***********************************/
}
?>