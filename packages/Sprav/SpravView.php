<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_CORE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class SpravView extends View {
	protected $buttons_source="";
	protected $sprav_list_id = "sprav_list";
	public $activeTab=1;
	public $sel_0_pref = "[ ";
	public $sel_0_suff = " ]";
	
	public function __construct($name) {
		parent::__construct($name);
		Portal::getInstance()->addStyleSheet("sprav.css", true);
	}
	
	public function renderSpravPanels(&$meta, &$rows, &$tree)	{
		echo "<div id=\"sprav-panel\" class=\"sprav-panel row\">";
		$this->renderTree($meta, $tree);
		$this->renderSprav($meta, $rows, "col-sm-9 col-md-10");
		echo "</div>";
	}
	public function renderTreeSelector(&$meta, &$tree)	{
		$mdl = Module::getInstance();
		$reestr = $mdl->get('reestr');
		$_lol = $reestr->get('lol'); if (!$_lol && $meta->selector) Util::halt();
		$this->sprav_list_id = "sprav_list_selector";
		$wrapper_class = " sprav-list sprav-list-selector";
		$multy_code=$reestr->get('multy_code');
		echo "<div id=\"".$this->sprav_list_id."\" class=\"moduleBody ".$this->get("module")."Module".$wrapper_class."\"><div class=\"content\"><div class=\"sprav-panel sprav-tree-selector container-fluid\">";
		echo "	<div class=\"tree-panel row\">";
		echo HTMLControls::renderHiddenField("sprav_list_id", $this->sprav_list_id, false, "sprav_list_id sprav-list-id-holder");
		echo "		<div class=\"sprav-tree-selector-title col-sm-12\"><h3 class=\"title\">".Text::_($meta->title)."</h3></div>";
		echo "		<div class=\"sprav-tree-selector-data col-sm-12\">";
		echo HTMLControls::renderHiddenField("lol", $_lol, false, "sprav_list_lol sprav-list-lol-holder");
		echo $tree->getTreeHTML(0, "ul", "sprav-tree-selector-ul", "treeview", 0, "selector_li");
		echo "		</div>";
		echo "	</div>";
		echo "</div></div></div>";
	}
	public function renderTree(&$meta, &$tree)	{
		$mdl = Module::getInstance();
		$reestr = $mdl->get('reestr');
		$multy_code=$reestr->get('multy_code');
		if(!$this->buttons_source) $this->buttons_source = Portal::getURI().LINK_TEMPLATES."/".Portal::getInstance()->getTemplate()."/images/buttons/";

		Portal::getInstance()->addScript("/redistribution/jquery.plugins/jquery.treeview.js");
		Portal::getInstance()->AddScriptDeclaration('$(document).ready(function(){
			$("#sprav-tree").treeview({
				animated: 100,
				unique: false,
				persist: "cookie",
				collapsed : true,
				cookieId: "sprav_navigator"
			});
			$("#multycode_'.$multy_code.'").addClass("active");
			$("#multycode_'.$multy_code.'").parents("li").removeClass("expandable").addClass("collapsable");
			$("#multycode_'.$multy_code.'").parents("li").children("ul").show();
			$("#sprav-tree").find("li.collapsable.lastExpandable").removeClass("lastExpandable").addClass("lastCollapsable");
		});');
		echo "<div id=\"tree-panel\" class=\"tree-panel col-sm-3 col-md-2\">";
		echo HTMLControls::renderHiddenField("sprav_list_id", $this->sprav_list_id, false, "sprav_list_id sprav-list-id-holder");
		echo "<div class=\"nmb\">";
		echo "<table class=\"sprav_tree_title\"><tbody><tr><td class=\"root_btn\">";
		echo "<div class=\"picto_left\"><a onclick=\"".sprintf($tree->element_js,'')."\">";
		echo "<img title=\"".Text::_("Show root")."\" alt=\"Top\"  class=\"sprav-button-home\" src=\"/images/blank.gif\" />";
		echo "</a></div>";
		echo "</td><td align=\"center\">";
		echo Text::_("Navigator");
		echo "</td><td class=\"collapse_tree_button\">";
		echo "<div id=\"collapse_button\" class=\"picto_right\"><a onclick=\"toggleSpravTree()\">";
		echo "<img class=\"sprav-button\" title=\"".Text::_("Toggle tree")."\" alt=\"\" src=\"/images/blank.gif\" width=\"1\" height=\"1\" />";
		echo "</a></div>";
		echo "</td></tr></tbody></table></div>";
		echo "<div class=\"sprav_tree_data\">".$tree->getTreeHTML(0,"ul","sprav-tree", "treeview",0,"multycode")."</div>";
		echo "</div>";
	}
	public function renderSprav(&$meta, &$rows, $column_class="")	{
		$this->buttons_source = Portal::getURI().LINK_TEMPLATES."/".Portal::getInstance()->getTemplate()."/images/buttons/";
		$_form ="";
		/* кнопки слева, заголовок, кнопки справа */
		$_html_pan="";
		$_html_left_pan="";
		$_html_center_pan="";
		$_html_center_pan_class = "";
		$_html_right_pan="";
		$_html_right_pan_0="";
		/* заголовок и тело таблицы */
		$_table_header_arr=array(); $_table_body_arr=array(); $_table_body_settings_arr=array(); //$_table_keys_arr=array();
		/* листалка */
		$_html_footer="";
		/*********получаем необходимые переменные*********/
		// $user = User::getInstance();
		$mdl = Module::getInstance();
		$reestr = $mdl->get('reestr');
		$_lol=$reestr->get('lol'); if ((!$_lol)&&($meta->selector)) Util::halt();
		$is_admin=User::getInstance()->isAdmin();
		$uid = User::getInstance()->getID(true);
		$needpan=$reestr->get('needpan',true);
		$is_ajax=$reestr->get('is_ajax',false);
		$this->milestone('Start', __FUNCTION__);
		$consider_parents=$reestr->get('consider_parents',true);
		if($meta->classTable) $_table_class=$meta->classTable; else $_table_class='sprav-table';
		$this->sprav_list_id = "sprav_list";
		$wrapper_class = " sprav-list";
		if($meta->selector) {
			$this->sprav_list_id = "sprav_list_selector";
			$wrapper_class.= " sprav-list-selector";
			$_table_class.=" sprav-selector";
		}
		if($meta->nofilter) $use_filter=false; else $use_filter=$reestr->get('use_filter',true);
		$page = $reestr->get('page', 1);
		$sort = $reestr->get('sort');
		$module = Request::getSafe('module');
		// $view =Request::getSafe('view');
		$view = $this->get('view'); // It is a bug fix ??????????? 
		$alias = Request::getSafe("alias");
		$option=Request::get('option','module');
		$layout = $this->get('layout');
		$psid=$reestr->get('psid');
		$controller= $this->get('controller');
		//$psid=$mdl->get('psid');
		$multy_code=$reestr->get('multy_code');
		$canModify=$reestr->get('canModify',false);
		$orderby = $reestr->get('orderby');
		$orderby_link = "&amp;orderby=".$orderby;
		$dop_head=$reestr->get('dop_head','');
		$sel_filt=$reestr->get('sel_filt');
		$str_sql_wf=$reestr->get('str_sql_wf');
		$records_count_wf=$reestr->get('records_count_wf',0);
		$ref = "index.php?module=".$module."&amp;view=".$view;
		if ($multy_code) $ref .= "&amp;multy_code=".$multy_code;
		if ($controller!='default') $ref .= "&amp;controller=".$controller;
		$ref_appendix=$reestr->get('ref_appendix');
		if($ref_appendix) $ref .= '&'.$ref_appendix;
		$filtered = $reestr->get('filtered');
		$KeyStringTabl=$meta->keystring;
		$fldcurrency=$meta->keycurrency;
		// проверка необходимости проставлять чеки строк
		// $col_count = count($meta->field);                   //количество полей таблицы
		$colrows=count($rows);
		if(defined("_ADMIN_MODE")) $side=1; else $side=0;
		if (isset($_SESSION['filter_np'][$module][$uid][$view.".".$layout.".".$side])&&($_SESSION['filter_np'][$module][$uid][$view.".".$layout.".".$side]==1)) $no_pages=1;
		else {
			if(Request::get('reset_filter',false)) $no_pages=0;
			elseif (defined("_ADMIN_MODE")){
				$no_pages=Request::get('no_pages',0);
			} else {
				$no_pages = 0;
			}
		}
		$trash= $reestr->get('trash',0);
		if($trash==1) {
			$trash_text="<br /><span class=\"small\">".Text::_("Trash records")."</span>";
		} else $trash_text='';
		// заголовки
		$_form .= "<form name=\"frmList\"  action=\"".(defined("_ADMIN_MODE") ? "index.php" : Router::_("index.php"))."\" method=\"post\">\n";
		$_form .= "<input type=\"hidden\" id=\"view\" name=\"view\" value=\"".$view."\" />\n";
		$_form .= "<input type=\"hidden\" id=\"option\" name=\"option\" value=\"".$option."\" />\n";
		$_form .= "<input type=\"hidden\" id=\"task\" name=\"task\" value=\"\" />\n";
		$_form .= "<input type=\"hidden\" id=\"return\" name=\"return\" value=\"0\" />\n";
		$_form .= "<input type=\"hidden\" id=\"module\" name=\"module\" value=\"".$module."\" />\n";
		$_form .= "<input type=\"hidden\" id=\"controller\" name=\"controller\" value=\"".$controller."\" />\n";
		$_form .= "<input type=\"hidden\" id=\"layout\" name=\"layout\" value=\"".$layout."\" />\n";
		$_form .= "<input type=\"hidden\" id=\"sort\" name=\"sort\" value=\"".$sort."\" />\n";
		$_form .= "<input type=\"hidden\" id=\"orderby\" name=\"orderby\" value=\"".$orderby."\" />\n";
		$_form .= "<input type=\"hidden\" id=\"page\" name=\"page\" value=\"".$page."\" />\n";
		$_form .= "<input type=\"hidden\" id=\"multy_code\" name=\"multy_code\" value=\"".$multy_code."\" />\n";
		$_form .= "<input type=\"hidden\" id=\"psid\" name=\"psid\" value=\"\" />\n";
		$_form .= "<input type=\"hidden\" id=\"trash\" name=\"trash\" value=\"".$trash."\" />\n";
		if ($is_admin && $needpan) {
			// Панели только админам, и только в админке, остальным кнопки прямо в шаблонах
			
			if ($meta->tree_index && !$trash && !$meta->selector){
				$_html_left_pan.= "<div id=\"toggle_sprav_tree\" class=\"picto_left\"><a href=\"#\" onclick=\"javascript:toggleSpravTree(); return false;\">";
				$_html_left_pan.= "	<img class=\"sprav-button\" src=\"/images/blank.gif\" alt=\"\" title=\"".Text::_("Toggle tree")."\" />";
				$_html_left_pan.= "</a></div> \n";
			}
			if(defined("_ADMIN_MODE")) {
				if ($trash) {
					$_html_left_pan .= $this->renderButton($meta,'list','Back to list', '',  false, 'left', false);
					$_html_left_pan .= $this->renderButton($meta,'filter', 'Filter', '', false, 'left', true);
				} else {
					$_html_left_pan .= $this->renderButton($meta,'go_up', 'Go up level', '',  false, 'left', false);
					if ($canModify) $_html_left_pan .= $this->renderButton($meta,'new', 'Add element', '', false, 'left', false);
					if ($canModify) $_html_left_pan .= $this->renderButton($meta,'clone', 'Clone element', 'Please select element from list', false, 'left', true);
					if ($canModify) $_html_left_pan .= $this->renderButton($meta,'modify', 'Modify element', 'Please select element from list', false, 'left', true);
					$_html_left_pan .= $this->renderButton($meta,'info', 'Info', 'Please select element from list', false, 'left', true);
					if ($canModify) $_html_left_pan .= $this->renderButton($meta,'new_string', 'Add element', 'Please select element from list', false, 'left', true);
					$_html_left_pan .= $this->renderButton($meta,'datagramm', 'Datagramm', 'Please select element from list', false, 'left', true);
					$_html_left_pan .= $this->renderButton($meta,'refresh', 'Refresh', 'Please select element from list', false, 'left', true);
					$_html_left_pan .= $this->renderButton($meta,'print', 'Print', 'Please select element from list', false, 'left', true);
					$_html_left_pan .= $this->renderButton($meta,'filter', 'Filter', '', false, 'left', true);
					$_html_left_pan .= $this->renderButton($meta,'filter_no', 'No filter', '', false, 'left', true);
					if($_html_left_pan) $_html_left_pan .= $this->renderButton($meta,'separator','','',false,"left",false);
					if ($canModify && $meta->linktable) $_html_left_pan .= $this->renderButton($meta,'modify_links', 'Modify links', 'Please select element from list', false, 'left', true);
					if ($canModify &&	( !$meta->parent_subordination|| ( (!$meta->linktable)||($meta->linktable && $multy_code) ) ) )
						$_html_left_pan .= $this->renderButton($meta,'reorder', 'Reorder items', '', 'Do you want to reorder all elements', 'left', true, false);
						$_html_left_pan .= $this->renderUniButtons($meta,'left');
				}
			} else $_html_left_pan .= $this->renderButton($meta,'go_up', 'Go up level', '',  false, 'left', false);
			if($_html_left_pan) $_html_left_pan = "<div class=\"sprav_mnu_left col-sm-3 clearfix\">".$_html_left_pan."</div>";
		}
		if($is_admin && $needpan) {
			if(!$meta->selector) {
				if (defined("_ADMIN_MODE") && $canModify) {
					
					if($trash) {
						$_html_right_pan .= $this->renderButton($meta,'clean_trash','Clean trash', "", 'This will completely remove all trashed and related elements. Continue','right', true, false);
						$_html_right_pan .= $this->renderButton($meta,'undelete', 'Undelete element', 'Please select element from list','Do you want to restore selected elements', 'right', true);
					}	else {
						$_html_right_pan .= $this->renderButton($meta,'trash','Trash', '',  false, 'right', false);
						$_html_right_pan .= $this->renderButton($meta,'delete', 'Delete element', 'Please select element from list', 'Do you want to delete selected elements', 'right', true);
						$_html_right_pan .= $this->renderUniButtons($meta,'right');
					}
					if($_html_right_pan){
						$_html_right_pan_0 = "<div class=\"sprav_mnu_right col-sm-3 d-block d-sm-none clearfix\">".$_html_right_pan."</div>";
						$_html_right_pan = "<div class=\"sprav_mnu_right col-sm-3 visible-sm visible-md visible-lg clearfix\">".$_html_right_pan."</div>";
					}
				}
			}	else {
				$_html_center_pan = "<div class=\"filter_selector\">";
				$_html_center_pan.= "<div class=\"row\"><div class=\"col-sm-3\">";
				$_html_center_pan.= HTMLControls::renderLabelField($meta->namestring,Text::_("Filter"));
				$_html_center_pan.= "</div><div class=\"col-sm-9\">";
				$_html_center_pan.= "<div class=\"picto_right\">";
				$alterAjaxFilterArgs = "'".$meta->namestring."','".$module."','".$view."','".$layout."','".$psid."','".$controller."','".$trash."','".$sort."','".$orderby."','0','".Request::getSafe('lol','linkEditor')."','".$this->sprav_list_id."','getContList'";
				$_html_center_pan.= "	<a class=\"filter-selector-apply\" href=\"\" onclick=\"alterAjaxFilter(".$alterAjaxFilterArgs."); return false;\">";
				$_html_center_pan.= "		<img class=\"sprav-button-filter\" src=\"/images/blank.gif\" alt=\"F\" title=\"".Text::_("Apply")."\" />";
				$_html_center_pan.= "	</a>";
				$_html_center_pan.= "</div>\n";
				$alterAjaxFilterInputJS=array("onkeydown"=>"handleAjaxFilterInput(event,".$alterAjaxFilterArgs.")");
				$_html_center_pan.= "<div class=\"filter_input\">".HTMLControls::renderInputText("filter_".$meta->namestring, "", 20, "", "", "form-control", false, false, "", $alterAjaxFilterInputJS)."</div>";
				$_html_center_pan.= "</div></div>";
				$_html_center_pan.= "</div>";
			}
		}
		if($_html_left_pan && $_html_right_pan) $_html_center_pan_class = "col-sm-6";
		elseif(!$_html_left_pan && !$_html_right_pan) $_html_center_pan_class = "col-sm-12";
		else $_html_center_pan_class = "col-sm-9";
		if($needpan) {
			$head_text = Text::_($meta->title); if ($dop_head && $consider_parents) $head_text .= ": ".$dop_head;
			$_html_center_pan = "<div class=\"sprav_mnu_center ".$_html_center_pan_class."\"><h3 class=\"spravtitle\">".$head_text.$trash_text."</h3>".$_html_center_pan."</div>\n";
		}
		if ($is_admin)	$_html_pan="<div id=\"nmb\" class=\"nmb container-fluid\"><div class=\"row\">".$_html_left_pan.$_html_right_pan_0.$_html_center_pan.$_html_right_pan."</div></div>";
		else $_html_pan=$_html_center_pan;
		/* получили заголовок справочника с кнопками */
		//$t_col_count = $col_count;
		if(($meta->selector)&&($is_ajax)) {
			$_table_header_arr["selector"]['html']="";
			$_table_header_arr["selector"]['value']="";
			$_table_header_arr["selector"]['class']="selector";
			$_table_header_arr["selector"]['orderby_class']="";
			$_table_header_arr["selector"]['onclick']="";
			$_table_header_arr["selector"]['width']="no";
		} elseif($meta->checkbox)	{
			//$t_col_count = $t_col_count + 1;
			$_th_onclick="";
			$_table_header_arr["checkbox"]['html']="<input onclick=\"checkAll(this);\" id=\"toggle_all\" type=\"checkbox\" name=\"toggle\" value=\"\" />";
			$_table_header_arr["checkbox"]['value']="";
			$_table_header_arr["checkbox"]['class']="checkbox-all";
			$_table_header_arr["checkbox"]['orderby_class']="";
			$_table_header_arr["checkbox"]['onclick']=$_th_onclick;
			$_table_header_arr["checkbox"]['width']="no";
		} else { }
		if (defined("_ADMIN_MODE") && $meta->linktable && $multy_code && !$trash && $consider_parents) { // заголовок линковочного поле сортировки
			if ($reestr->get('sort')=="ordering") {
				$orderby_class = ($orderby=="ASC" ? "order-down" : "order-up");
				$next_orderby = $reestr->get('next_orderby');
				$next_orderby_link = "&amp;orderby=".$next_orderby;
			} else { $next_orderby_link=''; $next_orderby=""; $orderby_class=" order-not-set";
			}
			if ($is_ajax) $_th_onclick="onclick=\"javascript:switchSortLink('".$module."','".$view."','".$layout."','0','ordering','".$next_orderby."','".$page."','".$_lol."','".$multy_code."','".$this->sprav_list_id."','getContList','".$controller."');\"";
			else {
				$sort_ref=$ref;
				if ($alias) $sort_ref.="&alias=".$alias;
				if ($layout) $sort_ref.="&layout=".$layout;
				if ($multy_code) $sort_ref.="&psid=".$multy_code;
				if ($page > 1) $sort_ref.="&page=".$page;
				$sort_ref.="&sort=ordering";
				if($next_orderby) $sort_ref.="&orderby=".$next_orderby;
				if ($trash) $sort_ref.="&trash=".$trash;
				$_th_onclick="onclick=\"document.location.href='".Router::_($sort_ref)."';\"";
			}
			$_table_header_arr["ordering"]['html']=Text::_("Ordering");
			$_table_header_arr["ordering"]['value']="";
			$_table_header_arr["ordering"]['class']="grid";
			$_table_header_arr["ordering"]['orderby_class']=$orderby_class;
			$_table_header_arr["ordering"]['onclick']=$_th_onclick;
			$_table_header_arr["ordering"]['width']="";
		}

		$quantity_visible_fields=1;
		//for ($i = 1; $i <= $col_count; $i++){
		$sort_base_ref = $ref;
		if ($alias) $sort_base_ref.= "&alias=".$alias;
		if ($layout) $sort_base_ref.= "&layout=".$layout;
		if ($multy_code) $sort_base_ref.= "&psid=".$multy_code;
		if ($page > 1) $sort_base_ref.= "&page=".$page;
		if ($trash) $sort_base_ref.= "&trash=".$trash;
		foreach ($meta->field as $i => $field) {
			if($meta->view[$i]==1)	{
				$quantity_visible_fields=$quantity_visible_fields+1;
				if ($meta->field[$i]==$reestr->get('sort')) {
					$orderby_class = ($orderby=="ASC" ? "order-down" : "order-up");
					$next_orderby = $reestr->get('next_orderby');
					$next_orderby_link = "&amp;orderby=".$next_orderby;
				} else { 
					$next_orderby_link=''; 
					$next_orderby=""; 
					$orderby_class="order-not-set";
				}
				if ($meta->field_orderby[$i]=="NONE" || $meta->ck_reestr[$i]) {
					$_th_onclick="";  $orderby_class='no-sort';
				} else if (!in_array($meta->input_type[$i],array("image","texteditor"))){
					if ($is_ajax) $_th_onclick="onclick=\"javascript:switchSortLink('".$module."','".$view."','".$layout."','0','".$meta->field[$i]."','".$next_orderby."','".$page."','".$_lol."','".$multy_code."','".$this->sprav_list_id."','getContList','".$controller."');\"";
					else {
						$sort_ref = $sort_base_ref;
						if ($meta->field[$i]) $sort_ref.="&sort=".$meta->field[$i];
						if($next_orderby) $sort_ref.="&orderby=".$next_orderby;
						$_th_onclick="onclick=\"document.location.href='".Router::_($sort_ref)."';\"";
					}
				} else {
					$_th_onclick=""; $orderby_class='no-sort';
				}
				if ($meta->val_type[$i]=='currency') $addTitle=' ('.Currency::getShortName(DEFAULT_CURRENCY).')'; else $addTitle='';
				$_table_header_arr[$meta->field[$i]]['html']=Text::_($meta->field_title[$i]).$addTitle;
				$_table_header_arr[$meta->field[$i]]['value']=$meta->field_title[$i].$addTitle;
				$_table_header_arr[$meta->field[$i]]['class']="grid";
				$_table_header_arr[$meta->field[$i]]['orderby_class']=$orderby_class;
				$_table_header_arr[$meta->field[$i]]['onclick']=$_th_onclick;
				$_table_header_arr[$meta->field[$i]]['width']=trim($meta->size[$i]);
			}
		}
		// конец заголовоков
		if ($colrows>0) {
			$rowNum=0;
			$namestring=$meta->namestring;
			
			if($meta->selector && $is_ajax) $selector_array=explode(",", $meta->selector_string);
			 		
			foreach ($rows as $row)	{
				$rowNum++;
				$_table_body_settings_arr[$rowNum]=array();
				$_table_body_settings_arr[$rowNum]["row_class"]="";
				if($meta->deleted || $meta->enabled){
					if($meta->deleted && $row->{$meta->deleted}) $_table_body_settings_arr[$rowNum]["row_class"]="deleted";
					elseif($meta->enabled && !$row->{$meta->enabled}) $_table_body_settings_arr[$rowNum]["row_class"]="disabled";
				}
				$id=$row->{$KeyStringTabl};//забираем в переменную $id значение идешника реальной строки
				$title=htmlspecialchars($row->{$namestring});
				// $_table_keys_arr[$rowNum]=$row->{$KeyStringTabl};
				if(($meta->selector)&&($is_ajax)) {
					$title_arr=array();
					if(count($selector_array)){
						foreach ($selector_array as $selector_str){
							$title_arr[]=htmlspecialchars($row->{$selector_str});
						}
					}
					$_cell_onclick="onclick=\"addFromSelector(this, '".$_lol."','".$id."','".implode(" : ", $title_arr)."');\""; $_current_name = "selector";
					$_table_body_arr[$rowNum][$_current_name]['hidden']=false;
					$_table_body_arr[$rowNum][$_current_name]['html']="<span class=\"selector-data\" data-container=\"".$_lol."\" data-element=\"".$id."\"><img width=\"1\" height=\"1\" src=\"/images/blank.gif\" alt=\"\" title=\"".Text::_("Add")."\"/></span>";
					$_table_body_arr[$rowNum][$_current_name]['value']="";
					$_table_body_arr[$rowNum][$_current_name]['class']="selector selector-".$id;
					$_table_body_arr[$rowNum][$_current_name]['onclick']=$_cell_onclick;
					$_table_body_arr[$rowNum][$_current_name]['width']="no";
				}	elseif($meta->checkbox)	{
					$_cell_onclick="";
					$_current_name = "checkbox-cb";
					$_table_body_arr[$rowNum][$_current_name]['hidden']=false;
					$_table_body_arr[$rowNum][$_current_name]['html']="<input type=\"checkbox\" name=\"cps_id[]\" value=\"".$id."\" id=\"cb".$rowNum."\" />\n";
					$_table_body_arr[$rowNum][$_current_name]['value']=$id;
					$_table_body_arr[$rowNum][$_current_name]['class']="checkbox-line";
					$_table_body_arr[$rowNum][$_current_name]['onclick']=$_cell_onclick;
					$_table_body_arr[$rowNum][$_current_name]['width']="no";
				} else { }

				if (defined("_ADMIN_MODE")&&($meta->linktable)&&($multy_code)&&(!$trash)&&($consider_parents)) { // линковочное поле сортировки
					if ($canModify) $_cell_onclick="onclick=\"modifyLinkOrdering(this,'".$module."','".$view."','".$layout."','".$id."','".$multy_code."');\"";	else  $_cell_onclick="";
					$_current_name = "ordering";
					if ($canModify) $_cell_class="ordering"; else $_cell_class="";
					$_table_body_arr[$rowNum][$_current_name]['hidden']=false;
					$_table_body_arr[$rowNum][$_current_name]['html']=$row->{$_current_name};
					$_table_body_arr[$rowNum][$_current_name]['value']=$row->{$_current_name};
					$_table_body_arr[$rowNum][$_current_name]['class']=$_cell_class;
					$_table_body_arr[$rowNum][$_current_name]['onclick']=$_cell_onclick;
					$_table_body_arr[$rowNum][$_current_name]['width']="no";
				}

				//for ($i = 1; $i <= $col_count; $i++) {
				foreach ($meta->field as $i => $field) {
					if($meta->view[$i]==1) { //если поле видимое то выводим на экран
						// $field = $meta->field[$i];	
						if($meta->field_is_method[$i]){
							if(method_exists($this, $meta->field_is_method[$i])){
								$str_table=$this->{$meta->field_is_method[$i]}($id, $row,$field);
							} else {
								$str_table="";
							}
						} else {
							$code=$row->{$field}; $str_table=""; 
							$field_tbl = $meta->field[$i];
							if($meta->ck_reestr[$i]) {
								if (is_array($meta->ck_reestr[$i])) $key_arr=$meta->ck_reestr[$i];
								else $key_arr=SpravStatic::getCKArray($meta->ck_reestr[$i]);
								if(is_array($key_arr) && $meta->input_type[$i] == "multiselect"){
									$code = explode(";", trim($code, ";"));
									$ms_code = array();
									if(is_array($code)){
										foreach($code as $ms_key=>$ms_val){
											if(array_key_exists($ms_val, $key_arr)){
												$ms_code[]=$key_arr[$ms_val];
											}
										}
										$row->{$field} = implode(", ", $ms_code);
									} else $row->{$field}="";
								} elseif(is_array($key_arr) && array_key_exists($code, $key_arr)) {
									$row->{$field}=$key_arr[$code];
								} else {
									$row->{$field}="";
								}
							} elseif ($meta->ch_table[$i]) {
								if ($meta->ch_table[$i]) {
									$field_tbl=$meta->field[$i]."_sql_replace";
								} else { 
									$field_tbl=$meta->field[$i];
								}
							}
						}
						if ($meta->val_type[$i]=='date') {
							if(!is_null($row->{$field_tbl})) $str_table=Date::fromSQL($row->{$field_tbl}, true); else $str_table = '';
						} elseif ($meta->val_type[$i]=='datetime') 	{
							if($row->{$field_tbl} != '0000-00-00 00:00:00' && !is_null($row->{$field_tbl})) $str_table=date("d.m.y H:i",Date::GetTimestamp($row->{$field_tbl}, 1));else $str_table = '';
						} elseif ($meta->val_type[$i]=='timestamp') {
							$str_table=Date::fromSQL($row->{$field_tbl}, false, false);
						} elseif ($meta->val_type[$i]=='text') {
							if(!$str_table)	$str_table=html_entity_decode($row->{$field_tbl}, ENT_COMPAT,DEF_CP);
						} elseif ($meta->val_type[$i]=='string') {
							switch($meta->input_type[$i])	{
								case 'file': // это прямой аплоад файлов
									if(empty($row->{$field_tbl})) {
										$str_table="";
									} else {
										if (defined("_ADMIN_MODE")) $file_link=BARMAZ_UF."/".$meta->module."/".$meta->upload_path[$i]."/".Files::splitAppendix($row->{$field_tbl});
										else $file_link=Router::_("index.php?module=".$meta->module."&task=downloadFile&psid=".$psid);
										$str_table="<a href=\"".$file_link."\" target=\"_blank\"><img src=\"/images/download.gif\" width=\"16\" height=\"16\" alt=\"\" border=\"0\" /></a>";
									}
								break;
								case 'file_select': // это выбор из списка файлов в категории
									if(empty($row->{$field_tbl})) {
										$str_table="";
									} else {
										if (defined("_ADMIN_MODE")) $file_link=BARMAZ_UF."/".$meta->module."/".$meta->upload_path[$i]."/".Files::splitAppendix($row->{$field_tbl});
										else $file_link=Router::_("index.php?module=".$meta->module."&task=downloadFile&psid=".$psid);
										$str_table="<a href=\"".$file_link."\" target=\"_blank\"><img src=\"/images/download.gif\" width=\"16\" height=\"16\" alt=\"\" border=\"0\" /></a>";
									}
								break;
								case 'image':
									if(empty($row->{$field_tbl})) {
										$tmpl_img="/images/nophoto.png";
										$str_table = "<img src=\"".$tmpl_img."\" class=\"spr_image\" alt=\"\" />";
									} else 	{
										if(isset($meta->img_module[$i])&&$meta->img_module[$i]){
											$tmpl_img=BARMAZ_UF."/".$meta->img_module[$i]."/".$meta->upload_path[$i]."/".Files::splitAppendix($row->{$field_tbl});
											$tmpl_img_path=BARMAZ_UF_PATH.$meta->img_module[$i].DS.str_replace("/",DS,$meta->upload_path[$i]).DS.Files::splitAppendix($row->{$field_tbl}, true);
										}
										else
										{
											$tmpl_img=BARMAZ_UF."/".$meta->module."/".$meta->upload_path[$i]."/".Files::splitAppendix($row->{$field_tbl});
											$tmpl_img_path=BARMAZ_UF_PATH.$meta->module.DS.str_replace("/",DS,$meta->upload_path[$i]).DS.Files::splitAppendix($row->{$field_tbl}, true);
										}
										//if ((file_exists(PATH_FRONT.$tmpl_img))&&(is_file(PATH_FRONT.$tmpl_img))) {
										if ((file_exists($tmpl_img_path))&&(is_file($tmpl_img_path))) {
											if (Files::mime_content_type($tmpl_img_path)=="application/x-shockwave-flash") {
												$str_table = "<a class=\"relpopup\" href=\"".$tmpl_img."\"><img src=\"/images/swf.png\" class=\"spr_flash\" alt=\"\" /></a>";
												$meta->link[$i]="";
											} else {
												$str_table = "<img src=\"".$tmpl_img."\" class=\"spr_image\" alt=\"\" />";
												if(!$meta->link[$i])	{
													$str_table = "<a class=\"relpopup\" href=\"".$tmpl_img."\">".$str_table."</a>";													
												}
											}
										} else  {
											$tmpl_img="/images/nophoto.png";
											$str_table = "<img src=\"".$tmpl_img."\" class=\"spr_image\" alt=\"\" />";
										}
									}
								break;
								case 'address': // это выбор из списка категории
									if(empty($row->{$field_tbl})) {
										$str_table="";
									} else {
										$str_table = Address::decode($row->{$field_tbl}, true);
									}
									break;
								default:
									//$str_table=html_entity_decode($row->{$field_tbl},ENT_COMPAT,DEF_CP);
									if(!$meta->field_is_method[$i] && !$str_table) $str_table=$row->{$field_tbl};
								break;
							}
						}
						elseif ($meta->val_type[$i]=='constanta') 	$str_table=Text::_($row->{$field_tbl});
						elseif ($meta->val_type[$i]=='boolean') {
							if ($row->{$field_tbl}) $switcherClass="on"; else $switcherClass="off";
							if (($meta->field[$i]==$meta->enabled)&&($canModify)){
								$str_table="<div onclick=\"toggleEnabled(this,'".$module."','".$view."','".$layout."','".$id."','".$controller."');\" class=\"switcher_".$switcherClass."\">&nbsp;</div>";
							} else {
								$str_table="<div class=\"boolean_".$switcherClass."\">&nbsp;</div>";
							}
						}
						elseif ($meta->val_type[$i]=='currency'){
							if($meta->keycurrency) $currency_val=$row->{$fldcurrency}; else $currency_val="";
							$str_table=number_format(Currency::getInstance()->convert($row->{$field_tbl},$currency_val), catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR);
						}
						else if (!$str_table) $str_table=$row->{$field_tbl};
						
						if($meta->translate_value[$i]) $str_table=Text::_($str_table);

						// подстановка линков на эелементы строки
						$link='';	$zn_js=0; $_cell_onclick ="";
						if($meta->link[$i])	{
							if($meta->link[$i]=='field') {
								//когда у нас в качестве ссылки работает содержимое поля достаем картинку
								if(!empty($str_table)) {
									if($meta->link_picture[$i]) {
										$img_link=$meta->link_picture[$i];
									} else {
										$img_link="/images/download3.gif";
									}
									// определяем направление ссылки (оно у нас в ключах живет в этом варианте)
									if($meta->link_vars[$i]) {
										$target_link=$meta->link_vars[$i];
									} else {
										$target_link="_blank";
									}
									// ну и формируем самы ссылку из содержания поля
									$str_table="<a href=\"".Router::_($str_table)."\" target=\"".$target_link."\"><img border=\"0\" src=\"".$img_link."\" alt=\"\" title=\"\" /></a>";
								}
							} else {// другие случаи
								// парсинг ключей линков ()
								$link=htmlspecialchars($meta->link[$i]);
								if($meta->link_vars[$i]) {
									$arr_link_vars=explode(",",$meta->link_vars[$i]);
									if(count($arr_link_vars)>0)	{
										foreach($arr_link_vars as $lv_key=>$lv_var)	{
											$lv_var=trim($lv_var);
											if(isset($row->{$lv_var})) {
												$link=str_replace("[".$lv_key."]",$row->{$lv_var}, $link);
											} else {
												$link=str_replace("[".$lv_key."]", ${$lv_var}, $link);
											}
										}
									}
								}
								if ($meta->link_types[$i]=='popup') {
									$linktype = " class=\"relpopup\"";
								} elseif (preg_match('/(popupwt)/',$meta->link_types[$i])) {
									$_linka=preg_split('/(:)/',$meta->link_types[$i]);
									if (isset($_linka[1])&&($_linka[1])) $link_ttl="title=\"".Text::_($_linka[1])."\""; else $link_ttl="";
									$linktype = $link_ttl." class=\"relpopupwt\"";
								} elseif ($meta->link_types[$i]=='_blank') {
									$linktype = "target=\"_blank\"";
								} else $linktype="";
								if (strpos($link,'javascript')===0)	{
									$zn_js=1;	$_cell_onclick = " onclick=\"".$link."\"";
								} else	{
									if($meta->link_picture[$i]) {
										$img_link=$meta->link_picture[$i];
										$str_table="<img border=\"0\" src=\"".$img_link."\" alt=\"\" title=\"\" />";
									}
									$str_table="<a ".$linktype." href=\"".Router::_($link)."\">".$str_table."</a>";
								}
							}
						}
						$_cell_class="choice_".($zn_js==0 ? "aa" : "ss")." grid field_".$meta->field[$i]." val_".$meta->val_type[$i];
						if ($meta->ordering_field==$meta->field[$i]){ // это сортировочное поле
							if ($canModify) {
								$_cell_onclick="onclick=\"modifySpravOrdering(this,'".$module."','".$view."','".$layout."','".$id."','".$multy_code."','".$controller."');\"";
								$_cell_class="ordering";
							}
						}
						$_table_body_arr[$rowNum][$meta->field[$i]]['hidden']=false;
						$_table_body_arr[$rowNum][$meta->field[$i]]['html']=$str_table;
						if($meta->ch_table[$i]||$meta->ck_reestr[$i])	$_table_body_arr[$rowNum][$meta->field[$i]]['value']=$code;
						elseif($meta->field_is_method[$i]) $_table_body_arr[$rowNum][$meta->field[$i]]['value']=$str_table;
						else $_table_body_arr[$rowNum][$meta->field[$i]]['value']=$row->{$field_tbl};
						$_table_body_arr[$rowNum][$meta->field[$i]]['class']=$_cell_class;
						$_table_body_arr[$rowNum][$meta->field[$i]]['onclick']=$_cell_onclick;
						$_table_body_arr[$rowNum][$meta->field[$i]]['width']=trim($meta->size[$i]);
					} else { // это поле скрыто
						$field_tbl=$meta->field[$i];
						$_table_body_arr[$rowNum][$meta->field[$i]]['hidden']=true;
						$_table_body_arr[$rowNum][$meta->field[$i]]['html']="";
						if(method_exists($this, $meta->field_is_method[$i])){
							//$str_table=$this->{$meta->field_is_method[$i]}($id, $row);
							$str_table=$this->{$meta->field_is_method[$i]}($id, $row,$field_tbl);
						} else {
							$str_table="";
						}
						if($meta->field_is_method[$i])	$_table_body_arr[$rowNum][$meta->field[$i]]['value']=$str_table;
						else $_table_body_arr[$rowNum][$meta->field[$i]]['value']=$row->{$field_tbl};
						$_table_body_arr[$rowNum][$meta->field[$i]]['class']='';
						$_table_body_arr[$rowNum][$meta->field[$i]]['onclick']='';
						$_table_body_arr[$rowNum][$meta->field[$i]]['width']='';
					}
				}
			}
			// Let's do something with table_body_arr
			Event::raise("sprav_view.table_body_arr.prepared", array("module"=>$module, "class_name"=>__CLASS__, "func_name"=>__FUNCTION__, "meta"=>$meta), $_table_body_arr);
		}
		if($no_pages==0) {
			$pages_ref = $ref;
			if ($layout) $pages_ref.="&amp;layout=".$layout;
			if ($alias) $pages_ref.="&amp;alias=".$alias;
			if ($psid) $pages_ref.="&amp;psid=".$psid;
			if ($sort) $pages_ref.="&amp;sort=".$sort;
			$pages_ref.=$orderby_link;
			if ($trash) $pages_ref.="&amp;trash=".$trash;
			$_SESSION[$module][$view][$layout]['rf']=$pages_ref;
			$_html_footer = $this->appendLimitStringWF($is_ajax, $page, $pages_ref, $records_count_wf, $str_sql_wf, $reestr);
		}
		$templatePath = Portal::getInstance()->getTemplatePath().'html'.DS.'modules'.DS.$this->get('module').DS.$this->getName().DS.$layout.'.php';
		$this->message(Text::_("Looking for template").": ".$templatePath, __FUNCTION__);
		echo "<div id=\"".$this->sprav_list_id."\" class=\"moduleBody ".$this->get("module")."Module".($column_class ? " ".$column_class : "").$wrapper_class."\"><div class=\"content\">";
		if ((file_exists($templatePath)) && (is_file($templatePath))) require_once $templatePath;
		else {
			$templatePath = PATH_SITE.'modules'.DS.$this->get('module').DS."views".DS."template".DS.$this->getName().DS.$layout.'.php';
			$this->message(Text::_("Looking for template").": ".$templatePath, __FUNCTION__);
			if ((file_exists($templatePath)) && (is_file($templatePath))) require_once $templatePath;
			if (!isset($spr_tmpl_overrided))
				if($meta->selector)	SpravStatic::renderBaseTemplate("", $_html_pan, $_table_class, $filtered, $_table_header_arr, $_table_body_arr, $_table_body_settings_arr, $_html_footer);
				else SpravStatic::renderBaseTemplate($_form, $_html_pan, $_table_class, $filtered, $_table_header_arr, $_table_body_arr, $_table_body_settings_arr, $_html_footer);
			$this->_rendered=true;
		}
		echo "</div></div>";
		$this->milestone('End', __FUNCTION__);
	}
	
	public function prepareInfoArray(&$meta, &$row)	{
		$_info_arr=array();
		$this->milestone('Start', __FUNCTION__);
		// $user = User::getInstance();
		// $is_admin=$user->isAdmin();
		// $option=Request::get('option','module');
		// $psid=$reestr->get('psid');
		$reestr = Module::getInstance()->get('reestr');
		$module = Request::get('module');
		$view =Request::get('view');
		$layout = $this->get('layout');
		$controller= $this->get('controller');
		$canModify=$reestr->get("canModify");
		$KeyStringTabl=$meta->keystring;
		$id=urlencode($row->{$KeyStringTabl}); //забираем в переменную $id значение идешника реальной строки
		
		$fldcurrency=$meta->keycurrency;
		//$col_count = count($meta->field); //количество полей таблицы
		//for ($i = 1; $i <= $col_count; $i++) {
		foreach ($meta->field as $i => $field) {
			if($meta->input_view[$i]==1) { //если поле видимое то выводим на экран
				// $field = $meta->field[$i];
				if($meta->field_is_method[$i]){
					if(method_exists($this, $meta->field_is_method[$i])){
						$str_table=$this->{$meta->field_is_method[$i]}($id, $row,$field);
					} else {
						$str_table="";
					}
				} else {
					$code = $row->{$field}; $str_table="";
					$field_tbl = $meta->field[$i];
					if($meta->ck_reestr[$i]) {
						if (is_array($meta->ck_reestr[$i])) $key_arr=$meta->ck_reestr[$i];
						else $key_arr=SpravStatic::getCKArray($meta->ck_reestr[$i]);
						if(is_array($key_arr) && $meta->input_type[$i] == "multiselect"){
							$code = explode(";", trim($code, ";"));
							$ms_code = array();
							if(is_array($code)){
								foreach($code as $ms_key=>$ms_val){
									if(array_key_exists($ms_val, $key_arr)){
										$ms_code[]=$key_arr[$ms_val];
									}
								}
								$row->{$field} = implode(", ", $ms_code);
							} else $row->{$field}="";
						} elseif(is_array($key_arr) && array_key_exists($code, $key_arr)) {
							$row->{$field}=$key_arr[$code];
						} else {
							$row->{$field}="";
						}
					} elseif ($meta->ch_table[$i]) {
						if ($meta->ch_table[$i]) {
							$field_tbl=$meta->field[$i]."_sql_replace";
						} else {
							$field_tbl=$meta->field[$i];
						}
					}
				}
				
				
				if ($meta->val_type[$i]=='date') {
					$str_table=Date::fromSQL($row->{$field_tbl},true);
				}
				elseif ($meta->val_type[$i]=='datetime') 	{
					if($row->{$field_tbl} != '0000-00-00 00:00:00') $str_table=date("d.m.y H:i", Date::GetTimestamp($row->{$field_tbl},1));else $str_table = '';
				}
				elseif ($meta->val_type[$i]=='timestamp') {
					$str_table=Date::fromSQL($row->{$field_tbl},false,false);
				}
				elseif ($meta->val_type[$i]=='text') 			{
					if(!$str_table) $str_table=html_entity_decode($row->{$field_tbl},ENT_COMPAT,DEF_CP);
				}
				elseif ($meta->val_type[$i]=='string') {
					switch($meta->input_type[$i])	{
						case 'file':
							if(empty($row->{$field_tbl})) {
								$str_table="";
							} else {
								$pathfile=BARMAZ_UF."/".$module."/f/".$row->{$field_tbl};
								$str_table="<a href=\"".$pathfile."\" target=\"_blank\"><img src=\"/images/download3.gif\" width=\"16\" height=\"16\" alt=\"\" border=\"0\" /></a>";
							}
							break;
						case 'image':
							if(empty($row->{$field_tbl})) {
								$tmpl_img="/images/nophoto.png";
								$str_table = "<img src=\"".$tmpl_img."\" width=\"".Module::getInstance()->getParam("thumb_width", false, catalogConfig::$thumb_width)."\" alt=\"\" />";
							} else 	{
								if(isset($meta->img_module[$i])&&$meta->img_module[$i]){
									$tmpl_img=BARMAZ_UF."/".$meta->img_module[$i]."/".$meta->upload_path[$i]."/".Files::splitAppendix($row->{$field_tbl});
									$tmpl_img_path=BARMAZ_UF_PATH.$meta->img_module[$i].DS.str_replace("/",DS,$meta->upload_path[$i]).DS.Files::splitAppendix($row->{$field_tbl}, true);
								}
								else
								{
									$tmpl_img=BARMAZ_UF."/".$meta->module."/".$meta->upload_path[$i]."/".Files::splitAppendix($row->{$field_tbl});
									$tmpl_img_path=BARMAZ_UF_PATH.$meta->module.DS.str_replace("/",DS,$meta->upload_path[$i]).DS.Files::splitAppendix($row->{$field_tbl}, true);
								}
								if ((file_exists($tmpl_img_path))&&(is_file($tmpl_img_path))) {
									if (Files::mime_content_type($tmpl_img_path)=="application/x-shockwave-flash") {
										$str_table = "<a class=\"relpopup\" href=\"".$tmpl_img."\"><img src=\"/images/swf.png\" width=\"".Module::getInstance()->getParam("thumb_width", false, catalogConfig::$thumb_width)."\" alt=\"\" /></a>";
									} else {
										$str_table = "<a class=\"relpopup\" href=\"".$tmpl_img."\"><img src=\"".$tmpl_img."\" width=\"".Module::getInstance()->getParam("thumb_width", false, catalogConfig::$thumb_width)."\" alt=\"\" /></a>";
									}
								} else  {
									$tmpl_img="/images/nophoto.png";
									$str_table = "<img src=\"".$tmpl_img."\" width=\"".Module::getInstance()->getParam("thumb_width", false, catalogConfig::$thumb_width)."\" alt=\"\" />";
								}
							}
							break;
						case 'address': // это выбор из списка категории
							if(empty($row->{$field_tbl})) {
								$str_table="";
							} else {
								$str_table = Address::decode($row->{$field_tbl}, true);
							}
							break;
						default:
							$str_table=$row->{$field_tbl};
							break;
					}
				}
				elseif ($meta->val_type[$i]=='constanta') 	$str_table=Text::_($row->{$field_tbl});
				elseif ($meta->val_type[$i]=='boolean') {
					if ($row->{$field_tbl}) $switcherClass="on"; else $switcherClass="off";
					if (($meta->field[$i]==$meta->enabled)&&($canModify)){
						$str_table="<div onclick=\"toggleEnabled(this,'".$module."','".$view."','".$layout."','".$id."','".$controller."');\" class=\"switcher_".$switcherClass."\">&nbsp;</div>";
					} else {
						$str_table="<div class=\"boolean_".$switcherClass."\">&nbsp;</div>";
					}
				}
				elseif ($meta->val_type[$i]=='currency') {
					if($fldcurrency) $val_currency=$row->{$fldcurrency}; else $val_currency="";
					$str_table=number_format(Currency::getInstance()->convert($row->{$field_tbl}, $val_currency), catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR);
				}
				else $str_table=$row->{$field_tbl};
				
				if($meta->translate_value[$i]) $str_table=Text::_($str_table);
				// подстановка линков на эелементы строки
				$link='';	$zn_js=0; $_cell_onclick ="";
				if($meta->link[$i])	{
					if($meta->link[$i]=='field') {
						//когда у нас в качестве ссылки работает содержимое поля достаем картинку
						if(!empty($str_table)) {
							if($meta->link_picture[$i]) {
								$img_link=$meta->link_picture[$i];
							}
							else {
								$img_link="/images/download3.gif";
							}
							// определяем направление ссылки (оно у нас в ключах живет в этом варианте)
							if($meta->link_vars[$i]) {
								$target_link=$meta->link_vars[$i];
							}
							else {
								$target_link="_blank";
							}
							// ну и формируем самы ссылку из содержания поля
							$str_table="<a href=\"".Router::_($str_table)."\" target=\"".$target_link."\"><img border=\"0\" src=\"".$img_link."\" alt=\"\" title=\"\" /></a>";
						}
					} else {// другие случаи
						// парсинг ключей линков ()
						$link=htmlspecialchars($meta->link[$i]);
						//echo $meta->link[$i]." ".$meta->link_vars[$i];
						if($meta->link_vars[$i]) {
							$arr_link_vars=explode(",",$meta->link_vars[$i]);
							if(count($arr_link_vars)>0)	{
								foreach($arr_link_vars as $lv_key=>$lv_var)	{
									if(isset($row->{$lv_var})) {
										$link=str_replace("[".$lv_key."]",$row->{$lv_var}, $link);
									} else {
										$link=str_replace("[".$lv_key."]",${$lv_var}, $link);
									}
								}
							}
						}
						if ($meta->link_types[$i]=='popup') {
							$linktype = " class=\"relpopup\"";
						}
						elseif (preg_match('/(popupwt)/',$meta->link_types[$i])) {
							$_linka=preg_split('/(:)/',$meta->link_types[$i]);
							if (isset($_linka[1])&&($_linka[1])) $link_ttl="title=\"".Text::_($_linka[1])."\""; else $link_ttl="";
							$linktype = $link_ttl." class=\"relpopupwt\"";
						} else $linktype="";
						if (strpos($link,'javascript')===0)	{
							$zn_js=1;		$_cell_onclick = " onclick=\"".$link."\"";
						} else	{ $str_table="<a ".$linktype." href=\"".Router::_($link)."\">".$str_table."</a>";
						}
					}
				}
				
				$_cell_class="choice_".($zn_js==0 ? "aa" : "ss")." grid field_".$meta->field[$i];
				$_cell_onclick="";
				if ($meta->ordering_field==$meta->field[$i]){ // это сортировочное поле
					if ($canModify) {
						$_cell_onclick="onclick=\"modifySpravOrdering(this,'".$module."','".$view."','".$layout."','".$id."','".$multy_code."','".$controller."');\"";
						$_cell_class="ordering";
					}
				}
				$_info_arr[$meta->field[$i]]['hidden']=false;
				$_info_arr[$meta->field[$i]]['type']=$meta->val_type[$i];
				$_info_arr[$meta->field[$i]]['input_type']=$meta->input_type[$i];
				$_info_arr[$meta->field[$i]]['html']=$str_table;
				if($meta->ch_table[$i]||$meta->ck_reestr[$i])	$_info_arr[$meta->field[$i]]['value']=$code;
				else $_info_arr[$meta->field[$i]]['value']=$row->{$field_tbl};
				$_info_arr[$meta->field[$i]]['class']=$_cell_class;
				$_info_arr[$meta->field[$i]]['onclick']=$_cell_onclick;
				$_info_arr[$meta->field[$i]]['width']=trim($meta->size[$i]);
				if ($meta->val_type[$i]=='currency') $addTitle=' ('.Currency::getShortName(DEFAULT_CURRENCY).')'; else $addTitle='';
				$_info_arr[$meta->field[$i]]['title']=Text::_($meta->field_title[$i]).$addTitle;
			} else { // это поле скрыто
				$_info_arr[$meta->field[$i]]['hidden']=true;
				$_info_arr[$meta->field[$i]]['type']="string";
				$_info_arr[$meta->field[$i]]['input_type']=$meta->input_type[$i];
				$_info_arr[$meta->field[$i]]['html']="";
				if($meta->field_is_method[$i]) $_info_arr[$meta->field[$i]]['value']=$str_table;
				else $_info_arr[$meta->field[$i]]['value']=$row->{$meta->field[$i]};
				$_info_arr[$meta->field[$i]]['class']='';
				$_info_arr[$meta->field[$i]]['onclick']='';
				$_info_arr[$meta->field[$i]]['width']='';
				$_info_arr[$meta->field[$i]]['title']='';
			}
		}
		$this->milestone('End', __FUNCTION__);
		return $_info_arr;
	}
	public function renderInfo(&$meta, &$row)	{
		// кнопки слева, заголовок, кнопки справа
		$_info_arr=array();
		// получаем необходимые переменные
		$reestr = Module::getInstance()->get('reestr');
		// $user = User::getInstance();
		// $is_admin=$user->isAdmin();
		$this->milestone('Start', __FUNCTION__);
		$module = Request::get('module');
		$view =Request::get('view');
		// $option=Request::get('option','module');
		$layout = $this->get('layout');
		$psid=$reestr->get('psid');
		$controller= $this->get('controller');
		$canModify=$reestr->get("canModify");
		//$KeyStringTabl=$meta->keystring;
		//$fldcurrency=$meta->keycurrency;
		if(is_object($row)){
			// проверка необходимости проставлять чеки строк
			//$id=urlencode($row->{$KeyStringTabl}); //забираем в переменную $id значение идешника реальной строки
			$namestring=$meta->namestring;
			$title=htmlspecialchars($row->{$namestring});
			$_info_arr=$this->prepareInfoArray($meta, $row);
		}
		// Let's do something with info_arr
		Event::raise("sprav_view.info_arr.prepared", array("module"=>$module, "class_name"=>__CLASS__, "func_name"=>__FUNCTION__, "meta"=>$meta), $_info_arr);
		$templatePath = Portal::getInstance()->getTemplatePath().'html'.DS.'modules'.DS.$this->get('module').DS.$this->getName().DS.$layout.'.php';
		$this->message(Text::_("Looking for template").": ".$templatePath, __FUNCTION__);
		echo "<div class=\"moduleBody ".$this->get("module")."Module\"><div class=\"content\">";
		if ($meta->title) $title=Text::_($meta->title); else $title="";
		if ((file_exists($templatePath)) && (is_file($templatePath))) require_once $templatePath;
		else {
			echo "<div class=\"container\"><div class=\"row\"><div class=\"col-md-12\">";
			$templatePath = PATH_SITE.'modules'.DS.$this->get('module').DS."views".DS."template".DS.$this->getName().DS.$layout.'.php';
			$this->message(Text::_("Looking for template").": ".$templatePath, __FUNCTION__);
			if ((file_exists($templatePath)) && (is_file($templatePath))) require_once $templatePath;
			if (!isset($spr_tmpl_overrided)){
				echo "<div id=\"info-wrapper\" class=\"rounded-pan".($meta->classTable ? " ".$meta->classTable : "")."\">";
				if($title) echo "<h4 class=\"title\">".$title."</h4>";
				if (is_array($_info_arr)) {
					foreach($_info_arr as $_fk=>$_cell) {
						if ($_cell['hidden']) continue;
						switch($_cell['input_type']){
							case "textarea":
							case "formated":
							case "texteditor":
								echo "<div class=\"spravInfo row ".$_cell['class']."\">";
								echo "<div class=\"col-xs-12\"><div class=\"info-label\">".$_cell['title'].": </div></div>";
								echo "<div class=\"col-xs-12\"><div class=\"info-data info-data-formated\" ".$_cell['onclick'].">".$_cell['html']."</div></div>";
								echo "</div>\n";
								break;
							default:
								echo "<div class=\"spravInfo row ".$_cell['class']."\">";
								echo "<div class=\"col-xs-6\"><div class=\"info-label\">".$_cell['title'].": </div></div>";
								echo "<div class=\"col-xs-6\"><div class=\"info-data\" ".$_cell['onclick'].">".$_cell['html']."</div></div>";
								echo "</div>\n";
								break;
						}
					}
				} else {
					echo "<div class=\"spravInfo row\"><div class=\"col-xs-12\">".Text::_("Does not contain the data")."</div></div>";
				}
				echo "</div>";
			}
			echo "</div></div></div>";
		}
		
		
		
		echo "</div></div>";
		$this->milestone('End', __FUNCTION__);
	}
	
	
	
/*
	public function renderInfo(&$meta, &$row)	{
		// кнопки слева, заголовок, кнопки справа
		$_info_arr=array();
		// получаем необходимые переменные
		$user = User::getInstance();
		$mdl = Module::getInstance();
		$reestr = $mdl->get('reestr');
		$is_admin=$user->isAdmin();
		$this->milestone('Start', __FUNCTION__);
		$module = Request::get('module');
		$view =Request::get('view');
		$option=Request::get('option','module');
		$layout = $this->get('layout');
		$psid=$reestr->get('psid');
		$controller= $this->get('controller');
		$canModify=$reestr->get("canModify");
		$KeyStringTabl=$meta->keystring;
		$fldcurrency=$meta->keycurrency;
		if(is_object($row)){
			// проверка необходимости проставлять чеки строк
			//$col_count = count($meta->field); //количество полей таблицы
			$id=urlencode($row->{$KeyStringTabl}); //забираем в переменную $id значение идешника реальной строки
			$namestring=$meta->namestring;
			$title=htmlspecialchars($row->{$namestring});
	
			//for ($i = 1; $i <= $col_count; $i++) {
			foreach ($meta->field as $i => $field) { 
				if($meta->input_view[$i]==1)  { //если поле видимое то выводим на экран
					// $field = $meta->field[$i];
					if($meta->field_is_method[$i]){
						if(method_exists($this, $meta->field_is_method[$i])){
							$str_table=$this->{$meta->field_is_method[$i]}($id, $row,$field);
						} else {
							$str_table="";
						}
					} else {
						$code = $row->{$field}; $str_table="";
						$field_tbl = $meta->field[$i];
						if($meta->ck_reestr[$i]) {
							if (is_array($meta->ck_reestr[$i])) $key_arr=$meta->ck_reestr[$i];
							else $key_arr=SpravStatic::getCKArray($meta->ck_reestr[$i]);
							if(is_array($key_arr) && $meta->input_type[$i] == "multiselect"){
								$code = explode(";", trim($code, ";"));
								$ms_code = array();
								if(is_array($code)){
									foreach($code as $ms_key=>$ms_val){
										if(array_key_exists($ms_val, $key_arr)){
											$ms_code[]=$key_arr[$ms_val];
										}
									}
									$row->{$field} = implode(", ", $ms_code);
								} else $row->{$field}="";
							} elseif(is_array($key_arr) && array_key_exists($code, $key_arr)) {
								$row->{$field}=$key_arr[$code];
							} else {
								$row->{$field}="";
							}
						} elseif ($meta->ch_table[$i]) {
							if ($meta->ch_table[$i]) {
								$field_tbl=$meta->field[$i]."_sql_replace";
							} else {
								$field_tbl=$meta->field[$i];
							}
						}
					}
					
					
					if ($meta->val_type[$i]=='date') {
						$str_table=Date::fromSQL($row->{$field_tbl},true);
					}
					elseif ($meta->val_type[$i]=='datetime') 	{
						if($row->{$field_tbl} != '0000-00-00 00:00:00') $str_table=date("d.m.y H:i", Date::GetTimestamp($row->{$field_tbl},1));else $str_table = '';
					}
					elseif ($meta->val_type[$i]=='timestamp') {
						$str_table=Date::fromSQL($row->{$field_tbl},false,false);
					}
					elseif ($meta->val_type[$i]=='text') 			{
						if(!$str_table) $str_table=html_entity_decode($row->{$field_tbl},ENT_COMPAT,DEF_CP);
					}
					elseif ($meta->val_type[$i]=='string') {
						switch($meta->input_type[$i])	{
							case 'file':
								if(empty($row->{$field_tbl})) {
									$str_table="";
								} else {
									$pathfile=BARMAZ_UF."/".$module."/f/".$row->{$field_tbl};
									$str_table="<a href=\"".$pathfile."\" target=\"_blank\"><img src=\"/images/download3.gif\" width=\"16\" height=\"16\" alt=\"\" border=\"0\" /></a>";
								}
						  break;
							case 'image':
								if(empty($row->{$field_tbl})) {
									$tmpl_img="/images/nophoto.png";
									$str_table = "<img src=\"".$tmpl_img."\" width=\"".Module::getInstance()->getParam("thumb_width", false, catalogConfig::$thumb_width)."\" alt=\"\" />";
								} else 	{
									if(isset($meta->img_module[$i])&&$meta->img_module[$i]){
										$tmpl_img=BARMAZ_UF."/".$meta->img_module[$i]."/".$meta->upload_path[$i]."/".Files::splitAppendix($row->{$field_tbl});
										$tmpl_img_path=BARMAZ_UF_PATH.$meta->img_module[$i].DS.str_replace("/",DS,$meta->upload_path[$i]).DS.Files::splitAppendix($row->{$field_tbl}, true);
									}
									else
									{
										$tmpl_img=BARMAZ_UF."/".$meta->module."/".$meta->upload_path[$i]."/".Files::splitAppendix($row->{$field_tbl});
										$tmpl_img_path=BARMAZ_UF_PATH.$meta->module.DS.str_replace("/",DS,$meta->upload_path[$i]).DS.Files::splitAppendix($row->{$field_tbl}, true);
									}
									if ((file_exists($tmpl_img_path))&&(is_file($tmpl_img_path))) {
										if (Files::mime_content_type($tmpl_img_path)=="application/x-shockwave-flash") {
											$str_table = "<a class=\"relpopup\" href=\"".$tmpl_img."\"><img src=\"/images/swf.png\" width=\"".Module::getInstance()->getParam("thumb_width", false, catalogConfig::$thumb_width)."\" alt=\"\" /></a>";
										} else {
											$str_table = "<a class=\"relpopup\" href=\"".$tmpl_img."\"><img src=\"".$tmpl_img."\" width=\"".Module::getInstance()->getParam("thumb_width", false, catalogConfig::$thumb_width)."\" alt=\"\" /></a>";
										}
									} else  {
										$tmpl_img="/images/nophoto.png";
										$str_table = "<img src=\"".$tmpl_img."\" width=\"".Module::getInstance()->getParam("thumb_width", false, catalogConfig::$thumb_width)."\" alt=\"\" />";
									}
								}
								break;
							case 'address': // это выбор из списка категории
								if(empty($row->{$field_tbl})) {
									$str_table="";
								} else {
									$str_table = Address::decode($row->{$field_tbl}, true);
								}
								break;
							default:
								$str_table=$row->{$field_tbl};
						  break;
						}
					}
					elseif ($meta->val_type[$i]=='constanta') 	$str_table=Text::_($row->{$field_tbl});
					elseif ($meta->val_type[$i]=='boolean') {
						if ($row->{$field_tbl}) $switcherClass="on"; else $switcherClass="off";
						if (($meta->field[$i]==$meta->enabled)&&($canModify)){
							$str_table="<div onclick=\"toggleEnabled(this,'".$module."','".$view."','".$layout."','".$id."','".$controller."');\" class=\"switcher_".$switcherClass."\">&nbsp;</div>";
						} else {
							$str_table="<div class=\"boolean_".$switcherClass."\">&nbsp;</div>";
						}
					}
					elseif ($meta->val_type[$i]=='currency') {
						if($fldcurrency) $val_currency=$row->{$fldcurrency}; else $val_currency="";
						$str_table=number_format(Currency::getInstance()->convert($row->{$field_tbl}, $val_currency), catalogConfig::$price_digits, DEFAULT_DECIMAL_SEPARATOR , DEFAULT_THOUSAND_SEPARATOR);
					}
					else $str_table=$row->{$field_tbl};
	
					if($meta->translate_value[$i]) $str_table=Text::_($str_table);
					// подстановка линков на эелементы строки
					$link='';	$zn_js=0; $_cell_onclick ="";
					if($meta->link[$i])	{
						if($meta->link[$i]=='field') {
							//когда у нас в качестве ссылки работает содержимое поля достаем картинку
							if(!empty($str_table)) {
								if($meta->link_picture[$i]) {
									$img_link=$meta->link_picture[$i];
								}
								else { 
									$img_link="/images/download3.gif";
								}
								// определяем направление ссылки (оно у нас в ключах живет в этом варианте)
								if($meta->link_vars[$i]) {
									$target_link=$meta->link_vars[$i];
								}
								else { 
									$target_link="_blank";
								}
								// ну и формируем самы ссылку из содержания поля
								$str_table="<a href=\"".Router::_($str_table)."\" target=\"".$target_link."\"><img border=\"0\" src=\"".$img_link."\" alt=\"\" title=\"\" /></a>";
							}
						} else {// другие случаи
							// парсинг ключей линков ()
							$link=htmlspecialchars($meta->link[$i]);
							//echo $meta->link[$i]." ".$meta->link_vars[$i];
							if($meta->link_vars[$i]) {
								$arr_link_vars=explode(",",$meta->link_vars[$i]);
								if(count($arr_link_vars)>0)	{
									foreach($arr_link_vars as $lv_key=>$lv_var)	{
										if(isset($row->{$lv_var})) {
											$link=str_replace("[".$lv_key."]",$row->{$lv_var}, $link);
										} else {
											$link=str_replace("[".$lv_key."]",${$lv_var}, $link);
										}
									}
								}
							}
							if ($meta->link_types[$i]=='popup') {
								$linktype = " class=\"relpopup\"";
							}
							elseif (preg_match('/(popupwt)/',$meta->link_types[$i])) {
								$_linka=preg_split('/(:)/',$meta->link_types[$i]);
								if (isset($_linka[1])&&($_linka[1])) $link_ttl="title=\"".Text::_($_linka[1])."\""; else $link_ttl="";
								$linktype = $link_ttl." class=\"relpopupwt\"";
							} else $linktype="";
							if (strpos($link,'javascript')===0)	{
								$zn_js=1;		$_cell_onclick = " onclick=\"".$link."\"";
							} else	{ $str_table="<a ".$linktype." href=\"".Router::_($link)."\">".$str_table."</a>";
							}
						}
					}
	
					$_cell_class="choice_".($zn_js==0 ? "aa" : "ss")." grid field_".$meta->field[$i];
					$_cell_onclick="";
					if ($meta->ordering_field==$meta->field[$i]){ // это сортировочное поле
						if ($canModify) {
							$_cell_onclick="onclick=\"modifySpravOrdering(this,'".$module."','".$view."','".$layout."','".$id."','".$multy_code."','".$controller."');\"";
							$_cell_class="ordering";
						}
					}
					$_info_arr[$meta->field[$i]]['hidden']=false;
					$_info_arr[$meta->field[$i]]['type']=$meta->val_type[$i];
					$_info_arr[$meta->field[$i]]['input_type']=$meta->input_type[$i];
					$_info_arr[$meta->field[$i]]['html']=$str_table;
					if($meta->ch_table[$i]||$meta->ck_reestr[$i])	$_info_arr[$meta->field[$i]]['value']=$code;
					else $_info_arr[$meta->field[$i]]['value']=$row->{$field_tbl};
					$_info_arr[$meta->field[$i]]['class']=$_cell_class;
					$_info_arr[$meta->field[$i]]['onclick']=$_cell_onclick;
					$_info_arr[$meta->field[$i]]['width']=trim($meta->size[$i]);
					if ($meta->val_type[$i]=='currency') $addTitle=' ('.Currency::getShortName(DEFAULT_CURRENCY).')'; else $addTitle='';
					$_info_arr[$meta->field[$i]]['title']=Text::_($meta->field_title[$i]).$addTitle;
				} else { // это поле скрыто
					$_info_arr[$meta->field[$i]]['hidden']=true;
					$_info_arr[$meta->field[$i]]['type']="string";
					$_info_arr[$meta->field[$i]]['input_type']=$meta->input_type[$i];
					$_info_arr[$meta->field[$i]]['html']="";
					if($meta->field_is_method[$i]) $_info_arr[$meta->field[$i]]['value']=$str_table;
					else $_info_arr[$meta->field[$i]]['value']=$row->{$meta->field[$i]};
					$_info_arr[$meta->field[$i]]['class']='';
					$_info_arr[$meta->field[$i]]['onclick']='';
					$_info_arr[$meta->field[$i]]['width']='';
					$_info_arr[$meta->field[$i]]['title']='';
	
				}
			}
		}
		// Let's do something with info_arr
		Event::raise("sprav_view.info_arr.prepared", array("module"=>$module, "class_name"=>__CLASS__, "func_name"=>__FUNCTION__, "meta"=>$meta), $_info_arr);
		$templatePath = Portal::getInstance()->getTemplatePath().'html'.DS.'modules'.DS.$this->get('module').DS.$this->getName().DS.$layout.'.php';
		$this->message(Text::_("Looking for template").": ".$templatePath, __FUNCTION__);
		echo "<div class=\"moduleBody ".$this->get("module")."Module\"><div class=\"content\">";
		if ($meta->title) $title=Text::_($meta->title); else $title="";
		if ((file_exists($templatePath)) && (is_file($templatePath))) require_once $templatePath;
		else {
			echo "<div class=\"container\"><div class=\"row\"><div class=\"col-md-12\">";
			$templatePath = PATH_SITE.'modules'.DS.$this->get('module').DS."views".DS."template".DS.$this->getName().DS.$layout.'.php';
			$this->message(Text::_("Looking for template").": ".$templatePath, __FUNCTION__);
			if ((file_exists($templatePath)) && (is_file($templatePath))) require_once $templatePath;
			if (!isset($spr_tmpl_overrided)){
				echo "<div id=\"info-wrapper\" class=\"rounded-pan".($meta->classTable ? " ".$meta->classTable : "")."\">";
				if($title) echo "<h4 class=\"title\">".$title."</h4>";
				if (is_array($_info_arr)) {
					foreach($_info_arr as $_fk=>$_cell) {
						if ($_cell['hidden']) continue;
						switch($_cell['input_type']){
							case "textarea":
							case "formated":
							case "texteditor":
								echo "<div class=\"spravInfo row ".$_cell['class']."\">";
								echo "<div class=\"col-xs-12\"><div class=\"info-label\">".$_cell['title'].": </div></div>";
								echo "<div class=\"col-xs-12\"><div class=\"info-data info-data-formated\" ".$_cell['onclick'].">".$_cell['html']."</div></div>";
								echo "</div>\n";
							break;	
							default:
								echo "<div class=\"spravInfo row ".$_cell['class']."\">";
								echo "<div class=\"col-xs-6\"><div class=\"info-label\">".$_cell['title'].": </div></div>";
								echo "<div class=\"col-xs-6\"><div class=\"info-data\" ".$_cell['onclick'].">".$_cell['html']."</div></div>";
								echo "</div>\n";
							break;
						}
					}
				} else { 
					echo "<div class=\"spravInfo row\"><div class=\"col-xs-12\">".Text::_("Does not contain the data")."</div></div>";
				}
				echo "</div>";
			}
			echo "</div></div></div>";
		}
		
		
		
		echo "</div></div>";
		$this->milestone('End', __FUNCTION__);
	}
*/
	/*
	ref - ссылка для подстановки в пагинатор
	records_count - количиство записей полученное в запросе с фильтром
	sql_str - строка запроса без ограничений
	mode - режим отображения 1- (1-3 4-6 7-9) или 2 ( 1 2 3 4 и внизу записей ПОКАЗАНО из ВСЕГО)
	limit_ss - количество показываемых старниц в пагинаторе
	*/
	public function appendLimitStringWF( $is_ajax=false, $current_page, $ref, $records_count_wf=0, $str_sql_wf, $reestr, $mode=2, $limit_ss=0)	{
		$_html = "";
		if (!$limit_ss)	$limit_ss = defined("_ADMIN_MODE") ? adminConfig::$adminPagesPerPanel : siteConfig::$pagesPerPanel;
		$list_limit = intval(defined("_ADMIN_MODE") ? Module::getInstance()->getParam("Admin_page_size") : Module::getInstance()->getParam("Page_size"));
		if(empty($str_sql_wf)) return; // ошибка когда идет попытка отображения без предварительной подготовки данных (в классе spravochnik)
		$limit_str="";
		if ($records_count_wf) {
			$total=$records_count_wf;
		} else {
			$db = Database::getInstance();
			$db->setQuery($str_sql_wf);
			$res_query=$db->query();
			$total= $res_query ? $db->getNumRows(): false;  // макимальное число строк
		}
		if(!$total) return;
		$total_pages = ceil($total/$list_limit); // всего старниц
		if ($current_page > $total_pages) {
			$current_page=$total_pages;
		} else if ($current_page < 1) {
			$current_page=1;
		}
		$full_pages = floor($total/$list_limit); // полных страниц
		$last_appendix = $total - $full_pages * $list_limit; // остаток на последней странице
		if ($current_page < ceil($limit_ss/2)) {
			$sdvig_min=ceil($limit_ss/2)-$current_page;
		} else { 
			$sdvig_min = 0;
		}
		if ($current_page > $total_pages - ceil($limit_ss/2)) {
			$sdvig_max= ceil($limit_ss/2) + $current_page - $total_pages;
		} else { 
			$sdvig_max = 0;
		}
		$first_page = $current_page - ceil($limit_ss/2) + $sdvig_min; // первая отображаемая страница
		$last_page = $current_page + ceil($limit_ss/2) + $sdvig_min; // последняя отображаемая страница
		if ($sdvig_max > 0) {
			$first_page = $first_page - $sdvig_max; $last_page = $total_pages;
		}
		$page_range="";
		$js_onclick_prev="";
		$js_onclick_next="";
		$link_prev = "";
		$link_next = "";
		$link_prev_str = "";
		$link_next_str = "";

		$first_page = max(1, $first_page);
		$last_page = min($total_pages, $last_page);
		$controller=$reestr->get('controller');
		if ($is_ajax) {
			$_module = Request::getSafe('module');
			$_view = Request::getSafe('view');
			$_layout = Request::getSafe('layout');
			$_multy_code=$reestr->get('multy_code');
			$_trash=$reestr->get('trash');
			$_sort=$reestr->get('sort');
			$_orderby=$reestr->get('orderby');
			$_lol=$reestr->get('lol');
			$js_onclick_first="switchPageOnContList('".$_module."','".$_view."','".$_layout."','".$_multy_code."','".$controller."','".$_trash."','".$_sort."','".$_orderby."','1','".$_lol."','".$this->sprav_list_id."')";
			$link_first="";
			$link_first_str = "<li class=\"page-item\"><span class=\"pageLink firstPageLink navigator\" title=\"".Text::_("Go first record")."\" onclick=\"".$js_onclick_first."\">&lt;&lt;</span></li>";
			$js_onclick_last="switchPageOnContList('".$_module."','".$_view."','".$_layout."','".$_multy_code."','".$controller."','".$_trash."','".$_sort."','".$_orderby."','".$total_pages."','".$_lol."','".$this->sprav_list_id."')";
			$link_last="";
			$link_last_str = "<li class=\"page-item\"><span class=\"pageLink lastPageLink navigator\" title=\"".Text::_("Go last record")."\" onclick=\"".$js_onclick_last."\">&gt;&gt;</span></li>";
		} else {
			$js_onclick_first="";
			$js_onclick_last="";
			$link_first = Router::_($ref."&page=1"); 
			$link_last = Router::_($ref."&page=".$total_pages);
			$link_first_str = "<li class=\"page-item\"><a class=\"pageLink firstPageLink navigator\" title=\"".Text::_("Go first record")."\" href=\"".$link_first."\">&lt;&lt;</a></li>";
			$link_last_str = "<li class=\"page-item\"><a class=\"pageLink lastPageLink navigator\" title=\"".Text::_("Go last record")."\" href=\"".$link_last."\">&gt;&gt;</a></li>";
		}
		for ($i = $first_page ; $i < $last_page + 1; $i++) {
			// формируем текущий рэндж
			$cur_start = ($i-1) * $list_limit + 1; 	// начало
			$cur_end= min( $total, (($i-1) * $list_limit) + $list_limit);   // конец
			if ($cur_start!=$cur_end) {
				$cur_range=$cur_start."-".$cur_end;
			}	else {
				$cur_range = $cur_start;
			}
			if ($i==($current_page)) {	// попали в диапазон
				if ($mode==2) {
					$limit_str.="<li class=\"page-item active\"><span class=\"active-navigator\">".$i."</span></li>";
					$page_range=$cur_range;
				} else {
					$limit_str.="<li class=\"page-item active\"><span class=\"active-navigator\">".$cur_range."</span></li>";
				}
				if ($i>1){
					$prev_page = $i-1;
					if ($is_ajax) {
						$js_onclick_prev="switchPageOnContList('".$_module."','".$_view."','".$_layout."','".$_multy_code."','".$controller."','".$_trash."','".$_sort."','".$_orderby."','".$prev_page."','".$_lol."','".$this->sprav_list_id."')";
						$link_prev_str="<li class=\"page-item\"><span class=\"prevPageLink pageLink\" onclick=\"".$js_onclick_prev."\">&lt;</span></li>";
					} else {
						$link_prev=Router::_($ref.($prev_page > 1 ? "&page=".$prev_page : ""));
						$link_prev_str="<li class=\"page-item\"><a class=\"prevPageLink pageLink\" href=\"".$link_prev."\">&lt;</a></li>";
					}
				} else {
					$link_prev="";
					$link_prev_str="<li class=\"page-item\"><span class=\"prevPageLink\">&lt;</span></li>";
					$link_first="";
					$link_first_str="<li class=\"page-item\"><span class=\"firstPageLink\">&lt;&lt;</span></li>";
				}
				if($i<$last_page){
					$next_page = $i + 1;
					if ($is_ajax) {
						$js_onclick_next="switchPageOnContList('".$_module."','".$_view."','".$_layout."','".$_multy_code."','".$controller."','".$_trash."','".$_sort."','".$_orderby."','".$next_page."','".$_lol."','".$this->sprav_list_id."')";
						$link_next_str="<li class=\"page-item\"><span class=\"nextPageLink pageLink\" onclick=\"".$js_onclick_next."\">&gt;</span></li>";
					} else {
						$link_next=Router::_($ref.($next_page > 1 ? "&page=".$next_page : ""));
						$link_next_str="<li class=\"page-item\"><a class=\"nextPageLink pageLink\" href=\"".$link_next."\">&gt;</a></li>";
					}
				} else {
					$link_next="";
					$link_next_str="<li class=\"page-item\"><span class=\"nextPageLink\">&gt;</span></li>";
					$link_last="";
					$link_last_str="<li class=\"page-item\"><span class=\"lastPageLink\">&gt;&gt;</span></li>";
				}
			}	else { // а здесь ссылочка
				if ($is_ajax) {
					$js_onclick="switchPageOnContList('".$_module."','".$_view."','".$_layout."','".$_multy_code."','".$controller."','".$_trash."','".$_sort."','".$_orderby."','".$i."','".$_lol."','".$this->sprav_list_id."')";
					if ($mode==2) {
						$limit_str.="<li class=\"page-item\"><span class=\"pageLink\" onclick=\"".$js_onclick."\">".$i."</span></li>";
					} else	{
						$limit_str.="<li class=\"page-item\"><span class=\"pageLink\" onclick=\"".$js_onclick."\">".$cur_range."</span></li>";
					}
				} else {
					if ($mode==2) {
						$limit_str.="<li class=\"page-item\"><a class=\"pageLink\" href=\"".Router::_($ref.($i>1 ? "&page=".$i : ""))."\">".$i."</a></li>";
					} else	{
						$limit_str.="<li class=\"page-item\"><a class=\"pageLink\" href=\"".Router::_($ref.($i>1 ? "&page=".$i : ""))."\">".$cur_range."</a></li>";
					}
				}
			}
		}
		if (defined('_ADMIN_MODE')) $lPath = PATH_TEMPLATES.adminConfig::$adminTemplate.DS.'html'.DS.'modules'.DS.'paginator.php';
		else $lPath = PATH_TEMPLATES.Portal::getInstance()->getTemplate().DS.'html'.DS.'modules'.DS.'paginator.php';
		if (is_file($lPath)) {
			$this->assign("firstPageLink",$link_first);
			$this->assign("firstPageJS",$js_onclick_first);
			$this->assign("prevPageLink",$link_prev);
			$this->assign("prevPageJS",$js_onclick_prev);
			$this->assign("nextPageLink",$link_next);
			$this->assign("nextPageJS",$js_onclick_next);
			$this->assign("lastPageLink",$link_last);
			$this->assign("lastPageJS",$js_onclick_last);
			$this->assign("pageLinks",$limit_str);
			$this->assign("recordsTotal",$total);
			$this->assign("pageRange",$page_range);
			ob_start();
			include $lPath;
			$_html = ob_get_contents();
			ob_end_clean();
		} else {
			$limit_str = $link_first_str. $link_prev_str .  $limit_str . $link_next_str . $link_last_str;
			// выводим список страниц
			if ($total_pages>1 || $mode==2){
				$_html .= "<div class=\"navigator".($is_ajax ? " is_ajax" : "")."\">";
				if ($total_pages>1) {
					$_html .= "<div class=\"navigator_pages\"><ul class=\"pagination\">".$limit_str."</ul></div>";
				}
				if ($mode==2) {
					$_html .= "<div class=\"navigator_records\">".Text::_("Records")." " . $page_range . " ".Text::_("of")." " . $total."</div>";
				}
				$_html .= "</div>";
			}
		}
		return $_html;
	}
	public function renderUniButtons($meta, $position){
		$_html = "";
		$mdl = Module::getInstance();
		$reestr = $mdl->get('reestr');
		$multy_code=$reestr->get('multy_code');
		if (count($meta->uni_buttons)){
			foreach ($meta->uni_buttons as $key_name=>$key_arr)	{
				if(isset($key_arr['position']) && ($key_arr['position']==$position)) {
					$_link='#';
					$_on_click = "";
					$_title=$key_arr['title'];
					$_alt=$key_arr['alt'];
					if (isset($key_arr['class'])) $subclass=" ".$key_arr['class']; else $subclass="";
					if(isset($key_arr['link']))	{
						$_link=sprintf($key_arr['link'],$multy_code);
						if (substr($_link,0,10)=='javascript') {
							$_on_click="onclick=\"".$_link."\""; $_link='#';
						}
					}	else {
						if (isset($key_arr['module'])) $_mod=$key_arr['module']; else $_mod="";
						if (isset($key_arr['view'])) $_view=$key_arr['view']; else $_view="";
						if (isset($key_arr['layout'])) $_layout=$key_arr['layout']; else $_layout="";
						if (isset($key_arr['task'])) $_task=$key_arr['task']; else $_task="";
						if (isset($key_arr['option'])) $_option=$key_arr['option']; else $_option=0;
						if (isset($key_arr['target'])) $_target=$key_arr['target']; else $_target=0;
						if (isset($key_arr['controller'])) $_controller=$key_arr['controller']; else $_controller="default";
						if (isset($key_arr['reset_multy'])) $reset_multy=$key_arr['reset_multy']; else $reset_multy='false';
						if (isset($key_arr['confirm'])) {
							$confirm=Text::_($key_arr['confirm']);
							if (isset($key_arr['alert'])) {
								$_alert=Text::_($key_arr['alert']);
								$_on_click="onclick=\"javascript: if (isChecked()!=1) { alert('".$_alert."'); return false;} else { if (confirm('".$confirm." ?')) {submitbutton('".$_mod."','".$_view."','".$_layout."','".$_task."',false,'".$_target."','".$_option."',".$reset_multy.",'".$_controller."'); return false; } else return false;} \"";
							} else {
								$_on_click="onclick=\"javascript: if (confirm('".$confirm." ?')) {submitbutton('".$_mod."','".$_view."','".$_layout."','".$_task."',false,'".$_target."','".$_option."',".$reset_multy.",'".$_controller."'); return false; } else return false; \"";
							}
						}	elseif (isset($key_arr['alert'])) {
							$_alert=Text::_($key_arr['alert']);
							$_on_click="onclick=\"javascript:if (isChecked()!=1) { alert('".$_alert."'); return false;} else {submitbutton('".$_mod."','".$_view."','".$_layout."','".$_task."',false,'".$_target."','".$_option."',".$reset_multy.",'".$_controller."');return false;}\"";
						} else {
							$_on_click="onclick=\"javascript:submitbutton('".$_mod."','".$_view."','".$_layout."','".$_task."',false,'".$_target."','".$_option."',".$reset_multy.",'".$_controller."');return false; \"";
						}
					}
					$_html.= "<div class=\"picto_".$position.$subclass."\"><a href=\"".Router::_($_link)."\" ".$_on_click."><img class=\"sprav-button sprav-button-".$key_name."\" src=\"/images/blank.gif\" alt=\"\" title=\"".Text::_($_title)."\" /></a></div>\n";
				}
			}
		}
		if ($_html) {
			$_separator=$this->renderButton($meta,'separator','','',false,$position,false);
			$_html=$_separator.$_html;
		}
		return $_html;
	}
	public function renderButton($meta,$name,$title, $alert="", $confirm, $position, $is_js, $need_selection=true){
		if ($name=="separator")	return "<div class=\"separator_".$position."\"></div>\n";
		$_html = "";
		$mdl=Module::getInstance();
		$reestr = $mdl->get('reestr');
		$trash= $reestr->get('trash',0);
		$multy_code=$reestr->get('multy_code',0);
		if(isset($meta->buttons[$name])&&$meta->buttons[$name]['title']) $title=$meta->buttons[$name]['title'];
		$module=$meta->module;
		$viewName=$meta->viewName;
		$layoutName=$meta->layoutName;
		$sort = $reestr->get('sort');
		$page = $reestr->get('page', 1);
		$orderby_link = "&amp;orderby=".$reestr->get('orderby');
		$onclick="return true;";
		if (isset($meta->buttons[$name])&&($meta->buttons[$name]['show'])) { // при указанной видимости сервиса подключаем его
			if ($meta->buttons[$name]['task']) $_task='&amp;task='.$meta->buttons[$name]['task']; else $_task="";
			if ($meta->buttons[$name]['module']) $_mod=$meta->buttons[$name]['module']; else $_mod="";
			if ($meta->buttons[$name]['view']) $_view='&amp;view='.$meta->buttons[$name]['view']; else $_view="";
			if ($meta->buttons[$name]['layout']) $_layout='&amp;layout='.$meta->buttons[$name]['layout']; else $_layout="";
			if ($meta->buttons[$name]['controller']) $_controller='&amp;controller='.$meta->buttons[$name]['controller']; else $_controller="";
			if ($meta->buttons[$name]['link'])	{
				$href=$meta->buttons[$name]['link'];
			}
			else	{
				if ($is_js) {
					$href="#";
					if ($need_selection) {
						$onclick="javascript:if(isChecked()!=1)";
						if ($alert) $onclick.="{alert('".Text::_($alert)."');  return false; }";
						if ($confirm) $onclick.="else{if(confirm('".Text::_($confirm)." ?'))"; else $onclick.="else{";
					} else {
						$onclick="javascript:";
						if ($confirm) $onclick.="if(!confirm('".Text::_($confirm)." ?')) { return false;} else {"; else $onclick.="{";
					}
					switch ($name) {
						case "filter":
							$onclick="javascript:showFilter('".$module."','".$meta->buttons[$name]['view']."','".$meta->buttons[$name]['layout']."','".$multy_code."','".Text::_("Filter")."',".$trash.",'".$meta->buttons[$name]['controller']."'); return false;"; 	break;
						case "print":
							$onclick.="submitbutton('".$meta->buttons[$name]['module']."','".$meta->buttons[$name]['view']."','".$meta->buttons[$name]['layout']."','".$meta->buttons[$name]['task']."','','_blank','0',false,'".$meta->buttons[$name]['controller']."'); return false;}";	break;
						case "clean_trash":
							$onclick.="cleanTrash('start','".$module."','".$viewName."','".$layoutName."','".$multy_code."',0,'".$meta->buttons[$name]['controller']."'); return false;}"; 	break;
							break;
						default:
							$onclick.="submitbutton('".$meta->buttons[$name]['module']."','".$meta->buttons[$name]['view']."','".$meta->buttons[$name]['layout']."','".$meta->buttons[$name]['task']."','','','0',false,'".$meta->buttons[$name]['controller']."');return false;}";	break;
					}
				} else {
					if ($confirm) $onclick="javascript:if(confirm('".Text::_($confirm)." ?')) { return true;} else {return false; }"; else $onclick="";
					switch ($name) {
						case "go_up":
							if ($meta->parent_view) $parent_view = $meta->parent_view; else $parent_view='';
							if ($meta->parent_layout) $parent_layout = $meta->parent_layout; else $parent_layout='';
							$href="index.php?module=".$module.$_controller."&amp;view=".$parent_view."&amp;layout=".$parent_layout."&amp;return=1"; // определить родительский справочник
							break;
						case "list":
							$href="index.php?module=".$module.$_controller."&amp;view=".$viewName."&amp;layout=".$layoutName."&amp;sort=$sort".$orderby_link."&amp;multy_code=".$multy_code."&amp;page=".$page;
							break;
						case "trash":
							$href="index.php?module=".$module.$_controller."&amp;view=".$viewName."&amp;layout=".$layoutName."&amp;sort=$sort".$orderby_link."&amp;multy_code=".$multy_code."&amp;page=".$page."&amp;trash=1";
							break;
						case "clean_trash":
							$href="index.php?module=".$module.$_controller."&amp;view=".$viewName."&amp;layout=".$layoutName."&amp;sort=$sort".$orderby_link."&amp;multy_code=".$multy_code."&amp;page=".$page."&amp;trash=1&amp;task=cleanTrash";
							break;
						default:
							$href="index.php?module=".$_mod.$_controller.$_view.$_layout.$_task."&amp;sort=".$sort.$orderby_link."&amp;multy_code=".$multy_code."&amp;page=".$page;
							break;
					}
				}
			}
			$_html.= "<div class=\"picto_".$position."\">";
			$_html.= "	<a href=\"".Router::_($href)."\" onclick=\"".$onclick."\">";
			$_html.= "		<img class=\"sprav-button-".$name."\" src=\"/images/blank.gif\" alt=\"\" title=\"".Text::_($title)."\" />";
			$_html.= "	</a>";
			$_html.= "</div>";
		}
		return $_html;
	}
	public function modify($row)	{
		$mdl = Module::getInstance();
		$reestr = $mdl->get('reestr');
		$model=$reestr->get('model');
		$meta=$model->meta;
		$dop_tab=0; // дополнительные вкладки от языков
		if($meta==null) {
			$layout= $reestr->get('layout','');
			$view= $reestr->get('view','');
			$module= $mdl->getName();
			$meta = new SpravMetadata($module,$view,$layout);
			$this->meta=$meta;
		}
		$ajaxModify=$reestr->get('ajaxModify');
		$linkModify=false;
		$module=$meta->module;
		$layout=$reestr->get('layout');
		$view=$reestr->get('view','');
		$controller=$reestr->get('controller','default');
		$task=$reestr->get('task','save');
		$onCancelURL=$reestr->get('onCancelURL',false);
		$sort=$reestr->get('sort');
		$page=$reestr->get('page', 1);
		$orderby=$reestr->get('orderby');
		$psid=$reestr->get('psid');
		$multy_code=$reestr->get('multy_code');
		$fldcurrency=$meta->keycurrency;
		$parent_view=$meta->parent_view;
		if ($meta->multy_field==$meta->keystring) { // добрались до линковки
			if (($meta->parent_table)&&($meta->linktable)&&($meta->parent_code)) {
				$linkArray=$model->getLinkArray($psid);
				$isNewLink=false;
				if(count($linkArray)==0) {
					$prow=$model->getParentElement($multy_code);
					$parent_field_name=$meta->parent_name;
					if(isset($prow->{$parent_field_name})) {
						$isNewLink=true;
						$title=$prow->{$parent_field_name};
						$linkArray=array($multy_code=>array('id'=>$multy_code,'title'=>$title));
					}
				}
				Portal::getInstance()->addScriptDeclaration("$(window).on('load',function() { addAfterContentLoadHandler('modifySelector'); });");
				Portal::getInstance()->addScript("/redistribution/jquery.plugins/jquery.treeview.js");
				$linkModify=true;
			}
		}
		// проверяем дополнительные поля товара

		$classTable=$reestr->get('classTable', ($meta->classTable ? $meta->classTable : ""));
		$dop_head=$reestr->get('dop_head');
		//$col_count=count($meta->field);
		$input_type=$meta->input_type;
		$input_size=$meta->input_size;
		$this->milestone("Starting collecting Selects lists", __FUNCTION__);
		//вытаскиваем списки селектов и передаем их в $select_row
		//из $select_row уже в темплейте поодиночке их вытащим обратно
		//for ($i = 1; $i <=$col_count; $i++) {
		foreach ($meta->field as $i => $field) {
			// добавляем только для select
			if (($meta->input_type[$i]=='multiselect') ||($meta->input_type[$i]=='select') || ($meta->input_type[$i]=='label_sel')) {
				if (($meta->ch_table[$i])&&($meta->ch_field[$i])&&($meta->ch_id[$i]))	{
					if($meta->tablename==$meta->ch_table[$i]) $selfselect=true;
					//else if($meta->ch_parent_field[$i]) $selfselect=true;
					else $selfselect=false;
					// if ($meta->input_type[$i]=='select') $temp_row=$model->getValsForSelect($i);
					if (($meta->input_type[$i]=='select') || ($meta->input_type[$i]=='multiselect')) $temp_row=$model->getValsForSelect($i);
					elseif ($meta->input_type[$i]=='label_sel') {
						if ($meta->multy_field==$meta->field[$i]) $row_val = $reestr->get('multy_code'); else $row_val=0;
						//if (!array_key_exists($row_val, $select[$index])) $row_val=0;
						$temp_row=$model->getValsForSelect($i, $row_val);
					}
					$select_row[$i]=array	(
							"row"=>$temp_row,
							"field"=>$meta->ch_field[$i],
							"deleted"=>$meta->ch_deleted[$i],
							"enabled"=>$meta->ch_enabled[$i],
							"id"=>$meta->ch_id[$i],
							"is_table"=>true,
							"selfselect"=>$selfselect );
					$temp_row=null; //чистим мусор
				} elseif($meta->ck_reestr[$i]) {
					if (is_array($meta->ck_reestr[$i])) $key_arr=$meta->ck_reestr[$i];
					else $key_arr=SpravStatic::getCKArray($meta->ck_reestr[$i]);
					$select_row[$i]=array(
							"row"=>$key_arr,
							"field"=>$meta->field[$i],
							"deleted"=>"",
							"enabled"=>"",
							"id"=>'cr'.$i,
							"is_table"=>false,
							"selfselect"=>false );
					$key_arr=null; //чистим мусор
				}
			}
		}
		$this->milestone("End collecting Selects lists", __FUNCTION__);
		$select=array();
		$select_classes=array();
		if (isset($select_row)&&count($select_row)) {
			foreach ($select_row as $index=>$values)	{
				$select_temp=array();
				if($values["is_table"]) { // элемент является выбором из таблицы
					if ($values["selfselect"]||$meta->ch_parent_field[$index]) {
						$select[$index][0]=$this->sel_0_pref.Text::_("Has not parent").$this->sel_0_suff;
						$select_classes[$index][0]="no_selection";
						if($meta->ch_parent_field[$index]) $parent_ch_field=$meta->ch_parent_field[$index];
						else $parent_ch_field=$meta->field[$index];
						$ordered_values = $model->reorderTree($values["row"],$select_row[$index]["id"],$parent_ch_field,$select_row[$index]["field"]);
						if (count($ordered_values)) {
							foreach($ordered_values as $value)	{
								$field_val=$value->{$select_row[$index]["field"]};
								$id_val=$value->{$select_row[$index]["id"]};
								if($select_row[$index]["deleted"]) $deleted_val=$value->{$select_row[$index]["deleted"]}; else $deleted_val=null;
								if($select_row[$index]["enabled"]) $enabled_val=$value->{$select_row[$index]["enabled"]}; else $enabled_val=null;
								if($meta->translate_value[$index]) $field_val=Text::_($field_val);
								$select[$index][$id_val]=$field_val;
								if(!is_null($deleted_val) && $deleted_val) $select_classes[$index][$id_val]="deleted";
								elseif(!is_null($enabled_val) && !$enabled_val) $select_classes[$index][$id_val]="disabled";
								else $select_classes[$index][$id_val]="";
							}
						}
					} else {
						if (!$meta->check_value[$index] && $meta->input_type[$index]!="multiselect"){
							if($meta->val_type[$index]=="string" && !isset($select[$index][""])) {
								$select[$index][""]=$this->sel_0_pref.Text::_("Not selected").$this->sel_0_suff;
								$select_classes[$index][""]="no_selection";
							} elseif(!isset($select[$index][0])) {
								$select[$index][0]=$this->sel_0_pref.Text::_("Not selected").$this->sel_0_suff;
								$select_classes[$index][0]="no_selection";
							}
						}
						if(count($values["row"])){
							foreach($values["row"] as $value)	{
								$id_val=$value->{$select_row[$index]["id"]};
								$field_val=$value->{$select_row[$index]["field"]};
								if($select_row[$index]["deleted"]) $deleted_val=$value->{$select_row[$index]["deleted"]}; else $deleted_val=null;
								if($select_row[$index]["enabled"]) $enabled_val=$value->{$select_row[$index]["enabled"]}; else $enabled_val=null;
								if(!$meta->check_value[$index] && !$id_val) continue; 
								if($meta->translate_value[$index]) $field_val=Text::_($field_val);
								$select[$index][$id_val]=html_entity_decode($field_val,ENT_COMPAT,DEF_CP);
								if(!is_null($deleted_val) && $deleted_val) $select_classes[$index][$id_val]="deleted";
								elseif(!is_null($enabled_val) && !$enabled_val) $select_classes[$index][$id_val]="disabled";
								else $select_classes[$index][$id_val]="";
							}
						}
					}
				}	else {
					$select_classes[$index]=array();
					if (!$meta->check_value[$index] && !isset($select_row[$index]["row"][""]) && !isset($select_row[$index]["row"][0]) && $meta->input_type[$index]!="multiselect") {
						$select_row[$index]["row"][""]=$this->sel_0_pref.Text::_("Not selected").$this->sel_0_suff;
						$select_classes[$index][""]="no_selection";
						asort($select_row[$index]["row"],SORT_STRING);
					}
					$select[$index]=$select_row[$index]["row"];
					if(is_array($select_row[$index]["row"])){
						if(count($select_row[$index]["row"])){
							foreach($select_row[$index]["row"] as $key=>$val){
								if(!isset($select_classes[$index][$key])) $select_classes[$index][$key]="";
							}
						}
					} else{
						$select_classes[$index]="";
					}
				} // элемент является выбором из реестра
			}
		}
		$select_row=null; //чистим мусор
		$this->milestone('Before form class creation', __FUNCTION__);
		$frm=new aForm('frmEdit','post','index.php',$ajaxModify,'frmEdit');
		$frm->addInput(array(	'NAME'=>'psid',				'TYPE'=>"hidden",	'VAL'=>$psid,			'ID'=>'psid'					));
		$frm->addInput(array(	'NAME'=>'layout',			'TYPE'=>"hidden",	'VAL'=>$layout,			'ID'=>'layout'				));
		$frm->addInput(array(	'NAME'=>'module',			'TYPE'=>"hidden",	'VAL'=>$module,			'ID'=>'module'				));
		$frm->addInput(array(	'NAME'=>'multy_code',		'TYPE'=>"hidden",	'VAL'=>$multy_code,		'ID'=>'multy_code'		));
		$frm->addInput(array(	'NAME'=>'sort',  			'TYPE'=>"hidden",	'VAL'=>$sort,			'ID'=>'sort'					));
		$frm->addInput(array(	'NAME'=>'page',				'TYPE'=>"hidden",	'VAL'=>$page,			'ID'=>'page'					));
		$frm->addInput(array(	'NAME'=>'task',				'TYPE'=>"hidden",	'VAL'=>$task,			'ID'=>'task'					));
		$frm->addInput(array(	'NAME'=>'view',				'TYPE'=>"hidden",	'VAL'=>$view,			'ID'=>'view'					));
		$frm->addInput(array(	'NAME'=>'orderby',			'TYPE'=>"hidden",	'VAL'=>$orderby,		'ID'=>'orderby'				));
		$frm->addInput(array(	'NAME'=>'parent_module',	'TYPE'=>"hidden",	'VAL'=>$module,			'ID'=>'parent_module'	));
		$frm->addInput(array(	'NAME'=>'controller',		'TYPE'=>"hidden",	'VAL'=>$controller,		'ID'=>'controller'	));
		$unique_js=array();
		foreach ($input_type as $index=>$value)	{
			$on_change_js=$meta->field_on_change[$index];
			$fld=$meta->field;
			if ($meta->field_descr[$index]) $field_description=Text::_($meta->field_descr[$index]); else $field_description="";
			switch ($value)	{
				case "hidden":
					//				case "sel_search": // строка с выбором из js-формы (выбор из найденного)
					if (isset($row->{$fld[$index]})) {
						$row_val=$row->{$fld[$index]};
					} elseif($meta->multy_field==$fld[$index]&&$meta->multy_field!=$meta->keystring) {
						$row_val = $reestr->get('multy_code');
					} else {
						if ($meta->default_value[$index]) {
							$param_default=$meta->default_value[$index];
							if(isset($meta->constants[$param_default])) {
								$row_val=$meta->constants[$param_default];
							} else {
								$row_val=$param_default;
							}
						} else $row_val="";
					}
					
					$arr_field=array('NAME'=>$meta->field[$index],	'TYPE'=>"hidden", 'VAL'=>$row_val, 'ID'=>$meta->field[$index] );
					break;
				case "textarea":
					if (isset($row->{$fld[$index]})) {
						$row_val=$row->{$fld[$index]};
					} else {
						if ($meta->default_value[$index]) {
							$param_default=$meta->default_value[$index];
							if(isset($meta->constants[$param_default])) {
								$row_val=$meta->constants[$param_default];
							}
							else {$row_val=$param_default;
							}
						} else $row_val="";
					}
					$txtarea_size=(int)$input_size[$index];
					if (!$txtarea_size) $txtarea_size=60;
					$arr_field=array(
							'ONCHANGE'=>$on_change_js,
							'TYPE'=>$value,
							'ID'=>$meta->field[$index],
							'NAME'=>$meta->field[$index],
							'CLASS'=>'form-control',
							'COLS'=>$txtarea_size,
							'ROWS'=>6,
							'LABEL'=>Text::_($meta->field_title[$index]),
							'DESCRIPTION'=>$field_description,
							'VAL'=>html_entity_decode($row_val,ENT_COMPAT,DEF_CP)
					  //'VAL'=>$row_val
					);
					if($meta->check_value[$index]) {
						$arr_field['REQUIRED']["FLAG"]=1;
						$arr_field['REQUIRED']["MESSAGE"]=Text::_("Undefined value").$meta->field_title[$index];
					}
					//					$frm->addInput($arr_field);
					break;
				case "texteditor":
					if (isset($row->{$fld[$index]})) {
						$row_val=$row->{$fld[$index]};
					} else {
						if ($meta->default_value[$index]) {
							$param_default=$meta->default_value[$index];
							if(isset($meta->constants[$param_default])) {
								$row_val=$meta->constants[$param_default];
							}
							else {$row_val=$param_default;
							}
						} else $row_val="";
					}
					$txtarea_size=(int)$input_size[$index];
					if (!$txtarea_size) $txtarea_size=60;
					$arr_field=array(
							'ONCHANGE'=>$on_change_js,
							'TYPE'=>$value,
							'ID'=>$meta->field[$index],
							'NAME'=>$meta->field[$index],
							'COLS'=>$txtarea_size,
							'ROWS'=>6,
							'LABEL'=>Text::_($meta->field_title[$index]),
							'DESCRIPTION'=>$field_description,
							'VAL'=>$row_val
					);
					if($meta->check_value[$index]) {
						$arr_field['REQUIRED']["FLAG"]=1;
						$arr_field['REQUIRED']["MESSAGE"]=Text::_("Undefined value").$meta->field_title[$index];
					}
					//					$frm->addInput($arr_field);
					break;
				case "formated":
					if (isset($row->{$fld[$index]})) {
						$row_val=$row->{$fld[$index]};
					} else {
						if ($meta->default_value[$index]) {
							$param_default=$meta->default_value[$index];
							if(isset($meta->constants[$param_default])) {
								$row_val=$meta->constants[$param_default];
							}
							else {$row_val=$param_default;
							}
						} else $row_val="";
					}
					$txtarea_size=(int)$input_size[$index];
					if (!$txtarea_size) $txtarea_size=60;
					$arr_field=array(
							'ONCHANGE'=>$on_change_js,
							'TYPE'=>"textarea",
							'ID'=>$meta->field[$index],
							'NAME'=>$meta->field[$index],
							'COLS'=>$txtarea_size,
							'ROWS'=>8,
							'CLASS'=>"label_in_sprav",
							'READONLY'=>true,
							'LABEL'=>Text::_($meta->field_title[$index]),
							'DESCRIPTION'=>$field_description,
							'VAL'=>html_entity_decode($row_val,ENT_COMPAT,DEF_CP)
					);
					if($meta->check_value[$index]) {
						$arr_field['REQUIRED']["FLAG"]=1;
						$arr_field['REQUIRED']["MESSAGE"]=Text::_("Undefined value").$meta->field_title[$index];
					}
					//					$frm->addInput($arr_field);
					break;
				case "select":
				case "multiselect":
					if (isset($row->{$fld[$index]})) {
						$row_val=$row->{$fld[$index]};
					} else {
						if ($meta->default_value[$index]) {
							$param_default=$meta->default_value[$index];
							if(isset($meta->constants[$param_default])) {
								$row_val=$meta->constants[$param_default];
							} else { 
								$row_val=$param_default;
							}
						} else $row_val=0;
					}
					if (!$row_val && $meta->multy_field==$fld[$index]) $row_val = $reestr->get('multy_code');

					if ($value=="multiselect") {
						//$multi_arr= json_decode($row_val, true);
						$multi_arr= explode(";", $row_val);
						$new_multi_arr=array();
						foreach($multi_arr as $multi_key=>$multi_val){
							if (array_key_exists($multi_val, $select[$index])) $new_multi_arr[$multi_key]=$multi_val;
						}
						$row_val=$new_multi_arr;
					} else {
						if (!array_key_exists($row_val, $select[$index])) $row_val=0;
					}				
					if (($psid) && ($meta->field_no_update[$index]) && (($row_val && $meta->check_value[$index]) || !$meta->check_value[$index]) ) {
						if(is_array($row_val)){
							foreach($row_val as $multi_key=>$multi_val){
								$arr_field=array(
										'TYPE'=>"hidden",
										'ID'=>'p_'.$meta->field[$index].'_select_'.$multi_key,
										'NAME'=>'p_'.$meta->field[$index].'_select[]',
										'SIZE'=>$input_size[$index],
										'LABEL'=>Text::_($meta->field_title[$index]),
										'DESCRIPTION'=>$field_description,
										'VAL'=>$multi_val
								);
								$frm->addInput($arr_field);
							}
							$arr_field=array(
									'ONCHANGE'=>$on_change_js,
									'TYPE'=>"text",
									'ID'=>$meta->field[$index],
									'NAME'=>$meta->field[$index],
									'SIZE'=>$input_size[$index],
									'LABEL'=>Text::_($meta->field_title[$index]),
									'DESCRIPTION'=>$field_description,
									'CLASS'=>"label_in_sprav",
									'READONLY'=>true,
									'VAL'=>implode(", ", $row_val)
							);
						} else {
							$arr_field=array(
									'TYPE'=>"hidden",
									'ID'=>'p_'.$meta->field[$index].'_select',
									'NAME'=>'p_'.$meta->field[$index].'_select',
									'SIZE'=>$input_size[$index],
									'LABEL'=>Text::_($meta->field_title[$index]),
									'DESCRIPTION'=>$field_description,
									'VAL'=>$row_val
							);
							$frm->addInput($arr_field);
							$arr_field=array(
									'ONCHANGE'=>$on_change_js,
									'TYPE'=>"text",
									'ID'=>$meta->field[$index],
									'NAME'=>$meta->field[$index],
									'SIZE'=>$input_size[$index],
									'LABEL'=>Text::_($meta->field_title[$index]),
									'DESCRIPTION'=>$field_description,
									'CLASS'=>"label_in_sprav",
									'READONLY'=>true,
									'VAL'=>(isset($select[$index][$row_val]) ? $select[$index][$row_val] : "")
							);
						}
					} else {
						$arr_field=array(
								'ONCHANGE'=>$on_change_js,
								'TYPE'=>"select",
								'ID'=>$meta->field[$index],
								'CLASS'=>'form-control',
								'NAME'=>$meta->field[$index],
								'SIZE'=>1,
								'LABEL'=>Text::_($meta->field_title[$index]),
								'DESCRIPTION'=>$field_description,
								'MULTIPLE'=>($value=="multiselect" ? 5 : 0), // 5 это размер
								'VAL'=>$row_val,
								'OPTIONS'=>$select[$index],
								'OPTIONS_CLASSES'=>$select_classes[$index]
						);
					}
					
					if($meta->check_value[$index]) {
						$arr_field['REQUIRED']["FLAG"]=1;
						$arr_field['REQUIRED']["MESSAGE"]=Text::_("Undefined value").$meta->field_title[$index];
					}
					//					$frm->addInput($arr_field);
					break;
				case "checkbox":
					if (isset($row->{$fld[$index]})) {
						$row_val=$row->{$fld[$index]};
					} else {
						if ($meta->default_value[$index]) {
							$param_default=$meta->default_value[$index];
							if(isset($meta->constants[$param_default])) {
								$row_val=$meta->constants[$param_default];
							}
							else {$row_val=$param_default;
							}
						} else $row_val=0;
					}
					$arr_field=array(
							'ONCHANGE'=>$on_change_js,
							'ID'=>$meta->field[$index],
							'NAME'=>$meta->field[$index],
							'TYPE'=>"checkbox",
							'LABEL'=>Text::_($meta->field_title[$index]),
							'DESCRIPTION'=>$field_description,
							'CHECKED'=>(int)$row_val,
							'VAL'=>"1"
					);
					//					$frm->addInput($arr_field);
					break;
				case "folder_select":
					if (isset($row->{$fld[$index]})) {
						$row_val=$row->{$fld[$index]};
					} else {
						if ($meta->default_value[$index]) {
							$param_default=$meta->default_value[$index];
							if(isset($meta->constants[$param_default])) {
								$row_val=$meta->constants[$param_default];
							}else{
								$row_val=$param_default;
							}
						} else $row_val="";
					}
					$dir_path=PATH_FRONT.str_replace("/", DS, $meta->upload_path[$index]).DS;
					$dir_list=Files::getFolders($dir_path,array(".svn",".",".."));
					$arr_keys=array(""=>$this->sel_0_pref.Text::_("Not selected").$this->sel_0_suff);
					if ($dir_list){
						if (!array_key_exists($row_val, $dir_list)) $row_val="";
						foreach($dir_list as $key=>$val) $arr_keys[$key]=$key;
					} else $row_val="";
					$arr_field=array(
							'ONCHANGE'=>$on_change_js,
							'TYPE'=>"select",
							'CLASS'=>'form-control',
							'ID'=>$meta->field[$index],
							'NAME'=>$meta->field[$index],
							'SIZE'=>1,
							'LABEL'=>Text::_($meta->field_title[$index]),
							'DESCRIPTION'=>$field_description,
							'VAL'=>$row_val,
							'OPTIONS'=>$arr_keys
					);
					if($meta->check_value[$index]) {
						$arr_field['REQUIRED']["FLAG"]=1;
						$arr_field['REQUIRED']["MESSAGE"]=Text::_("Undefined value").$meta->field_title[$index];
					}
					//					$frm->addInput($arr_field);
					break;
				case "file_select":
					if (isset($row->{$fld[$index]})) {
						$row_val=$row->{$fld[$index]};
					} else {
						if ($meta->default_value[$index]) {
							$param_default=$meta->default_value[$index];
							if(isset($meta->constants[$param_default])) {
								$row_val=$meta->constants[$param_default];
							}
							else {$row_val=$param_default;
							}
						} else $row_val="";
					}
					$dir_path=BARMAZ_UF_PATH.$meta->module.DS.str_replace("/", DS, $meta->upload_path[$index]).DS;
					$dir_list=Files::getFiles($dir_path);
					$arr_keys=array(""=>$this->sel_0_pref.Text::_("Not selected").$this->sel_0_suff);
					if ($dir_list){
						if (!array_key_exists($row_val, $dir_list)) $row_val="";
						foreach($dir_list as $key=>$val) $arr_keys[$key]=$key;
					} else $row_val="";
					$arr_field=array(
							'ONCHANGE'=>$on_change_js,
							'TYPE'=>"select",
							'CLASS'=>'form-control',
							'ID'=>$meta->field[$index],
							'NAME'=>$meta->field[$index],
							'SIZE'=>1,
							'LABEL'=>Text::_($meta->field_title[$index]),
							'DESCRIPTION'=>$field_description,
							'VAL'=>$row_val,
							'OPTIONS'=>$arr_keys
					);
					if($meta->check_value[$index]) {
						$arr_field['REQUIRED']["FLAG"]=1;
						$arr_field['REQUIRED']["MESSAGE"]=Text::_("Undefined value").$meta->field_title[$index];
					}
					//					$frm->addInput($arr_field);
					break;
				case "file":
					if (isset($row->{$fld[$index]})) {
						$row_val=$row->{$fld[$index]};
					} else {
						if ($meta->default_value[$index]) {
							$param_default=$meta->default_value[$index];
							if(isset($meta->constants[$param_default])) {
								$row_val=$meta->constants[$param_default];
							}
							else {$row_val=$param_default;
							}
						} else $row_val="";
					}
					$arr_field=array(
							'ONCHANGE'=>$on_change_js,
							'ID'=>$meta->field[$index],
							'NAME'=>$meta->field[$index],
							'TYPE'=>"fileselector",
							'SIZE'=>$input_size[$index],
							'LABEL'=>Text::_($meta->field_title[$index]),
							'DESCRIPTION'=>$field_description,
							'VAL'=>$row_val
					);
					break;
				case "image":
					if (isset($row->{$fld[$index]})) {
						$row_val=$row->{$fld[$index]};
					} else {
						if ($meta->default_value[$index]) {
							$param_default=$meta->default_value[$index];
							if(isset($meta->constants[$param_default])) {
								$row_val=$meta->constants[$param_default];
							}
							else {$row_val=$param_default;
							}
						} else $row_val="";
					}
					if ($row_val) {
						if(isset($meta->img_module[$index])&&$meta->img_module[$index]!="")
						{
							$tmpl_img=BARMAZ_UF."/".$meta->img_module[$index]."/".$meta->upload_path[$index]."/".Files::splitAppendix($row_val);
							$tmpl_img_path=BARMAZ_UF_PATH.$meta->img_module[$index].DS.str_replace("/",DS,$meta->upload_path[$index]).DS.Files::splitAppendix($row_val, true);

						}
						else
						{
							$tmpl_img=BARMAZ_UF."/".$meta->module."/".$meta->upload_path[$index]."/".Files::splitAppendix($row_val);
							$tmpl_img_path=BARMAZ_UF_PATH.$meta->module.DS.str_replace("/",DS,$meta->upload_path[$index]).DS.Files::splitAppendix($row_val, true);
						}
					} else {
						$tmpl_img=""; $tmpl_img_path="";
					}

					$arr_field=array(
							'ONCHANGE'=>$on_change_js,
							'ID'=>$meta->field[$index],
							'NAME'=>$meta->field[$index],
							'TYPE'=>"imageselector",
							'SIZE'=>$input_size[$index],
							'LABEL'=>Text::_($meta->field_title[$index]),
							'DESCRIPTION'=>$field_description,
							'VAL'=>$row_val,
							'URL'=>$tmpl_img,
							'PATH'=>$tmpl_img_path
					);
					//					$frm->addInput($arr_field);
					break;
				case "date_ajax":
					if (isset($row->{$fld[$index]})) {
						$row_val=Date::fromSQL($row->{$fld[$index]}, ".", true);
					} else {
						if ($meta->default_value[$index]) {
							$param_default=$meta->default_value[$index];
							if(isset($meta->constants[$param_default])) {
								$row_val=$meta->constants[$param_default];
							}
							else {$row_val=$param_default;
							}
						} else $row_val="";
					}
					$arr_field=array(
							'ONCHANGE'=>$on_change_js,
							'ID'=>$meta->field[$index],
							'NAME'=>$meta->field[$index],
							'TYPE'=>"dateselector",
							'SIZE'=>10,
							'CLASS'=>'form-control dateselector',
							'MAXLENGTH'=>10,
							'READONLY'=>true,
							'LABEL'=>Text::_($meta->field_title[$index]),
							'DESCRIPTION'=>$field_description,
							'VAL'=>$row_val
					);
					if($meta->check_value[$index]) {
						$arr_field['REQUIRED']["FLAG"]=1;
						$arr_field['REQUIRED']["MESSAGE"]=Text::_("Undefined value")." ".$meta->field_title[$index];
					}
					//					$frm->addInput($arr_field);
					break;
					//				case "datetime":
				case "datetime_ajax":
					if (isset($row->{$fld[$index]})) {
						$row_val=Date::fromSQL($row->{$fld[$index]}, false, true);
					} else {
						if ($meta->default_value[$index]) {
							$param_default=$meta->default_value[$index];
							if(isset($meta->constants[$param_default])) {
								$row_val=$meta->constants[$param_default];
							}
							else {$row_val=$param_default;
							}
						} else $row_val="";
					}
					$arr_field=array(
							'ONCHANGE'=>$on_change_js,
							'ID'=>$meta->field[$index],
							'NAME'=>$meta->field[$index],
							'TYPE'=>"datetimeselector",
							'CLASS'=>'form-control datetimeselector',
							'SIZE'=>16,
							'MAXLENGTH'=>16,
							'READONLY'=>true,
							'LABEL'=>Text::_($meta->field_title[$index]),
							'DESCRIPTION'=>$field_description,
							'VAL'=>$row_val
					);
					if($meta->check_value[$index]) {
						$arr_field['REQUIRED']["FLAG"]=1;
						$arr_field['REQUIRED']["MESSAGE"]=Text::_("Undefined value")." ".$meta->field_title[$index];
					}
					//					$frm->addInput($arr_field);
					break;
				case "address":
					if (isset($row->{$fld[$index]})) {
						$row_val=$row->{$fld[$index]};
					} else {
						$row_val="";
					}
					$arr_field=array(
							'ONCHANGE'=>$on_change_js,
							'ID'=>$meta->field[$index],
							'NAME'=>$meta->field[$index],
							'TYPE'=>"addressselector",
							'CLASS'=>"addressselector",
							'MODULE'=>$module,
							'SIZE'=>100,
							'MAXLENGTH'=>255,
							'READONLY'=>true,
							'LABEL'=>Text::_($meta->field_title[$index]),
							'DESCRIPTION'=>$field_description,
							'VAL'=>$row_val
					);
					//					$frm->addInput($arr_field);
					break;
				case "label":
					if($meta->field_is_method[$index]){
						if(method_exists($this, $meta->field_is_method[$index])){
							$row_val=$this->{$meta->field_is_method[$index]}($psid, $row,$fld[$index]);
						} else {
							$row_val="";
						}
						$arr_field=array(
								'ONCHANGE'=>$on_change_js,
								'TYPE'=>"html",
								'ID'=>$meta->field[$index],
								'NAME'=>$meta->field[$index],
								'SIZE'=>$input_size[$index],
								'LABEL'=>Text::_($meta->field_title[$index]),
								'DESCRIPTION'=>$field_description,
								'CLASS'=>"label_in_sprav",
								'READONLY'=>true,
								'VAL'=>$row_val
						);
					} else {
						if (isset($row->{$fld[$index]})) {
							$row_val=$row->{$fld[$index]};
							if ($meta->val_type[$index]=="date") $row_val=Date::fromSQL($row_val,true);
							elseif ($meta->val_type[$index]=="datetime") $row_val=Date::fromSQL($row_val,false);
							elseif ($meta->val_type[$index]=="boolean") {
								if ($row_val) $row_val=Text::_('Y'); else $row_val=Text::_('N');
							}
						} else {
							if ($meta->default_value[$index]) {
								$param_default=$meta->default_value[$index];
								if(isset($meta->constants[$param_default])) {
									$row_val=$meta->constants[$param_default];
								}
								else {$row_val=$param_default;
								}
							} else $row_val="";
						}
						$arr_field=array(
								'ONCHANGE'=>$on_change_js,
								'TYPE'=>"text",
								'ID'=>$meta->field[$index],
								'NAME'=>$meta->field[$index],
								'SIZE'=>$input_size[$index],
								'LABEL'=>Text::_($meta->field_title[$index]),
								'DESCRIPTION'=>$field_description,
								'CLASS'=>"label_in_sprav",
								'READONLY'=>true,
								'VAL'=>$row_val
						);
					}
					//					$frm->addInput($arr_field);
					break;
				case "label_sel":
					if (isset($row->{$fld[$index]})) {
						$row_val=$row->{$fld[$index]};
					} else {
						if ($meta->default_value[$index]) {
							$param_default=$meta->default_value[$index];
							if(isset($meta->constants[$param_default])) {
								$row_val=$meta->constants[$param_default];
							}
							else {$row_val=$param_default;
							}
						} else $row_val=0;
					}
					if ($meta->multy_field==$fld[$index]) $row_val = $reestr->get('multy_code');
					if (!array_key_exists($row_val, $select[$index])) $row_val=0;
					if (!$input_size[$index]) $input_size[$index]=strlen($select[$index][$row_val]);
					// Скрытое поле со значением
					$arr_field=array(
							'TYPE'=>"hidden",
							'ID'=>'p_'.$meta->field[$index].'_select',
							'NAME'=>'p_'.$meta->field[$index].'_select',
							'SIZE'=>$input_size[$index],
							'LABEL'=>Text::_($meta->field_title[$index]),
							'DESCRIPTION'=>$field_description,
							'VAL'=>$row_val
					);
					$frm->addInput($arr_field);
					$arr_field=array(
							'ONCHANGE'=>$on_change_js,
							'TYPE'=>"text",
							'ID'=>$meta->field[$index],
							'NAME'=>$meta->field[$index],
							'SIZE'=>$input_size[$index],
							'LABEL'=>Text::_($meta->field_title[$index]),
							'DESCRIPTION'=>$field_description,
							'CLASS'=>"label_in_sprav",
							'READONLY'=>true,
							'VAL'=>$select[$index][$row_val]
					);
					//					$frm->addInput($arr_field);
					break;
				case "filepath":
					if (isset($row->{$fld[$index]})) {
						$row_val=$row->{$fld[$index]};
					} else {
						if ($meta->default_value[$index]) {
							$param_default=$meta->default_value[$index];
							if(isset($meta->constants[$param_default])) {
								$row_val=$meta->constants[$param_default];
							}
							else {$row_val=$param_default;
							}
						} else $row_val="";
					}
					if (($psid)&&($meta->field_no_update[$index])) {
						$arr_field=array(
								'TYPE'=>"filepath",
								'ID'=>$meta->field[$index],
								'NAME'=>$meta->field[$index],
								'SIZE'=>$input_size[$index],
								'CLASS'=>"label_in_sprav",
								'READONLY'=>true,
								'MAXLENGTH'=>$meta->val_size[$index],
								'LABEL'=>Text::_($meta->field_title[$index]),
								'DESCRIPTION'=>$field_description,
								'VAL'=>htmlspecialchars_decode($row_val)
						);
					} else {
						$arr_field=array(
								'ONCHANGE'=>$on_change_js,
								'TYPE'=>"filepath",
								'ID'=>$meta->field[$index],
								'NAME'=>$meta->field[$index],
								'MAXLENGTH'=>$meta->val_size[$index],
								'SIZE'=>$input_size[$index],
								'CLASS'=>'form-control filepath',
								'LABEL'=>Text::_($meta->field_title[$index]),
								'DESCRIPTION'=>$field_description,
								'VAL'=>htmlspecialchars_decode($row_val)
						);
					}
					if($meta->check_value[$index]) {
						$arr_field['REQUIRED']["FLAG"]=1;
						$arr_field['REQUIRED']["MESSAGE"]=Text::_("Please fill")." ".$meta->field_title[$index];
					}
					// Restrict filepath show on frontend
					// if(!defined("_ADMIN_MODE")) $arr_field = array('ID'=>$meta->field[$index], 'NAME'=>$meta->field[$index], "TYPE"=>"hidden"); // first variant
					if(!defined("_ADMIN_MODE")) $arr_field = array('ID'=>$meta->field[$index], 'NAME'=>$meta->field[$index]); // second variant
					break;
				default:
				case "text":
				case "password":
				case "timestamp":
					if (isset($row->{$fld[$index]})) {
						$row_val=$row->{$fld[$index]};
					} else {
						if ($meta->default_value[$index]) {
							$param_default=$meta->default_value[$index];
							if(isset($meta->constants[$param_default])) {
								$row_val=$meta->constants[$param_default];
							}
							else {$row_val=$param_default;
							}
						} else $row_val="";
					}
					if ($value=="password") {
						$row_val="";
					}
					if (($psid)&&($meta->field_no_update[$index])) {
						$arr_field=array(
								'TYPE'=>"text",
								'ID'=>$meta->field[$index],
								'NAME'=>$meta->field[$index],
								'SIZE'=>$input_size[$index],
								'CLASS'=>"label_in_sprav",
								'READONLY'=>true,
								'MAXLENGTH'=>$meta->val_size[$index],
								'LABEL'=>Text::_($meta->field_title[$index]),
								'DESCRIPTION'=>$field_description,
								'VAL'=>htmlspecialchars_decode($row_val, ENT_QUOTES)
						);
					} else {
						if($meta->val_type[$index]=="int") $field_class="form-control numeric"; 
						elseif($meta->val_type[$index]=="float"||$meta->val_type[$index]=="currency") $field_class="form-control decimal";
						else $field_class="form-control";
						$arr_field=array(
								'ONCHANGE'=>$on_change_js,
								'TYPE'=>"text",
								'ID'=>$meta->field[$index],
								'NAME'=>$meta->field[$index],
								'MAXLENGTH'=>$meta->val_size[$index],
								'SIZE'=>$input_size[$index],
								'CLASS'=>$field_class,
								'LABEL'=>Text::_($meta->field_title[$index]),
								'DESCRIPTION'=>$field_description,
								'VAL'=>htmlspecialchars_decode($row_val, ENT_QUOTES)
						);
					}
					if($meta->check_value[$index]) {
						if($meta->check_value[$index]==1 || $meta->check_value[$index]==2) {
							$arr_field['REQUIRED']["FLAG"]=1;
							$arr_field['REQUIRED']["MESSAGE"]=Text::_("Please fill")." ".$meta->field_title[$index];
						}
						if($meta->check_value[$index]==2 || $meta->check_value[$index]==3) {
							$unique_js[$meta->field[$index]]=$meta->check_value[$index];
							$arr_field['UNIQUE']["FLAG"]=1;
						}
					}
					break;
			}
			if ($meta->input_view[$index]==0){
				$arr_field['TYPE']="hidden";
				if($value=='checkbox') $arr_field['VAL']=$row_val;
				elseif($value=='multiselect') $arr_field['VAL']=";".implode(";", $row_val).";";
			}
			$frm->addInput($arr_field);
		}
		if(count($unique_js)){
			$unique_js_str = "$(document).ready(function() {";
			foreach($unique_js as $k_ujs=>$v_ujs){
				if($v_ujs==2) $unique_js_str.= "$('#".$k_ujs."').bind('blur', function() {checkSpravFieldUnique('".$module."', '".$view."', '".$layout."', this,'".$psid."');});";
				elseif($v_ujs==3) $unique_js_str.= "$('#".$k_ujs."').bind('blur', function() {checkSpravFieldUnique('".$module."', '".$view."', '".$layout."', this,'".$psid."');});";
			}
			$unique_js_str.= "});";
			Portal::getInstance()->addScriptDeclaration($unique_js_str);
		}
		$frm->addInput(array( "TYPE"=>"submit","CLASS"=>"commonButton btn btn-info", "VAL"=>Text::_("Save"),		"NAME"=>"save", "ID"=>"save"));
		if(!$ajaxModify && defined("_ADMIN_MODE")) $frm->addInput(array( "TYPE"=>"submit","CLASS"=>"commonButton btn btn-info", "VAL"=>Text::_("Apply"),	"NAME"=>"apply", "ID"=>"apply"));
		if(!$ajaxModify && defined("_ADMIN_MODE") && $meta->buttons['clone']['view'])	{	
			$frm->addInput(array( "TYPE"=>"submit","CLASS"=>"commonButton btn btn-info", "VAL"=>Text::_("Save and add clone"),	"NAME"=>"add_clone", "ID"=>"add_clone"));
		}
		if (!$ajaxModify && defined("_ADMIN_MODE") && $meta->buttons['new']['show'])	{
			$frm->addInput(array( "TYPE"=>"submit","CLASS"=>"commonButton btn btn-info", "VAL"=>Text::_("Save and add new"),	"NAME"=>"add_new", "ID"=>"add_new"));
		}
		if ($ajaxModify) {
			$frm->addInput(array( "TYPE"=>"button","CLASS"=>"commonButton btn btn-info", "VAL"=>Text::_("Close"),	"NAME"=>"cancel",	"ONCLICK"=>"hidePopup()"));
		} else {
			// здесь & не заменялся
			if ($onCancelURL)	$frm->addInput(array( "TYPE"=>"button","CLASS"=>"commonButton btn btn-info", "VAL"=>Text::_("Close"),	"NAME"=>"cancel",	"ONCLICK"=>"document.location.href='".Router::_($onCancelURL,false)."'"));
			else $frm->addInput(array( "TYPE"=>"button","CLASS"=>"commonButton btn btn-info", "VAL"=>Text::_("Close"),	"NAME"=>"cancel",	"ONCLICK"=>"history.back()"));
		}

		if (!$psid)	{
			$head=Text::_("Adding data");
		} else {
			$head=Text::_("Changing data");
		}
		$module_path=PATH_MODULES;

		if(!$psid){
			if (isset($meta->templates['new'])) $template = $meta->templates['new']; else $template="modify";
		}	else	{
			if (isset($meta->templates['modify'])) $template = $meta->templates['modify']; else $template="modify";
		}
		$info="updateLabel(this,'".$module."','".$parent_view."','BuildPathTree',%s,1)";

		// Let's do something with table_body_arr
		Event::raise("sprav_view.modify_form.prepared", array("module"=>$module, "class_name"=>__CLASS__, "func_name"=>__FUNCTION__, "meta"=>$meta, "row"=>$row), $frm);
		
		$moduleName=$this->get('module');
		$path_name=Module::getReplaceModule($moduleName);
		$templatePath = Portal::getInstance()->getTemplatePath().'html'.DS.'modules'.DS.$path_name.DS.$this->getName().DS.$template.'.php';
		$filename =  $module_path . $path_name . DS . "views" . DS . "template" . DS . $view. DS. $template. ".php";
		$this->message(Text::_("Looking for template").": ".$templatePath, __FUNCTION__);
		if (Portal::getInstance()->noTemplate()) $class_postfix="_nt"; else $class_postfix="";
		echo "<div class=\"moduleBody ".$this->get("module")."Module\"><div class=\"content".$class_postfix."\">";
		if ($meta->title) $title=Text::_($meta->title); else $title="";
		if (is_file($templatePath)) {
			require_once $templatePath;
		} elseif (is_file($filename)) {
			$this->message('Spravochnick template:'.$filename, __FUNCTION__);
			include_once $filename;
		} else {
			$this->milestone('Before form StartLayout', __FUNCTION__);
			if(defined("_BARMAZ_TRANSLATE")) $translator = new Translator();

			$html=$frm->startLayout(false);
			$html.="<div class=\"container\"><div class=\"row\"><div class=\"col-md-12\">";
			$html.="<div id=\"modify-wrapper\" class=\"rounded-pan".($meta->classTable ? " ".$meta->classTable : "")."\">";
			$html.="<h4 class=\"title\">".$dop_head." ".$head." : ".$title."</h4>";
			$pans_titles=array(1=>"Main data", 2=>"Additional");
			$last_tab=$meta->input_last_page;
			$_activeTab=$this->activeTab;
			$limitIndLastTab=1; // указатель на количество панелей, больше которого надо делать вкладки
			if(defined("_BARMAZ_TRANSLATE")){
				$countFldForTranslate=$translator->getTranslateList($meta);
				
				if($countFldForTranslate){
					$limitIndLastTab=0; // у нас будут еще вкладки поскольку есть языки - панели нужны начиная с превой
					if(!$last_tab) $last_tab=1; 
				}
				/* у нас уже как минимум 1 вкладка основная
				* и надо индексы таблиц полей с 0 перенести на ее 
				* - то есть выполнить при включении скрипт простановки в полях метадаты
				* отнесение к вкладке 1 вместо 0 во всех значениях где оно 0
				* 
				*/ 
			}
			if($last_tab && $last_tab>$limitIndLastTab){ // начинаем панели если у нас больше вкладок чем лимит  
				$html.="<div id=\"tab_switcher\">";
				$html.="<ul class=\"nav nav-tabs\" id=\"tabs\">";
				for($_key=1; $_key<=$last_tab; $_key++){
					if ($_key==$_activeTab) $_class=' active'; else $_class="";
					$html.="<li class=\"nav-item switcher".$_class."\">";
					$html.="<a class=\"nav-link\" href=\"#tab_".$_key."\" data-key=\"".$_key."\" data-toggle=\"tab\">".(array_key_exists($_key, $pans_titles) ? Text::_($pans_titles[$_key]) : Text::_("Pan")." ".$_key)."</a>";
					$html.="</li>";
				}
				// если включена система переводов - добавляем панели языков к уже имеющимся вкладкам
				if(defined("_BARMAZ_TRANSLATE")){
				  // добавим проверку надо ли их вообще - может нет ни одного поля для перевода
				  if($countFldForTranslate)  $html.=$translator->prepareTranslatorPanelHead($_activeTab,$last_tab,$dop_tab);				  
				}
				$html.="</ul>";
				$html.="</div>"; // tab_switcher
			}
			if(!$last_tab) $last_tab=1;
			$last_tab+=$dop_tab; // добавим к основным число вкладок языков			
			if($last_tab>1) $html.="<div class=\"tab-content clearfix\">";
			for($key=1; $key<=$last_tab; $key++){
				if ($key==$_activeTab) $_class=' active'; else $_class="";
				if(defined("_BARMAZ_TRANSLATE")){
					if (in_array($key, $translator->getArrLang())) continue; // пропустим - ниже сверстается в ветке языков
				}
				$html.="<div class=\"tab-pane".($last_tab==1 ? "-single" : "").$_class."\" id=\"tab_".$key."\">";
				if ($linkModify && $key==1) {
					$html.="<div class=\"modify-wrapper row\"><fieldset>";
					$html.="<legend>".Text::_("Subordination").":</legend>";
					$html.=HTMLControls::renderHiddenField("linkEditor_nf",$meta->parent_name);
					$html.=HTMLControls::renderPopupMultySelect("linkEditor",$linkArray,"","","index.php?module=".$module."&amp;view=".$parent_view."&amp;layout=selector&amp;task=getContList&amp;lol=linkEditor&amp;option=ajax",'',$info,$isNewLink);
					$html.="</fieldset></div>";
				}
				foreach ($input_type as $index=>$value)	{
					if($key==$last_tab){
						if($meta->input_page[$index]>0 && $meta->input_page[$index]!=$key) continue;
					} else {
						if($meta->input_page[$index]!=$key) continue;
					}
					if ($meta->input_view[$index]==0 && $value!="hidden") continue;
					if (!$psid && $meta->is_add_custom[$index]) continue;
					switch ($value)	{
						case "hidden": continue 2;
						break;
						case "image":
							$html.="<div class=\"modify-image-wrapper\" id=\"wrapper-".$meta->field[$index]."\"><div class=\"row\">";
							$html.="<div class=\"modify-label col-sm-4\">".$frm->renderLabelFor($meta->field[$index])."&nbsp;".$frm->renderBalloonFor($meta->field[$index],false)."</div>";
							$html.="<div class=\"col-sm-8\">".$frm->renderInputPart($meta->field[$index])."</div>";
							$html.="</div></div>";
							break;
						case "textarea":
						case "formated":
						case "texteditor":
							$html.="<div class=\"modify-editor-wrapper\" id=\"wrapper-".$meta->field[$index]."\">";
							$html.="<div class=\"row row-margin\"><div class=\"modify-label col-sm-12\">".$frm->renderLabelFor($meta->field[$index])."&nbsp;".$frm->renderBalloonFor($meta->field[$index],false)."</div></div>";
							$html.="<div class=\"row\"><div class=\"modify-input col-sm-12\">".$frm->renderInputPart($meta->field[$index])."</div></div>";
							$html.="</div>";
							break;
						case "checkbox":
							$html.="<div class=\"modify-wrapper row\" id=\"wrapper-".$meta->field[$index]."\">";
							$html.="<div class=\"modify-label col-sm-4 col-xs-9\">".$frm->renderLabelFor($meta->field[$index])."&nbsp;".$frm->renderBalloonFor($meta->field[$index],false)."</div>";
							$html.="<div class=\"modify-input col-sm-8 col-xs-3\">".$frm->renderInputPart($meta->field[$index])."</div>";
							$html.="</div>";
							break;
						default:
							$html.="<div class=\"modify-wrapper row\" id=\"wrapper-".$meta->field[$index]."\">";
							$html.="<div class=\"modify-label col-sm-4\">".$frm->renderLabelFor($meta->field[$index])."&nbsp;".$frm->renderBalloonFor($meta->field[$index],false)."</div>";
							$html.="<div class=\"modify-input col-sm-8\">".$frm->renderInputPart($meta->field[$index])."</div>";
							$html.="</div>";
							break;
					}
				}
				$html.="</div>"; // tab_body
			}
			// если включена система переводов - добавляем данные на панель языков
			if(defined("_BARMAZ_TRANSLATE")){
				if($countFldForTranslate){
				$this->milestone('before include translation data', __FUNCTION__);
				$translateData=$translator->prepareTranslatorPanel($_activeTab, $meta,$frm);				
				$html.=$translateData;
				$this->milestone('after include translation data', __FUNCTION__);
				}
			}
			// теперь сведения по языкам
			if($last_tab>1) $html.="</div>";
			$html.="<div class=\"modify-buttons buttons\">";
			$html.=HTMLControls::renderHiddenField("activeTab", $_activeTab);
			$html.=$frm->renderInputPart("save");
			if (!$ajaxModify && defined("_ADMIN_MODE")) $html.=$frm->renderInputPart("apply");
			if (!$ajaxModify && $meta->buttons['clone']['show'] && defined("_ADMIN_MODE"))  $html.=$frm->renderInputPart("add_clone");
			if (!$ajaxModify && defined("_ADMIN_MODE") && $meta->buttons['new']['show']) $html.=$frm->renderInputPart("add_new");
			$html.=$frm->renderInputPart("cancel");
			$html.=$frm->endLayout();
			$html.="</div>"; // modify-buttons
			$html.="</div>"; // modify-wrapper
			$html.="</div></div></div>";
			echo $html;
			$this->milestone('After form DisplayOutput', __FUNCTION__);
		}
		echo "</div></div>";
	}
	public static function getCKArray($ck_label="") {
		return SpravStatic::getCKArray($ck_label);
	}
	
	public function getLetter($int)
	{
		$arr_letter[0]="A";
		$arr_letter[1]="B";
		$arr_letter[2]="C";
		$arr_letter[3]="D";
		$arr_letter[4]="E";
		$arr_letter[5]="F";
		$arr_letter[6]="G";
		$arr_letter[7]="H";
		$arr_letter[8]="I";
		$arr_letter[9]="J";
		$arr_letter[10]="K";
		$arr_letter[11]="L";
		$arr_letter[12]="M";
		$arr_letter[13]="N";
		$arr_letter[14]="O";
		$arr_letter[15]="P";
		$arr_letter[16]="Q";
		$arr_letter[17]="R";
		$arr_letter[18]="S";
		$arr_letter[19]="T";
		$arr_letter[20]="U";
		$arr_letter[21]="V";
		$arr_letter[22]="W";
		$arr_letter[23]="X";
		$arr_letter[24]="Y";
		$arr_letter[25]="Z";
		if($int>=0&&$int<26)	return $arr_letter[$int];
		else return "";
		
	}
	
	public function modifyLinks(){
		$mdl = Module::getInstance();
		$reestr = $mdl->get('reestr');
		$model=$reestr->get('model');
		$meta=$model->meta;
		$module=$meta->module;
		$layout=$reestr->get('layout');
		$view=$reestr->get('view','');
		$task=$reestr->get('task','save');
		$arr_psid=$reestr->get('arr_psid',false);
		$onCancelURL=$reestr->get('onCancelURL',false);
		$sort=$reestr->get('sort');
		$page=$reestr->get('page', 1);
		$orderby=$reestr->get('orderby');
		$multy_code=$reestr->get('multy_code');
		$parent_view=$meta->parent_view;
		if (Portal::getInstance()->noTemplate()) $class_postfix="_nt"; else $class_postfix="";
		Portal::getInstance()->addScriptDeclaration("$(window).on('load',function() { addAfterContentLoadHandler('modifySelector'); });");
		Portal::getInstance()->addScript("/redistribution/jquery.plugins/jquery.treeview.js");
		echo "<div class=\"moduleBody ".$this->get("module")."Module\"><div class=\"content".$class_postfix."\">";
		echo "<div class=\"container\"><div class=\"row\"><div class=\"col-md-12\">";
		echo "<div class=\"rounded-pan\">";
		echo "<form action=\"".(defined("_ADMIN_MODE") ? "index.php" : Router::_("index.php"))."\" method=\"post\">";
		echo HTMLControls::renderHiddenField("module",$module);
		echo HTMLControls::renderHiddenField("view",$view);
		echo HTMLControls::renderHiddenField("layout",$layout);
		echo HTMLControls::renderHiddenField("sort",$sort);
		echo HTMLControls::renderHiddenField("page",$page);
		echo HTMLControls::renderHiddenField("orderby",$orderby);
		echo HTMLControls::renderHiddenField("multy_code",$multy_code);
		echo HTMLControls::renderHiddenField("current_parent",$multy_code);
		echo HTMLControls::renderHiddenField("task",'saveLinks');
		echo "<fieldset>";
		echo "<legend>".Text::_("Subordination").":</legend>";
		$info="updateLabel(this,'".$module."','".$parent_view."','BuildPathTree',%s,1)";
		echo HTMLControls::renderPopupMultySelect("linkEditor",$this->linkArray,"","","index.php?module=".$module."&amp;view=".$parent_view."&amp;layout=selector&amp;task=getContList&amp;lol=linkEditor&amp;option=ajax","",$info);
		echo "</fieldset>";
		echo "<fieldset>";
		echo "<legend>".Text::_("Elements list").":</legend>";
		if(count($this->items)>0) {
			$counter=0;
			foreach($this->items as $value) {
				echo "<div class=\"element\">";
				echo "<div class=\"elementid\">".$value->id."</div>";
				echo "<div class=\"elementtitle\">".$value->title."</div>";
				echo HTMLControls::renderHiddenField("cps_id[]",$value->id,false);
				echo "</div>";
			}
		}
		echo "</fieldset>";

		echo "<div class=\"modify-buttons\">";
		echo "<input class=\"commonButton btn btn-info\" type=\"submit\" value=\"".Text::_("Save")."\" />";
		if ($onCancelURL) $onCancelClick="javascript:document.location.href='".Router::_($onCancelURL,false,false)."';";
		else $onCancelClick="javscript:history.go(-1);";
		echo "&nbsp;<input class=\"commonButton btn btn-info\" type=\"button\" onclick=\"".$onCancelClick."\" value=\"".Text::_("Cancel")."\" />";
		echo "</div>";

		//		echo "</div>";
		echo "</form>";
		echo "</div>";
		echo "</div></div></div>";
		echo "</div></div>";
	}

	public static function getValueFromCKArray($ck_label='',$val='')	{
		$array=SpravStatic::getCKArray($ck_label);
		if((is_array($array))&&(isset($array[$val]))) return $array[$val];
		else return '';
	}
}
?>