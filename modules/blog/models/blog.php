<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class blogModelblog extends Model {

	public function checkTreeEnabled($psid=0){
		$tree=new simpleTreeTable();
		$tree->table="blogs_cats";
		$tree->fld_id="bc_id";
		$tree->fld_parent_id="bc_id_parent";
		$tree->fld_title="bc_name";
		$tree->fld_deleted="bc_deleted";
		$tree->fld_enabled="bc_enabled";
		$tree->buildTreeArrays("", 0 , 1, 1);
		foreach ($tree->getTreeArr(0) as $obj){
			if ((int)$obj->id==$psid) return true;
		}
		return false;
	}
	public function getCategory($psid) {
		$res = false;
		if(!$this->checkTreeEnabled($psid)) return $res;
		$query = "SELECT * FROM #__blogs_cats WHERE bc_id=".intval($psid);
		$this->_db->setQuery($query);
		$this->_db->loadObject($res);
		return $res;
	}
	
	public function getBlog($blog_id, $published_only=true) {
		$blog=false;
		$query = "SELECT * FROM #__blogs WHERE b_deleted=0 AND b_id=".intval($blog_id);
		if ($published_only) $query .=" AND b_enabled=1";
		$this->_db->setQuery($query);
		$this->_db->loadObject($blog);
		if (is_object($blog) && $published_only){
			// translate system **********
			if(defined("_BARMAZ_TRANSLATE")){
				// преобразуем сведения на тот язык который сейчас выбран, если он отличается от дефолтного
				if(siteConfig::$defaultLanguage!=Text::getLanguage()){
					$translator = new Translator();
					$arr_tables=array('blogs');
					$arr_data=array('blogs'=>array($blog));
					$arr_psid['blogs'][$blog->b_id]=$blog->b_id;
					$arr_key['blogs']='b_id';
					//Util::showArray($blog,'before');
					$it_data= $translator->updateReturnData($arr_tables,$arr_data,$arr_psid,$arr_key,Text::getLanguage());
					if($it_data) $blog=$it_data['blogs'][0];
					//Util::showArray($blog,'after');
				}
			}

			$query = "SELECT bc.bc_id FROM #__blogs_cats as bc
					LEFT JOIN #__blogs_links AS links ON links.parent_id=bc.bc_id
					WHERE links.b_id=".$blog_id;
			$this->_db->setQuery($query);
			$result=$this->_db->loadResultArray();
			if(count($result)){
				$cat_enabled = false;
				foreach ($result as $key=>$val){
					// Если хоть одна ветка разрешена возвращаем блог
					$cat_enabled = $this->checkTreeEnabled((int)$val);
					if($cat_enabled) return $blog;
				}
				if(!$cat_enabled) return false;
			}
		}
		return $blog;
	}
	
	/**
	 * Список блогов в категории
	 * @param $psid - ид категории
	 */
	public function getBlogs($psid, $published_only=true) {
		$res=array();
		if($published_only){
			// проверим что там категории и не выключены ли они
			$category = getCategory($psid);
			if(!is_object($category)) return $res;
		}
		$rights=Module::getInstance('blog')->getModel('rights');
		$query = "SELECT b.*, links.ordering as ordering
				FROM #__blogs as b 
				LEFT JOIN #__blogs_links AS links ON links.b_id=b.b_id
				WHERE b.b_deleted=0	AND b.b_show_in_list=1	AND links.parent_id=".$psid;							 
		if ($published_only) $query .=" AND b.b_enabled=1";
		$query .=" ORDER BY links.ordering ASC";
		$this->_db->setQuery($query);
		$result=$this->_db->loadObjectList();
		if(count($result)) {
			foreach($result as $val) {
				if ($rights->checkAction($val->b_id,"read"))	{
					$res[]=$val;
				}
			}
		}
		return $res;
	}
}

?>