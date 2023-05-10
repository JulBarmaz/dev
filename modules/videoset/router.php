<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class videosetCustomRouter extends Router {
	public function parseRoute($request) {
		Debugger::getInstance()->message("Router (videoset) parsed URI ".$request);
		$_result=preg_split('/[\/]/',$request);
		$module=""; $view=""; $layout=""; $psid="";$alias="";$page404=false;
		$poshtml=strpos($_result[count($_result)-1],'.html');
		$last_element = preg_split('/(\.html)/',$_result[count($_result)-1]);
		if (preg_match('/(index.php)/',$last_element[0])) $last_element[0]="";
		if (preg_match('/(default)/',$last_element[0])) $last_element[0]="";
		$_result[count($_result)-1] = $last_element[0];
		switch (count($_result)) {
			case 2:
				$module = $_result[1];
				break;
			case 3:
				$module = $_result[1]; $view   = $_result[2];
				break;
			case 4:
				$module = $_result[1]; 	$view   = $_result[2];
				$alias=$_result[3];
				if($poshtml===false) { $page404=true; break; }
				$psid = Module::getHelper("videoset","videoset",true)->getIdByAlias($view,$alias);
				if (!$psid) $psid=intval($_result[3]);
				if (!$psid) $page404=true;
				break;
			case 5:
				$module = $_result[1]; $view = $_result[2];
				$layout = $_result[3]; $alias=$_result[4];
				if($poshtml===false) { $page404=true; break; }
				$psid = Module::getHelper("videoset","videoset",true)->getIdByAlias($view,$alias,$layout);
				if (!$psid) $psid=intval($_result[4]);
				if (!$psid) $page404=true;
				break;
			default:
				$page404=true;
				break;
		}
		$_vars = array();
		if ($module) $_vars['module']=$module;
		if ($view) $_vars['view']=$view;
		if ($layout) $_vars['layout']=$layout;
		if ($psid) $_vars['psid']=$psid;
		if ($alias) $_vars['alias']=$alias;
		if ($page404) $_vars['page404'] = $page404;
		return $_vars;
	}
	public function buildRoute($options, $mode="sitelink", $force_fronte=0, $absolute_link=0, $force_protocol=0) {
		$url = $options['module']; unset($options['module']);
		if (array_key_exists('view', $options) && $options['view']) {
			$url.= "/".$options['view']; unset($options['view']);
			if (array_key_exists('layout', $options)) {
				$url.= "/".$options['layout']; unset($options['layout']);
			}
			if (array_key_exists('alias', $options) && $options['alias']) {
				$url.= "/".$options['alias']; unset($options['alias']); unset($options['psid']);
			} else {
				if (array_key_exists('alias', $options)) unset($options['alias']);
				if (array_key_exists('psid', $options)) {
					$url.= "/".$options['psid']; unset($options['psid']);
				}
			}
		}
		$url.=".html"; $appendix="";
		if (count($options)>0) {
			foreach($options as $key=>$val) {	$appendix.="&".$key."=".$val;	}
		}
		$url=Portal::getURI($force_fronte, $absolute_link, $force_protocol).$url;
		if ($appendix) $url=$url."?".substr($appendix,1);
		return $url;
	}
}
?>