<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO


defined('_BARMAZ_VALID') or die("Access denied");
if(defined("_BARMAZ_TRANSLATE")) $translator = new Translator();
$dop_tab=0;
$html=$frm->startLayout(false);
$html.="<div class=\"container\"><div class=\"row\"><div class=\"col-md-12\"><div id=\"modify-wrapper\" class=\"rounded-pan\">";
$html.="<h4 class=\"title\">".$dop_head." ".$head." : ".Text::_($meta->title)."</h4>";

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
		$html.="<li class=\"switcher".$_class."\">";
		$html.="<a href=\"#tab_".$_key."\" data-key=\"".$_key."\" data-toggle=\"tab\">".(array_key_exists($_key, $pans_titles) ? Text::_($pans_titles[$_key]) : Text::_("Pan")." ".$_key)."</a>";
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
$html.="<div class=\"tablBody\">";
foreach ($input_type as $index=>$value)	{
	if($key==$last_tab){
		if($meta->input_page[$index]>0 && $meta->input_page[$index]!=$key) continue;
	} else {
		if($meta->input_page[$index]!=$key) continue;
	}

	if ($meta->input_view[$index]==0 && $value!="hidden") continue;
	if ($meta->field[$index]=="mi_access") {
		if (isset($row->mi_access)) {
			if ($row->mi_access == "all") $mi_access = $row->mi_access;
			else $mi_access = explode(";",$row->mi_access);
		}	else $mi_access="all";

		$html.="<div class=\"modify-wrapper row\" id=\"wrapper-".$meta->field[$index]."\">";
		$html.="<div class=\"col-sm-12\">";
		$html.="<fieldset><legend class=\"bold\">".Text::_($meta->field_title[$index]);
		$html.="</legend>";
		$roles = ACLObject::getRoles();
		if ($mi_access == "all") {
			$fsStyle="display:none;"; $ewShowChecked = " checked=\"checked\"";
		}	else {
			$fsStyle = ""; $ewShowChecked = "";
		}
		$ewaShowJS = "$('#itemAccess').toggle();";
		$html.="<div class=\"visible_all row\"><div class=\"col-xs-2 col-sm-1\"><input type=\"checkbox\" id=\"mi_access_all\" onclick=\"".$ewaShowJS."\" name=\"mi_access_all\"".$ewShowChecked." /></div>";
		$html.="<div class=\"col-xs-10 col-sm-11\">".HTMLControls::renderLabelField("mi_access_all",Text::_('Show everybody'))."</div>";
		$html.="</div>";
		$html.="<div id=\"itemAccess\" style=\"".$fsStyle."\">";
		foreach ($roles as $role) {
			$checked = "";
			if (is_array($mi_access) && in_array($role->ar_id,$mi_access)) $checked = "checked=\"checked\"";
			$html.="<div class=\"row\"><div class=\"col-xs-2 col-sm-1\"><input id=\"mi_access_".$role->ar_id."\" type=\"checkbox\" name=\"mi_access[".$role->ar_id."]\" $checked/></div>";
			$html.="<div class=\"col-xs-10 col-sm-11\">".HTMLControls::renderLabelField("mi_access_".$role->ar_id, $role->ar_title)."</div></div>";
		}
		$html.="</div>";
		$html.="</fieldset>";
		$html.="</div>";
		$html.="</div>";
	} elseif ($meta->field[$index]=="mi_module") {
		$html.="<div class=\"modify-wrapper row\" id=\"wrapper-".$meta->field[$index]."\">";
		$html.="<div class=\"modify-label col-sm-4\">".$frm->renderLabelFor($meta->field[$index])."&nbsp;".$frm->renderBalloonFor($meta->field[$index],false)."</div>";
		if (isset($row->mi_module)) $_cur_val=$row->mi_module; else $_cur_val="";
		$html.="<div class=\"modify-input col-sm-8\">";
		$html.=$this->renderModulesSelector($_cur_val);
		$html.="</div>";
		$html.="</div>";
	} elseif ($meta->field[$index]=="mi_view") {
		$html.="<div class=\"modify-wrapper row\" id=\"wrapper-".$meta->field[$index]."\">";
		$html.="<div class=\"modify-label col-sm-4\">".$frm->renderLabelFor($meta->field[$index])."&nbsp;".$frm->renderBalloonFor($meta->field[$index],false)."</div>";
		if (isset($row->mi_module)) $this->modname=$row->mi_module; else $this->modname="";
		if (isset($row->mi_view)) $this->vname=$row->mi_view; else $this->vname="";
		$html.="<div class=\"modify-input col-sm-8\">";
		$html.=$this->renderViewsSelector();
		$html.="</div>";
		$html.="</div>";
	} elseif ($meta->field[$index]=="mi_controller") {
		$html.="<div class=\"modify-wrapper row\" id=\"wrapper-".$meta->field[$index]."\">";
		$html.="<div class=\"modify-label col-sm-4\">".$frm->renderLabelFor($meta->field[$index])."&nbsp;".$frm->renderBalloonFor($meta->field[$index],false)."</div>";
		if (isset($row->mi_module)) $this->modname=$row->mi_module; else $this->modname="";
		if (isset($row->mi_controller)) $this->vcontroller=$row->mi_controller; else $this->vcontroller="";
		$html.="<div class=\"modify-input col-sm-8\">";
		$html.=$this->renderControllersSelector();
		$html.="</div>";
		$html.="</div>";
	} elseif ($meta->field[$index]=="mi_canonical_id") {
		$html.="<div class=\"modify-wrapper row\" id=\"wrapper-".$meta->field[$index]."\">";
		$html.="<div class=\"modify-label col-sm-4\">".$frm->renderLabelFor($meta->field[$index])."&nbsp;".$frm->renderBalloonFor($meta->field[$index],false)."</div>";
		if (isset($row->mi_canonical_id)) $this->mi_canonical_id=$row->mi_canonical_id; else $this->mi_canonical_id=0;
		if (isset($row->mi_id)) $this->mi_id=$row->mi_id; else $this->mi_id=0;
		$html.="<div class=\"modify-input col-sm-8\">";
		$html.=$this->renderMenuSelector();
		$html.="</div>";
		$html.="</div>";
	}	else {
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
			case "texteditor":
				$html.="<div class=\"modify-editor-wrapper\" id=\"wrapper-".$meta->field[$index]."\">";
				$html.="<div class=\"row\"><div class=\"modify-label col-sm-12\">".$frm->renderLabelFor($meta->field[$index])."&nbsp;".$frm->renderBalloonFor($meta->field[$index],false)."</div></div>";
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
		}
	}
} // foreach по полям
$html.="</div></div>"; // tab_body
} // for по вкладкам
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
$html.=$frm->renderInputPart("save");
if (!$ajaxModify) $html.=$frm->renderInputPart("apply");
$html.=$frm->renderInputPart("cancel");
$html.="</div>"; // buttons
$html.="</div>";
$html.="</div></div>";
$html.=$frm->endLayout();
echo $html;
?>