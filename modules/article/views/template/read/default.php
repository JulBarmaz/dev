<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

echo "<div class=\"article-wrapper float-fix\">";
if ($this->art) {
	if ($this->canModify) {
		echo "<div class=\"moduleShortcuts\">";
		echo "	<a class=\"linkButton btn btn-info\" rel=\"nofollow\" id=\"treeArticle\" href=\"".Router::_("index.php?module=article&amp;view=tree")."\">".Text::_('Tree')."</a>";
		echo "	<a class=\"linkButton btn btn-info\" rel=\"nofollow\" id=\"newArticle\" href=\"".Router::_("index.php?module=article&amp;task=modify")."\">".Text::_('New article')."</a>";
		echo "	<a class=\"linkButton btn btn-info\" rel=\"nofollow\" id=\"editArticle\" href=\"".Router::_("index.php?module=article&amp;task=modify&amp;psid=".$this->art->a_id)."\">".Text::_('Edit')."</a>";
		if ($this->art->a_deleted) echo "	<a class=\"linkButton btn btn-info\" rel=\"nofollow\" id=\"undeleteArticle\" href=\"".Router::_("index.php?module=article&amp;task=undelete&amp;psid=".$this->art->a_id)."\">".Text::_('Undelete')."</a>";
		else echo "	<a class=\"linkButton btn btn-info\" rel=\"nofollow\" id=\"deleteArticle\" href=\"".Router::_("index.php?module=article&amp;task=delete&amp;psid=".$this->art->a_id)."\">".Text::_('Delete')."</a>";
		echo "</div>";
	}
	?>
	<?php echo Event::raise("share.article");  ?>
	<?php if ($this->showTitle == 1) { ?>
		<h1 id="articleTitleRead" class="title"><?php echo $this->articleTitle; ?></h1>
	<?php } ?>
	
	<?php
		if ($this->art->a_thumb) {
			$filename=BARMAZ_UF_PATH."article".DS."thumbs".DS.Files::splitAppendix($this->art->a_thumb,true);
			if (Files::isImage($filename))	{
				$filelink=BARMAZ_UF."/article/thumbs/".Files::splitAppendix($this->art->a_thumb);
				echo "<img class=\"articleThumb\" alt=\"".$this->art->a_thumb."\" src=\"".$filelink."\" />";
			}
		}
	?>
	<?php if ($this->articleShowInfo == 1) { ?>
	<div id="articleHeader">
		<div class="articleProp articleDate"><?php echo Date::GetdateRus($this->articleDate); ?></div>
		<div class="articleProp articleAuthor"><span><?php echo Text::_('Author'); ?>:&nbsp;&nbsp;</span>
			<a href="<?php echo $this->articleAuthorProfileUrl; ?>"><?php echo $this->articleAuthor; ?></a>
		</div>
		<?php if ($this->use_rating) { ?>
		<div class="articleProp"><b><?php echo Text::_('Rating'); ?>:&nbsp;&nbsp;</b><?php echo $this->articleRating; ?></div>
		<?php } ?>
	</div>
	<?php }
	Event::raise('content.prepare', array("used_in_module"=>"article"), $this->articleHTML);
	$strippedHTML=strip_tags($this->articleHTML);
	if (!empty($strippedHTML) || strlen($this->articleHTML)>15) {
		?><div id="articleTextRead"><?php echo $this->articleHTML; ?></div><?php
	}
	if (($this->use_rating)&&($this->canVote)&&($this->articleShowInfo)) {
		echo "<div id=\"articleVote\">";
		echo Event::raise("rating.rendervotepanel",array("module"=>"article","element"=>"object","psid"=>$this->art->a_id, "mess"=>Text::_("Vote this article")));  // content.rating
		echo "</div>";
	}
	if (count($this->arrChilds)>0){
		if (!empty($strippedHTML) || strlen($this->articleHTML)>15) {
			echo "<div class=\"articleChildsTitle\">".Text::_("Articles")."</div>";
		}
		foreach($this->arrChilds as $key=>$row) {
			echo "<div class=\"articleChild\">";
			if ($row->a_alias) echo " <a href=\"".Router::_("index.php?module=article&amp;view=read&amp;psid=".$row->a_id."&amp;alias=".$row->a_alias)."\"><span>".$row->a_title."</span></a>";
			else echo " <a href=\"".Router::_("index.php?module=article&amp;view=read&amp;psid=".$row->a_id)."\"><span>".$row->a_title."</span></a>";
			echo "</div>";
		}
	}
	if(!$this->notmpl && $this->comm->commentsEnabled()&&$this->comm->checkACL("read")){
	?>
		<!-- Блок комментариев -->
		<div class="comments_list">
			<?php
			echo "<h4 class=\"titleBlockComment\">".Text::_('Comments').":</h4>";
			echo $this->comm->renderComments();
			if ($this->comm->checkACL("write")){	echo $this->comm->renderCommentForm();	}
		?>
		</div><!-- Конец блока комментариев-->
	<?php 	
	}
	Event::raise('content.rendered', array(), $this->art);
} else {
	if ($this->canModify) {
		echo "<p class=\"error\">".Text::_('Article not found')."</p>";
		echo "<div class=\"moduleShortcuts\">";
		echo "	<a class=\"linkButton btn btn-info\" rel=\"nofollow\" id=\"treeArticle\" href=\"".Router::_("index.php?module=article&amp;view=tree")."\">".Text::_('Tree')."</a>";
		echo "	<a class=\"linkButton btn btn-info\" rel=\"nofollow\" id=\"newArticle\" href=\"".Router::_("index.php?module=article&amp;view=edit")."\">".Text::_('New article')."</a>";
		echo "</div>";
	}
}
echo "</div>";
?>