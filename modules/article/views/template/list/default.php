<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

echo "<div class=\"postShare\">".Event::raise("share.article")."</div>";
Portal::getInstance()->setTitle(Text::_("Articles"));
Portal::getInstance()->setDescription(Text::_("Articles description"));
?>
<?php if ($this->main_article && $this->main_article->a_show_title== 1) { ?>
  <h1 id="articleTitleRead"><?php echo $this->main_article->a_title; ?></h1>
<?php } ?>
<div class="ArticleList">
	<?php
	foreach ($this->articles as $post) {
 $postlink=Router::_("index.php?module=article&amp;view=read&amp;psid=".$post->a_id."&amp;alias=".$post->a_alias);
 if (!$post->a_published) $published=" unpublished"; else $published="";
 ?>
	<div class="artBlock" id="post_<?php echo $post->a_id; ?>">
		<div class="artTheme<?php echo $published; ?>">
			<span class="artDate"><?php echo Date::fromSQL($post->a_date,true,true); ?>
			</span>
			<h2 class="title">
				<a href="<?php echo $postlink; ?>"><?php echo $post->a_title; ?>
				</a>
			</h2>
		</div>
		<div class="artText">
			<?php 		
			if($img=$this->getImage($post->a_thumb)) echo "<div class=\"artThumb\"><img src=\"".$img."\" alt=\"".$post->a_alt_thm."\" title=\"".$post->a_title_thm."\" /></div>";
			Event::raise('content.prepare', array("used_in_module"=>"article", "clean_all"=>0), $post->a_text);
			$first_hr=mb_strpos($post->a_text,'<hr id="system-readmore"',0);
			if ($first_hr) echo mb_substr($post->a_text,0, $first_hr)."...";
			//else echo mb_substr(strip_tags($post->a_text), 0, siteConfig::$shortTextLength)."...";
			else echo Text::cutHtml($post->a_text, siteConfig::$shortTextLength);
			echo "<div class=\"readMore\">";
			echo "<a href=\"".$postlink."\">".Text::_('Read more')."</a>";
			echo "</div>";
			Event::raise('content.rendered', array(), $post);
?>
		</div>
	</div>
	<?php } ?>
</div>
