<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

class forumControllerdefault extends SpravController {
	private function canModerate($forum_id) {
		$rightsModel = $this->getModel('rights');
		return $rightsModel->checkAction($forum_id,"moderate");
	}
	private function canModify($forum_id, $theme=false,$post=false) {
		if (!$forum_id) return false;
		$rightsModel = $this->getModel('rights');
		if ($theme) {
			return (($theme->t_author_id && User::getInstance()->getId() == $theme->t_author_id) || $rightsModel->checkAction($forum_id,"moderate"));
		} elseif ($post) {
			return (($post->p_author_id && User::getInstance()->getId() == $post->p_author_id) || $rightsModel->checkAction($forum_id,"moderate"));
		} else return false; //$rightsModel->checkAction($forum_id,"moderate");
	}
	public function ajaxsubscribeUserToTheme(){
		$theme_id=Request::getInt("theme_id",0);
		$subscribe=Request::getInt("subscribe",0);
		if($subscribe)	$this->subscribeUserToTheme($theme_id, User::getInstance()->getID());
		else $this->unsubscribeUserFromTheme($theme_id, User::getInstance()->getID());
		if($theme_id){
			if($this->userSubscribed($theme_id, User::getInstance()->getID())){
				echo json_encode(array("status"=>"OK","subscribed"=>"1", "event_text"=>"toggleForumSubscription(".$theme_id.", 0); return false;","title"=>Text::_("Unsubscribe")));
			} else {
				echo json_encode(array("status"=>"OK","subscribed"=>"0", "event_text"=>"toggleForumSubscription(".$theme_id.", 1); return false;","title"=>Text::_("Subscribe")));
			}
		} else {
			echo json_encode(array("status"=>"FAILED"));
		}
	}
	public function showNew() {
		$view=$this->getView();
		$mdl=Module::getInstance();
		$model = $this->getModel();
		$rightsModel = $this->getModel("rights");
		$view->setBreadcrumb(Text::_('Forums'),"index.php?module=forum");
		$allowed_ids = $rightsModel->getForumIdsForUser(User::getInstance()->getId(),User::getInstance()->getRole());
		$last_visit=User::getInstance()->getLastVisit();
		$posts=$model->getForumMessages($allowed_ids,$last_visit);
		$view->assign('themes',$posts);
	}
	public function showSection() {
		$view=$this->getView();
		$mdl=Module::getInstance();
		$model = $this->getModel();
		$rightsModel = $this->getModel("rights");
		$psid  = Request::getInt('psid', 0);
		$view->setBreadcrumb(Text::_('Forums'),"index.php?module=forum");
		$allowed_ids = $rightsModel->getForumIdsForUser(User::getInstance()->getId(),User::getInstance()->getRole());
		if ($psid) $canRead=in_array($psid,$allowed_ids); else $canRead=true;
		if ($canRead) {
			$canModerate=$rightsModel->checkAction($psid,"moderate");
			$newposts=$this->getHelper("posts")->countMessagesFromLastVisit(User::getInstance()->getId(),User::getInstance()->getRole());
			$section=$model->getSection($psid, $canModerate);
			if(!$psid || is_object($section)){
				$this->updateBreadcrumb($section,false);
				$sections=$model->getSections($psid, $allowed_ids, $canModerate);
				if ($canModerate) $themesCount=$model->getThemesCount($psid,false);
				else $themesCount=$model->getThemesCount($psid);
				$paginator = $model->createPaginator($view,$themesCount);
				$link="index.php?module=forum";
				if ($psid) $link.="&view=section&psid=".$psid.($section->f_alias ? "&alias=".$section->f_alias : "");
				$paginator->buildPagePanel($link);
				if ($canModerate) $themes=$model->getThemes($psid,false);
				else $themes=$model->getThemes($psid);
				if(is_object($section)){
					Portal::getInstance()->setTitle(  );
					$view->setMeta("title", Text::_("Forum")." - ".($section->f_meta_title ? $section->f_meta_title : $section->f_name).($view->page > 1 ? " - ".Text::_("Page")." ".$view->page : "") );
					if($section->f_meta_description) $view->setMeta("description", $section->f_meta_description);
					if($section->f_meta_keywords) $view->setMeta("keywords", $section->f_meta_keywords);
				}
				$view->assign('newposts',$newposts);
				$view->assign('canModerate',$canModerate);
				$view->assign('canWrite',$rightsModel->checkAction($psid,"write"));
				$view->assign('allowed_ids',$allowed_ids);
				$view->assign('section',$section);
				$view->assign('sections',$sections);
				$view->assign('themes',$themes);
			} else $this->setRedirect("index.php?module=forum", Text::_("Section absent"), 404);
		} else Util::redirect(Router::_("index.php?module=forum"), Text::_("Permission is absent to read this forum"), 403); 
	}
	public function showTheme() {
		if ($this->get('layout')=="lastpage") {
			$is_last_page = true;
			$this->set("layout","default",true);
		} $is_last_page = false;
		$view=$this->getView();
		$mdl=Module::getInstance();
		$model = $this->getModel();
		$rightsModel = $this->getModel("rights");
		$psid  = Request::getInt('psid', 0);
		$view->setBreadcrumb(Text::_('Forums'),"index.php?module=forum");
		$theme=$model->getTheme($psid,false);
		if ($theme && $rightsModel->checkAction($theme->t_forum_id,"read")) {
			$canModerate=$rightsModel->checkAction($theme->t_forum_id,"moderate");
			$section=$this->getModel("section")->getSection($theme->t_forum_id, $canModerate);
			if(is_object($section)){
				$this->updateBreadcrumb(false,$theme);
				if ($canModerate || (!$theme->t_deleted && ($theme->t_enabled || ($theme->t_author_id==User::getInstance()->getID() && $theme->t_author_id)))) {
					if($theme->t_author_id!=User::getInstance()->getID()) $this->touchTheme($psid);
					if ($canModerate) $postsCount=$model->getPostsCount($psid, false);
					else $postsCount=$model->getPostsCount($psid);
					$paginator = $model->createPaginator($view, $postsCount, $is_last_page);
					$link="index.php?module=forum&view=theme";
					if ($psid) $link.="&psid=".$psid.($theme->t_alias ? "&alias=".$theme->t_alias : "");
					$paginator->buildPagePanel($link);
					if ($canModerate) $posts=$model->getPosts($psid, false);
					else $posts=$model->getPosts($psid, true);
					$view->setMeta("title", Text::_("Theme")." - ".$theme->t_theme.($view->page > 1 ? " - ".Text::_("Page")." ".$view->page : ""));
					$view->assign('canModerate',$canModerate);
					$view->assign('canWrite',$rightsModel->checkAction($theme->t_forum_id,"write")&&$theme->t_enabled);
					$view->assign('theme',$theme);
					$view->assign('posts',$posts);
					$view->assign('userSubscribed',$this->userSubscribed($theme->t_id, User::getInstance()->getID()));
					$view->assign('section',$section);
				} else { $this->setRedirect("index.php?module=forum&view=section&psid=".$section->f_id.( $section->f_id ? "&alias=".$section->f_alias : "" ), Text::_("Theme is absent"), 404); }
			} else $this->setRedirect(Router::_("index.php?module=forum"), Text::_("Forum is absent"), 404);
		} else Util::redirect(Router::_("index.php?module=forum"), Text::_("Theme is absent"), 404); 
	}
	public function touchTheme($psid) {
		// TODO проверить referrer на выключенных форумах
		if (($psid)&&(!isset($_SESSION["forum_views"][$psid]))){
			$model = $this->getModel("theme");
			$model->touchTheme($psid);
			$_SESSION["forum_views"][$psid]=1;
		}
	}
	public function modifyTheme($errMessage=""){
		$view=$this->getView();
		$model = $this->getModel("section");
		$rightsModel = $this->getModel("rights");
		$view->setBreadcrumb(Text::_('Forums'),"index.php?module=forum");
		$this->set("layout","modify", true);
		$page  = Request::getInt('page', 0);
		$forum_id  = Request::getInt('psid', 0);
		$theme_id  = Request::getInt('tid', 0);
		$canModerate=$rightsModel->checkAction($forum_id,"moderate");
		$section=$model->getSection($forum_id, $canModerate);
		if($forum_id && is_object($section)) {
			if ($rightsModel->checkAction($forum_id,"write")) {
				/***********************************************/
				if ($theme_id) {
					$theme=$model->getTheme($theme_id,$forum_id,false); 
					if ($theme && !$theme->t_closed) {
						if (!$this->canModify($forum_id,$theme)) {
							$theme_id=0;
							$theme=false;
						}
					} else {
						$theme_id=0;
						$theme=false;
					}
				} else {
					$theme_id=0;
					$theme=false;
				}
				/***********************************************/
				if ($theme) {
					if($this->getModel('theme')->getPostsCount($theme->t_id,false)) Util::redirect("index.php?module=forum&view=theme&psid=".$theme->t_id, Text::_("Message modifying denied")); 
					$view->addBreadcrumb(Text::_('Modify theme'),"#");
					$newTagData=explode(',',$theme->t_tags);
					$theme->t_tags=""; $themeTags=array();
					foreach ($newTagData as $tag) {
						$tag = trim($tag);
						if ($tag != '' && !in_array($tag,$themeTags)) $themeTags[]=$tag;
					}
					if (count($themeTags)) $theme->t_tags=implode(",",$themeTags);
				} else {
					$view->addBreadcrumb(Text::_('New theme'),"#");
					$theme= new stdClass(); 
					$theme->t_id=0; $theme->t_text=Request::getSafe("themeText","");
					$theme->t_author_id=User::getInstance()->getID();
					$theme->t_theme=Request::getSafe("themeTitle","");
					$theme->t_tags=Request::getSafe("themeTags","");
					$theme->t_fixed=Request::getInt("t_fixed",0);
					$theme->t_closed=Request::getInt("t_closed",0);
				}
				$canModerate=$rightsModel->checkAction($forum_id,"moderate");
				$view->assign('section',$section);
				$view->assign('page',$page);
				$view->assign('theme',$theme);
				$view->assign('canModerate',$canModerate);
				$view->assign('userSubscribed',$this->userSubscribed($theme->t_id, $theme->t_author_id));
				$view->assign('premoderated',$section->f_premoderated);
				$view->assign('errMessage',$errMessage);
				
				$view->assign('disableCaptcha',$this->checkACL("forumDisableCaptcha",false));
				$view->render();
			} else { $this->setRedirect("index.php?module=forum&view=section&psid=".$forum_id. ( $section->f_id ? "&alias=".$section->f_alias : "" ), Text::_("Permission is absent to write this forum"), 403); }
		} else { $this->setRedirect("index.php?module=forum", Text::_("Forum is absent"), "404"); }
	}
	public function modifyPost($errMessage=""){
		$this->set("view","theme",true);
		$view=$this->getView();
		$model = $this->getModel("theme");
		$rightsModel = $this->getModel("rights");
		$view->setBreadcrumb(Text::_('Forums'),"index.php?module=forum");
		$this->set("layout","modify", true);
		$page  = Request::getInt('page', 0);
		$theme_id  = Request::getInt('psid', 0);
		$post_id  = Request::getInt('pid', 0);
		$theme=$model->getTheme($theme_id, false);
		if ($theme){
			if (!$theme->t_closed && $rightsModel->checkAction($theme->t_forum_id,"write")) {
				$section=$this->getModel("section")->getSection($theme->t_forum_id);
				// TODO Возможно нужно проверить на enabled по дереву
				/***********************************************/
				if($post_id) {
					$post=$model->getPost($post_id,$theme_id, false);
					if ($post){
						if (!$this->canModify($theme->t_forum_id, false, $post)) {
							$post_id=0;
							$post=false;
						}
					} else $post_id=0;
				} else {
					$post_id=0;
					$post=false;
				}
				/***********************************************/
				if ($post) {
					$view->addBreadcrumb(Text::_('Modify post'),"#");
				} else {
					$post= new stdClass(); 
					$post->p_id=0; $post->p_text=Request::getSafe("postText","");
					$post->p_theme=Request::getSafe("postTitle","RE:".htmlspecialchars_decode($theme->t_theme));
					$post->p_author_id=0;
					$post->p_deleted=0;
					$view->addBreadcrumb(Text::_('New post'),"#");
				}
				if ($post->p_deleted) {
					$this->setRedirect("index.php?module=forum&view=theme&psid=".$theme_id.( $theme->t_alias ? "&alias=".$theme->t_alias : "" ), Text::_("Post is deleted"), 404);
				} else {
					$view->assign('post',$post);
					$view->assign('page',$page);
					$view->assign('theme',$theme);
					$view->assign('userSubscribed',$this->userSubscribed($theme->t_id,($post->p_author_id ? $post->p_author_id : User::getInstance()->getId())));
					$view->assign('errMessage',$errMessage);
					$view->assign('premoderated',$section->f_premoderated);
					$view->assign('disableCaptcha',$this->checkACL("forumDisableCaptcha",false));
					$view->render();
				}
			} else { $this->setRedirect("index.php?module=forum&view=theme&psid=".$theme->t_id.( $theme->t_alias ? "&alias=".$theme->t_alias : "" ), Text::_("Permission is absent to write this forum"),403); }
		} else { $this->setRedirect("index.php?module=forum", Text::_("Theme is absent"), 404); }
	}
	
	public function updateBreadcrumb($section,$theme){
		if (!$section && !$theme) return false;
		elseif ($section){
			$parent_id=$section->f_parent_id;
		} elseif ($theme){
			$parent_id=$theme->t_forum_id;
		}
		if ($parent_id) {
			$forum=$this->getModel("section")->getSection($parent_id);
			if ($forum){
				$view=$this->getView();
				$view->addBreadcrumb($forum->f_name,"index.php?module=forum&view=section&psid=".$forum->f_id.($forum->f_alias ? "&alias=".$forum->f_alias : ""));
				
			} else return false;
		} else return false;
		return true;
	}

	private function toggleThemeField($field){
		$forum_id  = Request::getInt('psid', 0);
		$theme_id  = Request::getInt('tid', 0);
		$page  = Request::getInt('page', 0);
		if ($forum_id && $theme_id && $this->canModerate($forum_id)){
			$this->getModel("theme")->toggleThemeField($forum_id,$theme_id,$field);
			$theme=$this->getModel("theme")->getTheme($theme_id, false);
			$redirectLink="index.php?module=forum&view=theme&psid=".$theme_id.( $theme->t_alias ? "&alias=".$theme->t_alias : "" );
			if ($page) $redirectLink.="&page=".$page;
			$this->setRedirect($redirectLink);
		} else { $this->setRedirect("index.php?module=forum"); }
	}
	public function toggleThemePublished() { $this->toggleThemeField("t_enabled"); }	
	public function toggleThemeClosed() { $this->toggleThemeField("t_closed"); }	
	public function toggleThemeDeleted() { $this->toggleThemeField("t_deleted"); }	
	
	private function togglePostField($field){
		$theme_id  = Request::getInt('psid', 0);
		$post_id  = Request::getInt('pid', 0);
		$page  = Request::getInt('page', 0);
		$theme=$this->getModel("theme")->getTheme($theme_id, false);
		if ($theme && $post_id && $this->canModerate($theme->t_forum_id)){
			$this->getModel("theme")->togglePostField($theme_id,$post_id, $field);
			$redirectLink="index.php?module=forum&view=theme&psid=".$theme->t_id.( $theme->t_alias ? "&alias=".$theme->t_alias : "" );
			if ($page) $redirectLink.="&page=".$page;
			$redirectLink.="#post".$post_id;
			$this->setRedirect($redirectLink);
		} else { $this->setRedirect("index.php?module=forum"); }
	}
	public function togglePostPublished() { $this->togglePostField("p_enabled"); }
	public function togglePostDeleted() { $this->togglePostField("p_deleted"); }
	
	public function saveTheme(){
		$errMessage=""; $canSave=false;
		$model=$this->getModel("section");
		$forum_id  = Request::getInt('psid', 0);
		$theme_id  = Request::getInt('tid', 0);		
		$page  = Request::getInt('page', 0);
		if (!$forum_id) $this->setRedirect("index.php?module=forum",Text::_("Forum is absent"));
		else {
			$themeTitle=Request::getSafe("themeTitle","");
			$themeText=Request::getSafe("themeText","");
			$themeTags = $model->buildTagsString(Request::getSafe("themeTags",""));
				
			$subscribeToTheme=Request::getInt("t_subscribe",0);
			$oldTagData=array();
			Event::raise("captcha.checkResult",array("module"=>"forum"));
			if((!$this->checkACL("forumDisableCaptcha",false))&&isset($_SESSION['captcha_string'])&&($_SESSION['captcha_string']!="OK")) {
				//$errMessage = Text::_("Wrong captcha");
				$errMessage = $_SESSION['captcha_string'];
				unset($_SESSION['captcha_string']);
			}	elseif (!$themeTitle) $errMessage =Text::_("Specify theme");
			elseif (!$themeText) $errMessage =Text::_("Specify text");
			elseif((!$this->checkACL("forumDisableFloodControl",false))&&(User::checkFloodPoint())) {
				$errMessage = Text::_('Flood is found');
			} else {	
				$model=$this->getModel("section");
				$rightsModel=$this->getModel("rights");
				$canModerate=$rightsModel->checkAction($forum_id,"moderate");
				if ($canModerate) {
					$themeClosed=Request::getInt("t_closed",0);
					$themeFixed=Request::getInt("t_fixed",0);
				} else { $themeClosed=0; $themeFixed=0; }
				$forum=$model->getSection($forum_id, $canModerate);
				if (is_object($forum)) {
					$premoderated=$forum->f_premoderated;
					if($theme_id){
						if ($canModerate) $theme=$model->getTheme($theme_id,$forum_id, false);
						else $theme=$model->getTheme($theme_id,$forum_id);
						if($theme) {
							if($this->getModel('theme')->getPostsCount($theme_id,false)) $this->redirect("index.php?module=forum&view=theme&psid=".$theme_id.( $theme->t_alias ? "&alias=".$theme->t_alias : "" ), Text::_("Message modifying denied"));
							if($this->canModify($forum_id, $theme)) {
								$canSave=true;
								$oldTagData=explode(',',$theme->t_tags);
							}
						}
					} else { // считаем что тема новая
						if($rightsModel->checkAction($forum_id,"write")) $canSave=true; 
					}
				}
				if ($canSave){
					// Вариант с простой фильтрацией
					if (!$theme_id) $newflag=1; else $newflag=0; // ???????????
					$theme_id=$model->saveTheme($theme_id, $forum_id, $themeTitle, $themeText, $themeTags, $themeClosed, $themeFixed, $premoderated);
					if ($theme_id) $msg="Save successfull";
					else $msg="Save unsuccessfull";
					$themeTags=$model->updateTags($theme_id, $themeTags);
					$theme=$model->getTheme($theme_id);
					$redirectLink="index.php?module=forum&view=theme&psid=".$theme_id.($theme->t_alias ? "&alias=".$theme->t_alias : "").($page ? "&page=".$page : "");
					if($theme_id) {
						if ($newflag) {
							$this->reminderForModerator(false, $theme);
							if($subscribeToTheme) $this->subscribeUserToTheme($theme_id,User::getInstance()->getID());
						} else {
							if($premoderated) $this->reminderForModerator(false, $theme);
							
							if($theme->t_author_id && User::getInstance()->getID()==$theme->t_author_id){
								if (Request::getInt("p_subscribe",0)) $this->subscribeUserToTheme($theme_id, $theme->t_author_id);
								else $this->unsubscribeUserFromTheme($theme_id, $theme->t_author_id);
							}
						}
					}
				} else { 
					$redirectLink=Router::_("index.php?module=forum"); $msg="Permission is absent to save this theme"; 
				}
				$this->setRedirect($redirectLink, Text::_($msg));
			}	
			if($errMessage) $this->modifyTheme($errMessage);
		}
	}
	
	public function savePost(){
		$errMessage=""; $canSave=false; 
		$theme_id  = Request::getInt('psid', 0);
		$post_id  = Request::getInt('pid', 0);
		$page  = Request::getInt('page', 0);
		if (!$theme_id) $this->setRedirect("index.php?module=forum",Text::_("Theme is absent"));
		else {
			$postTitle=Request::getSafe("postTitle","");
			$postText=Request::getSafe("postText","");
			Event::raise("captcha.checkResult",array("module"=>"forum"));
			if((!$this->checkACL("forumDisableCaptcha",false))&&isset($_SESSION['captcha_string'])&&($_SESSION['captcha_string']!="OK")) {
				// $errMessage =Text::_("Wrong captcha"); 
				$errMessage = $_SESSION["captcha_string"];
				unset($_SESSION['captcha_string']);
			} elseif (!$postTitle) $errMessage =Text::_("Specify theme");
			elseif (!$postText) $errMessage =Text::_("Specify text");
			elseif((!$this->checkACL("forumDisableFloodControl",false)) && (User::checkFloodPoint())) {
				$errMessage = Text::_('Flood is found');
			} else {	
				$model=$this->getModel("theme");
				$rightsModel=$this->getModel("rights");
				// получим форум в который пишем через тему
				$theme=$theme=$model->getTheme($theme_id, false);
				if ($theme) $forum_id=$theme->t_forum_id; else $forum_id=0;
				$canModerate=$rightsModel->checkAction($forum_id,"moderate");
				if($forum_id){
					$fmodel=$this->getModel("section");
					$forum=$fmodel->getSection($forum_id, $canModerate);
					if ($forum) {
						$premoderated=$forum->f_premoderated;
						if ($post_id)	{
							if ($canModerate) $post=$model->getPost($post_id,$theme_id,false);
							else $post=$model->getPost($post_id,$theme_id);
							if($post && $this->canModify($forum_id, false,$post)) {	$canSave=true; }
						} else { // считаем что пост новый
							if($rightsModel->checkAction($forum_id,"write")) { $canSave=true;	}
						}
						if(!$theme->t_enabled) $canSave=false;
					} else Util::redirect("index.php?module=forum", Text::_("Forum is absent"));
				} else Util::redirect("index.php?module=forum", Text::_("Forum is absent"));
				if ($canSave){
					if (!$post_id) $nflag=1; else $nflag=0; // ???????????
					$post_id=$model->savePost($post_id, $theme_id, $postTitle, $postText,$premoderated);
					$redirectLink="index.php?module=forum&view=theme&psid=".$theme_id.($theme->t_alias ? "&alias=".$theme->t_alias : "");
					if ($nflag && $post_id) $redirectLink.="&layout=lastpage";
					elseif ($page) $redirectLink.="&page=".$page;
					if ($nflag && $post_id) {
						$this->reminderForModerator($post_id, $theme);
						$this->reminderForSubscribers($theme);
						if (Request::getInt("p_subscribe",0)) $this->subscribeUserToTheme($theme_id, User::getInstance()->getID());
					} elseif(!$nflag && $post_id){
						$post=$model->getPost($post_id);
						if($post->p_author_id && User::getInstance()->getID()==$post->p_author_id){
							if (Request::getInt("p_subscribe",0)) $this->subscribeUserToTheme($theme_id, $post->p_author_id);
							else $this->unsubscribeUserFromTheme($theme_id, $post->p_author_id);
						}							
					}
					if ($post_id) $msg="Save successfull";
					else $msg="Save unsuccessfull";
				} else { $redirectLink=Router::_("index.php?module=forum"); $msg="Permission is absent to save this post"; }
				$this->setRedirect($redirectLink, Text::_($msg));
			}	
			if($errMessage) $this->modifyPost($errMessage);
		}
	}
	public function reminderForModerator($post_id, $theme) {
		if (!is_object($theme)) return false;
		$link="index.php?module=forum&view=theme&psid=".$theme->t_id.($theme->t_alias ? "&alias=".$theme->t_alias : "");
		if ($post_id) $link.="&layout=lastpage";
		$link=Router::_($link, false, true, 1, 2);
		$text=sprintf(Text::_("forum mail short text"), Portal::getURI(), $theme->t_theme, $link);
		$to=$this->getModerators($theme->t_forum_id);
		$mail_theme=Text::_("Message for forum moderator");
		foreach($to as $email=>$val){
			aNotifier::addToQueue($email, $mail_theme, $text);
		}
	}
	private function getModerators($forum_id=0) {
		// @TODO Пока только по группам
		$sql="SELECT r_id FROM #__forum_rights WHERE f_id=".$forum_id." AND flag=1 AND action='moderate'";
		Database::getInstance()->setQuery($sql);
		$roles=Database::getInstance()->loadObjectList("r_id");
		$default_emails=array(soConfig::$siteEmail=>soConfig::$siteEmail);
		if (count($roles)){
			$roles_str=implode(",",array_keys($roles));
			$sql="SELECT u_email FROM #__users WHERE u_role IN (".$roles_str.") AND u_id<>".User::getInstance()->getID();
			Database::getInstance()->setQuery($sql);
			$emails=Database::getInstance()->loadObjectList("u_email");
			if (!count($emails)) return $default_emails;
			return $emails;
		} else return $default_emails;
	}
	public function reminderForSubscribers($theme) {
		if (!is_object($theme)) return false;
		$link=Router::_("index.php?module=forum&view=theme&psid=".$theme->t_id.($theme->t_alias ? "&alias=".$theme->t_alias : ""), false, true, 1, 2);
		$text=sprintf(Text::_("forum mail short text"),Portal::getURI(), $theme->t_theme, $link);
		$to=$this->getSubscribers($theme->t_id);
		$mail_theme=Text::_("Message from forum on")." ".htmlspecialchars(siteConfig::$metaTitle);
		if(count($to)){
			foreach($to as $email=>$val){
				aNotifier::addToQueue($email, $mail_theme, $text);
			}
		} 
	}
	private function getSubscribers($theme_id=0) {
		$default_emails=array();
		if(!$theme_id) return $default_emails;
		$sql="SELECT u_id FROM #__forum_subscribers WHERE t_id=".$theme_id;
		Database::getInstance()->setQuery($sql);
		$users=Database::getInstance()->loadObjectList("u_id");
		if (count($users)){
			$users_str=implode(",",array_keys($users));
			$sql="SELECT u_email FROM #__users WHERE u_id IN (".$users_str.") AND u_id<>".User::getInstance()->getID();
			Database::getInstance()->setQuery($sql);
			$emails=Database::getInstance()->loadObjectList("u_email");
			if (!count($emails)) return $default_emails;
			return $emails;
		} else return $default_emails;
	}
	private function userSubscribed($theme_id, $uid) {
		if($theme_id && $uid){
			$sql="SELECT COUNT(u_id) FROM #__forum_subscribers WHERE t_id=".$theme_id." AND u_id=".$uid;
			Database::getInstance()->setQuery($sql);
			return Database::getInstance()->loadResult();
		} else return false;
	}
	private function subscribeUserToTheme($theme_id, $uid){
		if($theme_id && $uid){
			$sql="INSERT IGNORE INTO #__forum_subscribers VALUES (".$theme_id.",".$uid.")";
			Database::getInstance()->setQuery($sql);
			return Database::getInstance()->query();
		}
		return false;
	}
	private function unsubscribeUserFromTheme($theme_id, $uid){
		$sql="DELETE FROM #__forum_subscribers WHERE t_id=".$theme_id." AND u_id=".$uid;
		Database::getInstance()->setQuery($sql);
		return Database::getInstance()->query();
	}
}
?>