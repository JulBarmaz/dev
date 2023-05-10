<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class confModelsitemap extends Model {
	public $map_messages="";
	private $crlf="";
	
	public function createMaps($_html_file, $_xml_file) {
		$this->crlf=CR_LF;
		$_br="<br />";
		$this->map_messages=""; $msg="";
		if (($_html_file)||($_xml_file)) {
			$linkarray=$this->getLinksArr();
			$this->addmainLink($linkarray);
			if ($_html_file) {
				$filemap=PATH_FRONT.DS.'sitemap.html';
				$maptext=$this->getSiteMapText($linkarray);
				clearstatcache();
				if (!($handle = fopen($filemap, "w"))) $msg.="Cannot open file (".$filemap.")".$_br;
				elseif (fwrite($handle, $maptext) === FALSE) $msg.="Cannot write to file (".$filemap.")".$_br;
				else {
					$msg.="The file ".$filemap." successfully created.".$_br;
					fclose($handle);
				}
			}
			if ($_xml_file) {
				$filemapxml=PATH_FRONT.DS.'sitemap.xml';
				$mapxml=$this->getSiteMapXML($linkarray);
				clearstatcache();
				if (!($handlexml = fopen($filemapxml, "w"))) $msg.="Cannot open file (".$filemapxml.")".$_br;
				elseif (fwrite($handlexml, $mapxml) === FALSE) $msg.="Cannot write to file (".$filemapxml.")".$_br;
				else {
					$msg.="The file ".$filemapxml." successfully created.".$_br;
					fclose($handlexml);
				}
			}
		}
		$this->map_messages=$msg;
		return $this->map_messages;
	}
	function getLinksArr() {
		$_arr=array();
		$i=0;
		$arr_name=Module::getInstalledModules(true, true);
		foreach($arr_name as $module) {
			// на всякий случай проверяем существует ли модуль на фронте
			// @TODO  @FIXME- надо ли тут проверять на замену модуля - в принципе модуль установлен
			// хотя есть вариант что замещающий снесут - будет коллизия. 
			if (file_exists(PATH_FRONT_MODULES.$module.DS.'module.php')) Module::getInstance($module)->getLinksArray($i,$_arr);
		}
		return $_arr;
	}

	function getSiteMapText($module_arr)	{
		$html=HTMLControls::renderStaticHeader(Text::_("Sitemap")." ".Portal::getURI(1, 1));
		if (count($module_arr)>0) {
			foreach($module_arr as $module=>$linkarray) {
				if (count($linkarray)>0) {
					foreach($linkarray as $rec) {
						$html.='<p class="link_'.$module.'"><a href="'.$rec['link'].'" title="'.htmlspecialchars($rec['fullname'],ENT_COMPAT,"UTF-8",false).'">'.htmlspecialchars($rec['name'],ENT_COMPAT,"UTF-8",false).'</a></p>'.$this->crlf;
					}
				}
			}
		}	
		$html.=HTMLControls::renderStaticFooter();
		return $html;
	}
	public function getSiteMapXML($module_arr){
		$sitemap_protocol_prefix=$this->getModule()->getParam('sitemap_protocol_prefix');
		
		$_xml ='<?xml version="1.0" encoding="UTF-8"?>'.$this->crlf;
		$_xml.='<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">'.$this->crlf;
		$todaySEO=	date('c', Date::mysqldatetime_to_timestamp(Date::todaySQL()));
		$arr_xmllink=array();
		if (count($module_arr)>0) {
			foreach($module_arr as $module=>$linkarray) {
				if (count($linkarray)>0) {
					foreach($linkarray as $rec) {
						if (!Router::isAbsoluteLink($rec['link'])) {
							if(strpos($rec['link'],'/')===0) $rec['link']=substr($rec['link'], 1);
							$rec['link'] = Portal::getURI(1, 1).$rec['link'];
						}
						// проверяем на protocol-independent и подставлякм протокол в начало
						if(strpos($rec['link'],'//')===0 && $sitemap_protocol_prefix) $rec['link']=$sitemap_protocol_prefix.":".$rec['link'];
						// избавляемся от якорей
						if(strpos($rec['link'],'#')!==false) $rec['link']=substr($rec['link'],0,strpos($rec['link'],'#'));
						$ind=md5($rec['link']);
						if(!isset($arr_xmllink[$ind])){
							$arr_xmllink[$ind]['loc']=htmlentities($rec['link']);
							if(isset($rec['date_change'])){
								$timestamp=Date::mysqldatetime_to_timestamp($rec['date_change']);
								if($timestamp) $arr_xmllink[$ind]['lastmod']=date('c', $timestamp);
								else $arr_xmllink[$ind]['lastmod']=$todaySEO;
							} else {
								$arr_xmllink[$ind]['lastmod']=$todaySEO;
							}
							if(isset($rec['changefreq'])){
								$arr_xmllink[$ind]['changefreq']=$rec['changefreq'];
							} else {
								$arr_xmllink[$ind]['changefreq']='weekly';
							}
							if(isset($rec['priority'])){
								$arr_xmllink[$ind]['priority']=$rec['priority'];
							} else{
								$arr_xmllink[$ind]['priority']='0.5';
							}
							// тут добавляем картинки если они у нас есть - по позициям которых еще не было
							if(isset($rec['img'])){
								$arr_xmllink[$ind]['img']=$rec['img'];
							}
							
							
							
							
						}
					}
				}
			}
		}
		unset($module_arr);
		unset($linkarray);	
		foreach ( $arr_xmllink as $key => $val ) {
			$_xml .= "<url>" . $this->crlf;
			$_xml .= "<loc>" . $val ['loc'] . "</loc>" . $this->crlf;
			if (isset ( $val ['img'] ) && count ( $val ['img'] )) {
				// сюда пихаем картинки если они есть
				foreach ( $val ['img'] as $img ) {
					$_xml .= "<image:image>". $this->crlf;
					$_xml .= "<image:loc>" .  $img ['image']  . "</image:loc>". $this->crlf;
					$_xml .= "<image:title>" . $img ['title'] . "</image:title>". $this->crlf;
					if (isset ( $img ['geo_location'] ))
						$_xml .= "<image:geo_location>" . $img ['geo_location'] . "</image:geo_location>". $this->crlf;
						if (isset ( $img ['license'] ))
							$_xml .= "<image:license>" . $img ['license'] . "</image:license>". $this->crlf;
							$_xml .= "</image:image>". $this->crlf;
				}
			}
			$_xml .= "<lastmod>" . $val ['lastmod'] . "</lastmod>" . $this->crlf;
			$_xml .= "<changefreq>" . $val ['changefreq'] . "</changefreq>" . $this->crlf;
			$_xml .= "<priority>" . $val ['priority'] . "</priority>" . $this->crlf;
			
			$_xml .= "</url>" . $this->crlf;
		}
		$_xml.='</urlset>';
		unset($arr_xmllink);
		return $_xml;
	}
	function addmainLink(&$linkarray)
	{
		$arr=array();
		$_arr['main'][0]['link']=Portal::getInstance()->GetURI(1,1);
		$_arr['main'][0]['name']=Portal::getInstance()->getTitle();
		$_arr['main'][0]['fullname']=Portal::getInstance()->getTitle();
		$linkarray=$_arr+$linkarray;
	}
	function correctLink(&$linkarray)
	{
		
		// собираем что надо исключить из списка - проверяем по loc, module тут не используем - вдруг ошиблись
		$sql_excl=" select * from #__sitemap_man where m_type='2'";
		$this->_db->setQuery($sql_excl);
		$list_excl=$this->_db->loadObjectList();
		Util::showArray($list_excl,'excl');
		if(is_array($list_excl)&&count($list_excl))
		{
			foreach ($list_excl as $link)
			{
				foreach ($linkarray as $km=>$vm)
				{
					foreach ($vm as $ke=>$ve)
					{
						if($ve['link']===$link->m_loc)
						{
							unset($linkarray[$km][$ke]);
						}
					}
				}
			}
		}
		
		
		$sql_incl=" select * from #__sitemap_man where m_type='1'";
		$this->_db->setQuery($sql_incl);
		$list_incl=$this->_db->loadObjectList();
		Util::showArray($list_incl,'incl');
		$counter_mod=false;
		if(is_array($list_incl)&&count($list_incl))
		{
			// считаем максимумы модулей
			
			foreach ($list_incl as $linki)
			{
				// тут надо проверить что такого url еще нет
				$exists=false;
				foreach ($linkarray as $km=>$vm)
				{
					if(!$counter_mod) $max_num[$km]=0;
					foreach ($vm as $ke=>$ve)
					{
						if(!$counter_mod)  $max_num[$km]=max($max_num[$km],$ke);
						if((isset($ve['link']))&&$ve['link']===$linki->m_loc)
						{
							$exists=true;
							break 2;
						}
					}
				}
				$counter_mod=true;
				if(!$exists)
				{
					$arr_add=array();
					$module=$linki->m_module;
					//echo($module."  ".$max_num[$module]."  ".$linki->m_loc." <br /> ");
					if(isset($max_num[$module])) {$nextnum=$max_num[$module]+1;} else {$nextnum=1;}
					$max_num[$module]=$nextnum;
					//echo("ПОСЛЕ  ".$module."  ".$max_num[$module]."  ".$linki->m_loc." <br /> ");
					$arr_add[$module][$nextnum]['link']=$linki->m_loc;
					$arr_add[$module][$nextnum]['name']=$linki->m_title;
					$arr_add[$module][$nextnum]['fullname']=$linki->m_title;
					$arr_add[$module][$nextnum]['priority']=$linki->m_priority;
					$arr_add[$module][$nextnum]['changefreq']=$linki->m_changefreq;
					$arr_add[$module][$nextnum]['date_change']=$linki->m_lastmod;
					if(isset($linkarray[$module])){
						$linkarray[$module]=$linkarray[$module]+$arr_add[$module];
					}
					else
					{
						$linkarray=$linkarray+$arr_add;
					}
				}
			}
		}
	}	
}
?>