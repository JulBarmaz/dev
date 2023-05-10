<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined ( '_BARMAZ_VALID' ) or die ( "Access denied" );
class blogModelpost extends Model {
	
	// Returns tag link strings
	private function getTagsText($tags, $output = 'HTML') {
		$tagstr = '';
		if ($tags) {
			$tagList = explode ( ',', $tags );
			foreach ( $tagList as $tag ) {
				if ($tag) {
					if ($output == 'HTML') {
						$tagLink = "index.php?task=search&searchtype=exact&where_search=blogs_tags&kwds=" . urlencode ( $tag );
						$tagAnchor = "<a rel=\"nofollow\" href=\"" . Router::_ ( $tagLink ) . "\">" . $tag . "</a>";
					} else {
						$tagAnchor = $tag;
					}
					
					if ($tagstr == '') {
						$tagstr .= $tagAnchor;
					} else {
						$tagstr .= ', ' . $tagAnchor;
					}
				}
			}
		}
		return $tagstr;
	}
	
	// Returns post count
	public function getPostCount($blogId, $published_only, $postStartDate = false, $postEndDate = false) {
		$query = "SELECT COUNT(*) FROM #__blogs_posts WHERE p_deleted=0 AND p_blog_id=" . $blogId;
		if ($published_only)
			$query .= " AND p_enabled=1";
		if ($postStartDate)
			$query .= " AND p_date>='" . $postStartDate . "'";
		if ($postEndDate)
			$query .= " AND p_date<='" . $postEndDate . "'";
		$this->_db->setQuery ( $query );
		return intval ( $this->_db->loadResult () );
	}
	
	// Returns posts array
	public function getPosts($blog, $published_only, $postStartDate = false, $postEndDate = false) {
		if (! $blog)
			return array ();
		$blogId = $blog->b_id;
		$orderby = $blog->b_porder_by;
		$orderdir = $blog->b_porder_dir;
		
		$query = "SELECT p.*, u.u_nickname as author FROM #__blogs_posts AS p
				LEFT JOIN #__users AS u ON p.p_author_id=u.u_id 
				WHERE  p_deleted=0";
		$query .= " AND p.p_blog_id=" . $blogId;
		if ($published_only)
			$query .= " AND p.p_enabled=1";
		if ($postStartDate)
			$query .= " AND p.p_date>='" . $postStartDate . "'";
		if ($postEndDate)
			$query .= " AND p.p_date<='" . $postEndDate . "'";
		if ($orderby && $orderdir)
			$query .= " ORDER BY " . $orderby . " " . $orderdir;
		$query .= $this->getAppendix ();
		$this->_db->setQuery ( $query );
		
		$posts = $this->_db->loadObjectList ();
		// translate system **********
		if(defined("_BARMAZ_TRANSLATE")){
			
			// преобразуем сведения на тот язык который сейчас выбран, если он отличается от дефолтного
			if(siteConfig::$defaultLanguage!=Text::getLanguage()){
				if(is_array ( $posts ) && count ( $posts ) > 0){				
					$translator = new Translator();
					$arr_tables=array('blogs_posts');
					$arr_data=array('blogs_posts'=>$posts);
					foreach($posts as $post){
					 $arr_psid['blogs_posts'][$post->p_id]=$post->p_id;
					 $arr_psid['users'][$post->p_author_id]=$post->p_author_id;
					}					
					$arr_key['blogs_posts']='p_id';
					$arr_key['users']='u_id';
					//Util::showArray($posts,'before');
					$post_data= $translator->updateReturnData($arr_tables,$arr_data,$arr_psid,$arr_key,Text::getLanguage());
					$posts=$post_data['blogs_posts'];
					//Util::showArray($posts,'after');
				}
			}
		}
			
		
		
		if (is_array ( $posts ) && count ( $posts ) > 0) {
			foreach ( $posts as $post ) {
				$post->tags = $this->getTagsText ( $post->p_tags );
			}
			return $posts;
		} else {
			return array ();
		}
	}
	public function getBlogByPostId($post_id, $published_only, $nodeleted = true) {
		$query = "SELECT p_blog_id FROM #__blogs_posts WHERE p_id=" . $post_id;
		if ($nodeleted)
			$query .= " AND p_deleted=0 ";
		if ($published_only)
			$query .= " AND p_enabled=1";
		$this->_db->setQuery ( $query );
		$blog_id = $this->_db->loadResult ();
		if ($blog_id) {
			$query = "SELECT * FROM #__blogs WHERE b_deleted=0 AND b_id=" . intval ( $blog_id );
			$this->_db->setQuery ( $query );
			$this->_db->loadObject ( $blog );
		} else
			$blog = false;
		return $blog;
	}
	
	// Returns single post
	public function getPost($post_id, $published_only, $nodeleted = true) {
		$query = "SELECT p.*, u.u_nickname as author FROM #__blogs_posts as p
		left join #__users as u ON p.p_author_id=u.u_id";
		$query .= " WHERE p.p_id=" . $post_id;
		if ($nodeleted)
			$query .= " AND p.p_deleted=0 ";
		if ($published_only)
			$query .= " AND p.p_enabled=1";
		$this->_db->setQuery ( $query );
		$post = new stdClass ();
		if ($this->_db->loadObject ( $post )) {
			$post->tags = $this->getTagsText ( $post->p_tags );
			$post->tagsText = $this->getTagsText ( $post->p_tags, 'text' );
			// translate system **********
			if(defined("_BARMAZ_TRANSLATE")){				
				// преобразуем сведения на тот язык который сейчас выбран, если он отличается от дефолтного
				if(siteConfig::$defaultLanguage!=Text::getLanguage()){
					$translator = new Translator();
					$arr_tables=array('blogs_posts');
					$arr_data=array('blogs_posts'=>array($post));
					$arr_psid['blogs_posts'][$post->p_id]=$post->p_id;
					$arr_psid['users'][$post->p_author_id]=$post->p_author_id;
					$arr_key['blogs_posts']='p_id';
					$arr_key['users']='u_id';
					//Util::showArray($post,'before');
					$posts= $translator->updateReturnData($arr_tables,$arr_data,$arr_psid,$arr_key,Text::getLanguage());
					if($posts) $post=$posts['blogs_posts'][0];
					//Util::showArray($post,'after');
				}
			}
			

		} else {
			$post->p_id = 0;
			$post->p_blog_id = 0;
		}
		return $post;
	}
	public function touchPost($psid) {
		$sql = "UPDATE #__blogs_posts SET p_comments=p_comments+1, p_touch_date=NOW() WHERE p_id=" . intval ( $psid );
		$this->_db->setQuery ( $sql );
		$this->_db->query ();
	}
	public function updatePostsCommentsCount() {
		$sql = "SELECT `cg_id` FROM `#__comms_grp` WHERE `cg_module`='blog' AND `cg_view`='post'";
		$this->_db->setQuery ( $sql );
		$grp_id = $this->_db->loadResult();
		$sql = "UPDATE `#__blogs_posts` SET `#__blogs_posts`.`p_comments`=(SELECT COUNT(`#__comms`.`cm_id`) FROM `#__comms` WHERE `#__comms`.`cm_grp_id`=".$grp_id." AND `#__comms`.`cm_obj_id`=`#__blogs_posts`.`p_id`);";
		$this->_db->setQuery ( $sql );
		$this->_db->query ();
	}
	// Saves post
	public function savePost($post, $published) {
		$query = "";
		if ($post->id) {
			$query .= "UPDATE #__blogs_posts SET p_theme='" . $post->theme . "', p_alias='" . $post->alias . "', p_text='" . $post->text . "', p_tags='" . $post->tagData . "', p_touch_date=NOW(), p_blog_id=" . $post->blog_id . " WHERE p_id=" . intval ( $post->id );
		} else {
			$query .= "INSERT INTO #__blogs_posts 
								(p_id, p_author_id, p_blog_id,p_theme, p_alias,p_text,p_date,p_touch_date,p_comments,p_rating,p_tags,p_enabled,p_deleted)
								VALUES(0," . User::getInstance ()->getID () . "," . $post->blog_id . ",'" . $post->theme . "','" . $post->alias . "','" . $post->text . "',NOW(),NOW(),0,0,'" . $post->tagData . "'," . $published . ",0)";
		}
		
		$this->_db->setQuery ( $query );
		$this->_db->query ();
		
		if ($post->id) {
			return $post->id;
		} else {
			$psid = $this->_db->insertid ();
			$this->reminderForModerator ( $psid, $post->blog_id, $post->theme, $post->text );
			return $psid;
		}
	}
	
	// Set Delete mark for post
	public function deletePost($post) {
		$query = "UPDATE `#__blogs_posts` SET `p_deleted`=1 WHERE `p_id`=" . $post->p_id;
		$this->_db->setQuery ( $query );
		$this->_db->query ();
	}
	public function togglePostPublished($post) {
		$query = "UPDATE #__blogs_posts SET p_enabled=ABS(p_enabled-1) WHERE p_id=" . $post->p_id;
		$this->_db->setQuery ( $query );
		$this->_db->query ();
	}
	public function togglePostComments($post) {
		$query = "UPDATE #__blogs_posts SET p_closed=ABS(p_closed-1) WHERE p_id=" . $post->p_id;
		$this->_db->setQuery ( $query );
		$this->_db->query ();
	}
	public function togglePostDeleted($post) {
		$query = "UPDATE #__blogs_posts SET p_deleted=ABS(p_deleted-1) WHERE p_id=" . $post->p_id;
		$this->_db->setQuery ( $query );
		$this->_db->query ();
	}
	public function getListPost($arrblogId, $arrblogIdEx, $post_count = 5) {
		$rights = Module::getInstance ( 'blog' )->getModel ( 'rights' );
		$res = array (
				0 
		);
		$res_ex = array ();
		if (! count ( $arrblogId )) {
			$sql_blog = "select b_id from #__blogs where b_deleted=0";
			$this->_db->setQuery ( $sql_blog );
			$arrblogId = $this->_db->LoadResultArray ();
		}
		if (count ( $arrblogId )) {
			foreach ( $arrblogId as $blogId ) {
				if ($rights->checkAction ( intval ( $blogId ), "read" )) {
					$res [] = intval ( $blogId );
				}
			}
		}
		if (count ( $arrblogIdEx )) {
			foreach ( $arrblogIdEx as $blogId ) {
				$res_ex [] = intval ( $blogId );
			}
		}
		$query = "SELECT b.p_id,b.p_blog_id,b.p_theme as title,b.p_touch_date as date,b.p_author_id as u_id, u.u_nickname as author
  						FROM `#__blogs_posts` as b
  						LEFT JOIN	#__users as u ON b.p_author_id=u.u_id
					  	WHERE b.p_deleted=0 AND b.p_enabled=1";
		if (count ( $res )) {
			$query .= " AND (b.p_blog_id IN (" . implode ( ",", $res ) . "))";
		}
		if (count ( $res_ex )) {
			$query .= " AND (b.p_blog_id NOT IN (" . implode ( ",", $res_ex ) . "))";
		}
		$query .= " ORDER BY b.p_ordering ASC, b.p_date DESC";
		$query .= " LIMIT " . ( int ) $post_count;
		$this->_db->setQuery ( $query );
		$posts = $this->_db->loadObjectList ();
		if (is_array ( $posts ) && count ( $posts ) > 0) {
			return $posts;
		} else {
			return array ();
		}
	}
	public function reminderForModerator($msgid, $blog_id, $posttheme) {
		if (! $msgid)
			return false;
		$theme = Text::_ ( "Message for blog moderator" );
		$link = Router::_ ( "index.php?module=blog&view=post&psid=".$msgid, false, true, 1, 2 );
		$text = sprintf ( Text::_ ( "blog mail short text" ), Portal::getURI (), $posttheme, $link );
		$to = $this->getModerators ( $blog_id );
		foreach ( $to as $email => $val ) {
			aNotifier::addToQueue ( $email, $theme, $text );
		}
	}
	private function getModerators($blog_id = 0) {
		// @TODO Пока только по группам
		$sql = "SELECT r_id FROM #__blogs_rights WHERE b_id=" . $blog_id . " AND flag=1 AND action='moderate'";
		$this->_db->setQuery ( $sql );
		$roles = $this->_db->loadObjectList ( "r_id" );
		$default_emails = array (
				soConfig::$siteEmail => soConfig::$siteEmail 
		);
		if (count ( $roles )) {
			$roles_str = implode ( ",", array_keys ( $roles ) );
			$sql = "SELECT u_email FROM #__users WHERE u_role IN (" . $roles_str . ")  AND u_activated=1 AND u_deleted=0 AND u_id <>" . User::getInstance ()->getID ();
			$this->_db->setQuery ( $sql );
			$emails = $this->_db->loadObjectList ( "u_email" );
			if (! count ( $emails ))
				return $default_emails;
			return $emails;
		} else
			return $default_emails;
	}
	public function updateAlias($psid, $alias, $name) {
		if($alias) $alias = mb_substr ( Translit::_ ( $alias, DEF_CP, false ), 0, 255 );
		if(!$alias) $alias = mb_substr ( Translit::_ ( $name ), 0, 255 );
		if($alias=="blog") $alias=mb_substr($psid."-".Translit::_($name), 0, 255);
		$sql = "SELECT COUNT(*) FROM #__blogs_posts WHERE p_alias='" . $alias . "' AND p_id<>" . $psid;
		$this->_db->setQuery ( $sql );
		if ($this->_db->loadResult () > 0) {
			$alias = mb_substr ( $psid . "-" . $alias, 0, 255 );
		}
		$sql = "UPDATE #__blogs_posts SET p_alias='" . $alias . "' WHERE p_id=" . $psid;
		$this->_db->setQuery ( $sql );
		if ($this->_db->query ())
			return $alias;
		else
			return false;
	}
}
?>