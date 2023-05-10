<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class articleControllerdefault extends Controller {
	private  $arrTranslateData=array();
	public function showRead($childs_order='title') {
		$page404	= Request::getBool('page404', false);
		if ($page404) $this->setRedirect('index.php',Text::_('Article not found'),404);
		
		$psid=$this->getPsid();
		$layout = $this->get('layout','read');
		$notmpl = Request::getInt('notmpl',0);
		$noctrl = Request::getInt('noctrl',$notmpl);
		$addbreadcrumb = Request::getBool('bc',true);
		$view = $this->getView();
		$model = $this->getModel('article');
		$view->assign("use_rating",$this->getConfigVal("use_rating"));

		if (!$psid) {
			if ( siteConfig::$defaultModule == 'article') {
				$view->assign('art',false);
				$view->assign('canModify', $this->checkACL("articleEditing",false));
			} else { 
				$this->setRedirect('index.php',Text::_('Article not found'),404); 
			}
		} else {
			// Article
			if ($this->checkACL("articleEditing",false)) $art = $model->getArticle($psid, 0, 0);
			else $art = $model->getArticle($psid);
			if (!$art) {
				if ($noctrl) { Util::halt(Text::_('Article not found')); }
				else {
					if (siteConfig::$defaultModule == 'article' && $psid == $this->getConfigVal("Default_item_ID")) {
						$view->assign('art',false);
						$view->assign('canModify', $this->checkACL("articleEditing",false));
					} else $this->setRedirect('index.php',Text::_('Article not found'),404);
				}
			} else {
				Module::getInstance()->setBreadCrumbVisibility($art->a_show_breadcrumb);
				if ($art->a_show_childs == '1') $arrChilds=$model->getChildsArray($art->a_id, $art->a_childs_order_by, $art->a_childs_order_dir);
				else $arrChilds=array();
				$user = User::getInstance();
				$artUrl = 'index.php?module=article&amp;view=read&amp;psid='.$art->a_id;
				if ($art->a_alias) $artUrl.="&alias=".$art->a_alias;
				if (!$noctrl){
					if ($art->a_id != $this->getConfigVal("Default_item_ID")) {
						if($addbreadcrumb) $this->createUpBreadcrumb($view,$art->a_id);
					}
				}

				if ($art->a_meta_keywords) $view->setMeta("keywords",$art->a_meta_keywords);
				if ($art->a_meta_title) $view->setMeta("title",$art->a_meta_title);
				else $view->setMeta("title",$art->a_title);
				if ($art->a_meta_description) $view->setMeta("description",$art->a_meta_description);
				$authorProfileUrl = Router::_("index.php?module=user&amp;view=info&amp;psid=".$art->a_author_id);
				$view->assign("comm",$this->initComments($art->a_id));
				$view->assign('articleAuthor',$art->author);
				$view->assign('articleRating',$art->a_rating);
				$view->assign('canModify', $this->checkACL("articleEditing",false));
				$view->assign('canVote',$this->checkACL("articleVoting",false));
				$view->assign('articleAuthorProfileUrl',$authorProfileUrl);
				$view->assign('articleDate',$art->a_date);
				$articleTitle = $art->a_title;
				$view->assign('articleTitle',$articleTitle);
				$view->assign('articleHTML',$art->a_text);
				$view->assign('art',$art);
				$view->assign("notmpl",$notmpl);
				$view->assign('articleShowInfo',$art->a_show_info);
				$view->assign('showTitle',$art->a_show_title);
				$view->assign('arrChilds',$arrChilds);
			}
		}
		$this->set("layout","default", true);
		if ($layout) {$view->setLayout($layout);}
	}

	//
	public function showTree() {
		$view = $this->getView();
		$model = $this->getModel('tree');
		$rootArticle=Request::getSafe("psid",0);
		$view->addBreadcrumb(Text::_('Articles'),'index.php?module=article');
		if($this->checkACL("articleEditing", false)) $model->buildTreeArrays(); 
		else $model->buildTreeArrays("", 0 , 1, 1);
		// и тут добавим про переводы
		if (defined ( "_BARMAZ_TRANSLATE" )) {
			if (siteConfig::$defaultLanguage != Text::getLanguage ()) {
				if (! count ( $this->arrTranslateData )) {
					$translator = new Translator ();
					$this->arrTranslateData = $translator->getListTranslateData('articles',
							Text::getLanguage(),array('a_title','a_alias'));
				}
				$arrTree=$model->getTreeAllRes();
				foreach ($arrTree as $key=>$val)
				{						
					if(isset ( $this->arrTranslateData [$val->id] )) {
						$val->title = $this->arrTranslateData [$val->id] ['a_title'];
						$val->alias = $this->arrTranslateData [$val->id] ['a_alias'];
					}
				}
				$model->setTreeAllRes($arrTree);
			}
		}

		$tree=$model->getTreeHTML($rootArticle,'ul','article_tree');
		$brokenParents=$model->getBrokenParents();
		$view->assign('tree',$tree);
		$view->assign('brokenParents',$brokenParents);
	}

	public function modify() {
		$this->checkACL("articleEditing");

		$user	= User::getInstance();
		$psid	= Request::getSafe('psid',"");
		$layout = $this->get('layout');
		$aParentId	= Request::get('parentid',Registry::getInstance()->get('articleParentId',0));
		$view = $this->getView("read");
		$view->setLayout("modify");
		$tree_model = $this->getModel('tree');

		$model = $this->getModel('article');
		$view->addBreadcrumb(Text::_('Articles'),'index.php?module=article');
		$view->addBreadcrumb(Text::_('Editing'),'#');

		if (!$psid) { // New article
			$tree_model->buildTreeArrays("",0,0);
			$select=$tree_model->getTreeHTML($aParentId, 'select','ArticleParent', 'parentId', $aParentId);
			$view->assign('articleSelect',$select);
			$view->assign('articleId',0);
			$view->assign('articleText','');
			$view->assign('articleAlias','');
			$view->assign('articleDate', Date::fromSQL(Date::nowSQL(), false, true));
			$view->assign('articleTitle','');
			$view->assign('showInfoCheck','checked="checked"');
			$view->assign('showInContentsCheck','checked="checked"');
			$view->assign('showChildsCheck','checked="checked"');
			$view->assign('showTitleCheck','checked="checked"');
			$view->assign('showBCCheck','checked="checked"');
			$view->assign('published','checked="checked"');
			$view->assign('metadescr','');
			$view->assign('metakeywords','');
			$view->assign('metatitle','');
			$view->render();
		}	else { // Edit article
			if ($this->checkACL("articleEditing",false)) $art = $model->getArticle($psid, 0, 0);
			else $art = $model->getArticle($psid);
			$artUrl = 'index.php?module=article&amp;view=read&amp;psid='.$psid;
			if (!$art) {
				$this->setRedirect('index.php',Text::_('Article not found'));
			}	else {
				if ($user->isAdmin() != true && (!$art->a_author_id || $user->getId() != $art->a_author_id)) {
					$this->setRedirect('index.php?module=article',Text::_('Permission is absent for editing this article'));
				}
				else {
					$showInfoCheck = '';
					$showInContentsCheck = '';
					$showChildsCheck = '';
					$showTitleCheck = '';
					$showBreadCrumbCheck = '';
					$published="";
					if ($art->a_show_info == '1') { $showInfoCheck = "checked=\"checked\""; }
					if ($art->a_show_in_contents == '1') { $showInContentsCheck = "checked=\"checked\""; }
					if ($art->a_show_childs == '1') { $showChildsCheck = "checked=\"checked\""; }
					if ($art->a_show_title == '1') { $showTitleCheck = "checked=\"checked\""; }
					if ($art->a_show_breadcrumb == '1') { $showBreadCrumbCheck = "checked=\"checked\""; }
					if ($art->a_published == '1') { $published = "checked=\"checked\""; }

					$tree_model->buildTreeArrays($art->a_id,0,0);
					$select=$tree_model->getTreeHTML(0,'select','ArticleParent', 'parentId', $art->a_parent_id);
					$view->assign('articleSelect',$select);
					$view->assign('articleId',$art->a_id);
					$view->assign('articleText',$art->a_text);
					$view->assign('articleAlias',$art->a_alias);
					$view->assign('articleDate', Date::fromSQL($art->a_date, false, true));
					$view->assign('articleTitle',$art->a_title);
					$view->assign('showInfoCheck',$showInfoCheck);
					$view->assign('showInContentsCheck',$showInContentsCheck);
					$view->assign('showChildsCheck',$showChildsCheck);
					$view->assign('showTitleCheck',$showTitleCheck);
					$view->assign('showBCCheck',$showBreadCrumbCheck);
					$view->assign('published',$published);
					$view->assign('metadescr',$art->a_meta_description);
					$view->assign('metakeywords',$art->a_meta_keywords);
					$view->assign('metatitle',$art->a_meta_title);
					$view->render();
				}
			}
		}
		if ($layout=="alias") {$view->setLayout('default');}
	}

	public function saveComment() {
		$layout	= Request::getSafe('layout');
		if ($layout=="alias") $this->set("layout","",true);
		parent::saveComment();
	}
	public function save() {
		$this->checkACL("articleEditing");
		$user = User::getInstance();
		$psid							= Request::getInt('psid',0);
		$aParentId				= Request::getInt('parentId',0);
		$aTitle						= Request::getSafe('articleTitle','');
		$aAlias						= Request::getSafe('articleAlias','');
		$aText						= Request::get('articleText','');
		$aShowInfo				= Request::getInt('articleShowInfo','');
		$aShowInContents	= Request::getInt('articleShowInContents',0);
		$aShowChilds			= Request::getInt('articleShowChilds',0);
		$aShowTitle				= Request::getInt('articleShowTitle',0);
		$aShowBreadCrumb	= Request::getInt('articleShowBreadCrumb',0);
		$published	= Request::getInt('articlePublished',0);
		$aDate						= Request::getDateTime('articleDate', Date::nowSQL());
		$metakeywords 		= Request::getSafe('metakeywords','');
		$metatitle 				= Request::getSafe('metatitle','');
		$metadescr 				= Request::getSafe('metadescr','');
		if ($aShowInfo) $aShowInfo = 1; else $aShowInfo = 0;
		if ($aShowInContents) $aShowInContents = 1; else $aShowInContents = 0;
		if ($aShowChilds) $aShowChilds = 1;	else $aShowChilds = 0;
		if ($aShowTitle) $aShowTitle = 1;	else $aShowTitle = 0;
		if ($aShowBreadCrumb) $aShowBreadCrumb = 1;	else $aShowBreadCrumb = 0;

		$model = $this->getModel('article');
		$new_alias="";
		if (!$psid) {
			$psid = $model->saveArticle(0,$aDate,$aParentId,$aAlias,$aTitle,$user->getId(),$aText,$aShowInfo,$aShowInContents,$aShowChilds,$aShowTitle,$aShowBreadCrumb,$metakeywords, $metatitle, $metadescr,$published);
			if($psid && $this->getConfigVal("use_rating")) Event::raise("rating.new",array("module"=>"article","element"=>"object","psid"=>$psid));   // content.rating
			if($psid) {
				$msg=Text::_('Article saved');
				$new_alias = $model->updateAlias($psid,$aAlias,$aTitle);
			}	else $msg=Text::_('Article not saved');
		}	else {
			$art = $model->getArticle($psid);
			if ($user->isAdmin() == false && (!$art->a_author_id || $user->getId() != $art->a_author_id)) {
				$this->setRedirect('index.php?module=article',Text::_('Permission is absent for editing this article'));
				return;
			}	else {
				if($model->saveArticle($psid,$aDate,$aParentId,$aAlias,$aTitle,0,$aText,$aShowInfo,$aShowInContents,$aShowChilds,$aShowTitle,$aShowBreadCrumb,$metakeywords, $metatitle, $metadescr,$published)) $msg=Text::_('Article saved');
				else $msg=Text::_('Article not saved');
				$new_alias = $model->updateAlias($psid,$aAlias,$aTitle);
			}
		}
		if($psid){
			$href='index.php?module=article&view=read&psid='.$psid;
			if ($new_alias) $href.="&amp;alias=".$new_alias;
			$this->setRedirect($href,$msg);
		} else $this->setRedirect('index.php',$msg);
	}

	public function delete() {
		$this->checkACL("articleEditing");
		$psid = Request::getInt('psid',0);
		$model = $this->getModel('article');
		$art = $model->getArticle($psid);
		if ($art == false) { $this->setRedirect("index.php?module=article&view=read&psid=".$psid); 	}
		else {
			$user = User::getInstance();
			$href="index.php?module=article&view=read&psid=".$psid;
			if ($art->a_alias) $href.="&amp;alias=".$art->a_alias;
			if (($art->a_author_id && $user->getId() == $art->a_author_id)||($user->isAdmin())) {
				$model->deleteArticle($psid);
				$this->setRedirect($href,Text::_('Article deleted'));
			}	else { $this->setRedirect($href); 	}
		}
	}

	public function undelete() {
		$this->checkACL("articleEditing");
		$psid = Request::getInt('psid',0);
		$model = $this->getModel('article');
		$art = $model->getArticle($psid,0);
		if ($art == false) { $this->setRedirect("index.php?module=article&view=read&psid=".$psid); 	}
		else {
			$user = User::getInstance();
			$href="index.php?module=article&view=read&psid=".$psid;
			if ($art->a_alias) $href.="&amp;alias=".$art->a_alias;
			if (($art->a_author_id && $user->getId() == $art->a_author_id)||($user->isAdmin())) {
				$model->undeleteArticle($psid);
				$this->setRedirect($href,Text::_('Article undeleted'));
			}	else { $this->setRedirect($href); 	}
		}
	}
	public function ajaxVote() {
		$psid = Request::getInt('psid',0);
		$element = Request::getSafe("element",false);
		switch ($element) {
			case "object":
				if ($this->checkACL("articleVoting",false) && $this->getConfigVal("use_rating")){
					$model = $this->getModel('article');
					$psid = Request::getInt('psid',0);
					$author= $model->getAuthor($psid);
					$direction = Request::getSafe('dir',"up");
					$params = array("module"=>"article","element"=>"object","psid"=>$psid,"direction"=>$direction, "author"=>$author);
					$data_voted=Event::raise("rating.check",$params);  // content.rating
					if (!siteConfig::$useMultiVote && $data_voted) {
						echo Text::_("Already voted");
					} else {
						if (User::checkFloodPoint()){
							echo Text::_("Flood found");
						} else {
							Event::raise("rating.vote",$params); // content.rating
							if ($direction=="up"){
								echo Text::_("Good");
							} else {
								echo Text::_("Bad");
							}
						}
					}
				}
		case "comment":
			parent::ajaxVote();
			break;
		default:
			return;
		break;
		}
	}
	/* Список статей */
	public function showList() 	{
		$model=$this->getModel('article');
		$view=$this->getView();
		$model->createPaginator($view,10);
		$psid = Request::getInt('psid',0);
		$list=$model->getArticles(!$this->checkACL("articleEditing", false), $psid);
		$view->assign('articles',$list);
		$art = false;
		if($psid) $art = $model->getArticle($psid);
		$view->assign('main_article',$art);
		
		$view->addBreadcrumb(Text::_("Main page"), Router::_("index.php"));
		$view->addBreadcrumb(Text::_("Articles"),"#");
		
	}
	public function createUpBreadcrumb($view,$psid) {
		$i=0;	$bc=array();
		$model = $this->getModel('article');
		$limitBread=$this->getConfigVal('breadcrumb_lenght');
		$arr=$model->getParentArticle($psid);
		if(count($arr)>0) {
			foreach($arr as $key=>$val) {
				$i++;
				$str=$val["title"];
				if($limitBread>0 && (mb_strlen($str) > $limitBread)) $bc[$i]['text']=mb_substr($str,0,$limitBread)."...";
				else $bc[$i]['text']=$val["title"];
				$bc[$i]['link']=Router::_("index.php?module=article&amp;view=read&amp;psid=".(int)$key."&amp;alias=".$val["alias"]);
			}
		}
		$i++;
		$breadcrumb_start=$this->getConfigVal('breadcrumb_start');
		$breadcrumb_start_link=$this->getConfigVal('breadcrumb_start_link');
		if($breadcrumb_start && $breadcrumb_start_link) {
			$bc[$i]['text']=Text::_($breadcrumb_start); $bc[$i]['link']=Router::_($breadcrumb_start_link);
		} else {
			$bc[$i]['text']=Text::_('Main page'); $bc[$i]['link']=Router::_("index.php");
		}
		$bc=array_reverse($bc);
		foreach($bc as $tkey=>$tval)	{
		  $view->addBreadcrumb($tval['text'],$tval['link']);
		}
	}
}

?>