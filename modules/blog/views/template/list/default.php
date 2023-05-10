<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");
if(!isset($this->blog)) return '';

if ($this->canWrite) {
	echo "<div class=\"moduleShortcuts\">";
	echo "	<a class=\"linkButton btn btn-info\" rel=\"nofollow\" id=\"treeArticle\" href=\"".Router::_("index.php?module=blog&amp;task=modify&amp;layout=new&amp;psid=".$this->psid)."\">".Text::_('New post')."</a>";
	echo "</div>";
}

// Если нужно то можно оверрайдить метатеги
// Portal::getInstance()->setTitle( ($this->blog->b_meta_title ? $this->blog->b_meta_title : $this->blog->b_name).($this->page>1 ? " - ".Text::_("Page")." ".$this->page : "") );
echo "<div class=\"postShare\">".Event::raise("share.blog")."</div>";
echo "<h1 class=\"title\">".$this->blog->b_name."</h1>";
if (strlen($this->blog->b_description)>20) {
	//echo "<div class=\"postBlock\"><div class=\"blogDescr\">".Text::toHtml($this->description)."</div>"."</div>";
	echo "<div class=\"blogDescr\">";
	if ($this->blog->b_thumb) {
		$filename=BARMAZ_UF_PATH."blog".DS."blog_thumbs".DS.Files::splitAppendix($this->blog->b_thumb,true);
		if (Files::isImage($filename))	{
			$filelink=BARMAZ_UF."/blog_thumbs/list/".Files::splitAppendix($this->blog->b_thumb);
			echo "<img class=\"bThumb\" title=\"".$this->blog->b_title_thm."\" alt=\"".$this->blog->b_alt_thm."\" src=\"".$filelink."\" />";
		}
	}
	echo $this->blog->b_description;
	echo "</div>";
}
?>
<?php if((isset($this->postDates["postStartDate"]) && $this->postDates["postStartDate"]) || (isset($this->postDates["postEndDate"]) && $this->postDates["postEndDate"])) { ?>
	<div class="post-dates"><?php echo Text::_("Filtered").": ".Date::fromSQL($this->postDates["postStartDate"], true)." - ".Date::fromSQL($this->postDates["postStartDate"], true); ?> <a href="<?php echo Router::_("index.php?module=blog&view=list&psid=".$this->blog->b_id."&alias=".$this->blog->b_alias."&mid=".$_SESSION['active_menu_id']."&reset=1",false,false); ?>"><?php echo Text::_("Reset filter"); ?></a></div>
<?php } ?>

<div class="postList float-fix">
	<?php
	// Добавим мид для подсветки меню - выводим только если есть верхний
	$mid=Request::getInt('mid');	$addmid='';	if($mid) $addmid="&mid=".$mid;
	
	foreach ($this->posts as $post) {
		$postlink=Router::_("index.php?module=blog&amp;view=post&amp;psid=".$post->p_id."&amp;alias=".$post->p_alias.$addmid);
		if (!$post->p_enabled) $published=" unpublished"; else $published="";
?>
		<div class="postBlock float-fix" id="post_<?php echo $post->p_id; ?>">
			<div class="postTheme<?php echo $published; ?>">
				<span class="postDate"><?php echo Date::fromSQL($post->p_date,false,true); ?>
				</span><a href="<?php echo $postlink; ?>"><?php echo $post->p_theme; ?>
				</a>
			</div>
			<div class="postText">
				<?php 		
				if ($post->p_thumb) {
					$filename=BARMAZ_UF_PATH."blog".DS."thumbs".DS.Files::splitAppendix($post->p_thumb,true);
					if (Files::isImage($filename))	{
						$filelink=BARMAZ_UF."/blog/thumbs/".Files::splitAppendix($post->p_thumb);
						echo "<img class=\"postThumb\" title=\"".$post->p_title_thm."\" alt=\"".$post->p_alt_thm."\" src=\"".$filelink."\" />";
					}
				}
				if (!$this->blog->b_guieditor) Event::raise("bbcode.parse",array(),$post->p_text);
				Event::raise('content.prepare', array("used_in_module"=>"blog","clean_all"=>1), $post->p_text);
				$first_hr=mb_strpos($post->p_text,'<hr id="system-readmore"',0);
				if ($first_hr) echo mb_substr($post->p_text,0,$first_hr);
				// else echo mb_substr(strip_tags($post->p_text), 0, siteConfig::$shortTextLength)."...";
				else echo Text::cutHtml($post->p_text, siteConfig::$shortTextLength);
				?>
			</div>
			<div class="readMore">
			<?php 
				if(!$this->blog->b_hide_comments){ 
					echo "<a href=\"".$postlink."\">".Text::_('Read post and comments');
					if ($post->p_comments) echo  "&nbsp;(".$post->p_comments.")";
					echo  "</a>";
				} else {
					echo "<a href=\"".$postlink."\">".Text::_('Read more')."</a>";
				}
			?>
			</div>
			<?php if($this->blog->b_post_rating) echo "<div class=\"postListRating\"><span>".Text::_('Rating')."</span>: ".$post->p_rating."</div>"; 
			?>
		</div>
	<?php } ?>
</div>
