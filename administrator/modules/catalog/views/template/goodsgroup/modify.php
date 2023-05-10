<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");
$html=$frm->startLayout(false);
$_activeTab=$this->activeTab;

$name_ggr=$frm->getInputValue('ggr_name');
if(!$name_ggr) $name_ggr=Text::_('New position');
$html.="<div class=\"container\"><div class=\"row\"><div class=\"col-md-12\">";
$html.="<div id=\"modify-wrapper\" class=\"rounded-pan".($meta->classTable ? " ".$meta->classTable : "")."\">";
$html.="<div id=\"tab_switcher\">";
$html.="<ul  class=\"nav nav-tabs\" id=\"tabs\">";
if ($_activeTab==1) $_class=' active'; else $_class="";
$html.="<li class=\"switcher".$_class."\">";
$html.="<a href=\"#tab_1\" data-toggle=\"tab\">".Text::_("Main data")."</a>";
$html.="</li>";
if($psid && count($this->group_fields)){
	if ($_activeTab==2) $_class=' active'; else $_class="";
	$html.="<li class=\"switcher".$_class."\">";
	$html.="<a href=\"#tab_2\" data-toggle=\"tab\">".Text::_("Group fields")."</a>";
	$html.="</li>";
}
$html.="</ul></div>"; // tab_switcher

if ($_activeTab==1) $_class=" active"; else $_class="";
$html.="<div class=\"tab-content clearfix\">";
$html.="<div class=\"tab-pane".$_class."\" id=\"tab_1\">";
$html.="<h4 class=\"title\">".$name_ggr."</h4>";
$html.="<table class=\"rounded-tbl non-rounded-tbl\">";
foreach ($input_type as $index=>$value)	{
	if ($meta->input_view[$index]==0 && $value!="hidden") continue;
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
			break;
	}
}
$html.="</table>";
$html.="</div>"; // tab-pane
if($psid && count($this->group_fields)){
	if ($_activeTab==2) $_class=" active"; else $_class="";
	$html.="<div class=\"tab-pane".$_class."\" id=\"tab_2\">";
	$html.="<h4 class=\"title\">".$name_ggr."</h4>";
	$fg_id = -1;
	foreach($this->group_fields as $fkey=>$fval){
		if($fval->fg_id !=$fg_id){
			if($fg_id > -1) $html.="</fieldset>";
			$html.="<fieldset class=\"modify-wrapper-block modify-wrapper-block-opened\" id=\"wrapper-block".$fval->fg_id."\">";
			$html.="<legend class=\"clickable\" onclick=\"toggleFieldsGroupChilds(this);\">".$fval->fg_name."</legend>";
			$fg_id = $fval->fg_id;
		}
		$html.="<div class=\"modify-wrapper row\" id=\"wrapper-".$meta->field[$index]."\">";
		$html.="<div class=\"modify-label col-xs-2 col-sm-1\">".HTMLControls::renderCheckbox("group_field[".$fval->f_id."]",($fval->parent_id ? $fval->f_id :0),$fval->f_id,"group_field_".$fval->f_id)."</div>";
		$html.="<div class=\"modify-label col-xs-10 col-sm-11\">".HTMLControls::renderLabelField("group_field_".$fval->f_id,$fval->f_descr)."</div>";
		$html.="</div>";
	}
	if($fg_id > -1) $html.="</fieldset>";
	$html.="</div>"; // tab-pane
}
$html.="</div>"; //tab-content
$html.="<div class=\"modify-buttons buttons\">";
$html.=HTMLControls::renderHiddenField("activeTab",$_activeTab);
$html.=$frm->renderInputPart("save");
$html.=$frm->renderInputPart("apply");
$html.=$frm->renderInputPart("add_clone");
$html.=$frm->renderInputPart("add_new");
$html.=$frm->renderInputPart("cancel");
$html.="</div>"; // modify-buttons buttons
$html.="</div>"; // modify-wrapper
$html.="</div></div></div>";
$html.=$frm->endLayout();
echo $html;
$js = "function toggleFieldsGroupChilds(el){
	// $(el).parent('.modify-wrapper-block').children('.modify-wrapper').toggle();
	if($(el).parent('.modify-wrapper-block').hasClass('modify-wrapper-block-opened')) $(el).parent('.modify-wrapper-block').removeClass('modify-wrapper-block-opened').addClass('modify-wrapper-block-closed');
	else $(el).parent('.modify-wrapper-block').removeClass('modify-wrapper-block-closed').addClass('modify-wrapper-block-opened');
}";
Portal::getInstance()->addScriptDeclaration($js);
?>