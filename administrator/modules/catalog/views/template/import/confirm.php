<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$form=new aForm("import_form", "post", "index.php?module=catalog&view=import",false);
$form->addInput(array("ID"=>"step", "TYPE"=>"hidden", "NAME"=>"step", "VAL"=>3));
$form->addInput(array("CLASS"=>"commonButton btn btn-info","TYPE"=>"submit",	"VAL"=>Text::_("Upload"),	"ID"=>"doit", "NAME"=>"doit"));
$form->StartLayout();
?>
<div class="container"><div class="row"><div class="col-md-12"><div class="catalog-manager rounded-pan rounded-pan-mini">
	<h4 class="title"><?php echo Text::_("Import catalog data");?></h4>
	<div class="row"><div class="col-md-12">
		<p class="error"><?php echo Text::_("Make backup first")."!!!";?></p>
	</div></div>
	<div class="buttons">
		<?php $form->renderInputPart("step"); ?>
		<?php $form->renderInputPart("doit"); ?>
	</div>
</div></div></div></div>
<?php $form->endLayout(); ?>