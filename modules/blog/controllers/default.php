<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class blogControllerdefault extends Controller {

	private function canModify($post) {
		$rightsModel = $this->getModel('rights');
		return (($post->p_author_id && User::getInstance()->getId() == $post->p_author_id) || $rightsModel->checkAction($post->p_blog_id,"moderate"));
	}

	private function canModerate($post) {
		$rightsModel = $this->getModel('rights');
		return $rightsModel->checkAction($post->p_blog_id,"moderate");
	}

	private function canPostVote($post) {
		$rightsModel = $this->getModel('rights');
		return ($rightsModel->checkAction($post->p_blog_id,"postvote"));
	}

	private function canUploadFiles($blog_id) {
		$rightsModel = $this->getModel('rights');
		return ($rightsModel->checkAction($blog_id,"filesupload"));
	}

	private function shortTheme($theme,$shortLength=0) {
		if (!$shortLength) {
			$shortLength=$this->getConfigVal('short_theme_length');
		}
		if (strlen($theme) > $shortLength) {
			$shTheme = mb_substr($theme,0,$shortLength);
			return $shTheme."...";
		}	else {
			return $theme;
		}
	}
	/* Блоги в категории */
	public function ajaxgetPostDatesForMonth(){
		$starYear=Request::getInt("post_year",0);
		$starMonth=Request::getInt("post_month",0);
		$blog_id=Request::getInt("psid",0);
		$dates_arr=Module::getHelper("post","blog")->getPostsDates($blog_id, $starYear, $starMonth);
		echo json_encode($dates_arr);
	}
	public function showCategory() {
		$this->checkACL("viewBlogList");
		$psid=Request::getInt('psid');
		if ($psid) {
			$model = $this->getModel('blog');
			$view = $this->getView();
			$category=$model->getCategory($psid);

			$rightsModel = $this->getModel('rights');
			if(is_object($category)){
				$blogs =$rightsModel->getBlogsForAction("read",$category->bc_id);
				$view->assign('category',$category);
				$view->assign('blogs',$blogs);
			} else $this->setRedirect(Router::_("index.php"),Text::_("Category absent"),404);
		} else $this->setRedirect(Router::_("index.php"),Text::_("Category absent"),404);
	}

	/* Посты в блоге */
	public function showList() 	{ 
		$rightsModel = $this->getModel('rights');
		$psid=$this->getPsid();
		if ($psid) {
			if ($rightsModel->checkAction($psid, "read")) {
				// можно его читать, тогда начинаем добывать данные
				$blogModel = $this->getModel('blog');
				$postModel = $this->getModel('post');
				$view = $this->getView();
				$blog = $blogModel->getBlog($psid);
				if ($blog) {
					if ($blog->b_layout) {
						$view->setLayout($blog->b_layout);
					}
					if ($rightsModel->checkAction($blog->b_id,"moderate")) $published_only=0; else $published_only=1;
					$view->addBreadcrumb(Text::_("Main page"),Router::_("index.php"));
					$view->addBreadcrumb($blog->b_name,"#");
					$view->assign('canWrite', $rightsModel->checkAction($psid,"write"));
					$postDates=array();
					$postDates["postStartDate"]=Request::getDate("postStartDate", false);
					$postDates["postEndDate"]=Request::getDate("postEndDate", false);
					if(!Request::getInt("reset")) $postDates = Module::getHelper("post")->getDates($psid,$postDates);
					else Module::getHelper("post")->resetDates($psid);
					// Paging
					$postCount = $postModel->getPostCount($psid,$published_only,$postDates["postStartDate"],$postDates["postEndDate"]);
					$paginator = $postModel->createPaginator($view,$postCount);
					$paginator->buildPagePanel("index.php?module=blog&view=list&psid=".$psid."&amp;alias=".$blog->b_alias);
					// assign meta
					$view->setMeta("title", ($blog->b_meta_title ? $blog->b_meta_title : $blog->b_name).($view->page>1 ? " - ".Text::_("Page")." ".$view->page : "") );
					if ($blog->b_meta_description) $view->setMeta("description",$blog->b_meta_description);
					if ($blog->b_meta_keywords) $view->setMeta("keywords",$blog->b_meta_keywords);
					$posts = $postModel->getPosts($blog,$published_only,$postDates["postStartDate"],$postDates["postEndDate"]);
					$view->assign('postDates',$postDates);
					$view->assign('posts',$posts);
					$view->assign('blog',$blog);
					$view->assign('psid',$psid);
				}	else $this->setRedirect('index.php',Text::_('Blog not exists'),404);
			} else {
				if (siteConfig::$defaultModule == 'blog' && $psid==$this->getConfigVal("Default_item_ID")) {
					$view = $this->getView(); $view->assign('posts',array()); $view->assign('description',''); $view->assign('psid',false);
					echo Text::_('Permission is absent for reading this blog');
					$this->haltView();
				} else $this->setRedirect('index.php',Text::_('Permission is absent for reading this blog'),404);
			}
		} else {
			if ( siteConfig::$defaultModule == 'blog') {
				$view = $this->getView();	$view->assign('posts',array());$view->assign('description','');$view->assign('psid',false);
				echo Text::_('Blog not exists');
			} else $this->setRedirect('index.php',Text::_('Blog not exists'),404);
		}
	}
	public function getComment() {
		$psid       = Request::getInt('psid', 0);
		if ($psid) {
			$this->showPost();
			$this->getView()->render();
		}
		else parent::getComment();
	}
	// один пост //
	public function showPost() {
		$rightsModel = $this->getModel('rights');
		$model = $this->getModel('post');
		$view = $this->getView();
		$psid = Request::getInt('psid',0);
		$comm_id    = Request::getInt('comm_id', 0);
		$blog = $model->getBlogByPostId($psid,0,0);
		if ($blog && $blog->b_id) {
			if ($blog->b_layout) {
				$view->setLayout($blog->b_layout);
			}
			// Check rights
			if ($rightsModel->checkAction($blog->b_id,"read")) {
				$view->addBreadcrumb($blog->b_name,"index.php?module=blog&view=list&psid=".$blog->b_id."&alias=".$blog->b_alias);
				if ($rightsModel->checkAction($blog->b_id,"moderate")) $published_only=0; else $published_only=1;
				$post = $model->getPost($psid,$published_only);
				if ($post && $post->p_id == 0) {
					$this->setRedirect('index.php?module=blog&view=list&psid='.$blog->b_id."&alias=".$blog->b_alias,Text::_('Post absent'),404);
				} elseif(!$this->canModify($post) && !$post->p_enabled) {
					$blog_alias = Module::getHelper("blog","blog")->getAliasByID("list",$post->p_blog_id);
					$this->setRedirect('index.php?module=blog&view=list&psid='.$post->p_blog_id."&alias=".$blog_alias,Text::_('Post unpublished'));
				}	else {
					$view->addBreadcrumb($this->shortTheme($post->p_theme),"#");
					$view->assign('canModify', $this->canModify($post));
					$canVote=$blog->b_post_rating;
					if($canVote) $canVote=$rightsModel->checkAction($blog->b_id,"postvote");
					$view->assign('canVote', $canVote);
					if ($post->p_meta_keywords) $view->setMeta("keywords",$post->p_meta_keywords);
					else if ($post->tagsText) $view->setMeta("keywords",$post->tagsText);
						
					if ($post->p_meta_title) $view->setMeta("title",$post->p_meta_title);
					else $view->setMeta("title",$post->p_theme);
						
					if ($post->p_meta_description) $view->setMeta("description",$post->p_meta_description);
					$view->assign('post',$post);
					$view->assign('blog',$blog);
					$view->assign('comm_id',$comm_id);
					$view->assign("comm",$this->initComments($psid));
				}
			}	else $this->setRedirect('index.php',Text::_('Permission is absent for reading this blog'));
		}	else $this->setRedirect('index.php',Text::_('Absent blog found'),404);
	}
	public function togglePostPublished() {
		$this->checkAuth();
		$psid = Request::getInt('psid',0);
		$model = $this->getModel('post');
		$post = $model->getPost($psid,0);

		if ($this->canModify($post)) {
			$model->togglePostPublished($post);
			$this->setRedirect('index.php?module=blog&view=post&psid='.$psid,Text::_('Post published flag toggled'));
		}	else {
			$this->setRedirect('index.php?module=blog&view=post&psid='.$psid,Text::_('Action denied'));
		}
	}
	public function togglePostComments() {
		$this->checkAuth();
		$psid = Request::getInt('psid',0);
		$model = $this->getModel('post');
		$post = $model->getPost($psid,0);

		if ($this->canModify($post)) {
			$model->togglePostComments($post);
			$this->setRedirect('index.php?module=blog&view=post&psid='.$psid,Text::_('Post comments flag toggled'));
		}	else {
			$this->setRedirect('index.php?module=blog&view=post&psid='.$psid,Text::_('Action denied'));
		}
	}
	public function togglePostDeleted() {
		$this->checkAuth();
		$psid = Request::getInt('psid',0);
		$model = $this->getModel('post');
		$post = $model->getPost($psid,0);

		if ($this->canModify($post)) {
			$model->togglePostDeleted($post);
			$this->setRedirect('index.php?module=blog&view=post&psid='.$psid,Text::_('Post deleted flag toggled'));
		}	else {
			$this->setRedirect('index.php?module=blog&view=post&psid='.$psid,Text::_('Action denied'));
		}
	}
	public function ajaxVote() {
		$psid = Request::getInt('psid',0);
		$element = Request::getSafe("element",false);
		switch ($element) {
			case "object":
				$model = $this->getModel("post");
				$post = $model->getPost($psid,1);
				if (!$post || !$post->p_author_id) return;
				else {
					if(!$this->canPostVote($post)) return;
					$author = $post->p_author_id;
				}
				$direction = Request::getSafe('dir',"up");
				$params = array("module"=>"blog","element"=>$element,"psid"=>$psid,"direction"=>$direction, "author"=>$author);
				$data_voted=Event::raise("rating.check",$params);  // content.rating
				if (!siteConfig::$useMultiVote && $data_voted) {
					echo Text::_("Already voted");
				} else {
					if (User::checkFloodPoint()){
						echo Event::raise("rating.rendervotepanel",array("module"=>"blog","view"=>"read","element"=>"object","psid"=>$psid, "mess"=>Text::_("Flood found")));
					} else {
						Event::raise("rating.vote",$params); // content.rating
						if ($direction=="up") echo Text::_("Good"); else echo Text::_("Bad");
					}
				}
				break;
			case "comment":
				parent::ajaxVote();
				break;
			default:
				return;
				break;
		}
	}

	public function modify() {
		$this->set("view","post",true);
		$this->checkAuth();
		$blogModel = $this->getModel('blog');
		$view = $this->getView();
		$model = $this->getModel('post');
		if ($this->get('layout')=="new") {
			$blogId = Request::getInt('psid',0);
			$blog = $blogModel->getBlog($blogId);
			if(!$blog) $blogId=0;
			$postId = 0;
		} else {
			$postId = Request::getInt('psid',0);
			$blog = $model->getBlogByPostId($postId,0);
			if($blog) $blogId = $blog->b_id;
			else {	$postId=0; $blogId = 0;
			}
		}
		$rightsModel = $this->getModel('rights');
		if($blogId) {
			$view->setLayout("modify");
			$blog_alias = Module::getHelper("blog","blog")->getAliasByID("list",$blogId);
			$view->addBreadcrumb($blog->b_name,"index.php?module=blog&view=list&psid=".$blogId."&alias=".$blog_alias);
			if ($postId == 0) {
				if ($rightsModel->checkAction($blogId,"write")) {
					// New post
					$view->assign('guiEditor',$blog->b_guieditor);
					$view->assign('filesUpload_editor',$rightsModel->checkAction($blogId,"filesupload"));
					$view->assign('postId',0);
					$view->assign('postTheme','');
					$view->assign('postAlias','');
					$view->assign('postText','');
					$view->assign('authorId',User::getInstance()->getID());
					$view->assign('tagData','');
					$view->assign('psid',$blogId);
					$view->assign('blogs',false);
					$view->addBreadcrumb(Text::_('New post'),"#");
					$view->render();
				}	else $this->setRedirect('index.php',Text::_('Permission is absent for writing this blog'));
			}	else {
				// Edit post
				$post = $model->getPost($postId,0);
				if ($this->canModify($post)) {
					$blogs =$rightsModel->getBlogsForAction("write,moderate");
					if ($blog->b_guieditor) {
						$postText = $post->p_text;
					}	else {
						$postText = strip_tags(str_replace("<br />","\n",$post->p_text));
					}
					$view->assign('guiEditor',$blog->b_guieditor);
					$view->assign('filesUpload_editor',$this->canUploadFiles($post->p_blog_id));
					$view->assign('postId',$post->p_id);
					$view->assign('postTheme',$post->p_theme);
					$view->assign('postAlias',$post->p_alias);
					$view->assign('postText',$postText);
					$view->assign('authorId',$post->p_author_id);
					$view->assign('tagData',$post->tagsText);
					$view->assign('psid',$post->p_blog_id);
					$view->assign('blogs',$blogs);

					$view->addBreadcrumb(Text::_('Editing'),"#");
					$view->render();
				}	else $this->setRedirect('index.php',Text::_('Permission is absent for writing this blog'));
			}
		}	else $this->setRedirect('index.php',Text::_('Absent blog found'),404);
	}

	public function save() {
		$this->checkAuth();

		$rightsModel = $this->getModel('rights');
		$model = $this->getModel('post');
		$bmodel = $this->getModel('blog');
		$postId = Request::getInt('postId',0);
		$blogNewId = Request::getInt('blog_id',0);
		if ($postId) {
			$blog = $model->getBlogByPostId($postId,0);
			$blogId = $blog->b_id;
		}	else {
			$blogId = Request::getInt('psid',0);
			$blog=$bmodel->getBlog($blogId);
		}

		$postTheme = Request::get('postTheme','');
		$alias=Request::getSafe('postAlias','');
		if($blog->b_guieditor) $postText = Request::get('postText','');
		else $postText = Request::getSafe('postText','');
		$tagData = $model->buildTagsString(Request::getSafe("tagData",""));
				
		if ($blogId && $blog) {
			$blog_alias = Module::getHelper("blog","blog")->getAliasByID("list",$blogId);
			if ($blog->b_premoderated) $published=0; else $published=1;
			if ($rightsModel->checkAction($blogId,"moderate"))$published=1;
			if ($rightsModel->checkAction($blogId,"write")) {
				if(strlen($postText)>20){
					// Preformat post text
					$post = new stdClass();
					if($blogNewId){
						if($rightsModel->checkAction($blogNewId,"write")) $post->blog_id = $blogNewId;
						else $post->blog_id = $blogId;
					} else {
						$post->blog_id = $blogId;
					}
					$post->id = $postId;
					if (!$postTheme) $postTheme = Text::_('Theme is empty');
					$post->theme		= htmlspecialchars($postTheme);
					$post->alias		= $alias;
					$post->text			= $postText;
					$post->tagData		= $tagData;
					if((User::checkFloodPoint())) {
						$url="index.php?module=blog&view=list&psid=".$blogId."&alias=".$blog_alias;
						$msg=Text::_("Flood found");
					}	else {
						$postNewId = $model->savePost($post,$published);
						if($postNewId) {
							$tagData=$model->updateTags($postNewId, $tagData);
							$alias=$model->updateAlias($postNewId, $post->alias, $post->theme);
						}
						if (!$postId&&$postNewId&&$blog->b_post_rating)	Event::raise("rating.new",array("module"=>"blog","element"=>"object","psid"=>$postNewId));  // content.newobject
						$url="index.php?module=blog&view=post&psid=".$postNewId."&alias=".$alias;  $msg=Text::_("Post saved");
					}
				} else {
					$url="index.php?module=blog&task=modify&layout=new&psid=".$blogId."&alias=".$blog_alias; $msg=Text::_("Some fields not filled");
				}
			}	else { 
				$url="index.php?module=blog&view=list&psid=".$blogId."&alias=".$blog_alias; $msg=Text::_("Permission is absent for writing this blog");
			}
		}	else { 
			$url="index.php"; $msg=Text::_("Absent blog found");
		}
		$this->setRedirect($url, $msg);
	}
	public function saveComment() {
		$moduleName	= Module::getInstance()->getName();
		$viewName 	= $this->getView()->getName();
		$psid       = Request::getSafe('psid', false); 					// ид строки
		$model = $this->getModel('post');
		$post = $model->getPost($psid,0);
		if ($post->p_id && (!$post->p_closed)){
			// $model->touchPost($psid);
			parent::saveComment();
			$model->updatePostsCommentsCount();
		} else {
			$url="index.php";
			$msg=Text::_("Permission is absent for writing this blog");
			$this->setRedirect($url, $msg);
		}
	}
}
?>
