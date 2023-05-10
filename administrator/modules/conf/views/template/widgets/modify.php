<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

?>
<div class="container"><div class="row"><div class="col-md-12"><div id="modify-wrapper" class="conf-manager rounded-pan">
<?php 
Text::parseWidget($row->aw_name);
echo $frm->startLayout(false);
echo "<h4 class=\"title\">".$dop_head." ".$head." : ".Text::_($meta->title)."</h4>";
if(count($this->requiredDisabledModules)){
	$requiredDisabledModules = array();
	foreach($this->requiredDisabledModules as $module){
		$requiredDisabledModules[] = Text::_($module);
	}
	echo "<h5 class=\"message_error\">".Text::_("Required modules are disabled in your version")." (".Portal::getLicenseType().")".": ".implode(", ", $requiredDisabledModules)."</h5>";
}
echo "<div class='tab-content clearfix'>";
echo "<div class=\"tab-pane tab-pane-single active\">";
foreach ($input_type as $index=>$value)	{
	if ($meta->input_view[$index]==0 && $value!="hidden") continue;
	if ($meta->field[$index]=="aw_config") {
		if (count($this->w_params)) {
			echo"<div class=\"modify-wrapper row\"><div class=\"col-sm-12\">";
			echo HTMLControls::renderParamsPanel("aw_config", $meta->field_title[$index], $this->w_params, $this->wp, $this->activeTab);
			echo"</div></div>";
		}
		continue;
	}	else if ($meta->field[$index]=="aw_access") {
		echo "<div class=\"modify-wrapper row\" id=\"wrapper-".$meta->field[$index]."\">";
		echo "<div class=\"col-sm-12\">";
		echo "<fieldset><legend class=\"bold\">".Text::_($meta->field_title[$index]);
		echo "</legend>";
		$roles = ACLObject::getRoles();
		if ($this->w_access == "all") {
			$fsStyle=" style=\"display:none;\""; $ewShowChecked = " checked=\"checked\"";
		}	else {
			$fsStyle = ""; $ewShowChecked = "";
		}
		$ewaShowJS = "$('#widgetAccess').toggle();";
		echo "<div class=\"visible_all row\"><div class=\"col-xs-2 col-sm-1\"><input type=\"checkbox\" id=\"aw_access_all\" onclick=\"".$ewaShowJS."\" name=\"aw_access_all\"".$ewShowChecked." /></div>";
		echo "<div class=\"col-xs-10 col-sm-11\">".HTMLControls::renderLabelField("aw_access_all",Text::_('Show everybody'))."</div>";
		echo "</div>";
		echo "<div id=\"widgetAccess\"".$fsStyle.">";
		foreach ($roles as $role) {
			echo "<div class=\"row\">";
			$checked = 0;
			if (is_array($this->w_access) && in_array($role->ar_id, $this->w_access)) $checked = $role->ar_id;
			echo "<div class=\"col-xs-3 col-sm-1\">".HTMLControls::renderCheckbox("aw_access[".$role->ar_id."]", $checked, $role->ar_id, "aw_access_".$role->ar_id)."</div>";
			echo "<div class=\"col-xs-9 col-sm-11\">".HTMLControls::renderLabelField("aw_access_".$role->ar_id, $role->ar_title)."</div>";
			echo "</div>";
		}
		echo "</div>";
		echo "</fieldset>";
		echo"</div></div>";
	}	else if ($meta->field[$index]=="aw_visible_in") {
		if ($this->w_visible == "all") {
			$fsStyle=" style=\"display:none;\""; $ewShowChecked = " checked=\"checked\"";
		}	else {
			$fsStyle = ""; $ewShowChecked = "";
		}
		$ewShowJS = "$('#widgetVisibility').toggle();$('#visible_except_p').toggle();";
		$ewShowHideAllJS = "$(this).parents('fieldset.collapsible-fieldset').toggleClass('collapsed-fieldset');";
		echo "<div class=\"modify-wrapper row\" id=\"wrapper-".$meta->field[$index]."\">";
		echo "<div class=\"col-sm-12\">";
		echo "<fieldset class=\"collapsible-fieldset\"><legend class=\"bold\" onclick=\"".$ewShowHideAllJS."\">".Text::_($meta->field_title[$index])."<i class=\"glyphicon glyphicon-chevron-up i-arrow-up\" aria-hidden=\"true\"></i><i class=\"glyphicon glyphicon-chevron-down i-arrow-down\" aria-hidden=\"true\"></i></legend>";
		echo "<div class=\"visibility-wrapper\">";
		echo "<div class=\"visible_all row\"><div class=\"col-xs-2 col-sm-1\"><input type=\"checkbox\" id=\"aw_visible_all\" onclick=\"".$ewShowJS."\" name=\"aw_visible_all\"".$ewShowChecked." /></div>";
		echo "<div class=\"col-xs-10 col-sm-11\">".HTMLControls::renderLabelField("aw_visible_all",Text::_('Show everywhere'))."</div>";
		echo "</div>";
		if ((is_array($this->w_visible))&&(count($this->w_visible))&&$this->w_visible[0]=='except')	$excShowChecked = " checked=\"checked\"";
		else $excShowChecked="";
		echo "<div class=\"row\" id=\"visible_except_p\"".$fsStyle."><div class=\"col-xs-2 col-sm-1\">";
		echo "<input id=\"visible_except\" type=\"checkbox\" value=\"1\" name=\"visible_except\"".$excShowChecked." />";
		echo "</div>";
		echo "<div class=\"col-xs-10 col-sm-11\">";
		echo HTMLControls::renderLabelField("visible_except", Text::_('Except'));
		echo "</div>";
		echo "</div>";
		echo "<div id=\"widgetVisibility\"".$fsStyle.">";
		foreach ($this->m_items as $m_item) {
			echo "<div class=\"row option option_".$m_item->mi_level."\">";
			if ($m_item->mi_level==1) {
				echo "<div class=\"col-sm-12 optgroup\">".HTMLControls::renderLabelField("aw_visible_in_".$m_item->mi_id, $m_item->mi_name)."</div>";
				$current_menu=$m_item->mi_name;
			} else {
				if ((is_array($this->w_visible))&&(in_array($m_item->mi_id,$this->w_visible)))	$checked = $m_item->mi_id; else $checked = 0;
				echo "<div class=\"col-xs-3 col-sm-1\">".HTMLControls::renderCheckbox("aw_visible_in[".$m_item->mi_id."]",$checked,$m_item->mi_id,"aw_visible_in_".$m_item->mi_id)."</div>";
				echo "<div class=\"col-xs-9 col-sm-11\">".HTMLControls::renderLabelField("aw_visible_in_".$m_item->mi_id,$m_item->mi_name, false,"","aw_visible")."</div>";
			}
			echo "</div>";
		}
		echo "</div>";
		echo "</div>";
		echo "</fieldset>";
		echo"</div></div>";
	}	else if ($meta->field[$index]=="aw_name") {
		echo "<div class=\"modify-wrapper row\" id=\"wrapper-".$meta->field[$index]."\">";
		echo "<div class=\"modify-label col-sm-4\">".$frm->renderLabelFor($meta->field[$index])."&nbsp;".$frm->renderBalloonFor($meta->field[$index],false)."</div>";
		echo "<div class=\"modify-input col-sm-8\">".HTMLControls::renderLabelField($meta->field[$index], Text::_($frm->getInputValue($meta->field[$index])." widget")." (".$frm->getInputValue($meta->field[$index]).")")."</div>";
		$frm->appendTag("TYPE", $meta->field[$index], "hidden");
		echo "</div>";
	}	else {
		switch ($value)	{
			case "hidden": continue 2;
			break;
			case "image":
				echo "<div class=\"modify-image-wrapper\" id=\"wrapper-".$meta->field[$index]."\"><div class=\"row\">";
				echo "<div class=\"modify-label col-sm-4\">".$frm->renderLabelFor($meta->field[$index])."&nbsp;".$frm->renderBalloonFor($meta->field[$index],false)."</div>";
				echo "<div class=\"col-sm-8\">".$frm->renderInputPart($meta->field[$index])."</div>";
				echo "</div></div>";
				break;
			case "textarea":
				echo "<div class=\"modify-editor-wrapper\" id=\"wrapper-".$meta->field[$index]."\">";
				echo "<div class=\"row\"><div class=\"modify-label col-sm-12\">".$frm->renderLabelFor($meta->field[$index])."&nbsp;".$frm->renderBalloonFor($meta->field[$index],false)."</div></div>";
				echo "<div class=\"row\"><div class=\"modify-input col-sm-12\">".$frm->renderInputPart($meta->field[$index])."</div></div>";
				echo "</div>";
				break;
			case "texteditor":
				echo "<div class=\"modify-editor-wrapper\" id=\"wrapper-".$meta->field[$index]."\">";
				echo "<div class=\"row\"><div class=\"modify-label col-sm-12\">".$frm->renderLabelFor($meta->field[$index])."&nbsp;".$frm->renderBalloonFor($meta->field[$index],false)."</div></div>";
				$params=array("mode"=>'source');
				echo "<div class=\"row\"><div class=\"modify-input col-sm-12\">".HTMLControls::renderEditor($meta->field[$index],$meta->field[$index],$frm->GetInputValue($meta->field[$index]),'Basic',200,500,$params)."</div></div>";
				echo "</div>";
				break;
			case "checkbox":
				echo "<div class=\"modify-wrapper row\" id=\"wrapper-".$meta->field[$index]."\">";
				echo "<div class=\"modify-label col-sm-4 col-xs-9\">".$frm->renderLabelFor($meta->field[$index])."&nbsp;".$frm->renderBalloonFor($meta->field[$index],false)."</div>";
				echo "<div class=\"modify-input col-sm-8 col-xs-3\">".$frm->renderInputPart($meta->field[$index])."</div>";
				echo "</div>";
				break;
			default:
				echo "<div class=\"modify-wrapper row\" id=\"wrapper-".$meta->field[$index]."\">";
				echo "<div class=\"modify-label col-sm-4\">".$frm->renderLabelFor($meta->field[$index])."&nbsp;".$frm->renderBalloonFor($meta->field[$index],false)."</div>";
				echo "<div class=\"modify-input col-sm-8\">".$frm->renderInputPart($meta->field[$index])."</div>";
				echo "</div>";
				break;
		}
		
	}
}
echo "</div></div>";
echo "<div class=\"modify-buttons buttons\">";
echo $frm->renderInputPart("save");
if (!$ajaxModify) echo $frm->renderInputPart("apply");
echo $frm->renderInputPart("cancel");
echo "</div>";
echo $frm->endLayout();
?>
</div></div></div></div>