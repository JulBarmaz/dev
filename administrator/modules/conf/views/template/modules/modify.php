<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

?>
<div class="container"><div class="row"><div class="col-md-12"><div id="modify-wrapper" class="conf-manager rounded-pan">
<?php 
echo $frm->startLayout(false);
echo "<h4 class=\"title\">".$dop_head." ".$head." : ".Text::_($meta->title)."</h4>";
echo "<div class='tab-content clearfix'>";
echo "<div class=\"tab-pane tab-pane-single active\">";
foreach ($input_type as $index=>$value)	{
	if ($meta->input_view[$index]==0 && $value!="hidden") continue;
	if ($meta->field[$index]=="m_config") {
		if (count($this->def_params)) {
			echo"<div class=\"modify-wrapper row\"><div class=\"col-sm-12\">";
			echo HTMLControls::renderParamsPanel("mod_param", $meta->field_title[$index], $this->def_params, $this->params, $this->activeTab);
			echo"</div></div>";
		}
		continue;
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
			case "texteditor":
				echo "<div class=\"modify-editor-wrapper\" id=\"wrapper-".$meta->field[$index]."\">";
				echo "<div class=\"row\"><div class=\"modify-label col-sm-12\">".$frm->renderLabelFor($meta->field[$index])."&nbsp;".$frm->renderBalloonFor($meta->field[$index],false)."</div></div>";
				echo "<div class=\"row\"><div class=\"modify-input col-sm-12\">".$frm->renderInputPart($meta->field[$index])."</div></div>";
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