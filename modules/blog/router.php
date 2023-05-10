<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class blogCustomRouter extends Router {
	private $_views=array("category", "list", "post");
	public function parseRoute($request) {
		Debugger::getInstance()->message("Router (blog) parsed URI ".$request);
		$_result=preg_split('/[\/]/',$request);
		$module=""; $view=""; $layout=""; $psid="";$alias="";$page404=false;
		$poshtml=strpos($_result[count($_result)-1],'.html');
		$last_element = preg_split('/(\.html)/',$_result[count($_result)-1]);
		if (preg_match('/(index.php)/',$last_element[0])) $last_element[0]="";
		if (preg_match('/(default)/',$last_element[0])) $last_element[0]="";
		$_result[count($_result)-1] = $last_element[0];
		$need_redirect=false;
		switch (count($_result)) {
			case 2:
				$module = $_result[1];
				break;
			case 3:
				$module = $_result[1]; $view   = $_result[2];
				if($poshtml===false) { $page404=true; break; }
				if (!in_array($view,$this->_views)){
					$alias=$view;
					$view="list";
 					if(defined("_BARMAZ_TRANSLATE")){
						$translator = new Translator();
						// тут узнаем может у нас уже переведенный алиас на входе и надо его ид искать в конкретном языке
						$lang=$translator->getLangByString($alias);
						
						if($lang)
						{
							$psid = $translator->getIdByAlias('blog',$view,$alias,$lang);
							if($lang!=Text::getLanguage())
							{ // у нас базовый язык выбран и надо алиас вернуть на родину
								
							  $alias=$translator->getAliasByPsid('blog',$view,Text::getLanguage(),$psid);
							  if($alias) $need_redirect=true;
							} 
							
						}
						else{
							if(siteConfig::$defaultLanguage!=Text::getLanguage())
								$psid = $translator->getIdByAlias('blog',$view,$alias,$lang);
						}
					}
					if(!$psid) $psid = Module::getHelper("blog","blog",true)->getIdByAlias($view,$alias);
					if(!$psid && intval($alias)) {
						$psid = intval($alias);
						$alias = "";
					}
					if (!$psid) $page404=true;
				}
			break;
			case 4:
				$module = $_result[1]; $view   = $_result[2];
				$alias=$_result[3];
				if($poshtml===false) { $page404=true; break; }
				if(defined("_BARMAZ_TRANSLATE")){
					$translator = new Translator();
					$lang=$translator->getLangByString($alias);
					if($lang)
					{
						$psid = $translator->getIdByAlias('blog',$view,$alias,$lang);
						// тут узнаем может у нас уже переведенный алиас на входе и надо его ид искать в конкретном языке
						if($lang!=Text::getLanguage())
						{ // у нас базовый язык выбран и надо алиас вернуть на родину  
							$alias=$translator->getAliasByPsid('blog',$view,Text::getLanguage(),$psid);
							if($alias) $need_redirect=true;
							// в этот момент нужно сделать редирект с новым алиасом если он пришел
							// пока не выходит - стандартный редирект падает на парвах по модулю, а утил редирект - не формирует 
							// нормального сео обращения
							//$newurl=Router::_("index.php?module=".$module."&view=".$view."&alias=".$alias."&psid=".$psid);
							//$newurl="index.php?module=".$module."&view=".$view."&alias=".$alias."&psid=".$psid;
							//var_dump($newurl);
							
							//Util::redirect($newurl,true,false);
							//Module::getInstance($module)->getController()->setRedirect($newurl);
							
						}
						
					}	
					else{ 
					if(siteConfig::$defaultLanguage!=Text::getLanguage())
						$psid = $translator->getIdByAlias('blog',$view,$alias,$lang);
					}
				}
				if(!$psid)	$psid = Module::getHelper("blog","blog",true)->getIdByAlias($view,$alias);
				if (!$psid) $psid=intval($_result[3]);
				if (!$psid) $page404=true;
				break;
			case 5:
				$module = $_result[1]; $view   = $_result[2];
				$layout = $_result[3]; $alias=$_result[4];
				if($poshtml===false) { $page404=true; break; }
				if(defined("_BARMAZ_TRANSLATE")){
					$translator = new Translator();
					// тут узнаем может у нас уже переведенный алиас на входе и надо его ид искать в конкретном языке
					$lang=$translator->getLangByString($alias);
					if($lang)
					{
						$psid = $translator->getIdByAlias('blog',$view,$alias,$lang);
						
					}
					else{
						if(siteConfig::$defaultLanguage!=Text::getLanguage())
							$psid = $translator->getIdByAlias('blog',$view,$alias,$lang);
					}
				}
				if(!$psid)	$psid = Module::getHelper("blog","blog",true)->getIdByAlias($view,$alias);
				if (!$psid) $psid=intval($_result[4]);
				if (!$psid) $page404=true;
				break;
			default:
				$page404=true;
				break;
		}
		$_vars = array();
		$_vars['module'] = $module;
		if ($view) $_vars['view'] = $view;
		if ($layout) $_vars['layout'] = $layout;
		if ($psid) $_vars['psid'] = $psid;
		if ($alias) $_vars['alias']=$alias;
		if($need_redirect){
			$redurl=$this->buildRoute($_vars);
		}
		if ($page404) $_vars['page404'] = $page404;
		return $_vars;
	}
	
	public function buildRoute($options, $mode="sitelink", $force_fronte=0, $absolute_link=0, $force_protocol=0) {
		$url = $options['module']; unset($options['module']);
		if (array_key_exists('view', $options)) {
			if($options['view']!="list") $url.= "/".$options['view']; 
			unset($options['view']);
			if (array_key_exists('alias', $options) && $options['alias']) {
				$url.= "/".$options['alias']; 
				unset($options['alias']); unset($options['psid']);
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