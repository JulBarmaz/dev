<?php
//BARMAZ_COPYRIGHT_TEMPLATE
//BARMAZ_MODULE_INFO

defined('_BARMAZ_VALID') or die("Access denied");

?>
<form action="<?php echo Router::_("index.php"); ?>" method="post">
	<input type="hidden" name="module" value="blog" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="postId" value="<?php echo $this->postId; ?>" />
	<input type="hidden" name="psid" value="<?php echo $this->psid; ?>" />
	<input type="hidden" name="authorId" value="<?php echo $this->authorId; ?>" />
	<input type="hidden" name="preEditTags" value="<?php echo $this->tagData; ?>" />
	<?php if($this->blogs&&count($this->blogs)>1) { ?>
		<div id="postBlog" class="postBlogEditor">
		<label for="postTheme"><?php echo Text::_("Move to");?>:</label>
		<?php echo HTMLControls::renderSelect("blog_id","blog_id", "b_id", "b_name", $this->blogs, $this->psid, false); ?>
		</div>
	<?php } ?>
	<div id="postTheme" class="postThemeEditor">
	<?php echo HTMLControls::renderLabelField("postThemeFld", Text::_('Theme'));?>:<input type="text" class="postThemeEdit form-control" id="postThemeFld" name="postTheme" size="120" value="<?php echo $this->postTheme; ?>" />
	</div>
	<div id="postAlias" class="postAliasEditor">
	<?php echo HTMLControls::renderLabelField("postAliasFld", Text::_('Alias'));?>:<input type="text" class="postAliasEdit form-control" id="postAliasFld" name="postAlias" size="120" value="<?php echo $this->postAlias; ?>" />
	</div>
  <div class="postTextEditor" >
  	<?php echo HTMLControls::renderLabelField("postText", Text::_('Text of record'));
		if($this->guiEditor) echo HTMLControls::renderEditor('postText','',$this->postText); 
		else {
			Event::raise("bbcode.editor",array("element_id"=>"postText"));
			echo HTMLControls::renderBBCodeEditor('postText','',$this->postText,50,15); 
		}
	?>
  </div>
	<div class="postTagsEditor" id="postTagsBlock">
		<?php echo HTMLControls::renderLabelField("postTags", Text::_('Tags')." (".Text::_('comma separated').")"); ?>:</b>
		<input type="text" name="tagData" id="postTags" size="80" class="postThemeEdit form-control" value="<?php echo $this->tagData; ?>" />
	</div>

	<div id="postEditorFooter">
	<input type="submit" class="commonButton btn btn-info" value="<?php echo Text::_('Save'); ?>" />
	</div>
</form>