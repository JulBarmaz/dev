<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");
class articleCustomRouter extends Router {
	private $_views=array("read", "tree","list");
	public function parseRoute($request) {
		Debugger::getInstance()->message("Router (article) parsed URI ".$request);
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
				if($poshtml===false) { $page404=true; break; }
				if (!in_array($view,$this->_views)){
					$alias=$view;
					$psid = Module::getHelper("article","article",true)->getArticleByAlias($view);
					
					if (!$psid) $page404=true;
					$view="read";
				}
				break;
			case 4:
				// links like /article/read/3.html
				$module = $_result[1]; $view   = $_result[2];
				if($poshtml===false) { $page404=true; break; }
				if (!in_array($view,$this->_views)){
					$alias=$view;
					$psid = Module::getHelper("article","article",true)->getArticleByAlias($view);
					if (!$psid) $page404=true;
					$view="read";
					$layout=urldecode($_result[3]);
				} else {
					$alias=$_result[3];
					$psid = Module::getHelper("article","article",true)->getArticleByAlias($alias);
					if (!$psid && intval($alias)) {
						$art = Module::getHelper("article","article",true)->getArticle(intval($alias));
						if($art && is_object($art)) $psid = $art->a_id;
						$alias="";
					}
					if (!$psid) $layout=urldecode($_result[3]);
				}
				break;
			default:
				$page404=true;
				break;
		}
		$_vars = array();
		if ($module) $_vars['module'] = $module;
		if ($view) $_vars['view'] = $view;
		if ($layout) $_vars['layout'] = $layout;
		if ($psid) $_vars['psid'] = $psid;
		if ($alias) $_vars['alias']=$alias;
		if ($page404) $_vars['page404'] = $page404;
		return $_vars;
	}

	public function buildRoute($options, $mode="sitelink", $force_fronte=0, $absolute_link=0, $force_protocol=0) {
		$url = $options['module']; unset($options['module']);
		if ((!array_key_exists('view', $options)||(array_key_exists('view', $options) && $options['view']=="read")) 
			&& isset($options['psid']) 
			&& strval(intval($options['psid']))!=$options['psid']
			) $alias=$options['psid'];
		elseif (array_key_exists('alias', $options)) $alias=$options['alias']; 
		else $alias="";
		if ($alias){
			if (array_key_exists('layout', $options)) $layout=$options['layout']; else $layout="";
			if (array_key_exists('view', $options)&&($options['view']!='read')) $view=$options['view']; else $view="";
			if ($view) $url.= "/".$view;
			$url.= "/".$alias;
			if ($layout){ $url.= "/".$layout;	}
			unset($options['view']);
			unset($options['layout']);
			unset($options['psid']);
			unset($options['alias']);
		} else {
			if (array_key_exists('view', $options)) {
				$url.= "/".$options['view']; unset($options['view']);
				if (array_key_exists('layout', $options)) {
					$url.= "/".$options['layout']; unset($options['layout']);
				}
				if (array_key_exists('psid', $options)) {
					$url.= "/".$options['psid']; unset($options['psid']);
				}
			}
		}
		$url.=".html"; $appendix="";
		if (count($options)>0) {
			foreach($options as $key=>$val) {
				$appendix.="&".$key."=".$val;
			}
		}
		$url=Portal::getURI($force_fronte, $absolute_link, $force_protocol).$url;
		if ($appendix) $url=$url."?".substr($appendix,1);
		return $url;
	}
	public function getTreeUp($view, $layout, $psid, $alias){
		$arr=array();
		$result[$view]=$this->getParentArticle($arr, $psid);
		return $result;
	}
	private function getParentArticle(&$path_arr, $a_id, $level=0){
		if(!$level) $path_arr=array();
		$sql="SELECT `a_id`,`a_parent_id`,`a_alias` FROM #__articles WHERE a_deleted=0 AND a_id=".(int)$a_id;
		Database::getInstance()->setQuery($sql);
		if(Database::getInstance()->LoadObject($res))	{
			$path_arr[$res->a_id]=$res->a_alias;
			if($res->a_parent_id)	{
				$level++;
				$this->getParentArticle($path_arr, $res->a_parent_id,$level);
			}
		}
		return $path_arr;
	}
	
}
?>