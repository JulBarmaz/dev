<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class menusModule extends Module {
	public function prepare() {
		/* Not need here if is set in module settings */ $this->setDefaultView('items');
	}
	public function getACLTemplate($is_admin=true){		
		$acl=array();$i=0;
		if($is_admin){
			$i++;$acl[$i]['ao_name']='menusModule'; $acl[$i]['ao_description']='Module access';
			$i++;$acl[$i]['ao_name']='viewMenusItems'; $acl[$i]['ao_description']='Items';
			$i++;$acl[$i]['ao_name']='deleteMenusItems'; $acl[$i]['ao_description']='Finally delete items';
		} else {
			$i++;$acl[$i]['ao_name']='menusModule'; $acl[$i]['ao_description']='Module access';
		}
		return 	$acl;
	}
	public function getLinksArray(&$i,&$_arr) {
		$db=Database::getInstance();
		$module=$this->getName();
		$listCore=$this->getParam('core_listmenu');
	
		$result = array("html"=>"","links"=>array());
		if($listCore){
			// добываем недостающие сведения из базы
			$sql_add="SELECT * FROM #__menus WHERE mi_deleted=0 AND mi_enabled=1";
			$db->setQuery($sql_add);
			$arr_add=$db->loadObjectList("mi_id");
			$arr_core=explode(",",$listCore);
			$tree=new simpleTreeTable();
			$tree->table="menus";
			$tree->fld_id="mi_id";
			$tree->fld_parent_id="mi_parent_id";
			$tree->fld_title="mi_name";
			$tree->fld_deleted="mi_deleted";
			$tree->fld_enabled="mi_enabled";
			$tree->fld_alias="mi_alias";
			$tree->fld_orderby="mi_ordering";
			$result["html"]='';
			// тут цикл по корневым узлам
			$tree->buildTreeArrays("",0,1,1);
			foreach($arr_core as $val)
			{
				// получившуюся ссылку прогоняем через таблицу редиректов ,с учетом подстановки и разбора
				$tree->element_link="index.php?module=article&amp;view=read&amp;psid=";
				foreach($tree->getTreeArr($val) as $val2)
				{
					$i++;
					if(isset($arr_add[$val2->id])&&$arr_add[$val2->id]->mi_link)
					{
						$_arr[$module][$i]['link']=$arr_add[$val2->id]->mi_link;
					}
					else
					{
						$_arr[$module][$i]['link']=Router::_("index.php?module=".$arr_add[$val2->id]->mi_module."&view=".$arr_add[$val2->id]->mi_view."&psid=".$arr_add[$val2->id]->mi_psid."&alias=".$arr_add[$val2->id]->mi_alias."&layout=".$arr_add[$val2->id]->mi_layout."&controller=".$arr_add[$val2->id]->mi_controller."&task=".$arr_add[$val2->id]->mi_task, true);
					}
					$_arr[$module][$i]['name']=$val2->title;
					$_arr[$module][$i]['fullname']=$val2->title;
	
				}
			}
		}
		return true;
	}
	
}
?>