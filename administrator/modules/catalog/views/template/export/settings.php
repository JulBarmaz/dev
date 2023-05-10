<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

Portal::getInstance()->AddScriptDeclaration("$(document).ready(function() { csv_export_checkbox('export_ggr'); csv_export_accordion('export_ggr');});");
$form=new aForm("export_form", "post", "index.php",false);
$form->addInput(array("ID"=>"step", 	"TYPE"=>"hidden", "NAME"=>"step", "VAL"=>1));
$form->addInput(array("ID"=>"module", 	"TYPE"=>"hidden", "NAME"=>"module", "VAL"=>"catalog"));
$form->addInput(array("ID"=>"view", 	"TYPE"=>"hidden", "NAME"=>"view", "VAL"=>"export"));
$form->addInput(array("CLASS"=>"commonButton btn btn-info","TYPE"=>"submit",	"VAL"=>Text::_("Continue"),	"ID"=>"doit", "NAME"=>"doit","ONCLICK"=>"return csv_export_proceed();"));
?>
<div class="container"><div class="row"><div class="col-md-12"><div class="catalog-manager rounded-pan rounded-pan-medium">
<h4 class="title"><?php echo Text::_("Export settings");?></h4>
	<?php $form->StartLayout(); ?>
	<div class="row"><div class="col-md-6"><?php echo Text::_("Groups");?></div><div class="col-md-6"><?php echo Text::_("Fields");?></div></div>
	<div class="row"><div class="col-md-6">
		<?php echo $this->tree; ?>
	</div><div class="col-md-6">
		<?php 
		foreach($this->fields as $key=>$val){
			if(in_array($key,$this->hidden_fields)) continue;
			echo "<span class=\"checkboxContainer\">".HTMLControls::renderCheckbox($key,1,1).HTMLControls::renderLabelField($key,$val,1)."</span>";
		}
		?>
	</div></div>
	<div class="buttons">
		<?php $form->renderInputPart("module"); ?>
		<?php $form->renderInputPart("view"); ?>
		<?php $form->renderInputPart("step"); ?>
		<?php $form->renderInputPart("doit"); ?>
	</div>
	<?php $form->endLayout(); ?>
</div></div></div></div>		