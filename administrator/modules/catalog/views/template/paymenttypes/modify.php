<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");
?>
<div class="container"><div class="row"><div class="col-md-12"><div id="modify-wrapper" class="catalog-manager rounded-pan rounded-pan-medium">
	<h4 class="title"><?php echo $dop_head." ".$head." : ".Text::_($meta->title); ?></h4>
	<?php 
	echo "<div class='tab-content clearfix'>";
	echo "<div class=\"tab-pane tab-pane-single active\">";
	echo $frm->startLayout(false);
	foreach ($input_type as $index=>$value)	{
		if ($meta->input_view[$index]==0 && $value!="hidden") continue;
		if ($meta->field[$index]=="pt_params") {
			if (count($this->def_params)) {
				echo"<div class=\"modify-wrapper row\"><div class=\"col-sm-12\">";
				echo HTMLControls::renderParamsPanel($meta->field[$index], $meta->field_title[$index], $this->def_params, $this->params);
				echo"</div></div>";
			}
			continue;
		}	else {
			switch ($value)	{
				case "hidden": continue 2;
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
	echo "</div></div>"
	?>
	<div class="buttons">
		<?php 
		echo $frm->renderInputPart("save");
		if (!$ajaxModify) echo $frm->renderInputPart("apply");
		echo $frm->renderInputPart("cancel");
		?>
	</div>
	<?php echo $frm->endLayout(); ?>
</div></div></div></div>