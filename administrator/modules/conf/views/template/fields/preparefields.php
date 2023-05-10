<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

$mod_arr=Module::getInstalledModules(); asort($mod_arr);
foreach($mod_arr as $k=>$v){ $_arr[$v]=$v; } 
$form=new aForm("prepare_fields_form","post","index.php?module=conf&task=checkFields",false);
$zone_adress=""; //или пустой если по морде ползем, или "administrator/",если по админке
$form->addInput(array("TYPE"=>"checkbox", "NAME"=>"admin_zone", "TITLE"=>"Admin modules", "VAL"=>1));
$form->addInput(array("TYPE"=>"checkbox", "NAME"=>"deflt_zone", "TITLE"=>"Modules", "VAL"=>1));
$form->addInput(array('TYPE'=>"select", 'ID'=>'m_module', 'NAME'=>'m_module', 'CLASS'=>'form-control', 'SIZE'=>1, 'LABEL'=>Text::_('select mod name'), 'VAL'=>$this->m_module, 'OPTIONS'=>$_arr));
$form->addInput(array("TYPE"=>"submit", "CLASS"=>"commonButton btn btn-info", "VAL"=>Text::_("Prepare"), "NAME"=>"Prepare"));
$form->StartLayout();
?>
<div class="container"><div class="row"><div class="col-md-12"><div class="conf-manager rounded-pan rounded-pan-mini">
	<h4 class="title"><?php echo Text::_("Select module");?>	</h4>
	<div id="prepareVisio">
		<div class="row"><div class="col-md-4"><?php echo Text::_("Modules");?></div>
		<div class="col-md-8"><?php $form->renderInputPart("m_module");?></div></div>
		<div class="buttons">
			<?php $form->renderInputPart("Prepare"); ?>
		</div>
	</div>
<?php $form->endLayout(); ?>
</div></div></div></div>
