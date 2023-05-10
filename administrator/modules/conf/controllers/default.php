<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class confControllerdefault extends SpravController {
	public function ajaxcheckSpravField() {
		$moduleName	= Module::getInstance()->getName();
		$viewname = $this->getView()->getName();
		$layout = $this->getView()->getLayout();
		
		$model = $this->getModel();
		$model->loadMeta();
		$fld=Request::getSafe('fld');
		$val=Request::getSafe('val');
		$psid=Request::getInt('psid',0);
		switch($fld){
			case "f_name":
				if(strlen($val) > 3) {
					if(substr($val,0,3)!="df_") $val="df_".DBUtil::cleanNameForDB($val);
				} else {
					echo Text::_("Error");
					return;
				}
			break;
		}
		if($model->isUniqueFieldValue($fld, $val, $psid)) echo "OK";  else echo Text::_("Occupied");
	}
	/* Управление видимостью полей */
	/* Ajax функции */
	public function ajaxListModules() {
		$this->set('view','fields',true);
		$view=$this->getView();
		$view->listModules();
	}
	public function ajaxListViews() {
		$module_name	= Request::getSafe('m_module',"");
		$result = $this->checkFields($module_name, false);
//		Util::logFile($result);
		$this->set('view','fields',true);
		$view=$this->getView();
		$view->listViews();
	}
	public function ajaxListLayouts() {
		$this->set('view','fields',true);
		$view=$this->getView();
		$view->listLayouts();
	}
	// форма выбора видимость каких полей редактировать
	public function selectVisio() {
		$this->set('view','fields',true);
		$this->set('layout','selectvisio',true);
		$view=$this->getView();
		$view->render();
	}
	// форма редактирования видимости полей
	public function prepareVisioForm() {
		$m_admin_side=Request::getInt('m_admin_side');
		$m_module=Request::get('m_module','');
		$m_view=Request::get('m_view','');
		$m_layout=Request::get('m_layout','');
		if (($m_admin_side!=0 && $m_admin_side!=1)|| !$m_module || !$m_view || !$m_layout) {
			$this->setRedirect("index.php?module=conf&task=selectVisio", Text::_("Data for selected items not found"));
		} else {
			$model=$this->getModel('fields');
			$res=$model->getLayoutInfo($m_admin_side, $m_module, $m_view,$m_layout); // Получаем сохраненные настройки
			Module::getInstance($m_module);
			$meta=new SpravMetadata($m_module,$m_view,$m_layout,true,true,abs($m_admin_side-1)); // Получаем текущую метадату
			if($res){
				$this->set('view','fields',true);
				$view=$this->getView();
				$view->assign("m_admin_side", $m_admin_side );
				$view->assign("m_meta_field", array_flip($meta->field));
				$view->assign("m_meta_name", $meta->field_title );
				$view->assign("meta", $meta);
				$view->assign("m_module", $m_module);
				$view->assign("m_view", $m_view);
				$view->assign("m_layout", $m_layout);
				$view->assign("res", $res);
				$this->set('layout','visioform',true);
				$error=false;
				foreach($meta->field as $id=>$fld){
					if (!array_key_exists($fld, $res)) $error=true;
				}
				if($error) $this->setRedirect("index.php?module=conf&task=prepareFields&m_module=".$m_module, Text::_("Metadata in tables is different").". ".Text::_("Reread data").".");
				else $view->render();
			}	else $this->setRedirect("index.php?module=conf&task=selectVisio", Text::_("Data for selected items not found"));
		}
	}
	public function saveVisio() {
		$mdl = Module::getInstance();
		$model=$mdl->getModel('fields');
		$is_apply	= Request::getSafe('apply');
		$m_admin_side=Request::get('m_admin_side',false);
		$m_module=Request::get('m_module',false);
		$m_view=Request::get('m_view',false);
		$m_layout=Request::get('m_layout',false);
		$res=$model->saveVisioData();
		if($res) $msg=Text::_("Save successfull"); else $msg=Text::_("Save unsuccessfull");
		if($is_apply) $url="index.php?module=conf&task=prepareVisioForm&m_admin_side=".$m_admin_side."&m_module=".$m_module."&m_view=".$m_view."&m_layout=".$m_layout;
		else $url="index.php?module=conf&task=selectVisio";
		$this->setRedirect($url,$msg);
	}
	// получаем форму для загрузки полей из метадаты
	public function prepareFields(){
		$m_module=Request::get('m_module',"");
		$this->set('view','fields',true);
		$view=$this->getView();
		$view->assign("m_module",$m_module);
		$this->set('layout','preparefields',true);
		$view->render();
	}
	// загружаем поля из метадаты
	public function checkFields($_module_name="", $redirect=true) {
		$msg_arr=array();
		$model = $this->getModel('fields');
		$module_name	= Request::getSafe('m_module', $_module_name);
		$arrMod=Module::getInstalledModules();
		if ($module_name && in_array($module_name, $arrMod)){
			Debugger::getInstance()->milestone("checkFields for front modules ".$module_name);
			$res=$model->saveFields(0, $module_name);
			if($res) $msg_arr[]=Text::_("Front")." : ".Text::_("Save successfull")." ".$model->getMsg();
			else $msg_arr[]=Text::_("Front")." :".Text::_("Save unsuccessfull")." ".$model->getMsg();
			Debugger::getInstance()->milestone("checkFields for admin modules ".$module_name);
			$res=$model->saveFields(1, $module_name);
			if($res) $msg_arr[]=Text::_("Admin zone")." : ".Text::_("Save successfull")." ".$model->getMsg();
			else $msg_arr[]=Text::_("Admin zone")." : ".Text::_("Save unsuccessfull")." ".$model->getMsg();
			$url="index.php?module=conf&task=selectVisio";
		} else {
			$msg_arr[]=Text::_("Module undefined");
			$url="index.php?module=conf&task=prepareFields";
		}
		if($redirect) $this->setRedirect($url, implode("<br />", $msg_arr));
		else return $msg_arr;
	}
	/* заготовка
	public function ajaxRereadMetadata()  {
		$this->set('view','fields',true);
		$model = $this->getModel('fields');
		$res=$model->rereadMetadata();
		Util::halt();
	}	
	*/
	public function checkFieldsByTable($tables=array()) {
		$model = $this->getModel('fields');
		if(count($tables)){
			$modules = $model->getModulesByTables($tables);
			if (count($modules)){
				foreach($modules as $module=>$ex){
					$this->checkFields($module, false);
				}
			}
		}
	}
	public function saveConfig() {
		$is_cancel=Request::get('cancel_but');
		if (!$is_cancel) {
			$db=Database::getInstance();
			$activeTab = Request::getSafe('config','site');
			$found=0; $up_sql='';
			foreach(ConfigTMPL::$_tabs as $k=>$v){
				if ($v[0]==$activeTab) $found=1;
			}
			if ($found) {
				// нашли существующую секцию
				$confTMPL_name=$activeTab."ConfigTMPL";
				$confTMPL= new $confTMPL_name;
				foreach ($confTMPL->props as $_key=>$_val ) {
					switch ($_val[0]) {
						case "boolean":
							$cur_val=Request::getInt($_key);
							if ($cur_val>0) $cur_val=1;
							break;
						case "integer":
							$cur_val=Request::getInt($_key);
							break;
						case "float":
							$cur_val=Request::getFloat($_key);
							break;
						case "text":
							$cur_val=Request::get($_key);
							break;
						case "string":
						case "filenames":
						case "folder":
						case "select":
						case "select_method":
						case "table_select":
							$cur_val=Request::getSafe($_key,'');
							break;
						case "password":
							$cur_val=Request::getSafe($_key,'');
							break;
						case 'multiselect':
						case "multiselect_method":							
						case "table_multiselect":							
							$cur_val=Request::get($_key,'');
							if (is_array($cur_val)) $cur_val=implode(";",$cur_val);
							break;
					}
					if($_val[0]=="password" && !$cur_val) continue;
					$up_sql.="INSERT INTO #__config VALUES ('".$activeTab."','".$_key."','".$db->doubleBaks($cur_val)."')";
					$up_sql.=" ON DUPLICATE KEY UPDATE `cfg_value`='".$db->doubleBaks($cur_val)."'".$db->getDelimiter()."\n";
				}
				$db->setQuery($up_sql);
				$sql_result = $db->query_batch(true,true);
				if ($sql_result) {
					$msg = 'Config saved';
					Event::raise('configuration.saved');
				}
				else $msg = 'Config save failed';
				$this->setRedirect("index.php?module=conf&view=config&config=".$activeTab,Text::_($msg));
			} else { $this->setRedirect("index.php"); }
		} else { $this->setRedirect("index.php"); }
	}

	public function showCladr()	{
		$this->showData();
	}
	
	public function showModules()	{
		$this->showData();
	}
	public function showPlugins() {
		$this->showData();
	}
	public function modifyDopfield() {
		$mdl = Module::getInstance();
		$reestr = $mdl->get('reestr');
		$reestr->set('task','saveDopfield');
		parent::modify();
	}
	public function saveDopfield(){
		if(!Request::getInt('psid')){
			$f_name=Request::getSafe("f_name");
			if(strlen($f_name)>3){
				if(substr($f_name,0,3)!="df_") $f_name="df_".DBUtil::cleanNameForDB($f_name);
				$_REQUEST["f_name"]=$f_name;
			} else return false;
		}
		parent::save();
		$tablename=Request::getSafe("p_f_table_select", Request::getSafe("f_table",""));
		$this->checkFieldsByTable(array($tablename));
	}
	public function modifyModule() {
		$mdl = Module::getInstance();
		$reestr = $mdl->get('reestr');
		$model = $this->getModel("modules");
		$psid = $this->getPsid();
		$page = Request::getInt('page', 1);
		$reestr->set('page', $page);
		$sort = Request::getSafe('sort');
		$reestr->set('sort', $sort);
		$orderby = Request::getSafe('orderby');
		$reestr->set('orderby', $orderby);
		$reestr->set('psid',$psid);
		$reestr->set('task','saveModule');
		$reestr->set('view','modules');
		$reestr->set('model',$model);
		$elem = $model->getElement();
		$view = $this->getView();
		$reestr->set("onCancelURL","index.php?module=conf&view=modules".'&sort='.$sort.'&page='.$page.'&orderby='.$orderby);
		$params = Params::parse($elem->m_config);
		$def_params = Module::getInstance($elem->m_name)->getParamsMask();
		$activeTab	= Request::getInt("activeTab",1);
		$view->assign('activeTab',$activeTab);
		$view->assign("params",$params);
		$view->assign("def_params",$def_params);
		$view->modify($elem);
	}
	public function saveModule() {
		$mdl = Module::getInstance();
		$moduleName	= $mdl->getName();
		$reestr = $mdl->get('reestr');

		$psid		= $this->getPsid();
		$multy_code = Request::getInt('multy_code',0);
		$layout		= Request::getSafe('layout');
		$page		= Request::getSafe('page');
		$sort		= Request::getSafe('sort');
		$orderby	= Request::getSafe('orderby');
		$is_apply	= Request::getSafe('apply');
		$activeTab	= Request::getInt("activeTab",1);
		$show_breadcrumb = Request::getInt('m_show_breadcrumb', 0);
		$incl_map = Request::getInt('m_incl_map', 0);
		$new_enabled = Request::getInt('m_enabled',0);
		$model = $this->getModel("modules");
		$view = $this->getView();
		$viewname = $view->getName();

		$reestr->set('multy_code',$multy_code);
		$reestr->set('view',$viewname);
		$reestr->set('psid',$psid);

		$params = Request::get("mod_param",array());
		$elem = $model->getElement($psid);
		$def_params = Module::getInstance($elem->m_name)->getParamsMask();
		$old_enabled = $elem->m_enabled;
		if($elem->m_name == siteConfig::$defaultModule && !$new_enabled) {
			$msg = Text::_("Impossible to disable main module");
			$is_apply = 1;
		} elseif(Module::isCoreModule($elem->m_name) && !$new_enabled) {
			$msg = Text::_("Impossible to disable core module");
			$is_apply = 1;
		} else {
			if($model->saveParams($psid, $def_params, $params) && $model->saveModule($psid, $show_breadcrumb, $incl_map, $new_enabled)) {
				$msg = Text::_("Save successfull");	
			} else {
				$msg = Text::_("Save unsuccessfull");
			}
		}
		if ($is_apply) $url='index.php?module='.$moduleName.'&view='.$viewname.'&layout='.$layout.'&task=modifyModule&psid='.$psid.'&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.$multy_code.'&activeTab='.$activeTab;
		else $url='index.php?module='.$moduleName.'&view='.$viewname.'&layout='.$layout.'&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.$multy_code;
		$this->setRedirect($url,$msg);
	}
	
	public function modifyPlugin() {
		$mdl = Module::getInstance();
		$reestr = $mdl->get('reestr');
		$model = $this->getModel();
		$arr_psid     = Request::get('cps_id', false);									// массив отмеченных галочкой элементов
		$psid         = Request::get('psid', false); 					// ид строки
		if(!$psid)  if($arr_psid&&is_array($arr_psid)&&count($arr_psid)>0) $psid = $arr_psid[0];
		$reestr->set('psid',$psid);
		$reestr->set('task','savePlugin');
		$reestr->set('view','plugins');
		$page = Request::getInt('page', 1);
		$reestr->set('page', $page);
		$sort = Request::getSafe('sort');
		$reestr->set('sort', $sort);
		$orderby = Request::getSafe('orderby');
		$reestr->set('orderby', $orderby);
		$reestr->set('model',$model);
		$elem = $model->getElement();
		$view = $this->getView();
		$reestr->set("onCancelURL","index.php?module=conf&view=plugins".'&sort='.$sort.'&page='.$page.'&orderby='.$orderby);
		$params = Params::parse($elem->p_params);
		$def_params = Plugin::getInstance($elem->p_path.".".$elem->p_name, 1)->getParamsMask();
		$activeTab	= Request::getInt("activeTab",1);
		$view->assign('activeTab',$activeTab);
		$view->assign("params",$params);
		$view->assign("def_params",$def_params);
		$view->modify($elem);
	}
	public function savePlugin() {
		$mdl = Module::getInstance();
		$moduleName	= $mdl->getName();
		$reestr = $mdl->get('reestr');

		$psid      = Request::get('psid', false);
		$multy_code = Request::get('multy_code',0);
		$layout		= Request::get('layout');
		$page		= Request::get('page', 1);
		$sort		= Request::get('sort');
		$orderby	= Request::get('orderby');
		$is_apply	= Request::get('apply',0);
		$activeTab	= Request::getInt("activeTab",1);
		$model = $this->getModel();
		$view = $this->getView();
		$viewname = $view->getName();

		$reestr->set('multy_code',$multy_code);
		$reestr->set('view',$viewname);
		$reestr->set('psid',$psid);

		$new_psid = $model->save();
		if($new_psid) {	
			$params = Request::get("plg_param",array());
			$elem = $model->getElement($new_psid);
			$def_params = Plugin::getInstance($elem->p_path.".".$elem->p_name, 1)->getParamsMask();
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
		
		if ($is_apply) $url='index.php?module='.$moduleName.'&view='.$viewname.'&layout='.$layout.'&task=modifyPlugin&psid='.$new_psid.'&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.$multy_code.'&activeTab='.$activeTab;
		else $url='index.php?module='.$moduleName.'&view='.$viewname.'&layout='.$layout.'&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.$multy_code;
		$this->setRedirect($url,$msg);
	}
	public function pi() {
		ob_start();
		phpinfo();
		$pinfo = ob_get_contents();
		ob_end_clean();
		$pinfo = preg_replace( '%^.*<body>(.*)</body>.*$%ms','$1',$pinfo);
		Portal::getInstance()->addStyleSheet('phpinfo.css');
		echo "<div id=\"phpinfo\">".$pinfo."</div>";
	}

	public function createmap() {
		$model = $this->getModel('sitemap');
		$msg=$model->createMaps(true,true);
		$url="index.php?module=conf&view=sitemap";
		$this->setRedirect($url,$msg);
	}
	public function newWidget() {
		$this->checkACL("viewConfWidgets");
		$widgets = Widget::getInstalledForFront();
		$view = $this->getView();
		if(count($widgets)){
			foreach ($widgets as $w){
				Text::parseWidget($w->w_name);
				$w->w_name_text=Text::_($w->w_name." widget")." (".$w->w_name.")";
			}
		}
		Util::sortStdClassArray($widgets, "w_name_text");
//		Util::showArray($widgets);
		$view->assign('widget_selector',HTMLControls::renderSelect('w_name','w_name', 'w_name', 'w_name_text', $widgets,0,0));
		$view->set('layout', 'new', true);
		$view->render();
	}
	public function saveNewWidget() {
		$this->checkACL("viewConfWidgets");
		$w_name = Request::getSafe("w_name","");
		if (!$w_name) $this->newWidget();
		else {
			Text::parseWidget($w_name);
			$model = Module::getInstance()->getModel();
			$psid=$model->saveNewWidget($w_name);
			if($psid) {
				$reestr = Module::getInstance()->get('reestr');
				$this->setRedirect("index.php?module=conf&view=widgets&task=modifyWidget&psid=".$psid);
			}
			else {
				$this->setRedirect("index.php?module=conf&view=widgets","Save unsuccessfull");
			}
		}
	}
	public function saveWidgetInstance() {
		$mdl		= Module::getInstance();
		$moduleName	= $mdl->getName();
		$reestr 	= $mdl->get('reestr');
		$model 		= $this->getModel();
		$viewname 	= $this->getView()->getName();
		$this->checkACL("view".ucfirst($moduleName).ucfirst($viewname));
		$psid 		= $this->getPsid();
		$multy_code	= Request::getInt('multy_code', 0);
		$layout		= Request::getSafe('layout');
		$page		= Request::getInt('page', 1);
		$sort		= Request::getSafe('sort');
		$orderby	= Request::getSafe('orderby');
		$is_apply	= Request::getSafe('apply',0);
		$activeTab	= Request::getInt("activeTab",1);
		$reestr->set('multy_code',$multy_code);
		$reestr->set('view',$viewname);
		$reestr->set('psid',$psid);
		$new_psid=$model->save();
		$w_name = Request::getSafe("aw_name","");
		$wp = Request::get("aw_config",array());
		if ($new_psid) {
			// Parameters
			$w_params = $model->getWidgetParams($w_name);
			// Access
			$w_access_all = Request::get("aw_access_all","off");
			if ($w_access_all == "off") {
				$w_access_arr = Request::get("aw_access",array());
				$w_access = "";
				foreach ($w_access_arr as $role=>$on) {
					$w_access .= "$role;";
				}
				$w_access = mb_substr($w_access,0,mb_strlen($w_access,DEF_CP) - 1,DEF_CP);
			}	else $w_access = "all";

			// Visibility
			$w_visible_all = Request::get("aw_visible_all","off");
			$w_visibility = "";
			if ($w_visible_all == "off") {
				$w_visible_except=Request::getInt("visible_except");
				if ($w_visible_except) $w_visibility="except;";
				$w_visibility_arr = Request::get("aw_visible_in",array());
				foreach ($w_visibility_arr as $w_vis) {
					$w_visibility .= "$w_vis;";
				}
				$w_visibility = mb_substr($w_visibility,0,mb_strlen($w_visibility,DEF_CP) - 1,DEF_CP);
			} else { $w_visibility = "all"; }
			$model->saveWidgetParams($new_psid,$wp,$w_params,$w_access,$w_visibility);
		}
		if($new_psid&&$w_name) Widget::cleanWidgetCache($new_psid,$w_name);
		if($new_psid) { $msg=Text::_("Save successfull"); $new_psid=urlencode($new_psid); }
		else { $msg=Text::_("Save unsuccessfull"); $new_psid=urlencode($psid); }
		if ($is_apply) $url='index.php?module='.$moduleName.'&view='.$viewname.'&layout='.$layout.'&task=modifyWidget&psid='.$new_psid.'&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.$multy_code.'&activeTab='.$activeTab;
		else $url='index.php?module='.$moduleName.'&view='.$viewname.'&layout='.$layout.'&sort='.$sort.'&page='.$page.'&orderby='.$orderby.'&multy_code='.$multy_code;
		$this->setRedirect($url,$msg);
	}
	public function modifyWidget() {
		$mdl = Module::getInstance();
		$reestr = $mdl->get('reestr');
		$model = $this->getModel();
		$reestr->set('view','widgets');
		$reestr->set('model',$model);
		$page = Request::getInt('page', 1);
		$reestr->set('page', $page);
		$sort = Request::getSafe('sort');
		$reestr->set('sort', $sort);
		$orderby = Request::getSafe('orderby');
		$reestr->set('orderby', $orderby);
		$reestr->set("onCancelURL","index.php?module=conf&view=widgets".'&sort='.$sort.'&page='.$page.'&orderby='.$orderby);
		$psid = $this->getPsid();
		$reestr->set('psid',$psid);
		$result = $model->getElement();
		$view = $this->getView();
		 
		$wp = Params::parse($result->aw_config);
		if(is_null($wp))	$wp=array();
		
		if ($result->aw_access == "all") {
			$w_access = $result->aw_access;
		}	else $w_access = explode(";",$result->aw_access);
		
		$w_params=$model->getWidgetParams($result->aw_name);
		$menus=new Menus();
		$m_items = $menus->getAllMenusItems();
		/* aw_visible */
		if ($result->aw_visible_in == "all") {
			$w_visible = $result->aw_visible_in;
		}	else $w_visible = explode(";",$result->aw_visible_in);
		if($model->hideWidgetContentParam($result->aw_name)){
			$result->aw_content = "";
			$model->meta->updateArrayField("input_view", "aw_content", 0);
		}
		$view->assign("w_visible", $w_visible);
		$view->assign("w_access", $w_access);
		$view->assign("w_params", $w_params);
		$view->assign("m_items", $m_items);
		$view->assign("requiredDisabledModules", $model->getRequiredDisabledModules($result->aw_name));
		if((isset($wp["Widget_ID"]) && !$wp["Widget_ID"]) || !isset($wp["Widget_ID"])) $wp["Widget_ID"]="widget_".$psid;
		$view->assign("wp", $wp);
		$activeTab	= Request::getInt("activeTab",1);
		$view->assign('activeTab',$activeTab);
		$reestr->set('task','saveWidgetInstance');
		$view->modify($result);
	}
	public function showWidgets() {
		$this->showData();
	}
	public function showDopfields_groups() {
		$this->showData();
	}
	public function showDopfields() {
		$this->showData();
	}
	public function showDopfields_choices() {
		$this->showData();
	}
	public function modifyDopfields_choices() {
		Module::getInstance()->get('reestr')->set('task','saveDopfields_choices');
		parent::modify();
	}
	public function saveDopfields_choices(){
		parent::save();
	}
	//****// Metadata //****//
	public function showMetadata()	{
		$model = $this->getModel();
		$view = $this->getView();
		$psid = $this->getPsid();
		switch($this->get('layout')){
			case "fields":
				if($psid){
					$result = $model->getFields($psid);
				} else {
					$url="index.php?module=conf&view=metadata";
					$this->setRedirect($url);
				}
				break;
			default:
				$result = $model->getHeaders();
				break;
		}
		$view->assign("result",$result);
	}
	public function modifyMetadata($hdr=false)	{
		$model = $this->getModel();
		$view = $this->getView();
		$psid = $this->getPsid();
		$view->setLayout("modify");
		$res = $model->getHeader($psid);
		if (!$res&&$hdr) $res=$hdr;
		$view->assign("res",$res);
		$view->assign("psid",$psid);
		$view->render();
	}
	public function modifyField($hdr=false)	{
		$model = $this->getModel();
		$view = $this->getView();
		$arr_psid  = Request::get('cps_id', false);		
		$psid      = Request::get('psid', 0); 					
		if(!$psid) if($arr_psid&&is_array($arr_psid)&&count($arr_psid)>0) $psid = $arr_psid[0];
		$view->setLayout("modify_field");
		$res = $model->getField($psid);
		if (!$res&&$hdr) $res=$hdr;
		$view->assign("res",$res);
		$view->assign("psid",$psid);
		$view->render();
	}
	public function delete(){
		parent::delete();
		$viewname = $this->getView()->getName();
		$arr_psid = Request::getSafe('cps_id', false);
		if($viewname=="dopfields" && count($arr_psid)){
			$model = $this->getModel("fields");
			$tables = $model->getTablesByFields($arr_psid);
			if(count($tables)) $this->checkFieldsByTable($tables);
		}
	}
	public function deleteMetadata(){
		$model = $this->getModel();
		$psid = $this->getPsid();
		if ($psid) $result=$model->deleteMetadata($psid); else $result=false;
		if($result) $msg=Text::_("Delete successfull"); else $msg=Text::_("Delete unsuccessfull");
		$url="index.php?module=conf&view=metadata";
		$this->setRedirect($url, $msg);
	}
	public function saveMetadata(){
		$model = $this->getModel();
		$hdr=$model->getHeaderFromRequest();
		if ((!$hdr->h_module)||(!$hdr->h_view)||((!$hdr->h_layout))) $this->modifyMetadata($hdr);
		else {
			$result=$model->saveHeader($hdr);
			if($result) { $msg=Text::_("Save successfull"); } 
			else { $msg=Text::_("Save unsuccessfull"); }
			$url="index.php?module=conf&view=metadata";
			$this->setRedirect($url,$msg);
		}
	}
	public function showRedirectLinks(){
		$this->showData();
	}
	public function showTmplzones(){
		$this->showData();
	}
	public function showMansitemap(){
		$this->showData();
	}
	
	public function checkTranslateList()
	{
		$html='';
		$modules = Module::getInstalledModules();
		$translator= new Translator();
		foreach( $modules as $module){
			$html.=$translator->checkModuleForTranslate($module);
		}
		echo $html;
	}
	
	public function checkTranslate()
	{
		if(!defined("_BARMAZ_TRANSLATE")) return false;
		$psid = $this->getPsid();
		$translator= new Translator();
		// выводим протокол проверки
		$model=$this->getModel();
		$moduleinfo=$model->getElement($psid);
		//Util::showArray($moduleinfo);
		$html=$translator->checkModuleForTranslate($moduleinfo->m_name);
		echo $html;
		
	}
}
?>