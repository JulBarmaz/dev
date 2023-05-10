<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

if ($this->theme){
	// Если нужно то можно оверрайдить метатеги
	// Portal::getInstance()->setTitle( Text::_("Theme")." - ".$this->theme->t_theme.($this->page > 1 ? " - ".Text::_("Page")." ".$this->page : "") );
	if(!User::getInstance()->isLoggedIn())	echo "<p class=\"warning\">".Text::_("For posting log in or register")."</p>";
	$onclick=" onclick=\"if (!confirm('".Text::_("Are you sure")."  ?')) return false;\"";
	if ($this->theme->t_deleted) $dclass=" deletedTheme"; 
	elseif (!$this->theme->t_enabled) $dclass=" disabledTheme";
	else $dclass="";
	if(!$this->theme->t_enabled){
		echo "<p class=\"red_warning\">".Text::_("The message will be published after consideration by moderator")."</p>";
	}	
	echo "<h3 class=\"title\"><a href=\"".Router::_("index.php?module=forum&view=section&psid=".$this->theme->t_forum_id.( $this->section->f_alias ? "&alias=".$this->section->f_alias : ""))."\">".$this->section->f_name."</a></h3>";
	echo "<div class=\"forumPost".$dclass."\">";
	if ($this->canModerate||(!$this->theme->t_closed && $this->theme->t_author_id && $this->theme->t_author_id==User::getInstance()->getId())){
		$link="index.php?module=forum&psid=".$this->theme->t_forum_id."&tid=".$this->theme->t_id;
		if ($this->page) $link.="&page=".$this->page;
		$link.="&task=";
		echo "<div class=\"forum_panel btn-group\">";
		if (!$this->theme->t_closed && !count($this->posts)) echo "<a rel=\"nofollow\" class=\"linkButton btn btn-info\" href=\"".Router::_("index.php?module=forum&task=modifyTheme&psid=".$this->theme->t_forum_id."&tid=".$this->theme->t_id)."\">".Text::_("Mod.")."</a>";
		if ($this->canModerate){
			if ($this->theme->t_closed) {
				$buttonLabel="Op";
				echo "<a".$onclick." rel=\"nofollow\" class=\"linkButton btn btn-success\" href=\"".Router::_($link."toggleThemeClosed")."\">".Text::_($buttonLabel)."</a>";
			} else {
				$buttonLabel="Cl";
				echo "<a".$onclick." rel=\"nofollow\" class=\"linkButton btn btn-danger\" href=\"".Router::_($link."toggleThemeClosed")."\">".Text::_($buttonLabel)."</a>";
			}
			if ($this->theme->t_deleted) {
				$buttonLabel="Undel";
				echo "<a".$onclick." rel=\"nofollow\" class=\"linkButton btn btn-success\" href=\"".Router::_($link."toggleThemeDeleted")."\">".Text::_($buttonLabel)."</a>";
			} elseif (!$this->theme->t_enabled)  {
				$buttonLabel="Enab";
				echo "<a".$onclick." rel=\"nofollow\" class=\"linkButton btn btn-success\" href=\"".Router::_($link."toggleThemePublished")."\">".Text::_($buttonLabel)."</a>";
				$buttonLabel="Del";
				echo "<a".$onclick." rel=\"nofollow\" class=\"linkButton btn btn-danger\" href=\"".Router::_($link."toggleThemeDeleted")."\">".Text::_($buttonLabel)."</a>";
			} else {
				$buttonLabel="Dis";
				echo "<a".$onclick." rel=\"nofollow\" class=\"linkButton btn btn-warning\" href=\"".Router::_($link."toggleThemePublished")."\">".Text::_($buttonLabel)."</a>";
			}
		}
		echo "</div>";
	}
	echo "<div class=\"forumPostBody row\">";
	echo "<div class=\"col-md-9\">";
	echo "<h1 class=\"title\">".$this->theme->t_theme."</h1>";
	Event::raise("bbcode.parse",array(),$this->theme->t_text);
	echo "<p class=\"postMessage\">".$this->theme->t_text."</p>";
	echo "</div>";
	echo "<div class=\"forumPostAuthor col-md-3\"><div>";
	if(!empty($this->theme->pf_img)) {
		$img_path=BARMAZ_UF_PATH."user".DS."i".DS."avatars".DS.Files::getAppendix($this->theme->pf_img).DS.$this->theme->pf_img;
		if (is_file($img_path))	$avatar='<img width="100" src="'.BARMAZ_UF.'/user/i/avatars/'.Files::getAppendix($this->theme->pf_img)."/".$this->theme->pf_img.'" alt="" />';
		else $avatar='<img class="avatar" alt="" src="/templates/'.Portal::getInstance()->getTemplate().'/images/nofoto.png" />';
	} else $avatar='<img class="avatar" alt="" src="/templates/'.Portal::getInstance()->getTemplate().'/images/nofoto.png" />';
	echo "<p>".$avatar."</p>";
	echo "<p>"."<a href=\"".Router::_("index.php?module=user&amp;view=info&amp;psid=".$this->theme->t_author_id)."\">".$this->theme->u_nickname."</a></p>";
//	echo "<p>".Date::GetdateRus($this->theme->t_date,2,false)."</p>";
	echo "<p>".Date::fromSQL($this->theme->t_date)."</p>";
	echo "</div></div>"; // forumPostAuthor
	echo "</div>";
	echo "<div class=\"clr\"></div>";
	if ($this->canWrite && !$this->theme->t_closed) {
		echo "<div class=\"forum_panel btn-group\">";
		if (User::getInstance()->getID()){
			if($this->userSubscribed){
				echo "<a id=\"subscribe_theme_button\" rel=\"nofollow\" onclick=\"toggleForumSubscription(".$this->theme->t_id.", 0); return false;\" class=\"linkButton btn btn-info\" href=\"#\">".Text::_("Unsubscribe")."</a>";
			} else {
				echo "<a id=\"subscribe_theme_button\" rel=\"nofollow\" onclick=\"toggleForumSubscription(".$this->theme->t_id.", 1); return false;\" class=\"linkButton btn btn-info\" href=\"#\">".Text::_("Subscribe")."</a>";
			}
		}
		echo "<a rel=\"nofollow\" class=\"linkButton btn btn-info\" href=\"".Router::_("index.php?module=forum&task=modifyPost&psid=".$this->theme->t_id."&page=".$this->page)."\">".Text::_("Answer theme")."</a>";
		echo "</div>";
	}
	echo "</div>";
	if (count($this->posts)){
		foreach	($this->posts as $post) {
			$link="index.php?module=forum&psid=".$post->p_theme_id."&pid=".$post->p_id;
			if ($this->page) $link.="&page=".$this->page;
			$link.="&task=";
			if ($post->p_deleted) $dclass=" deletedPost"; 
			elseif (!$post->p_enabled) $dclass=" disabledPost";
			else $dclass="";
			echo "<div class=\"forumPost".$dclass."\">";
			if ($this->canModerate||(!$this->theme->t_closed && $post->p_author_id && $post->p_author_id==User::getInstance()->getId())){
				echo "<div class=\"forum_panel btn-group\">";
				if (!$this->theme->t_closed) echo "<a class=\"linkButton btn btn-info\" href=\"".Router::_($link."modifyPost")."\">".Text::_("Mod.")."</a>";
				if ($this->canModerate){
					if ($post->p_deleted) { 
						$buttonLabel="Undel";  
						echo "<a".$onclick." rel=\"nofollow\" class=\"linkButton btn btn-success\" href=\"".Router::_($link."togglePostDeleted")."\">".Text::_($buttonLabel)."</a>";
					} elseif (!$post->p_enabled)  {
						$buttonLabel="Enab";
						echo "<a".$onclick." rel=\"nofollow\" class=\"linkButton btn btn-success\" href=\"".Router::_($link."togglePostPublished")."\">".Text::_($buttonLabel)."</a>";
						$buttonLabel="Del";  
						echo "<a".$onclick." rel=\"nofollow\" class=\"linkButton btn btn-danger\" href=\"".Router::_($link."togglePostDeleted")."\">".Text::_($buttonLabel)."</a>";
					} else {
						$buttonLabel="Dis";
						echo "<a".$onclick." rel=\"nofollow\" class=\"linkButton btn btn-warning\" href=\"".Router::_($link."togglePostPublished")."\">".Text::_($buttonLabel)."</a>";
					}
				}
				echo "</div>";
			}
			echo "<a name=\"post".$post->p_id."\"></a>";
			echo "<div class=\"forumPostBody row\">";
			echo "<div class=\"col-md-9\">";
			echo "<h3 class=\"title\">".$post->p_theme."</h3>";
			Event::raise("bbcode.parse",array(),$post->p_text);
			echo "<div class=\"postMessage\">".$post->p_text."</div>";
			echo "</div>";
			echo "<div class=\"forumPostAuthor col-md-3\"><div>";
			if(!empty($post->pf_img)) {
				$img_path=BARMAZ_UF_PATH."user".DS."i".DS."avatars".DS.Files::getAppendix($post->pf_img).DS.$post->pf_img;
				if (is_file($img_path))	$avatar='<img width="100" src="'.BARMAZ_UF.'/user/i/avatars/'.Files::getAppendix($post->pf_img)."/".$post->pf_img.'" alt="" />';
				else $avatar='<img class="avatar" alt="" src="/templates/'.Portal::getInstance()->getTemplate().'/images/nofoto.png" />';
			} else $avatar='<img class="avatar" alt="" src="/templates/'.Portal::getInstance()->getTemplate().'/images/nofoto.png" />';
			echo "<p>".$avatar."</p>";
			echo "<p>"."<a rel=\"nofollow\" class=\"linkButton\" href=\"".Router::_("index.php?module=user&amp;view=info&amp;psid=".$post->p_author_id)."\">".$post->u_nickname."</a></p>";
//			echo "<p>".Date::GetdateRus($post->p_date,2,false)."</p>";
			echo "<p>".Date::fromSQL($post->p_date)."</p>";
			echo "</div></div>"; // forumPostAuthor
			echo "</div>";
			if (($this->theme)&&($this->canWrite)) {
				echo "<div class=\"forum_panel\">";
				echo "<a rel=\"nofollow\" class=\"linkButton btn btn-info\" href=\"".Router::_("index.php?module=forum&task=modifyPost&psid=".$this->theme->t_id."&page=".$this->page)."\">".Text::_("Answer theme")."</a>";
				echo "</div>";
			}
			echo "</div>";
		}
	}
}
?>