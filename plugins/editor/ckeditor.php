<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_PLUGIN_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class editorPluginckeditor extends Plugin {
	protected $_events=array("editor.render");

	protected function setParamsMask(){
		parent::setParamsMask();
		$this->addParam("advanced_content_filter", "boolean", 1, false, null, Text::_("Disable to allow using manual code"));
		$this->addParam("enable_file_browser", "boolean", 0, false, null, Text::_("You must install and enable file browser plugin first"));
		$this->addParam("enter_mode", "select", "ENTER_P", false, array("ENTER_P"=>"P", "ENTER_BR"=>"BR", "ENTER_DIV"=>"DIV"));
		$this->addParam("extra_cke_plugins", "text", "", false, null, Text::_("One element per row"));
		$this->addParam("extra_cke_menus", "text", "", false, null, Text::_("One element per row"));
	}
	protected function onRaise($event, &$data) {
		$paramsArr=array();
		$paramsArr[]="'startupMode' : '".$this->getParam("mode", false, "wysiwyg")."'";
		$paramsArr[]="'allowedContent':".($this->getParam("advanced_content_filter") ?  "false" : "true")."";
		if(!$this->getParam("advanced_content_filter")){
			$paramsArr[]="'extraAllowedContent': '*(*);*{*}'";
			//$paramsArr[]="'htmlEncodeOutput ': false";
		}
/*
Rules for allowedContent=false

Allows any class in the editor is:
config.extraAllowedContent = '*(*)';

Allows any class and any inline style.
config.extraAllowedContent = '*(*);*{*}';

Allows only class="class1" and class="class2" for any tag:
config.extraAllowedContent = '*(class1,class2)';

Allows only class="class1" only for div tag:
config.extraAllowedContent = 'div(class1)';

Allows id attribute for any tag:
config.extraAllowedContent = '*[id]';

Allows style tag (<style type="text/css">...</style>):
config.extraAllowedContent = 'style';

Example:
config.extraAllowedContent = 'span;ul;li;table;td;style;*[id];*(*);*{*}';
OR
config.extraAllowedContent = 'div(*);center'
*/
		$extra_plugins = $this->getParam("extra_cke_plugins");
		$extra_plugins_arr = explode("\n", str_replace("\r","\n", $extra_plugins));
		$paramsArr[] = "'extraPlugins':'"."readmore".(count($extra_plugins_arr) ? ",".implode(",", $extra_plugins_arr) : "")."'";
		
		$extra_menus = $this->getParam("extra_cke_menus");
		$extra_menus_arr = explode("\n", str_replace("\r","\n", $extra_menus));

		$toolbars_arr = array("toolbar_Default"=>array(), "toolbar_Basic"=>array());
		$toolbars_arr["toolbar_Default"][] = array("Source");
		$toolbars_arr["toolbar_Default"][] = array("Cut","Copy","Paste","PasteText","PasteFromWord","-","Print");
		$toolbars_arr["toolbar_Default"][] = array("Undo","Redo","-","Find","Replace","-","SelectAll","RemoveFormat");
		$toolbars_arr["toolbar_Default"][] = array("Image","Flash","Table","HorizontalRule","Smiley","SpecialChar");
		$toolbars_arr["toolbar_Default"][] = array("Link","Unlink","Anchor","PageBreak");
		$toolbars_arr["toolbar_Default"][] = array("Readmore", "ShowBlocks","-","About","-","Maximize");
		$toolbars_arr["toolbar_Default"][] = "/";
		$toolbars_arr["toolbar_Default"][] = array("Styles","Format","Font","FontSize");
		$toolbars_arr["toolbar_Default"][] = array("TextColor","BGColor");
		$toolbars_arr["toolbar_Default"][] = array("Bold","Italic","Underline","Strike","-","Subscript","Superscript");
		$toolbars_arr["toolbar_Default"][] = array("JustifyLeft","JustifyCenter","JustifyRight","JustifyBlock");
		$toolbars_arr["toolbar_Default"][] = array("NumberedList","BulletedList","-","Outdent","Indent","Blockquote","CreateDiv");
		if(count($extra_menus_arr)) $toolbars_arr["toolbar_Default"][] = $extra_menus_arr;
		$toolbars_arr["toolbar_Basic"][] = array("Source","-","Bold","Italic","Underline","Strike","-","Link","Unlink","-","Image","Smiley","Flash","-",/*"PasteFromWord",*/"-","About");
		foreach($toolbars_arr as $tb_key=>$tb_val){
			$toolbar_text = "'".$tb_key."' : [";
			$toolbar_text_arr=array();
			foreach($tb_val as $k=>$v){
				if(!is_array($v) && $v==="/") $toolbar_text_arr[]="'/'";
				else $toolbar_text_arr[] = "['".implode("', '", $v)."']";
			}
			$toolbar_text.= implode(", ", $toolbar_text_arr)."]";
			$paramsArr[] = $toolbar_text;
			
		}
		
		if($this->getParam("toolbar", false)) $paramsArr[]="'toolbar' : '".$this->getParam("toolbar", false)."'";
		else $paramsArr[]="'toolbar' : 'Default'";
		
		$paramsArr[]="'enterMode':CKEDITOR.".$this->getParam("enter_mode");
		if($this->getParam("enter_mode")=="ENTER_BR")	$paramsArr[]="'shiftEnterMode':CKEDITOR.".$this->getParam("enter_mode");
		If($this->getParam("enable_file_browser"))	Event::raise("editor.ckeditor_params", array(), $paramsArr);
		$params=implode(",", $paramsArr);
// Util::showArray($paramsArr);
		Portal::getInstance()->addScript("/redistribution/ckeditor4/ckeditor.js");
		$init_js = "CKEDITOR.basePath = '/redistribution/ckeditor4/';";
		$init_js.= "$(document).ready(function() {";
		$init_js.= "CKEDITOR.replace('".$this->getParam("edName", false)."',{".$params."});";
		if(!$this->getParam("advanced_content_filter")) $init_js.= '$.each(CKEDITOR.dtd.$removeEmpty, function (i, value) { CKEDITOR.dtd.$removeEmpty[i] = false; });';
		$init_js.= "});";
		Portal::getInstance()->addScriptDeclaration($init_js);
	}
}
?>