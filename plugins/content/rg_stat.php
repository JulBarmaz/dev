<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_PLUGIN_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class contentPluginrg_stat extends Plugin {

	private $module="catalog";
	private $view= "goods";
	private $side=0;
	protected $_events=array("system.executeModuleAfter");	
	
	protected function setParamsMask(){
		parent::setParamsMask();
	}
	
	protected function onRaise($event, &$data) {
		if (Module::getInstance()->getName()!="catalog") return "";
		switch($event){
			case "system.executeModuleAfter":
				$module=$this->module;
				$view= $this->view;
				$side=$this->side;
				$psid = Request::getInt('psid', false); 					// ид строки
				$rg_referer = Request::getSafe("rg_referer","");
				if($rg_referer)	$this->writeRGEvent(base64_decode($rg_referer),$psid);
			break;
			default:
			break;
		}
	}
	private function writeRGEvent($remote_url, $psid){
		$sql="INSERT INTO #__goods_stat	(`gs_id`,`gs_remote_url`,`gs_goods_id`,	`gs_count`,	`gs_enabled`,`gs_deleted`) VALUES (NULL,'".$remote_url."',".$psid.",1,1,0) ON DUPLICATE KEY UPDATE gs_count=gs_count+1";
		Database::getInstance()->setQuery($sql);
		Database::getInstance()->query();
	}
}
?>