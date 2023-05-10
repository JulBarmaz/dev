<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");
$html=$frm->startLayout(false);
$_activeTab=$this->activeTab; 
$pans_titles=SpravStatic::getCKArray("admin_goods_pans_titles");
$last_tab=$meta->input_last_page;
$el_name=$frm->getInputValue($meta->namestring);
if(!$el_name) $el_name=Text::_('New position');
$html.="<div class=\"container\"><div class=\"row\"><div class=\"col-md-12\">";
$html.="<div id=\"modify-wrapper\" class=\"rounded-pan\">";
$html.="<div id=\"tab_switcher\">";
$html.="<ul  class=\"nav nav-tabs\" id=\"tabs\">";
for($_key=1; $_key<=$last_tab; $_key++){
	if ($_key==$_activeTab) $_class=' active'; else $_class="";
	$html.="<li class=\"switcher".$_class."\">";
	$html.="<a href=\"#tab_".$_key."\" data-key=\"".$_key."\" data-toggle=\"tab\">".Text::_($pans_titles[$_key])."</a>";
	$html.="</li>";
}
$_akey=$_key+2;
if ($_akey==$_activeTab) $_class=' active'; else $_class="";
$html.="<li class=\"switcher".$_class."\">";
$html.="<a href=\"#tab_".$_akey."\" data-key=\"".$_akey."\" data-toggle=\"tab\">".Text::_("Goods")."</a>";
$html.="</li>";
$html.="</ul>";
$html.="</div>";

$html.="<div class=\"tab-content clearfix\">";
for($key=1; $key<=$last_tab; $key++){
	if ($key==$_activeTab) $_class=" active"; else $_class="";
	$html.="<div class=\"tab-pane".$_class."\" id=\"tab_".$key."\">";
	$html.="<h4 class=\"title\">".$el_name."</h4>";	
	foreach ($input_type as $index=>$value)	{
		if($key==$last_tab){
			if($meta->input_page[$index]>0 && $meta->input_page[$index]!=$key) continue;
		} else {
			if($meta->input_page[$index]!=$key) continue;
		}
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
	$html.="</div>";
}

$akey=$key+2;
if ($akey==$_activeTab) $_class=" active"; else $_class="";
$html.="<div class=\"tab-pane".$_class."\" id=\"tab_".$akey."\">";
$html.="<h4 class=\"title\">".$el_name."</h4>";
$html.="<div class=\"modify-wrapper row\"><fieldset>";
$html.="<legend>".Text::_("Goods list").":</legend>";
$html.=HTMLControls::renderHiddenField("goodsEditor_nf",$meta->namestring);
$a_info=""; //"updateLabel(this,'".$module."','goods','getSKU',%s,1)";
$html.=HTMLControls::renderPopupMultySelect("goodsEditor",$this->goodsList,"","","index.php?module=".$module."&amp;view=goods&amp;layout=selector&amp;task=getContList&amp;lol=goodsEditor&amp;option=ajax",'',$a_info,false);
$html.="</fieldset></div>";
$html.="</div>";
$html.="</div>"; //tab-content
$html.="<div class=\"modify-buttons buttons\">";
$html.=HTMLControls::renderHiddenField("activeTab",$_activeTab);
$html.=$frm->renderInputPart("save");
$html.=$frm->renderInputPart("apply");
$html.=$frm->renderInputPart("add_new");
$html.=$frm->renderInputPart("cancel");
$html.="</div>"; // modify-buttons buttons
$html.="</div>"; // modify-wrapper
$html.=$frm->endLayout();
echo $html;
?>