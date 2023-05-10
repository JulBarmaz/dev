<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class confModuleParams {
	public static function _proceed(&$module){
		$module->addParam("sitemap_protocol_prefix", "select", "http", false, array("http"=>"http", "https"=>"https"), Text::_("sitemap protocol prefix description"));
		$module->addParam("sitemap_representation_file", "select", "common file", false, array("common file"=>"common file", "separate file"=>"separate file"), Text::_("sitemap xml file representation"));
	}
}
?>