<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class articleModelarticle extends Model {
	private  $arrTranslateData=array();
	public function checkTreeEnabled($psid=0){
		$tree=new simpleTreeTable();
		$tree->table="articles";
		$tree->fld_id="a_id";
		$tree->fld_parent_id="a_parent_id";
		$tree->fld_title="a_title";
		$tree->fld_deleted="a_deleted";
		$tree->fld_enabled="a_published";
		$tree->buildTreeArrays("", 0 , 1, 1);
		foreach ($tree->getTreeArr(0) as $obj){
			if ((int)$obj->id==$psid) return true;
		}
		return false;
	}
	public function getArticle($aid, $no_deleted=1, $enabled_only=1) {
		if($enabled_only && $no_deleted && !$this->checkTreeEnabled($aid)) return false;
		$query = "SELECT a.*, u.u_nickname as author FROM #__articles AS a 
					left join #__users AS u ON a.a_author_id=u.u_id 
					WHERE  a.a_id=".intval($aid);
		if ($no_deleted) $query.= " AND a.a_deleted=0";
		if ($enabled_only) $query.= " AND a.a_published=1";
		$this->_db->setQuery($query);
		if ($this->_db->loadObject($art)) {
			if ($art->a_parent_id != 0) {
				$query = "SELECT a_title FROM #__articles WHERE a_id=".$art->a_parent_id;
				$this->_db->setQuery($query);
				$art->parentTitle = strval($this->_db->loadResult());
			}
			// translate system **********
			if(defined("_BARMAZ_TRANSLATE")){
				// преобразуем сведения на тот язык который сейчас выбран, если он отличается от дефолтного
				if(siteConfig::$defaultLanguage!=Text::getLanguage()){
					$translator = new Translator();
					$arr_tables=array('articles');
					$arr_data=array('articles'=>array($art));
					$arr_psid['articles'][$art->a_id]=$art->a_id;
					$arr_psid['users'][$art->a_author_id]=$art->a_author_id;
					$arr_key['articles']='a_id';
					$arr_key['users']='u_id';
					//Util::showArray($arr_data,'before');
					$dataTranslate= $translator->updateReturnData($arr_tables,$arr_data,$arr_psid,$arr_key,Text::getLanguage());
					if($dataTranslate) $art=$dataTranslate['articles'][0];
					//Util::showArray($dataTranslate,'after');
				}
			}
			
			return $art;
		}
		else return false;
	}
	public function getArticleByName($name, $no_deleted=1, $enabled_only=1) {
		if(defined("_BARMAZ_TRANSLATE")){
			// выясняем на каком языке мы сейчас находимся, у нас другой язык
			if(siteConfig::$defaultLanguage!=Text::getLanguage()){
				$translator = new Translator();
				$psid=$translator->getIdByAlias('articles', '', $name, Text::getLanguage());
				if($psid) return $this->getArticle($psid,$no_deleted, $enabled_only);
			}
			// если ничего не нашлось или язык основной  отправляем по прежнему пути
			return $this->getArticleByNameNoTranslate($name, $no_deleted, $enabled_only);
		}
		else 
		{
			 return $this->getArticleByNameNoTranslate($name, $no_deleted, $enabled_only); 
		}	
	}
	public function getArticleByNameNoTranslate($name, $no_deleted=1, $enabled_only=1) {
		$query = "SELECT a.*, u.u_nickname as author FROM #__articles AS a
		left join #__users AS u ON a.a_author_id=u.u_id 
		WHERE a.a_alias='".strval($name)."'";
		if ($no_deleted) $query.= " AND a.a_deleted=0";
		if ($enabled_only)$query.= " AND a.a_published=1";
		$this->_db->setQuery($query);
		if ($this->_db->loadObject($art)) {
			if($enabled_only && $no_deleted && !$this->checkTreeEnabled($art->a_id)) return false;
			if ($art->a_parent_id != 0) {
				$query = "SELECT a_title FROM #__articles WHERE a_id=".$art->a_parent_id;
				$this->_db->setQuery($query);
				$art->parentTitle = strval($this->_db->loadResult());
			}
			return $art;
		}
		else return false;
	}
	public function getChildsArray($aid, $childs_order_by='a_ordering',$childs_order_dir="ASC") {
		$query = "SELECT a_id, a_alias, a_title, a_date FROM #__articles";
		$query.= " WHERE a_parent_id=".intval($aid)." AND a_deleted=0 AND a_published=1";
		if($childs_order_by) $query.= " ORDER BY ".$childs_order_by." ".$childs_order_dir;
		else $query.= " ORDER BY a_ordering ASC";
		$this->_db->setQuery($query);
		if ($art=$this->_db->loadObjectList()) {
			if(defined("_BARMAZ_TRANSLATE")){
				// преобразуем сведения на тот язык который сейчас выбран, если он отличается от дефолтного
				if(siteConfig::$defaultLanguage!=Text::getLanguage()){
					$translator = new Translator();
					$arr_tables=array('articles');
					$arr_data=array('articles'=>$art);
					$arr_psid['articles'][$art->a_id]=$art->a_id;
					$arr_key['articles']='a_id';
					//Util::showArray($arr_data,'before');
					$dataTranslate= $translator->updateReturnData($arr_tables,$arr_data,$arr_psid,$arr_key,Text::getLanguage());
					if($dataTranslate) $art=$dataTranslate['articles'];
					//Util::showArray($dataTranslate,'after');
				}				
			}
			return $art;
		} else {
			return array();
		}
	}
	public function getAuthor($psid, $no_deleted=1) {
		$query = "SELECT a_author_id FROM #__articles WHERE (a_id=".intval($psid).")";
		if ($no_deleted) $query.= " AND (a_deleted=0)";
		$this->_db->setQuery($query);
		$art = $this->_db->loadResult();
		return $art;
	}
	public function getArticles($visibleOnly=true, $parent_id=0, $layout="") {
		// Один уровень, поэтому делать проверку от родительских не будем
		$query = "SELECT a.*, u.u_nickname as author FROM #__articles AS a
				LEFT JOIN #__users AS u ON a.a_author_id=u.u_id ";
		if ($visibleOnly) $query .= " WHERE  a.a_deleted=0 AND a.a_published=1 AND a.a_show_in_contents=1 ";
		else $query .= " WHERE a.a_deleted=0 ";
		if ($layout=="alias") $query .= " AND a.a_parent_id IN (SELECT DISTINCT a1.a_id FROM #__articles AS a1 WHERE a1.a_alias='".$parent_id."')";
		else $query .= " AND a.a_parent_id=".$parent_id;
		if($parent_id){
			$resOrd=false;
			$ordering_query="SELECT a_childs_order_by AS fld, a_childs_order_dir AS ord FROM #__articles WHERE a_id=".(int)$parent_id;
			$this->_db->setQuery($ordering_query);
			if($this->_db->loadObject($resOrd) && is_object($resOrd)){
				$query.= " ORDER BY ".$resOrd->fld." ".$resOrd->ord;
			} else {
				$query.= " ORDER BY a_ordering ASC";
			}
		} else {
			$query.= " ORDER BY a_ordering ASC";
		}
		//$query .= $this->getAppendix();
		$this->_db->setQuery($query);
		$articles = $this->_db->loadObjectList();
		if(defined("_BARMAZ_TRANSLATE")){
			// преобразуем сведения на тот язык который сейчас выбран, если он отличается от дефолтного
			if(siteConfig::$defaultLanguage!=Text::getLanguage()){
				$translator = new Translator();
				$arr_tables=array('articles');
				$arr_data=array('articles'=>$articles);
				$arr_psid['articles'][$art->a_id]=$art->a_id;
				$arr_key['articles']='a_id';
				//Util::showArray($arr_data,'before');
				$dataTranslate= $translator->updateReturnData($arr_tables,$arr_data,$arr_psid,$arr_key,Text::getLanguage());
				if($dataTranslate) $articles=$dataTranslate['articles'];
				//Util::showArray($dataTranslate,'after');
			}
		}

		return $articles;
	}
	public function getArticleCount($visibleOnly=true, $parent_id=0, $layout="") {
		$query = "SELECT COUNT(*) FROM #__articles ";
		if ($visibleOnly) {	$query .= "WHERE (a_deleted=0) AND (a_show_in_contents=1)";	}
		else { $query .= "WHERE a_deleted=0"; }
		if ($layout=="alias") {
			$query .= " AND a_parent_id IN (SELECT DISTINCT a_id FROM #__articles WHERE a_alias='".$parent_id."')";
		} else {
			$query .= " AND a_parent_id=".$parent_id;
		}
		$this->_db->setQuery($query);
		return intval($this->_db->loadResult());
	}
	public function saveArticle($id,$date,$parentid,$name,$title,$authorid,$text,$showinfo,$showincontents, $showchilds,$aShowTitle,$aShowBreadCrumb,$metakeywords, $metatitle, $metadescr,$published) {
		if ($id == 0) {
			$query = "INSERT INTO #__articles 
					(a_id,a_parent_id,a_author_id, a_date,a_title,a_alias, a_text,
					a_show_info, a_show_in_contents, a_show_childs,a_show_title,a_show_breadcrumb, 
					a_meta_title, a_meta_description, a_meta_keywords,
					a_rating, a_ordering, a_published, a_deleted
					) VALUES (
					NULL,".$parentid.",".$authorid.",NOW(),'".$title."','".$name."','".$text."',"
					.intval($showinfo).",".intval($showincontents).",".intval($showchilds).",".intval($aShowTitle).",".intval($aShowBreadCrumb).",'"
					.$metatitle."','".$metadescr."','".$metakeywords
					."', 0,0,".intval($published).",0)";
			$this->_db->setQuery($query);
			$this->_db->query();
			return $this->_db->insertid();
		}	else {
			$query = "UPDATE #__articles SET
					 a_alias='".$name
					."', a_title='".$title
					."', a_date='".$date
					."', a_text='".$text
					."', a_show_info=".intval($showinfo)
					.", a_show_in_contents=".intval($showincontents)
					.", a_show_childs=".intval($showchilds)
					.", a_show_title=".intval($aShowTitle)
					.", a_show_breadcrumb=".intval($aShowBreadCrumb)
					.", a_meta_keywords='".$metakeywords
					."', a_meta_title='".$metatitle
					."', a_published='".intval($published)
					."', a_meta_description='".$metadescr
					."', a_parent_id=".intval($parentid)
					." WHERE a_id=".$id;
			$this->_db->setQuery($query);
			if ($this->_db->query()) return $id;
		}
		return false;
	}
	public function deleteArticle($aid) {
		$query = "UPDATE #__articles SET a_deleted=1 WHERE a_id=".intval($aid);
		$this->_db->setQuery($query);
		$this->_db->query();
	}
	public function undeleteArticle($aid) {
		$query = "UPDATE #__articles SET a_deleted=0 WHERE a_id=".intval($aid);
		$this->_db->setQuery($query);
		$this->_db->query();
	}
	public function createTree($parent_id=0, $id = 0,$show_html=true) {
		$idcolor='gv';
		$_menu="";
		$sql_txt = " SELECT d1.*, ( select count(d2.a_parent_id) from #__articles as d2 ";
		$sql_txt.= "\n where  d2.a_parent_id=d1.a_id AND d2.a_deleted=0 )  as col_children ";
		$sql_txt.="\n FROM #__articles as d1";
		$sql_txt.="\n WHERE  d1.a_parent_id=".(int)$parent_id;
		$sql_txt.="\n AND d1.a_deleted=0";
		if($id!=0)
		$sql_txt.="\n AND d1.a_id=".(int)$id;
		$sql_txt.="\n ORDER BY d1.a_title";
		$this->_db->setQuery($sql_txt);
		if($rows = $this->_db->loadObjectList())	{
			if(defined("_BARMAZ_TRANSLATE")){
				// преобразуем сведения на тот язык который сейчас выбран, если он отличается от дефолтного
				if(siteConfig::$defaultLanguage!=Text::getLanguage()){
					$translator = new Translator();
					$arr_tables=array('articles');
					$arr_data=array('articles'=>$rows);
					$arr_psid['articles'][$art->a_id]=$art->a_id;
					$arr_key['articles']='a_id';
					//Util::showArray($arr_data,'before');
					$dataTranslate= $translator->updateReturnData($arr_tables,$arr_data,$arr_psid,$arr_key,Text::getLanguage());
					if($dataTranslate) $rows=$dataTranslate['articles'];
					//Util::showArray($dataTranslate,'after');
				}
			}

			if($parent_id==0)	$_menu.="<ul id='".$idcolor."' class=\"treeview-".$idcolor." treeview\">";
			else $_menu.="<ul class=\"treeview-".$idcolor." treeview\">";
			foreach ($rows as $keys=>$row){
				$row->a_url='index.php?module=article&amp;view=read&amp;alias='.$row->a_alias.'&amp;psid='.$row->a_id;
				$row->a_target='';
				if($show_html) {
					$img_group='';
					$targ='';
					if (strlen(trim($row->a_url))==0)	$url='#';	else $url=$row->a_url;
					if($row->col_children>0)
					{$img_group="";//<img src=\"/images/tree.gif\" alt=\"\" />"; $url="#";
					$_menu.= '<li >'; //class="expandable"
					$_menu.= '<div class="hitarea expandable-hitarea"></div>';
					$_menu.='<span onclick="document.location.href=\''.$row->a_url.'\';">'.$row->a_title.'</span>';
					}
					else{
					$_menu.= "<li>\n";
					$_menu.= "<span class=\"listgr\" onclick=\"document.location.href='".$row->a_url."';\">".$row->a_title."</span>\n";
					}
					if($row->col_children>0) {
						$_menu.= $this->createTree($row->a_id,0,$show_html);
					}
					$_menu.= "</li>\n";
				}
			}
			$_menu.="</ul>";
		}
		return $_menu;
	}
	public function updateAlias($psid, $alias, $name){
		if($alias) $alias=mb_substr(Translit::_($alias, DEF_CP, false), 0, 255);
		if (!$alias) $alias=mb_substr(Translit::_($name), 0, 255);
		if ($alias=="article") $alias=mb_substr($psid."-".Translit::_($name), 0, 255);
		$sql="SELECT COUNT(*) FROM #__articles WHERE a_alias='".$alias."' AND a_id<>".$psid;
		$this->_db->setQuery($sql);
		if ($this->_db->loadResult()>0){
			$alias=mb_substr($psid."-".$alias, 0, 255);
		}
		$sql="UPDATE #__articles SET a_alias='".$alias."' WHERE a_id=".$psid;
		$this->_db->setQuery($sql);
		if($this->_db->query()) return $alias; else return false;
	}
	public function getParentArticle($a_id,$level=0){
		if(!$level) $this->current_path=null;
		$sql="SELECT `a_id`,`a_parent_id`,`a_title`,`a_alias`
			FROM #__articles
			WHERE a_deleted=0 AND a_id=".(int)$a_id;
		Database::getInstance()->setQuery($sql);
		if(Database::getInstance()->LoadObject($res))	{
			if (defined ( "_BARMAZ_TRANSLATE" )) {
				if (siteConfig::$defaultLanguage != Text::getLanguage ()) {
					if (! count ( $this->arrTranslateData )) {
						$translator = new Translator ();
						$this->arrTranslateData = $translator->getListTranslateData('articles',
								Text::getLanguage(),array('a_title','a_alias'));
					}
					if (isset ( $this->arrTranslateData [$res->a_id] )) {
						$res->a_title = $this->arrTranslateData [$res->a_id] ['a_title'];
						$res->a_alias = $this->arrTranslateData [$res->a_id] ['a_alias'];
					}
				}
			}

			$this->current_path[$res->a_id]["title"]=$res->a_title;
			$this->current_path[$res->a_id]["alias"]=$res->a_alias;
			if($res->a_parent_id)	{
				$level++;
				$this->getParentArticle($res->a_parent_id,$level);
			}
		}
		return $this->current_path;
	}
}
?>