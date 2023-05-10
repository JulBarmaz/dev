<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$form=new aForm("file_form", "post", "index.php?module=catalog&view=import",false);
$form->addInput(array("ID"=>"filecsv", "TYPE"=>"file", "NAME"=>"filecsv", "REQUIRED"=>array("FLAG"=>1,"MESSAGE"=>Text::_("It was not specified a valid file to upload"))));
$form->addInput(array("ID"=>"step", "TYPE"=>"hidden", "NAME"=>"step", "VAL"=>1)); 
$form->addInput(array("ID"=>"codepage", "TYPE"=>"select", "NAME"=>"codepage", "VAL"=>"windows-1251", "OPTIONS"=>array("windows-1251"=>"windows-1251","UTF-8"=>"UTF-8")));
$form->addInput(array("CLASS"=>"commonButton btn btn-info","TYPE"=>"submit",	"VAL"=>Text::_("Start"),	"ID"=>"doit", "NAME"=>"doit"));
$form->StartLayout();
?>
<div class="container"><div class="row"><div class="col-md-12"><div class="catalog-manager rounded-pan rounded-pan-mini">
	<h4 class="title"><?php echo Text::_("Import catalog data");?></h4>
	<div id="modify-wrapper">
		<div class="row"><div class="col-md-12">
			<p class="error"><?php echo Text::_("Make backup first")."!!!";?></p>
		</div></div>
		<div class="row"><div class="col-md-12">
			<p class="error"><?php echo Text::_("Maximum fields count")." - 64 !!!";?></p>
		</div></div>
		<div class="row"><div class="col-md-12">
			<?php echo Text::_("Filetype").": *.csv,*.zip";?>
		</div></div>
		<div class="row"><div class="col-sm-4"><?php echo HTMLControls::renderLabelField("filecsv",Text::_("Select source file").":");?></div>
		<div class="col-sm-8"><div class="fileselector"><?php $form->renderInputPart("filecsv");	?></div></div></div>
		<div class="row"><div class="col-sm-4"><?php echo HTMLControls::renderLabelField("codepage",Text::_("Codepage").":");?></div>
		<div class="col-sm-8"><?php $form->renderInputPart("codepage");	?></div></div>
		<div class="buttons">
			<?php $form->renderInputPart("step"); ?>
			<?php $form->renderInputPart("doit"); ?>
		</div>
	</div>
</div></div></div></div>
<?php $form->endLayout(); ?>
