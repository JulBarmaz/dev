<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO


defined('_BARMAZ_VALID') or die("Access denied");

class SpravController extends Controller	{
	public function __construct($name,$module) {
		parent::__construct($name,$module);
	}
	public function ajaxcheckSpravField() {
		$moduleName	= Module::getInstance()->getName();
		$viewname = $this->getView()->getName();
		$layout = $this->getView()->getLayout();
		
		$model = $this->getModel();
		$model->loadMeta();
		$fld=Request::getSafe('fld');
		$val=Request::getSafe('val');
		$psid=Request::getInt('psid',0);
		$reestr = Module::getInstance()->get('reestr');
		$reestr->set('view', $viewname);
		$reestr->set('multy_code', Request::getSafe("multy_code",0));
		$reestr->set('psid',$psid);
		$reestr->set('controller', Request::getSafe("controller"));
		
		// Update meta after reestr filled
		$model->updateMeta();
		
		if($model->isUniqueFieldValue($fld, $val, $psid)) echo "OK";  else echo Text::_("Occupied");
	}
	public function ajaxToggleEnabled() {
		$mdl=Module::getInstance();
		$model = $this->getModel();
		$viewname 	= $this->getView()->getName();
		$model->loadMeta();
		if($model->meta->use_view_rights){
			if (defined("_ADMIN_MODE")) $canModify=$this->checkACL("view".ucfirst($mdl->getName()).ucfirst($model->meta->use_view_rights), false);
			else $canModify=$this->checkACL("modify".ucfirst($mdl->getName()).ucfirst($model->meta->use_view_rights),false);
		} else {
			if (defined("_ADMIN_MODE")) $canModify=$this->checkACL("view".ucfirst($mdl->getName()).ucfirst($viewname), false);
			else $canModify=$this->checkACL("modify".ucfirst($mdl->getName()).ucfirst($viewname),false);
		}
		if ($canModify) {
			$psid   = $this->getPsid();
			$reestr = Module::getInstance()->get('reestr');
			$reestr->set('view', $viewname);
			$reestr->set('multy_code', Request::getSafe("multy_code",0));
			$reestr->set('psid',$psid);
			$reestr->set('page', Request::getInt("page", 1));
			$reestr->set('sort', Request::getSafe("sort"));
			$reestr->set('orderby', Request::getSafe("orderby"));
			$reestr->set('controller', Request::getSafe("controller"));
			
			// Update meta after reestr filled
			$model->updateMeta();
			
			if ($psid){
				if ($model->toggleEnabled($psid)) echo "OK"; else echo "ERR";
			} else echo "ERR2";
		} else echo "ERR1";
	}
	public function ajaxUpdateOrdering(){
		if(defined("_ADMIN_MODE")) {
			$mdl=Module::getInstance();
			$model = $this->getModel();
			$viewname 	= $this->getView()->getName();
			$model->loadMeta();
			if($model->meta->use_view_rights){
				if (defined("_ADMIN_MODE")) $canModify=$this->checkACL("view".ucfirst($mdl->getName()).ucfirst($model->meta->use_view_rights), false);
				else $canModify=false;
			} else {
				if (defined("_ADMIN_MODE")) $canModify=$this->checkACL("view".ucfirst($mdl->getName()).ucfirst($viewname), false);
				else $canModify=false;
			}
			if ($canModify) {
//				$psid   = Request::getInt('psid', 0);
				$psid   = $this->getPsid();
				$ordering   = Request::getInt('ordering', 0);
				$multy_code   = Request::getInt('multy_code', 0);
				$reestr = $mdl->get('reestr');
				$reestr->set("ordering",$ordering);
				$reestr->set("psid",$psid);
				$reestr->set("multy_code",$multy_code);

				// Update meta after reestr filled
				$model->updateMeta();
				
				if(!$ordering) echo "ERR";
				elseif ($model->updateOrdering()) echo "OK";
				else  echo "ERR";
			} else  echo "ERR";
		}
	}
	public function ajaxUpdateLinkOrdering() {
		if(defined("_ADMIN_MODE")) {
			$mdl=Module::getInstance();
			$model = $this->getModel();
			$viewname 	= $this->getView()->getName();
			$model->loadMeta();
			if($model->meta->use_view_rights){
				if (defined("_ADMIN_MODE")) $canModify=$this->checkACL("view".ucfirst($mdl->getName()).ucfirst($model->meta->use_view_rights), false);
				else $canModify=$this->checkACL("modify".ucfirst($mdl->getName()).ucfirst($model->meta->use_view_rights),false);
			} else {
				if (defined("_ADMIN_MODE")) $canModify=$this->checkACL("view".ucfirst($mdl->getName()).ucfirst($viewname), false);
				else $canModify=$this->checkACL("modify".ucfirst($mdl->getName()).ucfirst($viewname),false);
			}
			if ($canModify) {
				$psid   = $this->getPsid();
				$ordering   = Request::getInt('ordering', 0);
				$multy_code   = Request::getInt('multy_code', 0);
				$reestr = $mdl->get('reestr');
				$reestr->set("ordering",$ordering);
				$reestr->set("psid",$psid);
				$reestr->set("multy_code",$multy_code);
				
				// Update meta after reestr filled
				$model->updateMeta();
				
				if(!$ordering) echo "ERR";
				elseif ($model->updateLinkOrdering()) echo "OK";
				else  echo "ERR";
			} else  echo "ERR";
		}
	}
	public function ajaxshowAddressEditor(){
		$data=Request::getSafe("data","");
		$ctrl_id=Request::getSafe("id","");
		echo Address::renderEditor($data, false, false, array("updateAddressPanel('".$ctrl_id."')"), "pa_");
	}
	public function ajaxcheckAddressPanelData(){
		$address=Address::getTmpl();
		$data['country_id']=Request::getInt("pa_country_id");
		$data['region_id']=Request::getInt("pa_region_id");
		$data['district_id']=Request::getInt("pa_district_id");
		$data['locality_id']=Request::getInt("pa_locality_id");
		$data['country']=Request::getSafe("pa_country");
		$data['region']=Request::getSafe("pa_region");
		$data['district']=Request::getSafe("pa_district");
		$data['locality']=Request::getSafe("pa_locality");
		$data['zipcode']=Request::getSafe("pa_zipcode");
		$data['street']=Request::getSafe("pa_street");
		$data['house']=Request::getSafe("pa_house");
		$data['apartment']=Request::getSafe("pa_apartment");
		$data['fullinfo']=Request::getSafe("pa_fullinfo");
		foreach($address as $key=>$val){
			if (isset($data[$key])) $address[$key]=$data[$key]; else $address[$key]=$val;
		}
		echo base64_encode(json_encode($data));
	}
	public function ajaxBuildPathTree() {
//		$psid   = Request::getInt('psid', 0);
		$psid   = $this->getPsid();
		$model=$this->getModel();
		echo $model->getTreePath($psid);
	}
	public function ajaxgetContlist(){
		$reestr = Module::getInstance()->get('reestr');
		$reestr->set('is_ajax',1);
		$reestr->set('layout',$this->get('layout'));
		$reestr->set('view',$this->get('view'));
		$reestr->set('multy_code',Request::getSafe("multy_code",0));
		$reestr->set('lol',Request::getSafe('lol',''));
		$this->showData();
	}
	// Получаем Фильтр аджаксом
	public function ajaxshowfilter() {
		$reestr = Module::getInstance()->get('reestr');
		$reestr->set('controller',$this->get('controller'));
		$reestr->set('view',$this->get('view'));
		$reestr->set('layout',$this->get('layout'));
		$reestr->set('multy_code',Request::getSafe("multy_code",0));
		$reestr->set('trash',Request::getSafe("trash",0));
		$model = $this->getModel();
		echo $model->getAjaxFilter();
	}
	// Вызывается из функции alterAjaxFilter (Справочник selector)
	public function ajaxsetfilter() {
		$moduleName	= Module::getInstance()->getName();
		$viewname = $this->getView()->getName();
		$layout = $this->getView()->getLayout();
		$filter_string=Request::get("filter_string",'');
		$filter_name=Request::getSafe("filter_name",'');
		$flt_ext_mode=Request::getInt('filter_ext_mode',-1);
		if(defined("_ADMIN_MODE")) $side=1; else $side=0;
		if($flt_ext_mode===1 || $flt_ext_mode===0 ) $_SESSION['filter_ext_mode'][$moduleName][$uid][$viewname.".".$layout.".".$side]=$flt_ext_mode;
		$flt=new SpravFilter();
		$flt->resetFilterString($moduleName, $this->get('view'),$this->get('layout'));
		$filter_arr["c.".$filter_name]['type']='string';
		$filter_arr["c.".$filter_name]['title']="c.".$filter_name;
		$filter_arr["c.".$filter_name]['ck_reestr']=false;
		$_REQUEST["c_".$filter_name]=$filter_string;
		$flt->saveFilterString($filter_arr,$moduleName,$this->get('view'),$this->get('layout'),1);
		echo "OK";
	}
	public function ajaxresetfilter() {
		$answer = array("result"=>"ERROR", "href"=>"");
		$flt=new SpravFilter();
		if($flt->resetFilterString(Module::getInstance()->getName(), $this->get('view'), $this->get('layout'))){
			$answer["result"]="OK";
			
			$moduleName = Module::getInstance()->getName();
			$viewname = $this->get('view');
			$layout = $this->get('layout');
			$controller = Request::getSafe("controller");
			$sort = Request::getSafe("sort");
			$orderby = Request::getSafe("orderby");
			$page = Request::getInt('page', 1);
			$psid = $this->getPsid();
			$multy_code = Request::getInt('multy_code', 0);
			$alias = "";
			if(defined("_ADMIN_MODE")) $side=1; else $side=0;
			
			$url = 'index.php?module='.$moduleName;
			
			if(isset($_SESSION[$moduleName][$viewname.".".$layout.".".$side]['add_filter_hidden_fields'])) $add_filter_hidden_fields = $_SESSION[$moduleName][$viewname.".".$layout.".".$side]['add_filter_hidden_fields'];
			else $add_filter_hidden_fields = array();
			if(is_array($add_filter_hidden_fields)){
				foreach($add_filter_hidden_fields as $ahf_key=>$ahf_val){
					$url.= '&'.$ahf_key.'='.urlencode($ahf_val);
				}
			}
			
			if(!defined("_ADMIN_MODE")) {
				if($layout == "default") $layout = "";
				$router_vars['psid'] = $psid;
				if($psid == $multy_code) $multy_code = "";
				$alias = Router::getInstance($moduleName)->getAlias($router_vars);
			}
			
			$url.= ($viewname ? '&view='.$viewname : "");
			$url.= ($layout ? '&layout='.$layout : "");
			$url.= ($multy_code ? '&multy_code='.$multy_code : "");
			$url.= ($psid ? '&psid='.$psid : "");
			$url.= ($alias ? '&alias='.$alias : "");
			$url.= ($orderby ? '&orderby='.$orderby : "");
			$url.= ($sort ? '&sort='.$sort : "");
			$url.= ($controller ? "&controller=".$controller : "");
			$url.= '&page='.$page;
			$answer["href"] = Router::_($url, false, false);
//			$answer["url"] = $url;
		}
		echo json_encode($answer);
	}
	public function ajaxresetFilterKey(){
		$key=Request::getSafe('key_val');
		$answer = array("result"=>"ERROR", "href"=>"");
		if ($key){
			$uid=User::getInstance()->getID(true);
			// if(!$uid) $uid=session_id();
			$moduleName = Module::getInstance()->getName();
			$viewname = $this->get('view');
			$layout = $this->get('layout');
			$controller = Request::getSafe("controller");
			$sort = Request::getSafe("sort");
			$orderby = Request::getSafe("orderby");
			$page = Request::getInt('page', 1);
			$psid = $this->getPsid();
			$multy_code = Request::getInt('multy_code', 0);
			$alias = "";
			
			$flt = new SpravFilter();
			if(defined("_ADMIN_MODE")) $side=1; else $side=0;
			if($flt->resetFilterKey($moduleName, $viewname, $layout, $side, $key, $uid)) {
				if($flt->getFilter($side,$moduleName, $viewname, $layout)==false) $flt->resetFilterString($moduleName, $viewname, $layout);
				$url = 'index.php?module='.$moduleName;
				if(isset($_SESSION[$moduleName][$viewname.".".$layout.".".$side]['add_filter_hidden_fields'])) $add_filter_hidden_fields = $_SESSION[$moduleName][$viewname.".".$layout.".".$side]['add_filter_hidden_fields'];
				else $add_filter_hidden_fields = array();
				if(is_array($add_filter_hidden_fields)){
					foreach($add_filter_hidden_fields as $ahf_key=>$ahf_val){
						$url.= '&'.$ahf_key.'='.urlencode($ahf_val);
					}
				}
				if(!$side) {
					if($layout == "default") $layout = "";
					$router_vars['psid'] = $psid;
					if($psid == $multy_code) $multy_code = "";
					$alias = Router::getInstance($moduleName)->getAlias($router_vars);
				}
				$url.= ($viewname ? '&view='.$viewname : "");
				$url.= ($layout ? '&layout='.$layout : "");
				$url.= ($multy_code ? '&multy_code='.$multy_code : "");
				$url.= ($psid ? '&psid='.$psid : "");
				$url.= ($alias ? '&alias='.$alias : "");
				$url.= ($orderby ? '&orderby='.$orderby : "");
				$url.= ($sort ? '&sort='.$sort : "");
				$url.= ($controller ? "&controller=".$controller : "");
				$url.= '&page='.$page;
				$answer["href"] = Router::_($url, false, false);
				/***********************************/
				$answer["result"]="OK";
			}
		}
		echo json_encode($answer);
	}
	public function ajaxcleanTrash() {
		$result["first_rec"]="0";
		$result["rec_count"]="0";
		if (defined("_ADMIN_MODE")) {
			$mdl = Module::getInstance();
			$moduleName	= $mdl->getName();
			$reestr = $mdl->get('reestr');
			$model = $this->getModel();
			$viewname = $this->getView()->getName();
			$model->loadMeta();
			if($model->meta->use_view_rights){
				$canDelete=$this->checkACL("delete".ucfirst($mdl->getName()).ucfirst($model->meta->use_view_rights), false);
			} else {
				$canDelete=$this->checkACL("delete".ucfirst($mdl->getName()).ucfirst($viewname), false);
			}
			if ($canDelete) {
				$multy_code   = Request::getInt('multy_code', 0); 		// ид верхней группы
				$reestr->set('view',$viewname);
				$reestr->set('model',$model);
				$reestr->set("multy_code",$multy_code);
				$trash      = Request::getInt('trash',0);
				$layout			= Request::getSafe('layout');
				$first_rec	= Request::getInt('first_rec',0);
				
				// Update meta after reestr filled
				$model->updateMeta();
				
				$cleanRowsPerQuery=intval(adminConfig::$adminCleanRowsPerQuery);
				if (!$first_rec && $model->checkTrashChilds()) {
					$result["status"]="finished";
					$result["message"]=Text::_("Child objects exists")."<br />".Text::_("Click here.");
					$msg=Text::_("Child objects exists")."<br />".Text::_("Click here.");
				} else {
					// @TODO Возможно зацикливание
					if(!$first_rec) $model->markDeleteTrashChilds();
					$total_records=$model->countDeleted();
					$result["rec_count"]=$total_records;
					if ($cleanRowsPerQuery && $total_records && $total_records>$first_rec) { // до конца не дошли пока
						if ($model->cleanAttachments($first_rec,$total_records)) {
							$cur_range=$first_rec+$cleanRowsPerQuery;
							if ($cur_range>$total_records) $cur_range=$total_records;
							$result["status"]="processing";
							$result["message"]=Text::_("Processing")." ".$cur_range." ".Text::_("from")." ".$total_records;
							$result["first_rec"]=$first_rec+$cleanRowsPerQuery;
						}	 else {
							$result["status"]="error";
							$result["message"]=Text::_("Error deleting files")."<br />".Text::_("Click here");
						}
					} else { // теперь можно удалять все записи
						// cleanRecords новая функция вместо cleanTrash
						if ($model->cleanRecords()) {
							$result["status"]="finished";
							$result["message"]=Text::_("Finished")."<br />".Text::_("Click here");
						} else {
							$result["status"]="error";
							$result["message"]=Text::_("Error deleting records")."<br />".Text::_("Click here");
						}
					}
				}
			} else {
				$result["status"]="error";
				$res_msg=Text::_("Access denied");
				if (siteConfig::$debugMode)	$res_msg.="<br /> not right - delete".ucfirst($mdl->getName()).ucfirst($viewname);
				$res_msg.="<br />".Text::_("Click here");
				$result["message"]=$res_msg;
			}
		} else {
			$result["status"]="error";
			$result["message"]=Text::_("Function disabled")."<br />".Text::_("Click here");
		}
		echo json_encode($result);
	}
	public function reorder(){
		if(defined("_ADMIN_MODE")) {
			$viewname 	= $this->getView()->getName();
			$mdl=Module::getInstance();
			$model = $this->getModel();
			$model->loadMeta();
			if($model->meta->use_view_rights){
				$this->checkACL("view".ucfirst($mdl->getName()).ucfirst($model->meta->use_view_rights));
			} else {
				$this->checkACL("view".ucfirst($mdl->getName()).ucfirst($viewname));
			}
			$reestr = $mdl->get('reestr');
			$reestr->set("multy_code", Request::getInt('multy_code', 0));
			$reestr->set('sort', Request::getSafe("sort"));
			$reestr->set('orderby', Request::getSafe("orderby"));
			
			// Update meta after reestr filled
			$model->updateMeta();
			
			$model->reorder();
		}
		$this->showData();
	}
	public function ajaxshowData() {
		$this->showData();
	}
	// @FIXME Didn't found where is used
	public function applyFilter(){
		$module = Module::getInstance();
		$view 	= $this->getView()->getName();
		$model = $this->getModel();
		$model->loadMeta();
		if($model->meta->use_view_rights){
			$this->checkACL("view".ucfirst($mdl->getName()).ucfirst($model->meta->use_view_rights));
		} else {
			$this->checkACL("view".ucfirst($mdl->getName()).ucfirst($viewname));
		}
		$reestr = $module->get('reestr');
		$multy_code	= Request::getInt('multy_code', $reestr->get("multy_code",0)); 		// ид верхней группы
		$psid		= $this->getPsid();
		if(!$multy_code)	$multy_code=$psid;
		elseif(!$psid)		$psid=$multy_code;
		$layout=$this->getView()->getLayout();
		
		if(defined("_ADMIN_MODE")) $side=1; else $side=0;
		$uid = User::getInstance()->getID(true);
		$flt_ext_mode=Request::getInt('filter_ext_mode',-1);
		if($flt_ext_mode===1 || $flt_ext_mode===0 ) $_SESSION['filter_ext_mode'][$module->getName()][$uid][$view.".".$layout.".".$side]=$flt_ext_mode;
		$sort = Request::getSafe("sort");
		$orderby = Request::getSafe("orderby");
		$page		= Request::getInt('page', 1);
		$reestr->set('psid',$psid);
		$reestr->set('page',$page);
		$reestr->set('sort',$sort);
		$reestr->set('orderby',$orderby);
		$reestr->set("multy_code",$multy_code);
		
		// Update meta after reestr filled
		$model->updateMeta();
		
		if(Request::get('save_filter',false)) $model->applyFilterData();		
		$this->setRedirect("index.php?module=".$module->getName()."&view=".$view."&layout=".$layout.($psid ? "&psid=".$psid : "").($multy_code ? "&multy_code=".$multy_code : "").($sort ? "&sort=".$sort :"").($orderby ? "&orderby=".$orderby : "").(Request::get('reset_filter',false) ? "&reset_filter=1" :""));
	}
	// parent_element_name и parent_element_table влияют только если не будет родительской метадаты
	public function showData($parent_element_name='',$parent_element_table='') {
		$viewname 	= $this->getView()->getName();
		$mdl = Module::getInstance();
		$reestr = $mdl->get('reestr');
		$model = $this->getModel();
		$model->loadMeta();
		if($model->meta->use_view_rights){
			$this->checkACL("view".ucfirst($mdl->getName()).ucfirst($model->meta->use_view_rights));
			if (defined("_ADMIN_MODE")) $canModify=true; 
			else $canModify=$this->checkACL("modify".ucfirst($mdl->getName()).ucfirst($model->meta->use_view_rights),false);
		} else {
			$this->checkACL("view".ucfirst($mdl->getName()).ucfirst($viewname));
			if (defined("_ADMIN_MODE")) $canModify=true;
			else $canModify=$this->checkACL("modify".ucfirst($mdl->getName()).ucfirst($viewname),false);
		}
		// мультикод может проставляться при вызове в контроллерах модулей.
		$multy_code = Request::getInt('multy_code', $reestr->get("multy_code",0)); 		// ид верхней группы
		$psid   = $this->getPsid();
		if(!$multy_code)	$multy_code=$psid;
		elseif(!$psid) $psid=$multy_code;
		$reestr->set('psid',$psid);
		$reestr->set('parent_element_name', $parent_element_name);
		$reestr->set('parent_element_table', $parent_element_table);
		$reestr->set('canModify',$canModify);
		$reestr->set('page', Request::getInt("page", 1));
		$reestr->set('sort', Request::getSafe("sort"));
		$reestr->set('orderby', Request::getSafe("orderby"));
		$reestr->set("multy_code", $multy_code);
		$reestr->set('controller', Request::getSafe("controller"));
		$uid=User::getInstance()->getID(true);
		
		$flt_ext_mode=Request::getInt('filter_ext_mode',-1);
		if($flt_ext_mode===1 || $flt_ext_mode===0 ) $_SESSION['filter_ext_mode'][$mdl->getName()][$uid][$viewname.".".$reestr->get('layout',$this->get('layout')).".".(defined("_ADMIN_MODE") ? "1" : "0")]=$flt_ext_mode;
				
		$trash=Request::getInt('trash',0);
		$reestr->set("trash",$trash);
		$view = $this->getView();
		Request::getSafe('option')=="ajax" ? $is_ajax=1 : $is_ajax=0;
		$reestr->set("is_ajax",$is_ajax);
		
		// Update meta after reestr filled
		$model->updateMeta();
		
		if (defined("_ADMIN_MODE") && $model->meta->tree_index) {
			if ($trash||$model->meta->selector) $model->meta->view[$model->meta->tree_index]=1;
			else $model->meta->view[$model->meta->tree_index]=0;
		}
		if (!$is_ajax && !$trash && $model->meta->tree_index) {
			$spravTree = new simpleTreeTable ;
			$spravTree->table=$model->meta->tablename;
			$spravTree->fld_id=$model->meta->keystring;
			$spravTree->fld_parent_id=$model->meta->field[$model->meta->tree_index];
			$spravTree->fld_title=$model->meta->namestring;
			$spravTree->fld_deleted=$model->meta->deleted;
			$spravTree->fld_enabled=$model->meta->enabled;
			$spravTree->split_title=50;
			if ($model->meta->ordering_field) $spravTree->fld_orderby=$model->meta->ordering_field;
			else $spravTree->fld_orderby=$model->meta->namestring;
			$spravTree->element_js="setContList(this,'%s'); $(this).prev('.hitarea.expandable-hitarea').trigger('click');";
			$spravTree->buildTreeArrays("", 0, $model->meta->tree_skip_deleted, $model->meta->tree_skip_disabled);
			$sprav_cookie=$model->getSpravCookie($mdl->getName(),$viewname, $this->get('layout'));
			// @TODO Может multy_code=0 тоже учитывать ?
			if ($sprav_cookie['multy_code']) $reestr->set("multy_code",$sprav_cookie['multy_code']);
			$reestr->set('is_ajax',true);
			$result = $model->getData();
			$view->renderSpravPanels($model->meta,$result,$spravTree);
		} elseif(adminConfig::$adminSelectorAsTree && $model->meta->selector && $is_ajax && !$trash && $model->meta->tree_index) {
			$spravTree = new simpleTreeTable ;
			$spravTree->table=$model->meta->tablename;
			$spravTree->fld_id=$model->meta->keystring;
			$spravTree->fld_parent_id=$model->meta->field[$model->meta->tree_index];
			$spravTree->fld_title=$model->meta->namestring;
			$spravTree->fld_deleted=$model->meta->deleted;
			$spravTree->fld_enabled=$model->meta->enabled;
			$spravTree->split_title=50;
			if ($model->meta->ordering_field) $spravTree->fld_orderby=$model->meta->ordering_field;
			else $spravTree->fld_orderby=$model->meta->namestring;
			$spravTree->element_js="$(this).closest('div').prev('.hitarea').trigger('click');";
			$spravTree->buildTreeArrays("", 0, 1, 0);
			$sprav_cookie=$model->getSpravCookie($mdl->getName(),$viewname, $this->get('layout'));
			// @TODO Может multy_code=0 тоже учитывать ?
			if ($sprav_cookie['multy_code']) $reestr->set("multy_code",$sprav_cookie['multy_code']);
			$reestr->set('is_ajax',true);
			$view->renderTreeSelector($model->meta, $spravTree);
		} else {
			$result = $model->getData();
			$view->renderSprav($model->meta,$result, ($model->meta->tree_index && !$model->meta->selector && !$trash ? "col-sm-9 col-md-10" : ""));
		}
	}
	public function delete()	{
		$mdl = Module::getInstance();
		$moduleName	= $mdl->getName();
		$reestr = $mdl->get('reestr');
		$model = $this->getModel();
		$viewname = $this->getView()->getName();
		$model->loadMeta();
		if($model->meta->use_view_rights){
			if (defined("_ADMIN_MODE")) $this->checkACL("view".ucfirst($mdl->getName()).ucfirst($model->meta->use_view_rights));
			else $this->checkACL("modify".ucfirst($mdl->getName()).ucfirst($model->meta->use_view_rights));
		} else {
			if (defined("_ADMIN_MODE")) $this->checkACL("view".ucfirst($mdl->getName()).ucfirst($viewname));
			else $this->checkACL("modify".ucfirst($mdl->getName()).ucfirst($viewname));
		}
		$arr_psid     = Request::getSafe('cps_id', false);				// массив отмеченных галочкой элементов
		$psid   = $this->getPsid();
		$multy_code   = Request::getInt('multy_code', 0);
		$controller		= Request::getSafe("controller", $reestr->get("controller"));
		$reestr->set('controller',$controller);
		$reestr->set('view',$viewname);
		$reestr->set('model',$model);
		$reestr->set("multy_code",$multy_code);
		$reestr->set('arr_psid',$arr_psid);
		
		$trash      = Request::getInt('trash',0);
		$reestr->set("trash",$trash);
		$layout			= Request::getSafe('layout');
		$page			= Request::getInt('page', 1);
		$sort			= Request::getSafe('sort');
		$orderby		= Request::getSafe('orderby');
		
		// Update meta after reestr filled
		$model->updateMeta();
		
		if($model->delete()) $msg=Text::_("Operation complete");
		else  $msg=Text::_("Operation failed");
		$url='index.php?module='.$moduleName.($controller ? "&controller=".$controller : "").'&view='.$viewname.'&layout='.$layout.'&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.$multy_code.'&trash='.$trash;
		$this->setRedirect($url,$msg);
	}
	public function deleteNow()	{
		$mdl = Module::getInstance();
		$moduleName	= $mdl->getName();
		$reestr = $mdl->get('reestr');
		$model = $this->getModel();
		$viewname = $this->getView()->getName();
		$model->loadMeta();
		if($model->meta->use_view_rights){
			if (defined("_ADMIN_MODE")) $this->checkACL("view".ucfirst($mdl->getName()).ucfirst($model->meta->use_view_rights));
			else $this->checkACL("modify".ucfirst($mdl->getName()).ucfirst($model->meta->use_view_rights));
		} else {
			if (defined("_ADMIN_MODE")) $this->checkACL("view".ucfirst($mdl->getName()).ucfirst($viewname));
			else $this->checkACL("modify".ucfirst($mdl->getName()).ucfirst($viewname));
		}
		$arr_psid     = Request::getSafe('cps_id', false);				// массив отмеченных галочкой элементов
		$psid   = $this->getPsid();
		$multy_code   = Request::getInt('multy_code', 0);
		$controller		= Request::getSafe("controller", $reestr->get("controller"));
		$reestr->set('controller',$controller);
		$reestr->set('view',$viewname);
		$reestr->set('model',$model);
		$reestr->set("multy_code",$multy_code);
		$reestr->set('arr_psid',$arr_psid);
		$trash			= Request::getInt('trash',0);
		$reestr->set("trash",$trash);
		$layout			= Request::getSafe('layout');
		$page			= Request::getInt('page', 1);
		$sort			= Request::getSafe('sort');
		$orderby		= Request::getSafe('orderby');
		
		// Update meta after reestr filled
		$model->updateMeta();
		
		if($model->deleteNow()) $msg=Text::_("Operation complete");
		else  $msg=Text::_("Operation failed");
		$url='index.php?module='.$moduleName.($controller ? "&controller=".$controller : "").'&view='.$viewname.'&layout='.$layout.'&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.$multy_code.'&trash='.$trash;
		$this->setRedirect($url,$msg);
	}
	public function ajaxmodify(){
		Portal::getInstance()->disableTemplate();
		$this->modify(true);
	}
	public function make_clone(){
		$this->modifyElement(false, true);
	}
	public function modify($ajaxModify=false){
		$this->modifyElement($ajaxModify);
	}
	
	public function modifyElement($ajaxModify=false, $clone_mode=false){
		$mdl = Module::getInstance();
		$reestr = $mdl->get('reestr');
		$model = $this->getModel();
		$viewname = $this->getView()->getName();
		$multy_code   = Request::getInt('multy_code', 0);
		$reestr->set("multy_code",$multy_code);
		$model->loadMeta();
		if($model->meta->use_view_rights){
			if (defined("_ADMIN_MODE")) $this->checkACL("view".ucfirst($mdl->getName()).ucfirst($model->meta->use_view_rights));
			else $this->checkACL("modify".ucfirst($mdl->getName()).ucfirst($model->meta->use_view_rights));
		} else {
			if (defined("_ADMIN_MODE")) $this->checkACL("view".ucfirst($mdl->getName()).ucfirst($viewname));
			else $this->checkACL("modify".ucfirst($mdl->getName()).ucfirst($viewname));
		}
		$psid   = $this->getPsid();
		$reestr->set('ajaxModify',$ajaxModify);
		$reestr->set('view',$viewname);
		$reestr->set('psid',$psid);
		$activeTab	= Request::getInt("activeTab",1);
		$layout		= Request::getSafe('layout');
		$page			= Request::getInt('page', 1);
		$sort		= Request::getSafe('sort');
		$orderby	= Request::getSafe('orderby');
		$controller	= Request::getSafe('controller','');
		$reestr->set('controller',$controller);
		$reestr->set('layout',$layout);
		$reestr->set('page',$page);
		$reestr->set('sort',$sort);
		$reestr->set('orderby',$orderby);
		
		// Update meta after reestr filled
		$model->updateMeta();
		
		$url='index.php?module='.$mdl->getName().($controller ? "&controller=".$controller : "").'&view='.$viewname.'&layout='.$layout.'&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.urlencode($multy_code);
		$reestr->set('onCancelURL',$url);
		$reestr->set('model', $model);
		$reestr->set('metadata', $model->meta);
		if ($clone_mode) {
			$result = $model->getElementClone();
			$reestr->set('psid',0);
		} else $result = $model->getElement();
		$view = $this->getView();
		$view->assign('activeTab',$activeTab);
		$view->modify($result);
	}
	
	public function showInfo($ajaxModify=false){
		$mdl = Module::getInstance();
		$moduleName	= $mdl->getName();
		$reestr = $mdl->get('reestr');
//		$model = $this->getModel();
		$old_layout=$this->get("layout");
		$viewname = $this->getView()->getName();
//		$model->loadMeta();
		$model = $this->getModel();
		
		$this->set("layout","default",true);
		$model->loadMeta();
		if($model->meta->use_view_rights){
			$this->checkACL("view".ucfirst($mdl->getName()).ucfirst($model->meta->use_view_rights));
			if (defined("_ADMIN_MODE")) $canModify=true;
			else $canModify=$this->checkACL("modify".ucfirst($mdl->getName()).ucfirst($model->meta->use_view_rights),false);
		} else {
			$this->checkACL("view".ucfirst($mdl->getName()).ucfirst($viewname));
			if (defined("_ADMIN_MODE")) $canModify=true;
			else $canModify=$this->checkACL("modify".ucfirst($mdl->getName()).ucfirst($viewname),false);
		}
		$multy_code   = Request::getInt('multy_code', 0);
		$psid   = $this->getPsid();
		$flt_ext_mode=Request::getInt('filter_ext_mode',-1);
		if($flt_ext_mode===1 || $flt_ext_mode===0 ) $_SESSION['filter_ext_mode'][$module][$uid][$view.".".$layout.".".$side]=$flt_ext_mode;
//		$arr_psid     = Request::getSafe('cps_id', false);				// массив отмеченных галочкой элементов
//		$psid   = Request::getInt('psid', 0);
//		if(!$psid)  if($arr_psid&&is_array($arr_psid)&&count($arr_psid)>0) $psid = $arr_psid[0];
		$reestr->set('canModify',$canModify);
		$reestr->set('ajaxModify',$ajaxModify);
		$reestr->set('view',$viewname);
		$reestr->set('model',$model);
		$reestr->set("multy_code",$multy_code);
		$reestr->set('psid',$psid);
		$page			= Request::getInt('page', 1);
		$sort		= Request::getSafe('sort');
		$orderby	= Request::getSafe('orderby');
		$reestr->set('page',$page);
		$reestr->set('sort',$sort);
		$reestr->set('orderby',$orderby);
		
		// Update meta after reestr filled
		$model->updateMeta();
		
		$result = $model->getElementData($psid);
		$view = $this->getView();
		$view->set("layout",$old_layout,true);
		$view->assign("comm",$this->initComments($psid));
		$view->renderInfo($model->meta,$result);
	}

	public function save() {
		$mdl			= Module::getInstance();
		$moduleName		= $mdl->getName();
		$reestr 		= $mdl->get('reestr');
		$model 			= $this->getModel();
		$controller		= Request::getSafe("controller", $reestr->get("controller"));
		$viewname		= $this->getView()->getName();
		$model->loadMeta();
		if($model->meta->use_view_rights){
			if (defined("_ADMIN_MODE")) $this->checkACL("view".ucfirst($mdl->getName()).ucfirst($model->meta->use_view_rights));
			else $this->checkACL("modify".ucfirst($mdl->getName()).ucfirst($model->meta->use_view_rights));
		} else {
			if (defined("_ADMIN_MODE")) $this->checkACL("view".ucfirst($mdl->getName()).ucfirst($viewname));
			else $this->checkACL("modify".ucfirst($mdl->getName()).ucfirst($viewname));
		}
		$psid   = $this->getPsid();
		$multy_code	= Request::getInt('multy_code', 0);
		$layout		= Request::getSafe('layout');
		// Добавлено так как не отрабатывали профили
		$task		= $reestr->get("task","");
		$activeTab	= Request::getInt("activeTab",1);
		$page			= Request::getInt('page', 1);
		$sort			= Request::getSafe('sort');
		$orderby		= Request::getSafe('orderby');
		$is_apply		= Request::getSafe('apply',0);
		$is_add_clone		= Request::getSafe('add_clone',0);
		$is_add_new		= Request::getSafe('add_new',0);
		$reestr->set('multy_code',$multy_code);
		$reestr->set('view',$viewname);
		$reestr->set('psid',$psid);
		
		// Update meta after reestr filled
		$model->updateMeta();
		
		$new_psid=$model->save();
		if($new_psid) {
			$model->garbageCollector($new_psid);
			$msg=Text::_("Save successfull"); $new_psid=urlencode($new_psid);
		}	else {
			$msg=Text::_("Save unsuccessfull"); $new_psid=urlencode($psid);
		}
		if(!$task && $is_apply && isset($model->meta->buttons["modify"]) && isset($model->meta->buttons["modify"]["task"])){
			$task = $model->meta->buttons["modify"]["task"];
			$viewname = $model->meta->buttons["modify"]["view"];
		} elseif(!$task && $is_add_new && isset($model->meta->buttons["new"]) && isset($model->meta->buttons["new"]["task"])){
			$task = $model->meta->buttons["new"]["task"];
			$viewname = $model->meta->buttons["new"]["view"];
		} elseif(!$task && $is_add_clone){
			if(isset($model->meta->buttons["clone"]) && isset($model->meta->buttons["clone"]["task"])){
				$task = $model->meta->buttons["clone"]["task"];
				$viewname = $model->meta->buttons["clone"]["view"];
			} elseif(isset($model->meta->buttons["new"]) && isset($model->meta->buttons["new"]["task"])){
				$task = $model->meta->buttons["new"]["task"];
				$viewname = $model->meta->buttons["new"]["view"];
			}
		}
		if (count($model->getMessages()))	$msg.="<br />".implode("<br />",$model->getMessages());
		if ($is_apply) $url='index.php?module='.$moduleName.($controller ? "&controller=".$controller : "").'&view='.$viewname.'&layout='.$layout.'&task='.($task ? $task : "modify").'&psid='.$new_psid.'&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.$multy_code.'&activeTab='.$activeTab;
		elseif($is_add_clone) $url='index.php?module='.$moduleName.($controller ? "&controller=".$controller : "").'&view='.$viewname.'&layout='.$layout.'&task=make_clone&psid='.$new_psid.'&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.$multy_code;
		elseif($is_add_new) $url='index.php?module='.$moduleName.($controller ? "&controller=".$controller : "").'&view='.$viewname.'&layout='.$layout.'&task='.($task ? $task : "modify").'&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.$multy_code;
		else $url='index.php?module='.$moduleName.($controller ? "&controller=".$controller : "").'&view='.$viewname.'&layout='.$layout.'&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.$multy_code;
		$this->setRedirect($url,$msg);
	}
	public function modifyLinks(){
		$mdl = Module::getInstance();
		$moduleName	= $mdl->getName();
		$reestr = $mdl->get('reestr');
		$model = $this->getModel();
		$viewname = $this->getView()->getName();
		$model->loadMeta();
		if($model->meta->use_view_rights){
			if (defined("_ADMIN_MODE")) $this->checkACL("view".ucfirst($mdl->getName()).ucfirst($model->meta->use_view_rights));
			else $this->checkACL("modify".ucfirst($mdl->getName()).ucfirst($model->meta->use_view_rights));
		} else {
			if (defined("_ADMIN_MODE")) $this->checkACL("view".ucfirst($mdl->getName()).ucfirst($viewname));
			else $this->checkACL("modify".ucfirst($mdl->getName()).ucfirst($viewname));
		}
		$arr_psid     = Request::getSafe('cps_id', false);				// массив отмеченных галочкой элементов
		$multy_code   = Request::getInt('multy_code', 0);
		$reestr->set('view',$viewname);
		$reestr->set('model',$model);
		$reestr->set("multy_code",$multy_code);
		$layout			= Request::getSafe('layout');
		$page			= Request::getInt('page', 1);
		$sort			= Request::getSafe('sort');
		$orderby		= Request::getSafe('orderby');
		$reestr->set('layout',$layout);
		$reestr->set('page',$page);
		$reestr->set('sort',$sort);
		$reestr->set('orderby',$orderby);
		
		// Update meta after reestr filled
		$model->updateMeta();
		
		$url='index.php?module='.$moduleName.'&view='.$viewname.'&layout='.$layout.'&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.urlencode($multy_code);
		$reestr->set('onCancelURL',$url);
		$linkArray=$model->getParentLink($multy_code);

		$items = $model->getShortData($arr_psid);
		$view = $this->getView();
		$view->assign('items',$items);
		$view->assign('linkArray',$linkArray);
		$view->modifyLinks();
	}

	public function saveLinks() {
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
		$arr_psid		= Request::getSafe('cps_id', false);				// массив отмеченных галочкой элементов
		$multy_code		= Request::getInt('multy_code', 0);
		$layout			= Request::getSafe('layout');
		$page			= Request::getInt('page', 1);
		$sort			= Request::getSafe('sort');
		$orderby		= Request::getSafe('orderby');
		$is_apply		= Request::getSafe('apply',0);
		$reestr->set('multy_code',$multy_code);
		$reestr->set('view',$viewname);
		$group_array=Request::get('linkEditor');
		$current_group=Request::getInt('current_parent');
		
		// Update meta after reestr filled
		$model->updateMeta();
		$result=true;
		if(is_array($group_array)) {
			if(!in_array($current_group,$group_array))	{	  // удаляем текущую группу
				$result=$model->deleteLinks($current_group,$arr_psid);
			}
			if($result) {
				foreach($group_array as $val)	{
					if($val!=$current_group) $result=$model->addLinks($val,$arr_psid);
				}
			}
		} else { // вошедший массив пустой, значит удаляем текущую группу у выбранных товаров
			$result=$model->deleteLinks($current_group,$arr_psid);
		}

		if($result) {
			if($model->afterSaveLinks($current_group,$group_array,$arr_psid)){
				$msg=Text::_("Save successfull");
			} else {
				$msg=Text::_("Save successfull").". ".Text::_("Unable to perform extended updates");
			}
		} else { 
			$msg=Text::_("Save unsuccessfull");
		}
		$url='index.php?module='.$moduleName.'&view='.$viewname.'&layout='.$layout.'&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.$multy_code;
		$this->setRedirect($url,$msg);
	}
}
?>