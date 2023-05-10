<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class defaultViewmediamanager extends View {
	public function renderFull (){
		echo HTMLControls::renderHiddenField("mm_return_element", $this->return_element);
		if ($this->is_ajax) {
			echo HTMLControls::renderHiddenField("mm_nfl",$this->nfl);
			echo "<link href=\"/css/modules/service.css\" type=\"text/css\" rel=\"stylesheet\" />";
			echo "<script src=\"/js/modules/service.js\"></script>";
			echo "<script src=\"/redistribution/jquery.plugins/jquery.form.min.js\"></script>";
		} else {
			Portal::getInstance()->addScript("/redistribution/jquery.plugins/jquery.form.min.js");
			$script = "$(document).ready(function() { mm_prepareUploader(); });";
			Portal::getInstance()->addScriptDeclaration($script);
		}
		$this->render();
	}
}
?>