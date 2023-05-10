<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class catalogControllerdefault extends SpravController {
	
	protected function is_disabled() {
		if (catalogConfig::$ordersDisabled)	{
			if (Portal::getInstance()->get('option')=='ajax') exit(Text::_('This option is temporary disabled'));
			else $this->setRedirect(Router::_('index.php'),Text::_('This option is temporary disabled'));
		}
	}
	public function downloadFile(){
		$psid = Request::getInt('psid', false);
		$this->set("view","goods", true);
		$model = $this->getModel();
		$result=$model->getElement($psid);
		$redirect=Router::_('index.php?module=catalog&view=goods&layout=info&psid='.$psid);
		$msg=Text::_('Download file');
		$file = Request::getSafe('file', false);
		$fieldname = "df_".$file;
		$field_index = $model->meta->getFieldIndex($fieldname);
		$field_visible = $model->meta->view[$field_index] || $model->meta->input_view[$field_index];
		if ($file && $field_visible && isset($result->$fieldname) && $result->$fieldname){
			if ($this->checkACL("viewCatalogDownloadFiles", false)){
				$filepath=BARMAZ_UF_PATH;
				$filename=$result->$fieldname;
				if (is_file($filepath.$filename)){
					Util::download($filepath,$filename,$redirect,$msg);
					$params = array("psid"=>$psid, "product"=>$result, "field"=>$file);
					$data_voted=Event::raise("content.catalog_download", $params);
				} else $this->setRedirect($redirect,$msg);
			} else $this->setRedirect($redirect,Text::_("Download denied").". ".Text::_("You are not authorized"));
		} else $this->setRedirect($redirect,Text::_("Download denied"));
	}
	public function downloadDemo(){
		$psid = Request::getInt('psid', false);
		$this->set("view","goods", true);
		$model = $this->getModel();
		$result=$model->getElement($psid);
		$redirect=Router::_('index.php?module=catalog&view=goods&layout=info&psid='.$psid);
		$msg=Text::_('Download file');
		if ($result->g_file_demo&& ($result->g_type==3||$result->g_type==4)){
			if ($this->checkACL("viewCatalogDownloadDemo", false)){
				$filepath=BARMAZ_UF_PATH;
				$filename=$result->g_file_demo;
				if (is_file($filepath.$filename)){
					Util::download($filepath,$filename,$redirect,$msg);
					$params = array("psid"=>$psid, "product"=>$result);
					$data_voted=Event::raise("content.catalog_download", $params);
				} else $this->setRedirect($redirect,$msg);
			} else $this->setRedirect($redirect,Text::_("Download denied").". ".Text::_("You are not authorized"));
		} else $this->setRedirect($redirect,Text::_("Download denied"));
	}
	public function showVendors() {
		$page404 = Request::getBool('page404',false);
		if ($page404) { $this->setRedirect('index.php',Text::_('Vendor not found'),404); return; }
		$mdl = Module::getInstance();
		$reestr = $mdl->get('reestr');
		$reestr->set("needpan",false);
		$this->haltView();
		$psid = Request::getInt('psid', false); 					// ид строки
		if ($psid) {
			$this->getView()->setLayout("info");
			$this->showInfo();
		}
		else $this->showData();
	}
	public function showManufacturers() {
		$page404 = Request::getBool('page404',false);
		if ($page404) { $this->setRedirect('index.php',Text::_('Manufacturer not found'),404); return; }
		$mdl = Module::getInstance();
		$reestr = $mdl->get('reestr');
		$reestr->set("needpan",false);
		$this->haltView();
		$psid = Request::getInt('psid', false); 					// ид строки
		if ($psid) {
			$this->getView()->setLayout("info");
			$this->showInfo();
		}
		else $this->showData();
	}
	public function showGoods() {
		$page404	= Request::getBool('page404',false);
		if ($page404) { $this->setRedirect('index.php',Text::_('Goods not found'),404); return; }
		$tp = User::getInstance()->u_pricetype;
		$this->haltView();
		$save_filter = Request::getSafe("save_filter");
		$reset_filter = Request::getSafe("reset_filter");
		$without_parents = Request::getInt("without_parents", 0);
		if (($save_filter && !Request::getInt('psid', false)) || ($reset_filter && $without_parents)){
			Router::getInstance()->setVarsVal("psid", 0);
			Router::getInstance()->setVarsVal("multy_code", 0);
		}
		$mdl = Module::getInstance();
		$moduleName	= $mdl->getName();
		$reestr = $mdl->get('reestr');
		$reestr->set('controller', Request::getSafe("controller"));
		if($this->getConfigVal('enable_favourites_goods')) $favourites = Module::getHelper("favourites")->getFavourites(); else $favourites = array();
		if($this->getConfigVal('enable_compare_goods')) $compare = Module::getHelper("compare")->getCompare(); else $compare = array();
		switch($this->get('layout')) {
			case "info":
				$model = $this->getModel();
				$old_layout=$this->get("layout");
				$viewname = $this->getView()->getName();
				$this->checkACL("view".ucfirst($mdl->getName()).ucfirst($viewname));
				$multy_code   = Request::getInt('multy_code', false); 		// ид верхней группы
				$psid	=	$this->getPsid();
				
				$reestr->set('ajaxModify',false);
				$reestr->set('view',$viewname);
				$reestr->set('model',$model);
				$reestr->set("multy_code",$multy_code);
				$reestr->set('psid',$psid);
				$page			= Request::getInt('page', 1);
				$sort			= Request::getSafe('sort');
				$orderby		= Request::getSafe('orderby');
				$this->set("layout","default",true);
				$reestr->set('page', $page);
				$reestr->set('sort',$sort);
				$reestr->set('orderby',$orderby);
				/**************************************************/
				if($psid) {
					$group_code=$model->getFirstParent($psid);
					if (is_null($model->meta)) $model->loadMeta();
					$fbgListNames=$model->getGroupsFieldsArray($group_code);
//					Util::ddump($fbgListNames);
					$model->meta->cleanAddFields($fbgListNames);
//					Util::showArray($model->meta);
				}
				/**************************************************/
				$result = $model->getElementData($psid);
//				Util::showArray($result);
				/**************************************************/
				if(is_object($result)&&$result->g_type!=6){
					$view = $this->getView();
					if(isset($result->g_change_date)) Util::lastModifiedHeader($result->g_change_date);
					if(!isset($_COOKIE["BARMAZ_catalog_group"]) || !$_COOKIE["BARMAZ_catalog_group"]){
						$group_code=$model->getFirstParent($psid);
						Session::getInstance()->setcookie("BARMAZ_catalog_group", $group_code, 0,"/");
					}
					$complect = array();
					if ($result->g_type==5 && catalogConfig::$complectPriceAsGoodsSum){
						if($this->getConfigVal("show_kits_on_info_page")){
							$complect= $model->getComplectSet($result);
						}
						$model->updateComplectPrice($result, $complect);
					}
					if((isset($model->meta)&&($model->meta))) {
						if(!catalogConfig::$hide_prices)	$model->meta->updateArrayField('input_view','g_price_'.$tp,1);
						if(!siteConfig::$use_points_system)	$model->meta->updateArrayField('input_view','g_points',0);
						else	$model->meta->updateArrayField('input_view','g_points',0);
						if(catalogConfig::$show_stock)	$model->meta->updateArrayField('input_view','g_stock',1);
						else	$model->meta->updateArrayField('input_view','g_stock',0);
					}
					/* new canonical rules start */
					if(seoConfig::$sefMode){
						$canonical_group_alias = false;
						$canonical_group = $this->getModel('goods')->loadGroup($result->g_main_grp);
						if(is_object($canonical_group) && $canonical_group->ggr_alias){
							$canonical_group_alias = $canonical_group->ggr_alias;
						} else {
							$canonical_group = $this->getModel('goods')->getFirstParent($result->g_id);
							if(is_object($canonical_group) && $canonical_group->ggr_alias){
								$canonical_group_alias = $canonical_group->ggr_alias;
							}
						}
						if($canonical_group_alias){
							$canonical_link = Router::_("index.php?module=catalog&view=goods&layout=info&psid=".$result->g_id."&alias=".$result->g_alias."&ggr_alias=".$canonical_group_alias);
						} else {
							$canonical_link = Router::_("index.php?module=catalog&view=goods&layout=info&psid=".$result->g_id."&alias=".$result->g_alias);
						}
						Portal::getInstance()->addCustomTag("<link rel=\"canonical\" href=\"".$canonical_link."\" />");
						$up_group = Request::getInt("ggr_id"); // from router
						if(!$up_group && $canonical_group_alias) { 
							$this->setRedirect(Router::_("index.php?module=catalog"),'','404'); 
							return; 
						} else $this->createUpBreadcrumb($view, $up_group);
					} else {
						$up_group = Request::getInt("ggr_id", $result->g_main_grp);
						if(!$up_group) $up_group=$this->getModel('goods')->getFirstParent($result->g_id);
						if ($up_group) $this->createUpBreadcrumb($view, $up_group);
						// Canonical check
						if($up_group && $result->g_main_grp && $up_group!=$result->g_main_grp){
							$canonical_group_alias = $this->getModel('goods')->loadGroup($result->g_main_grp)->ggr_alias;
							if($canonical_group_alias){
								$canonical_link = Router::_("index.php?module=catalog&view=goods&layout=info&psid=".$result->g_id."&alias=".$result->g_alias."&ggr_alias=".$canonical_group_alias);
								Portal::getInstance()->addCustomTag("<link rel=\"canonical\" href=\"".$canonical_link."\" />");
							}
						}
					}
					/* new canonical rules stop */
					$limitBread=$this->getConfigVal('breadcrumb_lenght');
					$text_breadcrumb=$result->g_name;
					if($limitBread>0 && mb_strlen($text_breadcrumb)>$limitBread) $text_breadcrumb=mb_substr($text_breadcrumb,0,$limitBread)."...";
					$view->addBreadcrumb($text_breadcrumb,'#');
//					$basket= Basket::getInstance();
					if($result->g_meta_keywords) Portal::getInstance()->setMeta("keywords",$result->g_meta_keywords);
					else Portal::getInstance()->setMeta("keywords",$result->g_name);
					if($result->g_meta_description) Portal::getInstance()->setMeta("description",$result->g_meta_description);
					else Portal::getInstance()->setMeta("description",$result->g_name);
					/*********************************************************************************/
					if($result->g_meta_title) $g_meta_title = $result->g_meta_title;
					elseif($model->meta->view[$model->meta->getFieldIndex("g_name")]) $g_meta_title = $result->g_name;
					elseif($model->meta->view[$model->meta->getFieldIndex("g_fullname")]) $g_meta_title = $result->g_fullname;
					else $g_meta_title = "";
					Portal::getInstance()->setTitle($g_meta_title);
					/*********************************************************************************/
					$view->setLayout($old_layout);
					if ($result->g_flypage && $result->g_flypage!='info') $view->setLayout($result->g_flypage);
					$quadro_by_row=$this->getConfigVal('quadro_by_row');
					$view->assign("quadro_by_row", $quadro_by_row);
					$view->assign("comm",$this->initComments($psid));
					$view->assign("images",$model->getImages($psid));
					$options=$model->getOptions($psid);
					$view->assign("complect", $complect);
					$view->assign("options",$options);
					$view->assign("favourites",$favourites);
					$view->assign("compare",$compare);
					$view->assign("enable_favourites",$this->getConfigVal('enable_favourites_goods'));
					$view->assign("enable_compare",$this->getConfigVal('enable_compare_goods'));
					$view->assign("discounts", $model->getDiscounts(array($psid)));
					$view->assign("extendedPrices", $model->getExtendedPrices(array($psid)));
					$view->assign("analogs",$model->getAnalogs($psid));
					$view->assign("additionals",$model->getAdditional($psid));
					$view->assign("videos",$model->getVideos($psid));
					$view->assign("video_width",$this->getConfigVal("video_width"));
					$view->assign("video_height",$this->getConfigVal("video_height"));
					$view->assign("canDownloadDemo",$this->checkACL("viewCatalogDownloadDemo", false));
					$view->assign("canDownloadFiles",$this->checkACL("viewCatalogDownloadFiles", false));
					Event::raise("content.catalog.renderInfo.before", array("psid"=>$psid), $result);

					// @FIXME DEPRECATED REMOVE LATER
					Event::raise("content.catalog_show_info", array("psid"=>$psid), $result);
					
					$view->renderInfo($model->meta,$result);
				} else $this->setRedirect(Router::_("index.php?module=catalog"),'','404');
				break;
			default:
				$view = $this->getView();
				$uid = User::getInstance()->getID(true);
				// if(!$uid) $uid=session_id();
				/***********************************************************************************/
				$model = $this->getModel();
				$model->loadMeta();
				$g_name_visible = $model->meta->view[$model->meta->getFieldIndex("g_name")];
				$g_fullname_visible = $model->meta->view[$model->meta->getFieldIndex("g_fullname")];
				if($g_name_visible) $search_name_field = "g_name";
				elseif($g_fullname_visible) $search_name_field = "g_fullname";
				else $search_name_field = "";
				/***********************************************************************************/
				if(intval($this->getConfigVal('search_mode'))>0) $kwds = Request::getSafe("kwds");
				else $kwds = "";
				if($kwds) {
					$reestr->set('ref_appendix','kwds='.urlencode($kwds));
					$_SESSION[$moduleName][$this->getView()->getName().".".$this->get('layout').".0"]['add_filter_hidden_fields']=array("kwds"=>$kwds);
					switch($this->getConfigVal('search_mode')){
						case "1":
							$model->meta->custom_sql.=" AND (c.g_sku LIKE '%".$kwds."%'".( $search_name_field ? " OR c.".$search_name_field." LIKE '%".$kwds."%'" : "" ).")";
							break;
						case "2":
							$model->meta->custom_sql.=" AND c.g_sku LIKE '%".$kwds."%'";
							break;
						case "3":
							if($search_name_field) $model->meta->custom_sql.=" AND c.".$search_name_field." LIKE '%".$kwds."%'";
							break;
					}
				} else {
					$_SESSION[$moduleName][$this->getView()->getName().".".$this->get('layout').".0"]['add_filter_hidden_fields']=array();
				}
				
				
				/***********************************************************************************/
				// unset($_SESSION['filter_ext_mode'][$moduleName][$uid][$view->getName().".".$this->get('layout').".0"]); die();
				// Util::pre("catalog controller showData isset filter_ext_mode ".isset($_SESSION['filter_ext_mode'][$moduleName][$uid][$view->getName().".".$this->get('layout').".0"]));
				if($this->getConfigVal('Default_ext_filter')){
					if(!isset($_SESSION['filter_ext_mode'][$moduleName][$uid][$view->getName().".".$this->get('layout').".0"])){
						$_SESSION['filter_ext_mode'][$moduleName][$uid][$view->getName().".".$this->get('layout').".0"] = 1;
					}
				}
				/***********************************************************************************/
				
				
				/***********************************************************************************/
				$flt_ext_mode=Request::getInt('filter_ext_mode',-1);
				if($flt_ext_mode===1 || $flt_ext_mode===0 ) $_SESSION['filter_ext_mode'][$mdl->getName()][$uid][$view->getName().".".$reestr->get('layout',$this->get('layout')).".".(defined("_ADMIN_MODE") ? "1" : "0")]=$flt_ext_mode;
				/***********************************************************************************/
				$psid         = Request::getInt('psid', false); 					// ид строки
				$multy_code   = Request::getInt('multy_code', false); 					// ид верхней группы
				if(!$psid && !$multy_code) 	$psid	=	$this->getPsid();
				if(!$multy_code)	$multy_code=$psid;
				elseif(!$psid) $psid=$multy_code;
				$grp = $model->loadGroup($multy_code);
				if(isset($grp->ggr_change_date)) Util::lastModifiedHeader($grp->ggr_change_date);
				Session::getInstance()->setcookie("BARMAZ_catalog_group", $multy_code, 0,"/");
				if (is_object($grp) || !$psid){
					if (is_object($grp)) {
						if ($grp->ggr_meta_keywords) Portal::getInstance()->setMeta("keywords",$grp->ggr_meta_keywords);
						if ($grp->ggr_meta_description) Portal::getInstance()->setMeta("description",$grp->ggr_meta_description);
						if ($grp->ggr_meta_title) Portal::getInstance()->setTitle($grp->ggr_meta_title);
						else Portal::getInstance()->setTitle($grp->ggr_name);
					}
					$show_goods_from_subgroups=intval($this->getConfigVal('show_goods_from_subgroups'));
					if($show_goods_from_subgroups){
						$model->meta->link_with_childs = true;
					}
					/*************************************************************/
					//if($psid) {
						if (is_null($model->meta)) $model->loadMeta();
						$arr_parents = array(); 
						$arr_childs = array();
						$fbgListNames=$model->getGroupsFieldsArray($psid, $show_goods_from_subgroups, $arr_parents, $arr_childs);
//						Util::ddump($fbgListNames);
						$model->meta->cleanAddFields($fbgListNames);
//						Util::showArray($model->meta);
					//}
					/*************************************************************/
					/*
					$fbgListNames=$model->getGroupsFieldsArray($psid, true);
					for ($i = 1; $i <= count($model->meta->field); $i++){
						if($model->meta->is_add_custom[$i]){
							if(!array_key_exists($model->meta->field[$i], $fbgListNames)){
								$model->meta->updateArrayField('filter', $model->meta->field[$i], 0);
								$model->meta->updateArrayField('filter_ext', $model->meta->field[$i], 0);
							}
						}
					}
					*/
					/***********************************************************************************/
					if($this->getConfigVal('reset_filter_on_category_changed')){
						if(!$save_filter && !$reset_filter && !$without_parents){
							$filter_prev_group = isset($_SESSION['filter_prev_group'][$mdl->getName()][$uid]) ? intval($_SESSION['filter_prev_group'][$mdl->getName()][$uid]) : 0;
							$_SESSION['filter_prev_group'][$mdl->getName()][$uid]=$psid;
							/*
							Util::pre($filter_prev_group, $multy_code, $psid);
							Util::showArray($arr_parents, "arr_parents");
							Util::showArray($arr_childs, "arr_childs");
							*/
							if($filter_prev_group != $psid){
								if(array_key_exists($filter_prev_group, $arr_childs)){
									if(!$show_goods_from_subgroups){
										$flt=new SpravFilter();
										$flt->resetFilterString($mdl->getName(), $view->getName(), $reestr->get('layout',$this->get('layout')));
									}
								//	Util::pre("PREVIOUS WAS CHILD");
								} elseif(array_key_exists($filter_prev_group, $arr_parents)){
									if(!$show_goods_from_subgroups){
										$flt=new SpravFilter();
										$flt->resetFilterString($mdl->getName(), $view->getName(), $reestr->get('layout',$this->get('layout')));
									}
								//	Util::pre("PREVIOUS WAS PARENT");
								} else {
									// DO WE NEED TO SET $_REQUEST["reset_filter"] ????????????????????
									$flt=new SpravFilter();
									$flt->resetFilterString($mdl->getName(), $view->getName(), $reestr->get('layout',$this->get('layout')));
								//	Util::pre("PREVIOUS WAS NOT FROM BRANCH, LET'S RESET FILTER");
								}
							}
						}
					}
					/***********************************************************************************/
					$childs=$model->loadChildGroups($multy_code);
					$reestr->set('needpan',false);
					$reestr->set("multy_code",$multy_code);
					$reestr->set('psid',$psid);
					$reestr->set('page', Request::getInt('page', 1));
					$_default_sort=""; $_default_orderby="";
					if(is_object($grp) && $grp->ggr_default_sorting) $ggr_default_sorting=$grp->ggr_default_sorting;
					else {
						$ggr_default_sorting=$this->getConfigVal("default_goods_sorting");
						if($ggr_default_sorting=="0") $ggr_default_sorting="";
					}
					if($ggr_default_sorting) {
						$_default_sorting = explode(".", $ggr_default_sorting);
						if(count($_default_sorting)==2){
							$_default_sort=$_default_sorting[0]; $_default_orderby=$_default_sorting[1];
						}
					}
					if(!$g_name_visible && $_default_sort == "g_name") $_default_sort ="";
					if(!$g_fullname_visible && $_default_sort == "g_fullname") $_default_sort ="";
					$reestr->set('sort',Request::getSafe("sort", $_default_sort));
					$reestr->set('orderby',Request::getSafe("orderby", $_default_orderby));
					for($iii=1; $iii <= 5; $iii++) {
						$model->meta->updateArrayField('view','g_price_'.$iii, 0);
						$model->meta->updateArrayField('filter','g_price_'.$iii, 0);
						$model->meta->updateArrayField('filter_ext','g_price_'.$iii, 0);
					}
					if(!catalogConfig::$hide_prices)	{
						$model->meta->updateArrayField('view','g_price_'.$tp,1);
						$model->meta->updateArrayField('filter','g_price_'.$tp,1);
						$model->meta->updateArrayField('filter_ext','g_price_'.$tp,1);
					}
					if(!siteConfig::$use_points_system)	$model->meta->updateArrayField('view','g_points',0);
					else $model->meta->updateArrayField('view','g_points',0);
					if(catalogConfig::$show_stock)	$model->meta->updateArrayField('view','g_stock',1);
					else $model->meta->updateArrayField('view','g_stock',0);
					$filter_vendor_id=Session::getVar("filter.vendor");
					$filter_manufacturer_id=Session::getVar("filter.manufacturer");
					if (!is_null($filter_vendor_id)&&$filter_vendor_id) {
						$vnd=$model->getVendor($filter_vendor_id);
						if(is_object($vnd)){
							$model->meta->custom_sql.=" AND c.g_vendor=".$filter_vendor_id;
							$view->assign("filter_vendor", $vnd);
							//$view->assign("filter_vendor_id", $filter_vendor_id);
						} else Session::unetVar("filter.vendor");
					}
					if (!is_null($filter_manufacturer_id)&&$filter_manufacturer_id) {
						$mf=$model->getManufacturer($filter_manufacturer_id);
						if(is_object($mf)){
							$model->meta->custom_sql.=" AND c.g_manufacturer=".$filter_manufacturer_id;
							$view->assign("filter_manufacturer", $mf);
							//$view->assign("filter_manufacturer_id", $filter_manufacturer_id);
						} else Session::unsetVar("filter.manufacturer");
					}
					/*
					if($kwds){
						switch($this->getConfigVal('search_mode')){
							case "1":
								$model->meta->custom_sql.=" AND (c.g_sku LIKE '%".$kwds."%'".( $search_name_field ? " OR c.".$search_name_field." LIKE '%".$kwds."%'" : "" ).")";
								break;
							case "2":
								$model->meta->custom_sql.=" AND c.g_sku LIKE '%".$kwds."%'";
								break;
							case "3":
								if($search_name_field) $model->meta->custom_sql.=" AND c.".$search_name_field." LIKE '%".$kwds."%'";
								break;
						}
					}
					*/
					$result = $model->getData();
					if(catalogConfig::$complectPriceAsGoodsSum){
						if(count($result)){
							foreach($result as $res){
								$model->updateComplectPrice($res);
							}
						}
					}
					$this->createUpBreadcrumb($view, $psid);
					if (!is_null($grp)&&$grp->ggr_list_tmpl && $grp->ggr_list_tmpl!='defl') $view->setLayout($grp->ggr_list_tmpl);
					$show_filter_button=$this->getConfigVal('show_filter_button');
					$show_sort_links=$this->getConfigVal('show_sort_links');
					$quadro_by_row=$this->getConfigVal('quadro_by_row');
					if(count($result)){ 
						$view->assign("options_gids", $model->haveOptions(array_keys($result)));
						$view->assign("discounts", $model->getDiscounts(array_keys($result)));
					} else {
						$view->assign("options_gids", array());
						$view->assign("discounts", array());
					}
					$view->assign("favourites",$favourites);
					$view->assign("compare",$compare);
					$view->assign("enable_favourites",$this->getConfigVal('enable_favourites_goods'));
					$view->assign("enable_compare",$this->getConfigVal('enable_compare_goods'));
					$view->assign("quadro_by_row", $quadro_by_row);
					$view->assign("grp",$grp);
					$view->assign("show_filter_button",$show_filter_button);
					$view->assign("show_sort_links",$show_sort_links);
					$view->assign("multy_code",$multy_code);
					$view->assign("childs",$childs);
					$view->assign("kwds",$kwds);
					$view->assign("g_name_visible",$g_name_visible);
					$view->assign("g_fullname_visible",$g_fullname_visible);
					Event::raise("content.catalog.renderSprav.before", array("psid"=>$psid), $result);
					$view->renderSprav($model->meta,$result);
				} else  {
					$this->setRedirect("index.php?module=catalog","",'404');
				}
				break;
		}
	}
	/* используется для вывода информационной страницы без участия модели */
	public function showPage(){
		$view = $this->getView();
		$layout=$this->get('layout');
		$view->setLayout("default");
		$view->setLayout($layout);
		$view->render();
	}
	// Показываем корзину
	public function ajaxShowBasket() {
		$this->is_disabled();
		$html="<div class=\"full_basket_ajax container\"><div class=\"full_basket\">";
		$html.=Basket::getInstance()->modifyBasket(true, false, false);
		$html.="</div></div>";
		echo $html;
	}
	public function ajaxAddBasketPosition(){
		$answer=array("result"=>"FAILED", "message"=>Text::_("Failed to add product to basket").".", "redirect"=>"");
		$this->is_disabled();
		if(!User::checkFloodPoint(3)){
			$psid	=	$this->getPsid();
			$quantity = Request::getFloat('quantity', catalogConfig::$quantity_digits);
			if($quantity>0){
				$optArr = json_decode(Request::get('optArr',""),true);
				$newOptArr=array();
				if(is_array($optArr) && count($optArr)){
					foreach ($optArr as $oa){
						$opt_id=intval($oa['opt_id']);
						$val_id=(isset($oa['val_id']) ? intval($oa['val_id']) : 0);
						$newOptArr[$opt_id][$val_id]['quantity']=floatval($oa['quantity']);
						$newOptArr[$opt_id][$val_id]['value']=(isset($oa['value']) ? htmlspecialchars($oa['value']) : "");
						$newOptArr[$opt_id][$val_id]['val_id']=$val_id;
					}
				}
				if(count($_FILES)){
					$dir_name="orders".DS.Basket::getInstance()->getBasketHash();
					foreach ($_FILES as $opt=>$file){
						$opt_id=intval(str_replace("option_", "", $opt));
						$new_file=Files::uploadTempFile($opt, $dir_name);
						if(!$new_file) {
							echo Text::_("Failed to upload file"); 
							return; 
						}
						$newOptArr[$opt_id][0]['quantity']=1;
						$newOptArr[$opt_id][0]['value']=$new_file['filename'];
						$newOptArr[$opt_id][0]['val_id']=0;
					}
				}
				if (Basket::getInstance()->addBasketPosition($psid, $quantity, $newOptArr)) { 
					$answer["result"]="OK"; 
					$answer["message"]=Text::_("Product added to basket");
					// ссылку перехода даем всегда и проверяем на стороне браузера, ибо не критично
					$answer["redirect"]=Router::_("index.php?module=catalog&view=orders&layout=basket");
				} else $answer["message"].=" ".(Basket::getInstance()->basket_message ? "\n".Basket::getInstance()->basket_message : "");
			} // else не нужен, выводим дефолтное сообщение
		} else  $answer["message"].=" ".Text::_("Flood found");
		echo json_encode($answer);
	}
	public function ajaxUpdateBasketPosition() {
		$this->is_disabled();
		$psid = $this->getPsid();
		$quantity = Request::getFloat('quantity', catalogConfig::$quantity_digits);
		if ($psid) Basket::getInstance()->updateBasketPosition($psid,$quantity);
		echo Basket::getInstance()->modifyBasket(true, false, false);
	}
	public function ajaxDeleteBasketPosition() {
		$this->is_disabled();
		$psid = $this->getPsid();
		if ($psid) Basket::getInstance()->deleteBasketPosition($psid);
		echo Basket::getInstance()->modifyBasket(true, false, false);
	}
	public function ajaxgetMiniBasket() {
		$this->is_disabled();
		echo Basket::getInstance()->showMini();
	}
	protected function printOrder($order_id, $order_hash) {
		$this->is_disabled();
		Portal::getInstance()->changeTemplateFile('print');
		$this->renderOrder($order_id, $order_hash, 1, 0);
	}
	protected function viewOrder($order_id, $order_hash) {
		$this->is_disabled();
		$view = $this->getView();
		if (!Portal::getInstance()->inPrintMode()){
			$view->addBreadcrumb(Text::_('Cabinet'),Router::_("index.php?module=user&view=panel"));
			if(User::getInstance()->isLoggedIn()) $view->addBreadcrumb(Text::_('Orders'),Router::_("index.php?module=catalog&view=orders"));
			$view->addBreadcrumb(Text::_('Order'),"#");
		}
		$this->renderOrder($order_id, $order_hash);
	}
	protected function renderOrder($order_id, $order_hash="", $override_css=0, $controls=1, $_echo=1) {
		$view = $this->getView();
		$model=$this->getModel("orders");
		if ($order_id) {
			$order=$model->getOrder($order_id, $order_hash);
			if ($order) $order_items=$model->getOrderItems($order_id);
			else { 
				$order=false;
				$order_items=false;
			}
		} else { 
			$order=false;
			$order_items=false;
		}
		if ($order) {
			$payment=catalogPayment::getPaymentClass($order->o_pt_id);
			$delivery=catalogDelivery::getDeliveryClass($order->o_dt_id);
			$params = array("order"=>$order);
			Event::raise("content.catalog_order_render", $params);
		} else { 
			$delivery=false; 
			$payment=false;
		}
		$view->assign("override_css",$override_css);
		$view->assign("dt_class", $delivery );
		$view->assign("pt_class", $payment );
		$view->assign("order", $order );
		$view->assign("controls", $controls );
		$view->assign("order_items", $order_items );
		if (!$_echo){
			ob_start();
			$view->render();
			$html=ob_get_contents();
			ob_end_clean();
			$this->haltView();
			return $html;
		}
	}
	protected function payOrder($order_id, $order_hash) {
		$this->is_disabled();
		$view = $this->getView();
		if (!Portal::getInstance()->inPrintMode()){
			$view->addBreadcrumb(Text::_('Cabinet'),Router::_("index.php?module=user&view=panel"));
			if(User::getInstance()->isLoggedIn()) $view->addBreadcrumb(Text::_('Orders'),Router::_("index.php?module=catalog&view=orders"));
			$view->addBreadcrumb(Text::_('Order payment'),"#");
		}
		$model=$this->getModel();
		$user_id=Session::restoreToken(Request::getSafe("BARMAZ_TOKEN",""));
		if(Request::getSafe('mode','show')=="show" && !User::getInstance()->isLoggedIn()){
			$order=$model->getOrder($order_id, $order_hash);
		} else {
			$order=$model->getAbstractOrder($order_id);
		}
		if ($order) {
			$order_items=$model->getOrderItems($order_id);
			$pt_class=catalogPayment::getPaymentClass($order->o_pt_id);
			if ($pt_class) $pt_class->assignOrder($order,$order_items);
		}	else {
			$pt_class=false;
		}
		$view->assign("pt_class", $pt_class );
	}
	protected function selectBasket() {
		$this->is_disabled();
		$vendor=Request::getInt("vendor",0);
		Session::setVar("basket_vendor", $vendor);
		// без вендора это выбор корзины и ее изменение
		if (!$vendor) {
			Session::unsetVar("order_without_registration");
			$view = $this->getView();
			$model=$this->getModel();
			$view->addBreadcrumb(Text::_('Cabinet'),Router::_("index.php?module=user&view=panel"));
			if(User::getInstance()->isLoggedIn()) $view->addBreadcrumb(Text::_('Orders'),Router::_("index.php?module=catalog&view=orders"));
			$view->addBreadcrumb(Text::_('Order registration'),"#");
		} else {
			$this->submitOrder();
		}
	}

	protected function submitOrder() {
		$this->is_disabled();
		$err_found=false;
		$err_message=array();
		$view = $this->getView();
		$model=$this->getModel();
		$payment_type = Request::getInt('payment_type',0);
		$delivery_type = Request::getInt('delivery_type',0);
		
		$without_registration = Request::getInt('without_registration', intval(Session::getVar("order_without_registration")));
		if(catalogConfig::$ordersWithoutRegistration && !backofficeConfig::$cryptoUserData && $without_registration){
			Session::setVar("order_without_registration", 1);
		} else $without_registration = 0;

		if (!User::getInstance()->isLoggedIn() && !$without_registration){
			$view->setLayout("signin");
		} else {
			if(Request::getInt('i_agree', 0) && Request::getInt('privacy_policy_agree', 0)){
				if ($payment_type && $delivery_type) {
					$payment = catalogPayment::getPaymentClass($payment_type);
					$delivery = catalogDelivery::getDeliveryClass($delivery_type);
					if ($payment && $delivery){
						if($delivery->checkData($err_message) && $payment->checkData($err_message) && $this->checkOrderData($err_message)){
							$delivery_data=$delivery->save();
							$payment_data=$payment->save();
							$userdata=array();
							if ($this->getConfigVal("require_person")) $userdata["userdata_person"]=Request::getSafe("userdata_person","");
							if ($this->getConfigVal("require_email")) $userdata["userdata_email"]=Request::getSafe("userdata_email","");
							if ($this->getConfigVal("require_phone")) $userdata["userdata_phone"]=Request::getSafe("userdata_phone","");
							if($without_registration) $order_hash=md5("9658".time()."040".rand(1,1000));
							else $order_hash="";
							if ($model->saveOrder($payment, $delivery, $userdata, $order_hash)) {
								$order_id=Basket::getInstance()->order_id;
								$vendor_id=Basket::getInstance()->order_vendor;
								$this->notifyOrder($order_id, $order_hash, $userdata, $vendor_id, true);
								$this->haltView();
								Basket::getInstance()->cleanBasket();
								Session::unsetVar("order_without_registration");
								if($without_registration) $this->setRedirect("index.php?module=catalog&view=orders&layout=payment&order_id=".$order_id."&order_hash=".$order_hash, Basket::getInstance()->order_message);
								else $this->setRedirect("index.php?module=catalog&view=orders&layout=payment&order_id=".$order_id, Basket::getInstance()->order_message);
							} else {
								$this->setRedirect('index.php', Basket::getInstance()->order_message);
							}
						} else $err_found=true;
					} else $err_found=true;
				} else $err_found=true;
			} else $err_found=true;
		} 
		if ($err_found){
			$view->setLayout("new");
			$delivery_form="";
			$pt_list=$model->getPaymentTypes();
			if (count($pt_list)==1) $payment_type=$pt_list[0]->pt_id;
			elseif (count($pt_list)==0) $err_message[]=Text::_("Orders unavailable now");
			elseif(!$payment_type) $payment_type=$pt_list[0]->pt_id;
			if ($payment_type) {
				$count_vendor_goods=count(Basket::getInstance()->calculateVendor());
				if($count_vendor_goods){
					$vendor_id=Session::getVar("basket_vendor");
					if(!$vendor_id) $vendor_id = catalogConfig::$default_vendor;
					$vendor=Vendor::getInstance()->getVendor($vendor_id);
					if($vendor->v_minimum_basket && $vendor->v_minimum_basket>Basket::getInstance()->total){
						$not_enough_sum = true;
					} else {
						$not_enough_sum = false;
					}
					$order_weight=Basket::getInstance()->weight;
					$order_total=Basket::getInstance()->total;
					$dt_list=$model->getDeliveryTypes($payment_type,$order_weight,$order_total);
					if (count($dt_list)==1) $delivery_type=$dt_list[0]->dt_id;
					elseif (count($dt_list)==0) $err_message[]=Text::_("Orders unavailable now");
					elseif(!$delivery_type) $delivery_type=$dt_list[0]->dt_id;
					if ($delivery_type) $data_form=$this->renderUserDataForm($payment_type,$delivery_type);
					$view->addBreadcrumb(Text::_('Cabinet'),Router::_("index.php?module=user&view=panel"));
					if(User::getInstance()->isLoggedIn()) $view->addBreadcrumb(Text::_('Orders'),Router::_("index.php?module=catalog&view=orders"));
					$view->addBreadcrumb(Text::_('Order registration'),"#");
					$view->assign("not_enough_quantity", Basket::getInstance()->not_enough_quantity);
					$view->assign("not_enough_sum", $not_enough_sum);
					$view->assign("delivery_form", $data_form); // название во вьюхе не меняем, иначе полетит у тех у кого свои шаблоны заказа
					$view->assign("err_message", implode(", ", $err_message));
					$view->assign("pt_list", $pt_list);
					$view->assign("dt_list", $dt_list);
					$view->assign("pt_selected", $payment_type);
					$view->assign("dt_selected", $delivery_type);
				} else {
					Session::unsetVar("basket_vendor");
				}
				$view->assign("count_vendor_goods", $count_vendor_goods);
			} else $dt_list="";
		}
	}
	protected function getOrderData($order_id, $order_hash="") {
		$model=$this->getModel("orders");
		$order_items = false;
		if ($order_id) {
			$order=$model->getOrder($order_id, $order_hash);
			if ($order) $order_items=$model->getOrderItems($order_id);
			else {
				$order=false;
				$order_items=false;
			}
		} else {
			$order=false;
			$order_items=false;
		}
		if ($order) {
			$payment=catalogPayment::getPaymentClass($order->o_pt_id);
			$delivery=catalogDelivery::getDeliveryClass($order->o_dt_id);
			$params = array("order"=>$order);
			Event::raise("content.catalog_order_render", $params);
		} else {
			$delivery=false;
			$payment=false;
		}
		if (is_object($order) && ($order_items) && count($order_items)) {
			$result["delivery"] = $delivery;
			$result["payment"] = $payment;
			$result["order"] = $order;
			$result["order_items"] = $order_items;
		} else {
			$result = false;
		}
		return $result;
	}
	protected function renderOrderEmail($data, $template) {
		$this->set('layout', $template, true);
		$view = $this->getView();
		$view->resetRenderFlag();
		$view->setLayout($template);
		$view->assign("dt_class", $data["delivery"] );
		$view->assign("pt_class", $data["payment"] );
		$view->assign("order", $data["order"] );
		$view->assign("order_items", $data["order_items"] );
		ob_start();
		$view->render();
		$this->haltView();
		$html=ob_get_contents();
		ob_end_clean();
		return $html;
	}
	protected function notifyOrder($order_id, $order_hash, $userdata, $vendor_id, $notify_admin=false, $data_override4loggedin=true){
		$view = $this->getView();
		$model=$this->getModel("orders");
		$data = $this->getOrderData($order_id, $order_hash);
		if(is_array($data)){
			$text = $this->renderOrderEmail($data, "order_email4admin");
			if ($notify_admin && catalogConfig::$catalogAdminEmail){
				$to=catalogConfig::$catalogAdminEmail;
				if($this->getConfigVal('sms2admin')) $admin_phone=soConfig::$Phone; else $admin_phone="";
				$theme=Text::_("Order from site")." № ".$order_id." ".Text::_("from")." ".Date::GetdateRus(Date::todaySQL());
				aNotifier::addToQueue($to, $theme, $text, "html", $admin_phone);
			}
			if ($vendor_id) { // напоминание вендору
				$vendor=Vendor::getInstance()->getVendor($vendor_id);
				if($this->getConfigVal('sms2vendor')) $vendor_phone=$vendor->v_contact_phone; else $vendor_phone="";
				if ($vendor_phone ||(isset($vendor->v_contact_email) && $vendor->v_contact_email)){
					$to=$vendor->v_contact_email;
					$theme=Text::_("Order from site")." № ".$order_id." ".Text::_("from")." ".Date::GetdateRus(Date::todaySQL());
					aNotifier::addToQueue($to, $theme, $text, "html", $vendor_phone);
				}
			}
			if (catalogConfig::$multy_vendor && $vendor_id!=catalogConfig::$default_vendor) {		// напоминание основному вендору вендору
				$vendor=Vendor::getInstance()->getVendor(catalogConfig::$default_vendor);
				if($this->getConfigVal('sms2vendor')) $vendor_phone=$vendor->v_contact_phone; else $vendor_phone="";
				if ($vendor_phone || (isset($vendor->v_contact_email) && $vendor->v_contact_email)){
					$to=$vendor->v_contact_email;
					$theme=Text::_("Order from site")." № ".$order_id." ".Text::_("from")." ".Date::GetdateRus(Date::todaySQL());
					aNotifier::addToQueue($to, $theme, $text, "html", $vendor_phone);
				}
			}
			$to=""; $user_phone="";
			if($data_override4loggedin && User::getInstance()->isLoggedIn()) $to=User::getInstance()->getEmail();
			// @TODO Override of phone is need
			if(!$to || (!$user_phone && $this->getConfigVal('sms2user'))) {
				// попытаемся достать нужное из заказа если включены соответсвующие флаги у модуля
				if(count($userdata)){
					if(!$to && array_key_exists("userdata_email", $userdata) && $userdata['userdata_email']) $to=$userdata['userdata_email'];
					if(!$user_phone && array_key_exists("userdata_phone", $userdata) && $userdata['userdata_phone']) $user_phone=$userdata['userdata_phone'];
				}
			}
			$text = $this->renderOrderEmail($data, "order_email4user");
			if($to || $user_phone) {
				$theme=Text::_("Your order")." № ".$order_id." ".Text::_("from")." ".Date::GetdateRus(Date::todaySQL())." ".Text::_("is accepted");
				aNotifier::addToQueue($to, $theme, $text, "html", $user_phone);
			}
		}
	}
	public function showSales(){
		$this->is_disabled();
		$view = $this->getView();
		$view->addBreadcrumb(Text::_('Cabinet'),Router::_("index.php?module=user&view=panel"));
		$view->addBreadcrumb(Text::_('My sales'),"#");
		$model=$this->getModel();
		$orders = $model->getOrders();
		$status_arr = $model->getStatuses();
		$view->assign("orders", $orders);
		$view->assign("status_arr", $status_arr);
	}
	public function showOrders() {
		$this->is_disabled();
		switch($this->get('layout')) {
			case "basket":
				$this->selectBasket();
				break;
			case "new":
				$this->submitOrder();
				break;
			case "payment":
				$order_id = Request::getInt('order_id',0);
				$order_hash = Request::getSafe('order_hash','');
				if (!$order_id){
					/*
					Для систем которые думают что они самые умные и не дают нормальных возвратных url
					Например отдается постом orderId, тогда добавляем в ссылку &order_id_alias=orderId
					*/ 
					$order_id_alias = Request::getSafe('order_id_alias',"");
					if($order_id_alias)	$order_id = Request::getInt($order_id_alias,0);
					if(!$order_id){
						/*
						Для систем которые думают что они самые умные, да еще и самые хитрые и присылают вообще неизвестно что.
						Например отдается поток в котором json
						Для них callback ссылки будут вида :
						index.php?module=catalog&view=orders&layout=payment&payment=robokassa&mode=recieve
						или 
						catalog/orders/payment/robokassa/recieve.html
						*/
						$pt_name=Request::getSafe("payment");
						if($pt_name){
							$mode=Request::getSafe("mode");
							$order_id=catalogPayment::getOrderIdByAbstractPaymentClass($pt_name, $mode);
						}
					}
				}
				$this->payOrder($order_id, $order_hash);
				break;
			case "delivery":
				$order_id = Request::getInt('order_id',0);
				$order_hash = Request::getSafe('order_hash','');
				if (!$order_id){
					/*
					 Для систем которые думают что они самые умные и не дают нормальных возвратных url
					 Например отдается постом orderId, тогда добавляем в ссылку &order_id_alias=orderId
					 */
					$order_id_alias = Request::getSafe('order_id_alias',"");
					if($order_id_alias)	$order_id = Request::getInt($order_id_alias,0);
					if(!$order_id){
						/*
						 Для систем которые думают что они самые умные, да еще и самые хитрые и присылают вообще неизвестно что.
						 Например отдается поток в котором json
						 Для них callback ссылки будут вида :
						 index.php?module=catalog&view=orders&layout=delivery&delivery=rus_post&mode=recieve
						 или
						 catalog/orders/delivery/rus_post/recieve.html
						 */
						$dt_name=Request::getSafe("delivery");
						if($dt_name){
							$mode=Request::getSafe("mode");
							$order_id=catalogDelivery::getOrderIdByAbstractDeliveryClass($dt_name, $mode);
						}
					}
				}
				// Тут можно вызвать статический обработчик системы доставки или функцию контроллера или модели
				break;
			case "order":
				$order_id = Request::getInt('order_id',0);
				$order_hash = Request::getSafe('order_hash','');
				$this->viewOrder($order_id, $order_hash);
				break;
			case "print":
				$this->set('layout',"order",true);
				$order_id = Request::getInt('order_id',0);
				$order_hash = Request::getSafe('order_hash','');
				$this->printOrder($order_id, $order_hash);
				break;
			default:
				$view = $this->getView();
				$view->addBreadcrumb(Text::_('Cabinet'),Router::_("index.php?module=user&view=panel"));
				$view->addBreadcrumb(Text::_('Orders'),"#");
				$model=$this->getModel();
				$orders = $model->getOrders();
				$status_arr = $model->getStatuses();
				$view->assign("orders", $orders);
				$view->assign("status_arr", $status_arr);
				break;
		}
	}
	/**
	 * Создает бреадкрамб от главной до позиции по всем путям
	 */
	public function createUpBreadcrumb($view, $ggr_id) {
		$i=0;	$bc=array();
		$model = $this->getModel('goods');
		$limitBread=$this->getConfigVal('breadcrumb_lenght');
		$arr=$model->getParentGroup($ggr_id);
		if(count($arr)>0) {
			foreach($arr as $key=>$val) {
				$i++;
				$str=$val["title"];
				if($limitBread>0 && (mb_strlen($str) > $limitBread)) $bc[$i]['text']=mb_substr($str,0,$limitBread)."...";
				else $bc[$i]['text']=$val["title"];
				$bc[$i]['link']=Router::_("index.php?module=catalog&amp;view=goods&amp;psid=".(int)$key."&amp;alias=".$val["alias"]);
			}
		}
		$i++;
		$breadcrumb_start=$this->getConfigVal('breadcrumb_start');
		$breadcrumb_start_link=$this->getConfigVal('breadcrumb_start_link');
		if($breadcrumb_start && $breadcrumb_start_link) {
			$bc[$i]['text']=Text::_($breadcrumb_start); $bc[$i]['link']=Router::_($breadcrumb_start_link);
		} else {
			$bc[$i]['text']=Text::_('Main page'); $bc[$i]['link']=Router::_("index.php");
		}
		$bc=array_reverse($bc);
		//		Util::showArray($bc);
		foreach($bc as $tkey=>$tval)	{
			$view->addBreadcrumb($tval['text'],$tval['link']);
		}
	}
	public function showPayments() {
		$this->is_disabled();
		$view=$this->getView();
		$view->addBreadcrumb(Text::_('Cabinet'),"index.php?module=user&amp;view=panel");
		$view->addBreadcrumb(Text::_('My payments'),"#");
	}
	public function showReports() {
		$view=$this->getView();
		$view->addBreadcrumb(Text::_('Cabinet'),"index.php?module=user&amp;view=panel");
	 $view->addBreadcrumb(Text::_('My reports'),"#");
	}
	public function ajaxgetGoodsInfo() {
		$psid=Request::getInt('psid');
		$model = $this->getModel('goods');
		$result=$model->getElement($psid);
		echo json_encode($result);
	}
	public function ajaxrenderUserDataForm($pt_id=0, $dt_id=0){
		$this->is_disabled();
		if (!$pt_id && !$dt_id) {
			$pt_id=Request::getInt('pt_id');
			$dt_id=Request::getInt('dt_id');
		}
		//echo $this->renderUserDataForm($pt_id, $dt_id);
		$dt_class=catalogDelivery::getDeliveryClass($dt_id);
		$answer=array("html"=>$this->renderUserDataForm($pt_id, $dt_id));
		echo json_encode($answer);
	}
	public function ajaxExecuteDataQueryDT($dt_id=0){
		$answer = array("html"=>"");
		$this->is_disabled();
		if (!$dt_id) $dt_id=Request::getInt('dt_id');
		$data_query = Request::getSafe("data_query");
		$dt_class=catalogDelivery::getDeliveryClass($dt_id);
		if (is_object($dt_class) && $data_query) {
			$answer["html"] = $dt_class->executeDataQuery($data_query);
		}
		echo json_encode($answer);
	}
	public function ajaxExecuteDataQueryPT($pt_id=0){
		$answer = array("html"=>"");
		$this->is_disabled();
		if (!$pt_id) $pt_id=Request::getInt('pt_id');
		$data_query = Request::getSafe("data_query");
		$pt_class=catalogPayment::getPaymentClass($pt_id);
		if (is_object($pt_class) && $data_query) {
			$answer["html"] = $pt_class->executeDataQuery($data_query);
		}
		echo json_encode($answer);
	}
	public function ajaxcalculateDelivery(){
		$answer=array("is_error"=>1, "error_text"=>"<p class=\"delivery_error error\">".Text::_("Failed initializing delivery")."</p>","need_recalc"=>1 , "delivery_sum"=>0, "delivery_text"=>"", "total_sum"=>0, "total_text"=>"");
		$dt_id=Request::getInt('delivery_type');
		$mode=(Request::getInt('on_load', 0) ? 0 : 1);
		$dt_class=catalogDelivery::getDeliveryClass($dt_id);
		if($dt_class->isLoaded()){
			$userdata=array();
			if ($this->getConfigVal("require_person")) $userdata["userdata_person"]=Request::getSafe("userdata_person","");
			if ($this->getConfigVal("require_email")) $userdata["userdata_email"]=Request::getSafe("userdata_email","");
			if ($this->getConfigVal("require_phone")) $userdata["userdata_phone"]=Request::getSafe("userdata_phone","");
			$answer["need_recalc"]=$dt_class->needRecalc();
			$answer["delivery_sum"]=Currency::getInstance()->convert($dt_class->calculate($mode, $userdata), $dt_class->getCurrency());
			$answer["taxes_sum"] = Currency::getInstance()->convert($dt_class->getTaxSum(), $dt_class->getCurrency());
			$answer["total_sum"] = Request::getFloat("order_results_summa", 0);
			$answer["delivery_text"]="<p class=\"delivery_text\"><label class=\"fake_label\">".Text::_("Delivery sum").":</label> ".number_format($answer["delivery_sum"], catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)." ".Request::getSafe("order_results_currency")."</p>";
			$answer["taxes_text"]="<p class=\"taxes_text\"><label class=\"fake_label\">".Text::_("Tax")." ".$dt_class->getTaxName().":</label> ".number_format($answer["taxes_sum"], catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)." ".Request::getSafe("order_results_currency")."</p>";
			$answer["total_text"]="<p class=\"total_text\"><label class=\"fake_label\">".Text::_("Total sum by order").":</label> ".number_format($answer["total_sum"] + $answer["delivery_sum"], catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR)." ".Request::getSafe("order_results_currency")."</p>";
			$answer["is_error"]=$dt_class->isError();
			$answer["error_text"]="<p class=\"delivery_error error\">".$dt_class->getErrorText()."</p>";
		}
		echo json_encode($answer);
	}
	public function ajaxrenderDeliverySelector(){
		$this->is_disabled();
		$view = $this->getView("orders");
		$view->setLayout("dts");
		$psid = Request::getInt('psid',0);
		if($psid) {
			if(count(Basket::getInstance()->calculateVendor())){
				$order_weight=Basket::getInstance()->weight;
				$order_total=Basket::getInstance()->total;
				$model = $this->getModel("orders");
				$dt_list=$model->getDeliveryTypes($psid, $order_weight,$order_total);
				$view->assign("dt_list", $dt_list);
				$view->assign("dt_selected", $dt_list[0]->dt_id);
				$view->render();
			} else {
				Session::unsetVar("basket_vendor");
			}
		}
	}

	public function ajaxgetAddress(){
		$psid=Request::getInt('psid');
		if (User::getInstance()->isLoggedIn()){
			$userdata=Userdata::getInstance(User::getInstance()->u_id);
			$address=$userdata->getAddress($psid);
		} else {
			$address=Address::getTmpl();
		}
		echo json_encode($address);
	}
	public function ajaxresetfilter(){
		if(Request::getInt("full_reset", 0)){
			Session::unsetVar("filter.vendor");
			Session::unsetVar("filter.manufacturer");
		}
		parent::ajaxresetfilter();
	}
	public function ajaxsetCustomFilter(){
		$filter_by=Request::getSafe('filter_by');
		$psid=Request::getInt('psid');
		if ($filter_by && $psid){
			switch($filter_by){
				case "vendor":
					Session::setVar("filter.vendor",$psid);
					echo "OK";
					break;
				case "manufacturer":
					Session::setVar("filter.manufacturer",$psid);
					echo "OK";
					break;
				default:
					echo "ERROR";
					break;
			}
		} else echo "ERROR";
	}
	public function ajaxresetCustomFilter(){
		$filter_by=Request::getSafe('filter_by');
		if ($filter_by){
			switch($filter_by){
				case "vendor":
					Session::unsetVar("filter.vendor");
					echo "OK";
					break;
				case "manufacturer":
					Session::unsetVar("filter.manufacturer");
					echo "OK";
					break;
				default:
					echo "ERROR";
					break;
			}
		} else echo "ERROR";
	}
	public function ajaxgetRandomGoods() {
		$rg_referer=Request::getSafe("rg_referer", "");
		$grpsid=0;
		$gid=0;
		$new=0;
		$hit=0;
		$goods = Module::getHelper("goods", "catalog")->getRandomGoods($grpsid, $gid, $new, $hit, $rg_referer);
		if (count($goods)){
			echo json_encode($goods[0],JSON_FORCE_OBJECT);
		}
	}
	public function checkOrderData(&$err_message){
		$no_errors=true;
		if ($this->getConfigVal("require_person") && !Request::getSafe("userdata_person","")){
			$err_message[]=Text::_("Some fields not filled");
			$no_errors=false;
		} 
		if ($this->getConfigVal("require_email") && !Mailer::checkEmail(Request::getSafe("userdata_email",""))){
			$err_message[]=Text::_("Wrong email");
			$no_errors=false;
		}
		if ($this->getConfigVal("require_phone") && !SMSProvider::checkPhoneNumber(Request::getSafe("userdata_phone",""))){
			$err_message[]=Text::_("Wrong phone");
			$no_errors=false;
		}
		return $no_errors;
	}
	public function renderUserDataForm($pt_id, $dt_id){
		$html="";
		$pt_class=catalogPayment::getPaymentClass($pt_id);
		$dt_class=catalogDelivery::getDeliveryClass($dt_id);
		if (is_object($pt_class)  && is_callable(array($pt_class,"renderForm"))) {
			$html.=$pt_class->renderForm();
		}
		if (is_object($dt_class) && is_callable(array($dt_class,"renderForm"))) {
			$html.=$dt_class->renderForm();
		}
		return $html;
	}
	public function ajaxliveSearch(){
		$kwds=Request::getSafe('kwds');
		$controller=Request::getSafe('controller');
		if($controller == "default") $controller = "";
		$view = $this->getView("goods");
		$view->setLayout("livesearch");
		$search_mode = intval($this->getConfigVal("search_mode"));
		$live_search_show_more_goods = $this->getConfigVal("live_search_show_more_goods");
		if($search_mode > 0 && mb_strlen($kwds) >= $this->getConfigVal("minimum_search_length")){
			$model = $this->getModel();
			$live_search_show_categories = $this->getConfigVal("live_search_show_categories");
			$categories = array();
			
			if($live_search_show_categories){
				$cats_ids = $model->getCategories4LiveSearch($kwds, $search_mode);
				if(count($cats_ids)){
					$i = 0;
					foreach ($cats_ids as $lsc_key=>$lsc_val) {
						$categories[] = $model->getParentGroup($lsc_val->cat_id);
						$i++;
						if($i >= $live_search_show_categories) break; 
					}
				}
			}
			
			$live_search_show_goods = $this->getConfigVal("live_search_show_goods");
			$goods = array();
			if($live_search_show_goods){
				$count_goods = $model->getCountGoods4LiveSearch($kwds, $search_mode);
				$goods = $model->getGoods4LiveSearch($kwds, $search_mode, $live_search_show_goods);
			}
			$view->assign("kwds", $kwds);
			$view->assign("controller", $controller);
			$view->assign("live_search_show_categories", $live_search_show_categories);
			$view->assign("categories", $categories);
			$view->assign("live_search_show_goods", $live_search_show_goods);
			$view->assign("count_goods", $count_goods);
			$view->assign("goods", $goods);
			$view->assign("search_mode", $search_mode);
			$view->assign("live_search_show_more_goods", $live_search_show_more_goods);
			
			$view->render();
		}
	}
	public function ajaxaddToFavourites(){
		if($this->getConfigVal('enable_favourites_goods')){
			$this->is_disabled();
			$psid = $this->getPsid();
			if ($psid) Module::getHelper("favourites")->addFavouritesPosition($psid);
			echo "OK";
		} else {
			echo "ERROR";
		}
	}
	public function ajaxremoveFromFavourites(){
		if($this->getConfigVal('enable_favourites_goods')){
			$this->is_disabled();
			$psid = $this->getPsid();
			if ($psid) Module::getHelper("favourites")->deleteFavouritesPosition($psid);
			echo "OK";
		} else {
			echo "ERROR";
		}
	}
	public function ajaxaddToCompare(){
		if($this->getConfigVal('enable_compare_goods')){
			
			$this->is_disabled();
			$psid = $this->getPsid();
			if ($psid) Module::getHelper("compare")->addComparePosition($psid);
			echo "OK";
		} else {
			echo "ERROR";
		}
	}
	public function ajaxremoveFromCompare(){
		if($this->getConfigVal('enable_compare_goods')){
			$this->is_disabled();
			$psid = $this->getPsid();
			if ($psid) Module::getHelper("compare")->deleteComparePosition($psid);
			echo "OK";
		} else {
			echo "ERROR";
		}
	}
	public function favourites(){
		if($this->getConfigVal('enable_favourites_goods')){
			$tp = User::getInstance()->u_pricetype;
			$mdl = Module::getInstance();
			$view = $this->getView("goods");
			$viewname = $view->getName();
			$this->checkACL("view".ucfirst($mdl->getName()).ucfirst($viewname));
			$breadcrumb_start=$this->getConfigVal('breadcrumb_start');
			$breadcrumb_start_link=$this->getConfigVal('breadcrumb_start_link');
			if($breadcrumb_start && $breadcrumb_start_link) {
				$view->addBreadcrumb(Text::_($breadcrumb_start), Router::_($breadcrumb_start_link));
			}
			$view->addBreadcrumb(Text::_("Favourites list"),'#');
			$model = $this->getModel();
			$model->loadMeta();
			$reestr = $mdl->get('reestr');
			$goods_ids = Module::getHelper("favourites")->getFavourites();
			/******************************************************************/
			$model->meta->show_nopages = true;
			$model->meta->link_with_childs = true;
			$reestr->set('needpan',false);
			$reestr->set("multy_code", 0);
			$reestr->set('psid', 0);
			$reestr->set('page', Request::getInt('page', 1));
			$_default_sort=""; $_default_orderby="";
			$ggr_default_sorting=$this->getConfigVal("default_goods_sorting");
			if($ggr_default_sorting=="0") $ggr_default_sorting="";
			if($ggr_default_sorting) {
				$_default_sorting = explode(".", $ggr_default_sorting);
				if(count($_default_sorting)==2){
					$_default_sort=$_default_sorting[0]; $_default_orderby=$_default_sorting[1];
				}
			}
			$reestr->set('sort',Request::getSafe("sort", $_default_sort));
			$reestr->set('orderby',Request::getSafe("orderby", $_default_orderby));
			for($iii=1; $iii <= 5; $iii++) {
				$model->meta->updateArrayField('view','g_price_'.$iii, 0);
				$model->meta->updateArrayField('filter','g_price_'.$iii, 0);
				$model->meta->updateArrayField('filter_ext','g_price_'.$iii, 0);
			}
			if(!catalogConfig::$hide_prices)	{
				$model->meta->updateArrayField('view','g_price_'.$tp,1);
				$model->meta->updateArrayField('filter','g_price_'.$tp,1);
				$model->meta->updateArrayField('filter_ext','g_price_'.$tp,1);
			}
	
			if(!siteConfig::$use_points_system)	$model->meta->updateArrayField('view','g_points',0);
			else $model->meta->updateArrayField('view','g_points',0);
			if(catalogConfig::$show_stock)	$model->meta->updateArrayField('view','g_stock',1);
			else $model->meta->updateArrayField('view','g_stock',0);
			$model->meta->custom_sql.=" AND c.g_id IN (".implode(",", array_keys($goods_ids)).")";
			$view->setLayout("favourites");
			$_SESSION['filter_np']["catalog"][User::getInstance()->getID(true)][$viewname.".favorites.0"]=1;
			if(count($goods_ids)) $result = $model->getData();
			else $result = array();
			if(catalogConfig::$complectPriceAsGoodsSum){
				if(count($result)){
					foreach($result as $res){
						$model->updateComplectPrice($res);
					}
				}
			}
			if(count($result)){
				$view->assign("options_gids", $model->haveOptions(array_keys($result)));
				$view->assign("discounts", $model->getDiscounts(array_keys($result)));
			} else {
				$view->assign("options_gids", array());
				$view->assign("discounts", array());
			}
			$quadro_by_row=$this->getConfigVal('quadro_by_row');
			$view->assign("quadro_by_row", $quadro_by_row);
			/******************************************************************/
			//$view->setLayout("favourites");
			$view->assign("goods_ids", $goods_ids);
			$view->renderSprav($model->meta,$result);
			//$view->render();
		} else {
			$this->setRedirect('index.php',Text::_('Page not found'),404);
		}
	}
	public function compare(){
		if($this->getConfigVal('enable_compare_goods')){
			$tp = User::getInstance()->u_pricetype;
			$mdl = Module::getInstance();
			$view = $this->getView("goods");
			$viewname = $view->getName();
			$this->checkACL("view".ucfirst($mdl->getName()).ucfirst($viewname));
			$breadcrumb_start=$this->getConfigVal('breadcrumb_start');
			$breadcrumb_start_link=$this->getConfigVal('breadcrumb_start_link');
			if($breadcrumb_start && $breadcrumb_start_link) {
				$view->addBreadcrumb(Text::_($breadcrumb_start), Router::_($breadcrumb_start_link));
			}
			$view->addBreadcrumb(Text::_("Compare list"),'#');
			$goods = array();
			$goods_info_arr = array();
			$goods_ids = Module::getHelper("compare")->getCompare();
			if(count($goods_ids)){
				$model = $this->getModel();
				// $model->loadMeta();
				foreach(array_keys($goods_ids) as $psid){
					$result = $model->getElementData($psid);
					if(is_object($result)){
						$complect = array();
						if ($result->g_type==5 && catalogConfig::$complectPriceAsGoodsSum){
							if($this->getConfigVal("show_kits_on_info_page")){
								$complect= $model->getComplectSet($result);
							}
							$model->updateComplectPrice($result, $complect);
						}
						if((isset($model->meta)&&($model->meta))) {
							if(!catalogConfig::$hide_prices)	$model->meta->updateArrayField('input_view','g_price_'.$tp,1);
							if(!siteConfig::$use_points_system)	$model->meta->updateArrayField('input_view','g_points',0);
							else	$model->meta->updateArrayField('input_view','g_points',0);
							if(catalogConfig::$show_stock)	$model->meta->updateArrayField('input_view','g_stock',1);
							else	$model->meta->updateArrayField('input_view','g_stock',0);
						}
						$goods[$psid] = $result;
						$goods_info_arr[$psid] = $view->prepareInfoArray($model->meta,$result);
					}
				}
			}
			$view->assign("goods_ids", $goods_ids);
			$view->assign("goods", $goods);
			$view->assign("goods_info_arr", $goods_info_arr);
			$view->setLayout("compare");
			$view->render();
		} else {
			$this->setRedirect('index.php',Text::_('Page not found'),404);
		}
	}
}
?>