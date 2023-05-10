<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");
$html=$frm->startLayout(false);
$le_script="";
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
$html.="<a href=\"#tab_".$_akey."\" data-key=\"".$_akey."\" data-toggle=\"tab\">".Text::_("Analogs")."</a>";
$html.="</li>";
if (is_object($row) && $row->g_type==5)	$_style=""; else $_style=" style=\"display:none;\""; 
$_akey=$_key+3;
if ($_akey==$_activeTab) $_class=' active'; else $_class="";
$html.="<li id=\"gmtab_".$_akey."\" class=\"switcher".$_class."\">";
$html.="<a href=\"#tab_".$_akey."\" data-key=\"".$_akey."\" data-toggle=\"tab\">".Text::_("Complect set structure")."</a>";
$html.="</li>";
$_akey=$_key+4;
if ($_akey==$_activeTab) $_class=' active'; else $_class="";
$html.="<li class=\"switcher".$_class."\">";
$html.="<a href=\"#tab_".$_akey."\" data-key=\"".$_akey."\" data-toggle=\"tab\">".Text::_("Discounts and surcharges")."</a>";
$html.="</li>";
$_akey=$_key+5;
if ($_akey==$_activeTab) $_class=' active'; else $_class="";
$html.="<li class=\"switcher".$_class."\">";
$html.="<a href=\"#tab_".$_akey."\" data-key=\"".$_akey."\" data-toggle=\"tab\">".Text::_("Additional goods")."</a>";
$html.="</li>";
$html.="</ul>";
$html.="</div>";
$html.="<div class=\"tab-content clearfix\">";
for($key=1; $key<=$last_tab; $key++){
	if ($key==$_activeTab) $_class=" active"; else $_class="";
	$html.="<div class=\"tab-pane".$_class."\" id=\"tab_".$key."\">";
	$html.="<h4 class=\"title\">".$el_name."</h4>";	
	if ($linkModify && $key==1) {
		$le_script="
			$(document).ready(function() {
				$('#linkEditor .delete-but').parent('p').on('remove', function() {
					var cur_grp_id = $(this).children('input[name=\'linkEditor[]\']').val()
					if($('#g_main_grp option:selected').val()==cur_grp_id){
						$('#g_main_grp option[value=\'0\']').attr('selected','selected');
						alert('".Text::_("You must specify main group now")."!');
					}
					$('#g_main_grp option[value=\''+cur_grp_id+'\']').remove();
				});
			});
		";
		$html.="<div class=\"modify-wrapper row\" id=\"wrapper-g_main_grp\">";
		$html.="<div class=\"modify-label col-sm-4\">".$frm->renderLabelFor('g_main_grp')."&nbsp;".$frm->renderBalloonFor('g_main_grp',false)."</div>";
		$html.="<div class=\"modify-input col-sm-8\">";
		$html.=HTMLControls::renderSelect('g_main_grp', 'g_main_grp', 'id', 'title', $linkArray, $frm->getInputValue('g_main_grp'));
		$html.="</div>";
		$html.="</div>";
		$html.="<div class=\"modify-wrapper row\"><fieldset>";
		$html.="<legend>".Text::_("Goods groups").":</legend>";
		$html.=HTMLControls::renderHiddenField("linkEditor_nf",$meta->parent_name);
		$html.=HTMLControls::renderPopupMultySelect("linkEditor",$linkArray,"","","index.php?module=".$module."&amp;view=".$parent_view."&amp;layout=selector&amp;task=getContList&amp;lol=linkEditor&amp;option=ajax",'',$info,$isNewLink);
		$html.="</fieldset></div>";
	}
	if ($key==2) {
		$html.="<div class=\"row\">";
		$html.="	<div class=\"col-sm-12\">";
		$html.="		<p class=\"adtl_img_link\"><a class=\"linkButton btn btn-info\" href=\"".Router::_("index.php?module=catalog&view=images&psid=".$frm->getInputValue('g_id'))."\">".Text::_("View additional images")."</a></p>";
		$html.="	</div>";
		$html.="</div>";
		$html.="<div class=\"row\">";
		$html.="	<div class=\"col-sm-6\">";
		$html.="		<div class=\"modify-image-wrapper\" id=\"wrapper-g_image\">";
		$html.="			<div class=\"row\">";
		$html.="				<div class=\"modify-label col-sm-4\">".$frm->renderLabelFor("g_image")."&nbsp;".$frm->renderBalloonFor("g_image",false)."</div>";
		$html.="				<div class=\"col-sm-8\">".$frm->renderInputPart("g_image")."</div>";
		$html.="			</div>";
		$html.="			<div class=\"row\">";
		$html.="				<div class=\"modify-label col-sm-4\">".$frm->renderLabelFor("g_title_img")."&nbsp;".$frm->renderBalloonFor("g_title_img",false)."</div>";
		$html.="				<div class=\"col-sm-8\">".$frm->renderInputPart("g_title_img")."</div>";
		$html.="			</div>";
		$html.="			<div class=\"row\">";
		$html.="				<div class=\"modify-label col-sm-4\">".$frm->renderLabelFor("g_alt_img")."&nbsp;".$frm->renderBalloonFor("g_alt_img",false)."</div>";
		$html.="				<div class=\"col-sm-8\">".$frm->renderInputPart("g_alt_img")."</div>";
		$html.="			</div>";
		$html.="		</div>";
		$html.="	</div>";
		$html.="	<div class=\"col-sm-6\">";
		$html.="		<div class=\"modify-image-wrapper\" id=\"wrapper-g_medium_image\">";
		$html.="			<div class=\"row\">";
		$html.="				<div class=\"modify-label col-sm-4\">".$frm->renderLabelFor("g_medium_image")."&nbsp;".$frm->renderBalloonFor("g_medium_image",false)."</div>";
		$html.="				<div class=\"col-sm-8\">".$frm->renderInputPart("g_medium_image")."</div>";
		$html.="			</div>";
		$html.="			<div class=\"row\">";
		$html.="				<div class=\"modify-label col-sm-4\">".$frm->renderLabelFor("g_title_med")."&nbsp;".$frm->renderBalloonFor("g_title_med",false)."</div>";
		$html.="				<div class=\"col-sm-8\">".$frm->renderInputPart("g_title_med")."</div>";
		$html.="			</div>";
		$html.="			<div class=\"row\">";
		$html.="				<div class=\"modify-label col-sm-4\">".$frm->renderLabelFor("g_alt_med")."&nbsp;".$frm->renderBalloonFor("g_alt_med",false)."</div>";
		$html.="				<div class=\"col-sm-8\">".$frm->renderInputPart("g_alt_med")."</div>";
		$html.="			</div>";
		$html.="		</div>";
		$html.="	</div>";
		$html.="</div>";
		$html.="<div class=\"row\">";
		$html.="	<div class=\"col-sm-6\">";
		$html.="		<div class=\"modify-image-wrapper\" id=\"wrapper-g_thumb\">";
		$html.="			<div class=\"row\">";
		$html.="				<div class=\"modify-label col-sm-4\">".$frm->renderLabelFor("g_thumb")."&nbsp;".$frm->renderBalloonFor("g_thumb",false)."</div>";
		$html.="				<div class=\"col-sm-8\">".$frm->renderInputPart("g_thumb")."</div>";
		$html.="			</div>";
		$html.="			<div class=\"row\">";
		$html.="				<div class=\"modify-label col-sm-4\">".$frm->renderLabelFor("g_title_thm")."&nbsp;".$frm->renderBalloonFor("g_title_thm",false)."</div>";
		$html.="				<div class=\"col-sm-8\">".$frm->renderInputPart("g_title_thm")."</div>";
		$html.="			</div>";
		$html.="			<div class=\"row\">";
		$html.="				<div class=\"modify-label col-sm-4\">".$frm->renderLabelFor("g_alt_thm")."&nbsp;".$frm->renderBalloonFor("g_alt_thm",false)."</div>";
		$html.="				<div class=\"col-sm-8\">".$frm->renderInputPart("g_alt_thm")."</div>";
		$html.="			</div>";
		$html.="		</div>";
		$html.="	</div>";
		$html.="</div>";
	} else {
		foreach ($input_type as $index=>$value)	{
			if($key==$last_tab){
				if($meta->input_page[$index]>0 && $meta->input_page[$index]!=$key) continue;
			} else {
				if($meta->input_page[$index]!=$key) continue;
			}
			if ($meta->input_view[$index]==0 && $value!="hidden") continue;
			if ($meta->field[$index]=='g_main_grp')	continue;
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
	}
	$html.="</div>";
}

$akey=$key+2;
if ($akey==$_activeTab) $_class=" active"; else $_class="";
$html.="<div class=\"tab-pane".$_class."\" id=\"tab_".$akey."\">";
$html.="<h4 class=\"title\">".$el_name."</h4>";
$html.="<div class=\"modify-wrapper row\"><fieldset>";
$html.="<legend>".Text::_("Goods list").":</legend>";
$html.=HTMLControls::renderHiddenField("analogEditor_nf",$meta->namestring);
$a_info=""; //"updateLabel(this,'".$module."','".$view."','getSKU',%s,1)";
$html.=HTMLControls::renderPopupMultySelect("analogEditor",$this->analogList,"","","index.php?module=".$module."&amp;view=".$view."&amp;layout=selector&amp;task=getContList&amp;lol=analogEditor&amp;option=ajax",'',$a_info,false);
$html.="</fieldset></div>";
$html.="</div>";

$akey=$key+3;
if ($akey==$_activeTab) $_class=" active"; else $_class="";
$html.="<div class=\"tab-pane".$_class."\" id=\"tab_".$akey."\">";
$html.="<h4 class=\"title\">".$el_name."</h4>";
$html.="<div class=\"modify-wrapper row\"><fieldset>";
$html.="<legend>".Text::_("Goods list").":</legend>";
$html.=HTMLControls::renderHiddenField("complectEditor_nf",$meta->namestring);
$a_info=""; //"updateLabel(this,'".$module."','".$view."','getSKU',%s,1)";
$html.=HTMLControls::renderPopupMultySelectWQ("complectEditor",$this->complectList,true,"","","","index.php?module=".$module."&amp;view=".$view."&amp;layout=selector&amp;task=getContList&amp;lol=complectEditor&amp;option=ajax",'',$a_info,false);
$html.="</fieldset></div>";
$html.="</div>";

$akey=$key+4;
if ($akey==$_activeTab) $_class=" active"; else $_class="";
$html.="<div class=\"tab-pane".$_class."\" id=\"tab_".$akey."\">";
$html.="<h4 class=\"title\">".$el_name."</h4>";
$html.="<div class=\"modify-wrapper row\"><fieldset>";
$html.="<legend>".Text::_("Discounts and surcharges list").":</legend>";
$html.=HTMLControls::renderHiddenField("discountEditor_nf",$meta->namestring);
$a_info="updateLabel(this,'".$module."','discounts','getSKU',%s,1)";
$html.=HTMLControls::renderPopupMultySelect("discountEditor",$this->discountsList,"","","index.php?module=".$module."&amp;view=discounts&amp;layout=selector&amp;task=getContList&amp;lol=discountEditor&amp;option=ajax",'',"",false);
$html.="</fieldset></div>";
$html.="</div>";

$akey=$key+5;
if ($akey==$_activeTab) $_class=" active"; else $_class="";
$html.="<div class=\"tab-pane".$_class."\" id=\"tab_".$akey."\">";
$html.="<h4 class=\"title\">".$el_name."</h4>";
$html.="<div class=\"modify-wrapper row\"><fieldset>";
$html.="<legend>".Text::_("Additional goods").":</legend>";
$html.=HTMLControls::renderHiddenField("additionalEditor_nf",$meta->namestring);
$a_info=""; //"updateLabel(this,'".$module."','".$view."','getSKU',%s,1)";
$html.=HTMLControls::renderPopupMultySelect("additionalEditor",$this->additionalList,"","","index.php?module=".$module."&amp;view=".$view."&amp;layout=selector&amp;task=getContList&amp;lol=additionalEditor&amp;option=ajax",'',$a_info,false);
$html.="</fieldset></div>";
$html.="</div>";

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
$html.=$frm->endLayout();
Portal::getInstance()->addScriptDeclaration($le_script);
echo $html;
?>