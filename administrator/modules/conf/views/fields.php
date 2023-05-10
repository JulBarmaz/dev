<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class defaultViewfields extends View {
	function listModules()	{
		$type=Request::getInt('m_admin_side');
		$mdl = Module::getInstance();
		$model=$mdl->getModel("fields");
		$res=$model->getModulesList($type);
		$html="";
		if(is_array($res)&&count($res)>0) {
			foreach($res as $val)	{
				$html.="<option value=\"$val\">$val</option>";
			}
		} else $html="<option value=\"0\">".Text::_("Modules absent")."</option>";
		echo $html;
	}
	function listViews()	{
		$type=Request::getInt('m_admin_side');
		$m_module=Request::get('m_module','');
		$mdl = Module::getInstance();
		$model=$mdl->getModel("fields");
		$res=$model->getViewsList($type,$m_module);
		$html="";
		if(is_array($res)&&count($res)>0) {
			foreach($res as $val)	{
				$html.="<option value=\"$val\">$val</option>";
			}
		} else $html="<option value=\"0\">".Text::_("Views absent")."</option>";
		echo $html;
	}

	function listLayouts() {
		$type=Request::getInt('m_admin_side');
		$m_module=Request::get('m_module','');
		$m_view=Request::get('m_view','');
		$mdl = Module::getInstance();
		$model=$mdl->getModel("fields");
		$res=$model->getLayoutsList($type,$m_module, $m_view);
		$html="";
		if(is_array($res)&&count($res)>0) {
			foreach($res as $val)	{
				$html.="<option value=\"$val\">$val</option>";
			}
		} else $html="<option value=\"0\">".Text::_("Layouts absent")."</option>";
		echo $html;
	}
}
?>