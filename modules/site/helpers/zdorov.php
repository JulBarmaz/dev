<?php
defined('_BARMAZ_VALID') or die("Access denied");

class siteHelperZdorov {
	public function zdorovgetSitemapHTML() {
		$db=Database::getInstance();
		$module='article';
		$result = array("html"=>"","links"=>array());
		
		$tree=new simpleTreeTable();
		$tree->table="articles";
		$tree->fld_id="a_id";
		$tree->fld_parent_id="a_parent_id";
		$tree->fld_title="a_title";
		$tree->fld_deleted="a_deleted";
		$tree->fld_enabled="a_published";
		$tree->fld_alias="a_alias";
		$tree->fld_orderby="a_title";
		$tree->element_link="index.php?module=article&amp;view=read&amp;psid=";
		// формируем ссылки которых не должно быть в карте сайта
		$excl_ar=$this->getExclArticlesMap();
		$tree->buildTreeArrays($excl_ar,0,1,1);
		$result["title_link"]=false;
		$result["html"] = $tree->getTreeHTML(0,'ul','article_tree');
		return $result;
	}
	
	public function getExclArticlesMap()
	{
		$string_ar="";
		$db=Database::getInstance();
		$sql="select obj_id from #__articles_data where field_name='df_excl_map' and field_value='1'";
		$db->setQuery($sql);
		$res=$db->loadResultArray();
		if($res){
			$excl_ar=array_flip($res);
			$string_ar=implode(",", array_keys($excl_ar));
		}
		return $string_ar;
	}
	
	
	public function videogetSitemapHTML() {
		$db=Database::getInstance();
		$_arr=array();
		$module='videoset';
		//$result = array("html"=>"","links"=>array());
		
		$sql1='SELECT vg_id, vg_title, vg_alias FROM #__videoset_galleries WHERE vg_deleted=0 AND vg_published=1';
		$db->setQuery($sql1);
		$i=0;
		$res1=$db->loadObjectList();
		//Util::showArray($res1);
		$html='';
		if (count($res1)) {
			$html.="<ul>";
			foreach($res1 as $val1)  {
				$i++;
				$_arr[$module][$i]['link']=Router::_("index.php?module=videoset&view=videos&psid=".$val1->vg_id."&alias=".$val1->vg_alias, true);
				$_arr[$module][$i]['name']=$val1->vg_title;
				$_arr[$module][$i]['fullname']=$val1->vg_title;
				$html.="<li><a href=\"".Router::_("index.php?module=videoset&view=videos&psid=".$val1->vg_id."&alias=".$val1->vg_alias, true)."\">".$val1->vg_title."</a></li>";
				}
				$html.="</ul>";
		}
		$result["title_link"]=false;
		$result["html"] = $html;
		return $result;
	}
}
?>