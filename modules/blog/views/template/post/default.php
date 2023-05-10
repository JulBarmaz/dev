<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

if (isset($this->post)&&$this->post) {
	if (!$this->post->p_enabled) {
		$published=" unpublished";
		$post_on_text="Enable";
	} else {
		$published="";
		$post_on_text="Disable";
	}
	if ($this->canModify) {
		echo "<div class=\"moduleShortcuts\">";
		echo "	<a class=\"linkButton btn btn-info\" rel=\"nofollow\" id=\"editBlogPost\" href=\"".Router::_("index.php?module=blog&amp;task=modify&amp;psid=".$this->post->p_id)."\">".Text::_('Edit')."</a>";
		echo "	<a class=\"linkButton btn btn-info\" rel=\"nofollow\" id=\"togglePostPublished\" onclick=\"javascript: if (confirm('".Text::_("Toggle post published flag")."?')) return true; else return false;\" href=\"".Router::_("index.php?module=blog&amp;task=togglePostPublished&amp;psid=".$this->post->p_id)."\">".Text::_($post_on_text)."</a>";
		if ($this->post->p_closed) $comm_on_text="Comms on"; else $comm_on_text="Comms off";
		echo "	<a class=\"linkButton btn btn-info\" rel=\"nofollow\" id=\"togglePostComments\" onclick=\"javascript: if (confirm('".Text::_("Toggle post comments write flag")."?')) return true; else return false;\" href=\"".Router::_("index.php?module=blog&amp;task=togglePostComments&amp;psid=".$this->post->p_id)."\">".Text::_($comm_on_text)."</a>";
		if (!$this->post->p_enabled)  echo "	<a class=\"linkButton btn btn-info\" rel=\"nofollow\" id=\"togglePostDeleted\" onclick=\"javascript: if (confirm('".Text::_("Toggle post deleted flag")."?')) return true; else return false;\" href=\"".Router::_("index.php?module=blog&amp;task=togglePostDeleted&amp;psid=".$this->post->p_id)."\">".Text::_("Toggle deleted")."</a>";
		echo "</div>";
	}
	?>
	<div class="postBlock singlePost" id="post_<?php echo $this->post->p_id; ?>">
		<?php echo Event::raise("share.blogpost"); ?>
		<h1 class="postTheme<?php echo $published; ?>"><?php echo $this->post->p_theme; ?></h1>
		<div class="row">
			<div class="postText col-md-12">
				<?php if(!$this->blog->b_hide_properties){ ?>
				<div class="rightblog">
					<div class="postData">
						<div class="postProp"><b><?php echo Text::_('Date'); ?></b>: <?php echo Date::fromSQL($this->post->p_date,false,true); ?></div>
						<div class="postProp"><b><?php echo Text::_('Author'); ?></b>: <a href="<?php echo Router::_("index.php?module=user&amp;view=info&amp;psid=".$this->post->p_author_id); ?>"><?php echo $this->post->author; ?></a></div>
						<?php if($this->post->tags) { ?><div class="postProp"><b><?php echo Text::_('Tags'); ?></b>: <?php echo $this->post->tags; ?></div><?php } ?>
						<?php if($this->blog->b_post_rating){?> <div class="postProp"><b><?php echo Text::_('Rating'); ?></b>: <?php echo $this->post->p_rating; ?></div><?php } ?>
						<?php if($this->canVote) { ?>
						<div class="postProp">
							<?php echo Event::raise("rating.rendervotepanel",array("module"=>"blog","element"=>"object","psid"=>$this->post->p_id, "mess"=>Text::_("Vote this post"))); 	 // content.rating
							 ?>
						</div>
						<?php } ?>
					</div>
				</div>
				<?php } ?>
				<?php
				if ($this->post->p_thumb) {
					$filename=BARMAZ_UF_PATH."blog".DS."thumbs".DS.Files::splitAppendix($this->post->p_thumb,true);
					if (Files::isImage($filename))	{
						$filelink=BARMAZ_UF."/blog/thumbs/".Files::splitAppendix($this->post->p_thumb);
						echo "<img class=\"postThumb\" title=\"".$this->post->p_title_thm."\" alt=\"".$this->post->p_alt_thm."\" src=\"".$filelink."\" />";
					}
				}
				if (!$this->blog->b_guieditor) Event::raise("bbcode.parse",array(),$this->post->p_text);
				Event::raise('content.prepare', array("used_in_module"=>"blog"), $this->post->p_text);
				echo $this->post->p_text;
				?>
			</div>
		</div>
		<?php if(!$this->blog->b_hide_comments){ ?>
		<div class="row">
			<div class="comments_list col-md-12"><!-- Блок комментариев -->
			<?php if ($this->comm->commentsEnabled()&&$this->comm->checkACL("read")){
				if ($this->comm_id)	$comments=$this->comm->renderComment($this->comm_id, false);
				else $comments=$this->comm->renderComments();
				if($comments) {
					echo "<h4 class=\"titleBlockComment\">".Text::_('Comments').":</h4>";
					echo $comments;
				}
				if(!$this->post->p_closed){
					if ($this->comm->checkACL("write")){
						echo $this->comm->renderCommentForm();
					} elseif(!User::getInstance()->isLoggedIn()) {
						echo "<h4 class=\"blogCommentWarning\">".Text::_("For adding comments it is necessary to become authorized")."</h4>";
						echo "<div id=\"sign_in\">";
						echo "	<h4 class=\"not_authorized\">".Text::_("You are not authorized").".</h4>";
						echo "	<h4 class=\"please_login\">".Text::_(backofficeConfig::$noRegistration ? "Please log in" : "Please log in or register").".</h4>";
						echo "	<a rel=\"nofollow\" class=\"linkButton relpopup btn btn-info\" href=\"".Router::_("index.php?module=user&amp;view=login")."\">".Text::_("Log in")."</a>";
						if (!backofficeConfig::$noRegistration) echo "	<a class=\"linkButton btn btn-info\" href=\"".Router::_("index.php?module=user&amp;view=register")."\">".Text::_("Register")."</a>";
						echo "</div>";
					}
				}
			}	?>
			</div><!-- Конец блока комментариев-->
		</div>
		<?php } ?>
	</div>
<?php } ?>